<?php

namespace App\Http\Controllers;

use App\DataTables\CountryDataTable;
use App\Http\Requests\UpdateCountryRequest;
use App\Repositories\CountryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Country;
use DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /** @var  CountryRepository */
    private $countryRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(CountryRepository $countryRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->countryRepository = $countryRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }
    

    public function index(countryDataTable $countryDataTable)
    {
        return $countryDataTable->render('country.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->countryRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->countryRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('country.create')->with("customFields", isset($html) ? $html : false);
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
        $input['country_name']=$input['name'];

        $checkCountryName = Country::where('country_name', $request->name)->get();

        if (count($checkCountryName) > 0) { 
            Flash::error('this country is exist');
            return redirect()->route('country.index');
        }

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->countryRepository->model());
        try {
            $country = $this->countryRepository->create($input);
            $country->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));

        return redirect(route('country.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\country  $country
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = $this->countryRepository->findWithoutFail($id);

        if (empty($country)) {
            Flash::error('Category not found');

            return redirect(route('country.index'));
        }

        return view('country.show')->with('country', $country);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\country  $country
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $country = $this->countryRepository->findWithoutFail($id);


        if (empty($country)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('country.index'));
        }

        if (empty($country)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('country.index'));
        }


        return view('country.edit')->with('country', $country)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateCountryRequest $request
     *
     * @return Response
     */
    public function update($id, request $request)
    {
        $country = $this->countryRepository->findWithoutFail($id);

        if (empty($country)) {
            Flash::error('Country not found');
            return redirect(route('country.index'));
        }
        $input = $request->all();
        $input['country_name']=$input['name'];

        // DB::table('countries')->where('id', $id)->update(['country_name' => $input['name']]);
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->countryRepository->model());
        try {
            $country = $this->countryRepository->update($input, $id);
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $country->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.category')]));

        return redirect(route('country.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = $this->countryRepository->findWithoutFail($id);

        if (empty($country)) {
            Flash::error('country not found');

            return redirect(route('country.index'));
        }

        $this->countryRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('country.index'));
    }
}
