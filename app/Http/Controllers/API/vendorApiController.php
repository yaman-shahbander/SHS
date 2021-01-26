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
use App\Models\reviews;

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

        $lang = false;
        if($request->device_code) {
            $vendor = User::where('device_code', $request->device_code)->first();
            $l=$vendor->language;
            $arr=[
                'lang'=>$vendor->language,
                'bool'=>$lang
            ];
            if(!empty($vendor)) {
                $hiddenElems = ['created_at', 'updated_at','custom_fields','has_media']; 
                try {
                    $categories  = Category::with('subCategory')->get(['id', 'name_'.$vendor->language, 'description'])->makeHidden($hiddenElems);
                    $lang = true;
                } catch (\Exception  $e) {
                    $categories  = Category::with('subCategory')->get()->makeHidden($hiddenElems);
                    $lang = false;
                }

                $respone=[];
                    foreach($categories as $category){
                            $respone[]=[
                                 'id'    => $category->id,
                                 'name'  => $lang ? 
                                 $category['name_'. $vendor->language] : $category['name'],
                                 'description'    => $category->description,
                                 'sub_categories' => $category->subCategoryAPI->transform(function($q) use($arr){
                                  
                                    return $arr['bool']? $q->only(['id','name_'.$arr['lang']]):$q->only(['id','name']);
                                 })
                    ];

                }
        
                return $this->sendResponse($respone, 'Categories with subcategories retrieved successfully!');
                 } else {
                    return $this->sendResponse([], 'User not found!');
                 }
            }  else {
                return $this->sendResponse([], 'Error!');
            }
    }

        

    public function workHours(Request $request) {
        if($request->device_code) {
            $vendor = User::where('device_code', $request->device_code)->first();
           if(!empty($vendor)) {


            try {
                $Days  = Day::all(['id', 'name_'.$vendor->language]);
            } 
            catch (\Exception  $e) {
                $Days  = Day::all(['id', 'name_en']);
            }
          
            $respone[]=$Days;
                return $this->sendResponse($respone, 'days retrieved successfully!');
            } else {
               return $this->sendResponse([], 'User not found!');
            }
       } 
        else {
           return $this->sendResponse([], 'Error!');
       }
        
    }
    public function vendorReviews(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            $hiddenElems = ['custom_fields', 'has_media'];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
                $response = $user->clientsAPI->transform(function($q){
                  
                   $r=reviews::find($q->pivot->id);
                    return $q =[
                        'user_id' => $q->id,
                        'name' => $q->name,
                        'description'=> $q->pivot->description,
                        'avatar' => asset('storage/Avatar').'/'. $q->avatar,
                        'reviews' => getFullRating($r)
                    ];
                });

                return $this->sendResponse($response, 'Reviews retrieved successfully!');
            } catch (\Exception $e) {
        return $this->sendError($e->getMessage(), 401);
      }
    } 
  }

  public function backgroundAvatar(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
                
                $response = [
                    'avatar'    => asset('storage/Avatar').'/'. $user->avatar,
                    'background' => asset('storage/vendors_background').'/'. $user->background_profile
                ];

                return $this->sendResponse($response, 'Data retrieved successfully!');
            } catch (\Exception $e) {
                   return $this->sendError($e->getMessage(), 401);
            }
        }
     }

     public function vendorInfo(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
                $response = [
                    'caption'        => $user->caption,
                    'Business_name'  => $user->nickname,
                    'Owner_name'     => $user->name,
                    'About_you'      => $user->description
                ];

                return $this->sendResponse($response, 'Data retrieved successfully!');
                } catch (\Exception $e) {
                    return $this->sendError($e->getMessage(), 401);
             }
         }
     }

     public function vendorInfoUpdate(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
       
                $user->name          = $request->ownerName;
                $user->caption = $request->input('caption')==null ? '':$request->input('caption','');
                $user->description = $request->input('aboutyou')==null ? '':$request->input('aboutyou','');
                $user->nickname = $request->input('businessName')==null ? '':$request->input('businessName','');
                $user->save();

                $response = [
                    'Business_name'  => $user->nickname,
                    'Owner_name'     => $user->name,
                    'About_you'      => $user->description
                ];

                return $this->sendResponse($response, 'Data retrieved successfully!');
                } catch (\Exception $e) {
                    return $this->sendError($e->getMessage(), 401);
             }
         }
     }


    public function contactLocation(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
                $response = [
                    'coordinates'        => [ 
                        'latitude'  => $user->coordinates->latitude,
                        'longitude' => $user->coordinates->longitude
                     ],
                    'address'        => $user->address,
                    'website'        => $user->website,
                    'phone_number'   => $user->phone
                ];

                return $this->sendResponse($response, 'Data retrieved successfully!');
                } catch (\Exception $e) {
                    return $this->sendError($e->getMessage(), 401);
            }
        }
    }

    public function contactLocationUpdate(Request $request) {
        if($request->header('devicetoken')) {
            $response = [];
            try {
                $user = User::where('device_token', $request->header('devicetoken'))->first();
                if (empty($user)) {
                    return $this->sendError('User not found', 401);
                }
                $user->coordinates->latitude = $request->input('latitude')==null ? '':$request->input('latitude','');
                $user->coordinates->latitude  = $request->latitude;
                $user->coordinates->longitude = $request->longitude;
                $user->coordinates->longitude = $request->input('longitude')==null ? '':$request->input('longitude','');

                $user->address = $request->input('address')==null ? '':$request->input('address','');
                $user->website = $request->input('website')==null ? '':$request->input('website','');
                $user->phone = $request->input('phone')==null ? '':$request->input('phone','');
             
        
                $user->save();
                $user->coordinates->save();

                $response = [
                    'coordinates'  => 
                    [
                        
                        'latitude'  => $user->coordinates->latitude,
                        'longitude' => $user->coordinates->longitude,

                    ],
                    'address'  => $user->address,
                    'website'  => $user->website,
                    'phone'    => $user->phone
                ];

                return $this->sendResponse($response, 'Data retrieved successfully!');
                } catch (\Exception $e) {
                    return $this->sendError($e->getMessage(), 401);
             }
         }
     }

     public function supportedSubcategpries(Request $request) {
        $lang = false;
        if($request->header('devicetoken')) {
            $vendor = User::where('device_token', $request->header('devicetoken'))->first();
           $sub=$vendor->subcategories->transform(function($q) {
                                 
                return  $q->id;
             });
             //return $sub;
            $l=$vendor->language;
            $arr=[
                'lang'=>$vendor->language,
                'bool'=>$lang
            ];
            if(!empty($vendor)) {
                $ids=[3,4,7];
                $hiddenElems = ['created_at', 'updated_at','custom_fields','has_media']; 
                try {
                    $categories  = Category::with('subCategory')->get(['id', 'name_'.$vendor->language, 'description'])->transform(function($q) use($sub) {
                                 
                          $q->subCategory->transform(function($q) use($sub) {
                              if(in_array($q->id,$sub->toArray()))
                             $q['check']=1;
                            else
                             $q['check']=0;
                             return $q;
                          });
                          return $q;
                     });
                    $lang = true;
                } catch (\Exception  $e) {
                 return $e->getMessage();
                }

                $respone=[];
                    foreach($categories as $category){
                            $respone[]=[
                                 'id'    => $category->id,
                                 'name'  => $lang ? 
                                 $category['name_'. $vendor->language] : $category['name'],
                                 'description'    => $category->description,
                                 'sub_categories' => $category->subCategory->transform(function($q) use($arr){
                                  
                                    return $arr['bool']? $q->only(['id','name_'.$arr['lang'],'check']):$q->only(['id','name','check']);
                                 })
                    ];

                }
        
                return $this->sendResponse($respone, 'Categories with subcategories retrieved successfully!');
                 } else {
                    return $this->sendResponse([], 'User not found!');
                 }
            }  else {
                return $this->sendResponse([], 'Error!');
            }
    }

     
}


