<?php

namespace App\Http\Controllers;

use App\DataTables\VendorSuggestedDataTable;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\VendorSuggRepository;
use App\vendors_suggested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Prettus\Validator\Exceptions\ValidatorException;
use Flash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class VendorsSuggestedController extends Controller
{
    /** @var  VendorSuggRepository */

    private $vendorSugRepository;




    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    public function __construct(VendorSuggRepository $vendorSugRepo,
                                CustomFieldRepository $customFieldRepo)
    {
        parent::__construct();
        $this->vendorSugRepository = $vendorSugRepo;
        $this->customFieldRepository = $customFieldRepo;
    }
    /**
     * Display a listing of the Vendors.
     *
     * @param VendorSuggestedDataTable $vendorsugDataTable
     * @return Response
     */
    public function index(VendorSuggestedDataTable $vendorsugDataTable)
    {
        return $vendorsugDataTable->render('settings.vendors_suggested.index');
    }
    /**
     * Show the form for creating a new Vendor.
     *
     * @return Response
     */
    public function create()
    {

        $hasCustomField = in_array($this->vendorSugRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorSugRepository->model());
            $html = generateCustomField($customFields);
        }

        return view('settings.vendors_suggested.create')
            ->with("customFields", isset($html) ? $html : false);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorSugRepository->model());
        $input['user_id']=auth()->user()->id;
        try {
            $vendor = $this->vendorSugRepository->create($input);
            $vendor->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success('saved successfully.');
        return redirect(route('suggested/vendor.index'));
    }

    /**
     * Display the specified vendor.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $vendor = $this->vendorSugRepository->findWithoutFail($id);

        if (empty($vendor)) {
            Flash::error('Vendor not found');

            return redirect(route('vendors_suggested.index'));
        }

        return view('settings.vendors_suggested.show')->with('vendor', $vendor);
    }
    /**
     * Show the form for editing the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $vendor = $this->vendorSugRepository->findWithoutFail($id);


        if (empty($vendor)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('vendor.index'));
        }
        $customFieldsValues = $vendor->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorSugRepository->model());
        $hasCustomField = in_array($this->vendorSugRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('settings.vendors_suggested.edit')->with('vendor', $vendor)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        $vendor = $this->vendorSugRepository->findWithoutFail($id);

        if (empty($vendor)) {
            Flash::error('vendor not found');
            return redirect(route('categories.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->vendorSugRepository->model());
        try {
            $vendor = $this->vendorSugRepository->update($input, $id);


            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $vendor->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.vendor')]));

        return redirect(route('suggested/vendor.index'));
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $vendor = $this->vendorSugRepository->findWithoutFail($id);

        if (empty($vendor)) {
            Flash::error('vendor not found');

            return redirect(route('vendor.index'));
        }

        $this->vendorSugRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.vendor')]));

        return redirect(route('vendor.index'));
    }



}
