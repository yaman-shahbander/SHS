<?php

namespace App\Http\Controllers;

use App\Delegate;
use Illuminate\Http\Request;
use App\Repositories\UploadRepository;
use App\Repositories\DelegateRepository;
use App\Repositories\CustomFieldRepository;
use App\DataTables\DelegateDataTable;
use Flash;
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
        $input['balance']=$input['balance'];

        $checkDelegateName = Delegate::where('name', $request->name)->get();

        if (count($checkDelegateName) > 0) { 
            Flash::error('this Delegate is exist');
            return redirect()->route('delegate.index');
        }

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->delegateRepository->model());
        try {
            $country = $this->delegateRepository->create($input);
            $country->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.dalegate')]));

        return redirect(route('delegate.index'));
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
    public function edit(Delegate $delegate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Delegate  $delegate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Delegate $delegate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Delegate  $delegate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Delegate $delegate)
    {
        //
    }
}
