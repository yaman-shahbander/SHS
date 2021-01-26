<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\specialOffers;

class SpecialOffersAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vendor_id = $request->id;
        $vendor    =  specialOffers::where('user_id', $vendor_id)->get(['description', 'title', 'image']);
        $response = [];
        foreach($vendor as $info) {
            $response['description'] = $info->description;
            $response['title']       = $info->title;
            $response['image']       = url('storage/specialOffersPic/' . $info->image);
        }
        return $this->sendResponse($response, 'Offers retrieved successfully');
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
        
        $vendor_specialOffer = new specialOffers();
        $vendor_specialOffer->user_id = $request->id;
        $vendor_specialOffer->description = $request->description;
        $vendor_specialOffer->title = $request->title;
        $vendor_specialOffer->subcategory_id = $request->subcategory_id;
        $vendor_specialOffer->image = "Null.png";
        

        if ($vendor_specialOffer->save()){
            if (!empty ($request->file('image'))) {

            $imageName = uniqid() . $request->file('image')->getClientOriginalName();

            $request->file('image')->move(public_path('storage/specialOffersPic'), $imageName);

            $vendor_specialOffer->update(['image' => $imageName]);

            return $this->sendResponse($vendor_specialOffer->toArray(), 'Offers Saved successfully');

            } else
            return $this->sendResponse($vendor_specialOffer->toArray(), 'Offers Saved successfully with default image');
    }  else
        return $this->sendResponse([], 'Error');
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
