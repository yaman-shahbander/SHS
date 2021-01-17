<?php

namespace App\Http\Controllers;

use App\DataTables\PendingReviewDataTable;
use App\Models\reviews;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ReviewsRepositry;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class PendingReviewsController extends Controller
{
    /** @var  ReviewsRepositry */
    private $reviewRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(ReviewsRepositry $reviewRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->reviewRepository = $reviewRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the Category.
     *
     * @param PendingReviewDataTable $reviewDataTable
     * @return Response
     */
    public function index(PendingReviewDataTable $reviewDataTable)
    {
        return $reviewDataTable->render('pending_reviews.index');
    }

    /**
     * Show the form for creating a new Category.
     *
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created Category in storage.
     *
     *     *
     * @return Response
     */
    public function store(Request $request)
    {
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
//        $category = $this->categoryRepository->findWithoutFail($id);
//
//        if (empty($category)) {
//            Flash::error('Category not found');
//
//            return redirect(route('categories.index'));
//        }
//
//        return view('categories.show')->with('category', $category);
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

        $pendingReview = $this->reviewRepository->findWithoutFail($id);
       // return $subcategory;


        if (empty($pendingReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('reviews.index'));
        }


        return view('pending_reviews.fields')->with(['review'=> $pendingReview]);
    }

    /**
     * Update the specified reviews in storage.
     *
     * @param int $id
     *
     *
     * @return Response
     */
    public function approve(Request $request)
    {
       // return dd($request->id);
       $review = reviews::find($request->id);


        if (empty($review)) {
            Flash::error('Review not found');
            return redirect(route('reviews.index'));        }

        try {
           // return dd($review);
           // $review->save();
           $review=$this->reviewRepository->update(['approved'=>1],$request->id);


        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }


        Flash::success('Reviews Approved');

        return redirect(route('reviews.index'));    }


        public function update($id,Request $request){
            $review = $this->reviewRepository->findWithoutFail($id);
//$review = reviews::find($id);

        if (empty($review)) {
            Flash::error('Review not found');
            return redirect(route('reviews.index'));        }

        try {
           // return dd($review);
           // $review->save();
           $input = $request->all();
           
           unset($input['homeowner_name']);
           unset($input['vendor_name']);

           $review=$this->reviewRepository->update($input,$id);
          // return dd($input);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }


        Flash::success('Reviews Updated');

        return redirect(route('reviews.index'));  
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
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            Flash::error('Review not found');

            return redirect(route('reviews.index'));
        }

        $this->reviewRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('reviews.index'));
    }



}