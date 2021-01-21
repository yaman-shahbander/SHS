<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Homeowner_filter;
use App\Models\Gallery;
use App\Balance;
use App\subCategory;
use App\Models\Fee;
use App\Models\Category;
use App\Models\Day;
class vendorApiController extends Controller
{
    
    public function index(Request $request) {
        $id = $request->id; //subcategory id

        $userID = $request->user_id; // user id

        $userSetting = Homeowner_filter::where('homeOwner_id' ,$userID)->first('vendor_filter'); // user setting

        $respone = [];

        $maxBalance = 0;
        
        $maxBalanceId;

        $Allvendors = User::whereHas('subcategories', function($query) use ($id) {
            $query->where('subcategory_id', $id);
        })->get();

        
        foreach($Allvendors as $vendor) // maxBalanceId
            if ($vendor->Balance->balance > $maxBalance) {
                $maxBalance   = $vendor->Balance->balance;
                $maxBalanceId = $vendor->Balance->id;
            }

        $featuredVendor = User::where('balance_id', $maxBalanceId)->get(); 

        if ($userSetting->vendor_filter == 2) { // Availibility first

            $vendorsAvailability = User::whereHas('subcategories', function($query) use ($id) {
                $query->where('subcategory_id', $id);
            })->orderBy('status_id')->get();

            $vendors = $featuredVendor->merge($vendorsAvailability);

        } else if ($userSetting->vendor_filter == 1) { // Vendors with special offers first
            
            $vendorsSpecialOffers = User::whereHas('specialOffers', function($query) use ($id) {
                $query->where('subcategory_id', $id);
                })->get();

            $vendors = $featuredVendor->merge($vendorsSpecialOffers);

            $vendors = $vendors->merge($Allvendors);

        }

        $i = 0;
        foreach($vendors as $vendor) {
            $respone[$i] = [
                'id'            => $vendor->id,
                'name'          => $vendor->name,
                'email'         => $vendor->email,
                'rating'        => getRating($vendor),
                'description'   => $vendor->description,
                'avatar'        => $vendor->getFirstMediaUrl('avatar','icon')
            ];
            $i++;
        }
        return $this->sendResponse($respone, 'vendors retrieved successfully');
    }

    public function vendorFeefunc(Request $request) {
        $featuredVendor   = User::find($request->id);

        $featuredVendorBalance = $featuredVendor->Balance->balance;

        $count = count(Fee::all()); // if there is a fee value
        if ($count > 0) { 

            $value = Fee::all('fee_amount');

            $value = $value[0]['fee_amount'];

            $featuredVendorBalance -= $value;

         } else {

            $featuredVendorBalance = $featuredVendorBalance;
         }

        Balance::where('id', $featuredVendor->Balance->id)->update([
            'balance' => $featuredVendorBalance]);

        return $this->sendResponse($featuredVendorBalance, 'Fee subtracted successfully');

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

    public function categorySubCatFunc(Request $request) {
        $hiddenElems = ['created_at', 'updated_at','custom_fields','has_media']; 

        $categories  = Category::with('subCategory')->get()->makeHidden($hiddenElems);

        return $this->sendResponse($categories->toArray(), 'Categories with subcategories retrieved successfully!');
    }

    public function workHours(Request $request) {
        $Days = Day::select(['id', 'name_en', 'name_ar'])->get();

        return $this->sendResponse($Days->toArray(), 'Days retrieved successfully!');
    }

    
} 


