<template>
    <div>
    <div v-if="loading">
        <section v-if="checkoutStep === 'phone'" id="cart-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-6 col-lg-5">
                    <div class="text-center mb-4">
                        <h4>{{ $t('Checkout') }}</h4>
                        <p class="mb-0">{{ $t('Mobile Number') }}</p>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                                <div class="form-group mb-3">
                                    <label class="small text-muted">{{ $t('Mobile Number') }}</label>
                                    <div class="d-flex">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="width:72px;text-align:center;border-color:#ff2a42;color:#ff2a42;font-weight:600;">+88</span>
                                        </div>
                                        <input type="text" class="form-control mobile_number_login_page" v-model="checkoutPhone" placeholder="01XXXXXXXXX" maxlength="11" @input="normalizePhoneDigits" @keyup.enter.prevent="isValidBangladeshPhone && !phoneLoading && submitInitialPhone()" style="border-color:#ff2a42;">
                                    </div>
                                </div>
                            <button type="button" class="btn btn-primary site_color1 btn-block" @click.prevent="submitInitialPhone" :disabled="phoneLoading || !isValidBangladeshPhone">
                                <span v-if="phoneLoading" class="spinner-border spinner-border-sm"></span>
                                <span v-else>{{ $t('Next') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>

        <section v-if="checkoutStep === 'address' && cartData.sub_total > 0" id="cart-page" class="checkout-premium">
        <div class="container">
            <div class="row cart-page-container">
                <div class="col-12 col-sm-12 col-md-8 col-lg-8 pr-md-3 checkout-order-column">
                    <div class="row shoping-cart-text">
                        <div class="col-12 col-sm-12 col-md-12">
                            <h4 class="d-flex align-items-center justify-content-between">
                                <span>{{ $t('Your Order') }}</span>
                                <small v-if="checkoutItemCount" class="checkout-item-count">{{ checkoutItemCount }} {{ checkoutItemCount === 1 ? $t('item') : $t('items') }}</small>
                            </h4>
                        </div>
                    </div>
                    <div class="cart-calculation checkout-order-card">
                        <table  class="table text-left cart_table" width="100%">
                            <thead>
                                <tr>
                                    <th width="48%">{{ $t('Product Details') }}</th>
                                    <th width="14%">{{ $t('Unit Price') }}</th>
                                    <th class="text-center" width="18%">{{ $t('Quantity') }}</th>
                                    <th width="15%">{{ $t('Total') }}</th>
                                    <th class="text-right" width="5%">{{ $t('Remove') }}</th>
                                </tr>
                            </thead>
                           
                           
                            <tbody v-for="(cartgroup, index) in cartData.cart" :key="index">
                                <tr v-for="(cart, itemIndex) in cartgroup.items" :key="cart.row_id" class="cart_product_group checkout-product-row">
                                
                                    <td class="checkout-product-details">
                                        <div class="table-item">
                                                <div class="media">
                                                    <span class="checkout-product-number">{{ productOrdinal(index, itemIndex) }}</span>
                                                    <img @error="imageLoadError"  class="mr-3 product-cart-img" :src="baseurl+'/'+cart.product.default_image" alt="">
                                                    <div class="media-body">
                                                        <h5 class="mt-0"> <router-link :to="{ name: 'product', params: {slug: cart.product.slug } }"> {{ cart.product.title }} </router-link></h5>
                                                        <div v-if="cart.product_type != 'digital' && logged_in_user_address != 0 && logged_in_user.default_address_id != null" class="select_shipping_options">
                                                                <ul v-if="cart.shipping_options != 0" class="list-group list-group-horizontal">
                                                                    <li :data-product-id="cart.product_id"  data-shipping-method="free_shipping" :data-shipping-cost="0" :data-qty="cart.qty" v-if="cart.shipping_options.free_shipping == 'on'" class="list-group-item"> BDT 0 <br> {{ $t('Free Shipping') }}  <br>  {{ $t('Est. Arrival: Within 7 to 15 days') }} </li>
                                                                    <li :data-product-id="cart.product_id" data-shipping-method="standard_shipping" :data-shipping-cost="cart.shipping_options.standard_shipping" :data-qty="cart.qty" v-if="cart.shipping_options.standard_shipping > 0" class="list-group-item selected_shipping" >BDT {{ cart.shipping_options.standard_shipping }}  <br>{{ $t('Standard Shipping') }}  <br> {{ $t('Est. Arrival: Within 4 to 7 days') }} </li>
                                                                    <li :data-product-id="cart.product_id" data-shipping-method="express_shipping" :data-shipping-cost="cart.shipping_options.express_shipping" :data-qty="cart.qty" v-if="cart.shipping_options.express_shipping > 0" class="list-group-item">BDT {{ cart.shipping_options.express_shipping }} <br> {{ $t('Express Shipping') }}  <br> {{ $t('Est. Arrival: Within 1 to 3 days') }} </li>
                                                                </ul>
                                                                 <ul v-else class="list-group list-group-horizontal">
                                                                    <li :data-product-id="cart.product_id" data-shipping-method="standard_shipping" :data-shipping-cost="150" :data-qty="cart.qty" class="list-group-item selected_shipping">BDT 150 <br> {{ $t('Standard Shipping') }}  <br> {{ $t('Est. Arrival: Within 4 to 7 days') }}</li>
                                                                </ul>
                                                        </div>
    
                                                        <span  v-if="cart.product_type == 'variable'">
                                                            <p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in cart.variable_options" :key="key"> <b>{{key}}</b> : {{vOption}}</p>
                                                        </span>
                                                        <span  v-if="cart.product_type == 'digital'">
                                                            <p v-if="cart.variable_options" class="mb-0 text-capitalize font-13"> <b>{{ $t('Contact Number') }}</b> : {{cart.variable_options}}</p>
                                                        </span>
                                                    </div>
                                                </div>
    
                                        </div> 
                                    </td>
                                    <td class="checkout-unit-price"><div class="table-item">BDT {{ cart.price }}</div></td>
                                    <td class="text-center checkout-quantity-cell">
                                        <div v-if="cart.product.product_type == 'simple' || cart.product.product_type == 'variable'" class="checkout-quantity-control">
                                            <button type="button" @click.prevent="updateCheckoutQty(cart, -1)" :disabled="Number(cart.qty) <= 1 || cartMutationLoading[cart.row_id]" aria-label="Decrease quantity">−</button>
                                            <span>{{ cart.qty }}</span>
                                            <button type="button" @click.prevent="updateCheckoutQty(cart, 1)" :disabled="cartMutationLoading[cart.row_id]" aria-label="Increase quantity">+</button>
                                        </div>
                                        <span v-else>—</span>
                                    </td>
                                    <td class="checkout-line-total-cell"><div class="table-item">BDT {{ Number(cart.price) * Number(cart.qty) }}</div></td>
                                    <td class="text-right checkout-remove-cell">
                                        <button type="button" class="checkout-remove-button" @click.prevent="removeCheckoutItem(cart)" :disabled="cartMutationLoading[cart.row_id]" :aria-label="$t('Remove')">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <section v-if="visibleRelatedProducts.length" class="checkout-related-section">
                        <h5>{{ $t('You May Also Like') }}</h5>
                        <div class="checkout-related-list">
                            <article v-for="product in visibleRelatedProducts" :key="product.id" class="checkout-related-card">
                                <router-link :to="{name:'product', params:{slug:product.slug}}">
                                    <img @error="imageLoadError" :src="baseurl+'/'+product.default_image" :alt="product.title">
                                </router-link>
                                <router-link class="checkout-related-title" :to="{name:'product', params:{slug:product.slug}}">{{ product.title }}</router-link>
                                <strong>BDT {{ product.price_after_offer || product.price }}</strong>
                                <button type="button" class="btn checkout-related-add" @click.prevent="addRelatedProduct(product)" :disabled="relatedCartLoading[product.id]">
                                    <span v-if="relatedCartLoading[product.id]" class="spinner-border spinner-border-sm"></span>
                                    <span v-else>+ {{ $t('Add to Cart') }}</span>
                                </button>
                            </article>
                        </div>
                    </section>
                </div>
                <div class="col-12 col-sm-12 col-md-4 col-lg-4 payment checkout-sidebar">
                    <h5>{{ $t('Shipping information') }}</h5>
    
                    <div v-if="logged_in_user_address != 0 && logged_in_user.default_address_id != null" class="address_details">
                        <ul  v-for="(address,index) in logged_in_user_address" :key="index" v-if="logged_in_user.default_address_id == address.id" >
                            <li> 
    
                                <div class="row p-0">
                                    <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                                    <div class="col-lg-10 p-0"> <span>{{ formatShippingAddress(address) }}</span> </div>
                                    <div class="col-lg-1 pl-0"><i class="fa fa-pencil address_btn" @click.prevent="openCheckoutAddressModal" aria-hidden="true"></i></div>
                                </div>
                            </li>
                            <li> <b><i class="fa fa-phone" aria-hidden="true"></i></b> <span>{{address.shipping_phone}}</span></li>
                            <li v-if="address.shipping_email"> <b><i class="fa fa-envelope-o" aria-hidden="true"></i></b> <span>{{address.shipping_email}}</span> </li>
                        </ul>    
                    </div>
                     <div v-else-if="logged_in_user.id"  class="address_details_alt"> 
                         
                         <div class="row p-0">
                             <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                             
                             <div v-if="logged_in_user.default_address_id == null" class="col-lg-10 p-0"> <p class="required_addtess" data-required-address="true">{{ $t('You need to select your default shipping address') }}.</p> </div>
                             <div v-else class="col-lg-10 p-0"> <p class="required_addtess" data-required-address="true">{{ $t('You need to add your shipping address') }}.</p> </div>
     
                             <div class="col-lg-1 pl-0"><i class="fa fa-pencil address_btn" @click.prevent="openCheckoutAddressModal" aria-hidden="true"></i></div>
                         </div>
                                 
                     </div>
                     <div v-else-if="guestAddressSaved" class="address_details guest-address-summary">
                         <button type="button" class="guest-address-edit" @click.prevent="openCheckoutAddressModal" :aria-label="$t('Edit shipping address')">
                             <i class="fa fa-pencil" aria-hidden="true"></i>
                         </button>
                         <p class="guest-address-name mb-1">{{ guestShippingFirstName }}</p>
                         <p class="mb-1"><i class="fa fa-phone" aria-hidden="true"></i> {{ guestShippingPhone }}</p>
                         <p class="mb-1"><i class="fa fa-map-marker" aria-hidden="true"></i> {{ guestShippingAddress }}</p>
                         <p v-if="guestLocationDisplay" class="mb-1">{{ guestLocationDisplay }}</p>
                         <p v-if="selectedAddressUnionTitle" class="mb-0">{{ selectedAddressUnionTitle }}</p>
                         <small v-if="guestAddressFromHistory" class="previous-address-badge">আপনার পূর্বের ডেলিভারি ঠিকানা</small>
                     </div>
                     <div v-else>
                         <div class="row p-0">
                             <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                             <div class="col-lg-10 p-0"> <p class="required_addtess" data-required-address="true">{{ $t('You need to add your shipping address') }}.</p> </div>
                             <div class="col-lg-1 pl-0"><i class="fa fa-pencil address_btn" @click.prevent="openCheckoutAddressModal" aria-hidden="true"></i></div>
                         </div>
                     </div>

                    <div v-if="guestAddressSaved || (logged_in_user_address != 0 && logged_in_user.default_address_id != null)" class="delivery-estimate-card">
                        <i class="fa fa-truck" aria-hidden="true"></i>
                        <span><strong>{{ deliveryAreaLabel }}</strong><small>{{ deliveryEstimate }}</small></span>
                    </div>
    
                    <div class="note">
                         <textarea type="text" v-model="note" class="form-control form_note" rows="3" :placeholder="$t('Write a note here')+'..'"></textarea>
                    </div>
    
    
                    <div v-if="collectedVoucher.length" class="collect_voucher_modal">
                        <h5>{{ $t('Collected Voucher') }}</h5>
                        <div  v-for="(cv,index) in collectedVoucher" :key="index">
                            <p><input type="hidden" name="collected_voucher" class="collected_voucher_radio" :value="cv.voucher_id" required> 
                            <img @error="imageLoadError" style="width:100%" :src="baseurl+'/'+cv.voucher.banner" alt="">
                            </p>
                        </div>
                    </div>
    
                    <div class="voucher_button mt-3">
                        <ul class="list-group list-group-horizontal">
                            <li v-if="useableVouchers.length" style="width:100%" class="list-group-item">
                                <a data-toggle="modal" data-target=".use_voucher_modal" href="javascript:void(0)">
                                    <p class="text-center mb-0"> {{ $t('Use Voucher') }}</p>
                                </a> 
                                
                            </li>
                        </ul>
                    </div>
                    <div v-if="!isFreeDeliveryEligible" class="checkout-free-delivery-progress">
                        <div class="checkout-free-delivery-title">
                            <span class="checkout-free-delivery-icon"><i class="fa fa-gift" aria-hidden="true"></i></span>
                            <div><strong>Free Delivery!</strong><p>আর মাত্র ৳{{ freeDeliveryRemaining }} যোগ করলে ডেলিভারি চার্জ ফ্রি</p></div>
                        </div>
                        <div class="checkout-free-delivery-bar">
                            <div class="checkout-free-delivery-fill" :style="{ width: freeDeliveryProgress + '%' }"></div>
                        </div>
                        <div class="checkout-free-delivery-footer"><span>৳{{ freeDeliveryRemaining }} আরও লাগবে</span><span>৳{{ formattedFreeDeliveryThreshold }}</span></div>
                    </div>
                    <div v-else class="checkout-free-delivery-progress checkout-free-delivery-eligible">
                        <div class="checkout-free-delivery-title">
                            <span class="checkout-free-delivery-icon"><i class="fa fa-gift" aria-hidden="true"></i></span>
                            <div><strong>Free Delivery!</strong><p>আপনি ফ্রি ডেলিভারি পেয়েছেন</p></div>
                        </div>
                        <div class="checkout-free-delivery-bar"><div class="checkout-free-delivery-fill" style="width:100%"></div></div>
                        <div class="checkout-free-delivery-footer"><span class="free-delivery-label">FREE</span><span>৳{{ formattedFreeDeliveryThreshold }}</span></div>
                    </div>
                    <div v-if="checkoutOfferEnabled" class="checkout-offer-card">
                        <div class="checkout-offer-heading"><strong>{{ $t('Checkout Offer') }}</strong><span class="checkout-offer-badge">{{ checkoutOfferPercent }}% OFF</span></div>
                        <p>{{ checkoutOfferMessage }}</p>
                        <small>অফার উইন্ডো রিফ্রেশ হবে: {{ formattedCheckoutOfferTime }}</small>
                    </div>
                    <div class="payment-calculation mt-3 mb-4">
                          <h5>{{ $t('Order Summary') }}</h5>
                        <ul>
                            <li :data-subtotal-amount="authoritativeSubtotal" class="data_sub_total"><b>{{ $t('Subtotal') }}:</b><span>BDT {{ authoritativeSubtotal }}</span></li>
                            <li :data-shipping-cost="displayShippingCost" class="shipping_cost_li"><b>{{ $t('Shipping Cost') }}:</b><span v-if="isFreeDeliveryEligible" class="free-delivery-label">FREE</span><span v-else>BDT {{ displayShippingCost }}</span></li>
                            <li v-show="otherDiscountAmount > 0" class="coupon_discount" :data-coupon-discount="couponDiscountAmount"><b>{{ $t('Other Discount') }}:</b><span>BDT {{ otherDiscountAmount }}</span></li>
                            <li v-if="checkoutOfferEnabled"><b>{{ $t('Checkout Offer') }} ({{ checkoutOfferPercent }}%):</b><span>BDT {{ checkoutOfferAmount }}</span></li>
                            <li> <b class="totaprice" id="totalPrice" :data-total-price="checkoutDisplayTotal"> {{ $t('Total Payable') }} </b> <span>BDT&nbsp;<span class="calculatedTotal">{{ checkoutDisplayTotal }}</span></span></li>
                        </ul>
                        <span data-voucher-discount="0" class="show_voucher_discount d-none"><span class="v_amount">BDT 0</span></span>
                    </div>
                    <div class="paymentmethod mt-3">
                         <h5> {{ $t('Payment Method') }}</h5>
                        <ul class="list-group list-group-horizontal">
                            <li data-payment-method="cash_on_delivery" class="list-group-item" :class="{'selected_payment': selectedPayment === 'cash_on_delivery'}" @click="selectedPayment = 'cash_on_delivery'">
                                <p class="text-center mb-0"> <img @error="imageLoadError" src="/images/cod1.png" alt=""> <br><b> {{ $t('Cash On Delivery') }}</b></p>
                            </li>
                            <li data-payment-method="online_payment" class="list-group-item" :class="{'selected_payment': selectedPayment === 'online_payment'}" @click="selectedPayment = 'online_payment'">
                                <p class="text-center mb-0"> <img @error="imageLoadError" src="/images/ssl.png" alt=""> <br><b>{{ $t('Online Payment') }}</b></p>
                            </li>
                        </ul>
                    </div>
                    <div class="procced-checkout mt-3">
                        <ul>
                            <li><button class="btn btn-primary site_color1 proceed_to_pay" @click.prevent="proceedToPay()" :disabled="orderSubmitting"><span v-if="orderSubmitting" class="spinner-border spinner-border-sm"></span><span v-else>{{ checkoutCtaText }}</span></button></li>
                        </ul>
                    </div>
                    <div id="addCouponBlock" class="checkout-coupon-bottom">
                        <button type="button" class="checkout-coupon-toggle" @click="couponExpanded = !couponExpanded">আপনার কি কোনো কুপন আছে?</button>
                        <div v-if="couponExpanded" class="input-group mt-2">
                            <input id="couponeCode" type="text" class="form-control" :placeholder="$t('Write a coupon code here')+'..'" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <span class="input-group-text text-center d-block" @click.prevent="applyCouponCode()" id="basic-addon2">{{ $t('Apply') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>

        <section v-if="checkoutStep === 'address' && cartDataReady && !(Number(cartData.sub_total) > 0)" class="checkout-empty-cart text-center">
            <div class="container">
                <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                <h4>{{ $t('No product in cart') }}!</h4>
                <router-link :to="{name:'products'}">{{ $t('Continue shopping') }}</router-link>
            </div>
        </section>

        <section v-if="checkoutStep === 'address' && !cartDataReady" id="cart-page-shimmer">
        <div class="container">
            <div class="row">
                <div v-if="cartData.sub_total == 0 || cartData.status == 0" class="col-md-12">
                    <p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
                    <h4> {{ $t('No product in cart') }} ! </h4>
                    <p> <router-link :to="{name:'products'}"> {{ $t('Continue shopping') }} </router-link></p>
                </div>
                <div v-else class="col-md-12 simar_lodding">
                
                <div class="container">
                    <div class="row cart-page-container">
                        <div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
                            <div class="row shoping-cart-text">
                                <div class="col-12 col-sm-12 col-md-12">
    
                                    <div class="shimmer">
                                        <div class="h_3 w_10 ml_10 mt_5"></div>
                                    </div>
    
                                </div>
                            </div>
                            <div class="cart-calculation">
                                <table  class="table text-left cart_table" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="5%"> <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
                                            <th width="60%"></th>
                                            <th width="5%"> </th>
                                            <th width="5%"> </th>
                                            <th width="20%">  <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
                                            <th width="5%" style="text-align: right;">  <div class="shimmer"><div class="h_2 w_5"></div></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="group_header mb-3">
                                            <td colspan="7">
                                                <small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
                                            </td>
                                        </tr> 
                                        <tr class="cart_product_group">
                                            <td> 
                                                <div class="product-cart-img">
                                                    <div class="shimmer"><div class="h_10 w_6 mr_5"></div></div>  
                                                </div> 
                                            </td>
                                            <td> 
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div></td>
                                        </tr>
    
                                        <tr class="group_header mb-3">
                                            <td colspan="7">
                                                <small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
                                            </td>
                                        </tr> 
                                        <tr class="cart_product_group">
                                            <td> 
                                                <div class="product-cart-img">
                                                    <div class="shimmer"><div class="h_10 w_6 mr_5"></div></div>  
                                                </div> 
                                            </td>
                                            <td> 
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div></td>
                                        </tr>
                                        <tr class="group_header mb-3">
                                            <td colspan="7">
                                                <small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
                                            </td>
                                        </tr> 
                                        <tr class="cart_product_group">
                                            <td> 
                                                <div class="product-cart-img">
                                                    <div class="shimmer"><div class="h_10 w_6 mr_5"></div></div>  
                                                </div> 
                                            </td>
                                            <td> 
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
                            <div class="note">
                                <div class="shimmer"><div class="h_2 w_48per mb_5"></div></div>
                            </div>
                            <div class="payment-calculation">
                                <ul>
                                    <li> <div class="shimmer"><div class="h_10 w_100per mb_5"></div></div> </li>
                                    <li> <div class="shimmer"><div class="h_7 w_100per mb_10"></div></div> </li>
                                    <li>  <div class="shimmer"><div class="h_3 w_100per mb_5"></div></div> </li>
                                    <li>  <div class="shimmer"><div class="h_3 w_100per mb_10"></div></div> </li>
                                    <li>  <div class="shimmer"><div class="h_3 w_48per mb_10"></div></div> </li>
                                    <li> 
                                    <div class="shimmer"> 
                                        <div class="h_7 w_48per f_left"></div>
                                        <div class="h_7 w_48per f_right mb_10"></div>
                                    </div> 
                                    </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                </ul>
                            </div>
                            <div class="procced-checkout">
                                <ul>
                                    <li>  <div class="shimmer"><div class="h_3 w_100per mb_10"></div></div> </li>
                                </ul>    
                            </div>
                        </div>
                    </div>
                </div>
    
                </div>
            </div>
        </div>
        </section>
    
    
    
    </div>
    
    <div v-else>
    <section id="cart-page-shimmer">
        <div class="container">
            <div class="row">
                <div v-if="cartData.sub_total == 0 || cartData.status == 0" class="col-md-12">
                    <p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
                    <h4> {{ $t('No product in cart') }} ! </h4>
                    <p> <router-link :to="{name:'products'}"> {{ $t('Continue shopping') }} </router-link></p>
                </div>
                <div v-else class="col-md-12 simar_lodding">
                
                <div class="container">
                    <div class="row cart-page-container">
                        <div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
                            <div class="row shoping-cart-text">
                                <div class="col-12 col-sm-12 col-md-12">
    
                                    <div class="shimmer">
                                        <div class="h_3 w_10 ml_10 mt_5"></div>
                                    </div>
    
                                </div>
                            </div>
                            <div class="cart-calculation">
                                <table  class="table text-left cart_table" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="5%"> <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
                                            <th width="60%"></th>
                                            <th width="5%"> </th>
                                            <th width="5%"> </th>
                                            <th width="20%">  <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
                                            <th width="5%" style="text-align: right;">  <div class="shimmer"><div class="h_2 w_5"></div></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="group_header mb-3">
                                            <td colspan="7">
                                                <small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
                                            </td>
                                        </tr> 
                                        <tr class="cart_product_group">
                                            <td> 
                                                <div class="product-cart-img">
                                                    <div class="shimmer"><div class="h_10 w_6 mr_5"></div></div>  
                                                </div> 
                                            </td>
                                            <td> 
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div></td>
                                        </tr>
    
                                        <tr class="group_header mb-3">
                                            <td colspan="7">
                                                <small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
                                            </td>
                                        </tr> 
                                        <tr class="cart_product_group">
                                            <td> 
                                                <div class="product-cart-img">
                                                    <div class="shimmer"><div class="h_10 w_6 mr_5"></div></div>  
                                                </div> 
                                            </td>
                                            <td> 
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div></td>
                                        </tr>
                                        <tr class="group_header mb-3">
                                            <td colspan="7">
                                                <small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
                                            </td>
                                        </tr> 
                                        <tr class="cart_product_group">
                                            <td> 
                                                <div class="product-cart-img">
                                                    <div class="shimmer"><div class="h_10 w_6 mr_5"></div></div>  
                                                </div> 
                                            </td>
                                            <td> 
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                                <div class="shimmer"><div class="h_10 w_6 mr_5 f_left"></div></div>  
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
                                            <td> <div class="shimmer"><div class="h_3 w_5"></div></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
                            <div class="note">
                                <div class="shimmer"><div class="h_2 w_48per mb_5"></div></div>
                            </div>
                            <div class="payment-calculation">
                                <ul>
                                    <li> <div class="shimmer"><div class="h_10 w_100per mb_5"></div></div> </li>
                                    <li> <div class="shimmer"><div class="h_7 w_100per mb_10"></div></div> </li>
                                    <li>  <div class="shimmer"><div class="h_3 w_100per mb_5"></div></div> </li>
                                    <li>  <div class="shimmer"><div class="h_3 w_100per mb_10"></div></div> </li>
                                    <li>  <div class="shimmer"><div class="h_3 w_48per mb_10"></div></div> </li>
                                    <li> 
                                    <div class="shimmer"> 
                                        <div class="h_7 w_48per f_left"></div>
                                        <div class="h_7 w_48per f_right mb_10"></div>
                                    </div> 
                                    </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                    <li><div class="shimmer"><div class="h_2 w_100per mb_5"></div></div> </li>
                                </ul>
                            </div>
                            <div class="procced-checkout">
                                <ul>
                                    <li>  <div class="shimmer"><div class="h_3 w_100per mb_10"></div></div> </li>
                                </ul>    
                            </div>
                        </div>
                    </div>
                </div>
    
                </div>
            </div>
        </div>
    </section>
    </div>


        <div class="modal fade checkout-modal" id="checkoutAddressModal" tabindex="-1" role="dialog" aria-labelledby="checkoutAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg checkout-address-dialog" role="document">
            <div class="modal-content checkout-modal-content">
            <div class="modal-header checkout-modal-header">
                <h5 class="modal-title" id="checkoutAddressModalLabel">{{ guestAddressSaved ? $t('Update Address') : $t('Add New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body checkout-modal-body">
                <div class="d-flex flex-column text-center">
    
                    <ul class="nav nav-tabs" v-if="logged_in_user && logged_in_user.id">
                        <li class="nav-item" :class="{active: addressModalTab === 'book'}">
                            <a class="nav-link" data-toggle="tab" href="#home" @click.prevent="addressModalTab = 'book'">{{ $t('Address book') }}</a>
                        </li>
                        <li class="nav-item" :class="{active: addressModalTab === 'new'}">
                            <a class="nav-link" data-toggle="tab" href="#menu1" @click.prevent="addressModalTab = 'new'"> <i class="fa fa-plus"></i> {{ $t('Add new address') }}</a>
                        </li>
                    </ul>
    
                    <div class="tab-content">
                        <div id="home" class="tab-pane fade" :class="{in: true, active: addressModalTab === 'book'}">
                                <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col"> {{ $t('Full Name') }} </th>
                                        <th scope="col"> {{ $t('Phone') }}</th>
                                        <th scope="col"> {{ $t('Address') }}</th>
                                        <th scope="col"> {{ $t('Defalut') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                    <tr v-for="(address, index) in logged_in_user_address" :key="index" @click.prevent="change_address(address.id)">
                                        <td> {{ address.shipping_first_name }}  {{ address.shipping_last_name }}  </td>
                                        <td> {{ address.shipping_phone }} </td>
                                        <td>{{ formatShippingAddress(address) }}</td>
                                        <td> 
                                            <span v-if="logged_in_user.default_address_id == address.id">
                                              <div class="select_address"> </div>
                                            </span>
                                         </td>
                                    </tr>
                                   
                                </tbody>
                                </table>
                        </div>
                        <div id="menu1" class="tab-pane fade" :class="{in: true, active: addressModalTab === 'new'}">
                            <div class="col-md-12">
                                    <form @submit.prevent="addNewAddress()">
                                    <div class="options">
                                        <div class="row text-left">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for=""> {{ $t('Full Name') }}<span style="color:#f00">*</span></label>
                                                    <input type="text" class="form-control shipping_first_name" v-model="guestShippingFirstName" placeholder="আপনার পূর্ণ নাম লিখুন" required>
                                                    <div class="validation_error" v-if="errors.shipping_first_name" v-html="errors.shipping_first_name[0]" />
                                                </div>
                                            </div>
    
                                           <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">  {{ $t('Phone') }} <span style="color:#f00">*</span></label>
                                                    <input type="text" class="form-control popup_phone" v-model="guestShippingPhone" placeholder="01XXXXXXXXX" @input="normalizeGuestShippingPhone" required>
                                                    <div class="validation_error" v-if="errors.shipping_phone" v-html="errors.shipping_phone[0]" />
                                                </div>
                                            </div>
    
                                        </div>
    
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">  {{ $t('Address') }} <span style="color:#f00">*</span></label>
                                                    <textarea
                                                        v-model="shippingAddressInput"
                                                        @input="onShippingAddressInput"
                                                        @blur="resolveAddressOnBlur"
                                                        name=""
                                                        id=""
                                                        cols="30"
                                                        rows="3"
                                                        class="form-control shipping_address"
                                                        placeholder="পূর্ণ ঠিকানা লিখুন: থানা, জেলা, ইউনিয়ন/এলাকা, হোল্ডিং/অফিস নং, রোড"
                                                        required
                                                    ></textarea>
                                                    <div class="validation_error" v-if="errors.shipping_address" v-html="errors.shipping_address[0]" />

                                                    <small v-if="resolverLoading" class="form-text text-muted">
                                                        এলাকা শনাক্ত করা হচ্ছে...
                                                    </small>

                                                    <div v-else-if="hasSelectedAddressLocation && !hasUsefulAddressDetail" class="mt-2" style="width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #efb45a;border-radius:5px;background:#fff8e8;color:#805400;font-size:13px;line-height:1.35;overflow-wrap:anywhere;">
                                                        <span aria-hidden="true">⚠</span>
                                                        এলাকা শনাক্ত হয়েছে: {{ selectedAddressThanaTitle }}, {{ selectedAddressDistrictTitle }}। সঠিক ডেলিভারির জন্য গ্রাম/ইউনিয়ন/এলাকা ও বাড়ি/রোড/অফিসের তথ্য লিখুন।
                                                    </div>

                                                    <div v-else-if="hasSelectedAddressLocation && hasUsefulAddressDetail" class="mt-2" style="width:100%;box-sizing:border-box;padding:6px 9px;border:1px solid #79bd79;border-radius:5px;background:#edf9ed;color:#267326;font-size:13px;line-height:1.35;overflow-wrap:anywhere;font-weight:600;">
                                                        ✓ এলাকা শনাক্ত হয়েছে: {{ selectedAddressThanaTitle }}, {{ selectedAddressDistrictTitle }}
                                                    </div>

                                                    <small v-else-if="resolverMatchType === 'ambiguous'" class="form-text text-warning">
                                                        নিচ থেকে সঠিক থানা/এরিয়া নির্বাচন করুন।
                                                    </small>

                                                    <div v-else-if="resolverMatchType === 'district_only'" class="mt-2" style="width:100%;box-sizing:border-box;padding:6px 9px;border:1px solid #7fc4d9;border-radius:5px;background:#eef9fc;color:#246477;font-size:13px;line-height:1.35;overflow-wrap:anywhere;">
                                                        জেলা শনাক্ত হয়েছে। নিচ থেকে থানা/এরিয়া নির্বাচন করে পূর্ণ ঠিকানা দিন।
                                                    </div>

                                                    <small v-else-if="resolverMatchType === 'none'" class="form-text text-danger">
                                                        ঠিকানা থেকে এলাকা শনাক্ত করা যায়নি। নিচ থেকে জেলা ও থানা নির্বাচন করুন।
                                                    </small>

                                                    <small v-if="resolverError" class="form-text text-danger">
                                                        এলাকা শনাক্ত করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।
                                                    </small>

                                                    <small v-if="cartContainsGrocery" class="form-text text-warning">
                                                        গ্রোসারি ডেলিভারির জন্য একটি বিস্তারিত সংরক্ষিত ঠিকানা ও এলাকা প্রয়োজন।
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row text-left">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="checkout_shipping_district">{{ $t('District') }} <span style="color:#f00">*</span></label>
                                                    <select id="checkout_shipping_district" v-model="resolvedShippingDistrict" @change="onManualDistrictChange" class="form-control" required>
                                                        <option :value="null" disabled>--Select District--</option>
                                                        <option v-for="district in districts" :key="district.id" :value="district.id">{{ district.title }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="checkout_shipping_thana">{{ $t('Thana / Area') }} <span style="color:#f00">*</span></label>
                                                    <select id="checkout_shipping_thana" v-model="resolvedShippingThana" @change="onManualThanaChange" class="form-control" :disabled="!resolvedShippingDistrict" required>
                                                        <option :value="null" disabled>--Select Thana / Area--</option>
                                                        <option v-for="upazila in orderedAddressUpazilas" :key="upazila.id" :value="upazila.id">{{ upazila.title }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row text-left">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="checkout_shipping_union">{{ $t('Area / Union') }}</label>
                                                    <select id="checkout_shipping_union" v-model="resolvedShippingUnion" class="form-control" :disabled="!resolvedShippingThana">
                                                        <option :value="null" disabled>--Select Area / Union--</option>
                                                        <option v-for="unionItem in unions" :key="unionItem.id" :value="unionItem.id">{{ unionItem.title }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-right mb-0"> <button type="submit" class="btn btn-primary site_color1 checkout-address-submit">{{ guestAddressSaved ? $t('Update Address') : $t('Save Address') }}</button> </p>
                                    </div>
                                    </form>
    
    
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    
    
    <div class="modal fade checkout-modal" id="checkoutOtpModal" tabindex="-1" role="dialog" aria-labelledby="checkoutOtpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered checkout-otp-dialog" role="document">
            <div class="modal-content checkout-modal-content">
            <div class="modal-header checkout-modal-header">
                <h5 class="modal-title" id="checkoutOtpModalLabel">Verify OTP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body checkout-modal-body checkout-otp-body">
                <p v-if="checkoutPhone" class="checkout-otp-phone">We sent a 4-digit OTP to <strong>+88 {{ checkoutPhone }}</strong></p>
                <div class="checkout-otp-digits" @paste.prevent="onOtpPaste">
                    <input
                        v-for="(digit, index) in otpDigits"
                        :key="index"
                        :data-otp-index="index"
                        :value="digit"
                        type="text"
                        class="form-control checkout-otp-digit"
                        :maxlength="index === 0 ? 4 : 1"
                        inputmode="numeric"
                        :autocomplete="index === 0 ? 'one-time-code' : 'off'"
                        :aria-label="'OTP digit ' + (index + 1)"
                        @input="onOtpDigitInput(index, $event)"
                        @keydown="onOtpDigitKeydown(index, $event)"
                    >
                </div>
                <button type="button" class="btn btn-primary site_color1 btn-block checkout-otp-submit" @click.prevent="verifyCheckoutOtp" :disabled="otpLoading || checkoutOtpCode.length !== 4">
                    <span v-if="otpLoading" class="spinner-border spinner-border-sm"></span>
                    <span v-else>Verify OTP</span>
                </button>
                <p class="checkout-resend mb-0">
                    <span v-if="otpLoading">Sending OTP...</span>
                    <span v-else-if="otpResendSeconds > 0">Resend OTP in {{ formattedOtpResendTime }}</span>
                    <a v-else href="javascript:void(0)" @click.prevent="sendCheckoutOtp">Resend OTP</a>
                </p>
            </div>
            </div>
        </div>
    </div>

    <div v-if="useableVouchers.length" class="modal fade use_voucher_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <form @submit.prevent="checkUseableVoucher">
                <div class="modal-header border-bottom-0 abs_right ">
                    <button type="button" class="close custom_close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            
                <div class="modal-body">
                    <h3 class="text-center"> {{ $t('Use Voucher') }} </h3>
                   
                    <p class="text-center"> {{ $t('Please select the voucher') }}</p>
                    <div class="row">
                        <div  class="col-md-6" v-for="(cv,index) in useableVouchers" :key="index">
                            <p><input type="radio" name="useable_vouchers" class="useable_vouchers_radio" :data-voucher-discount="Number(cv.voucher.amount)" :value="cv.voucher_id" required> <img @error="imageLoadError" style="width:80%" :src="baseurl+'/'+cv.voucher.banner" alt=""></p>
                        </div>
                    </div>
                </div>
    
                <div class="modal-footer border-top-0 ">
                    <p class="text-right"><button type="submit" class="btn btn-dark"> {{ $t('Apply') }} </button></p>
                </div>
            </form>
    
        </div>
      </div>
    </div>
    </div>
    </template>
    
    
    <script>
    import axios from 'axios'
    
    export default {
        data(){
            return{
                loading:false,
                carts:'',
                baseurl:'',
                sub_total:'',
                agree:false,
                note:'',
                coupon_discount:{},
                cartCount:'',
                addresses: {},
                districts:{},
                upazilas:{},
                unions:{},
                shippingAddressInput:'',
                resolverLoading:false,
                resolverMatchType:'',
                resolverCandidates:[],
                selectedResolverCandidateIndex:'',
                resolvedLocation:null,
                resolvedShippingDistrict:null,
                resolvedShippingThana:null,
                resolvedShippingUnion:null,
                resolvedCityId:null,
                resolvedZoneId:null,
                resolverError:false,
                resolverDebounceTimer:null,
                resolverRequestSequence:0,
                resolverLastRequestedAddress:'',
                manualLocationOverride:false,
                thanaRequestSequence:0,
                errors:{},
                errors: [],
                finalCalculatedTotal:0,
                otherDiscountAmount:0,
                checkoutPhone:'',
                checkoutStep:'phone',
                phoneLoading:false,
                otpRequired:false,
                otpVerified:false,
                otpSent:false,
                otpLoading:false,
                otpResendSeconds:0,
                otpResendTimer:null,
                checkoutOtpCode:'',
                otpDigits:['', '', '', ''],
                otpCallback:null,
                guestShippingFirstName:'',
                guestShippingPhone:'',
                guestShippingAddress:'',
                guestShippingDistrict:null,
                guestShippingThana:null,
                guestShippingUnion:null,
                guestAddressSaved:false,
                guestAddressDisplay:'',
                guestAddressFromHistory:false,
                checkoutCustomerId:null,
                checkoutCustomerAddressId:null,
                addressModalTab:'new',
                freeDeliveryThreshold:1990,
                selectedPayment:'cash_on_delivery',
                orderSubmitting:false,
                couponExpanded:false,
                checkoutOfferClock:Date.now(),
                checkoutOfferTimer:null,
                cartMutationLoading:{},
                relatedProducts:[],
                relatedCartLoading:{},
            }
        },
    
        methods:{
			normalizePhoneDigits(event){
				const raw = event.target.value || '';
				const converted = raw.replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit));
				const digits = converted.replace(/\D/g, '').replace(/^880/, '0');
				event.target.value = digits;
				this.checkoutPhone = digits;
			},
            normalizeGuestShippingPhone(event){
                const raw = event.target.value || '';
                const converted = raw.replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit));
                const digits = converted.replace(/\D/g, '').replace(/^880/, '0');
                event.target.value = digits;
                this.guestShippingPhone = digits;
            },
            focusOtpDigit(index){
                this.$nextTick(() => {
                    const input = document.querySelector('#checkoutOtpModal [data-otp-index="' + index + '"]');
                    if(input){
                        input.focus();
                        input.select();
                    }
                });
            },
            syncCheckoutOtpCode(){
                this.checkoutOtpCode = this.otpDigits.join('');
            },
            onOtpDigitInput(index, event){
                const digits = (event.target.value || '').replace(/\D/g, '').slice(0, 4);
                if(digits.length > 1){
                    for(let digitIndex = 0; digitIndex < 4; digitIndex++){
                        this.$set(this.otpDigits, digitIndex, digits[digitIndex] || '');
                    }
                    this.syncCheckoutOtpCode();
                    this.focusOtpDigit(Math.min(digits.length, 4) - 1);
                    return;
                }
                const value = digits.slice(-1);
                this.$set(this.otpDigits, index, value);
                event.target.value = value;
                this.syncCheckoutOtpCode();
                if(value && index < this.otpDigits.length - 1){
                    this.focusOtpDigit(index + 1);
                }
            },
            onOtpDigitKeydown(index, event){
                if(event.key === 'Backspace' && this.otpDigits[index]){
                    event.preventDefault();
                    this.$set(this.otpDigits, index, '');
                    this.syncCheckoutOtpCode();
                }else if(event.key === 'Backspace' && index > 0){
                    event.preventDefault();
                    this.$set(this.otpDigits, index - 1, '');
                    this.syncCheckoutOtpCode();
                    this.focusOtpDigit(index - 1);
                }else if(event.key === 'ArrowLeft' && index > 0){
                    event.preventDefault();
                    this.focusOtpDigit(index - 1);
                }else if(event.key === 'ArrowRight' && index < this.otpDigits.length - 1){
                    event.preventDefault();
                    this.focusOtpDigit(index + 1);
                }
            },
            onOtpPaste(event){
                const digits = (event.clipboardData || window.clipboardData)
                    .getData('text')
                    .replace(/\D/g, '')
                    .slice(0, 4)
                    .split('');
                if(!digits.length){
                    return;
                }
                for(let index = 0; index < 4; index++){
                    this.$set(this.otpDigits, index, digits[index] || '');
                }
                this.syncCheckoutOtpCode();
                this.focusOtpDigit(Math.min(digits.length, 4) - 1);
            },
            loading_method(){
				this.loading = true;
            },
            productOrdinal(groupIndex, itemIndex){
                const groups = Array.isArray(this.cartData.cart) ? this.cartData.cart : [];
                const preceding = groups.slice(0, groupIndex).reduce((total, group) => {
                    return total + (Array.isArray(group.items) ? group.items.length : 0);
                }, 0);
                return String(preceding + itemIndex + 1).padStart(2, '0');
            },
            cartRequestConfig(){
                return {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        'Access-Control-Allow-Origin': '*',
                        'Authorization': 'Bearer ' + localStorage.getItem('token')
                    }
                };
            },
            refreshCheckoutCart(){
                const isGuestWithAddress = (!this.logged_in_user || !this.logged_in_user.id)
                    && this.guestAddressSaved
                    && this.guestShippingDistrict
                    && this.guestShippingThana;
                const refresh = isGuestWithAddress
                    ? axios.get(this.$baseUrl + '/api/v1/initcart', {
                        headers: this.cartRequestConfig().headers,
                        params: {
                            session_key: localStorage.getItem('session_key') || '',
                            guest_shipping_district: this.guestShippingDistrict,
                            guest_shipping_thana: this.guestShippingThana,
                            guest_shipping_union: this.guestShippingUnion || ''
                        }
                    }).then(response => {
                        if(response.data.status != 1){
                            swal('Oops', response.data.message || 'Unable to calculate delivery charge.', 'error');
                            return;
                        }
                        this.$store.commit('LOADED_CART', response.data);
                    })
                    : this.$store.dispatch('loadedCart');
                return Promise.resolve(refresh).then(() => {
                    this.$nextTick(() => this.calculateFinalAmount());
                });
            },
            updateCheckoutQty(cart, update){
                if(!cart || !cart.row_id || (update < 0 && Number(cart.qty) <= 1)){
                    return;
                }
                this.$set(this.cartMutationLoading, cart.row_id, true);
                const formData = new FormData();
                formData.append('rowId', cart.row_id);
                formData.append('update', update);
                formData.append('session_key', localStorage.getItem('session_key') || '');
                axios.post(this.$baseUrl + '/api/v1/update-qty', formData, this.cartRequestConfig()).then(response => {
                    if(response.data.status == 1){
                        return this.refreshCheckoutCart();
                    }
                    swal('Oops', response.data.message, 'error');
                }).catch(() => {
                    swal('Oops', 'Unable to update quantity. Please try again.', 'error');
                }).finally(() => {
                    this.$set(this.cartMutationLoading, cart.row_id, false);
                });
            },
            removeCheckoutItem(cart){
                if(!cart || !cart.row_id){
                    return;
                }
                this.$set(this.cartMutationLoading, cart.row_id, true);
                const formData = new FormData();
                formData.append('row_id', cart.row_id);
                formData.append('session_key', localStorage.getItem('session_key') || '');
                axios.post(this.$baseUrl + '/api/v1/remove-cart-item', formData, this.cartRequestConfig()).then(response => {
                    if(response.data.status == 1){
                        return this.refreshCheckoutCart();
                    }
                    swal('Oops', response.data.message, 'error');
                }).catch(() => {
                    swal('Oops', 'Unable to remove this product. Please try again.', 'error');
                }).finally(() => {
                    this.$set(this.cartMutationLoading, cart.row_id, false);
                });
            },
            loadRelatedProducts(){
                axios.get(this.$baseUrl + '/api/v1/get-featured-product', {
                    headers: {'X-localization': localStorage.getItem('lang')},
                    params: {checkout_candidates: 1}
                }).then(response => {
                    const items = response.data && Array.isArray(response.data.items) ? response.data.items : [];
                    this.relatedProducts = items.filter(product => {
                        return product.product_type === 'simple'
                            && Number(product.in_stock) > 0
                            && Number(product.qty) > 0;
                    });
                }).catch(() => {
                    this.relatedProducts = [];
                });
            },
            addRelatedProduct(product){
                if(!product || !product.id || this.relatedCartLoading[product.id]){
                    return;
                }
                this.$set(this.relatedCartLoading, product.id, true);
                axios.post(this.$baseUrl + '/api/v1/add-to-cart', {
                    product_id: product.id,
                    qty: 1,
                    session_key: localStorage.getItem('session_key')
                }, this.cartRequestConfig()).then(response => {
                    if(response.data.status == 1){
                        return this.refreshCheckoutCart();
                    }
                    swal('Oops', response.data.message, 'error');
                }).catch(() => {
                    swal('Oops', 'Unable to add this product. Please try again.', 'error');
                }).finally(() => {
                    this.$set(this.relatedCartLoading, product.id, false);
                });
            },
            readCookieValue(name){
                if(typeof document === 'undefined' || !document.cookie){
                    return '';
                }
                const prefix = encodeURIComponent(name) + '=';
                const cookies = document.cookie.split(';');
                for(let index = 0; index < cookies.length; index++){
                    const cookie = cookies[index].trim();
                    if(cookie.indexOf(prefix) === 0){
                        const value = cookie.substring(prefix.length);
                        try {
                            return decodeURIComponent(value);
                        } catch (error) {
                            return value;
                        }
                    }
                }
                return '';
            },
            proceedToPay(){
                if(this.orderSubmitting){
                    return;
                }
                let collectedVoucher = '';
                let usedVoucher = '';
                let token = localStorage.getItem("token");
                let axiosConfig = {
                  headers: {
                      'Content-Type': 'application/json;charset=UTF-8',
                      "Access-Control-Allow-Origin": "*",
                      'Authorization': 'Bearer '+token
                  }
                }
    
                const isLoggedInCustomer = this.logged_in_user && this.logged_in_user.id;
                if(isLoggedInCustomer && (this.logged_in_user_address == 0 || this.logged_in_user.default_address_id == null)){
                        swal ( "Oops" , 'Please select your default shipping address!',  "error");
                    return true;
                }
                if(!isLoggedInCustomer && !this.guestAddressSaved){
                    this.openCheckoutAddressModal();
                    return true;
                }
    
                if(this.useableVouchers.length){
                    let isCheckedUsedVoucher = false;
                    jQuery('.useable_vouchers_radio').each(function(key,val){
                        if (jQuery(this).is(':checked')) {
                            isCheckedUsedVoucher = true;
                            usedVoucher = jQuery(this).val();
                        }
                    });
                    if(!isCheckedUsedVoucher){
                        swal ( "Oops" , 'Please select which voucher you want to use!',  "error");
                        jQuery('.voucher_button li').addClass('validated_class');
                        return true;
                    }
                }
    
                    let shipping_method  = [];

                    jQuery('.select_shipping_options .selected_shipping').each(function(key,val){
                        shipping_method[key] = { 
                            product_id : jQuery(this).attr('data-product-id'),
                            shipping_method: jQuery(this).attr('data-shipping-method'),
                            shipping_cost: jQuery(this).attr('data-shipping-cost'),
                        }
                    });

                    let formData = {
                        note: jQuery('.form_note').val(),
                        coupon: jQuery('#couponeCode').val(),
                        collectedVoucher: collectedVoucher,
                        usedVoucher: usedVoucher,
                        payment_method : this.selectedPayment,
                        shipping_method : shipping_method, 
                        shipping_cost_120: Number(jQuery('.payment-calculation .shipping_cost_li1').attr('data-shipping-cost')),
                        guest_shipping_first_name: this.guestShippingFirstName || '',
                        guest_shipping_phone: this.guestShippingPhone || '',
                        guest_shipping_address: this.guestShippingAddress || '',
                        guest_shipping_district: this.guestShippingDistrict || this.resolvedShippingDistrict || '',
                        guest_shipping_thana: this.guestShippingThana || this.resolvedShippingThana || '',
                        guest_shipping_union: this.guestShippingUnion || this.resolvedShippingUnion || '',
                        session_key: localStorage.getItem("session_key") || '',
                        guest_address_id: this.checkoutCustomerAddressId || '',
                        fbp: this.readCookieValue('_fbp'),
                        fbc: this.readCookieValue('_fbc')
                    }
         
                        
                        this.orderSubmitting = true;
                        axios.post(this.$baseUrl+'/api/v1/order', formData, axiosConfig).then(response => {
                            if(response.data.status == 1){
                                swal({
                                    title: 'Your order has been placed successfully.',
                                    icon: "success",
                                    timer: 3000
                                }).then(()=>{
                                    this.$store.dispatch('loadedCart');
                                    const orderRoute = {name:'orderDetails', params: {id: response.data.invoice.order_id}};
                                    if (response.data.invoice.guest_order_reference) {
                                        orderRoute.query = {guest_order_reference: response.data.invoice.guest_order_reference};
                                    }
                                    this.$router.push(orderRoute);
                                });
                            }else if( response.data.status == 302){
                                window.location.href = response.data.url;
                            }else{
                                swal ( "Oops" , response.data.message || 'Order Failed! Please try again later',  "error");
                            }
                        }).catch(error => {
                            const responseData = error && error.response ? error.response.data : null;
                            const responseMessage = responseData && (responseData.message || responseData.error);
                            swal("Oops", responseMessage || 'Unable to place the order. Please try again.', "error");
                        }).finally(() => {
                            this.orderSubmitting = false;
                        });
            },

            async submitInitialPhone(){
                let phone = (this.checkoutPhone || '').replace(/\D/g, '');
                if(!/^01[3-9]\d{8}$/.test(phone)){
                    swal("Oops", 'Please enter a valid Bangladesh mobile number.', "error");
                    return;
                }
                this.checkoutPhone = phone;
                this.phoneLoading = true;
                try {
                    const response = await axios.post(this.$baseUrl+'/api/v1/checkout/phone-check', { phone });
                    this.otpRequired = response.data.otp_required === true || response.data.otp_required == 1;
                    this.otpVerified = !this.otpRequired;
                    if(response.data.otp_bypass_reason === 'blocked_customer'){
                        swal("Oops", 'This account is temporarily blocked. Please contact support.', "error");
                        return;
                    }
                    if(!this.otpRequired){
                        this.applyPreviousShippingAddress(response.data.last_shipping_address);
                        this.advanceToAddress();
                    } else {
                        this.otpCallback = () => {
                            this.advanceToAddress();
                        };
                        await this.sendCheckoutOtp();
                    }
                } catch (e) {
                    swal("Oops", 'Phone check failed. Please try again.', "error");
                } finally {
                    this.phoneLoading = false;
                }
            },
            openCheckoutAddressModal(){
                if(!this.logged_in_user || !this.logged_in_user.id){
                    this.addressModalTab = 'new';
                    this.guestShippingPhone = this.guestShippingPhone || this.checkoutPhone;
                }
                this.$nextTick(() => this.showCheckoutModal('#checkoutAddressModal'));
            },
            showCheckoutModal(selector, onShown){
                const modal = jQuery(selector);
                if(!modal.length){
                    return;
                }
                const isMobile = window.matchMedia && window.matchMedia('(max-width: 767px)').matches;
                if(isMobile && document.activeElement && typeof document.activeElement.blur === 'function'){
                    document.activeElement.blur();
                }
                if(isMobile && !modal.parent().is('body')){
                    modal.appendTo(document.body);
                }
                modal.off('.checkoutVisibility');
                modal.one('shown.bs.modal.checkoutVisibility', () => {
                    modal.css('z-index', 20000);
                    jQuery('.modal-backdrop').last().css('z-index', 19990);
                    if(typeof onShown === 'function'){
                        onShown();
                    }
                });
                modal.one('hidden.bs.modal.checkoutVisibility', () => {
                    if(!jQuery('.modal.show').length){
                        jQuery('.modal-backdrop').remove();
                        jQuery('body').removeClass('modal-open').css('padding-right', '');
                    }
                });
                window.setTimeout(() => modal.modal('show'), isMobile ? 80 : 0);
            },
            applyPreviousShippingAddress(address){
                if(
                    !address
                    || !address.shipping_first_name
                    || !address.shipping_address
                    || !address.shipping_district
                    || !address.shipping_thana
                ){
                    return;
                }

                this.guestShippingFirstName = address.shipping_first_name;
                this.guestShippingPhone = this.checkoutPhone;
                this.guestShippingAddress = address.shipping_address;
                this.guestShippingDistrict = address.shipping_district;
                this.guestShippingThana = address.shipping_thana;
                this.guestShippingUnion = address.shipping_union || null;
                this.resolvedShippingDistrict = address.shipping_district;
                this.resolvedShippingThana = address.shipping_thana;
                this.resolvedShippingUnion = address.shipping_union || null;
                this.shippingAddressInput = address.shipping_address;
                this.guestAddressSaved = true;
                this.guestAddressFromHistory = true;
                this.guestAddressDisplay = [
                    address.shipping_first_name,
                    this.checkoutPhone,
                    address.district_title,
                    address.upazila_title,
                    address.union_title,
                    address.shipping_address,
                ].filter(Boolean).join(' | ');

                this.loadAddressUpazilas(address.shipping_district);
                this.refreshCheckoutCart();
                this.loadAddressUnions(address.shipping_thana).then(unions => {
                    if(String(this.resolvedShippingThana) === String(address.shipping_thana)){
                        this.unions = unions;
                    }
                });
            },
            advanceToAddress(){
                if(this._advanceToAddressQueued){
                    return;
                }
                this._advanceToAddressQueued = true;

                const finishAdvance = () => {
                    this._advanceToAddressQueued = false;
                    this.checkoutStep = 'address';
                    this.addressModalTab = 'new';
                    this.$nextTick(() => {
                        this.guestShippingPhone = this.checkoutPhone;
                        if(!this.logged_in_user || !this.logged_in_user.id){
                            if(this.otpVerified && !this.guestAddressSaved){
                                const previousGuest = this.loadPreviousGuestAddress(this.checkoutPhone);
                                if(previousGuest){
                                    this.guestShippingFirstName = previousGuest.guestShippingFirstName || '';
                                    this.guestShippingPhone = previousGuest.guestShippingPhone || this.checkoutPhone;
                                    this.guestShippingAddress = previousGuest.guestShippingAddress || '';
                                    this.guestShippingDistrict = previousGuest.guestShippingDistrict || null;
                                    this.guestShippingThana = previousGuest.guestShippingThana || null;
                                    this.guestShippingUnion = previousGuest.guestShippingUnion || null;
                                    this.resolvedShippingDistrict = this.guestShippingDistrict;
                                    this.resolvedShippingThana = this.guestShippingThana;
                                    this.resolvedShippingUnion = this.guestShippingUnion;
                                    if(this.resolvedShippingDistrict){
                                        this.loadAddressUpazilas(this.resolvedShippingDistrict);
                                    }
                                    this.guestAddressSaved = true;
                                    this.guestAddressDisplay = [this.guestShippingFirstName, this.guestShippingPhone, this.selectedAddressDistrictTitle, this.selectedAddressThanaTitle, this.guestShippingAddress].filter(Boolean).join(' | ');
                                    this.refreshCheckoutCart();
                                }
                            }
                            if(!this.guestAddressSaved){
                                this.showCheckoutModal('#checkoutAddressModal');
                            }
                        }
                    });
                };

                if(jQuery('#checkoutOtpModal').hasClass('show')){
                    jQuery('#checkoutOtpModal').one('hidden.bs.modal', finishAdvance);
                } else {
                    finishAdvance();
                }
            },
            async sendCheckoutOtp(){
                this.otpLoading = true;
                try {
                    const response = await axios.post(this.$baseUrl+'/api/v1/checkout/send-otp', { phone: this.checkoutPhone });
                    if(response.data.status == 1){
                        this.otpSent = true;
                        this.otpDigits = ['', '', '', ''];
                        this.checkoutOtpCode = '';
                        this.startOtpResendCountdown();
                        this.$nextTick(() => {
                            this.showCheckoutModal('#checkoutOtpModal', () => this.focusOtpDigit(0));
                        });
                    } else {
                        swal("Oops", response.data.message || 'Failed to send OTP.', "error");
                    }
                } catch (e) {
                    swal("Oops", 'Failed to send OTP. Please try again.', "error");
                } finally {
                    this.otpLoading = false;
                }
            },
            startOtpResendCountdown(){
                if(this.otpResendTimer){
                    clearInterval(this.otpResendTimer);
                }
                this.otpResendSeconds = 120;
                this.otpResendTimer = setInterval(() => {
                    if(this.otpResendSeconds <= 1){
                        clearInterval(this.otpResendTimer);
                        this.otpResendTimer = null;
                        this.otpResendSeconds = 0;
                    }else{
                        this.otpResendSeconds--;
                    }
                }, 1000);
            },
            async verifyCheckoutOtp(){
                if(!this.checkoutOtpCode || this.checkoutOtpCode.length !== 4){
                    swal("Oops", 'Please enter the 4-digit OTP.', "error");
                    return;
                }
                this.otpLoading = true;
                try {
                    const response = await axios.post(this.$baseUrl+'/api/v1/checkout/verify-otp', {
                        phone: this.checkoutPhone,
                        otp: this.checkoutOtpCode
                    });
                    if(response.data.status == 1){
                        this.otpVerified = true;
                        this.applyPreviousShippingAddress(response.data.last_shipping_address);
                        jQuery('#checkoutOtpModal').modal('hide');
                        if(this.otpCallback){
                            this.otpCallback();
                        } else {
                            this.advanceToAddress();
                        }
                    } else {
                        swal("Oops", response.data.message || 'Invalid OTP.', "error");
                    }
                } catch (e) {
                    swal("Oops", 'OTP verification failed. Please try again.', "error");
                } finally {
                    this.otpLoading = false;
                }
            },
            async addNewAddress(){
                if(this.cartContainsGrocery){
                    swal("Please check", "Please use a detailed saved address with an area for grocery delivery.", "error");
                    return;
                }

                if(!this.resolvedShippingDistrict){
                    swal("Please check", "ডেলিভারি চার্জ নির্ধারণের জন্য জেলা নির্বাচন করুন।", "error");
                    return;
                }

                if(!this.resolvedShippingThana){
                    swal("Please check", "ডেলিভারি চার্জ নির্ধারণের জন্য থানা/এরিয়া নির্বাচন করুন।", "error");
                    return;
                }

                if(!this.logged_in_user || !this.logged_in_user.id){
                    this.guestShippingFirstName = this.guestShippingFirstName || jQuery('.shipping_first_name').val();
                    this.guestShippingPhone = this.guestShippingPhone || jQuery('.popup_phone').val();
                    this.guestShippingAddress = this.shippingAddressInput;
                    this.guestShippingDistrict = this.resolvedShippingDistrict;
                    this.guestShippingThana = this.resolvedShippingThana;
                    this.guestShippingUnion = this.resolvedShippingUnion;
                    try {
                        const registration = await axios.post(this.$baseUrl+'/api/v1/checkout/register-verified-customer', {
                            phone: this.checkoutPhone,
                            name: this.guestShippingFirstName,
                            shipping_address: this.guestShippingAddress,
                            shipping_district: this.guestShippingDistrict,
                            shipping_thana: this.guestShippingThana,
                            shipping_union: this.guestShippingUnion || null
                        });
                        if(registration.data.status != 1){
                            swal('Oops', registration.data.message || 'Unable to save customer information.', 'error');
                            return;
                        }
                        this.checkoutCustomerId = registration.data.customer_id;
                        this.checkoutCustomerAddressId = registration.data.address_id;
                    } catch (error) {
                        const response = error && error.response && error.response.data;
                        const validationErrors = response && response.errors
                            ? Object.values(response.errors).reduce((all, messages) => all.concat(messages), [])
                            : [];
                        const safeMessage = (response && response.message) || validationErrors[0] || 'Unable to save customer information.';
                        swal('Oops', safeMessage, 'error');
                        return;
                    }
                    this.guestAddressSaved = true;
                    this.guestAddressFromHistory = false;
                    this.guestAddressDisplay = [this.guestShippingFirstName, this.guestShippingPhone, this.selectedAddressDistrictTitle, this.selectedAddressThanaTitle, this.guestShippingAddress].filter(Boolean).join(' | ');
                    this.saveGuestAddress(this.checkoutPhone, {
                        guestShippingFirstName: this.guestShippingFirstName,
                        guestShippingPhone: this.guestShippingPhone,
                        guestShippingAddress: this.guestShippingAddress,
                        guestShippingDistrict: this.guestShippingDistrict,
                        guestShippingThana: this.guestShippingThana,
                        guestShippingUnion: this.guestShippingUnion,
                    });
                    jQuery('#checkoutAddressModal').modal('hide');
                    this.refreshCheckoutCart();
                    swal({
                        title: 'Address saved for checkout.',
                        icon: "success",
                        timer: 3000
                    });
                    return;
                }

                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                let formData = new FormData();
                formData.append('shipping_first_name', jQuery('.shipping_first_name').val());
                formData.append('shipping_phone', jQuery('.popup_phone').val());
                formData.append('shipping_address', this.shippingAddressInput);
                formData.append('shipping_district', this.resolvedShippingDistrict);
                formData.append('shipping_thana', this.resolvedShippingThana);
                if(this.resolvedShippingUnion){
                    formData.append('shipping_union', this.resolvedShippingUnion);
                }
    
                axios.post(this.$baseUrl+'/api/v1/add-new-address', formData, axiosConfig).then(response => {
                    if(response.data.status == 1){
                        swal({
                            title: 'New address added successfull.',
                            icon: "success",
                            timer: 3000
                        });
                        this.shippingAddressInput = '';
                        this.clearResolvedLocation();
                        this.manualLocationOverride = false;
                        this.$store.dispatch('loadedUser');
                        jQuery('a[href="#home"]').trigger('click');
                    }else{
                        this.errors = response.data.message;
                    }
                });
            },
            change_address($address_id){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                let formData = new FormData();
                formData.append('address_id', $address_id);
                axios.post(this.$baseUrl+'/api/v1/update-default-address', formData, axiosConfig).then(response => {
                    if(response.data.status == 1){
                        this.$store.dispatch('loadedCart');
                        this.$store.dispatch('loadedUser');
                        jQuery('.close').trigger('click');   
                   }else{
                       swal ( "Please check" ,  response.data.message,  "error");
                   }
                });
            },
            loadPreviousGuestAddress(phone){
                try {
                    const raw = localStorage.getItem('guest_checkout_addresses');
                    const all = raw ? JSON.parse(raw) : {};
                    const key = (phone || '').replace(/\D/g, '');
                    const previous = all[key];
                    if(previous && previous.guestShippingAddress){
                        return previous;
                    }
                } catch (e) {
                    console.error('Failed to load previous guest address', e);
                }
                return null;
            },
            saveGuestAddress(phone, data){
                try {
                    const raw = localStorage.getItem('guest_checkout_addresses');
                    const all = raw ? JSON.parse(raw) : {};
                    const key = (phone || '').replace(/\D/g, '');
                    all[key] = data;
                    localStorage.setItem('guest_checkout_addresses', JSON.stringify(all));
                } catch (e) {
                    console.error('Failed to save guest address', e);
                }
            },

            checkUseableVoucher(){
                let usedVoucher = 0;
                jQuery('.close').trigger('click');
                 jQuery('.useable_vouchers_radio').each(function(key,val){
                    if (jQuery(this).is(':checked')) {
                        usedVoucher = jQuery(this).attr('data-voucher-discount');
                    }
                });
                jQuery('.show_voucher_discount').attr('data-voucher-discount',Number(usedVoucher));
                jQuery('.show_voucher_discount .v_amount').html('BDT '+usedVoucher);
                this.calculateFinalAmount();
            },
    
            calculateFinalAmount(){
                    let subTotal = Number(jQuery('.data_sub_total').attr('data-subtotal-amount'));
                    let shippingCost = Number(jQuery('.shipping_cost_li').attr('data-shipping-cost'));
                    let couponAmount = jQuery('.coupon_discount').attr('data-coupon-discount') ? Number(jQuery('.coupon_discount').attr('data-coupon-discount')) : 0;
                    let voucherAmount = jQuery('.show_voucher_discount').attr('data-voucher-discount') ? Number(jQuery('.show_voucher_discount').attr('data-voucher-discount')) : 0;
                    this.otherDiscountAmount = couponAmount + voucherAmount;
                    //console.log('subTotal :'+subTotal+',couponAmount:'+couponAmount+',voucherAmount:'+voucherAmount+',shippingCost: '+shippingCost);
                    this.finalCalculatedTotal = Math.max(0, (subTotal+shippingCost) - (couponAmount+voucherAmount+this.checkoutOfferAmount));
                    $('.calculatedTotal').text(this.finalCalculatedTotal);
    
            },
            imageLoadError(event){
                event.target.src = "/images/notfound.png";
            },
            formatShippingAddress(address){
                let location = [];
                if(address.upazila && address.upazila.title){
                    location.push(address.upazila.title);
                }
                if(address.district && address.district.title){
                    location.push(address.district.title);
                }
                if(address.union && address.union.title){
                    location.push(address.union.title);
                }
                if(address.shipping_address && location.length){
                    return location.join(', ') + ' — ' + address.shipping_address;
                }
                return address.shipping_address || location.join(', ');
            },
            clearResolvedLocation(clearSelection = true){
                this.resolverMatchType = '';
                this.resolverCandidates = [];
                this.selectedResolverCandidateIndex = '';
                this.resolvedLocation = null;
                this.resolvedShippingUnion = null;
                this.unions = [];
                this.resolvedCityId = null;
                this.resolvedZoneId = null;
                this.resolverError = false;
                if(clearSelection){
                    this.resolvedShippingDistrict = null;
                    this.resolvedShippingThana = null;
                    this.upazilas = [];
                }
            },
            onShippingAddressInput(){
                if(this.resolverDebounceTimer){
                    clearTimeout(this.resolverDebounceTimer);
                }

                this.resolverRequestSequence++;
                this.resolverLoading = false;
                this.resolverLastRequestedAddress = '';
                this.clearResolvedLocation(!this.manualLocationOverride);

                if(this.shippingAddressInput.trim().length > 0){
                    this.resolverDebounceTimer = setTimeout(() => {
                        this.resolveAddressLocation();
                    }, 600);
                }
            },
            resolveAddressOnBlur(){
                if(this.resolverDebounceTimer){
                    clearTimeout(this.resolverDebounceTimer);
                    this.resolverDebounceTimer = null;
                }

                if(this.shippingAddressInput.trim().length > 0 && !this.resolvedLocation){
                    this.resolveAddressLocation();
                }
            },
            resolveAddressLocation(){
                const address = this.shippingAddressInput.trim();

                if(address.length === 0){
                    this.clearResolvedLocation(!this.manualLocationOverride);
                    this.resolverMatchType = address.length ? 'none' : '';
                    return;
                }

                if(this.resolverLoading && this.resolverLastRequestedAddress === address){
                    return;
                }

                const requestId = ++this.resolverRequestSequence;
                const canAutoSelect = !this.manualLocationOverride;
                this.resolverLoading = true;
                this.resolverError = false;
                this.resolverLastRequestedAddress = address;

                axios.post(this.$baseUrl+'/api/v1/resolve-address-location', {
                    address: address
                }).then(response => {
                    if(requestId !== this.resolverRequestSequence || address !== this.shippingAddressInput.trim()){
                        return;
                    }

                    const data = response.data || {};
                    this.clearResolvedLocation(false);
                    this.resolverCandidates = Array.isArray(data.candidates) ? data.candidates : [];
                    this.resolverMatchType = data.match_type || 'none';

                    if(canAutoSelect && !this.manualLocationOverride && this.resolverMatchType === 'strong' && this.resolverCandidates.length){
                        this.applyResolvedCandidate(this.resolverCandidates[0], requestId);
                    }else if(canAutoSelect && !this.manualLocationOverride && this.resolverMatchType === 'district_only' && data.district){
                        this.applyDistrictOnly(data.district, requestId);
                    }else if(canAutoSelect && !this.manualLocationOverride && this.resolverMatchType === 'ambiguous'){
                        this.applySafeAmbiguousDistrict(requestId);
                    }
                }).catch(() => {
                    if(requestId !== this.resolverRequestSequence){
                        return;
                    }

                    this.clearResolvedLocation(!this.manualLocationOverride);
                    this.resolverError = true;
                }).then(() => {
                    if(requestId === this.resolverRequestSequence){
                        this.resolverLoading = false;
                    }
                });
            },
            async loadAddressDistricts(){
                const response = await axios.get(this.$baseUrl + '/api/v1/get-district/0');
                this.districts = response.data;
            },
            async loadAddressUpazilas(districtId){
                const requestId = ++this.thanaRequestSequence;
                const response = await axios.get(this.$baseUrl + '/api/v1/get-upazila/' + districtId);
                if(requestId === this.thanaRequestSequence && String(this.resolvedShippingDistrict) === String(districtId)){
                    this.upazilas = response.data;
                }
            },
            async loadAddressUnions(upazilaId){
                const response = await axios.get(this.$baseUrl + '/api/v1/get-union/' + upazilaId);
                return Array.isArray(response.data) ? response.data : [];
            },
            async applyResolvedCandidate(candidate, resolverRequestId){
                this.resolvedLocation = candidate;
                this.resolvedShippingDistrict = candidate.district_id;
                this.resolvedShippingThana = null;
                this.resolvedShippingUnion = null;
                this.resolvedCityId = candidate.city_id;
                this.resolvedZoneId = candidate.zone_id;
                await this.loadAddressUpazilas(candidate.district_id);
                if(resolverRequestId === this.resolverRequestSequence && !this.manualLocationOverride){
                    this.resolvedShippingThana = candidate.upazila_id;
                    const unions = await this.loadAddressUnions(candidate.upazila_id);
                    if(
                        resolverRequestId === this.resolverRequestSequence
                        && !this.manualLocationOverride
                        && String(this.resolvedShippingThana) === String(candidate.upazila_id)
                    ){
                        this.unions = unions;
                        if(candidate.matched_area_id){
                            const matchedUnion = unions.find(union => String(union.id) === String(candidate.matched_area_id));
                            this.resolvedShippingUnion = matchedUnion ? matchedUnion.id : null;
                        }
                    }
                }
            },
            async applySafeAmbiguousDistrict(resolverRequestId){
                const districtIds = Array.from(new Set(this.resolverCandidates.map(candidate => String(candidate.district_id))));
                if(districtIds.length !== 1){
                    return;
                }
                const candidate = this.resolverCandidates[0];
                this.resolvedShippingDistrict = candidate.district_id;
                this.resolvedShippingThana = null;
                await this.loadAddressUpazilas(candidate.district_id);
                if(resolverRequestId !== this.resolverRequestSequence || this.manualLocationOverride){
                    return;
                }
            },
            async applyDistrictOnly(district, resolverRequestId){
                this.resolvedShippingDistrict = district.district_id;
                this.resolvedShippingThana = null;
                this.resolvedCityId = district.city_id;
                this.resolvedZoneId = null;
                await this.loadAddressUpazilas(district.district_id);
                if(resolverRequestId !== this.resolverRequestSequence || this.manualLocationOverride){
                    return;
                }
            },
            onManualDistrictChange(){
                this.manualLocationOverride = true;
                this.resolverRequestSequence++;
                this.thanaRequestSequence++;
                this.clearResolvedLocation(false);
                this.resolvedShippingThana = null;
                this.upazilas = [];
                this.loadAddressUpazilas(this.resolvedShippingDistrict);
            },
            async onManualThanaChange(){
                this.manualLocationOverride = true;
                this.resolverRequestSequence++;
                this.resolvedLocation = null;
                this.resolverMatchType = '';
                this.resolvedCityId = null;
                this.resolvedZoneId = null;
                this.resolvedShippingUnion = null;
                this.unions = this.resolvedShippingThana
                    ? await this.loadAddressUnions(this.resolvedShippingThana)
                    : [];
            },

            async getDistrict(){
            let id =  jQuery('#division').find('option:selected').val();
            await axios.get(this.$baseUrl + "/api/v1/get-district/"+id).then((response) => {
                    this.upazilas = {};
                    this.unions = {};
                    this.districts = response.data;
                });
            },
            
            async getUpazila(){
                let id =  jQuery('#district').find('option:selected').val();
                await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
                        this.unions = {};
                        this.upazilas = response.data;
                    });
                },
                async getUnion(){
                    let id =  jQuery('#upazila').find('option:selected').val();
                await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
                        this.unions = response.data;
                    });
            },
    
    
            applyCouponCode(){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
    
                let formData = new FormData();
                formData.append('coupon', $('#couponeCode').val());
                axios.post(this.$baseUrl+'/api/v1/get-coupon-amount', formData,axiosConfig).then(response => {
                    if(response.data.status == 1){
                        this.coupon_discount = response.data;
                        jQuery('.coupon_discount').attr('data-coupon-discount',response.data.amount);
    
                        this.couponExpanded = false;
                        jQuery('.coupon_discount').show();
                        
                          this.calculateFinalAmount();
                        swal({
                            title: response.data.message,
                            icon: "success",
                            timer: 3000
                        });
                    }else{
                        swal ( "Oops", response.data.message, "error");
                    }
                    
                });
            },
            removeCoupon(){
                jQuery('#addCouponBlock').show();
                jQuery('.coupon_discount').attr('data-coupon-discount',0)
                this.coupon_discount = {};
                let that = this;
                setTimeout(function(){ 
                    that.calculateFinalAmount();
                 },200);
            },
    
            updateShippingOption(shipping_method, shipping_cost, rowId){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                let formData = new FormData();
                formData.append('shipping_method', shipping_method);
                formData.append('shipping_cost', shipping_cost);
                formData.append('rowId', rowId);
                axios.post(this.$baseUrl+'/api/v1/update-shipping-option', formData,axiosConfig).then(response =>{
                    if(response.data.status == '1'){
                        this.$store.dispatch('loadedCart');
                        swal({
                            title: "Shipping method updated Successfully.",
                            icon: "success",
                            timer: 3000
                        });
                    }else{
                        swal ( "Oops", response.data.message, "error");
                    }
    
                });
            },
            scrollToTop() {
                window.scrollTo(0,0);
            },
    
            // getCollectedVoucher(){
            // 	let token = localStorage.getItem("token");
            // 		let axiosConfig = {
            // 			headers: {
            // 				'Content-Type': 'application/json;charset=UTF-8',
            // 				"Access-Control-Allow-Origin": "*",
            // 				'Authorization': 'Bearer '+token
            // 			}
            // 		}
    
            // 		axios.get(this.$baseUrl + "/api/v1/get-collected-vouchers",axiosConfig).then((response) => {
            //             this.collectedVoucher = response.data
            // 		});
            // },
            // getUseableVouchers(){
            //     let token = localStorage.getItem("token"); 
            //     let axiosConfig = {
            //         headers: {
            //             'Content-Type': 'application/json;charset=UTF-8',
            //             "Access-Control-Allow-Origin": "*",
            //             'Authorization': 'Bearer '+token
            //         }
            //     }
            //     axios.get(this.$baseUrl + "/api/v1/get-useable-vouchers",axiosConfig).then((response) => {
            //         this.useableVouchers = response.data
            //     });
            // },
    
        },
        computed:{
            isValidBangladeshPhone(){
                const phone = (this.checkoutPhone || '').replace(/\D+/g, '');
                return /^01[3-9]\d{8}$/.test(phone);
            },
            checkoutItemCount(){
                const groups = this.cartData && Array.isArray(this.cartData.cart) ? this.cartData.cart : [];
                return groups.reduce((total, group) => {
                    const items = Array.isArray(group.items) ? group.items : [];
                    return total + items.reduce((quantity, item) => quantity + Number(item.qty || 0), 0);
                }, 0);
            },
            cartDataReady(){
                const cart = this.$store.getters.getLoadedCart;
                return Boolean(cart && (cart.status !== undefined || cart.sub_total !== undefined));
            },
            visibleRelatedProducts(){
                const groups = this.cartData && Array.isArray(this.cartData.cart) ? this.cartData.cart : [];
                const cartProductIds = [];
                groups.forEach(group => {
                    (Array.isArray(group.items) ? group.items : []).forEach(item => {
                        cartProductIds.push(String(item.product_id));
                    });
                });
                return this.relatedProducts
                    .filter(product => cartProductIds.indexOf(String(product.id)) === -1)
                    .slice(0, 4);
            },
            authoritativeSubtotal(){
                return Number((this.cartData && this.cartData.sub_total) || this.sub_total || 0);
            },
            couponDiscountAmount(){
                return this.coupon_discount && this.coupon_discount.status == 1
                    ? Number(this.coupon_discount.amount || 0)
                    : 0;
            },
            isFreeDeliveryEligible(){
                return this.authoritativeSubtotal >= this.freeDeliveryThreshold;
            },
            freeDeliveryRemaining(){
                return Math.max(0, this.freeDeliveryThreshold - this.authoritativeSubtotal).toLocaleString('en-US');
            },
            formattedFreeDeliveryThreshold(){
                return Number(this.freeDeliveryThreshold).toLocaleString('en-US');
            },
            freeDeliveryProgress(){
                if(!this.freeDeliveryThreshold){ return 100; }
                return Math.min(100, Math.round((this.authoritativeSubtotal / this.freeDeliveryThreshold) * 100));
            },
            displayShippingCost(){
                return this.isFreeDeliveryEligible ? 0 : Number((this.cartData && this.cartData.shipping_cost) || 0);
            },
            checkoutDisplayTotal(){
                return Math.max(0, this.authoritativeSubtotal + this.displayShippingCost - this.otherDiscountAmount - this.checkoutOfferAmount);
            },
            checkoutCtaText(){
                return 'PLACE ORDER • ৳' + this.checkoutDisplayTotal;
            },
            checkoutOfferEnabled(){
                if(!this.cartData){ return false; }
                const enabled = String(this.cartData.checkout_offer_enabled).toLowerCase();
                return ['1', 'true', 'on', 'yes'].indexOf(enabled) !== -1;
            },
            checkoutOfferPercent(){
                return Number((this.cartData && this.cartData.checkout_offer_discount_percent) || 0);
            },
            checkoutOfferAmount(){
                return this.checkoutOfferEnabled
                    ? Number((this.cartData && this.cartData.checkout_offer_discount_amount) || 0)
                    : 0;
            },
            checkoutOfferMessage(){
                return (this.cartData && this.cartData.checkout_offer_message)
                    || 'অফার চলছে — আপনার প্রয়োজন হলে এখনই কনফার্ম করুন';
            },
            checkoutOfferWindowSeconds(){
                const minutes = Math.max(1, Number((this.cartData && this.cartData.checkout_offer_countdown_minutes) || 60));
                return Math.round(minutes * 60);
            },
            checkoutOfferRemainingSeconds(){
                const windowSeconds = this.checkoutOfferWindowSeconds;
                const elapsed = Math.floor(this.checkoutOfferClock / 1000) % windowSeconds;
                return elapsed === 0 ? windowSeconds : windowSeconds - elapsed;
            },
            formattedCheckoutOfferTime(){
                const minutes = Math.floor(this.checkoutOfferRemainingSeconds / 60);
                const seconds = this.checkoutOfferRemainingSeconds % 60;
                return ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
            },
            activeShippingDistrict(){
                if(this.resolvedShippingDistrict){ return this.resolvedShippingDistrict; }
                if(this.logged_in_user_address != 0 && this.logged_in_user.default_address_id != null){
                    const selected = this.logged_in_user_address.find(address => address.id == this.logged_in_user.default_address_id);
                    return selected ? selected.shipping_district : null;
                }
                return null;
            },
            isInsideDhaka(){
                return String(this.activeShippingDistrict) === '14'
                    || String(this.selectedAddressDistrictTitle || '').toLowerCase() === 'dhaka';
            },
            deliveryAreaLabel(){
                return this.isInsideDhaka ? 'Inside Dhaka' : 'Outside Dhaka';
            },
            deliveryEstimate(){
                return this.isInsideDhaka ? 'Within 1-2 Days' : '2-3 Days';
            },
            selectedAddressDistrictTitle(){
                const district = (Array.isArray(this.districts) ? this.districts : [])
                    .find(item => String(item.id) === String(this.resolvedShippingDistrict));
                return district ? district.title : (this.resolvedLocation ? this.resolvedLocation.district_title : '');
            },
            selectedAddressThanaTitle(){
                const upazila = (Array.isArray(this.upazilas) ? this.upazilas : [])
                    .find(item => String(item.id) === String(this.resolvedShippingThana));
                return upazila ? upazila.title : (this.resolvedLocation ? this.resolvedLocation.upazila_title : '');
            },
            selectedAddressUnionTitle(){
                const union = (Array.isArray(this.unions) ? this.unions : [])
                    .find(item => String(item.id) === String(this.resolvedShippingUnion));
                return union ? union.title : (this.resolvedLocation ? this.resolvedLocation.union_title : '');
            },
            guestLocationDisplay(){
                return [this.selectedAddressDistrictTitle, this.selectedAddressThanaTitle]
                    .filter(Boolean)
                    .join(' / ');
            },
            formattedOtpResendTime(){
                const minutes = Math.floor(this.otpResendSeconds / 60);
                const seconds = this.otpResendSeconds % 60;
                return ('0' + minutes).slice(-2) + ':' + ('0' + seconds).slice(-2);
            },
            hasSelectedAddressLocation(){
                return Boolean(this.resolvedShippingDistrict && this.resolvedShippingThana);
            },
            hasUsefulAddressDetail(){
                if(!this.hasSelectedAddressLocation){ return false; }
                let residual = this.shippingAddressInput.toLowerCase()
                    .replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit))
                    .replace(/[,.;:|/\\_\-()]+/g, ' ')
                    .replace(/\s+/g, ' ').trim();
                const detailTerms = ['village','union','area','road','street','lane','house','holding','office','building','shop','market','mosque','school','college','institution','sector','block','floor','flat','para','moholla','গ্রাম','ইউনিয়ন','এলাকা','রোড','সড়ক','গলি','বাড়ি','বাসা','হোল্ডিং','অফিস','ভবন','দোকান','মার্কেট','বাজার','মসজিদ','স্কুল','কলেজ','প্রতিষ্ঠান','সেক্টর','ব্লক','তলা','ফ্লোর','ফ্ল্যাট','পাড়া','মহল্লা'];
                const districtEquivalents = {
                    '3': ['barguna', 'বরগুনা'],
                    '14': ['dhaka', 'ঢাকা'],
                    '19': ['gazipur', 'গাজীপুর']
                };
                const thanaEquivalents = {
                    '67': ['keraniganj', 'কেরানীগঞ্জ'],
                    '81': ['mirpur 10', 'mirpur 11', 'মিরপুর ১০', 'মিরপুর ১১'],
                    '94': ['mohammadpur', 'মোহাম্মদপুর'],
                    '122': ['savar', 'সাভার'],
                    '142': ['turag', 'তুরাগ', 'dharangartek', 'dharangertek', 'ধরঙ্গারটেক'],
                    '181': ['amtali', 'amtoli', 'amtoly', 'আমতলী'],
                    '371': ['tongi', 'টঙ্গি', 'টঙ্গী'],
                    '562': ['mohammadpur', 'মোহাম্মদপুর']
                };
                const structuredEquivalents = this.resolvedLocation && Array.isArray(this.resolvedLocation.location_evidence)
                    ? this.resolvedLocation.location_evidence
                    : [];
                const equivalents = [this.selectedAddressDistrictTitle, this.selectedAddressThanaTitle]
                    .concat(districtEquivalents[String(this.resolvedShippingDistrict)] || [])
                    .concat(thanaEquivalents[String(this.resolvedShippingThana)] || [])
                    .concat(structuredEquivalents)
                    .filter(Boolean)
                    .map(value => String(value).toLowerCase().replace(/[,.;:|/\\_\-()]+/g, ' ').replace(/\s+/g, ' ').trim())
                    .sort((left, right) => right.length - left.length);
                equivalents.forEach(equivalent => {
                    const escaped = equivalent.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '\\s+');
                    residual = residual.replace(new RegExp('(^|\\s)' + escaped + '(?=\\s|$)', 'gu'), ' ');
                });
                residual = residual.replace(/\s+/g, ' ').trim();
                const addressTokens = residual.split(/\s+/).filter(Boolean);
                if(detailTerms.some(term => addressTokens.indexOf(term) !== -1)){
                    return true;
                }
                return /[0-9০-৯]/u.test(residual);
            },
            orderedAddressUpazilas(){
                const upazilas = Array.isArray(this.upazilas) ? this.upazilas : [];
                const candidateIds = this.resolverCandidates
                    .filter(candidate => String(candidate.district_id) === String(this.resolvedShippingDistrict))
                    .map(candidate => String(candidate.upazila_id));
                return upazilas.slice().sort((left, right) => {
                    const leftIndex = candidateIds.indexOf(String(left.id));
                    const rightIndex = candidateIds.indexOf(String(right.id));
                    if(leftIndex === -1 && rightIndex === -1){ return 0; }
                    if(leftIndex === -1){ return 1; }
                    if(rightIndex === -1){ return -1; }
                    return leftIndex - rightIndex;
                });
            },
            collectedVoucher(){
                return this.$store.getters.getLoadedVocher;
            },
            useableVouchers(){
                return this.$store.getters.getLoadedUseableVocher;
            },
    
    
            cartData(){
              const loadedCart = this.$store.getters.getLoadedCart;
              this.sub_total = loadedCart.sub_total;
              const shippingCost = Number(this.sub_total) >= this.freeDeliveryThreshold
                ? 0
                : Number(loadedCart.shipping_cost || 0);
              this.finalCalculatedTotal = Number(this.sub_total || 0) + shippingCost;
              return loadedCart;
            },
            user(){
              return this.$store.getters.getLoadedUser.id;
            },
            logged_in_user(){
                return this.$store.getters.getLoadedUser.user || {};
            },
            logged_in_user_address(){
                let x = this.$store.getters.getLoadedUser.address;
                let res = 0;
                if(x != undefined){
                    if(x.length != 0){
                        res = this.$store.getters.getLoadedUser.address;
                    }
                }
                return res;
            },
            cartContainsGrocery(){
                const loadedCart = this.$store.getters.getLoadedCart;
                const groups = loadedCart && Array.isArray(loadedCart.cart) ? loadedCart.cart : [];

                return groups.some(group => {
                    const items = Array.isArray(group.items) ? group.items : [];
                    return items.some(item => {
                        return item.product && item.product.is_grocery === 'grocery';
                    });
                });
            }
        },
    
        beforeDestroy(){
            if(this.resolverDebounceTimer){
                clearTimeout(this.resolverDebounceTimer);
            }
            this.resolverRequestSequence++;
            if(this.otpResendTimer){
                clearInterval(this.otpResendTimer);
                this.otpResendTimer = null;
            }
            if(this.checkoutOfferTimer){
                clearInterval(this.checkoutOfferTimer);
                this.checkoutOfferTimer = null;
            }
            this._advanceToAddressQueued = false;
            const checkoutModals = jQuery('#checkoutOtpModal, #checkoutAddressModal');
            checkoutModals.off('.checkoutVisibility').off('hidden.bs.modal').modal('hide');
            checkoutModals.appendTo(this.$el);
            jQuery('.modal-backdrop').remove();
            jQuery('body').removeClass('modal-open');
            jQuery('body').css('padding-right', '');
        },

        mounted(){
            this.scrollToTop();
            const plugin = document.createElement("script");
            plugin.setAttribute( "src",this.$frontendUrl+"/assets/js/parts/cart.js");
            plugin.async = true;
            document.body.appendChild(plugin);
            this.baseurl = this.$baseUrl;
            document.title = "LuxiQue | Checkout"; 
            // this.getCollectedVoucher();
            // this.getUseableVouchers();
            this.$store.dispatch('loadedVoucher');
            this.$store.dispatch('loadedUsableVoucher');
            this.loading_method();
            this.loadAddressDistricts();
            this.loadRelatedProducts();
            this.checkoutOfferTimer = setInterval(() => {
                this.checkoutOfferClock = Date.now();
            }, 1000);
        }

    }
    </script>

