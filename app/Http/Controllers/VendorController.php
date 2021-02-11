<?php

namespace App\Http\Controllers;

use App\city;
use App\Country;
use App\DataTables\SubCategoriesVendorDataTable;
use App\DataTables\VendorDataTable;

use App\Events\UserRoleChangedEvent;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\DataTables\RatingDataTable;
use App\DataTables\ReviewsDataTable;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ReviewsRepositry;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\VendorRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\subCategory;
 use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
 use Illuminate\Support\Facades\Route;
use App\Models\GmapLocation;
use App\Models\Fee;

class VendorController extends Controller
{
    /** @var  ReviewsRepositry */
    private $reviewRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;



    /**
     * Display a listing of the Review.
     *
     * @param ReviewsDataTable $reviewDataTable
     * @return Response
     */
    private $vendorRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;



    public function __construct(ReviewsRepositry $reviewRepo,VendorRepository $vendorRepo, RoleRepository $roleRepo, UploadRepository $uploadRepo,
                                CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->reviewRepository = $reviewRepo;

        $this->vendorRepository = $vendorRepo;
        $this->roleRepository = $roleRepo;
        $this->uploadRepository = $uploadRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    public function index(VendorDataTable $vendorDataTable)
    {

        return $vendorDataTable->render('settings.vendors.index');
    }
    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */

//    public function create()
//    {
//        $role = $this->roleRepository->pluck('name', 'name');
//        //$role = $role->where('name','vendor');
//        $role = array(
//          "vendor" => "vendor"
//        );
//
//        //dd($role);
//        $rolesSelected = [];
//        $hasCustomField = in_array($this->vendorRepository->model(), setting('custom_field_models', []));
//        if ($hasCustomField) {
//            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorRepository->model());
//            $html = generateCustomField($customFields);
//        }
//
//        return view('settings.vendors.create')
//            ->with("role", $role)
//            ->with("customFields", isset($html) ? $html : false)
//            ->with("rolesSelected", $rolesSelected);
//    }



    public function create()
    {
        $countries=Country::all();
//        $cities=City::all();
        $role = $this->roleRepository->pluck('name', 'name');

        $rolesSelected = [];
        $hasCustomField = in_array($this->vendorRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorRepository->model());
            $html = generateCustomField($customFields);
        }
        $style="";
        return view('settings.vendors.create')
            ->with("role", $role)
            ->with("customFields", isset($html) ? $html : false)
            ->with("rolesSelected", $rolesSelected)
            ->with("style", $style)
            ->with("countries", $countries);

    }



    /**
     * Display a listing of the Review.
     *
     * @param ReviewsDataTable $reviewDataTable
     * @return Response
     */
    public function profile(Request $request,SubCategoriesVendorDataTable $subCategoriesDataTableDataTable)
    {
        $countries=Country::all();
        
        $user = $this->vendorRepository->findWithoutFail($request->id);
        unset($user->password);
        $customFields = false;
        $role = $this->roleRepository->pluck('name', 'name');
        $rolesSelected = $user->getRoleNames()->toArray();
        $customFieldsValues = $user->customFieldsValues()->with('customField')->get();
        //dd($customFieldsValues);
        $hasCustomField = in_array($this->vendorRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorRepository->model());
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

        $favoriteVendor = $user->homeOwnerFavorite; // Users who added this vendor as a favorite
        
    
        return $dataTable=$subCategoriesDataTableDataTable->render('settings.vendors.profile',compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues','countries','cities','style', 'favoriteVendor']));
        

            
          //  $subcategories = subCategory::all();

          //  return dd($user->clients);
            

      //  return $dataTable=$reviewsDataTable->render('settings.vendors.profile',compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues','countries','cities', 'subcategories']));


      //  return view('settings.vendors.profile', compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues','countries','cities','dataTable']));
    }

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
        // $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorRepository->model());

        $input['roles'] = "vendor";
        $input['password'] = Hash::make($input['password']);
        $input['api_token'] = str_random(60);


        try {
            $user = $this->vendorRepository->create($input);
            $user->syncRoles($input['roles']);
            // $user->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

            if (!empty ($request->file('avatar'))) {
                $imageName = uniqid() . $request->file('avatar')->getClientOriginalName();
                $request->file('avatar')->move(public_path('storage/Avatar'), $imageName);
                $user->avatar = $imageName;
                $user->save();
            }
                // $cacheUpload = $this->uploadRepository->getByUuid($input['avatar']);
                // $mediaItem = $cacheUpload->getMedia('avatar')->first();
                // $mediaItem->copy($user, 'avatar');
            
            // event(new UserRoleChangedEvent($user));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success('saved successfully.');

        return redirect(route('vendors.index'));
    }

    public function featuredfeeFunction() {
        $count = count(Fee::all()); // if there is an old fee value
        if ($count > 0) { 
            $value = Fee::all('fee_amount');
            $value = $value[0]['fee_amount'];
            return view('settings.vendors.featuredVendorfee')->with('count', $count)
            ->with('value', $value);
         } else {
            return view('settings.vendors.featuredVendorfee')->with('count', $count);
         }
    }

    public function savefeeFunction(Request $request) {
        $check = Fee::all();
        if(count($check) == 0) {
            $newfee = new Fee;
            $newfee->fee_amount = $request->fee_amount;
            $newfee->save();
            Flash::success('Fee saved successfully.');
            return redirect(route('vendors.index'));
        } else {
            Fee::first()->update([
                'fee_amount' => $request->fee_amount
            ]);
            Flash::success('Fee updated successfully.');
            return redirect(route('vendors.index'));
        }
    }

}
