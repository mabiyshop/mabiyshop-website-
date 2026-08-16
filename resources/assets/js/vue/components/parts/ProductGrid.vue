<template>
    <div typeof="schema:Product" class="product" :itemprop="'itemListElement'"  >
        <div class="product-image">
            <div class="product_badge" v-if="data.offer_percentage > 0">
            <span>-{{data.offer_percentage}}%</span>
            </div>
            <div v-if="data.shop_verified.if_veryfied" class="product_badge_flagship"><span> <img :src="baseurl+'/'+data.shop_verified.veryfied_banner" alt="Flagship"> </span></div>
            <router-link :to="{ name: 'product', params: {slug: data.slug } }"><img loading="lazy" property="schema:image" @error="imageLoadError" :src="data.default_image ? thumbnailUrl+'/'+data.default_image : '/images/notfound.png'" alt=""></router-link>
        </div>
        <div class="product-details">
                <div v-if="parseInt(data.price_after_offer.replace(/,/g, '')) == parseInt(data.price.replace(/,/g, ''))" class="offer_gap"></div>
                <router-link :to="{ name: 'product', params: {slug: data.slug } }">
                    <p property="schema:name" class="elipsis_title">{{data.title}}</p>
                </router-link>

            <div class="product-title">
                <div typeof="schema:Offer" class="price">
                <ul>
                    <li>  
                    <div property="schema:price" class="now-price">BDT {{  data.price_after_offer }}</div> 
                    </li>
                    <li><div class="old-price"> <del v-if="parseInt(data.price_after_offer.replace(/,/g, ''))  < parseInt(data.price.replace(/,/g, ''))">BDT {{ data.price }}</del> </div></li>
                </ul>
                </div>
               
               

               
                <!--Add to Cart Button start-->

                <div v-if="data.product_type == 'variable' || data.product_type == 'service'" class="text-center variable_details">
                <div class="row">
                        <ul class="hover_icon_group icon_group">
                            <li><i class="fa fa-heart" @click="addToWishlist(data.id)" aria-hidden="true"></i></li>
                            <li><i class="fa fa-eye" :data-modal="data.id" data-toggle="modal" :data-target="'#quickViewModal'+data.id" aria-hidden="true"></i></li>
                            <li><i class="fa fa-retweet"  @click="addToCompare(data.id)" aria-hidden="true"></i></li>
                        </ul>
                        <div class="col-12 col-sm-12 col-md-12">
                            <router-link :to="{ name: 'product', params: {slug: data.slug } }" :class="'add_to_cart disabledbtn'+data.id" ><i class="fa fa-info-circle"></i>{{ $t('Details') }}</router-link>
                        </div>
                    </div>
                </div>
                <div v-else class="row p-0">
                <span v-if="data.in_stock > 0 && data.qty > 0" style="width: 90%;margin: 0 auto;">
                    <div class="row all-efecive-btns">
                        <ul class="hover_icon_group icon_group">
                            <li><i class="fa fa-heart" @click="addToWishlist(data.id)" aria-hidden="true"></i></li>
                            <li><i class="fa fa-eye"  :data-modal="data.id" data-toggle="modal" :data-target="'#quickViewModal'+data.id" aria-hidden="true"></i></li>
                            <li><i class="fa fa-retweet"  @click="addToCompare(data.id)" aria-hidden="true"></i></li>
                        </ul>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 pr-1 add_to_cart_area">
                            <a :class="'add_to_cart disabledbtn'+data.id" type="submit" name="price"  @click.prevent="addToCart(data.id)"> 
                                {{ $t('Add To Cart') }}
                            </a>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 pl-1 buy_now_area">
                            <a :class="'buy_now buynowdisabledbtn'+data.id" type="submit" name="price"  @click.prevent="buyNow(data.id)">
                                {{ $t('Buy Now') }}
                            </a>
                        </div>
                    </div>
                </span>
                <span v-else>
                    <div class="row">
                        <ul class="hover_icon_group icon_group">
                            <li><i class="fa fa-heart" @click="addToWishlist(data.id)" aria-hidden="true"></i></li>
                            <li><i class="fa fa-eye"  :data-modal="data.id" data-toggle="modal" :data-target="'#quickViewModal'+data.id" aria-hidden="true"></i></li>
                            <li><i class="fa fa-retweet"  @click="addToCompare(data.id)" aria-hidden="true"></i></li>
                        </ul>
                    </div>
                    <div class="col-md-12 text-center"> <a class="out_of_stock" >{{ $t('Out Of Stock') }}</a></div>
                </span>
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
            calculated_price: '',
            discount_Percentage: '',
            AllRating: '',
        }
    },
    props:['data'],
    computed: {
        schemaPrice() {
        // Format the price as per your requirements
        return `BDT ${this.data.price_after_offer}`;
        },
        schemaOldPrice() {
        // Format the old price as per your requirements
        return `BDT ${this.data.price}`;
        }
    },
    methods:{
            imageLoadError(event){
                event.target.src = "/images/notfound.png";
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
                            timer: 1000
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
                            timer: 1000
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
                        timer: 1000
                    }).then(()=>{
                        $('.disabledbtn'+product_id).attr('disabled', true);
                        if(lang == 'bn'){
                             $('.disabledbtn'+product_id).html('যুক্ত করুন');
                        }else{
                            $('.disabledbtn'+product_id).html('Add To Cart');
                        }
                    });
                    }else{
                    swal ( "Oops", response.data.message, "error");
                        $('.disabledbtn'+product_id).attr('disabled', false);
                        $('.quickview_add_cart').attr('disabled', false);
                        
                        if(lang == 'bn'){
                             $('.disabledbtn'+product_id).html('যুক্ত করুন');
                        }else{
                            $('.disabledbtn'+product_id).html('Add To Cart');
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
                axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty:1,session_key:session_key},axiosConfig ).then(response => {
                    if(response.data.status == 1){
                    this.$store.dispatch('loadedCart');
					jQuery('.back_to_cart').trigger('click');
                    swal({
                        title: "Product added to cart Successfully.",
                        icon: "success",
                        timer: 1000
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
                    }else{
                    swal ( "Oops", response.data.message, "error");
                        $('.buynowdisabledbtn'+product_id).attr('disabled', false);
                        $('.quickview_add_cart').attr('disabled', false);
                        
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
                    timer: 1000
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
                        timer: 1000
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

            // axios.get(this.$baseUrl + "/api/v1/get-all-rating/"+data.id).then((response) => {
            //     this.AllRating = response.data;
            // });
        }

    },
    mounted(){
        this.baseurl = this.$baseUrl;
        this.thumbnailUrl = this.$thumbnailUrl;
        this.calculated_functions(this.$props.data);
    }
}
</script>
