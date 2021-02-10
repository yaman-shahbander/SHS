<?php
/**
 * File name: UserController.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers;

use App\Country;
use App\DataTables\RatingDataTable;
use App\DataTables\UserDataTable;
use App\DataTables\AdminUserDataTable;
use App\DataTables\SuperAdminDataTable;

use App\DataTables\VendorDataTable;
use App\DataTables\SubCategoriesVendorDataTable;

use App\Events\UserRoleChangedEvent;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;

use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use App\Repositories\VendorRepository;
use Flash;
use App\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\subCategory;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use Illuminate\Support\Facades\Route;
use App\Models\GmapLocation;
use DB;
use App\Jobs\SendVerificationEmail;
class UserController extends Controller
{
    /** @var  UserRepository */
    private $userRepository;

    /** @var  VendorRepository */
    private $vendorRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    private $uploadRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    public function __construct(VendorRepository $vendorRepo, UserRepository $userRepo, RoleRepository $roleRepo, UploadRepository $uploadRepo,
                                CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->userRepository = $userRepo;
        $this->vendorRepository = $vendorRepo;
        $this->roleRepository = $roleRepo;
        $this->uploadRepository = $uploadRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param UserDataTable $userDataTable
     * @return Response
     */
    public function index(UserDataTable $userDataTable)
    {   

        return $userDataTable->render('settings.users.index');
    }



    /**
     * Display a user profile.
     *
     * @param
     * @return Response
     */
    public function profile()
    {

        $countries=Country::all();
        $subcategories = subCategory::all();
        $user = $this->userRepository->findWithoutFail(auth()->id());
        unset($user->password);
        $customFields = false;
        $role = $this->roleRepository->pluck('name', 'name');
        $rolesSelected = $user->getRoleNames()->toArray();
        $customFieldsValues = $user->customFieldsValues()->with('customField')->get();
        //dd($customFieldsValues);
        $hasCustomField = in_array($this->userRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
            $customFields = generateCustomField($customFields, $customFieldsValues);
        }
        if(!empty($user->cities->id))
        {
            $cities=City::where('country_id',$user->cities->country_id)->get();

        }
        else
            $cities=[];
            $style="";
            //$subcategories = subCategory::where('vendor_id', $user->vendors->subcategory_id)->get();
        return view('settings.users.profile', compact(['user', 'role', 'style','rolesSelected', 'customFields', 'customFieldsValues','countries','cities', 'subcategories']));
    }


    public function userprofile(Request $request,SubCategoriesVendorDataTable $subCategoriesDataTableDataTable)
    {
        $countries=Country::all();
        
        $user = $this->userRepository->findWithoutFail($request->id);
        unset($user->password);
        $customFields = false;
        $role = $this->roleRepository->pluck('name', 'name');
        $rolesSelected = $user->getRoleNames()->toArray();
        $customFieldsValues = $user->customFieldsValues()->with('customField')->get();
        //dd($customFieldsValues);
        $hasCustomField = in_array($this->userRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
            $customFields = generateCustomField($customFields, $customFieldsValues);
        }
        if(!empty($user->cities->id))
        {
            $cities=City::where('country_id',$user->cities->country_id)->get();

        }
        else
            $cities=[];
          //  return dd($user->subcategories);
        $user->rating=getRating($user);

        $userCoordinates =  GmapLocation::where('user_id', $request->id)->first();
        
        if(!empty($userCoordinates)) {
            Mapper::map(
                $userCoordinates->latitude,
                $userCoordinates->longitude,
                [
                    'zoom'      => 8,
                    'draggable' => false,
                    'marker'    => true,
                    'markers' => ['title' => 'My Location', 'animation' => 'BOUNCE']
                ]);

                // document.getElementById("gmap").style.
                $style='style="width: 100%; height: 300px"';
        } else {
                $style="";
        }
        
        return $dataTable=$subCategoriesDataTableDataTable->render('settings.users.profile',compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues','countries','cities','style']));
        
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        $countries=Country::all();
//        $cities=City::all();
        $role = $this->roleRepository->pluck('name', 'name');
        
        $rolesSelected = [];
        $hasCustomField = in_array($this->userRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
            $html = generateCustomField($customFields);
        }
        $style="";
        return view('settings.users.create')
            ->with("role", $role)
            ->with("customFields", isset($html) ? $html : false)
            ->with("rolesSelected", $rolesSelected)
            ->with("style", $style)
            ->with("countries", $countries);

    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        if($request->city=="0")
        {
            Flash::warning('please select country and city ');
            return redirect()->back();
        }
        $input = $request->all();
        $input['city_id']=$input['city'];
        $input['user_id']=Auth()->user()->id;
       // return Auth()->user()->id;


        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());

        $input['roles'] = isset($input['roles']) ? $input['roles'] : [];
        $input['password'] = Hash::make($input['password']);
        $input['api_token'] = str_random(60);


        try {
            $user = $this->userRepository->create($input);
            $role_id=DB::table('roles')->where('name',$input['roles'])->pluck('id');
            
            $permissions=DB::table('role_has_permissions')->where('role_id', $role_id)->pluck('permission_id');
  
            $user->syncRoles($input['roles']);
            $user->syncPermissions($permissions);
            $user->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

            if (!empty ($request->file('avatar'))) {
                $imageName = uniqid() . $request->file('avatar')->getClientOriginalName();
                $request->file('avatar')->move(public_path('storage/Avatar'), $imageName);
                $user->avatar = $imageName;
                $user->save();
            }
           // event(new UserRoleChangedEvent($user));
            // dispatch(new SendVerificationEmail($user));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success('saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('settings.users.profile')->with('user', $user);
    }

    public function loginAsUser(Request $request, $id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }
        auth()->login($user, true);
        if (auth()->id() !== $user->id) {
            Flash::error('User not found');
        }
        return redirect(route('users.profile'));
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        if ($id==1) {
            Flash::error('Permission denied');
            return redirect(route('users.index'));
        }

        $countries=Country::all();

        $user = $this->userRepository->findWithoutFail($id);
        unset($user->password);
        $html = false;
        $role = $this->roleRepository->pluck('name', 'name');
        $rolesSelected = $user->getRoleNames()->toArray();
        $customFieldsValues = $user->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
        $hasCustomField = in_array($this->userRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }
        if(!empty($user->cities->id))
        {
            $cities=City::where('country_id',$user->cities->country_id)->get();

        }
        else
        $cities=[];
        $style="";
            return view('settings.users.edit')
                ->with('user', $user)->with("role", $role)
                ->with("rolesSelected", $rolesSelected)
                ->with("customFields", $html)
                ->with("style", $style)
                ->with("countries", $countries)
                ->with("cities", $cities);
        }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        if (env('APP_DEMO', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('users.profile'));
        }
        if ($id==1 ) {
            Flash::error('Permission denied');
            return redirect(route('users.profile'));
        }
        if($request->city=="0")
        {
            Flash::warning('please select country and city ');
            return redirect()->back();
        }
        $user = $this->userRepository->findWithoutFail($id);


        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.profile'));
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());

        $input = $request->all();
        $input['city_id']=$input['city'];
        $input['user_id']=Auth()->user()->id;
        //return $input['user_id'];

        if (!auth()->user()->can('permissions.index')) {
            unset($input['roles']);
        } else {
        $input['roles'] = isset($input['roles']) ? $input['roles'] : [];
        }
        if (empty($input['password'])) {
            unset($input['password']);
        } else {
            $input['password'] = Hash::make($input['password']);
        }
        try {
            $user = $this->userRepository->update($input, $id);
            if (empty($user)) {
                Flash::error('User not found');
                return redirect(route('users.profile'));
            }
            if (isset($input['avatar']) && $input['avatar']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['avatar']);
                $mediaItem = $cacheUpload->getMedia('avatar')->first();
                $mediaItem->copy($user, 'avatar');
            }
            if (auth()->user()->can('permissions.index')) {
                $role_id=DB::table('roles')->where('name',$input['roles'])->pluck('id');
                $permissions=DB::table('role_has_permissions')->where('role_id', $role_id)->pluck('permission_id');
                $user->syncRoles($input['roles']);
                $user->syncPermissions($permissions);
            }

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $user->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
           // event(new UserRoleChangedEvent($user));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }


        Flash::success('User updated successfully.');

        return redirect()->back();

    }

    /**
     * Remove the specified User from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if (env('APP_DEMO', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('users.index'));
        }
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

        Flash::success('User deleted successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove Media of User
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        if (env('APP_DEMO', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
        } else {
            if (auth()->user()->can('medias.delete')) {
                $input = $request->all();
                $user = $this->userRepository->findWithoutFail($input['id']);
                try {
                    if ($user->hasMedia($input['collection'])) {
                        $user->getFirstMedia($input['collection'])->delete();
                    }
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }
    }
    public function getcity(Request $request){
        $cities = City::where('country_id',$request->id)
            ->get();
        return $cities;
    }

    public function showAdmin(AdminUserDataTable $userDataTable)
    {
        return $userDataTable->render('settings.users.index');
    }
    public function superAdmin(SuperAdminDataTable $userDataTable)
    {
        return $userDataTable->render('settings.users.superAdmin');
    }
    
   
}
