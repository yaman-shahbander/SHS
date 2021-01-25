<?php
/**
 * File name: web.php
 * Last modified: 2020.06.11 at 15:08:31
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|kjkjbkjbkjb
*/
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    

    return "Cache is cleared";
});
Route::get('/verify/{api_token}', 'AppSettingController@verifyUser');

Route::get('/logout',function (){
    return redirect('/');
});
////////
///
Route::get('/home','HomeController@index')->name('home');
Route::post('/send-notification', 'NotificationController@store')->name('send.notification');


/*Route::resource('chats', 'ChatController')->except([
            'show','create','update','destroy','edit','store']);*/

Route::get('login/{service}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{service}/callback', 'Auth\LoginController@handleProviderCallback');
Auth::routes();

//Route::get('payments/failed', 'PayPalController@index')->name('payments.failed');
//Route::get('payments/razorpay/checkout', 'RazorPayController@checkout');
//Route::post('payments/razorpay/pay-success/{userId}/{deliveryAddressId?}/{couponCode?}', 'RazorPayController@paySuccess');
//Route::get('payments/razorpay', 'RazorPayController@index');
//
//Route::get('payments/paypal/express-checkout', 'PayPalController@getExpressCheckout')->name('paypal.express-checkout');
//Route::get('payments/paypal/express-checkout-success', 'PayPalController@getExpressCheckoutSuccess');
//Route::get('payments/paypal', 'PayPalController@index')->name('paypal.index');

