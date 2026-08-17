<?php 
Route::group(['prefix'=>'admin'],function(){
    Route::get('/get-attribute-set-details/{attributeSetId}', 'Backend\AttributesetsController@get_attribute_set_details')->name('admin.get.attribute.set.details');
	Route::get('generate-slug', 'Backend\ProductsController@generateSlug');
    Route::post('/change-payment-status', 'Backend\ProductsController@changePaymentStatus')->middleware('auth');
    Route::post('/orders/bulk-accept', 'Backend\ProductsController@bulkAcceptOrders')->middleware('auth')->name('admin.orders.bulk.accept');
    Route::post('/change-notification-status', 'Backend\ProductsController@changeNotificationStatus');
    Route::get('get-live-notification', 'Backend\ProductsController@getLiveNotification');
});
