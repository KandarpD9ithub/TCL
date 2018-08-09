<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'AngularController@serveApp');
Route::get('/unsupported-browser', 'AngularController@unsupported');
Auth::routes();
Route::get('/home', 'HomeController@index');
Route::resource('manage-tables', 'ManangeTablesController');
Route::get('manage-tables/activeInactive/{tableId}', 'ManangeTablesController@activeInactive');
Route::group(['middleware' => ['web', 'auth', 'authenticate']], function () {
    Route::resource('franchise', 'FranchiseController');
    Route::resource('employee', 'UserController');
    Route::resource('taxes', 'TaxController');
    Route::resource('rules', 'DiscountOfferRuleController');

    Route::post('menu/inactive/{productId}', [
        'as' => 'menu.inactiveMenuItems',
        'uses' => 'MenuController@inactiveMenuItems'
    ]);

    Route::post('menu/remove/product/{productId}', [
        'as' => 'menu.remove.product',
        'uses' => 'MenuController@removeProducts'
    ]);
    Route::resource('menu', 'MenuController');

    Route::get('change-password', [
        'as' => 'change-password',
        'uses' => 'UserController@getChangePassword'
    ]);
    Route::post('change-password/store', [
        'as' => 'change-password.store',
        'uses' => 'UserController@postChangePassword'
    ]);

    Route::resource('product-price', 'ProductPriceController');

    Route::get('change-password', [
        'as' => 'change-password',
        'uses' => 'UserController@getChangePassword'
    ]);
    Route::post('change-password/store', [
        'as' => 'change-password.store',
        'uses' => 'UserController@postChangePassword'
    ]);

    Route::resource('product-price', 'ProductPriceController');

    Route::resource('special-product', 'SpecialProductController');
    Route::get('order/track-time', 'ReportController@trackTime')->name('order.track-time');
    Route::resource('product', 'ProductController');
    Route::get('category/sale', 'ReportController@categoryWise')->name('category.sale');
    Route::get('item/sale', 'ReportController@itemSale')->name('item.sale');
    Route::get('sale/report', 'ReportController@saleReport')->name('sale.report');
    Route::get('nfc_band/report', 'ReportController@NFCBandReport')->name('nfc_band.report');
    Route::get('total/sale', 'ReportController@totalSale')->name('total.sale');
    Route::get('customer/wallet_history', 'ReportController@WalletHistory')->name('customer.wallet_history');
    Route::get('transaction', 'ReportController@transaction');
    Route::get('transaction/excel', [
        'uses' => 'ReportController@transactionExcel',
        'as' => 'transactionExcel'
    ]);
    Route::get('category/sale/excel', [
        'uses' => 'ReportController@categoryWiseExcel',
        'as' => 'categoryWiseExcel'
    ]);
    Route::get('total/sale/excel', [
        'uses' => 'ReportController@totalSaleExcel',
        'as' => 'totalSaleExcel'
    ]);
    Route::get('sale/report/excel', [
        'uses' => 'ReportController@saleReportExcel',
        'as' => 'saleReportExcel'
    ]);
    Route::get('item/sale/excel', [
        'uses' => 'ReportController@itemSaleExcel',
        'as' => 'itemSaleExcel'
    ]);
    Route::get('customer/report/excel', [
        'uses' => 'ReportController@CustoemrReportExcel',
        'as' => 'CustoemrReportExcel'
    ]);
    Route::get('customer/wallet_history/excel', [
        'uses' => 'ReportController@CustoemrWalletHistoryExcel',
        'as' => 'CustoemrWalletHistoryExcel'
    ]);
    Route::resource('category', 'CategoryController');
    Route::get('track-time/excel', [
        'uses' => 'ReportController@trackOrderTimeExcel',
        'as' => 'trackOrderTimeExcel'
    ]);


    //Routes for Customer
    Route::resource('customer', 'CustomerBackEndController');
    Route::post('customer/update_comment/{id}','CustomerBackEndController@update_comment');
    Route::post('customer/recharge','CustomerBackEndController@recharge');
    Route::post('customer/issue_band','CustomerBackEndController@issue_band');
    Route::resource('non-chargeable', 'NonChargeablePeopleController');
    Route::get('non-chargeable/activeInactive/{id}', 'NonChargeablePeopleController@activeInactive');
});

