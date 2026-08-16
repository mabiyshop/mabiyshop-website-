<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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


Route::prefix('v1')->group(function () {
	Route::get('get/pathao/access/tocken', 'Api\ApiController@getAccessTocken');
	Route::any('pathao-status-webhook', 'Api\ApiController@PathaoStatusWebhook');

    Route::get('sliders', 'Api\ApiController@sliders');
    Route::get('brands', 'Api\ApiController@brands')->middleware('localization');
	Route::get('get-search-brands', 'Api\ApiController@searchBrands');
	
    Route::get('featured-seller', 'Api\ApiController@featured_seller');
    Route::get('categories', 'Api\ApiController@categories')->middleware('localization');
	Route::get('site-info', 'Api\ApiController@site_info')->middleware('localization');
	
	
    Route::get('search-categories', 'Api\ApiController@searchcategories');
 
	


    Route::get('category/{id}', 'Api\ApiController@singleCategory');
    Route::get('get-category-by-slug/{slug}', 'Api\ApiController@singleCategorybySlug');
   
	
	//Grocery
	Route::get('get-grocery-categories', 'Api\ApiController@groceryCategories');
	Route::get('get-grocery-flash-deals', 'Api\ApiController@getGroceryFlashDeals');
	Route::get('get-daily-grocery-deals-product', 'Api\ApiController@getDailyGroceryDealsProduct')->middleware('localization');
	Route::get('get-grocery-products-you-may-like', 'Api\ApiController@groceryProductsYouMayLike');
	Route::get('get-grocery-ads-and-sponsored', 'Api\ApiController@adsAndSponsored');

	
	

	Route::get('get-flash-deals', 'Api\ApiController@getFlashDeals'); 
	Route::get('get-flash-deal/{slug}', 'Api\ApiController@getFlashDeal');
	Route::get('get-category-wise-flash-deal/{category_id}/{slug}', 'Api\ApiController@CategoryWisegetFlashDeal');
	

    Route::get('get-products-by-category', 'Api\ApiController@getProductsByCategoryId');
    Route::get('get-products-by-brand', 'Api\ApiController@getProductsByBrandId');
    Route::get('get-products-by-seller', 'Api\ApiController@getProductsBySellerId');
	Route::get('get-seller-information', 'Api\ApiController@sellerInformation');



    Route::get('get-product/{slug}', 'Api\ApiController@getProductBySlug')->middleware('localization');
	Route::any('get-search-product', 'Api\ApiController@getSearchProduct');

	//Products
	Route::get('get-onsale-product', 'Api\ApiController@getOnsaleProduct')->middleware('localization');
	Route::get('get-featured-product', 'Api\ApiController@getFeaturedProduct')->middleware('localization');
	Route::get('get-bestSelling-product', 'Api\ApiController@getbestSellingProduct');
	Route::get('get-mostViewed-product', 'Api\ApiController@getmostViewedProduct')->middleware('localization');
	Route::get('get-newArrival-product', 'Api\ApiController@getnewArrivalProduct');
	Route::get('get-flashsale-product', 'Api\ApiController@getflashSaleProduct')->middleware('localization');
	
	Route::post('get-cart', 'Api\ApiController@getCart');
	Route::get('check-user', 'Api\ApiController@checkUuser');
	Route::post('get-sub-category', 'Api\ApiController@SubCategoryById');
	Route::get('get-all-sellers', 'Api\ApiController@getAllSellers');
	Route::post('get-all-sellers-post', 'Api\ApiController@getAllSellersPost');
	
	//User
	Route::post('login', 'Api\ApiController@login');
	Route::post('user-register', 'Api\ApiController@userRegister');
	Route::get('get-user-details','Api\ApiController@get_user_details');
	Route::post('update-user-details','Api\ApiController@update_user_details');
	Route::get('get-address','Api\ApiController@getAddress');
	Route::post('vendor-register', 'Api\ApiController@vendorRegister');
	Route::post('corporate-user-register', 'Api\ApiController@corporateUserRegister');
	
	Route::get('get-my-affiliate-details','Api\ApiController@getMyAffiliateDetails');
	Route::post('affiliate-withdrawal-request','Api\ApiController@affiliateWithdrawalRequest');

	
	
	

	//Compare
	Route::get('get-compare-list','Api\ApiController@initCompare');
	Route::post('add-to-compare', 'Api\ApiController@addToCompare');
	Route::post('check-compare', 'Api\ApiController@checkCompare');
	Route::post('remove-compare', 'Api\ApiController@removeCompare');

	//Wishlist
	Route::get('/initwishlist', 'Api\ApiController@initWishlist');
	Route::post('add-to-wishlist', 'Api\ApiController@addToWishlist');
	Route::post('remove-wishlist', 'Api\ApiController@removeWishlist');


	
	//Review
	Route::post('add-review', 'Api\ApiController@review');
	Route::get('get-all-rating/{productId}', 'Api\ApiController@AllRating');
	Route::get('get-review-by-product-id/{productId}', 'Api\ApiController@reveiwByProductId');
	Route::post('check-reviewed', 'Api\ApiController@checkReviewed');
	//Vendor rating
	Route::get('get-seller-rating/{vendor_id}', 'Api\ApiController@getSellerRating');

	
	Route::get('get-seller-product-comments/{slug}', 'Api\ApiController@get_seller_product_comments');
	
	
	//Notifications
	Route::get('get-notifications','Api\ApiController@getNotifications');
	Route::get('get/customer/notifications','Api\ApiController@getUserwiseNotifications');
	Route::get('get/seller/notifications','Api\VendorApiController@getVendorwiseNotifications');
	Route::post('view-notification','Api\ApiController@viewNotification');
	
	
	
	
	
	//Simillar product 
	Route::get('/get-simillar-product', 'Api\ApiController@getSimillarProduct');
	Route::get('get-brand-info-by-id/{brandId}', 'Api\ApiController@brandInfoByID');
	Route::get('get-products-by-seller-slug/{slug}', 'Api\ApiController@sellerProductsBySlug');



	Route::post('newsletter-subscribtion', 'Api\ApiController@newsletterSubscribtion');
	


	//Cart
	Route::get('/inituser', 'Api\ApiController@initUser');
	Route::get('/initcart', 'Api\ApiController@initCart');
	Route::get('/initCompare', 'Api\ApiController@initCompare');
	
	Route::post('/remove-cart-item', 'Api\ApiController@RemoveCartItem');
	Route::post('add-to-cart', 'Api\ApiController@addToCart');
	Route::post('restock-request', 'Api\ApiController@restockRequest');
	
	Route::post('update-qty', 'Api\ApiController@updateQty');
	Route::post('update-shipping-option', 'Api\ApiController@updateShippingOption');
    Route::post('variable-add-to-cart', 'Api\ApiController@variableAddToCart');
    Route::post('digital-add-to-cart', 'Api\ApiController@digitalAddToCart');
	
	//Order
	Route::post('order', 'Api\ApiController@Order');
	Route::get('pay-again/{order_id}', 'Api\ApiController@payAgain');
	Route::get('digital-payment-link/{order_id}', 'Api\ApiController@digitalPaymentLink');
	Route::get('cancel-order/{order_id}', 'Api\ApiController@cancelOrder');
	Route::post('product-recieve-confirmation/{order_details_id}', 'Api\ApiController@productRecieveConfirmation');

	Route::post('order-auto-renewal', 'Api\ApiController@orderAutoRenewal');
	Route::post('cancel-order-auto-renewal', 'Api\ApiController@cancelOrderAutoRenewal');

	
	Route::post('order-status-success', 'SslCommerzPaymentController@success');
	Route::post('order-status-failed', 'SslCommerzPaymentController@failed');
	Route::post('order-status-canceled', 'SslCommerzPaymentController@cancel');
	
	
	Route::post('shipping-information', 'Api\ApiController@shippingInformation');
	Route::get('get-shipping-information', 'Api\ApiController@getShippingInformation');
	Route::post('get-coupon-amount', 'Api\ApiController@getCouponAmount');
	Route::get('get-user-orders', 'Api\ApiController@ordersByUserId');
	Route::get('get-user-quatations', 'Api\ApiController@quatationsByUserId');
	Route::get('get-order-list', 'Api\ApiController@getOrderList');
	Route::get('get-single-order/{order_id}', 'Api\ApiController@getSingleOrder');
	Route::get('download-file/{product_id}/{order_id}', 'Api\ApiController@downloadFile');
	
	Route::get('get-single-quatation/{id}', 'Api\ApiController@getSingleQuatation');
	Route::get('get-return-product', 'Api\ApiController@getReturnProduct');
	Route::post('request-for-quatation', 'Api\ApiController@requestForQuatation');
	

	//Password
	Route::post('change-password', 'Api\ApiController@changePassword');
	Route::post('forgot-password', 'Api\ApiController@forgotPassword');
	

	//Get product
	Route::get('get-category-search/{data}', 'Api\ApiController@categorySearch');

	Route::get('get-promotion-product/{product_type}', 'Api\ApiController@getPromotionProduct');
	Route::get('get-groceries', 'Api\ApiController@getGroceries')->middleware('localization');

	Route::get('get-category-product/{slug}', 'Api\ApiController@getCategoryProduct')->middleware('localization');
	Route::get('get-promotional-category-product/{slug}', 'Api\ApiController@getPromotionalCategoryProduct')->middleware('localization');
	Route::get('get-all-product', 'Api\ApiController@getAllProduct')->middleware('localization');
	
	//Location
	Route::post('/resolve-address-location','Api\ApiController@resolveAddressLocation');
	Route::get('/get-district/{id}','Api\ApiController@get_district');
	Route::get('/get-upazila/{id}','Api\ApiController@get_upazila');
	Route::get('/get-union/{id}','Api\ApiController@get_union');
	Route::get('/get-shipping-rates/{district_id}/{product_id}/{seller_id}','Api\ApiController@get_shipping_rates');
	
	Route::post('update-default-address', 'Api\ApiController@updateDefaultAddress');
	Route::post('add-new-address', 'Api\ApiController@addNewAddress');
	Route::get('delete-address/{id}', 'Api\ApiController@deleteAddress');
	Route::get('get-single-address/{id}', 'Api\ApiController@getSingleAddress');
	Route::post('get-selected-corporate-address', 'Api\ApiController@getSelectedCorporateAddress');
	
	//Social Login
	Route::post('social-login/facebook', 'SocialController@loginWithFacebook');
	Route::post('social-login/google', 'SocialController@loginWithGoogle');

	//Social Login App
	Route::post('app-social-login', 'Api\ApiController@social_login');

	//Voucher
	Route::get('get-voucher', 'Api\ApiController@getCustomerVouchers');
	Route::post('collect-voucher', 'Api\ApiController@collectCustomerVoucher');
	Route::get('get-home-page-voucher', 'Api\ApiController@getHomePageVoucher');
	Route::get('initcollectedvoucher','Api\ApiController@getCollectedVouchers');
	Route::get('inituseablevoucher','Api\ApiController@getUseableVouchers');
	Route::get('get-my-collected-vouchers','Api\ApiController@getMyCollectedVouchers');

	


	// Offers
	Route::get('get-regular-offer', 'Api\ApiController@getCustomerRegularOffers');
	Route::get('get-promotional-offer', 'Api\ApiController@getCustomerPromotionalOffers');

	//OTP
	Route::post('generate-otp', 'Api\ApiController@generateOTP');
	Route::post('generate-otp-for-signup', 'Api\ApiController@generateOTPForSignup');
	Route::post('otp-login', 'Api\ApiController@otpLogin');

	

	
	//Ad
	Route::get('get-add', 'Api\ApiController@getAd');
	Route::get('get-promotional-banner', 'Api\ApiController@getPromotionalBanner');
	
	
	//Check Auth
	Route::post('checkauth', 'Api\ApiController@checkAuth');
	
	//Vouchers
	Route::get('get-collected-vouchers', 'Api\ApiController@getCollectedVouchers');
	Route::get('get-useable-vouchers', 'Api\ApiController@getUseableVouchers');
	//Route::get('get-init-voucher','Api\ApiController@initVouchers');

	//Coupons
	Route::get('get-my-coupons', 'Api\ApiController@getAllCoupons');
	Route::post('by-coupon-using-loyaltypoin', 'Api\ApiController@buyCouponWithLoyaltyPoint');

	//Others
	Route::get('get-promotion-title', 'Api\ApiController@getPromotionTitle');
	Route::get('get-page-content/{slug}', 'Api\ApiController@pageContent')->middleware('localization');
	Route::get('get-static-pages', 'Api\ApiController@staticPages');
	Route::get('get-search-suggetion/{searchContent}', 'Api\ApiController@suggetionProduct');
	Route::post('contact', 'Api\ApiController@contact');
	Route::post('return-request', 'Api\ApiController@returnRequest');
	Route::post('save-what-user-search', 'Api\ApiController@saveWhatUserSearch');

	Route::post('quotation-action', 'Api\ApiController@quotationAction');


	Route::get('get-saller-review', 'Api\ApiController@getSellerReview');



	//Blog
	Route::get('get-blog-categories', 'Api\ApiController@blogCategories')->middleware('localization');
	Route::get('get-blog-by-slug/{slug}', 'Api\ApiController@blogByCategory');
	Route::get('get-blogs', 'Api\ApiController@getBlogs')->middleware('localization');
	Route::get('get-category-wise-blogs/{slug}', 'Api\ApiController@getCategoryWiseBlogs')->middleware('localization');
	
	Route::get('get-single-blog/{slug}', 'Api\ApiController@getSingleBlog')->middleware('localization');


	Route::post('app-link-request','Api\ApiController@appLinkRequest');

	// Career 
	Route::get('get-career', 'Api\ApiController@getCareers')->middleware('localization');
	Route::get('get-career-details/{id}', 'Api\ApiController@getCareerDetails')->middleware('localization');
	Route::post('apply/for/job', 'Api\ApiController@applyForJob');

	Route::get('get-single-product-offer/{slug}', 'Api\ApiController@getSingleProductOffersDetails');
	Route::get('get-single-product-shipping/{city_id}/{zone_id}/{product_id}/{qty}', 'Api\ApiController@getSingleProductShippingCost');
	Route::post('get-single-product-shipping-order', 'Api\ApiController@getSingleProductOffersOrder');
});






