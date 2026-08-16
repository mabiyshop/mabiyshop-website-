<template>
	
    <div>
		<meta>
		<span v-if="productfound">
		<div v-if="loading" id="product_details_area">
			
			<section id="product-details">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="bredcum">
								<p> {{ singleProduct.breadcrumb }} </p>
							</div>
						</div>
					</div>
					<div typeof="schema:Product" class="row details-container">
						<div class="col-md-8 image-section">
							<div class="row">
								<div class="col-md-6">
									
								
									<div class="mycontainer">
										<div v-if="singleProduct.video_link" id="show-video">
											<iframe v-if="videoLoaded" width="100%" height="400" :src="singleProduct.video_link"></iframe>
										</div>

										<div class="zoom-show" :href="baseurl+'/'+singleProduct.default_image">
											<img property="schema:image" @error="imageLoadError" :src="baseurl+'/'+singleProduct.default_image" id="show-img" style="object-fit: contain;"/>
										</div>
										<div class="small-img" >
											<img @error="imageLoadError" @click="loadVideoFromNavigation('prev')" src="/images/online_icon_right@2x.png" class="icon-left" alt="" id="prev-img" />
											<div class="small-container">
												<div id="small-img-roll">
													<img @error="imageLoadError" :src="baseurl+'/'+singleProduct.default_image" class="show-small-img" alt="" />
													<template v-if="gallery_images">
														<img
															v-for="(img, index) in gallery_images"
															:key="'Z' + index"
															:src="baseurl + '/' + img"
															class="show-small-img"
															alt=""
															@error="imageLoadError"
														/>
													</template>

													<img  v-if="singleProduct.video_link" :src="youtubeThumbnailUrl" class="show-small-img video" alt="video" @click="videoLoaded = true" />
													
												</div>
											</div>
											<img @error="imageLoadError" @click="loadVideoFromNavigation('next')" src="/images/online_icon_right@2x.png" class="icon-right" alt="" id="next-img" />
										</div>
									</div>


								</div>
								<div class="col-md-6 description-section">
									<h4 property="schema:name" class="mb-0">{{ singleProduct.title }}</h4>
									<small v-if="singleProduct.weight"> <b class="single_page_weight">weight: </b> {{ singleProduct.weight }} <span  v-if="singleProduct.weight_unit">{{ singleProduct.weight_unit }} </span> <br> </small>

									<small class="text-danger" v-if="singleProduct.vat > 0">{{ singleProduct.vat }}% VAT Applicable <br></small>

									 <small class="text-secondary"> <b>SKU: </b> <span class="single_page_sku"> <b v-if="singleProduct.sku" >{{ singleProduct.sku }}</b> </span>  </small>

									<p class="product_short_description"><small>{{ singleProduct.short_description }}</small></p>
									<div class="star-rating title_rating">
									<span :style="AllRating.average_percentage">  </span> 
									</div> 
									<span> ({{AllRating.total_rate > 0 ?AllRating.total_rate:0}}) <!-- {{ $t('Star Rating') }}(s) --> </span>

									<p class="text-danger" v-if="singleProduct.minimum_cart_value > 0"><small>Note: You need minimum BDT {{ singleProduct.minimum_cart_value }} in your cart to buy this item.</small></p>
									
									
									<div class="share_section">	
									<ul class="share_list">
										<li>
											<div class="share_parent"> 
												<i class="fa fa-share-alt" aria-hidden="true"></i> 
												<div class="share_box">
													<ul>
														<li> <a :href="`https://www.facebook.com/sharer/sharer.php?u=`+ecodedUrl" target="_blank"> <i class="fa fa-facebook" aria-hidden="true"></i> <span>{{ $t('Share on Facebook') }}</span>  </a> </li>
														<li> <a :href="`https://twitter.com/share?url=`+ecodedUrl"> <i class="fa fa-twitter" aria-hidden="true"></i>  <span> {{ $t('Share on Twitter') }}  </span></a> </li>
														<li> <a :href="`mailto:?subject=Favourite&body=`+ecodedUrl" onClick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" title="Share on Mail">   <i class="fa fa-envelope-o" aria-hidden="true"></i> <span> {{ $t('Share via Email') }} </span> </a> </li>
														<li> <a :href="`whatsapp://send?text=`+ecodedUrl" data-action="share/whatsapp/share" onClick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;" target="_blank" title="Share on whatsapp"> <i  class="fa fa-whatsapp" aria-hidden="true"></i> <span>{{ $t('Share on Whatsapp') }}  </span></a> </li>
													</ul>
												</div>
											</div>
										</li>
										<li><i class="fa fa-heart" @click="addToWishlist(singleProduct.id)" aria-hidden="true"></i></li>
										<li>
											<span title="Add To Compare" class="compare" @click="addToCompare(singleProduct.id)"> <i class="fa fa-retweet" aria-hidden="true"></i></span>
										</li>
									</ul>
									</div>
									

									
									<!-- Simple product start -->
									<span v-if="singleProduct.product_type == 'simple'">
										<div typeof="schema:Offer"  class="single-page-price">

											<div property="schema:price" class="currect-price"><b>BDT {{  singleProduct.price_after_offer }}</b></div>
											<span v-if="singleProduct.special_price_type == 1">
												<div class="single-old-price" v-if="parseInt(singleProduct.price_after_offer)  < parseInt(singleProduct.price)">
													<del>BDT {{ singleProduct.price }}</del>
												</div>
											</span>

											<span v-if="singleProduct.special_price_type == 2">
												<div class="single-old-price" v-if="parseInt(singleProduct.price_after_offer)  < parseInt(singleProduct.price)" >
												
													<del>BDT {{ singleProduct.price }}</del> 
													<br>  <small>Discount: </small> <small>  {{ discount_Percentage }}%</small></div>
											</span>
										</div>


										<div class="row">
											<div class="col-md-12 quantity_grid" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
												<ul class="quantity">
													<li>{{ $t('Quantity') }}:</li>
												</ul>
												<ul class="quantity_calculate">
													<li><button class="minus">-</button></li>
													<li><button class="qty" data-qty='1' :data-total_qty="singleProduct.qty" :data-productId="singleProduct.id">1</button></li>
													<li><button class="plus">+</button></li>
												</ul>
												<!-- <h1 class="btn btn-primary clickMe">Click Me</h1> -->
											</div>
										</div>
										<div class="row add_buy" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
											<div class="col-md-6">
												<button :id="'buyNowBtn'" :class="'mb-1 btn buy_now single_product_buynow_button buynowdisabledbtn'+singleProduct.id"  @click="buyNow(singleProduct.id)"> <i class="fa fa-shopping-basket"></i> {{ $t('Buy Now') }}</button>
												<button :id="'addToCartBtn'" :class="'btn btn-primary addToCart single_product_addtocart_button disabledbtn'+singleProduct.id"  @click="addToCart(singleProduct.id)"> <i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }}</button>
												
											</div>
										</div>
										<div class="row out_of_stock_single_page" v-else>
											<div class="col-12 col-lg-12">
												<div class="out_of_stock"> {{ $t('Out Of Stock') }} </div>
											</div>
											<div class="col-12 col-lg-12">
												<div :class="'restockbtn btn btn-primary disabledbtn'+singleProduct.id"  @click="restockRequest(singleProduct.id, singleProduct.seller_id)"> <i class="fa fa-bicycle mr-2" aria-hidden="true"></i> {{ $t('Restock Request') }}</div>
											</div>
											
										</div>
									</span>
									<!-- Simple product end -->
									
									
				
									<!-- variable product start -->
									<div v-if="singleProduct.product_type == 'variable'" id="variable_product">
									<form ref="variable_form" id="variable_form" @submit.prevent="variableAddToCart(singleProduct.id)" >
										<div typeof="schema:Offer"  class="single-page-price">
											<div property="schema:price" class="calculated_price"  :data-calculated-price="Number(calculated_price)" :data-discount-available="singleProduct.discount_available" :data-single-old-price="singleProduct.price" :data-discount-type="singleProduct.special_price_type" :data-discount-price="singleProduct.special_price"><b>BDT <span class="price_text" >{{ Number(calculated_price) }}</span></b></div>
											<div class="single-old-price"  v-if="calculated_price < singleProduct.price" ><del>BDT  {{ singleProduct.price }}</del> <span v-if="discount_Percentage" class="discout_percentage">{{ discount_Percentage }}%</span></div>
											<input type="hidden" class="variable_sku" name="variable_sku"></input>
										</div>
										<div class="row">
											<div class="col-sm-6 productSize">
												<span v-for="(metas, index) in singleProduct.meta" :key="'A'+index"> 
													<span v-if="metas.meta_key == 'custom_options'">
													
														<span v-for="(val, index) in metas.meta_value" :key="'B'+index"> 
															
															<div v-if="singleProduct.hidden_option[val.title]" class="row">
																<!--Radio-->
																<div v-if="val.type == 'radio'" class="col-md-12 col-lg-12 mb-3 option_parent_group">
																		
																		<div class="variant_title"> 
																			<b>{{ val.title }} <b v-if="val.is_required == '1'" class="is_required">*</b>: </b>
																			<span class="selectedValue"></span>
																			<input type="hidden" data-additional-price="0" data-additional-qty="-1" class="variant_input" :name="val.title">
																		</div>

																		<ul>
																			<li v-for="(option, index) in val.value" :key="'C'+index" > 
																				
																				<div class="radio_image">
																					<span v-if="option.variant_image">
																						<img @error="imageLoadError" :title="'Additional Price BDT '+ option.price" class="color_image option_selector" :src="baseurl+'/'+option.variant_image" :data-price="option.price" 
																						:data-variable-sku="option.sku" 
																						:data-variable-qty="singleProduct.qty" 
																						:data-title="option.title" 
																						:data-sku="option.sku"/>
																					</span>
																					<span 
																					:data-price="option.price" 
																					:data-variable-sku="option.sku" 
																					:data-variable-qty="singleProduct.qty"  
																					:data-title="option.title" 
																					:title="'Additional Price BDT '+ option.price" class="option_selector radio_select" v-else>
																						{{ option.title }}  <span class="radio_select_price"> (+BDT {{ option.price }}) </span>
																					</span>
																				</div>
																				
																			</li>
																		</ul>
																</div>
																<!--Dropdown-->
																<div v-if="val.type == 'dropdown'" class="col-md-12 col-lg-12 mb-3 option_parent_group">
																	<div class="variant_title"> 
																		<b>{{ val.title }}: </b> 
																		<span class="selectedValue"></span>
																		<input type="hidden" data-additional-price="0" data-additional-qty="-1" class="variant_input" :name="val.title">
																	</div>
																	
																	
																	<select class="form-control variant_dropdwon">
																		<option value="0" disabled selected>-Select {{val.title}}-</option>
																		
																		<option v-for="(option, index) in val.value" 
																			:value="option.title" 
																			:data-variable-qty="singleProduct.qty"
																			:data-variable-sku="option.sku"
																			:data-dropdownprice="option.price" 
																			:data-src="baseurl+'/'+option.variant_image"
																			:key="'D'+index">
																			{{ option.title }} 
																		</option>
																	</select>
																</div>
															</div>


														</span>
													</span>
												</span>
											</div>
											<!--Quantity-->
											<div class="col-sm-6 quantity_grid" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
												<ul class="quantity">
													<li>{{ $t('Quantity') }}:</li>
												</ul>
												<ul class="quantity_calculate">
													<li><button type="button" class="minus">-</button></li>
													<li>
														<button type="button" class="qty" data-qty='1' :data-total_qty="10">1</button>
														<input type="hidden" name="qty" class="qtyInput" value="1">
													</li>
													<li><button type="button" class="variable_plus">+</button></li>
												</ul>
											</div>
											<!-- <div class="row out_of_stock_single_page out_of_stock_digi_vari" v-else>
												<span class="out_of_stock">Out Of Stock</span>
											</div> -->


											<div class="row out_of_stock_single_page out_of_stock_digi_vari" v-else>
												<div class="col-12 col-lg-12">
													<div class="out_of_stock"> {{ $t('Out Of Stock') }} </div>
												</div>
												<div class="col-12 col-lg-12">
													<div :class="'restockbtn btn btn-primary disabledbtn'+singleProduct.id"  @click="restockRequest(singleProduct.id, singleProduct.seller_id)"> <i class="fa fa-bicycle mr-2" aria-hidden="true"></i> {{ $t('Restock Request') }}</div>
												</div>
											</div>





											<!--Quantity-->
										</div>
										<div class="row add_buy add_buy_variable" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
											<div class="col-md-6">
												<input type="hidden" name="product_id" :value="singleProduct.id">
												<button type="button" :id="'addToCartBtn'" :class="'btn btn-primary variable_disabled_btn single_product_addtocart_button addToCart disabledbtn'+singleProduct.id" disabled> <i class="fa fa-shopping-basket"></i> {{ $t('Buy Now') }}</button>
												<!-- <button type="button" :id="'buyNowBtn'" :class="'btn btn-primary buy_now variable_disabled_btn buyNowBtn buynowdisabledbtn'+singleProduct.id" disabled> <i class="fa fa-shopping-basket"></i> {{ $t('Buy Now') }}</button> -->
											</div>
										</div>
									

										</form>
									</div>
									<!-- variable product end -->






									
									<!-- Digital nad Service product start -->
									<span v-if="singleProduct.product_type == 'digital' || singleProduct.product_type == 'service'" class="digital_product">
										<div class="single-page-price">
											<div class="currect-price"><b>BDT {{ calculated_price }}</b></div>
											<span v-if="singleProduct.special_price_type == 1">
												<div class="single-old-price" v-if="calculated_price < singleProduct.price">
													<del>BDT {{ singleProduct.price }}</del>
												</div>
											</span>

											<span v-if="singleProduct.special_price_type == 2">
												<div class="single-old-price" v-if="calculated_price < singleProduct.price">
													<del>BDT {{ singleProduct.price }}</del> <br>  
													<small>Discount: </small>
													<small>  {{ discount_Percentage }}%</small>
												</div>
											</span>
										</div>

										
									<span v-if="singleProduct.product_type == 'digital'">
										<span v-for="(meta,index) in singleProduct.meta" :key="'E'+index">
												<span v-if="meta.meta_key == 'product_sale_option'">
													<span v-if="meta.meta_value == 'digital'">
															<div class="row">
																<div class="col-md-12">
																	<div class="form-group">
																		<label for="">{{ $t('Mobile number') }}</label>
																		<input type="text" name="number" :placeholder="$t('Mobile number')+'..'" class="form-control phone_number">
																	</div>
																</div>
															</div>
													</span>
												</span>
												<span v-else>
													<input type="hidden" name="number" value="-1" class="form-control phone_number">
												</span>
										</span>
									</span>


									<span v-else>
										<label for="">{{ $t('When do you want to take service from us?') }}</label>
										<p class="mb-0">{{$t('Select your prefer date')}} <span class="text-danger">*</span> </p>
										<input type="date" name="service_date" id="service_date" class="form-control">
										<input type="hidden" name="number" value="-1" class="form-control phone_number">
										<p class="mb-0 mt-2">{{$t('Select your prefer time, expert will arrive by your selected time')}} <span class="text-danger">*</span></p>
										<select name="service_time" id="service_time" class="form-control">
											<option value="10-11am">10-11 am</option>
											<option value="11-12pm">11-12 pm</option>
											<option value="12-1pm">12-1 pm</option>
											<option value="1-2pm">1-2 pm</option>
											<option value="2-3pm">2-3 pm</option>
											<option value="3-4pm">3-4 pm</option>
											<option value="4-5pm">4-5 pm</option>
											<option value="5-6pm">5-6 pm</option>
											<option value="6-7pm">6-7 pm</option>
											<option value="7-8pm">7-8 pm</option>
										</select>
									</span>
										

										<div class="row">
											<div class="col-md-12 quantity_grid" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
												<ul class="quantity">
													<li>{{ $t('Quantity') }}:</li>
												</ul>
												<ul class="quantity_calculate">
													<li><button class="minus">-</button></li>
													<li><button class="qty digital_product_qty" data-qty='1' :data-total_qty="singleProduct.qty" :data-productId="singleProduct.id">1</button></li>
													<li><button class="plus">+</button></li>
												</ul>
											</div>
											<!-- <div class="row out_of_stock_single_page out_of_stock_digi_vari" v-else>
												<span class="out_of_stock"> {{ $t('Out Of Stock') }} </span>
											</div> -->

											<div class="row out_of_stock_single_page out_of_stock_digi_vari" v-else>
												<div class="col-12 col-lg-12">
													<div class="out_of_stock"> {{ $t('Out Of Stock') }} </div>
												</div>
												<div class="col-12 col-lg-12">
													<div :class="'restockbtn btn btn-primary disabledbtn'+singleProduct.id" @click="restockRequest(singleProduct.id, singleProduct.seller_id)"> <i class="fa fa-bicycle mr-2" aria-hidden="true"></i> {{ $t('Restock Request') }}</div>
												</div>
											</div>
										</div>


										<div class="row add_buy" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
											<div class="col-md-6">
												<button  v-if="singleProduct.product_type == 'digital'" :id="'addToCartBtn'" :class="'btn btn-primary addToCart disabledbtn'+singleProduct.id" @click.prevent="digitaladdToCart(singleProduct.id)"> <i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }} </button>
												<button v-else :id="'addToCartBtn'" :class="'btn btn-primary addToCart disabledbtn'+singleProduct.id" @click.prevent="digitaladdToCart(singleProduct.id,'service')"> <i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }} </button>
											</div>
										</div>
									</span>
									<!-- Digital product end -->

									<p class="product_short_description_on_mobile"><small>{{ singleProduct.short_description }}</small></p>
								</div>
							</div>
						</div>
						<div class="col-md-4 pt-2 delivery">
	
							<div class="row">
								<div class="col-md-12"><p>{{ $t('Delivery Option') }}</p></div>
							</div>

					

							<div v-if="logged_in_user_address != 0" class="row deliveryOption">
								
								
								<div class="col-md-12">
									<div class="row" v-for="(address,index) in logged_in_user_address" :key="'F'+index" v-if="logged_in_user.default_address_id == address.id" >
										<div class="col-1 col-sm-1 col-md-1"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
										<div class="col-8 col-sm-8 col-md-8" id="dynamicAddress"> 
											<span v-if="address.division">{{address.division.title}}, </span> 
											<span v-if="address.district">{{address.district.title}}, </span>
											<span v-if="address.upazila">{{address.upazila.title}}, </span>
											<span v-if="address.union">{{address.union.title}}, </span>
											<span v-if="address.shipping_address">{{address.shipping_address}}</span>

											<!-- {{  address.division.title +', '+address.district.title+', '+address.upazila.title+', '+ address.union.title+', '+address.shipping_address }} -->
										</div>
										<div class="col-3 col-sm-3 col-md-3 text-right"><a href="javascript:void(0)" class="change_location"> <i class="fa fa-pencil-square-o"></i> {{ $t('Change') }}</a></div>
									</div>
								</div>
								

								<div class="col-md-12" id="sOption">
									<div class="options">
										<div class="row">
											<div class="col-md-6">
													<div class="form-group">
														<label for="">{{ $t('Division') }}<span style="color:#f00">*</span></label>
															<select  @change.prevent="getDistrict()" name="division" id="division" class="form-control" required>
																	<option disabled selected>--Select Division--</option>
																	<option value="68" >Dhaka</option>
																	<option value="36">Chattogram</option>
																	<option value="60">Rajshahi</option>
																	<option value="65">Khulna</option>
																	<option value="66">Barishal</option>
																	<option value="67">Sylhet</option>
																	<option value="69">Rangpur</option>
																	<option value="6175">Mymensingh</option>
															</select>
													</div>
											</div>
											<div class="col-md-6">
													<div class="form-group">
														<label for="">{{ $t('District') }}<span style="color:#f00">*</span></label>
															<select  @change.prevent="getUpazila()" name="district" id="district" class="form-control" required>
																	<option disabled selected>--Select District--</option>
																	<option data-removeable="true" v-for="(district,index) in districts" :key="'G'+index" :value="district.id">{{district.title}}</option>
															</select>
													</div>
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
													<div class="form-group">
														<label for="">{{ $t('Upazila / Thana') }}<span style="color:#f00">*</span></label>
															<select  @change.prevent="getUnion()" name="upazila" id="upazila" class="form-control" required>
																	<option disabled selected>--Select Upazila--</option>
																	<option data-removeable="true" v-for="(upazila,index) in upazilas" :key="'H'+index" :value="upazila.id">{{upazila.title}}</option>
															</select>
													</div>
											</div>
											<div class="col-md-6">
													<div class="form-group">
														<label for="">{{ $t('Union / Area') }}<span style="color:#f00">*</span></label>
															<select name="union" id="union" class="form-control" required>
																	<option disabled selected>--Select Union--</option>
																	<option data-removeable="true" v-for="(union,index) in unions" :key="'J'+index" :value="union.id">{{union.title}}</option>
															</select>
													</div>
											</div>

										</div>
										<p class="text-right"><a @click="checkShippingRate()" href="javascript:void(0)" class="btn btn-dark">{{ $t('Check Shipping Rate') }}</a></p>
									</div>
								</div>
							</div>


							<div v-if="logged_in_user_address">
								<span v-if="singleProduct.product_type != 'digital'">
								<div class="row deliveryOption cashOndelivery">
									<div class="col-1 col-sm-1 col-md-1"><i class="fa fa-truck" aria-hidden="true"></i></div>
									<div class="col-8 col-sm-8 col-md-8"  v-for="(address,index) in logged_in_user_address" :key="'k'+index" v-if="logged_in_user.default_address_id == address.id">
										<span class="slectedShipping"> <span class="selectedShippingTitle text-capitalize">Standard Shipping</span> </span> 
										<p v-if="address.division.title == 'Dhaka'"><small  class="selectedShippingSubtitle">{{ $t('Package will be send within 24 - 48 hours') }}</small></p> 
										<p v-else><small  class="selectedShippingSubtitle">{{ $t('Package will be send within 48 to 72 hours') }}</small></p> 
										<!-- <p v-if="index == 'express_shipping'"><small  class="selectedShippingSubtitle">{{ $t('Package will be send within 1 to 3 days') }}</small></p>  -->
									</div>
									<!-- <div class="col-3 col-sm-3 col-md-3 text-right">
										<span v-if="index == 'free_shipping'"> BDT 0</span>
										<span v-else class="standardShippingConst">
											<span v-if="sp != 0">BDT {{sp}}</span>
											<span class="text-danger" v-else>{{ $t('Not Available') }}</span>
										</span>
									</div> -->
								</div>
								<div class="row deliveryOption cashOndelivery">
									<div class="col-1 col-sm-1 col-md-1"><i class="fa fa-bangladeshi-taka-sign">৳</i></div>
									<div class="col-8 col-sm-8 col-md-8">
										<span class="slectedShipping"> <span class="selectedShippingTitle text-capitalize">Secure Payment</span> </span> 
										<p ><small class="selectedShippingSubtitle">{{ $t('Cash on delivery and Online payment available') }}</small></p>
									</div>
									
								</div>
								</span>
							</div>


							

							<span v-if="singleProduct.meta != ''">
								<span v-for="(metas, index) in singleProduct.meta" :key="'L'+index">
									<span v-if="metas.meta_key == 'product_miscellaneous_information'">
										<span v-if="metas.meta_value.allow_cash_on_delivery">
											<div class="row deliveryOption cashOndelivery">
												<div class="col-1 col-sm-1 col-md-1"><i class="fa fa-money" aria-hidden="true"></i></div>
												<div class="col-8 col-sm-8 col-md-8">
													<span v-if="metas.meta_value.allow_cash_on_delivery == 'on'"> {{ $t('Cash On Delivery Available') }}</span>
													<span v-else> {{ $t('Cash On Delivery Not Available') }}</span>
												</div>
												<div class="col-3 col-sm-3 col-md-3 text-right"><span></span></div>
											</div>
										</span>
									</span>
								</span>
							</span>


							<span v-if="singleProduct.meta != ''">
								<span v-for="(metas, index) in singleProduct.meta" :key="'M'+index">
									<span v-if="metas.meta_key == 'product_miscellaneous_information'">
										<span v-if="metas.meta_value.allow_change_of_mind">
											<div class="row deliveryOption returnAndWarranty">
												<div class="col-12 col-sm-12 col-md-12"><p>{{ $t('Return & Warranty') }}</p></div>
												<div class="col-1 col-sm-1 col-md-1"><i class="fa fa-refresh" aria-hidden="true"></i></div>
												<div class="col-11 col-sm-11 col-md-11">
													{{ $t('7 Days Return') }} <br />
													<span v-if="metas.meta_value.allow_change_of_mind == 'on'"> <small>{{ $t('Change of mind is applicable') }}</small></span>
													<span v-else> <small>{{ $t('Change of mind is not applicable') }}</small> </span>
												</div>
											</div>
										</span>
									</span>
								</span>
							</span>




							<span v-if="singleProduct.meta != ''">
								<span v-for="(metas, index) in singleProduct.meta" :key="'N'+index">
									<span v-if="metas.meta_key == 'product_miscellaneous_information'">
										<span v-if="metas.meta_value.warrenty_period">
											<div class="row deliveryOption returnAndWarranty">
												
												<div class="col-1 col-sm-1 col-md-1">
													<i v-if="metas.meta_value.warrenty_period > 0" class="fa fa-check" aria-hidden="true"></i>
													<i v-else class="fa fa-times" aria-hidden="true"></i>
												</div>

												<div class="col-11 col-sm-11 col-md-11">
													<span v-if="metas.meta_value.warrenty_period > 0"> {{ $t('Warranty Available') }} </span>
													<span v-else>{{ $t('Warranty Not Available') }}</span>
												</div>
											</div>
										</span>
									</span>
								</span>
							</span>






							<div class="row deliveryOption returnAndWarranty">
								<div class="col-6 col-sm-6 col-md-8">
									<p class="m-0"> {{ $t('Shop Name') }} </p>
									<h6> <router-link :to="{ name: 'shop', params: {slug: singleProduct.shop_slug } }">{{ singleProduct.shop_name}}</router-link> </h6>
									
									<div v-if="if_veryfied" class="flagship_relative"><span> <img @error="imageLoadError" :src="baseurl+'/'+singleProduct.shop_verified.veryfied_banner" alt="Flagship"> </span></div>
									
									
								</div>
								<div class="col-6 col-sm-6 col-md-4 text-right">
								<!--  <span :data-product-title="singleProduct.title" :data-product-image="baseurl+'/'+singleProduct.default_image" :data-seller-id="singleProduct.seller_id" :data-seller-name="singleProduct.shop_name" class="chating-box-nav chating-box-product"><i class="fa fa-commenting-o" aria-hidden="true"></i>  {{ $t('Chat Now') }}</span>-->
								</div>
							</div>
							<div class="row rating text-center">
								<div class="col-4 col-sm-4 col-md-4">
									<small>{{ $t('Seller Ratings') }}</small>
									<br />
									<b v-if="seller_ratings_info.seller_ratings">{{ seller_ratings_info.seller_ratings }}%</b>
									<b v-else>0%</b>
								</div>
								<div class="col-4 col-sm-4 col-md-4">
									<small>{{ $t('Shipped on Time') }}</small>
									<br />
									<!-- <b v-if="seller_ratings_info.shipped_on_time">{{ seller_ratings_info.shipped_on_time }}%</b> -->
									<!-- <b v-else>100%</b> -->
									<b>100%</b>
								</div>
								<!-- <div class="col-4 col-sm-4 col-md-4">
									<small>{{ $t('Chat Response Rate') }}</small>
									<br />
									<b v-if="seller_ratings_info.chat_response_rate">{{ seller_ratings_info.chat_response_rate }}%</b>
									<b v-else>0%</b>
								</div> -->
							</div>

							<div class="row rating text-center go-to-store">
								<div class="col-md-12">
									<span>  <router-link :to="{ name: 'shop', params: {slug: singleProduct.shop_slug } }"> {{ $t('Go To Store') }} </router-link> </span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

		


			<!-- Tab Section Start -->
			<section id="product-details-description">
				<div class="container">
					<div class="col-md-12 product-details-descripiton">
						<div class="row">
							<div class="col-md-12">
								<ul class="nav nav-tabs" id="myTab" role="tablist">
									<li class="nav-item" role="presentation">
										<a class="nav-link active" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true"> {{ $t('Product Description') }} </a>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link" data-toggle="tab" href="#specification" role="tab" aria-controls="specification" aria-selected="false"> {{ $t('Specification') }}</a>
									</li>

									<span v-for="(metas, index) in singleProduct.meta" :key="'O'+index"> 
									<span v-if="metas.meta_key == 'tab_option'">
									<span v-for="(val, index) in metas.meta_value" :key="'P'+index"> 
										<li class="dynamic_nav_item" role="presentation">
											<a class="dynamic_nav_link" :data-id="index" data-toggle="tab"> {{ val.tab_option_title }}</a>
										</li>
									</span>
									</span>
									</span>


								</ul>
								<div class="tab-content" id="myTabContent">
									<div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description">
										<div v-html="singleProduct.description"></div>
									</div>


									<div class="tab-pane fade" id="specification" role="tabpanel" aria-labelledby="specification">
										<span v-for="(sp, index) in singleProduct.specification" :key="'Q'+index">
											<p v-if="sp.meta_value"> 
												<strong class="text-capitalize">{{ sp.meta_key.replace(/_/g, " ") }}</strong> : {{sp.meta_value}} 
											</p> 
										</span>
									</div>

									<span v-for="(metas, index) in singleProduct.meta" :key="'R'+index"> 
									<span v-if="metas.meta_key == 'tab_option'">
									<span v-for="(val, index) in metas.meta_value" :key="'S'+index"> 
										<div :class="'all_item dynamic_section_'+index" role="tabpanel">
											<p>{{ val.tab_option_description }}</p>
										</div>
									</span>
									</span>
									</span>


								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- Tab Section End -->


			<section id="product-rating">
				<div class="container ">
				<div class="row signle_page_aling">
				<div class="col-md-12 product-rating-content">
					<div class="row rating-section">
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-12">
									<h4> {{ $t('Ratings And Reviews') }} </h4>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<b> {{ AllRating.average > 0 ? AllRating.average : 0 }}/5</b>
									<ul>
										<div class="star-rating">
										<span :style="AllRating.average_percentage"></span>
										</div>
									</ul>
									<p>{{AllRating.total_rate > 0 ?AllRating.total_rate:0 }} {{ $t('Star Rating') }}(s)</p>
								</div>
								<div class="col-md-3">
									<div class="all-star">
										<ul>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
										</ul>
									</div>
									<div class="all-star">
										<ul>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
										</ul>
									</div>
									<div class="all-star">
										<ul>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
										</ul>
									</div>
									<div class="all-star">
										<ul>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
										</ul>
									</div>
									<div class="all-star">
										<ul>
											<li ><i class="fa fa-star" aria-hidden="true"></i></li>
											
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
											<li><i class="fa fa-star-o" aria-hidden="true"></i></li>
										</ul>
									</div>
								</div>
								<div class="col-md-5 progres-section">
									<div class="progress">
										<div class="progress-bar" :style="AllRating.five_star_percentage"></div>
									</div>
									<span>{{AllRating.five_star }}</span>
									
									<div class="progress">
										<div class="progress-bar" :style="AllRating.four_star_percentage"></div>
									</div>
									<span>{{AllRating.four_star }}</span>
									
									<div class="progress">
										<div class="progress-bar" :style="AllRating.three_star_percentage"></div>
									</div>
									<span>{{AllRating.three_star }}</span>
									
									<div class="progress">
										<div class="progress-bar" :style="AllRating.two_star_percentage"></div>
									</div>
									<span>{{AllRating.two_star }}</span>
									<div class="progress">
										<div class="progress-bar" :style="AllRating.one_star_percentage"></div>
									</div>
									<span>{{AllRating.one_star }}</span>
								</div>
							</div>
						</div>
						<div class="col-md-4"></div>
					</div>
					<div v-if="Total_Comments > 0" class="row review-section">
						<div class="col-md-12">
							<h4>{{ $t('Product Reviews') }}</h4>
						</div>
						<div class="col-md-12">
						
							<!--Single comment start-->
							<span v-if="SingleReview.data">
							<div v-for="(review, index) in SingleReview.data" :key="'T'+index" class="review-box">
								<div class="row">
									<div class="col-md-12">
										<div class="all-star">
											<ul>
												<li v-if="review.rate > 0"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
												<li v-if="review.rate > 1"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
												<li v-if="review.rate > 2"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>											
												
												<li v-if="review.rate > 3"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
												<li v-if="review.rate > 4"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
											</ul>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="all-star">
											<ul class="review_user_info">
												<li><b> {{ $t('Reviwed By') }}: </b></li>
												<li> <span> {{ review.user_name }} </span> </li>
												<li> <span class="verified"> <img @error="imageLoadError" :src="baseurl+'/uploads/verify.png'"> {{ $t('Verified Purchase') }}</span></li>
											</ul>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<p>{{ review.comment_text }}</p>
									</div>
								</div>
								<div class="row review_image">
									<div v-for="(img, index) in review.image" :key="'U'+index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img != 'no-image'"> <img @error="imageLoadError" :src="baseurl+'/'+img"> </span> </div>
								</div>

								<!-- repl box start -->
								<div v-if="review.replay.length > 0" class="row replay_box">
									<div v-for="(replay, index) in review.replay" :key="'V'+index" class="review-box replay_box_review">
										<div class="row">
											<div class="col-md-12">
												<div class="all-star">
													<ul class="review_user_info">
														<li><b>{{ $t('Replied By') }}: </b></li>
														<li> <span> {{ replay.user_name }} </span> </li>
														<!-- <li> <span class="verified"> <img @error="imageLoadError" :src="baseurl+'/uploads/verify.png'"> Verified Purchase</span></li> -->
													</ul>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<p>{{ replay.comment_text }}</p>
											</div>
										</div>
										<div class="row review_image">
											<div v-for="(img, index) in replay.image" :key="'W'+index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img != 'no-image'"> <img @error="imageLoadError" :src="baseurl+'/'+img"> </span> </div>
										</div>
									</div>
								</div>
								<!-- repl box end -->

							</div>


							
							<div class="row">
								<div class="col-md-12">
									<pagination :data="SingleReview" @pagination-change-page="getComments"></pagination>
								</div>
							</div>
							</span>
							
							
							<span v-else-if="temporaryComment">
							<div v-for="(review, index) in temporaryData" :key="'X'+index" class="review-box">
								<div class="row">
									<div class="col-md-12">
										<div class="all-star">
											<ul>
												<li v-if="review.rate > 0"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
												<li v-if="review.rate > 1"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
												<li v-if="review.rate > 2"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>											
												
												<li v-if="review.rate > 3"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
												<li v-if="review.rate > 4"><i class="fa fa-star" aria-hidden="true"></i></li>
												<li v-else><i class="fa fa-star-o" aria-hidden="true"></i></li>
												
											</ul>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="all-star">
											<ul class="review_user_info">
												<li><b>{{ $t('Reviwed By') }}: </b></li>
												<li> <span> {{ review.user_name }} </span> </li>
												<li> <span class="verified"> <img @error="imageLoadError" :src="baseurl+'/uploads/verify.png'"> {{ $t('Verified') }}</span></li>
											</ul>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<p>{{ review.comment_text }}</p>
									</div>
								</div>
								<div class="row review_image">
									<div v-for="(img, index) in review.image" :key="'Y'+index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img"> {{ img }} <img @error="imageLoadError" :src="baseurl+'/'+img"> </span> </div>
								</div>
							</div>
							</span>
							<!--Single comment end-->
						</div>
					</div>
				</div>
				</div>
				</div>
			</section>
			
			
			
			<!--Similar product start--> 
			<section class="products_shop margin_bottom_custom" v-if="product_simillar_status == 1">
			<div class="product-main">
				<div class="container">
				<div class="row">
					<div class="col-md-12">
					<div class="row">
					<div class="col-md-6 col-lg-6">
						<div class="title">
						<h3> {{ $t('Simillar Products') }}</h3>
						</div>
					</div>
					</div>

					<div class="swiper similerSwiper">
						<div class="swiper-wrapper">
							<div class="swiper-slide" data-swiper-autoplay="5000" v-for="(data, index) in product_simillar" :key="'XX'+index" :data-length="index">
								<ProductGrid :data="data" ></ProductGrid>
							</div>
						</div>
						<div class="swiper-pagination"></div>
					</div> 

					<div v-for="(data, index) in product_simillar" :key="'YY'+index" :data-length="index">
						<!-- <QuickView :data="data" ></QuickView> -->
					</div>
						
					</div>
				</div>
				</div>
			</div>
			</section>
			<!--similar product end-->
		</div>

		<div v-else class="container single_page_loading">
			<section id="product-details">
				<div id="only_shimmer">
					<div class="row details-container">
						<div class="col-md-8 image-section">
							<div class="row">
								<div class="col-md-6">
									<div class="row w_100per padding_problem">
										<div class="shimmer width_problem"> 
											<div class="h_40 w_100per"></div>
										</div>
									</div>
									
									<div class="small-img">
										<div class="shimmer"> 
											<div class="h_6 w_5 ml_5"></div>
											<div class="h_6 w_5 ml_5"></div>
											<div class="h_6 w_5 ml_5"></div>
											<div class="h_6 w_5 ml_5"></div>
										</div>
									</div>
								</div>
								<div class="col-md-6 description-section">
									<div class="shimmer"><div class="h_3 w_100per"></div></div>
									<div class="shimmer"><div class="h_3 w_10"></div></div>
									<div class="share_section share_section_shimmer">	
										<ul class="share_list">
											<li>
												<div class="shimmer"><div class="h_3 w_3"></div></div>
											</li>
											<li>
												<div class="shimmer"><div class="h_3 w_3"></div></div>
											</li>
										</ul>
									</div>
									<div class="single-page-price">
										<div class="shimmer"><div class="h_4 w_10"></div></div>
									</div>
									<!-- Simple product start -->
									<div class="row">
										<div class="col-md-12 quantity_grid">
											<ul class="quantity">
												<li><div class="shimmer"><div class="h_3 w_5 mt_5"></div></div> :</li>
											</ul>
											<ul class="quantity_calculate">
												<li><div class="shimmer"><div class="h_4 w_10"></div></div> :</li>
											</ul>
										</div>
									</div>
									<div class="row add_buy" v-if="singleProduct.in_stock > 0 && singleProduct.qty > 0">
										<div class="col-md-6">
											<div class="shimmer"><div class="h_4 w_100per"></div></div>
										</div>
										<div class="col-md-6">
											<div class="shimmer"><div class="h_4 w_100per"></div></div>
										</div>
									</div>
									<!-- Simple product end -->
								</div>
							</div>
						</div>
						<div class="col-md-4 pt-2 delivery" id="shimmer_delivery">
							<div class="row deliveryOption">
								<div class="col-md-12">
									<div class="row">
										<div class="col-2 col-sm-2 col-md-2"><div class="shimmer"><div class="h_5 w_100per"></div></div></div>
										<div class="col-7 col-sm-7 col-md-7" id="dynamicAddress"> 
											<div class="shimmer"><div class="h_5 w_100per"></div></div>
										</div>
										<div class="col-3 col-sm-3 col-md-3 text-right"><div class="shimmer"><div class="h_5 w_4"></div></div></div>
									</div>
									<div class="row">
										<div class="col-2 col-sm-2 col-md-2"><div class="shimmer"><div class="h_5 w_100per"></div></div></div>
										<div class="col-7 col-sm-7 col-md-7" id="dynamicAddress"> 
											<div class="shimmer"><div class="h_5 w_100per"></div></div>
										</div>
										<div class="col-3 col-sm-3 col-md-3 text-right"><div class="shimmer"><div class="h_5 w_4"></div></div></div>
									</div>

									<div class="row">
										<div class="col-2 col-sm-2 col-md-2"><div class="shimmer"><div class="h_5 w_100per"></div></div></div>
										<div class="col-7 col-sm-7 col-md-7" id="dynamicAddress"> 
											<div class="shimmer"><div class="h_5 w_100per"></div></div>
										</div>
										<div class="col-3 col-sm-3 col-md-3 text-right"><div class="shimmer"><div class="h_5 w_4"></div></div></div>
									</div>

									<div class="row">
										<div class="col-2 col-sm-2 col-md-2"><div class="shimmer"><div class="h_5 w_100per"></div></div></div>
										<div class="col-7 col-sm-7 col-md-7" id="dynamicAddress"> 
											<div class="shimmer"><div class="h_5 w_100per"></div></div>
										</div>
										<div class="col-3 col-sm-3 col-md-3 text-right"><div class="shimmer"><div class="h_5 w_4"></div></div></div>
									</div>
									<div class="row">
										<div class="col-2 col-sm-2 col-md-2"><div class="shimmer"><div class="h_5 w_100per"></div></div></div>
										<div class="col-7 col-sm-7 col-md-7" id="dynamicAddress"> 
											<div class="shimmer"><div class="h_5 w_100per"></div></div>
										</div>
										<div class="col-3 col-sm-3 col-md-3 text-right"><div class="shimmer"><div class="h_5 w_4"></div></div></div>
									</div>

								</div>
							</div>
							<div class="row rating text-center" id="shimmer_rating">
								<div class="col-4 col-sm-4 col-md-4">
									<div class="shimmer"><div class="h_10 w_100per"></div></div>
								</div>
								<div class="col-4 col-sm-4 col-md-4">
									<div class="shimmer"><div class="h_10 w_100per"></div></div>
								</div>
								<div class="col-4 col-sm-4 col-md-4">
									<div class="shimmer"><div class="h_10 w_100per"></div></div>
								</div>
							</div>

							<div class="row rating text-center go-to-store">
								<div class="col-md-12">
									<span> <div class="shimmer"><div class="h_4 w_100per"></div></div> </span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>

		</span>
		<span v-else>
			<div class="container">
				<div class="row promotion_page">
					<div class="col-md-12">
						<img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
						<h4> {{ $t('Product Not Found') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
					</div>
				</div>
			</div>
		</span>


		<br><br>


    </div>
</template>

<script type="application/ld+json">
	
</script>

<script>
    import axios from "axios";
    import Form from "vform";
	import swal from 'sweetalert';
	import pagination from 'laravel-vue-pagination';
	import ProductGrid from './parts/ProductGrid';
	import QuickView from "./parts/QuickView.vue";
    export default {

		mode: 'history',
        data() {
            return {
				productfound:true,
				loading: false,
                baseurl: "",
                singleProduct: [],
                default_image: "",
                gallery_images: "",
				calculated_price:'',
				discount_Percentage:'',
				ecodedUrl:'',
				produt_id:'',
				rate:0,
				AllRating:'',
				files: '',
				user: '',
				SingleReview:{},
				checkCommented:'',
				checkCompare:'',
				showCommentForm:1,
				Total_Comments:'',
				temporaryComment:false,
				temporaryData:'',
				testData:'',
				cartForm: new Form({
				  product_id: '',
				  price:'',
				}),
				product_simillar:'',
				addresses: {},
				districts:{},
				upazilas:{},
				unions:{},
				havetoload:true,
				product_simillar_status:1,
				if_veryfied:false,
				calculated_in_stock:null,
				seller_ratings_info:'',
				purchased:false,
				thumbnailUrl:'',
				productId:'',
				videoLoaded:false,
            };
        },
		components: {
			pagination,
			ProductGrid,
			QuickView
		},
		methods: {
			imageLoadError(event){
				event.target.src = "/images/notfound.png";
			},
			loadVideoFromNavigation(direction){
				let currentImage = $(".show-small-img[alt='now']");
				let targetImage = direction == 'next' ? currentImage.next() : currentImage.prev();
				if(targetImage.hasClass('video')){
					this.videoLoaded = true;
				}
			},

			initiateSliders(){
				var swiper = new Swiper(".similerSwiper", {
				slidesPerView: 6,
				spaceBetween: 30,
				breakpoints: {
					320: {
					slidesPerView: 2,
					spaceBetween: 10
					},
					640: {
					slidesPerView: 3,
					spaceBetween: 10
					},
					1200: {
					slidesPerView: 5,
					spaceBetween: 10
					},
					1920: {
					slidesPerView: 6,
					spaceBetween: 10
					}
				},
				pagination: {
					el: ".swiper-pagination",
					clickable: true,
				},
				});
			},

			addToCompare(product_id){
				let session_key = localStorage.getItem("session_key");
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
						'Content-Type': 'application/json;charset=UTF-8',
						"Access-Control-Allow-Origin": "*",
						'Authorization': 'Bearer '+token
					}
				}
				this.produt_id = product_id;
				axios.post(this.$baseUrl+'/api/v1/add-to-compare', {product_id:product_id,session_key:session_key}, axiosConfig).then(response => {
					if(response.data.status == 1){
						this.checkCompare = 1;
						$('.alreadycompared').css({'color':'#c7c7c7'});
						this.$store.dispatch('loadedCompares');
						swal({
							title: "Successfully added to your compare list.",
							icon: "success",
							timer: 3000
						});
					}else{
						swal("Sorry" , response.data.message,  "error" );
					}
				});

			},
	
			restockRequest(product_id,seller_id){
				$('.disabledbtn'+product_id).attr('disabled', true);
				$('.disabledbtn'+product_id).html('<div class="spinner-border spinner-border-sm"></div>');
				let session_key = localStorage.getItem("session_key");
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
					}
				}

				let lang = localStorage.getItem("lang");
		
				axios.post(this.$baseUrl+'/api/v1/restock-request', {product_id:product_id,seller_id:seller_id,session_key:session_key},axiosConfig ).then(response => {
					if(response.data.status == 1){
						swal({
							title: response.data.message,
							icon: "success",
							timer: 10000
						}).then(()=>{
							$('.disabledbtn'+product_id).attr('disabled', false);
							if(lang == 'bn'){
								$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> রিস্টক রিকোয়েস্ট');
							}else{
								$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> Restock Request');
							}
						});
					}else{
						swal( "Oops", response.data.message, "error");
						$('.disabledbtn'+product_id).attr('disabled', false);
						if(lang == 'bn'){
							$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> রিস্টক রিকোয়েস্ট');
						}else{
							$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> Restock Request');
						}
					}
				});
			},


			addToCart(product_id){
				$('.disabledbtn'+product_id).attr('disabled', true);
				$('.disabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
				let session_key = localStorage.getItem("session_key");
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
					}
				}

				let lang = localStorage.getItem("lang");
				let qty = jQuery('.qty').attr('data-qty');
					axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty,session_key:session_key},axiosConfig ).then(async response => {
						if(response.data.status == 1){
							await this.$store.dispatch('loadedCart');
							jQuery('.back_to_cart').trigger('click');
							swal({
								title: "Product added to cart Successfully.",
								icon: "success",
								timer: 3000
							}).then(()=>{
								$('.disabledbtn'+product_id).attr('disabled', false);
								if(lang == 'bn'){
									$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
								}else{
									$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
								}
							});
							dataLayer.push({ ecommerce: null });
							dataLayer.push({
								event: "AddToCart",
								ecommerce: {
									currency: "BDT",
									add: {                                // 'add' actionFieldObject measures.
										products: [{                        //  adding a product to a shopping cart.
											name: response.data.product.title,
											id: response.data.product.id,
											price: response.data.data.price,
											brand: response.data.product.brand_title,
											category: response.data.product.category_title,
											variant: response.data.data.variable_options,
											quantity: response.data.data.qty
										}]
									}
								}
							});
						}else{
							swal ( "Oops", response.data.message, "error");
							$('.disabledbtn'+product_id).attr('disabled', false);
							if(lang == 'bn'){
								$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
							}else{
								$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
							}
						}
					});
			},
			buyNow(product_id){
				$('.buynowdisabledbtn'+product_id).attr('disabled', true);
				$('.buynowdisabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
				let session_key = localStorage.getItem("session_key");
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
					}
				}

				let lang = localStorage.getItem("lang");
				let qty = jQuery('.qty').attr('data-qty');
					axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty,session_key:session_key},axiosConfig ).then(response => {
						if(response.data.status == 1){
							this.$store.dispatch('loadedCart');
							jQuery('.back_to_cart').trigger('click');
							swal({
								title: "Product added to cart Successfully.",
								icon: "success",
								timer: 3000
							}).then(()=>{
								$('.buynowdisabledbtn'+product_id).attr('disabled', true);
								if(lang == 'bn'){
									$('.buynowdisabledbtn'+product_id).html('এখন কিনুন');
								}else{
									$('.buynowdisabledbtn'+product_id).html('Buy Now');
								}
								jQuery('.show_checkout_section').trigger('click');
								jQuery('.left_cart_icon').trigger('click');
							});
							dataLayer.push({ ecommerce: null });
							dataLayer.push({
								event: "AddToCart",
								ecommerce: {
									currency: "BDT",
									add: {                                // 'add' actionFieldObject measures.
										products: [{                        //  adding a product to a shopping cart.
											name: response.data.product.title,
											id: response.data.product.id,
											price: response.data.data.price,
											brand: response.data.product.brand_title,
											category: response.data.product.category_title,
											variant: response.data.data.variable_options,
											quantity: response.data.data.qty
										}]
									}
								}
							});
						}else{
							swal ( "Oops", response.data.message, "error");
							$('.disabledbtn'+product_id).attr('disabled', false);
							if(lang == 'bn'){
								$('.disabledbtn'+product_id).html('এখন কিনুন');
							}else{
								$('.disabledbtn'+product_id).html('Buy Now');
							}
						}
					});
			},
			variableAddToCart(product_id){
				$('.disabledbtn'+product_id).attr('disabled', true);
				$('.disabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
				let token = localStorage.getItem("token");
				let session_key = localStorage.getItem("session_key");
				let axiosConfig = {
					headers: {
						'Content-Type': 'application/json;charset=UTF-8',
						"Access-Control-Allow-Origin": "*",
						'Authorization': 'Bearer '+token
					}
				}

				const formData = new FormData(this.$refs['variable_form']);
				const data = {session_key:session_key};
				for (let [key, val] of formData.entries()) {
					Object.assign(data, { [key]: val });
				}

				axios.post(this.$baseUrl+'/api/v1/variable-add-to-cart',data,axiosConfig).then(response =>{
					if(response.data.status == '1'){
						this.$store.dispatch('loadedCart');
						jQuery('.back_to_cart').trigger('click');
						swal({
						title: "Product added to cart Successfully.",
						icon: "success",
						timer: 3000
						}).then(()=>{
							$('.disabledbtn'+product_id).attr('disabled', false);
							$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Buy Now');
							jQuery('.show_checkout_section').trigger('click');
							jQuery('.left_cart_icon').trigger('click');
						});
						dataLayer.push({ ecommerce: null });
						dataLayer.push({
							event: "AddToCart",
							ecommerce: {
								currency: "BDT",
								add: {                                // 'add' actionFieldObject measures.
									products: [{                        //  adding a product to a shopping cart.
										name: response.data.product.title,
										id: response.data.product.id,
										price: response.data.data.price,
										brand: response.data.product.brand_title,
										category: response.data.product.category_title,
										variant: response.data.data.variable_options,
										quantity: response.data.data.qty
									}]
								}
							}
						});
					}else{
						$('.disabledbtn'+product_id).attr('disabled', false);
						$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
						swal ( "Oops", response.data.message, "error");
					}
				}).catch(function(){
					$('.disabledbtn'+product_id).attr('disabled', false);
					$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
					swal ( "Oops" ,  'Something went wrong. Please try again later!',  "error");
				});
			},

			digitaladdToCart(product_id,product_type=null){
				$('.disabledbtn'+product_id).attr('disabled', true);
				$('.disabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
				let user  = this.$store.getters.getLoadedUser.user;
				let session_key = localStorage.getItem("session_key");
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
					}
				}
				let qty = $('.digital_product_qty').attr('data-qty');
				let phone_number = $('.phone_number').val();
				let service_date = $('#service_date').val();
				let service_time = $('#service_time').find('option:selected').val();

				if(product_type == 'service'){
					if(service_date == '' || service_time == '' ){
						swal ( "Oops", 'Service date and time is required!.', "error");
						$('.disabledbtn'+product_id).attr('disabled', false);
						$('.disabledbtn'+product_id).html('Add To Cart');
						return;
					}
				}

			
				if(phone_number == -1 || phone_number.length == 11){

					axios.post(this.$baseUrl+'/api/v1/digital-add-to-cart', {
						shipping_option:'',
						service_date:service_date,
						service_time:service_time,
						phone_number:phone_number,
						session_key:session_key, 
						product_id:product_id, 
						qty:qty
					}, axiosConfig).then(response =>{
						if(response.data.status == '1'){
							this.$store.dispatch('loadedCart');
							jQuery('.back_to_cart').trigger('click');
							swal({
							title: "Product added to cart Successfully.",
							icon: "success",
							timer: 3000
							}).then(()=>{
								$('.disabledbtn'+product_id).attr('disabled', false);
								$('.disabledbtn'+product_id).html('Add To Cart');
							});
						}else{
							$('.disabledbtn'+product_id).attr('disabled', false);
							$('.disabledbtn'+product_id).html('Add To Cart');
							swal ( "Oops", response.data.message, "error");
						}
					});
				}else{
					$('.disabledbtn'+product_id).attr('disabled', false);
					$('.disabledbtn'+product_id).html('Add To Cart');
					swal ( "Oops", 'Please type a valid mobile number.', "error");
				}
			},

			addToWishlist(product_id){
				let session_key = localStorage.getItem("session_key");
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
						'Content-Type': 'application/json;charset=UTF-8',
						"Access-Control-Allow-Origin": "*",
						'Authorization': 'Bearer '+token
					}
				}
				this.produt_id = product_id;
				axios.post(this.$baseUrl+'/api/v1/add-to-wishlist', {product_id:product_id,session_key:session_key}, axiosConfig).then(response => {
					if(response.data.status == 1){
						this.$store.dispatch('loadedWishlist');
						swal({
							title: "Successfully added to your wishlist.",
							icon: "success",
							timer: 3000
						});
					}else{
						swal("Sorry" , response.data.message,  "error" );
					}
				});

			},

            load_single_product() {
				this.videoLoaded = false;
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
						'Content-Type': 'application/json;charset=UTF-8',
						"Access-Control-Allow-Origin": "*",
						"X-localization": localStorage.getItem("lang"),
						'Authorization': 'Bearer '+token
					}
				}

                let slug = this.$route.params.slug;
				let that = this;
                axios.get(this.$baseUrl + "/api/v1/get-product/"+slug, axiosConfig).then((response) => {
					if(response.data.status == 0 || response.data.status == '0'){
						that.productfound = false;
						//that.$router.push({name:'notfound'});
					}else{
						this.singleProduct  = response.data;
						this.purchased  = response.data.purchased ?? false;
						this.seller_ratings_info  = response.data.seller_ratings_info;
						
						this.if_veryfied  = response.data.shop_verified.if_veryfied;
						
						let productId = response.data.id;
						this.productId = productId;

						that.getComments(productId);
						//console.log('Single product meta = '+response.data.meta);
						this.calculated_in_stock= response.data.calculated_in_stock;
						this.produt_id      = response.data.id;
						this.default_image  = response.data.default_image;
						if(response.data.gallery_images){
							this.gallery_images = response.data.gallery_images.split(",");
						}else{
							this.gallery_images = null;
						}
						this.ecodedUrl      = encodeURIComponent(this.$baseUrl+this.$route.fullPath);
						let user_id = localStorage.getItem("userID");
						let categoryId = response.data.category_id;
						this.calculated_price = response.data.price_after_offer;
						this.loading = true;
						//Check compare
						axios.post(this.$baseUrl+'/api/v1/check-compare', {product_id:productId}, axiosConfig).then(response => {
							this.checkCompare = response.data;
						});
						//Check reviewed
						axios.post(this.$baseUrl+'/api/v1/check-reviewed', {user_id:user_id, product_id:productId}).then(response => {
							this.checkCommented = response.data.message;
							// setTimeout(function() {
							// 	$('.show-small-img:first-of-type').click();
							// 	$(".show-small-img").removeAttr('alt');
							// 	$(".show-small-img:first").attr('alt', 'now');
							// },500);

						});
						//All star ratig
						axios.get(this.$baseUrl + "/api/v1/get-all-rating/"+productId).then((response) => {
							this.AllRating = response.data;
						});
						//Simillar product

						axios.get(this.$baseUrl+'/api/v1/get-simillar-product', { params: {categoryId:categoryId,produt_id:productId }}).then(response => {
							this.product_simillar_status = response.data.status;
							this.product_simillar = response.data.simillar_products;
							setTimeout(function(){
								$('.simillar_width').attr("data-carouseWith", response.data.length*293);
								$('.simillar_slider').css({'width':response.data.length*293+'px'});
								$('.show-small-img').first().trigger('click');
							},500);
						});

						dataLayer.push({ ecommerce: null });
						dataLayer.push({
						event: "view_item_list",
						ecommerce: {
							currency: "BDT",
							value: this.calculated_price,
							items: [
								{
									item_id: this.singleProduct.id,
									item_name: this.singleProduct.title,
									affiliation: "",
									coupon: "",
									discount: this.singleProduct.discount_Percentage,
									index: 0,
									item_brand: this.singleProduct.category_title,
									item_category: this.singleProduct.brand_title,
									item_list_id: "",
									item_list_name: "",
									item_variant: "",
									location_id: "",
									content_type: "product",
									price: this.calculated_price,
									quantity: this.singleProduct.qty
								}
							]
						}
					});
					}
					// setTimeout(function() {
					// 	console.log(Number($('#variable_form .calculated_price').attr('data-calculated-price')));
					// 	$('#variable_form .price_text').text('');
					// 	// $('#variable_form .price_text').text(Number($('#variable_form .calculated_price').attr('data-calculated-price')));
					// 	$('#variable_form .variant_dropdwon').prop('selectedIndex',1).trigger('change');;
					// },1000);
                });
				const plugin = document.createElement("script");
				plugin.setAttribute( "src",this.$frontendUrl+"/assets/js/parts/product.js");
				plugin.async = true;
				document.body.appendChild(plugin);
				// fbq('track', 'PageView');

				// fbq('track', 'PageView', {
				// 	content_name: 'Really Fast Running Shoes', 
				// 	content_category: 'Apparel & Accessories > Shoes',
				// 	content_ids: ['1234'],
				// 	content_type: 'product',
				// 	value: 4.99,
				// 	currency: 'USD' 
				// });    

				
            },
			getComments(){
				let product_id = this.productId;
				axios.get(this.$baseUrl + "/api/v1/get-review-by-product-id/"+product_id).then((response) => {
					this.SingleReview = response.data;
					//console.log(response.data);
					this.Total_Comments = response.data.total;
					//console.log('Total comments = '+response.data.total);
					//console.log('COMMENTS = '+response.data);

					const plugin = document.createElement("script");
					plugin.setAttribute( "src",this.$frontendUrl+"/assets/js/parts/big.js");
					plugin.async = true;
					document.body.appendChild(plugin);
					
				});
              
			},
			
			rating($id){
				this.rate = $id;
			},
			handleFilesUpload(){
				this.files = this.$refs.files.files;
			},
			reveiewSubmit(){
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
				let user_id = localStorage.getItem("user_id");
				for( var i = 0; i < this.files.length; i++ ){
				  let file = this.files[i];
				  formData.append('files[' + i + ']', file);
				}
				formData.append('user_id', user_id);
				formData.append('product_id', product_id);
				formData.append('comment', comment);
				formData.append('rate', rate);
				if(rate < 1){
					swal({
					  title: "Please star rate this product.",
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
						$('.review-section').fadeOut("slow");
						
					}).catch(function(){
					  swal ( "Oops" ,  'Something went wrong',  "error" );
					});
				}
			},
			scrollToTop(){
				window.scrollTo(0,0);
			},
			getDistrict(){
				let id =  jQuery('#division').find('option:selected').val();
				axios.get(this.$baseUrl + "/api/v1/get-district/"+id).then((response) => {
					this.upazilas = {};
					this.unions = {};
					this.districts = response.data;
				});
			},
			getUpazila(){
				let id =  jQuery('#district').find('option:selected').val();
				axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
					this.unions = {};
					this.upazilas = response.data;
				});
			},
			getUnion(){
				let id =  jQuery('#upazila').find('option:selected').val();
				axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
					this.unions = response.data;
				});
			},
			checkShippingRate(){
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
						'Content-Type': 'application/json;charset=UTF-8',
						"Access-Control-Allow-Origin": "*",
						'Authorization': 'Bearer '+token
					}
				}

				let district_id = jQuery('#district').find('option:selected').val();
				let product_id = this.singleProduct.id;
				let seller_id = this.singleProduct.seller_id;

				let division_title = jQuery('#division').find('option:selected').text();
				let district_title = jQuery('#district').find('option:selected').text();
				let upazila_title = jQuery('#upazila').find('option:selected').text();
				let union_title = jQuery('#union').find('option:selected').text();

				 +', '+district_title+', '+upazila_title+', '+ union_title;

				axios.get(this.$baseUrl + "/api/v1/get-shipping-rates/"+district_id+'/'+product_id+'/'+seller_id,axiosConfig).then((response) => {
					this.singleProduct.shipping_options = response.data;
					jQuery('.change_location').trigger('click');
					let text = division_title +', '+district_title+', '+upazila_title+', '+ union_title;
					jQuery('#dynamicAddress').html(text);
					
				});
			},
			br(){
				return '/sssssssssssssss'.replace(/\\/g, '');
			}
        },

		computed:{
			youtubeThumbnailUrl(){
				let videoLink = this.singleProduct.video_link;
				let videoId = typeof videoLink == 'string' ? videoLink.match(/(?:youtube(?:-nocookie)?\.com\/(?:embed\/|watch\?(?:.*&)?v=)|youtu\.be\/)([A-Za-z0-9_-]{11})/) : null;
				return videoId ? 'https://img.youtube.com/vi/'+videoId[1]+'/hqdefault.jpg' : this.$frontendUrl+'/images/video.jpg';
			},
			logged_in_user(){
				return this.$store.getters.getLoadedUser.user;
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
			htmlSsr() {
				return  this.$store.getters.getLoadedSsr;
			},
		},
		watch:{
			$route(to, from){
				$('#layer_1').hide();
				this.load_single_product();
				this.scrollToTop();
				setTimeout(function() {
					$('[class=\'swiper-slide\']').css({'width': '226.667px',  'margin-right':'10px'});
					$('[class=\'swiper-slide-prev swiper-slide-next\']').css({'width': '226.667px',  'margin-right':'10px'});
					$('[class=\'swiper-slide-next swiper-slide-prev\']').css({'width': '226.667px',  'margin-right':'10px'});
				}, 1500);
				setTimeout(function() {
					$('[class=\'swiper-slide\']').css({'width': '226.667px',  'margin-right':'10px'});
					$('[class=\'swiper-slide-prev swiper-slide-next\']').css({'width': '226.667px',  'margin-right':'10px'});
					$('[class=\'swiper-slide-next swiper-slide-prev\']').css({'width': '226.667px',  'margin-right':'10px'});
				}, 2000);
				setTimeout(function() {
					$('[class=\'swiper-slide\']').css({'width': '226.667px',  'margin-right':'10px'});
					$('[class=\'swiper-slide-prev swiper-slide-next\']').css({'width': '226.667px',  'margin-right':'10px'});
					$('[class=\'swiper-slide-next swiper-slide-prev\']').css({'width': '226.667px',  'margin-right':'10px'});
				}, 5000);





			}
		},

        mounted() {

			this.thumbnailUrl = this.$thumbnailUrl;
			//ZOOM image
			$(document).ready(function() {
				setTimeout(function() {
					$('.circle-1').css({ 'background': '#0093d9' });
					$('.circle-1-text').css({ 'color': '#0093d9' });
					$('.circle-1').html('<i class="fa fa-check" aria-hidden="true"></i>');
					$('#big-img').css({ 'background': '#fff' });
					$('[id=\'big-img\']').css({ 'background': '#fff' });
					$('.zoom-show').zoomImage();
					$('.show-small-img:first-of-type').css({ 'border': 'solid 1px #951b25', 'padding': '2px' });
					$('.show-small-img:first-of-type').attr('alt', 'now').siblings().removeAttr('alt');
					$('.show-small-img').click(function() {
						$('#show-img').attr('src', $(this).attr('src'));
						$('[id=\'big-img\']').attr('src', $(this).attr('src'));
						$(this).attr('alt', 'now').siblings().removeAttr('alt');
						$(this).css({ 'border': 'solid 1px #951b25', 'padding': '2px' }).siblings().css({ 'border': 'none', 'padding': '0' });
						if ($('#small-img-roll').children().length > 4) {
							if ($(this).index() >= 3 && $(this).index() < $('#small-img-roll').children().length - 1) {
								$('#small-img-roll').css('left', -($(this).index() - 2) * 76 + 'px');
							} else if ($(this).index() == $('#small-img-roll').children().length - 1) {
								$('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px');
							} else {
								$('#small-img-roll').css('left', '0');
							}
						}
					});

				}, 500);
			});

			this.load_single_product();

			var that = this;
			setInterval(function(){
				if(jQuery('.similerSwiper').find('.swiper-pagination-clickable').length == 0 ){
					that.initiateSliders();
				}
			},500);


            this.baseurl = this.$baseUrl;
			this.scrollToTop();
			this.br();
			
			
        },
		updated(){
			let title = (this.singleProduct.title != undefined) ? this.singleProduct.title : '';
			document.title = "MabiY Shop | "+title; 

			var date = new Date();
			var getmonth = date.getMonth() + 1;
			var getday = date.getDate()+1;
			var getyear = date.getFullYear();
			if(getmonth < 10) getmonth = '0' + getmonth.toString();
			if(getday < 10) getday = '0' + getday.toString();
			var inDate = getyear + '-' + getmonth + '-' + getday;
			$('#service_date').attr('min', inDate);

		}

    }


</script>