Route::get('firebase/sw-js', 'AppSettingController@initFirebase');
Route::group(['prefix' => LaravelLocalization::setLocale()], function()
{

    Route::get('storage/app/public/{id}/{conversion}/{filename?}', 'UploadController@storage');
    Route::middleware('auth')->group(function () {
        Route::middleware('Checklanguage')->group(function () {
            Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
        Route::get('/', 'DashboardController@index')->name('dashboard');

        Route::post('uploads/store', 'UploadController@store')->name('medias.create');
        Route::get('profile', 'UserController@profile')->name('users.profile');
        Route::get('vendors/profile', 'VendorController@profile')->name('vendors.profile');
        Route::post('users/remove-media', 'UserController@removeMedia');
        Route::resource('users', 'UserController');
        Route::get('showAdmin','UserController@showAdmin')->name('showAdmin');
        Route::get('superAdmin','UserController@superAdmin')->name('superAdmin');
        Route::resource('delegate','DelegateController');
        Route::resource('subscription','SubscriptionController');
        Route::get('dashboard', 'DashboardController@index')->name('dashboard');

        Route::group(['middleware' => ['permission:medias']], function () {
            Route::get('uploads/all/{collection?}', 'UploadController@all');
            Route::get('uploads/collectionsNames', 'UploadController@collectionsNames');
            Route::post('uploads/clear', 'UploadController@clear')->name('medias.delete');
            Route::get('medias', 'UploadController@index')->name('medias');
            Route::get('uploads/clear-all', 'UploadController@clearAll');
        });
        Route::group(['middleware' => ['permission:chats.index']],function(){
        Route::get('chats','ChatController@index');
            Route::get('chat/{id}','ChatController@view_chat');
        });
        Route::group(['middleware' => ['permission:messages.index']],function(){
        Route::get('chats','ChatController@index');
            Route::get('chat/{id}','ChatController@view_chat');
        });

        Route::group(['middleware' => ['permission:vendors.index']],function(){
            Route::get('vendors','VendorController@index')->name('vendors.index');
            /*Route::get('vendor/{id}','VendorController@show_vendor');*/
            Route::get('vendors/create','VendorController@create')->name('vendors.create');
            Route::post('vendors/store','VendorController@store')->name('vendors.store');
        });

        Route::group(['middleware' => ['permission:permissions.index']], function () {
            Route::get('permissions/role-has-permission', 'PermissionController@roleHasPermission');
            Route::get('permissions/refresh-permissions', 'PermissionController@refreshPermissions');
        });
        Route::group(['middleware' => ['permission:permissions.index']], function () {
            Route::post('permissions/give-permission-to-role', 'PermissionController@givePermissionToRole');
            Route::post('permissions/revoke-permission-to-role', 'PermissionController@revokePermissionToRole');
        });
        Route::group(['middleware' => ['permission:suggestions.index']], function () {
                Route::resource('suggestions', 'SuggestionController')->except([
                'show','create','update','destroy','edit','store']);
            });

        Route::group(['middleware' => ['permission:languages.index']], function () {
            Route::get('languages', 'AppSettingController@languages_index');
            Route::post('languages', 'AppSettingController@languages_update');
        });

        Route::group(['middleware' => ['permission:app-settings']], function () {
            Route::prefix('settings')->group(function () {
                Route::resource('permissions', 'PermissionController');
                Route::resource('roles', 'RoleController');
                Route::resource('customFields', 'CustomFieldController');
    //            Route::resource('currencies', 'CurrencyController')->except([
    //                'show'
    //            ]);
                Route::get('users/login-as-user/{id}', 'UserController@loginAsUser')->name('users.login-as-user');
                Route::patch('update', 'AppSettingController@update');
                Route::patch('translate', 'AppSettingController@translate');
                Route::get('sync-translation', 'AppSettingController@syncTranslation');
                Route::get('clear-cache', 'AppSettingController@clearCache');
                Route::get('check-update', 'AppSettingController@checkForUpdates');
                // disable special character and number in route params
                Route::get('/{type?}/{tab?}', 'AppSettingController@index')
                    ->where('type', '[A-Za-z]*')->where('tab', '[A-Za-z]*')->name('app-settings');
            });
        });

    //    Route::post('cuisines/remove-media', 'CuisineController@removeMedia');
    //    Route::resource('cuisines', 'CuisineController')->except([
    //        'show'
    //    ]);

    //    Route::post('restaurants/remove-media', 'RestaurantController@removeMedia');
    //    Route::get('requestedRestaurants', 'RestaurantController@requestedRestaurants')->name('requestedRestaurants.index'); //adeed
    //    Route::resource('restaurants', 'RestaurantController')->except([
    //        'show'
    //    ]);

    Route::post('categories/remove-media', 'CategoryController@removeMedia');
    Route::post('msubcategory/remove-media', 'SubCategoryController@removeMedia');
    Route::resource('categories', 'CategoryController')->except([
        'show'
    ]);



    Route::resource('faqCategories', 'FaqCategoryController')->except([
        'show'
    ]);

//    Route::resource('orderStatuses', 'OrderStatusController')->except([
//        'create', 'store', 'destroy'
//    ]);;

//    Route::post('foods/remove-media', 'FoodController@removeMedia');
//    Route::resource('foods', 'FoodController')->except([
//        'show'
//    ]);

//    Route::post('galleries/remove-media', 'GalleryController@removeMedia');
//    Route::resource('galleries', 'GalleryController')->except([
//        'show'
//    ]);

//    Route::resource('foodReviews', 'FoodReviewController')->except([
//        'show'
//    ]);

    //Route::get('suggestions', 'SuggestionController@index');
//    Route::resource('nutrition', 'NutritionController')->except([
//        'show'
//    ]);

//    Route::post('extras/remove-media', 'ExtraController@removeMedia');
//    Route::resource('extras', 'ExtraController');

//    Route::resource('payments', 'PaymentController')->except([
//        'create', 'store', 'edit', 'destroy'
//    ]);;

    Route::resource('faqs', 'FaqController')->except([
        'show'
    ]);
    ///
//    Route::resource('restaurantReviews', 'RestaurantReviewController')->except([
//        'show'
//    ]);
//
//    Route::resource('favorites', 'FavoriteController')->except([
//        'show'
//    ]);
//
//    Route::resource('orders', 'OrderController');

//    Route::resource('notifications', 'NotificationController')->except([
//        'create', 'store', 'update', 'edit',
//    ]);;
//
//    Route::resource('carts', 'CartController')->except([
//        'show', 'store', 'create'
//    ]);
//    Route::resource('deliveryAddresses', 'DeliveryAddressController')->except([
//        'show'
//    ]);
//
//    Route::resource('drivers', 'DriverController')->except([
//        'show'
//    ]);
//
//    Route::resource('earnings', 'EarningController')->except([
//        'show', 'edit', 'update'
//    ]);

//    Route::resource('driversPayouts', 'DriversPayoutController')->except([
//        'show', 'edit', 'update'
//    ]);
//
//    Route::resource('restaurantsPayouts', 'RestaurantsPayoutController')->except([
//        'show', 'edit', 'update'
//    ]);
//
//    Route::resource('extraGroups', 'ExtraGroupController')->except([
//        'show'
//    ]);

//    Route::post('extras/remove-media', 'ExtraController@removeMedia');
//
//    Route::resource('extras', 'ExtraController')->except([
//        'show'
//    ]);
//    Route::resource('coupons', 'CouponController')->except([
//        'show'
//    ]);
    Route::post('slides/remove-media','SlideController@removeMedia');
        Route::resource('slides', 'SlideController')->except([
        'show'
    ]);

    });

    Route::resource('subcategory', 'SubCategoryController')->except([
        'show'
    ]);

    Route::resource('suggested/vendor', 'VendorsSuggestedController');
    Route::post('store_vendors_suggested','VendorsSuggestedController@store_vendors_suggested')->name('store_vendors_suggested');
    Route::resource('country', 'CountryController')->except([
        'show'
    ]);

    Route::resource('city', 'CityController')->except([
        'show'
    ]);
    //to get cities


    ///for special offers

    Route::resource('special/offers', 'SpecialOffersController')->except([
        'show'
    ]);

    Route::resource('bannedUsers', 'BannedUsersController')->except([
        'show'
    ]);
    Route::resource('pending/reviews','PendingReviewsController');
    Route::resource('reviews/approved','ReviewsController');
    Route::get('reviews/approve','PendingReviewsController@approve')->name('reviews.approve');
    Route::resource('rating','RatingController');
    Route::get('/subcategoryVendor/{id}', 'SubCategoryController@getSubcategoryVendors');
    Route::resource("/homeOwnerFavorites", "favoriteController");
    //Route::get('/homeOwnerFavorites/{id}', 'favoriteController@getSubcategoryVendors');
    Route::resource('/vendorRegistration', 'DurationController');
    Route::resource("/durationOffer", "durationOffersController");
    Route::resource('/balance', 'BalanceController');
    Route::get('{id}/addBalance', 'BalanceController@addBalance')->name('balance.add');
    Route::put('{id}/balanceaddUpdate', 'BalanceController@balanceaddUpdate')->name('balance.addupdate');
    Route::resource('/transfer', 'TransferTransactionController');
    Route::get('/transferHistory/{id}', 'TransferTransactionController@transactionHistory');
    Route::get('user/profile', 'UserController@userprofile')->name('user.profile');
    Route::resource('/notification', 'NotificationController');
    Route::get('/fee', 'VendorController@featuredfeeFunction')->name('vendor.fee');
    Route::post('/feeSave', 'VendorController@savefeeFunction')->name('fee.save');
    Route::get('/mousa', 'HomeController@mousa');



    /*
    * This is the main app route [Chatify Messenger]
    */
    Route::prefix('chatify')->group(function () {


        Route::get('/', 'vendor\Chatify\MessagesController@index')->name(config('chatify.path'));

        /**
         *  Fetch info for specific id [user/group]
         */
        Route::post('/idInfo', 'vendor\Chatify\MessagesController@idFetchData');

        /**
         * Send message route
         */
        Route::post('/sendMessage', 'vendor\Chatify\MessagesController@send')->name('send.message');

        /**
         * Fetch messages
         */
        Route::post('/fetchMessages', 'vendor\Chatify\MessagesController@fetch')->name('fetch.messages');

        /**
         * Download attachments route to create a downloadable links
         */
        Route::get('/download/{fileName}', 'vendor\Chatify\MessagesController@download')->name(config('chatify.attachments.route'));

        /**
         * Authintication for pusher private channels
         */
        Route::post('/chat/auth', 'vendor\Chatify\MessagesController@pusherAuth')->name('pusher.auth');

        /**
         * Make messages as seen
         */
        Route::post('/makeSeen', 'vendor\Chatify\MessagesController@seen')->name('messages.seen');

        /**
         * Get contacts
         */
        Route::post('/getContacts', 'vendor\Chatify\MessagesController@getContacts')->name('contacts.get');

        /**
         * Update contact item data
         */
        Route::post('/updateContacts', 'vendor\Chatify\MessagesController@updateContactItem')->name('contacts.update');


        /**
         * Star in favorite list
         */
        Route::post('/star', 'vendor\Chatify\MessagesController@favorite')->name('star');

        /**
         * get favorites list
         */
        Route::post('/favorites', 'vendor\Chatify\MessagesController@getFavorites')->name('favorites');

        /**
         * Search in messenger
         */
        Route::post('/search', 'vendor\Chatify\MessagesController@search')->name('search');

        /**
         * Get shared photos
         */
        Route::post('/shared', 'vendor\Chatify\MessagesController@sharedPhotos')->name('shared');

        /**
         * Delete Conversation
         */
        Route::post('/deleteConversation', 'vendor\Chatify\MessagesController@deleteConversation')->name('conversation.delete');

        /**
         * Delete Conversation
         */
        Route::post('/updateSettings', 'vendor\Chatify\MessagesController@updateSettings')->name('avatar.update');

        /**
         * Set active status
         */
        Route::post('/setActiveStatus', 'vendor\Chatify\MessagesController@setActiveStatus')->name('activeStatus.set');


        /*
        * [Group] view by id
        */
        Route::get('/group/{id}', 'vendor\Chatify\MessagesController@index')->name('group');

        /*
        * user view by id.
        * Note : If you added routes after the [User] which is the below one,
        * it will considered as user id.
        *
        * e.g. - The commented routes below :
        */
// Route::get('/route', function(){ return 'Munaf'; }); // works as a route
        Route::get('/{id}', 'vendor\Chatify\MessagesController@index')->name('user');
// Route::get('/route', function(){ return 'Munaf'; }); // works as a user id
    });
});
});
