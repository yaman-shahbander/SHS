<?php

namespace App\Http\Controllers;

use App\Delegate;
use Illuminate\Http\Request;
use App\Repositories\UploadRepository;
use App\Repositories\DelegateRepository;
use App\Repositories\CustomFieldRepository;
use App\DataTables\DelegateDataTable;
use Flash;
use App\Balance;

class DelegateController extends Controller
{
    /** @var  delegateRepository */
    private $delegateRepository;

    private $uploadRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    public function __construct(DelegateRepository $DelegateRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->delegateRepository = $DelegateRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DelegateDataTable $delegateDataTable)
    {
        return $delegateDataTable->render('delegate.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->delegateRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->delegateRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('delegate.create')->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->all();
        $input['name']=$input['name'];

        $input['phone']=$input['phone'];

        $checkDelegateName = Delegate::where('phone', $request->phone)->first();

        if (!empty($checkDelegateName)) { 
            Flash::error('this Delegate is exist');
            return redirect()->route('sales_man.index');
        }

        try {
            $balance = new Balance();
            $request->balance == null ? $balance->balance = 0.0 : $balance->balance =$request->balance;
            $balance->save();
            $input['balance_id'] = $balance->id;
            unset($input['balance']);
            $salesMan = $this->delegateRepository->create($input);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.dalegate')]));

        return redirect(route('sales_man.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Delegate  $delegate
     * @return \Illuminate\Http\Response
     */
    public function show(Delegate $delegate)
    {
        $delegate = $this->delegateRepository->findWithoutFail($id);

        if (empty($delegate)) {
            Flash::error('delegate not found');

            return redirect(route('delegate.index'));
        }

        return view('delegate.show')->with('delegate', $delegate);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Delegate  $delegate
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $saleMan = $this->delegateRepository->findWithoutFail($id);


        if (empty($saleMan)) {
            Flash::error('Salesman not found');

            return redirect(route('sales_man.index'));
        }
        
        return view('delegate.edit')->with('saleMan', $saleMan)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Delegate  $delegate
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();
        $input['name']=$input['name'];

        $input['phone']=$input['phone'];

        $checkDelegateName = Delegate::where('phone', $request->phone)->first();

        
        if (!empty($checkDelegateName)) { 
            if ($id != $checkDelegateName->id) {
            Flash::error('this Delegate is exist');
            return redirect()->route('sales_man.index');
            }
        }

        try {
            unset($input['balance']);
            $salesMan = $this->delegateRepository->update($input, $id);
            $salesMan->Balance->balance=$request->balance == null ? 0.0 : $request->balance;
            $salesMan->Balance->save();

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('Salesman updated successfully', ['operator' => __('lang.dalegate')]));

        return redirect(route('sales_man.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Delegate  $delegate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $salesman = $this->delegateRepository->findWithoutFail($id);

        if (empty($salesman)) {
            Flash::error('Salesman not found');

            return redirect(route('sales_man.index'));
        }

        $this->delegateRepository->delete($id);

        Flash::success(__("Deleted Successfully", ['operator' => __('lang.category')]));

        return redirect(route('sales_man.index'));
    }
}