Route::prefix('v1/seller')->group(function () {
	
	// Login & Registration 
	Route::post('login', 'Api\VendorApiController@login');
	Route::post('seller-register', 'Api\VendorApiController@sellerRegister');
	Route::get('seller-profile', 'Api\VendorApiController@sellerProfile');
	
	Route::post('seller-update-profile', 'Api\VendorApiController@sellerProfileUpdate');
	Route::post('seller-change-password', 'Api\VendorApiController@sellerChangePassword');

	// Balance
	Route::get('seller/balance', 'Api\VendorApiController@sellerBalance');
	
	// Dashboard
	Route::get('dashboard', 'Api\VendorApiController@seller_dashboard');
	Route::get('customer/list', 'Api\VendorApiController@seller_customerlist');
	
	// Product 
	Route::get('product/list', 'Api\VendorApiController@product_list');
	Route::get('product/create', 'Api\VendorApiController@product_create');
	Route::post('product/store', 'Api\VendorApiController@product_store');
	Route::get('product/edit/{id}', 'Api\VendorApiController@product_edit');
	Route::post('product/update/{id}', 'Api\VendorApiController@product_update');
	Route::get('product/delete/{id}', 'Api\VendorApiController@product_delete');
	
	
	//Attribute SET
	Route::get('product/attribute-set', 'Api\VendorApiController@attribute_set');
	
	// Orders
	Route::get('order/list', 'Api\VendorApiController@order_list');
	Route::get('order/details/{id}', 'Api\VendorApiController@order_details');
	Route::post('order/update', 'Api\VendorApiController@order_update');
	
	// Notification
	Route::post('view-notification/{id}','Api\VendorApiController@viewNotification');
	
	
	//Brand 
	Route::get('brands', 'Api\VendorApiController@brands');

	// Review
	Route::get('reviews', 'Api\VendorApiController@reviews');
	
	// Accounting
	Route::get('payment/method', 'Api\VendorApiController@sellerPaymentMethod');
	Route::get('product/refund/request', 'Api\VendorApiController@productRefundRequest');
	Route::get('withdrawal/request', 'Api\VendorApiController@withdrawalRequest');
	Route::post('withdrawal/request/send', 'Api\VendorApiController@vendor_withdrawal_request_send');

	// Reports 
	Route::get('sales-reports', 'Api\VendorApiController@sallerSalesReport');
	Route::get('balance-history', 'Api\VendorApiController@sallerBalanceHistory');
	Route::get('get-single-product-wise-sale/{product_id}', 'Api\VendorApiController@getSingleProductWise');
	Route::post('sale-status-wise', 'Api\VendorApiController@getStatusWiseSale');
	Route::get('low-stock-products', 'Api\VendorApiController@lowStockProducts');

});
