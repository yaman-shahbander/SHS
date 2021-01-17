<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\SubCategoriesRepository;
use App\Repositories\CustomFieldRepository;
use App\DataTables\SubCategoriesDataTable;
use App\DataTables\SubCaVendorDataTable;
use App\Http\Requests\UpdateSubCategoryRequest;
use Flash;
use App\Repositories\UploadRepository;
use App\subCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class SubCategoryController extends Controller
{

    /** @var  SubCategoriesRepository */
    private $subcategoryRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(SubCategoriesRepository $subcategoryRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->subcategoryRepository = $subcategoryRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the Category.
     *
     * @param SubCategoriesDataTable $subcategoryDataTable
     * @return Response
     */
    public function index(SubCategoriesDataTable $subcategoryDataTable)
    {            
        $categories = subCategory::all();
//     return view('SubCategories.index',compact('categories'));
        return $subcategoryDataTable->render('SubCategories.index');



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hasCustomField = in_array($this->subcategoryRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subcategoryRepository->model());
            $html = generateCustomField($customFields);
        }
        $categories=Category::all();
        if(count($categories)!=0) {

            //  $categories = Category::
//        whereDoesntHave('children')
            //    where('brand_id', $brands->first()->id)->orderby('parent_id')->get();


            return view('SubCategories.create', ['categories'=>$categories,'customFields'=> isset($html) ? $html : false]);
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
        if ($request->category == "0") {
            // var_dump($request->brand);
            Flash::error('Please select category');

            return redirect()->back();
            //

        }
        $subcategories = subCategory::where('name', $request->name)->get();
         foreach ($subcategories as $category) {

                if ($category->category_id == $request->category) {
                    Flash::error('this category is exist');

                    return redirect()->back();
                       }
//
//
        }
        $input = $request->all();
         $input['category_id']=$input['category'];

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subcategoryRepository->model());

        try {
            $subcategory = $this->subcategoryRepository->create($input);
            $subcategory->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            //return getCustomFieldsValues($customFields, $request);
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($subcategory, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));

        return redirect(route('subcategory.create'));


//
//        try {
//
//            if ($request->category == "0") {
//                // var_dump($request->brand);
//                return redirect()->back()->with("error", 'please select category');
//                //
//
//            }
//            $subcategories = subCategory::where('name', $request->name)->get();
//            foreach ($subcategories as $category) {
//
//                if ($category->category_id == $request->category) {
//                    return redirect()->back()->with("error", 'this category is exist');
//                }
//
//
//            }
//
//            $subcategory = new subCategory();
//            $subcategory->category_id = $request->category;
//            $subcategory->description = $request->description;
//            $subcategory->name = $request->name;
//
//            if ($subcategory->save()) {
//                Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));
//
//                return redirect()->back();
//
//            } else {
//                Flash::error('Somthings wrong');
//
//                return redirect()->back();
//            }
//
//
//        }
//        catch (\Exception $e) {
//            Flash::error($e->getMessage());
//
//
//            return redirect()->back();
//
//        }
    }

    /**
     * Display the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $subcategory = $this->subcategoryRepository->findWithoutFail($id);

        if (empty($subcategory)) {
            Flash::error('Category not found');

            return redirect(route('subcategory.index'));
        }

        return view('subcategory.show')->with('category', $subcategory);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories=Category::all();

        $subcategory = $this->subcategoryRepository->findWithoutFail($id);
       // return $subcategory;


        if (empty($subcategory)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('subcategory.index'));
        }


        return view('SubCategories.edit')->with(['subcategory'=> $subcategory,'categories'=>$categories]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param UpdateSubCategoryRequest $request
     */
    public function update($id, UpdateSubCategoryRequest $request)
    {
        $subcategory = $this->subcategoryRepository->findWithoutFail($id);
//return $subcategory;
        if (empty($subcategory)) {
            Flash::error('Category not found');
            return redirect(route('subcategory.index'));
        }
        $input = $request->all();
        $input['category_id']=$input['category'];

        try {
            $subcategory = $this->subcategoryRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($subcategory, 'image');
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.category')]));

        return redirect(route('subcategory.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subcategory = $this->subcategoryRepository->findWithoutFail($id);

        if (empty($subcategory)) {
            Flash::error('Category not found');

            return redirect(route('subcategory.index'));
        }

        $this->subcategoryRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('subcategory.index'));
    }
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $subcategory = $this->subcategoryRepository->findWithoutFail($input['id']);
        try {
            if ($subcategory->hasMedia($input['collection'])) {
                $subcategory->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Display a listing of the Category.
     *
     * @param SubCaVendorDataTable $subCaVendorDataTable
     * @return Response
     */

    public function getSubcategoryVendors($id,SubCaVendorDataTable $subCaVendorDataTable) {
       // $subCaVendorDataTable = $this->subcategoryRepository->findWithoutFail($id);

        return $subCaVendorDataTable->with('id',$id)->render('SubCategories.subcategoryvendor');
    }
}
