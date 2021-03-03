<?php

namespace App\Http\Controllers;

use App\Models\BannedUsers;
use Illuminate\Http\Request;
use App\DataTables\BannedUsersDataTable;
use App\Repositories\CustomFieldRepository;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\BannedUsersRepository;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\User;
use DateTime;

class BannedUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /** @var  CategoryRepository */
    private $BannedUsersRepository;
    private $usersRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(BannedUsersRepository $BannedUsersRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo,
    UserRepository $usersRepo)
    {
        parent::__construct();
        $this->BannedUsersRepository = $BannedUsersRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->usersRepository = $usersRepo;
        
    }

    public function index(BannedUsersDataTable $BannedUsersDataTable)
    {
        if(!auth()->user()->hasPermissionTo('bannedUsers.index')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }
        return $BannedUsersDataTable->render('bannedUsers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->hasPermissionTo('bannedUsers.create')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }
        $hasCustomField = in_array($this->BannedUsersRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->BannedUsersRepository->model());
            $html = generateCustomField($customFields);
        }
        $users=User::all();
        if(count($users)!=0) {
            return view('bannedUsers.create', ['users'=>$users,'customFields'=> isset($html) ? $html : false]);
        }else{
            return redirect()->back()->with(["error"=> 'Please add category','customFields'=> isset($html) ? $html : false]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!auth()->user()->hasPermissionTo('bannedUsers.store')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }
        if ($request->username == "0") {
            Flash::error('Please select username');
            return redirect()->back();
        }
        $userid = BannedUsers::where('user_id', $request->username)->get();
            if ($userid == $request->username) {
                Flash::error('this username is exist');
                return redirect()->back();
            }
         $input = $request->all();
         $input['description']=$input['description'];
         $input['user_id']=$input['username'];
         $input['temporary_ban']=$input['temp_ban'];
         $input['forever_ban']=$input['banValue'];

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->BannedUsersRepository->model());

        try {
            $BannedUser = $this->BannedUsersRepository->create($input);
            $BannedUser->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));

        return redirect(route('bannedUsers.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BannedUsers  $bannedUsers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $bannedUser = $this->BannedUsersRepository->findWithoutFail($id);

        if (empty($bannedUser)) {
            Flash::error('user not found');

            return redirect(route('bannedUsers.index'));
        }

        return view('bannedUsers.show')->with('bannedUser', $bannedUser);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BannedUsers  $bannedUsers
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->hasPermissionTo('bannedUsers.edit')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }
        $bannedUsers = $this->BannedUsersRepository->findWithoutFail($id);


        if (empty($bannedUsers)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('bannedUsers.index'));
        }
        $customFieldsValues = $bannedUsers->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->BannedUsersRepository->model());
        $hasCustomField = in_array($this->BannedUsersRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('bannedUsers.edit')->with('bannedUsers', $bannedUsers)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BannedUsers  $bannedUsers
     * @return \Illuminate\Http\Response
     */
    public function update($id ,Request $request)
    {
        if(!auth()->user()->hasPermissionTo('bannedUsers.update')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }

        $bannedUser = $this->BannedUsersRepository->findWithoutFail($id);

        if (empty($bannedUser)) {
            Flash::error('user not found');
            return redirect(route('bannedUsers.index'));
        }
        $input = $request->all();
        $input['description']=$input['description'];
        $input['temporary_ban']=$input['temp_ban'];
        $input['forever_ban']=$input['banValue'];

        try {
            $bannedUser = $this->BannedUsersRepository->update($input, $id);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.category')]));

        return redirect(route('bannedUsers.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BannedUsers  $bannedUsers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->hasPermissionTo('bannedUsers.destroy')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }

        $bannedUser = $this->BannedUsersRepository->findWithoutFail($id);
        if (empty($bannedUser)) {
            Flash::error('user not found');

            return redirect(route('bannedUsers.index'));
        }

        $this->BannedUsersRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('bannedUsers.index'));
    }
    public function banned($id){
        $banne_id = $this->usersRepository->findWithoutFail($id);
        $user=$this->BannedUsersRepository()->create([
            'user_id'=>$banne_id,
            'description'=>'',
        ]);

    }

    public function saveUpdateBlocking(Request $request) {

        if(!$request->forever && DateTime::createFromFormat('Y-m-d', $request->temporary_ban) !== FALSE ){
            Flash::error('You have to select temporary time or check it forever');
            return redirect()->back();
        }
        
        if($request->forever == "on" && $request->temporary_ban ){
            Flash::error('You have to select temporary time or check it forever');
            return redirect()->back();
        }

        if($request->Ban_description==null){

            Flash::error('You have to write a description');

            return redirect()->back();
        }
     //   return var_dump($request->forever);
        $baneduser= BannedUsers::where('user_id',$request->id)->first();

        if(empty($baneduser)){
            $baneduser=new BannedUsers();
            $baneduser->user_id=$request->id;
        }
      
        $baneduser->description=$request->Ban_description;

        if($request->forever && $request->forever=="on")

        $baneduser->forever_ban="1";
        else{
            $baneduser->forever_ban="0";

            $baneduser->temporary_ban=$request->temp_ban;

        }


        if($baneduser->save()){
        Flash::success(__('User Blocked Succesfully', ['operator' => __('lang.category')]));

        return redirect()->back();
        }
        else{
            Flash::error('somthing was wrong');

            return redirect()->back();
        }
      
    }
    public function unBlockuser(Request $request) {

        if(!auth()->user()->hasPermissionTo('unBlock')){
            return view('vendor.errors.page', ['code' => 403, 'message' => '<strong>You don\'t have The right permission</strong>']);
        }
        $baneduser= BannedUsers::where('user_id',$request->id)->first();
  

        if (empty($baneduser)) {
            Flash::error('User not blocked');

            return redirect()->back();
        }

            if($baneduser->delete())
                {
                     Flash::success(__('User unBlocked Succesfully', ['operator' => __('lang.category')]));

                     return redirect()->back();
                }
             else{
                     Flash::error('somthing was wrong');

                      return redirect()->back();
                }
    }


    public function showProfile(Request $request) { // determine wether the user is (homeowner, vendor, admin, or superadmin)

        $user = User::find($request->id);
       // return  dd($user->roles->);
        if ($user->hasRole('vendor')) {
            return redirect(route('vendors.profile', ['id' => $user->id]));
        }

        if ($user->hasRole('homeowner')) {
            return redirect(route('user.profile', ['id' => $user->id]));
        }

    }
    
}
