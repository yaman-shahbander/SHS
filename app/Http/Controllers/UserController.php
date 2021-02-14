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
use App\Models\User;
use App\Balance;


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
    
        $input['user_id']=Auth()->user()->id;
        $input['password'] = Hash::make($input['password']);

        while(true) {
            $payment_id = '#' . rand(1000, 9999) . rand(1000, 9999);
            if (!(User::where('payment_id', $payment_id)->exists())) {      
                break;
            } else continue;
        }  

            $input['language'] = $request->input('language') == null ? '' : $request->input('language', '');
            $input['phone'] = $request->input('phone') == null ? '' : $request->input('phone', '');      
            $input['payment_id'] = $payment_id;
            $balance = new Balance();
            $balance->balance = 0.0;
            $balance->save();
            $input['balance_id'] = $balance->id;
            $input['is_verified'] = 0;
            $input['city_id'] = $request->city;
            $token = openssl_random_pseudo_bytes(16);
            $user = $this->vendorRepository->create($input);

            //Convert the binary data into hexadecimal representation.
            $token = bin2hex($user->id . $token);
            $input['device_token'] = $token;
            $user = $this->vendorRepository->update($input,$user->id);
        
             $user->assignRole('homeowner');
             $user->assignRole($request->roles);


        try {
   

            if ($request->file('avatar')) {
                $imageName = uniqid() . $request->file('avatar')->getClientOriginalName();

                $request->file('avatar')->move(public_path('storage/Avatar'), $imageName);

                $user->avatar = $imageName;
                $user->save();
            }

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
        $input = $request->all();
    
        $input['user_id']=Auth()->user()->id;
        $input['password'] = Hash::make($input['password']); 

            $input['language'] = $request->input('language') == null ? '' : $request->input('language', '');
            $input['phone'] = $request->input('phone') == null ? '' : $request->input('phone', '');      
            
            $input['city_id'] = $request->city;

            $user = $this->userRepository->update($input,$id);

            DB::table('model_has_roles')->where('model_id', $user->id)->delete();

            $user->assignRole($request->roles);

        try {
   
            if ($request->file('avatar')) {
                $imageName = uniqid() . $request->file('avatar')->getClientOriginalName();

                $request->file('avatar')->move(public_path('storage/Avatar'), $imageName);

                try{ unlink(public_path('storage/Avatar').'/'.$user->avatar);}
                catch (\Exception $e) {}

                $user->avatar = $imageName;
                $user->save();
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success('User Updated Successfully!');

        return redirect(route('users.index'));

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

        if ($user->balance_id != null) {
            Balance::find($user->balance_id)->delete();
        }
        

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        try{ unlink(public_path('storage/Avatar').'/'.$user->avatar);}
        catch (\Exception $e) {}

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
