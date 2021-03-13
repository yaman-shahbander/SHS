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
use DB;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use Illuminate\Support\Facades\Route;
use App\Models\GmapLocation;
use App\Models\Fee;
use App\Models\User;
use App\Balance;
use Validator;

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



    public function __construct(
        ReviewsRepositry $reviewRepo,
        VendorRepository $vendorRepo,
        RoleRepository $roleRepo,
        UploadRepository $uploadRepo,
        CustomFieldRepository $customFieldRepo
    ) {
        parent::__construct();
        $this->reviewRepository = $reviewRepo;

        $this->vendorRepository = $vendorRepo;
        $this->roleRepository = $roleRepo;
        $this->uploadRepository = $uploadRepo;
        $this->customFieldRepository = $customFieldRepo;
    }

    public function index(VendorDataTable $vendorDataTable)
    {
        if (!auth()->user()->hasPermissionTo('vendors.index')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

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
        if (!auth()->user()->hasPermissionTo('vendors.create')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        $countries = Country::all();
        //        $cities=City::all();
        $role = $this->roleRepository->pluck('name', 'name');

        $rolesSelected = [];
        $hasCustomField = in_array($this->vendorRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorRepository->model());
            $html = generateCustomField($customFields);
        }
        $style = "";
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
    public function profile(Request $request, SubCategoriesVendorDataTable $subCategoriesDataTableDataTable)
    {
        if (!auth()->user()->hasPermissionTo('vendors.profile')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        $countries = Country::all();

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
        if (!empty($user->cities->id)) {
            $cities = City::where('country_id', $user->cities->country_id)->get();
        } else
            $cities = [];
        //  return dd($user->subcategories);
        $user->rating = getRating($user);

        $userCoordinates =  GmapLocation::where('user_id', $request->id)->first();

        if (!empty($userCoordinates)) {
            Mapper::map(
                $userCoordinates->latitude,
                $userCoordinates->longitude,
                [
                    'zoom'      => 8,
                    'draggable' => false,
                    'marker'    => true,
                    'markers' => ['title' => 'My Location', 'animation' => 'BOUNCE']
                ]
            );

            // document.getElementById("gmap").style.
            $style = 'style="width: 100%; height: 300px"';
        } else {
            $style = "";
        }

        $favoriteVendor = $user->homeOwnerFavorite; // Users who added this vendor as a favorite


        return $dataTable = $subCategoriesDataTableDataTable->render('settings.vendors.profile', compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues', 'countries', 'cities', 'style', 'favoriteVendor']));



        //  $subcategories = subCategory::all();

        //  return dd($user->clients);


        //  return $dataTable=$reviewsDataTable->render('settings.vendors.profile',compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues','countries','cities', 'subcategories']));


        //  return view('settings.vendors.profile', compact(['user', 'role', 'rolesSelected', 'customFields', 'customFieldsValues','countries','cities','dataTable']));
    }

    public function store(CreateUserRequest $request)
    {

        if (!auth()->user()->hasPermissionTo('vendors.store')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        if ($request->city == "0") {
            Flash::warning(trans('lang.select_country_city'));
            return redirect()->back();
        }

        if ($request->input('email') == null && $request->input('phone') == null) {
            Flash::error('Either email or phone should be filled!');
            return redirect()->back();
        }

        if ($request->input('website')) {
            if (!filter_var($request->input('website'), FILTER_VALIDATE_URL)) {
                Flash::error('Enter a valid url');
                return redirect()->back();
            }
        }


        $input = $request->all();

        $input['user_id'] = Auth()->user()->id;
        $input['password'] = Hash::make($input['password']);

        while (true) {
            $payment_id = '#' . rand(1000, 9999) . rand(1000, 9999);
            if (!(User::where('payment_id', $payment_id)->exists())) {
                break;
            } else continue;
        }
        $input['approved']=1;
        $input['is_verified']=1;
        $input['language'] = $request->input('language') == null ? '' : $request->input('language', '');
        $input['phone'] = $request->input('phone') == null ? '' : $request->input('phone', '');
        $input['payment_id'] = $payment_id;
        $balance = new Balance();
        $balance->balance = 0.0;
        $balance->save();
        $input['balance_id'] = $balance->id;

        $input['city_id'] = $request->city;
        $token = openssl_random_pseudo_bytes(16);
        $user = $this->vendorRepository->create($input);

        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($user->id . $token);
        $input['device_token'] = $token;
        $user = $this->vendorRepository->update($input, $user->id);

        $user->assignRole('vendor');
        $user->assignRole($request->roles);

        try {


            if ($request->file('avatar')) {

                $imageName = uniqid() . $request->file('avatar')->getClientOriginalName();

                $imageName = preg_replace('/\s+/', '_', $imageName);

                $request->file('avatar')->move(public_path('storage/Avatar'), $imageName);

                $user->avatar = $imageName;
                $user->save();
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(trans('lang.store_operation'));

        return redirect(route('vendors.index'));
    }

    public function featuredfeeFunction()
    {

        if (!auth()->user()->hasPermissionTo('vendors.fee')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

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

    public function savefeeFunction(Request $request)
    {

        if (!auth()->user()->hasPermissionTo('vendors.feeSave')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        $check = Fee::all();
        if (count($check) == 0) {
            $newfee = new Fee;

            $amount  = strip_tags($request->fee_amount);

            if (preg_match('/[a-zA-Z]/', $amount)) {
                Flash::Error(trans('lang.only_numbers'));
                return redirect(route('vendors.index'));
            }


            if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
                Flash::Error(trans('lang.only_numbers'));
                return redirect(route('vendors.index'));
            }

            if ($amount < 0) {
                Flash::Error(trans('lang.negative_amount'));
                return redirect(route('vendors.index'));
            }

            $newfee->fee_amount = $amount;

            $newfee->save();

            Flash::success(trans('lang.store_operation'));
            return redirect(route('vendors.index'));
        } else {

            $amount  = strip_tags($request->fee_amount);

            if (preg_match('/[a-zA-Z]/', $amount)) {
                Flash::Error(trans('lang.only_numbers'));
                return redirect(route('vendors.index'));
            }


            if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $amount)) {
                Flash::Error(trans('lang.only_numbers'));
                return redirect(route('vendors.index'));
            }

            if ($amount < 0) {
                Flash::Error(trans('lang.negative_amount'));
                return redirect(route('vendors.index'));
            }

            Fee::first()->update([
                'fee_amount' => $amount
            ]);

            Flash::success(trans('lang.fee_update'));
            return redirect(route('vendors.index'));
        }
    }

    public function update($id, Request $request)
    {

        if (!auth()->user()->hasPermissionTo('vendors.update')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        if (env('APP_DEMO', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('users.profile'));
        }
        if ($id == 1) {
            Flash::error('Permission denied');
            return redirect(route('users.profile'));
        }
        if ($request->city == "0") {
            Flash::warning(trans('lang.select_country_city'));
            return redirect()->back();
        }

        if ($request->input('email') == null && $request->input('phone') == null) {
            Flash::success(trans('lang.require_email_phone'));
            return redirect()->back();
        }

        $input = $request->all();

        $input['user_id'] = Auth()->user()->id;
        $input['password'] = Hash::make($input['password']);

        $input['language'] = $request->input('language') == null ? '' : $request->input('language', '');
        $input['phone'] = $request->input('phone') == null ? '' : $request->input('phone', '');

        $input['city_id'] = $request->city;

        unset($input['email']);

        unset($input['phone']);

        $user = $this->vendorRepository->update($input, $id);

        if ($user->email != $request->email) {

            $checkEmail = User::where('email', '=', $request->email)->first();

            if ($checkEmail != null) {
                Flash::error(trans('validation.email'));

                return redirect(route('vendors.edit', [$user->id]));
            } else {
                $user->email = $request->email;
            }
        }

        if ($user->phone != $request->phone) {

            $checkPhone = User::where('phone', '=', $request->phone)->first();

            if ($checkPhone != null) {
                Flash::error(trans('validation.phone'));

                return redirect(route('vendors.edit', [$user->id]));
            } else {
                $user->phone = $request->phone;
            }

        }

        $user->save();

        DB::table('model_has_roles')->where('model_id', $user->id)->delete();

        $user->assignRole($request->roles);

        try {

            if ($request->file('avatar')) {

                $imageName = uniqid() . $request->file('avatar')->getClientOriginalName();

                $imageName = preg_replace('/\s+/', '_', $imageName);

                $request->file('avatar')->move(public_path('storage/Avatar'), $imageName);

                try {
                    unlink(public_path('storage/Avatar') . '/' . $user->avatar);
                } catch (\Exception $e) {
                }

                $user->avatar = $imageName;
                $user->save();
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(trans('lang.update_operation'));

        return redirect(route('vendors.index'));
    }

    public function edit($id)
    {

        if (!auth()->user()->hasPermissionTo('vendors.edit')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        if ($id == 1) {
            Flash::success(trans('lang.Permission_denied'));
            return redirect(route('users.index'));
        }

        $countries = Country::all();

        $user = $this->vendorRepository->findWithoutFail($id);
        unset($user->password);
        $html = false;
        $role = $this->roleRepository->pluck('name', 'name');
        $rolesSelected = $user->getRoleNames()->toArray();
        $customFieldsValues = $user->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorRepository->model());
        $hasCustomField = in_array($this->vendorRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        if (empty($user)) {
            Flash::success(trans('lang.user_not_found'));

            return redirect(route('users.index'));
        }
        if (!empty($user->cities->id)) {
            $cities = City::where('country_id', $user->cities->country_id)->get();
        } else
            $cities = [];
        $style = "";
        return view('settings.vendors.edit')
            ->with('user', $user)->with("role", $role)
            ->with("rolesSelected", $rolesSelected)
            ->with("customFields", $html)
            ->with("style", $style)
            ->with("countries", $countries)
            ->with("cities", $cities);
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermissionTo('vendors.destroy')) {
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        if (env('APP_DEMO', false)) {
            Flash::warning('This is only demo app you can\'t change this section ');
            return redirect(route('users.index'));
        }
        $user = $this->vendorRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::success(trans('lang.user_not_found'));

            return redirect(route('users.index'));
        }

        if ($user->balance_id != null) {
            Balance::find($user->balance_id)->delete();
        }

        try {
            unlink(public_path('storage/Avatar') . '/' . $user->avatar);
        } catch (\Exception $e) {
        }

        $this->vendorRepository->delete($id);

        Flash::success(trans('lang.delete_operation'));

        return redirect(route('vendors.index'));
    }
}
