<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CityRepository;
use App\Models\User;

class cityApiController extends Controller
{

    /** @var  CountryRepository */
    private $cityRepository;


    public function __construct(CityRepository $cityRepo)
    {
        $this->cityRepository = $cityRepo;
    }


    public function index(Request $request) {
        $country_id = $request->id;

        $cities = $this->cityRepository->where(['country_id'=> $country_id])->get(["id", "city_name"]);

        return $this->sendResponse($cities->toArray(), 'Cities retrieved successfully');
    }

    public function store(Request $request) {
        $id = $request->id; // vendor ID
        $vendor = User::find($id);
        $cities = $request->cities;

        if ($vendor->vendor_city()->sync($cities)) {
            return $this->sendResponse($vendor->vendor_city->makeHidden('pivot')->toArray(), 'Citites saved successfully');
        } else {
            return $this->sendResponse([], 'Error');
        }
    }
}