<style scoped>
    .checkout-modal {
        z-index: 20000;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    .checkout-modal .modal-dialog {
        width: auto;
        margin: 1.5rem auto;
    }
    .checkout-address-dialog {
        max-width: 760px;
    }
    .checkout-otp-dialog {
        max-width: 430px;
    }
    .checkout-modal-content {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 18px 50px rgba(0, 0, 0, .2);
        overflow: hidden;
    }
    .checkout-modal-header {
        align-items: center;
        min-height: 62px;
        padding: 16px 22px;
        border-bottom: 1px solid #ececec;
        background: #fff;
    }
    .checkout-modal-header .modal-title {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        line-height: 1.3;
    }
    .checkout-modal-header .close {
        margin: -8px -8px -8px auto;
        padding: 8px;
        line-height: 1;
    }
    .checkout-modal-body {
        max-height: calc(100vh - 130px);
        padding: 20px 22px 22px;
        overflow-y: auto;
    }
    .checkout-modal-body label {
        margin-bottom: 6px;
        color: #666;
        font-size: 13px;
        font-weight: 600;
    }
    .checkout-modal-body .form-group {
        margin-bottom: 16px;
    }
    .checkout-modal-body .form-control {
        border-color: #dfe3e6;
        border-radius: 7px;
        background: #fff;
    }
    .checkout-modal-body .form-control:focus {
        border-color: #0f8f87;
        box-shadow: 0 0 0 3px rgba(15, 143, 135, .12);
    }
    .checkout-address-submit {
        min-width: 150px;
        padding: 10px 24px;
        font-weight: 600;
    }
    .checkout-modal-body .options > p:last-child {
        margin-top: 4px;
        padding-top: 16px;
        border-top: 1px solid #edf0f2;
    }
    .checkout-otp-body {
        padding-top: 22px;
        text-align: center;
    }
    .checkout-otp-phone {
        margin-bottom: 18px;
        color: #555;
    }
    .checkout-otp-digits {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin: 4px 0 18px;
    }
    .checkout-otp-digit {
        flex: 0 0 54px;
        width: 54px;
        height: 58px;
        padding: 0;
        border: 1px solid #d9d9d9;
        border-radius: 9px;
        text-align: center;
        font-size: 25px;
        font-weight: 700;
        line-height: 58px;
    }
    .checkout-otp-digit:focus {
        border-color: #0f8f87;
        box-shadow: 0 0 0 3px rgba(15, 143, 135, .12);
    }
    .checkout-otp-submit {
        min-height: 44px;
        font-weight: 600;
    }
    .checkout-resend {
        min-height: 24px;
        margin-top: 14px;
        color: #777;
        text-align: center;
    }
    .guest-address-summary {
        position: relative;
        padding: 14px 42px 14px 16px;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        background: #fff;
    }
    .guest-address-edit {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 50%;
        background: #f3f3f3;
        cursor: pointer;
    }
    .checkout-premium {
        padding: 32px 0 48px;
        background: #f6f7f9;
    }
    .delivery-estimate-card i { color: #0f8f87; }
    .checkout-premium .cart-page-container {
        align-items: flex-start;
    }
    .checkout-premium .shoping-cart-text {
        margin: 0 0 12px;
    }
    .checkout-premium .shoping-cart-text h4 {
        margin: 0;
        color: #222;
    }
    .checkout-item-count {
        color: #777;
    }
    .checkout-order-card,
    .checkout-sidebar > .address_details,
    .checkout-sidebar > .address_details_alt,
    .checkout-sidebar > .guest-address-summary,
    .checkout-sidebar > .note,
    .checkout-sidebar > .checkout-free-delivery-progress,
    .checkout-sidebar > .checkout-offer-card,
    .checkout-sidebar > .paymentmethod,
    .checkout-sidebar > .payment-calculation,
    .checkout-sidebar > .procced-checkout,
    .checkout-sidebar > .checkout-coupon-bottom {
        border: 1px solid #e9eaed;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(25, 32, 45, .05);
    }
    .checkout-order-card {
        padding: 8px 18px 14px;
        overflow: hidden;
    }
    .checkout-order-card .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    .checkout-order-card .table thead th {
        padding: 13px 10px;
        border-top: 0;
        border-bottom: 1px solid #eceef1;
        color: #777;
    }
    .checkout-order-card .table td {
        padding: 14px 10px;
        border-top: 1px solid #e9edec;
        border-bottom: 1px solid #e9edec;
        background: #fff;
        vertical-align: middle;
    }
    .checkout-product-row td:first-child { border-left: 1px solid #e9edec; border-radius: 8px 0 0 8px; }
    .checkout-product-row td:last-child { border-right: 1px solid #e9edec; border-radius: 0 8px 8px 0; }
    .checkout-order-card .group_header td {
        padding-top: 15px;
        padding-bottom: 7px;
        border-top: 0;
        color: #777;
        border: 0;
        background: transparent;
    }
    .checkout-order-card .product-cart-img {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        object-fit: cover;
    }
    .checkout-product-number {
        flex: 0 0 28px;
        padding-top: 4px;
        color: #93a09f;
    }
    .checkout-quantity-control {
        display: inline-flex;
        height: 34px;
        align-items: center;
        vertical-align: middle;
        border: 1px solid #e4e8e7;
        border-radius: 6px;
        background: #fafbfb;
        overflow: hidden;
    }
    .checkout-quantity-control button { display: inline-flex; width: 34px; height: 34px; padding: 0; border: 0; align-items: center; justify-content: center; color: #0f8f87; background: transparent; cursor: pointer; }
    .checkout-quantity-control button:disabled { color: #aeb6b4; cursor: not-allowed; }
    .checkout-quantity-control span { display: inline-flex; min-width: 30px; height: 34px; align-items: center; justify-content: center; text-align: center; }
    .checkout-remove-button { width: 34px; height: 34px; border: 0; border-radius: 50%; color: #a44; background: #fff3f3; cursor: pointer; }
    .checkout-related-section { margin-top: 16px; padding: 16px; border: 1px solid #e9eaed; border-radius: 10px; background: #fff; box-shadow: 0 5px 18px rgba(25, 32, 45, .05); }
    .checkout-related-section h5 { margin-bottom: 12px; }
    .checkout-related-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; }
    .checkout-related-card { display: flex; min-width: 0; flex-direction: column; gap: 7px; padding: 10px; border: 1px solid #edf0ef; border-radius: 8px; }
    .checkout-related-card img { width: 100%; height: 100px; border-radius: 6px; object-fit: cover; }
    .checkout-related-title { display: -webkit-box; min-height: 38px; overflow: hidden; color: #303735; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
    .checkout-related-card strong { color: #0f8f87; }
    .checkout-related-add { margin-top: auto; padding: 7px 8px; border: 1px solid #0f8f87; color: #0f8f87; background: #fff; }
    .checkout-empty-cart { padding: 44px 15px; }
    .checkout-empty-cart img { max-width: 220px; width: 100%; }
    .checkout-order-card .media-body h5 {
        margin-bottom: 5px;
    }
    .checkout-sidebar {
        background: transparent;
    }
    .checkout-sidebar > h5 {
        margin: 0 0 12px;
    }
    .checkout-sidebar > .address_details,
    .checkout-sidebar > .address_details_alt,
    .checkout-sidebar > .note,
    .checkout-sidebar > .checkout-free-delivery-progress,
    .checkout-sidebar > .checkout-offer-card,
    .checkout-sidebar > .paymentmethod,
    .checkout-sidebar > .payment-calculation,
    .checkout-sidebar > .procced-checkout,
    .checkout-sidebar > .checkout-coupon-bottom {
        margin-top: 12px;
        padding: 16px;
    }
    .delivery-estimate-card {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 12px;
        padding: 12px 14px;
        border: 1px solid #dcecea;
        border-radius: 10px;
        color: #344643;
        background: #f6fbfa;
    }
    .delivery-estimate-card span,
    .delivery-estimate-card small { display: block; }
    .delivery-estimate-card small { margin-top: 2px; color: #71807e; }
    .previous-address-badge {
        display: inline-block;
        margin-top: 8px;
        padding: 4px 8px;
        border-radius: 12px;
        color: #0b756f;
        background: #e8f7f5;
    }
    .checkout-offer-card {
        position: relative;
        color: #27524e;
        border-color: #cfe9e5;
        background: #effaf8;
    }
    .checkout-offer-heading { display: flex; gap: 10px; align-items: center; justify-content: space-between; }
    .checkout-offer-card p {
        margin: 10px 0 5px;
    }
    .checkout-offer-card small {
        color: #547773;
    }
    .checkout-offer-badge {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        color: #fff;
        background: #0f8f87;
    }
    .checkout-premium .checkout-sidebar > .checkout-free-delivery-progress {
        display: block;
        width: 100%;
        min-width: 0;
        overflow: visible;
        color: #315450;
        border-color: #cfe9e5;
        background: #f2fbf9;
    }
    .checkout-free-delivery-title {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }
    .checkout-free-delivery-title p { margin: 2px 0 0; }
    .checkout-free-delivery-icon {
        display: inline-flex;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #0f8f87;
        background: #dff4f0;
    }
    .checkout-free-delivery-bar {
        height: 7px;
        margin-top: 12px;
        overflow: hidden;
        border-radius: 999px;
        background: #dfeae8;
    }
    .checkout-free-delivery-fill {
        height: 100%;
        border-radius: inherit;
        background: #0f8f87;
        transition: width .3s ease;
    }
    .checkout-free-delivery-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 7px;
        color: #52716d;
    }
    .checkout-free-delivery-eligible {
        border-color: #a9ddd5;
        background: #e9f8f5;
    }
    .checkout-premium .checkout-sidebar > #addCouponBlock.checkout-coupon-bottom {
        min-height: 24px;
        overflow: visible;
        margin-bottom: 12px;
        display: block;
        visibility: visible;
    }
    .checkout-coupon-toggle {
        width: 100%;
        padding: 0;
        border: 0;
        color: #0f8f87;
        background: transparent;
        text-align: left;
    }
    .checkout-sidebar #addCouponBlock .form-control,
    .checkout-sidebar #addCouponBlock .input-group-text {
        height: 42px;
        border-color: #dfe3e6;
    }
    .checkout-sidebar #addCouponBlock .input-group-text {
        color: #fff;
        background: #0f8f87;
        cursor: pointer;
    }
    .checkout-sidebar .paymentmethod h5,
    .checkout-sidebar .payment-calculation h5 {
        margin-bottom: 12px;
    }
    .checkout-sidebar .paymentmethod .list-group-item {
        flex: 1 1 50%;
        padding: 12px 8px;
        border-color: #e6e7ea;
        cursor: pointer;
    }
    .checkout-sidebar .paymentmethod {
        display: block !important;
        visibility: visible !important;
    }
    .checkout-sidebar .paymentmethod .list-group {
        display: flex !important;
        width: 100%;
        flex-direction: row;
    }
    .checkout-sidebar .paymentmethod .selected_payment {
        border-color: #0f8f87;
        background: #f0fbfa;
        box-shadow: inset 0 0 0 1px #0f8f87;
    }
    .checkout-sidebar .payment-calculation ul,
    .checkout-sidebar .procced-checkout ul {
        margin: 0;
    }
    .checkout-sidebar .payment-calculation li {
        padding: 8px 0;
    }
    .checkout-sidebar .payment-calculation li:last-child {
        margin-top: 5px;
        padding-top: 13px;
        border-top: 1px solid #e5e8ea;
    }
    .free-delivery-label { color: #0f8f87; }
    .checkout-sidebar .proceed_to_pay {
        position: static !important;
        inset: auto !important;
        float: none;
        display: block;
        width: 100%;
        min-height: 46px;
        margin-top: 10px;
    }
    .checkout-sidebar .proceed_to_pay,
    .checkout-address-submit,
    .checkout-otp-submit {
        border-color: #0f8f87 !important;
        background: #0f8f87 !important;
        box-shadow: none !important;
    }
    .checkout-sidebar .proceed_to_pay:hover,
    .checkout-address-submit:hover,
    .checkout-otp-submit:hover {
        border-color: #0b756f !important;
        background: #0b756f !important;
    }
    @media (max-width: 767px) {
        .checkout-modal.show { display: block !important; }
        .checkout-modal .modal-dialog {
            margin: .75rem;
        }
        .checkout-modal .modal-dialog-centered {
            min-height: calc(100% - 1.5rem);
            align-items: flex-start;
        }
        .checkout-modal-header {
            min-height: 56px;
            padding: 14px 16px;
        }
        .checkout-modal-body {
            max-height: calc(100vh - 90px);
            padding: 16px;
        }
        .checkout-address-submit {
            width: 100%;
        }
        .checkout-otp-digits {
            gap: 8px;
        }
        .checkout-otp-digit {
            flex-basis: 48px;
            width: 48px;
            height: 54px;
            line-height: 54px;
        }
        .checkout-premium {
            padding: 20px 0 150px;
            overflow-x: hidden;
        }
        .checkout-premium > .container { width: 100%; max-width: 100%; padding-right: 15px; padding-left: 15px; }
        .checkout-order-column {
            padding-right: 15px !important;
        }
        .checkout-order-card {
            padding: 4px 10px 10px;
            overflow: visible;
        }
        .checkout-order-card .table,
        .checkout-order-card tbody,
        .checkout-product-row { display: block; width: 100%; }
        .checkout-order-card .table { min-width: 0; border-spacing: 0; }
        .checkout-order-card thead,
        .checkout-order-card .group_header { display: none; }
        .checkout-order-card tbody { margin: 10px 0; }
        .checkout-product-row { display: grid; grid-template-columns: minmax(0, 1fr) auto; grid-template-areas: "details details" "unit total" "quantity remove"; gap: 10px 12px; padding: 12px; border: 1px solid #e9edec; border-radius: 9px; background: #fff; }
        .checkout-order-card .checkout-product-row td { padding: 0; border: 0; border-radius: 0; }
        .checkout-product-details { grid-area: details; }
        .checkout-unit-price { grid-area: unit; }
        .checkout-quantity-cell { grid-area: quantity; text-align: left !important; }
        .checkout-line-total-cell { grid-area: total; text-align: right; }
        .checkout-remove-cell { grid-area: remove; }
        .checkout-product-number { flex-basis: 24px; }
        .checkout-order-card .product-cart-img { width: 58px; height: 58px; }
        .checkout-related-section { padding: 14px 12px; overflow: hidden; }
        .checkout-related-list { display: flex; gap: 10px; overflow-x: auto; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; padding-bottom: 5px; }
        .checkout-related-card { flex: 0 0 160px; scroll-snap-align: start; }
        .checkout-quantity-control,
        .checkout-quantity-control span { height: 38px; }
        .checkout-quantity-control button { width: 38px; height: 38px; }
        .checkout-sidebar,
        .checkout-sidebar > div,
        .checkout-sidebar textarea,
        .checkout-sidebar .input-group,
        .checkout-sidebar .proceed_to_pay { max-width: 100%; }
        .checkout-sidebar {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
            padding-right: 15px;
            padding-left: 15px;
            overflow: visible;
        }
        .checkout-order-column { order: 1; }
        .checkout-sidebar { order: 2; }
        .checkout-sidebar > h5 { order: 1; }
        .checkout-sidebar > .address_details,
        .checkout-sidebar > .address_details_alt,
        .checkout-sidebar > .guest-address-summary { order: 2; }
        .checkout-sidebar > .delivery-estimate-card { order: 3; }
        .checkout-sidebar > .note { order: 4; }
        .checkout-sidebar > .collect_voucher_modal,
        .checkout-sidebar > .voucher_button { order: 5; }
        .checkout-sidebar > .checkout-free-delivery-progress { order: 6; }
        .checkout-sidebar > .checkout-offer-card { order: 7; }
        .checkout-sidebar > .payment-calculation { order: 8; }
        .checkout-sidebar > .paymentmethod { order: 9; }
        .checkout-sidebar > .procced-checkout { order: 10; }
        .checkout-sidebar > .checkout-coupon-bottom { order: 11; }
        .checkout-sidebar > .address_details,
        .checkout-sidebar > .address_details_alt,
        .checkout-sidebar > .guest-address-summary,
        .checkout-sidebar > .note,
        .checkout-sidebar > .checkout-free-delivery-progress,
        .checkout-sidebar > .checkout-offer-card,
        .checkout-sidebar > .payment-calculation,
        .checkout-sidebar > .paymentmethod,
        .checkout-sidebar > .procced-checkout,
        .checkout-sidebar > .checkout-coupon-bottom {
            width: 100%;
            min-width: 0;
            overflow-wrap: anywhere;
        }
        .checkout-sidebar .paymentmethod .list-group-item {
            flex: 1 1 50%;
            min-width: 0;
            padding: 10px 5px;
            overflow: hidden;
        }
        .checkout-sidebar .paymentmethod img {
            max-width: 82px;
            height: auto;
        }
        .checkout-coupon-bottom .input-group {
            flex-wrap: nowrap;
        }
        .checkout-coupon-bottom .form-control {
            min-width: 0;
        }
        .checkout-sidebar .proceed_to_pay { display: block; width: 100%; margin-bottom: 0; }
        .checkout-free-delivery-progress { padding: 13px; }
    }
    </style>
