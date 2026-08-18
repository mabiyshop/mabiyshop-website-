<template>
<div>

<section id="profile-page">
    <div class="container">
	<div class="col-md-12 account_wrapper bg_white">
		<div class="row profile">
			<div class="col-md-1">
				<img @error="imageLoadError" v-if="userData.avatar != null" :src="baseurl+'/'+userData.avatar" >
			</div>
			<div class="col-md-11">
				<div class="username">
					<b>{{ userData.name  }}</b>
					<p class="mb-0">{{ userData.email }}</p>
                    <p v-if="userData.user_type == 2"> <span class="badge badge-danger">{{$t('Corporate Customer')}}</span> </p>
				</div>
			</div>
		</div>
        <div class="row account_box">
            <div class="col-md-2 col-lg-2 profile-navigation">
                <div class="profile-nav">
                    <Leftsidebar></Leftsidebar>
                </div>
            </div>
            <div class="col-md-10 col-lg-10">
                <div class="order_page single_order">
                    <div v-if="orderLoadError" class="alert alert-danger mt-3" role="alert">
                        {{ orderLoadError }}
                    </div>

                    <div class='row mb-3 mt-3'>
                        <div class="col-12 col-sm-12 col-md-12 text-right">
                           
                           <span v-if="!single_order.parent_order_id">
                            
                            <button v-if="!auto_renewal" type="button" class="btn btn-danger"  data-toggle="modal" data-target="#staticBackdrop" > {{ $t('Order Auto Renewal') }}</button>

                            <span v-if="single_order.auto_renewal">
                                <button v-if="single_order.auto_renewal.status == 0" type="button" class="btn btn-danger"  data-toggle="modal" data-target="#staticBackdrop" > {{ $t('Reactive Order Auto Renewal') }}</button>
                            </span>

                            <span v-if="single_order.auto_renewal">
                                <button v-if="single_order.auto_renewal.status == 1" @click.prevent="cancelAutoRenewal()" type="button" class="btn btn-danger cancel-auto-renewal"> {{ $t('Cancel Auto Renewal') }}</button>
                            </span>
                            
                            <span v-if="single_order.auto_renewal">
                                <button v-if="single_order.auto_renewal.status == 1"  data-toggle="modal" data-target="#staticBackdrop" type="button" class="btn btn-secondary"> {{ $t('Update Auto Renewal') }}</button>
                            </span>
                           

                        </span>

                            <!-- Modal -->
                            <div v-if="!single_order.parent_order_id" class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="staticBackdropLabel">{{ $t('Order Auto Renewal') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                        <div class="modal-body text-left">
                                            <p>NEVER RUN OUT OF YOUR FAVORITE PRODUCTS AGAIN!</p> 
                                            <p>With our Order Auto Renewal  Program, enjoy the convenience of having products delivered to your door step by daily/weekly/bi-weekly/monthly.</p>
                                            <ul>
                                                <li> <i class="fa fa-caret-right"></i> You set the schedule</li>
                                                <li><i class="fa fa-caret-right"></i> Easily add or remove products</li>
                                                <li><i class="fa fa-caret-right"></i> Skip shipments anytime</li>
                                                <li><i class="fa fa-caret-right"></i> Cancel anytime, no hassles</li>
                                                <li><i class="fa fa-caret-right"></i> Receive message / email reminders.</li>
                                                <li><i class="fa fa-caret-right"></i> Get Products at your door step!</li>
                                            </ul>

                                            

                                            <div class="auto_renewal_form">
                                                <div class="form-group">
                                                    <label  class="text-uppercase" for="renewal_cycle">Order Renewal Cycle</label>
                                                    <select name="renewal_cycle" class="renewal_cycle form-control" required>
                                                        <option value="0"> -- Select Auto Renewal Cycle --</option>
                                                        <option value="1">Daily</option>
                                                        <option value="7">Weekly</option>
                                                        <option value="15">Bi - Weekly</option>
                                                        <option value="30">Monthly</option>
                                                </select>
                                                </div>
                                                
                                                <button type="button" @click.prevent="setAutoRenewal()" class="btn btn-primary">{{ $t('Set Auto Renewal') }}</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary" @click.prevent="printInvoice()"> {{ $t('Print Invoice') }}</button>     
                            <button data-toggle="modal" data-target="#staticBackdropPartial" v-if="(single_order.total_amount - single_order.paid_amount) > 0 && single_order.payment_method == 'online_payment'" type="button" class="btn btn-dark"> {{ $t('Pay Due Amount') }}</button>


                             <!-- Modal -->
                            <div v-if="(single_order.total_amount - single_order.paid_amount) > 0 && single_order.payment_method == 'online_payment'"  class="modal fade" id="staticBackdropPartial" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropPartialLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropPartialLabel">{{ $t('Pay Due Amount') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                            <div class="modal-body text-left">

                                                <div class="form-group">
                                                    <label  class="text-uppercase" for="payment_amount">Payment Amount</label>
                                                    <input type="number" id="payment_amount" name="payment_amount" placeholder="Amount you want to pay.." class="form-control">
                                                </div>
                                                <button type="button" @click.prevent="payAgain(single_order.id)" class="btn btn-primary">{{ $t('Pay Now') }}</button>
                                    
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-2">
                            <h5>{{ $t('Order Details') }}</h5>
                            <div class="single_order_details mb-3 ">
                                <div class="card">
                                    <div class="card-body pb-0">
                                        <ul v-if="single_order">

                                            

                                            <li v-if="single_order.parent_order_id">
                                                <p class="text-danger">This order has been autometically generated by system.
                                                    Parent Order ID is MS{{single_order.created_at|prefix_year}}{{single_order.parent_order_id}}. If you want to cancel/renew/update this auto renewal order system  <router-link :to="{name: 'orderDetails', params: {id: single_order.parent_order_id } }">click here</router-link> . </p>
                                            </li>

                                            <li v-if="!single_order.parent_order_id">
                                                <span v-if="single_order.auto_renewal">
                                                     <p class="text-danger" v-if="single_order.auto_renewal.status == 1">This order has 

                                                        <span class="text-danger text-uppercase" v-if="single_order.auto_renewal.renewal_cycle == 1">Daily</span>
                                                        <span class="text-danger text-uppercase" v-else-if="single_order.auto_renewal.renewal_cycle == 7">Weekly</span>
                                                        <span class="text-danger text-uppercase" v-else-if="single_order.auto_renewal.renewal_cycle == 15">Bi-Weekly</span>
                                                        <span class="text-danger text-uppercase" v-else-if="single_order.auto_renewal.renewal_cycle == 30">Monthly</span>
                                                        auto renewal service. Next order will be autometically place at {{single_order.auto_renewal.next_order_date}}</p>
                                                </span>
                                            </li>
                                            
                                            <li> <b>{{ $t('Order ID') }}: </b> <span>  MS{{single_order.created_at|prefix_year}}{{ single_order.id }} </span>  </li>
                                            <li> <b>{{ $t('Date') }}: </b> <span> {{ single_order.created_at | formatDate }}</span> </li>
                                            <li> <b>{{ $t('Payment Status') }} : </b> <span  :style="{ background: single_order.statuses.color_code, color:'#fff', padding:4+'px', borderRadius:4+'px'}"> {{ single_order.statuses.title }}</span> </li>
                                            <li> <b>{{ $t('Payment Method') }}: </b> <span> {{ single_order.payment_method }} </span> </li>
                                            
                                            <li v-if="single_order.grocery_shipping_cost > 0"> <b>{{ $t('Grocery Shipping Cost') }}: </b> <span>BDT {{ single_order.grocery_shipping_cost }} </span> </li>
                                            
                                            <li> <b>{{ $t('Total Shipping Cost') }}: </b> <span>BDT {{ single_order.shipping_cost }} </span> </li>
                                            <li  v-if="single_order.vat > 0"> <b>{{ $t('VAT') }}: </b> <span>BDT {{ single_order.vat }} </span> </li>
                                            <li v-if="single_order.coupon_amount > 0"> <b>{{ $t('Coupon Discount') }}: </b> <span>BDT {{ single_order.coupon_amount }} </span> </li>
                                            <li v-if="single_order.voucher_amount > 0"> <b>{{ $t('Voucher Discount') }}: </b> <span>BDT {{ single_order.voucher_amount }} </span> </li>
                                           
                                            <li> <b>{{ $t('Total Amount') }}: </b> <span>BDT {{ single_order.total_amount }} </span> </li>
                                            <li> <b>{{ $t('Paid Amount') }}: </b> <span>BDT {{ single_order.paid_amount }} </span> </li>
                                            <li v-if="single_order.refunded > 0"> <b>{{ $t('Refunded Amount') }}: </b> <span>BDT {{ single_order.refunded }} </span> </li>

                                            <li class="text-danger" v-if="(single_order.total_amount - single_order.paid_amount) > 0" >
                                                <b>{{ $t('Due Amount') }}: </b> <span>BDT {{ single_order.total_amount - single_order.paid_amount }} </span> 
                                            </li>
                                            <li v-if="single_order.note "> <b>{{ $t('Note') }}: </b> <span> {{ single_order.note  }} </span> </li>
                                            <li v-if="single_order.is_pickpoint == 1" ><span class="badge badge-danger">{{$t('Pick Point Order')}}</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2">
                             <h5 style="font-size: 20px;">{{ $t('Shipping information') }} </h5>
                            <div class="single_order_details mb-3">
                                <div class="card">
                                    <div class="card-body pb-0">
                                        <ul v-if="address && single_order.is_pickpoint != 1">
                                            <li> <b>{{ $t('Full Name') }} : </b> <span> {{ address.shipping_first_name }} </span> </li>
                                            <li> <b>{{ $t('Address') }}: </b> <span> {{ formattedShippingAddress }} </span> </li>
                                            <li> <b>{{ $t('Post code') }}: </b> <span> {{ address.shipping_postcode }} </span> </li>
                                            <li> <b>{{ $t('Phone') }}: </b> <span> {{ address.shipping_phone }} </span> </li>
                                            <li> <b>{{ $t('Email') }}: </b> <span> {{ address.shipping_email }} </span> </li>
                                        </ul>

                                        <ul v-if="address && single_order.is_pickpoint == 1">
                                            <li> <b>{{ $t('Pick Point') }} : </b> <span> {{ address.title }} </span> </li>
                                            <li v-if="address.division"> <b>{{ $t('Division') }}: </b> <span> {{ address.division.title }} </span> </li>
                                            <li v-if="address.district"> <b>{{ $t('District') }}: </b> <span> {{ address.district.title }} </span> </li>
                                            <li v-if="address.upazila"> <b>{{ $t('Upazila / Thana') }} : </b> <span> {{ address.upazila.title }} </span> </li>
                                            <li v-if="address.union"> <b>{{ $t('Union / Area') }}: </b> <span> {{ address.union.title }} </span> </li>
                                            <li> <b>{{ $t('Post code') }}: </b> <span> {{ address.postcode }} </span> </li>
                                            <li v-if="address.phone"> <b>{{ $t('Phone') }}: </b> <span> {{ address.phone }} </span> </li>
                                            <li v-if="address.email"> <b>{{ $t('Email') }}: </b> <span> {{ address.email }} </span> </li>
                                            <li> <b>{{ $t('Address') }}: </b> <span> {{ address.address }} </span> </li>
                                        </ul>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <h5>{{ $t('Products') }}</h5>
                    <div class="single_order_details mb-4">
                        <ul v-if="order_products">
                            <div class="user-orders border_only" style="overflow-x:auto;" id="single_order_details">
                                <table class="table table-bordered user-orders-full" width="100%">
                                    <thead>
                                    <tr>
                                        <th width="35%">{{ $t('Product') }}</th>
                                        <!-- <th width="15%">{{ $t('Name') }}</th> -->
                                        <th width="15%">{{ $t('Price') }}</th>
                                        <th width="5%"> {{ $t('Qty') }}</th>
                                        <th width="10%"> {{ $t('Shipping Cost') }}</th>
                                        <th width="10%"> {{ $t('Packaging Cost') }}</th>
                                        <th width="10%"> {{ $t('Security Charge') }}</th>
                                        <th width="15%"> {{ $t('Sub Total') }}</th>
                                        <th width="15%">{{ $t('Shipping Status') }}</th>
                                        <th width="15%"> {{ $t('Action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(productData, index) in order_products" :key="index">
                                            <td class="text-left"> 
                                                <router-link :to="{ name: 'product', params: {slug: productData.product.slug } }">
                                                    <img @error="imageLoadError" :src="baseurl+'/'+productData.product.default_image" >
                                                    <br/>
                                                    {{ productData.product.title }}
                                                    <br/><b>SKU:</b> {{ productData.product_sku }}
                                                    <br/> <span v-if="productData.product_options"> 
                                                        <span  v-if="productData.product.product_type == 'variable'">
                                                            <p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in productData.product_options" :key="key"> <b>{{key}}</b> : {{vOption}}</p>
                                                        </span>

                                                        <span  v-if="productData.product.product_type == 'service'">
                                                            <p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in productData.product_options" :key="key"> <b>{{ key.replace('_',' ') }}</b> : {{vOption}}</p>
                                                        </span>

                                                    </span>
                                                </router-link>
                                            </td>
                                          
                                            <td>BDT {{ productData.price }} </td>
                                            <td> {{ productData.product_qty }} </td>
                                            <td> BDT {{ productData.shipping_cost }} </td>
                                            <td> BDT {{  productData.packaging_cost ? productData.packaging_cost : 0   }} </td>
                                            <td> BDT {{ productData.security_charge ? productData.security_charge : 0  }} </td>
                                            <td>BDT {{ Number(productData.price * productData.product_qty) + Number(productData.shipping_cost) }} </td>
                                            <td v-for="(status, index) in statuses" :key="index" v-if="status.id == productData.status"> <span  :style="{ background: status.color_code, color:'#fff', borderRadius:4+'px'}" class="badge badge-primary"> {{ status.title }}</span> </td>
                                            <td>  
                                                <router-link v-if="productData.status == 10" :to="{ name: 'return', params: { order_id: single_order.id+','+productData.product.id} }"><button class="btn btn-primary sm mb-1" style="padding: 1px 10px;border-radius:4px;"> {{ $t('Return') }}  </button> </router-link>

                                                <router-link v-if="productData.status != 6"  :to="{ name: 'track', params: { order_id: single_order.id+','+productData.product.id} }"><button class="btn btn-primary sm mb-1" style="padding: 1px 10px;border-radius:4px;"> {{ $t('Track') }}  </button> </router-link>

                                                <button :title="$t('I have accepted this product without any fault or loss')" v-if="productData.status == 10" @click="completeOrder(productData.id)" class="btn btn-success sm mb-1" style="padding: 1px 10px;border-radius:4px;"> {{ $t('Complete') }}  </button> 
     
                                                <button v-if="single_order.status == 6 && productData.isDownloadable == 'downloadable' && productData.product.product_type == 'digital'" @click.prevent="downloadFile(productData.product_id, productData.file_extension)" class="btn btn-primary sm mb-1" style="padding: 1px 10px;border-radius:4px;">  Download </button>

                                                <span v-if="productData.product.allow_review == 1 && productData.status == 6 && productData.reviewed == 0"><button data-toggle="modal" data-target=".staticBackdrop" class="btn btn-success sm mb-1 rate_now_button" style="padding: 1px 10px;border-radius:4px;"> {{ $t('Rate Product') }}  </button> </span>
                                            </td>

                                                 <section v-if="productData.status == 6 && productData.reviewed == 0" class="reviewProduct product-rating-content">

                                                        <div class="modal fade staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="staticBackdropLabel">{{ $t('Submit Your Ratings And Reviews') }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body pb-0">
                                                                        <!--Comment form start-->
                                                                            <span v-if="showCommentForm == 1 && purchased">
                                                                            
                                                                                    <div class="row review-section">
                                                                        
                                                                                            <div class="col-md-12">
                                                                                                <label for=""> {{ $t('Star Rating') }} </label>
                                                                                                <br>
                                                                                                <ul class="star_list pt-0 star_rating">
                                                                                                    
                                                                                                    <li v-if="rate > 0" @click="rating(1)" title="1 star"><i class="star fa fa-star" aria-hidden="true"></i></li>
                                                                                                    <li v-else @click="rating(1)" title="1 star"><i class="star fa fa-star-o" aria-hidden="true"></i></li>
                                                                                                    
                                                                                                    <li v-if="rate > 1" @click="rating(2)" title="1 star"><i class="star fa fa-star" aria-hidden="true"></i></li>
                                                                                                    <li v-else @click="rating(2)" title="2 star"><i class="star fa fa-star-o" aria-hidden="true"></i></li>
                                                                                                    
                                                                                                    <li v-if="rate > 2" @click="rating(3)" title="1 star"><i class="star fa fa-star" aria-hidden="true"></i></li>
                                                                                                    <li v-else @click="rating(3)" title="3 star"><i class="star fa fa-star-o" aria-hidden="true"></i></li>
                                                                                                    
                                                                                                    <li v-if="rate > 3" @click="rating(4)" title="1 star"><i class="star fa fa-star" aria-hidden="true"></i></li>
                                                                                                    <li v-else @click="rating(4)" title="4 star"><i class="star fa fa-star-o" aria-hidden="true"></i></li>
                                                                                                    
                                                                                                    <li v-if="rate > 4" @click="rating(5)" title="1 star"><i class="star fa fa-star" aria-hidden="true"></i></li>
                                                                                                    <li v-else @click="rating(5)" title="5 star"><i class="star fa fa-star-o" aria-hidden="true"></i></li>
                                                                                                    
                                                                                                </ul>
                                                                                            </div>
                                                                                        
                                                                                            <div class="col-md-12 review_form">
                                                                                                <form @submit.prevent="reveiewSubmit()" enctype="multipart/form-data">
                                                                                                    <div class="form-group">
                                                                                                        <label for="">{{ $t('Comment') }}</label>
                                                                                                        <input type="hidden" class="rate" :value="rate">
                                                                                                        <input type="hidden" class="product_id" :value="productData.product.id">
                                                                                                        <input type="hidden" class="require_moderation" :value="productData.product.require_moderation">
                                                                                                        <input type="hidden" class="order_details_id" :value="productData.id">
                                                                                                        <textarea type="text"  name="comment" class="form-control comment" :placeholder="$t('Enter your Comment')" rows="6" required></textarea>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label for="">{{ $t('Image') }}</label>
                                                                                                        <br>
                                                                                                        <!-- <input type="file" id="files" ref="files" multiple v-on:change="handleFilesUpload()"> -->
                                                                                                        <input type="file" id="files" ref="files" @change="handleFilesUpload" multiple>
                                                                                                        <div class="priview_image">
                                                                                                            <div v-for="(image,index) in review_preview" :key="index"> <img :src="image" /> </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div style="clear: both;" class="form-group">
                                                                                                        <button class="btn btn-primary" type="submit">{{ $t('Submit Review') }}</button>
                                                                                                    </div>
                                                                                                </form>
                                                                                            </div>
                                                                                    </div>
                                                                            
                                                                            </span>
                                                                            <!--Comment form end-->
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
    				
                                                </section>
                                          
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
	</div>
</section>


<section id="print_invoice">
    <table style="width: 650px;margin:30px auto">
        <tbody>
            <tr>
                <td style="width: 375px;padding-top: 0px; padding-bottom: 10px;">
                    <p style="margin-bottom: 5px;"><strong style="text-transform:uppercase;font-size: 14px;">Delivery Information</strong><br>
                    <div v-if="address" style="font-size: 12px;padding-right: 0px;">
                        <span style="font-weight: 600;">  Address: </span> {{ formattedShippingAddress }}
                        <br>
                        <span style="font-weight: 600;"> Name: </span>  {{ address.shipping_first_name }}
                        <br>
                        <span style="font-weight: 600;">  HP: </span>  {{ address.shipping_phone }}
                        <br>
                        <span style="font-weight: 600;">  E-mail: </span>  {{ address.shipping_email }}
                    </div>
                    </p>
                </td>
                <td style="width: 245px;text-align:right;padding-left: 0px; font-size: 12px; padding-bottom: 10px;">
                    <p style="font-size:12px; padding: 0;margin: 0;width: 280px;">
                        <!-- <h3 style="text-align: right;margin-bottom: 0;"> INVOICE</h3> -->
                        <img :src="baseurl+'/'+site_info.header_logo" alt="" width="70" />
                        <br>
                        <span style="font-weight: 600;"></span> {{ site_info.cnf_address }} 
                        <br>
                        <span style="font-weight: 600;">  HP: </span> {{ site_info.phone_number }} 
                        <br>
                        <span style="font-weight: 600;">  E-mail: </span> {{ site_info.cnf_email }} 
                    </p>
                </td>
                
            </tr>
            <tr>
                <td style="width: 100%;position: relative;"><hr style="width: 100%;margin: 0px;width: 650px;margin: 0px;position: absolute;z-index: 9999;background: #fdfdfd;"></td>
            </tr>
            
        </tbody>
        
    </table>
    

    <table style="width: 650px;margin:50px auto">
        <tbody>
            <tr>
                <td style="width: 650px;">
                    <div class="row" style="display: flex; -ms-flex-wrap: wrap;flex-wrap: wrap;padding: 0!important;">
                        <div class="col-md-6" style="flex: 0 0 55%;max-width: 55%;position: relative;text-align: right!important;padding: 0!important;"> <span style="text-transform: uppercase;font-size: 20px;font-weight: 600;">Invoice</span>  </div>
                        <div class="col-md-6" style="flex: 0 0 45%;max-width: 45%;position: relative;text-align: right!important;padding: 0!important;">  <span style="text-transform: uppercase;font-size: 14px;font-weight: 600;">  Order ID: MS{{single_order.created_at|prefix_year}}{{ single_order.id }} </span> </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>


    <table id="product" style="width: 650px;margin:50px auto">
        <tbody>
            <tr style="padding: 5px;">
                <td style="width: 70px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong>  SL</strong></td>
                <td style="width: 400px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Item</strong></td>
                <td style="width: 200px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Qty</strong></td>
                <td style="width: 200px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Price</strong></td>
                <!-- <td style="width: 220px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Total Shipping Cost</strong></td> -->
                <!-- <td style="width: 220px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Packaging Cost</strong></td>
                <td style="width: 220px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Security Charge </strong></td> -->
                <td style="width: 120px; text-align: right;text-transform: uppercase;font-size: 12px;"><strong>Sub Total</strong></td>
            </tr>
            

            <tr style="padding: 5px;border-bottom:1px solid #ebebeb;" v-if="order_products" v-for="(productData, index) in order_products" :key="index">
                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;">{{ index+1 }}</td>

                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;">  
                    {{ productData.product.title }}
                    <br/><b>SKU:</b> {{ productData.product_sku }}
                    <span  v-if="productData.product.product_type == 'variable'">
                        <br/>
                        <span style="margin-right:5px;" v-for="(vOption,key) in productData.product_options" :key="key"> <b>{{key}}</b> : {{vOption}}</span>
                    </span>
                </td>

                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;"> <span style="font-size: 12px;"> {{ productData.product_qty }}</span></td>
                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;"> <span style="font-size: 12px;">BDT {{ productData.price }}</span></td>
                <!-- <td style="width: 120px; text-align: left;font-size: 12px;">BDT {{ productData.shipping_cost }}</td> -->
                <!-- <td style="width: 120px; text-align: left;font-size: 12px;">BDT {{ productData.packaging_cost }}</td>
                <td style="width: 120px; text-align: left;font-size: 12px;">BDT {{ productData.security_charge }}</td> -->
                <td style="width: 120px; text-align: right;font-size: 12px;">BDT {{ productData.price*productData.product_qty }}</td>
            </tr>


            
        </tbody>
    </table>

    <table style="width: 650px;text-align:right; margin:50px auto">
        <tbody>
            <tr>
                <td style="width: 210px;">&nbsp;</td>
                <td style="width: 80px;">
                    <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Sub Total:&nbsp;</strong>BDT 
                        {{ single_order.global_subtotal }} 
                    </p>
          
                    <!-- <p v-if="single_order.grocery_shipping_cost > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">Grocery Shipping Cost:&nbsp;</strong>BDT  {{ single_order.grocery_shipping_cost }}</p> -->
                    <p v-if="single_order.shipping_cost > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">Total Shipping Cost(+):&nbsp;</strong>BDT  {{ single_order.shipping_cost }}</p>

                    <p v-if="single_order.total_packaging_cost > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">Packaging Cost(+):&nbsp;</strong>BDT  {{ single_order.total_packaging_cost }}</p>
                    <p v-if="single_order.total_security_charge > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">Security Charge(+):&nbsp;</strong>BDT  {{ single_order.total_security_charge }}</p>
                    <p v-if="single_order.vat > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">VAT(+):&nbsp;</strong>BDT  {{ single_order.vat }}</p>
                    <!-- <p style="margin: 1px;font-size: 12px;" v-if="single_order.discount_amount > 0"><strong style="float: left;">Discount(-) :&nbsp;</strong>BDT  {{ single_order.discount_amount }}</p> -->
                    <p v-if="single_order.coupon_amount > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">Coupon Discount(-):&nbsp;</strong>BDT  {{ single_order.coupon_amount }}</p>
                    <p v-if="single_order.voucher_amount > 0" style="margin: 1px;font-size: 12px;"><strong style="float: left;">Voucher Discount(-):&nbsp;</strong>BDT  {{ single_order.voucher_amount }}</p>

                    <span v-if="single_order.payment_method == 'cash_on_delivery'">
                        <span v-if="single_order.status == '6'">
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Paid:&nbsp;</strong>BDT {{ single_order.paid_amount }} </p>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Due:&nbsp;</strong>BDT 0.00 </p>
                        </span>
                        <span v-else>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Paid:&nbsp;</strong>BDT 0</p>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Due:&nbsp;</strong>BDT {{ single_order.total_amount }}</p>
                        </span>
                    </span>
                    <span v-else>
                        <span>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Paid:&nbsp;</strong>BDT {{ single_order.paid_amount }} </p>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Due:&nbsp;</strong>BDT {{ single_order.total_amount - single_order.paid_amount }} </p>
                        </span>

                    </span>




                    
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width: 650px;text-align:left; margin:50px auto">
        <tbody style="text-align: center;">
            <p style="font-size:12px;text-align: center;">Thank you for being with us. Stay connected with <b>mabiyshop.com</b> </p>
            <p style="text-align: center;"><img :src="barcode" alt=""  width="150px" height="30px"></p>
        </tbody>
    </table>
</section>


</div>
</template>


<script>
import Form from 'vform'
import axios from 'axios'
import swal from 'sweetalert';
import Leftsidebar from './default/Leftsidebar';
import moment from 'moment';
import pagination from 'laravel-vue-pagination';

export default {
	data(){
		return{
            dateTime:'',
            site_info:'',
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
            single_order:'',
            address:'',
            order_products:[],
            statuses:[],
            showCommentForm:1,
            purchased:true,
            rate:0,
            files: '',
            review_preview:[],
            barcode:'',
            auto_renewal:true,
            allow_status:'',
            orderLoadError:'',
		}
	},
	components:{
		Leftsidebar,
        pagination
	},
    computed:{
        formattedShippingAddress(){
            if(!this.address){
                return '';
            }

            const location = [];
            if(this.address.upazila && this.address.upazila.title){
                location.push(this.address.upazila.title);
            }
            if(this.address.district && this.address.district.title){
                location.push(this.address.district.title);
            }
            if(this.address.union && this.address.union.title){
                location.push(this.address.union.title);
            }

            const writtenAddress = (this.address.shipping_address || '').trim();
            if(location.length && writtenAddress){
                return location.join(', ') + ' — ' + writtenAddress;
            }
            return writtenAddress || location.join(', ');
        }
    },
	methods:{

        handleFilesUpload(e){
            const file = e.target.files
            this.files = file;
            let images = [];
            for (let [key, value] of Object.entries(file)) {
                images.push(URL.createObjectURL(value))
            }
            this.review_preview = images;
        },
        setAutoRenewal(){
            let formData = new FormData();
            let renewal_cycle  = $('.renewal_cycle').find('option:selected').val();
            let order_id = this.$route.params.id;
            if(renewal_cycle == 0){
                swal({
                    title: 'Please select order auto renewal cycle first!',
                    icon: "error",
                    timer: 3000
                });
            }


            formData.append('renewal_cycle',renewal_cycle);
            formData.append('order_id',order_id);

            let token = localStorage.getItem("token");
			
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}

            axios.post(this.$baseUrl+'/api/v1/order-auto-renewal', formData,axiosConfig).then(response => {
                if(response.data.status == 1){
                    swal({
                        title: response.data.message,
                        icon: "success",
                        timer: 3000
                    }).then(()=>{
                        $('.renewal_cycle').val('');
                        $('.close').trigger('click');
                        this.load_single_order();
                    });
                }else{
                    this.errors = response.data.message;
                }
            });
        },

        cancelAutoRenewal(){
            let formData = new FormData();
            let order_id = this.$route.params.id;
            formData.append('order_id',order_id);

            let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}

            axios.post(this.$baseUrl+'/api/v1/cancel-order-auto-renewal', formData,axiosConfig).then(response => {
                if(response.data.status == 1){
                    swal({
                        title: response.data.message,
                        icon: "success",
                        timer: 3000
                    });
                    $('.cancel-auto-renewal').hide();

                }else{
                    this.errors = response.data.message;
                }
            });
        },

        reveiewSubmit(){
            let that = this;
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer '+token
                }
            }

            let comment = $('.comment').val();
            let rate    = $('.rate').val();
            let formData = new FormData();
            let product_id = $('.product_id').val();
            let order_details_id = $('.order_details_id').val();
            let user_id = localStorage.getItem("user_id");
            for( var i = 0; i < this.files.length; i++ ){
                let file = this.files[i];
                formData.append('files[' + i + ']', file);
            }

            formData.append('user_id', user_id);
            formData.append('order_details_id', order_details_id);
            formData.append('product_id', product_id);
            formData.append('require_moderation', $('.require_moderation').val());
            
            formData.append('comment', comment);
            formData.append('rate', rate);
            if(rate < 1){
                swal({
                    title: "Please rate star ratings for this product.",
                    icon: "error",
                    timer: 3000
                });
            }else{ 
               
                axios.post(this.$baseUrl+'/api/v1/add-review', formData, axiosConfig).then(function(response){
                    swal({
                        title: "Thank you for your review.",
                        icon: "success",
                        timer: 3000
                    });
                    that.load_single_order();
                    // $('.rate_now_button').hide();
                    $('.close').trigger("click");
                }).catch(function(){
                    swal ( "Oops" ,  'Something went wrong',  "error" );
                });
            }
        },

        payAgain(order_id){
			let token = localStorage.getItem("token");
            let amount = jQuery('#payment_amount').val();
            if(amount){
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                axios.get(this.$baseUrl+'/api/v1/pay-again/'+order_id+'?amount='+amount, axiosConfig).then(response => {
                    
                    if(response.data.status == 302){
                        window.location.href = response.data.url;
                    }else{
                        swal ( "Oops" , response.data.message,  "error");
                    }
                });
            }else{
                swal ( "Oops" , 'Payment amount field is required!',  "error");
            }

		},

        rating($id){
				this.rate = $id;
		},
        imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },
        date_time(){
            this.dateTime = new Date().toLocaleString();
        },
        site_information(){
            axios.get(this.$baseUrl+'/api/v1/site-info').then(response => {
                this.site_info = response.data;
            });
        },
        printInvoice(){
            var divToPrint= jQuery('#print_invoice').html();
            var newWin=window.open('','Print-Window');
            newWin.document.open();
            newWin.document.write('<html><link href=""><body onload="window.print()">'+divToPrint+'</body></html>');
            newWin.document.close();
            setTimeout(function(){newWin.close();},100);

        },
		getUserDetails(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
			axios.get(this.$baseUrl+'/api/v1/get-user-details', axiosConfig).then(response =>{
				this.userData = response.data;
			}).catch(function(){
                // this.$router.push({name:'sign-up'});
			    swal ( "Oops" ,  'Something went wrong',  "error" );
			});
		},

		
        load_single_order(){
            this.orderLoadError = '';
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
            let order_id = this.$route.params.id;
            axios.get(this.$baseUrl + "/api/v1/get-single-order/"+order_id, axiosConfig).then((response) => {
                if (response.data.status == 0) {
                    this.single_order = '';
                    this.address = '';
                    this.order_products = [];
                    this.statuses = [];
                    this.orderLoadError = response.data.message || 'Order not found.';
                    return;
                }
                axios.get("/api/barcode/"+'MS'+moment(String(response.data.order.created_at)).format('YY')+response.data.order.id).then((response) => {
                        this.barcode = response.data
                });

                this.single_order = response.data.order;
                this.address = response.data.shipping_address;
                this.order_products = response.data.products;
                this.statuses = response.data.statuses;
                this.auto_renewal = response.data.order.auto_renewal;

            }).catch(() => {
                this.single_order = '';
                this.address = '';
                this.order_products = [];
                this.statuses = [];
                this.orderLoadError = 'Unable to load order details. Please try again.';
            });
        },

     
        downloadFile(productId,extension=null) {
            let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token,
                  'Cache-Control': 'no-cache'
			  }
			}
            let order_id = this.$route.params.id;
            axios({
                url: this.$baseUrl + "/api/v1/download-file/"+productId+'/'+order_id, 
                method: 'GET',
                responseType: 'blob',
                baseURL: '/',
                headers: { 'Cache-Control': 'no-cache' },
                axiosConfig
            }).then((response) => {
                let blob = new Blob([response.data]);
                let link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'dowonload.'+extension;
                if(blob.size == 1){
                    blob = '';
                    link = '';
                    link.href = '';
                    link.download = '';
                    response='';
                    window.URL.createObjectURL('');
                    swal ( "Oops" ,  'Something went wrong.',  "error" );
                }else{
                    link.click();
                    blob = '';
                    link = '';
                    link.href = '';
                    link.download = '';
                    response='';
                    window.URL.createObjectURL('');


                }

            });
        },
        


        completeOrder(order_details_id){
            let that = this;
            let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}

            axios.post(this.$baseUrl+'/api/v1/product-recieve-confirmation/'+order_details_id,{}, axiosConfig).then(function(response){
               if(response.data.status == 1){
                    swal({
                        title: "Thank you for your feedback. This product will be marked as successfully delivered to you.",
                        icon: "success",
                        timer: 3000
                    });

                    that.load_single_order();
               }else{
                swal ( "Oops" ,  'Something went wrong',  "error" );
               }
               
            }).catch(function(){
                swal ( "Oops" ,  'Something went wrong',  "error" );
            });
        },
    	scrollToTop(){
            window.scrollTo(0,0);
        }


	},

    watch:{
        $route(to, from){
            this.load_single_order();
            this.scrollToTop();
        }
    },
	mounted(){
        this.scrollToTop();
        this.date_time();
        this.site_information();
        this.getUserDetails();
        this.baseurl = this.$baseUrl;
        this.load_single_order();
        document.title = "MabiY Shop | My Order Details";  
	}
}
</script>
