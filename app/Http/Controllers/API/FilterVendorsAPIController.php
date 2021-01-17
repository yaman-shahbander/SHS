<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\specialOffers;
use App\Models\Homeowner_filter;

class FilterVendorsAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = $request->id;
        $userSettings = Homeowner_filter::where('homeOwner_id', $id)->get(['homeOwner_id', 'vendor_filter']);
        return $this->sendResponse($userSettings->toArray(), 'Settings retrieved successfully');
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
        $homeowner_id =  $request->id;
        $userSettings = Homeowner_filter::where('homeOwner_id', $homeowner_id)->get();

        if(count($userSettings) > 0) {

            return $this->sendResponse($userSettings->toArray(), 'Error setting is exist');
        }

        $userSettings = Homeowner_filter::create([
            'homeOwner_id'  => $homeowner_id,
            'vendor_filter' => $request->filter_setting
        ]);
        return $this->sendResponse($userSettings->toArray(), 'Setting added successfully');

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
    public function update($id, Request $request)
    {
        
        $userSettings = Homeowner_filter::where('homeOwner_id', $id)->first(); 
        if(empty($userSettings)) {
            return $this->sendResponse($userSettings->toArray(), 'Error setting is not exist');
        } 

        $userSettings->vendor_filter = $request->filter_setting;
        if ($userSettings->save())
        return $this->sendResponse($userSettings->toArray(), 'setting updated successfully');
        return $this->sendResponse($userSettings->toArray(), 'Error in updating setting');
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
