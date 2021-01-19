<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Homeowner_filter;
use App\Models\Gallery;

class vendorApiController extends Controller
{
    public function index(Request $request) {
        $id = $request->id; //subcategory id

        $userID = $request->user_id; // user id

        $userSetting = Homeowner_filter::where('homeOwner_id' ,$userID)->first('vendor_filter'); // user setting

        $respone = [];

        if ($userSetting->vendor_filter == 2) { // Availibility first

            $vendors = User::whereHas('subcategories', function($query) use ($id) {
                $query->where('subcategory_id', $id);
            })->orderBy('status_id')->get();

        } else if ($userSetting->vendor_filter == 1) { // Vendors with special offers first

            $Allvendors = User::whereHas('subcategories', function($query) use ($id) {
                    $query->where('subcategory_id', $id);
                })->get();

            $vendorsSpecialOffers = User::whereHas('specialOffers', function($query) use ($id) {
                $query->where('subcategory_id', $id);
                })->get();

            $vendors = $vendorsSpecialOffers->merge($Allvendors);

        }

        
        
        $i = 0;
        foreach($vendors as $vendor) {
            $respone[$i] = [
                'id'            => $vendor->id,
                'name'          => $vendor->name,
                'email'         => $vendor->email,
                'rating'        => getRating($vendor),
                'description'   => $vendor->description,
                'avatar'   => $vendor->getFirstMediaUrl('avatar','icon')
            ];
            $i++;
        }
        return $this->sendResponse($respone, 'vendors retrieved successfully');
    }

    public function profile(Request $request) { // for homeOwners
        $vendorID = $request->id;
        $respone = [];
        $reviewsHiddenColumns = ['custom_fields', 'media', 'has_media'];
        $vendor = User::find($vendorID);
        $attrs = $vendor->clientsAPI->makeHidden($reviewsHiddenColumns);
        $reviews = [];
        $i = 0;
        
            foreach($attrs as $attr) {
                $reviews[$i]['id'] = $attr->id;
                $reviews[$i]['name'] = $attr->name;
                $reviews[$i]['avatar'] = $attr->getFirstMediaUrl('avatar', 'icon');
                $reviews[$i]['last_name'] = $attr->last_name;
                $reviews[$i]['description'] = $attr->pivot->description;
                $i++;
            }

    

            $respone = [
                'id'             => $vendor->id,
                'name'           => $vendor->name,
                'email'          => $vendor->email,
                'rating'         => getRating($vendor),
                'description'    => $vendor->description,
                'phone'          => $vendor->phone,
                'avatar'         => $vendor->getFirstMediaUrl('avatar', 'icon'),
                'subcategories'  => $vendor->subcategoriesApi->makeHidden('pivot'),
                'offers'         => $vendor->specialOffers->makeHidden(['user_id', 'created_at', 'updated_at']),
                'reviews'        => $reviews,
                'working_hours'  => $vendor->days->makeHidden('pivot'),
                'availability'   => $vendor->vendor_city->makeHidden('pivot'),
                'image'          => $vendor->gallery->transform(function($gallery){
                                    $gallery['image'] = asset('storage/gallery') . '/' . $gallery['image'];
                                    return $gallery['image'];
                                })
            ];
        return $this->sendResponse($respone, 'vendor profile retrieved successfully');

    }

    
} 


