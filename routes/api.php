<?php
/**
 * File name: api.php
 * Last modified: 2020.08.20 at 17:21:16
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('homeOwner')->group(function () {
    Route::post('login', 'API\Driver\UserAPIController@login');
    Route::post('register', 'API\Driver\UserAPIController@register');
    Route::post('verified', 'API\Driver\UserAPIController@verify');
    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\Driver\UserAPIController@user');
    Route::get('logout', 'API\Driver\UserAPIController@logout');
    Route::get('settings', 'API\Driver\UserAPIController@settings');
    //Change password
    Route::post('change-password', 'API\AuthController@change_password');
    //change phone
    Route::post('change-phone', 'API\AuthController@change_phone');
    //My reviews
    Route::post('myReviews', 'API\Driver\UserAPIController@myReviews');
    //My favorites(BookMark)
    Route::post('bookMark', 'API\Driver\UserAPIController@bookMark');
    //History of vendors
    Route::post('history', 'API\Driver\UserAPIController@history');
    //Delete account
    Route::delete('deleteAccount', 'API\Driver\UserAPIController@delete');
    //Leave a review
    Route::post('leaveReview', 'API\Driver\UserAPIController@leaveReview');
    //Gmaps location
    Route::post('storegmapLocation', 'API\GmapLocationAPIController@store');

    Route::post('updategmapLocation', 'API\GmapLocationAPIController@update');

    // Route::resource('gmapLocation', 'API\GmapLocationAPIController');
});

Route::prefix('vendor')->group(function () {
    Route::post('login', 'API\Manager\UserAPIController@login');
    Route::post('register', 'API\Manager\UserAPIController@register');
    Route::post('verified', 'API\Manager\UserAPIController@verify');

    Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
    Route::get('user', 'API\Manager\UserAPIController@user');
    Route::get('logout', 'API\Manager\UserAPIController@logout');
    Route::get('settings', 'API\Manager\UserAPIController@settings');
    //Change password
    Route::post('change-password', 'Api\AuthController@change_password');
    //change phone
    Route::post('change-phone', 'API\AuthController@change_phone');
    //My reviews
    Route::post('myReviews', 'API\Manager\UserAPIController@myReviews');
    //My favorites(BookMark)
    Route::post('bookMark', 'API\Manager\UserAPIController@bookMark');
    //History of vendors
    Route::post('history', 'API\Manager\UserAPIController@history');
    //Delete account
    Route::delete('deleteAccount', 'API\Manager\UserAPIController@delete');
    //Leave a review
    Route::post('leaveReview', 'API\Manager\UserAPIController@leaveReview');
    //Gmaps location
    Route::post('storegmapLocation', 'API\GmapLocationAPIController@store');

    Route::post('updategmapLocation', 'API\GmapLocationAPIController@update');

    //Route::resource('gmapLocation', 'API\GmapLocationAPIController');
    //save photo profile
    Route::post('photoProfile', 'API\Manager\UserAPIController@backgroundPic');
    //vendor profile
    Route::post('vendorProfile', 'API\Manager\UserAPIController@vendorprofile');
    //vendor profile
    Route::post('saveVednorData', 'API\Manager\UserAPIController@completeRegistration');
    //categories with subcategories API
    Route::post('categoriesVendor', 'API\vendorApiController@categorySubCatFunc');
    //Woring hours for a new vendor
    Route::post('workHoursDays', 'API\vendorApiController@workHours');
    //User reviews to a specific vendor
    Route::post('vendorReviews', 'API\vendorApiController@vendorReviews');
    //vendor background and avatar
    Route::post('vendorbackgroundAvatar', 'API\vendorApiController@backgroundAvatar');
    //vendor about info
    Route::post('vendorInfo', 'API\vendorApiController@vendorInfo');
    //vendor about info Update
    Route::post('vendorInfoUpdate', 'API\vendorApiController@vendorInfoUpdate');
    //contact and location
    Route::post('contactLocation', 'API\vendorApiController@contactLocation');
    //contact and location update
    Route::post('contactLocationUpdate', 'API\vendorApiController@contactLocationUpdate');
    //supported subcategoies by the vendor
    Route::post('supportedSubcategpries', 'API\vendorApiController@supportedSubcategpries');
    //Add a reply to a homeowner review (vendor reply to a homeowner)
    Route::post('vendorReply', 'API\vendorApiController@vendorReply');
});


Route::post('login', 'API\UserAPIController@login');
Route::post('register', 'API\UserAPIController@register');
Route::post('send_reset_link_email', 'API\UserAPIController@sendResetLinkEmail');
Route::get('user', 'API\UserAPIController@user');
Route::get('logout', 'API\UserAPIController@logout');
Route::get('settings', 'API\UserAPIController@settings');

Route::resource('cuisines', 'API\CuisineAPIController');
Route::resource('categories', 'API\CategoryAPIController');
//get subgategory
Route::post('subcategory', 'API\SubCategoryController@index');
//Country APi Controller
Route::resource('countries', 'API\CountryAPIController');
//city APi Controller
//Route::p('cities', 'API\cityApiController');
Route::post('cities', 'API\cityApiController@index');
//forgot password API
Route::post('forgot-password', 'API\AuthController@forgot_password');
//vendor rating API
Route::post('vendorWithRating', 'API\vendorApiController@index');
//vendor profile API
Route::post('vendorProfile', 'API\vendorApiController@profile');
//User current balance API
Route::post('currentBalance', 'API\MoneyAPIController@currentBalance');
//History of all transactions API
Route::post('transactions', 'API\MoneyAPIController@history');
//Transfer money API
Route::post('transfer', 'API\MoneyAPIController@transferMoney');
//Route::resource('filter', 'API\FilterVendorsAPIController');

//FIlter vendors API
Route::post('getfilter', 'API\FilterVendorsAPIController@index');
//FIlter vendors API
Route::post('storefilter', 'API\FilterVendorsAPIController@store');
//FIlter vendors API
Route::post('updatefilter', 'API\FilterVendorsAPIController@update');

//Route::resource('specialOffers', 'API\SpecialOffersAPIController');

//special offers vendors API
Route::post('getVendorspecialOffers', 'API\SpecialOffersAPIController@index');

//special offers vendors API
Route::post('storeVendorspecialOffers', 'API\SpecialOffersAPIController@store');

//vendor map location
Route::post('vendorLocation', 'API\GmapLocationAPIController@VendorMapDetails');
//chat messaging
Route::post('allMessages','API\ChatAPIController@history');
//featured vendor fee API
Route::post('vendorFee', 'API\vendorApiController@vendorFeefunc');
//Refer a vendor
Route::post('vendorRefer', 'API\vendorApiController@vendorRefer');


Route::resource('restaurants', 'API\RestaurantAPIController');

Route::resource('faq_categories', 'API\FaqCategoryAPIController');
Route::get('foods/categories', 'API\FoodAPIController@categories');
Route::resource('foods', 'API\FoodAPIController');
Route::resource('galleries', 'API\GalleryAPIController');
Route::resource('food_reviews', 'API\FoodReviewAPIController');
Route::resource('nutrition', 'API\NutritionAPIController');
Route::resource('extras', 'API\ExtraAPIController');
Route::resource('extra_groups', 'API\ExtraGroupAPIController');
Route::resource('faqs', 'API\FaqAPIController');
Route::resource('restaurant_reviews', 'API\RestaurantReviewAPIController');
Route::resource('currencies', 'API\CurrencyAPIController');
Route::resource('slides', 'API\SlideAPIController')->except([
    'show'
]);

Route::middleware('auth:api')->group(function () {
    Route::post('Kitchen_confirmed_order','API\OrderAPIController@Kitchen_confirmed_order');
    Route::post('suggestion/store','API\SuggestionAPIController@store');
    Route::group(['middleware' => ['role:driver']], function () {
        Route::prefix('driver')->group(function () {
            Route::resource('orders', 'API\OrderAPIController');
            Route::resource('notifications', 'API\NotificationAPIController');
            Route::post('users/{id}', 'API\UserAPIController@update');
            Route::resource('faq_categories', 'API\FaqCategoryAPIController');
            Route::resource('faqs', 'API\FaqAPIController');
        });
    });
    Route::group(['middleware' => ['role:manager']], function () {
        Route::prefix('manager')->group(function () {
            Route::post('users/{id}', 'API\UserAPIController@update');
            Route::get('users/drivers_of_restaurant/{id}', 'API\Manager\UserAPIController@driversOfRestaurant');
            Route::get('dashboard/{id}', 'API\DashboardAPIController@manager');
            Route::resource('restaurants', 'API\Manager\RestaurantAPIController');
            Route::resource('faq_categories', 'API\FaqCategoryAPIController');
            Route::resource('faqs', 'API\FaqAPIController');
        });
    });
    Route::post('users/{id}', 'API\UserAPIController@update');
    Route::post('/chat_create','API\ChatAPIController@store');
    Route::post('/message_create','API\MessageAPIController@store');

    Route::resource('order_statuses', 'API\OrderStatusAPIController');

    Route::get('payments/byMonth', 'API\PaymentAPIController@byMonth')->name('payments.byMonth');
    Route::resource('payments', 'API\PaymentAPIController');

    Route::get('favorites/exist', 'API\FavoriteAPIController@exist');
    Route::resource('favorites', 'API\FavoriteAPIController');

    Route::resource('orders', 'API\OrderAPIController');

    Route::resource('food_orders', 'API\FoodOrderAPIController');

    Route::resource('notifications', 'API\NotificationAPIController');

    Route::get('carts/count', 'API\CartAPIController@count')->name('carts.count');
    Route::resource('carts', 'API\CartAPIController');

    Route::resource('delivery_addresses', 'API\DeliveryAddressAPIController');

    Route::resource('drivers', 'API\DriverAPIController');

    Route::resource('earnings', 'API\EarningAPIController');

    Route::resource('driversPayouts', 'API\DriversPayoutAPIController');

    Route::resource('restaurantsPayouts', 'API\RestaurantsPayoutAPIController');

    Route::resource('coupons', 'API\CouponAPIController')->except([
        'show'
    ]);
});
//for dashboard
Route::post('user/select','UserController@getcity');
Route::post('subcategory/select','NotificationController@getsubcategory');


/*
if ($request->device_code) {
            $vednor = User::where('device_code', $request->device_code)->first();
            if (!empty($vednor)) {

            } else {

            }
        } else {

        }
*/
