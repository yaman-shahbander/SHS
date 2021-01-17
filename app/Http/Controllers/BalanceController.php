<?php

namespace App\Http\Controllers;

use App\Balance;
use Illuminate\Http\Request;
use App\DataTables\BalanceDataTable;
use App\Repositories\CustomFieldRepository;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\BalanceRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\User;
use DB;

class BalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /** @var  BalanceRepository */
    private $balanceRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(BalanceRepository $balanceRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->balanceRepository = $balanceRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }
    
    public function index(BalanceDataTable $balanceDataTable)
    {
        return $balanceDataTable->render('balance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->balanceRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->balanceRepository->model());
            $html = generateCustomField($customFields);
        }
        $users=User::all();
        if(count($users)!=0) {
            return view('balance.create', ['users'=>$users,'customFields'=> isset($html) ? $html : false]);
        }else{
            return redirect()->back()->with(["error"=> 'Please add balance','customFields'=> isset($html) ? $html : false]);
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

        $latestBalanceId = DB::table('balances')->latest('id')->first("id");
        $latestBalanceIdPlusOne = ($latestBalanceId->id + 1);
        
         $input = $request->all();
         $input['id'] = $latestBalanceIdPlusOne;
         $input['balance'] = $input['balance'];

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->balanceRepository->model());

        try {
            $balance = $this->balanceRepository->create($input);
            $balance->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        DB::table('users')
        ->where('id', $request->nameselect)
        ->update(array('balance_id' => $latestBalanceIdPlusOne));

        Flash::success(__('Balance saved successfully', ['operator' => __('lang.category')]));

        return redirect(route('balance.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Balance  $balance
     * @return \Illuminate\Http\Response
     */
    public function show(Balance $balance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Balance  $balance
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $balance_id = User::find($id)->balance_id;

        $user_name = User::find($id)->name;

        $balance = $this->balanceRepository->findWithoutFail($balance_id);


        if (empty($balance)) {
            Flash::error(__('Balance Not found', ['operator' => __('lang.category')]));

            return redirect(route('balance.index'));
        }
        $customFieldsValues = $balance->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->balanceRepository->model());
        $hasCustomField = in_array($this->balanceRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('balance.edit')->with('balance', $balance)->with('user_name', $user_name)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Balance  $balance
     * @return \Illuminate\Http\Response
     */
    public function update($id ,Request $request)
    {
        $balance_id = User::find($id)->balance_id;

        $balance = $this->balanceRepository->findWithoutFail($balance_id);

        if (empty($balance)) {
            Flash::error('balance not found');
            return redirect(route('balance.index'));
        }

        $input = $request->all();

        $input['balance'] = $input['balance'];

        try {

            $balance = $this->balanceRepository->update($input, $balance_id);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('Balance successfully updated', ['operator' => __('lang.category')]));

        return redirect(route('balance.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Balance  $balance
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $balance_id = User::find($id)->balance_id;

        $this->balanceRepository->delete($balance_id);

        Flash::success(__('Balance Deleted Successfully', ['operator' => __('lang.category')]));

        return redirect(route('balance.index'));
    }

    public function addBalance($id) {
        $balance_id = User::find($id)->balance_id;

        $user_name = User::find($id)->name;

        $balance = $this->balanceRepository->findWithoutFail($balance_id);


        if (empty($balance)) {
            Flash::error(__('Balance Not found', ['operator' => __('lang.category')]));

            return redirect(route('balance.index'));
        }
        
        $customFieldsValues = $balance->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->balanceRepository->model());
        $hasCustomField = in_array($this->balanceRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        return view('balance.add')->with('balance', $balance)->with('user_name', $user_name)->with("customFields", isset($html) ? $html : false);
    }

    public function balanceaddUpdate($id ,Request $request) {
        $balance_id = User::find($id)->balance_id;

        $balance = $this->balanceRepository->findWithoutFail($balance_id); 

        if (empty($balance)) {
            Flash::error('balance not found');
            return redirect(route('balance.index'));
        }

        $input = $request->all();

        $input['balance'] = $input['balance'] + $input['Add'];

        try {

            $balance = $this->balanceRepository->update($input, $balance_id);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('Balance successfully updated', ['operator' => __('lang.category')]));

        return redirect(route('balance.index'));
    }
    
}
