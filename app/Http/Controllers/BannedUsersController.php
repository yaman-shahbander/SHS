<?php

namespace App\Http\Controllers;

use App\Models\BannedUsers;
use Illuminate\Http\Request;
use App\DataTables\BannedUsersDataTable;
use App\Repositories\CustomFieldRepository;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\BannedUsersRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\User;

class BannedUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /** @var  CategoryRepository */
    private $BannedUsersRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(BannedUsersRepository $BannedUsersRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->BannedUsersRepository = $BannedUsersRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    public function index(BannedUsersDataTable $BannedUsersDataTable)
    {
        return $BannedUsersDataTable->render('bannedUsers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        
        $bannedUser = $this->BannedUsersRepository->findWithoutFail($id);
        if (empty($bannedUser)) {
            Flash::error('user not found');

            return redirect(route('bannedUsers.index'));
        }

        $this->BannedUsersRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('bannedUsers.index'));
    }
}
