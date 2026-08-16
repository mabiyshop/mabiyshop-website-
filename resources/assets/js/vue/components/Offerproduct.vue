<template>
<div>

<section v-if="loading">
    <div class="container">
        <span v-if="products.data">
        <div class="row">
            <div class="search_banner">
                <img @error="imageLoadError" v-if="category_banner" :src="baseurl+'/'+category_banner" alt="">
                <img @error="imageLoadError" v-else :src="baseurl+'/assets/images/banner-1.jpg'" alt="">
                
            </div>
        </div>
        <div class="row search_products promotion">
            <div v-for="(data, index) in products.data" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-2">
                <ProductGrid :data="data"></ProductGrid>
                <QuickView :data="data"></QuickView>
            </div>
        </div>
        <div class="row order_pagination">
            <div class="col-md-12">
                <pagination :data="products" @pagination-change-page="load_products"></pagination>
            </div>
        </div>
        </span>
        <span v-else>
            <section class="bg_white"><div class="row cart_empty"><div class="col-md-12"><p><img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
            </p> 
            <h4> {{ $t('Product Not Found') }} </h4> 
            <p>  
                <router-link class="router-link-active" :to="{ name: 'products', params: {} }">{{ $t('Continue shopping') }}</router-link>
            </p> </div></div></section>
        </span>
    </div>
</section>
<section v-else id="offer_page">
    <div class="container offer_shimmer">
        <div class="row w_100per padding_problem">
            <div class="shimmer width_problem"> 
                <div class="h_30 w_100per mb_10 mt_10"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per mb_30"></div> </div></div>
        </div>
        <div class="row">
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per mb_30"></div> </div></div>
        </div>
    </div>
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
import ProductGrid from './parts/ProductGrid';
import QuickView from './parts/QuickView.vue';
export default {
	data(){
		return{
			is_user: '',
            baseurl:'',
            products:{},
            content:'',
            category_banner:'',
            loading:false,
            thumbnailUrl:'',
		}
	},
	components:{
    Leftsidebar,
    pagination,
    ProductGrid,
    QuickView
},
	methods:{
        imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },
		addToCart(product_id,buynow = ''){
            if(buynow == 'buynow'){
                $('.buynowdisabledbtn'+product_id).attr('disabled', true);
                $('.buynowdisabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
            }else{
                $('.disabledbtn'+product_id).attr('disabled', true);
                $('.disabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
            }

           let user  = this.$store.getters.getLoadedUser.user;
            if(!user){
                $('#popupLoignModal').trigger('click');
                $('#dyanamicInput').html('<input type="hidden" id="addToCartProductId" value="'+product_id+'"><input type="hidden" id="loginBuyNow" value="'+buynow+'"> <input type="hidden" id="ProdcutType" value="simple">');
            }else{
                if(buynow == 'buynow'){
                    $('.buynowdisabledbtn'+product_id).attr('disabled', true);
                    $('.buynowdisabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
                }else{
                    $('.disabledbtn'+product_id).attr('disabled', true);
                    $('.disabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
                }
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
        
                axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty:1},axiosConfig ).then(response => {
                    if(response.data.status == 1){
                        this.$store.dispatch('loadedCart');
						jQuery('.back_to_cart').trigger('click');
                        swal({
                        title: "Product added to cart Successfully.",
                        icon: "success",
                        timer: 3000
                        }).then(()=>{
                            if(buynow == 'buynow'){
                            $('.buynowdisabledbtn'+product_id).attr('disabled', true);
                            $('.buynowdisabledbtn'+product_id).html('Buy Now');
                            }else{
                            $('.disabledbtn'+product_id).attr('disabled', true);
                            $('.disabledbtn'+product_id).html('Add To Cart');
                            }
                        if(buynow == 'buynow'){
                            this.$router.push({name:'checkout'});
                        } 
                        });
                    }else{
                        swal ( "Oops", response.data.message, "error");
                    }
                });
            }
		},
        load_products(page=1){
				let axiosConfig = {
					headers: {
						"X-localization": localStorage.getItem("lang"),
					}
				}

            let slug = this.$route.params.slug;
			axios.get(this.$baseUrl+'/api/v1/get-promotional-category-product/'+slug+"?page="+page,axiosConfig).then(response => {
				this.products = response.data.products;
				this.category_banner = response.data.category_banner;
                this.loading = true;
			});
        },
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
    watch:{
        $route(to, from){
            this.scrollToTop();
            this.load_products();
        },
        
    },
    computed:{
        logged_in_user(){
            return this.$store.getters.getLoadedUser.user;
        },
    },
	mounted(){
        this.thumbnailUrl = this.$thumbnailUrl;
        this.baseurl = this.$baseUrl;
        this.load_products();
        this.scrollToTop();
        document.title = "MabiY Shop | Category-Product";  
	}

}
</script>