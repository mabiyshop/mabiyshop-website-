<template>
<div>

<section v-if="loading" class="ddd">
    <div class="container">
        <span v-if="products.data">
        <div class="row">
            <div class="search_banner">
                <img @error="imageLoadError" :src="banner" alt="">
            </div>
        </div>
        <div class="row search_products promotion">
            <div v-for="(data, index) in products.data" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-2 search_product_div">
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
            <div class="row promotion_page">
                <div class="col-md-12">
                    <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                    <h4> {{ $t('Product Not Found') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
                </div>
            </div>
        </span>
    </div>
</section>
<section v-else id="register-page">
    <div class="container products_shimmer">
        <!-- <div class="row">
            <div class="col-md-12">
               <img @error="imageLoadError" src="/images/product-2.gif" alt="">
            </div>
        </div> -->
        <div class="row w_100per padding_problem">
            <div class="shimmer width_problem"> 
                <div class="h_30 w_100per mb_10"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per mb_30"></div> </div></div>
        </div>
        <div class="row">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div> </div></div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per mb_30"></div> </div></div>
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
            banner:'',
            loading:false,
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
        load_products(page=1){
            let product_type = this.$route.params.product_type;
			axios.get(this.$baseUrl+'/api/v1/get-promotion-product/'+product_type+"?page="+page).then(response => {
				this.products = response.data.products;
				this.banner = response.data.banner;
                this.loading = true;
			});
        },
    	scrollToTop() {
            window.scrollTo(0,0);
        },
	},
    computed:{
        logged_in_user(){
            return this.$store.getters.getLoadedUser.user;
        },
    },
	mounted(){
        this.baseurl = this.$baseUrl;
        this.load_products();
        this.scrollToTop();
	}

}
</script>