<?php

namespace App\Http\Controllers;

use App\DataTables\ReviewsDataTable;
use App\Models\reviews;
use App\Repositories\CustomFieldRepository;
use App\Repositories\ReviewsRepositry;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Flash;

class ReviewsController extends Controller
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
     * Display a listing of the Review.
     *
     * @param ReviewsDataTable $reviewDataTable
     * @return Response
     */
    public function index(ReviewsDataTable $reviewDataTable)
    {
        return $reviewDataTable->render('reviews.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\reviews  $reviews
     * @return \Illuminate\Http\Response
     */
    public function show(reviews $reviews)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\reviews  $reviews
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $pendingReview = $this->reviewRepository->findWithoutFail($id);
       // return $subcategory;


        if (empty($pendingReview)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('approved.index'));
        }


        return view('reviews.fields')->with(['review'=> $pendingReview]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\reviews  $reviews
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request){
        $review = $this->reviewRepository->findWithoutFail($id);
//$review = reviews::find($id);

    if (empty($review)) {
        Flash::error('Review not found');
        return redirect(route('approved.index'));        }

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

    return redirect(route('approved.index'));  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\reviews  $reviews
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            Flash::error('Review not found');

            return redirect(route('approved.index'));
        }

        $this->reviewRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('approved.index'));
    }
}
