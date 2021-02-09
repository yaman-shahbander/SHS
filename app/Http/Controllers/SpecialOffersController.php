<?php

namespace App\Http\Controllers;

use App\Models\specialOffers;
use App\Models\User;
use Illuminate\Http\Request;

use App\Repositories\SpecialOffersRepositry;
use App\Repositories\CustomFieldRepository;
use App\DataTables\SpecialOffersDataTable;
use Flash;
use App\Repositories\UploadRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\Category;

class SpecialOffersController extends Controller
{
    /** @var  SpecialOffersRepositry */
    private $SpecialRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(SpecialOffersRepositry $SpecialRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->SpecialRepository = $SpecialRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    public function index(SpecialOffersDataTable $SpecialDataTable)
    {
        return $SpecialDataTable->render('special_offers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->SpecialRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->SpecialRepository->model());
            $html = generateCustomField($customFields);
        }

        $categories = Category::all();

        $vendors = User::whereHas('roles', function($q) {
            $q->where('role_id', 3);
        })->get();

        return view('special_offers.create', [
            'categories' => $categories,
            'vendors'    => $vendors,
            'customFields'=> isset($html) ? $html : false]);
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

        

        return $input;
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->SpecialRepository->model());

        try {
            $special = $this->SpecialRepository->create($input);
            $special->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));

        return redirect(route('offers.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\specialOffers  $special
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $special = $this->SpecialRepository->findWithoutFail($id);

        if (empty($special)) {
            Flash::error('Offers not found');

            return redirect(route('offers.index'));
        }

        return view('special_offers.show')->with('special', $special);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\specialOffers  $special
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vendors = User::whereHas(
            'roles', function($q){
            $q->where('name', 'Manager');
        }
        )->get();
        $special = $this->SpecialRepository->findWithoutFail($id);

        if (empty($special)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('offers.index'));
        }


        return view('special_offers.edit')->with(['special'=> $special,'vendors'=>$vendors]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\specialOffers  $special
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $special = $this->SpecialRepository->findWithoutFail($id);
        if (empty($special)) {
            Flash::error('Offers not found');
            return redirect(route('offers.index'));
        }
        $input = $request->all();
        $input['user_id']=$input['user'];

        //  DB::table('cities')->where('id', $id)->update(['city_name' => $input['name']]);
        try {
            $special = $this->SpecialRepository->update($input, $id);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.category')]));

        return redirect(route('offers.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\specialOffers  $special
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $special = $this->SpecialRepository->findWithoutFail($id);

        if (empty($special)) {
            Flash::error('offers not found');

            return redirect(route('offers.index'));
        }

        $this->SpecialRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('offers.index'));
    }
}

