<?php

namespace App\Http\Controllers;

use App\City;
use App\Repositories\CityRepository;
use App\Repositories\CustomFieldRepository;
use App\DataTables\CityDataTable;
use App\Http\Requests\UpdateCityRequest;
use Flash;
use App\Repositories\UploadRepository;
use App\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use DB;

class CityController extends Controller
{
    /** @var  SubCategoriesRepository */
    private $CityRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(CityRepository $CityRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->CityRepository = $CityRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    public function index(CItyDataTable $CityDataTable)
    {   
        return $CityDataTable->render('city.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->CityRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->CityRepository->model());
            $html = generateCustomField($customFields);
        }
        $countries=Country::all();
        if(count($countries)!=0) {
            return view('city.create', ['countries'=>$countries,'customFields'=> isset($html) ? $html : false]);
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
        if ($request->country == "0") {
            // var_dump($request->brand);
            Flash::error('Please select country');

            return redirect()->back();
            //

        }

       // $checkCityName = City::where('city_name', $request->name)->get();

//        if (count($checkCityName) > 0) { 
//            Flash::error('this city is exist');
//            return redirect()->route('city.index');
//        }

        
         $input = $request->all();
         $input['country_id']=$input['country'];
         $input['city_name']=$input['name'];
         $input['name_en']=$input['name_en'];
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->CityRepository->model());

        try {
            $subcategory = $this->CityRepository->create($input);
            $subcategory->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));

        return redirect(route('city.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = $this->CityRepository->findWithoutFail($id);

        if (empty($city)) {
            Flash::error('Category not found');

            return redirect(route('city.index'));
        }

        return view('city.show')->with('city', $city);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $countries=Country::all();

        $city = $this->CityRepository->findWithoutFail($id);

        if (empty($city)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('city.index'));
        }


        return view('city.edit')->with(['city'=> $city,'countries'=>$countries]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $city = $this->CityRepository->findWithoutFail($id);
        if (empty($city)) {
            Flash::error('City not found');
            return redirect(route('city.index'));
        }
        $input = $request->all();
        $input['country_id']=$input['country'];
        $input['city_name']=$input['name'];
        $input['name_en']=$input['name_en'];

      //  DB::table('cities')->where('id', $id)->update(['city_name' => $input['name']]);
        try {
            $city = $this->CityRepository->update($input, $id);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.category')]));

        return redirect(route('city.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\city  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = $this->CityRepository->findWithoutFail($id);

        if (empty($city)) {
            Flash::error('City not found');

            return redirect(route('city.index'));
        }

        $this->CityRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('city.index'));
    }
}
