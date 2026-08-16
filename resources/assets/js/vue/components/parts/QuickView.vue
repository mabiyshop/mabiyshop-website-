<template>
    <div class="modal fade" :id="'quickViewModal'+data.id" tabindex="-1" :aria-labelledby="'quickViewModalLabel'+data.id" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <p class="close_icon"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></p>
                <div class="modal-body">
                    <section id="product-details">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 image-section">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mycontainer quick_view">
                                                <div class="zoom-show" :href="baseurl+'/'+data.default_image">
                                                    <img @error="imageLoadError" :data-src="baseurl+'/'+data.default_image" :data-image-section="data.id" class="showImage" style="object-fit: contain;"/>
                                                </div>
                                                <div class="small-img" v-if="data.gallery_images">
                                                    <img src="/images/online_icon_right@2x.png" class="icon-left previewImage" alt="" :data-section-id="data.id"/>
                                                    <div class="small-container">
                                                        <div id="small-img-roll" :class="'small_images_list_'+data.id">
                                                            <img :data-src="baseurl+'/'+data.default_image" class="show-small-img" alt="now" :data-section-id="data.id" />
                                                            <img v-for="img in (data.gallery_images.split(','))" :key="img" :data-src="baseurl+'/'+img" class="show-small-img" alt="" :data-section-id="data.id" />
                                                        </div>
                                                    </div>
                                                    <img src="/images/online_icon_right@2x.png" class="icon-right nextImage" alt="" :data-section-id="data.id" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 description-section quick_view_description">
                                            <h4>{{ data.title }}</h4>
                                            <small> <b>weight: </b> {{ data.weight }} <span>{{ data.weight_unit }} </span> </small>
                                            <br>
                                            <small> {{ data.short_description }} </small>
                                            <div class="total_rats"><small> {{data.AllRating.total_rate > 0 ?data.AllRating.total_rate:0}} Ratings </small></div> 
                                            <div class="star-rating title_rating">
                                                <span :style="data.AllRating.average_percentage">  </span> 
                                            </div> 
                                            <div class="total_rats_per"> <b> ({{data.AllRating.average}}/5) </b> </div>
                                            
                                            
                                            
                                            <div class="share_section">	
                                                <ul class="share_list">
                                                    <li><span title="Add To Wishlist" class="compare" @click="addToWishlist(data.id)"> <i class="fa fa-heart" aria-hidden="true"></i></span></li>
                                                    <li><span title="Add To Compare" class="compare" @click="addToCompare(data.id)"> <i class="fa fa-retweet" aria-hidden="true"></i></span></li>
                                                </ul>
                                            </div>
                                            
    
                                            
                                            <!-- Simple product start -->
                                            <span v-if="data.product_type == 'simple'">
                                                <div class="single-page-price quick_view_price">
                                                    <div class="currect-price">
                                                        <b>BDT {{  data.price_after_offer }}</b>
                                                        <span v-if="data.special_price_type == 1">
                                                            <span v-if="parseInt(data.price_after_offer.replace(/,/g, ''))  < parseInt(data.price.replace(/,/g, ''))">
                                                                <del> <small>BDT {{ data.price }}</small> </del>
                                                            </span>
                                                        </span>
                                                        <span v-if="data.special_price_type == 2">
                                                            <span v-if="parseInt(data.price_after_offer.replace(/,/g, ''))  < parseInt(data.price.replace(/,/g, ''))" >
                                                                <del> <small>BDT {{ data.price }}</small> </del>
                                                            </span>
                                                        </span>
                                                    </div>
                                                </div>
    
    
                                                <div class="row">
                                                    <div class="col-md-12 quantity_grid quick_view_qty" v-if="data.in_stock > 0 && data.qty > 0">
                                                        <ul class="quantity">
                                                            <li>{{ $t('Quantity') }}:</li>
                                                        </ul>
                                                        <ul class="quantity_calculate">
                                                            <li><button class="minus">-</button></li>
                                                            <li><button class="qty" data-qty='1' :data-total_qty="data.qty" :data-productId="data.id">1</button></li>
                                                            <li><button class="plus">+</button></li>
                                                        </ul>
                                                        <!-- <h1 class="btn btn-primary clickMe">Click Me</h1> -->
                                                    </div>
                                                </div>
                                                <div class="row add_buy mt-2" v-if="data.in_stock > 0 && data.qty > 0">
                                                    <div class="col-md-6">
                                                        <button :class="'btn btn-primary addToCart quickview_add_cart disabledbtn'+data.id"  @click="addToCart(data.id)"><i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }}</button>
                                                    </div>
                                                </div>
                                                <!-- <div class="row out_of_stock_single_page" v-else>
                                                    <span class="out_of_stock"> {{ $t('Out Of Stock') }} </span>
                                                </div> -->
    
                                                <div class="row out_of_stock_single_page" v-else>
                                                    <div class="col-12 col-lg-12">
                                                        <div class="out_of_stock"> {{ $t('Out Of Stock') }} </div>
                                                    </div>
                                                    <div class="col-12 col-lg-12">
                                                        <div :class="'restockbtn btn btn-primary disabledbtn'+data.id"  @click="restockRequest(data.id)"> <i class="fa fa-bicycle mr-2" aria-hidden="true"></i> {{ $t('Restock Request') }}</div>
                                                    </div>
                                                </div>
    
    
    
                                            </span>
                                            <!-- Simple product end -->
                                            
                                            <!-- variable product start -->
                                            <div v-if="data.product_type == 'variable'" id="variable_product">
                                            <form ref="variable_form" id="variable_form" @submit.prevent="variableAddToCart(data.id)" class="mb-5">
                                                <div class="single-page-price quick_view_price">
                                                    <div class="calculated_price" :data-calculated-price="Number(calculated_price)"><b>BDT <span class="price_text">{{ Number(calculated_price) }}</span></b>
                                                        <span class="single-old-price" v-if="calculated_price < data.price"><del> <small>BDT  {{ data.price }} </small></del> <span class="discout_percentage">{{ discount_Percentage }}%</span></span>
                                                    </div>
                                                </div>
    
    
                                                <div class="row">
                                                    <div class="col-md-12 productSize">
                                                        <span v-for="(metas, index) in data.meta" :key="'AB'+index"> 
                                                            <span v-if="metas.meta_key == 'custom_options'">
    
                                                                <span v-for="(val, index) in metas.meta_value" :key="'AC'+index"> 
                                                                    
                                                                    <div v-if="data.hidden_option[val.title]" class="row">
                                                                        <!--Radio-->
                                                                        <div v-if="val.type == 'radio'" class="col-md-12 col-lg-12 mb-3 option_parent_group">
                                                                                
                                                                                <div class="variant_title"> 
                                                                                    <b>{{ val.title }} <b v-if="val.is_required == '1'" class="is_required">*</b>: </b>
                                                                                    <span class="selectedValue"></span>
                                                                                    <input type="hidden" data-additional-price="0" data-additional-qty="-1" class="variant_input" :name="val.title">
                                                                                </div>
    
                                                                                <ul>
                                                                                    <li v-for="(option, index) in filteredOptions" :key="'AD'+index">
                                                                                        
                                                                                        <div class="radio_image">
                                                                                            <span v-if="option.variant_image">
                                                                                            <img :title="'Additional Price BDT '+ option.price" class="color_image option_selector" :src="baseurl+'/'+option.variant_image" :data-price="option.price" :data-variable-qty="option.qty" :data-title="option.title" :data-sku="option.sku"/>
                                                                                            </span>
                                                                                            <span :data-price="option.price" :data-variable-qty="option.qty"  :data-title="option.title" :title="'Additional Price BDT '+ option.price" class="option_selector radio_select" v-else>
                                                                                                {{ option.title }}
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
                                                                                <option v-for="(option, index) in variant_dropdwons" :key="'AE'+index"
                                                                                        :value="option.title"
                                                                                        :data-variable-qty="option.qty"
                                                                                        :data-dropdownprice="option.price">
                                                                                    {{ option.title + ' (+BDT ' + option.price + ')' }}
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
    
    
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <!--Quantity-->
                                                    <div class="col-md-12 quantity_grid quick_view_qty" v-if="calculated_in_stock">
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
                                                            <div :class="'restockbtn btn btn-primary disabledbtn'+data.id"  @click="restockRequest(data.id)"> <i class="fa fa-bicycle mr-2" aria-hidden="true"></i> {{ $t('Restock Request') }}</div>
                                                        </div>
                                                    </div>
    
    
                                                    <!--Quantity-->
                                                </div>
                                               
                                               
                                               
                                               
                                                <div class="row add_buy add_buy_variable  mt-2" v-if="calculated_in_stock">
                                                    <div class="col-md-6">
                                                        <input type="hidden" name="product_id" :value="data.id">
                                                        <button type="button" :class="'btn btn-primary variable_disabled_btn addToCart disabledbtn'+data.id" disabled><i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }}</button>
                                                    </div>
                                                </div>
                                            
    
                                                </form>
                                            </div>
                                            <!-- variable product end -->
    
                                            <!-- Digital product start -->
                                            <span v-if="data.product_type == 'digital'" class="digital_product">
                                                <div class="single-page-price">
                                                    <div class="currect-price"><b>BDT {{ calculated_price }}</b></div>
                                                    <span v-if="data.special_price_type == 1">
                                                        <div class="single-old-price" v-if="calculated_price < data.price"><del>BDT {{ data.price }}</del> <span class="discout_percentage"> {{ discount_Percentage }}%</span></div>
                                                    </span>
    
                                                    <span v-if="data.special_price_type == 2">
                                                        <div class="single-old-price" v-if="calculated_price < data.price"><del>BDT {{ data.price }}</del> <br>  <small>Discount: </small> <small>  {{ discount_Percentage }}%</small></div>
                                                    </span>
                                                </div>
    
                                                
    
                                                <span v-for="(meta,index) in data.meta" :key="'AF'+index">
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
                                                
    
                                                <div class="row">
                                                    <div class="col-md-12 quantity_grid quick_view_qty" v-if="data.in_stock > 0 && data.qty > 0">
                                                        <ul class="quantity">
                                                            <li>{{ $t('Quantity') }}:</li>
                                                        </ul>
                                                        <ul class="quantity_calculate">
                                                            <li><button class="minus">-</button></li>
                                                            <li><button class="qty digital_product_qty" data-qty='1' :data-total_qty="data.qty" :data-productId="data.id">1</button></li>
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
                                                            <div :class="'restockbtn btn btn-primary disabledbtn'+data.id"  @click="restockRequest(data.id)"> <i class="fa fa-bicycle mr-2" aria-hidden="true"></i> {{ $t('Restock Request') }}</div>
                                                        </div>
                                                    </div>
    
    
    
                                                </div>
                                                <div class="row add_buy  mt-2" v-if="data.in_stock > 0 && data.qty > 0">
                                                    <div class="col-md-6">
                                                        <button :class="'btn btn-primary addToCart disabledbtn'+data.id" @click.prevent="digitaladdToCart(data.id)"><i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }} </button>
                                                    </div>
                                                </div>
                                            </span>
                                            <!-- Digital product end -->
    
    
    
    
                                            <!-- Tab Section Start -->
                                            <section id="product-details-description">
                                                <div class="container p-0">
                                                    <div class="col-md-12 product-details-descripiton" style="border:none;">
                                                        <div class="row">
                                                            <div class="col-md-12 p-0">
                                                                <ul class="nav nav-tabs" role="tablist">
                                                                    <li class="nav-item" role="presentation">
                                                                        <a class="nav-link quickview_description active" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true"> {{ $t('Product Description') }} </a>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <a class="nav-link quickview_specification" data-toggle="tab" href="#specification" role="tab" aria-controls="specification" aria-selected="false"> {{ $t('Specification') }}</a>
                                                                    </li>
    
                                                                    <span v-for="(metas, index) in data.meta" :key="'AG'+index"> 
                                                                    <span v-if="metas.meta_key == 'tab_option'">
                                                                    <span v-for="(val, index) in metas.meta_value" :key="'AH'+index"> 
                                                                        <li class="dynamic_nav_item" role="presentation">
                                                                            <a class="dynamic_nav_link" :data-id="index" data-toggle="tab"> {{ val.tab_option_title }}</a>
                                                                        </li>
                                                                    </span>
                                                                    </span>
                                                                    </span>
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane quickview_description_tab fade show active" role="tabpanel" aria-labelledby="description">
                                                                        <div property="schema:description" v-html="data.description"></div>
                                                                    </div>
                                                                    <div class="tab-pane quickview_specification_tab fade" role="tabpanel" aria-labelledby="specification">
                                                                 
                                                                        <span v-for="(sp, index) in data.specification" :key="'AI'+index">
                                                                            <p v-if="sp.meta_value"> 
                                                                                <strong class="text-capitalize">{{ sp.meta_key.replace(/_/g, " ") }}</strong> : {{sp.meta_value}} 
                                                                            </p> 
                                                                        </span>
                                                                    </div>
                                                                    <span v-for="(metas, index) in data.meta" :key="'AI'+index"> 
                                                                    <span v-if="metas.meta_key == 'tab_option'">
                                                                    <span v-for="(val, index) in metas.meta_value" :key="'AJ'+index"> 
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
    
    
    
    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
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
                baseurl:'',
                thumbnailUrl:'',
                calculated_in_stock:true,
                calculated_price: 0,
                discount_Percentage: '',
                AllRating: '',
            }
        },
        props:['data'],
        methods:{
                imageLoadError(event){
                    event.target.src = "/images/notfound.png";
                },
    
                restockRequest(product_id){
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
            
                    axios.post(this.$baseUrl+'/api/v1/restock-request', {product_id:product_id,session_key:session_key},axiosConfig ).then(response => {
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
                    axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty:1,session_key:session_key},axiosConfig ).then(response => {
                        if(response.data.status == 1){
                        this.$store.dispatch('loadedCart');
                        jQuery('.back_to_cart').trigger('click');
                        swal({
                            title: "Product added to cart Successfully.",
                            icon: "success",
                            timer: 3000
                        }).then(()=>{
                            $('.disabledbtn'+product_id).attr('disabled', true);
                            if(lang == 'bn'){
                                 $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
                            }else{
                                $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
                            }
                        });
                        }else{
                        swal ( "Oops", response.data.message, "error");
                            $('.disabledbtn'+product_id).attr('disabled', false);
                            $('.quickview_add_cart').attr('disabled', false);
                            
                            if(lang == 'bn'){
                                 $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
                            }else{
                                $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
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
                            $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
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
    
            digitaladdToCart(product_id){
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
                if(phone_number == -1 || phone_number.length == 11){
                    axios.post(this.$baseUrl+'/api/v1/digital-add-to-cart', {shipping_option:'', phone_number:phone_number,session_key:session_key, product_id:product_id, qty:qty}, axiosConfig).then(response =>{
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
    
            calculated_functions(data){
                const start = new Date(data.special_price_start);
                const end = new Date(data.special_price_end);
                const diffTime = Math.abs(start - end);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if(data.special_price_type == 1){
                    if(diffTime > 0){
                        this.calculated_price = data.special_price;
                        //Discount percentage
                        var discount = data.price - data.special_price;
                        this.discount_Percentage = parseFloat(discount/data.price*100).toFixed(2);
                    }else{
                        this.calculated_price = data.price;
                    }
                }
                if(data.special_price_type == 2){
                    if(diffTime > 0){
                        let discount = data.special_price/100*data.price;
                        let price = data.price - parseFloat(discount).toFixed(2);
                        this.calculated_price = parseFloat(price).toFixed(2);
                        this.discount_Percentage = parseFloat(data.special_price).toFixed(0);
                    }else{
                        this.calculated_price = data.price;
                    }
                }
    
            }
    
        },
        computed: {
            filteredOptions() {
                return this.val.value.filter(option => option.qty > 0);
            },
            variant_dropdwons() {
                return this.val.value.filter(option => option.qty > 0);
            }
        },
        mounted(){
            this.baseurl = this.$baseUrl;
            this.thumbnailUrl = this.$thumbnailUrl;
            this.calculated_functions(this.$props.data);
        }
    }
    </script>