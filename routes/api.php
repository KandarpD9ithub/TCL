<?php

//use Illuminate\Http\Request;

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

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');*/
Route::group(['middleware' => ['BasicAuth']], function () {
    Route::get('orders', 'OrderController@index');
    Route::post('customer/orders', 'OrderController@storeMultipleOrder');
    Route::resource('customer', 'CustomerController');
    Route::get('popularItems', 'OrderController@mostOrderedItems');
    Route::post('taxes', 'OrderController@getAllTaxes');
    Route::get('products', 'OrderController@getAllProducts');
    Route::get('customer/{customerId}/orders', 'CustomerController@customerOrder');
    Route::resource('customer/order', 'OrderController');
    Route::post('customer/order/cancel/{orderId}', 'OrderController@orderCancellation');
    Route::post('order/{orderId}/status', 'OrderController@statusChanged');
    Route::post('order/{orderId}/payment-method', 'OrderController@payment');
    Route::get('order/cancelled', 'OrderController@cancelOrderList');
    Route::get('order/edit-orders', 'OrderController@editOrderList');
    Route::get('menu-popular-products', 'OrderController@popularProductsByCategory');
    Route::get('all/taxes', 'OrderController@getAllTax');
    Route::get('profile', 'ProfileController@profile');
    Route::get('franchise/detail', 'ProfileController@userFranchise');
    Route::get('date/time', 'OrderController@getCurrentTime');
    Route::get('offers', 'OrderController@getOffers');
    Route::get('discount', 'OrderController@getDiscount');
    Route::get('menu', 'OrderController@getMenu');
    Route::post('change_password','CustomerController@change_password');
    Route::post('search/user','CustomerController@searchUser');
    Route::post('customer_band_details','CustomerController@CustomerBandDetails');
    Route::get('get_country','CustomerController@getCountries');
    Route::post('create/band_details','CustomerController@createBandDetails');
    Route::post('check/band_details','CustomerController@CheckBandDetails');
    Route::post('add/customer_pin','CustomerController@addCustomerPin');
    Route::post('update/profile_picture','CustomerController@updateProfilePicture');
    Route::post('add/wallet_history','CustomerController@addWalletHistory');
    Route::post('edit/pin','CustomerController@editPin');
    Route::post('inactive/pin','CustomerController@pinActiveInactive');
    Route::post('forgot/pin','CustomerController@forgotPin');
    Route::post('check/otp','CustomerController@checkOTP');
    Route::post('user/edit','CustomerController@userEdit');
    Route::post('check/customer_pin','CustomerController@checkUserPin');
    Route::post('report/band','CustomerController@ReportBand');
    Route::post('change/pin','CustomerController@changePin');    
    Route::post('add/mobile_id','CustomerController@addMobileId');
    Route::get('nc/list', 'NonChargeablePeopleController@allList'); 
    Route::post('band/return','CustomerController@bandReturn');
    Route::get('getTables','OrderController@getTables');

});
