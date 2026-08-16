<template>
<div>

<section v-if="loading" id="register-page">
    <div class="container all_sellers">
        <div class="row seller_search">
           <div class="col-md-3"> </div>
           <div class="col-md-3"> </div>
           <div class="col-md-3"> </div>
           <div class="col-md-3">
                <form @submit.prevent="load_seller_post()">
                <div class="input-group">
                    <input type="text" :placeholder="$t('Search Seller')+'..'" class="form-control seller">
                    <div class="input-group-append" @click.prevent="load_seller_post()">
                        <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                </div>
                </form>
           </div>
        </div>




        <span v-if="sellersLength > 0">
            <div class="row  p-0">
                <div v-for="(seller, index) in sellers.data" :key="index" class="col-12 col-sm-12 col-md-4 col-lg-4 mb-4">
                    <!-- <div class="single_seller">
                        <router-link :title="seller.name" :to="{ name: 'shop', params: {slug: seller.slug } }">
                            <div v-if="seller.shop_verified.if_veryfied" class="product_badge_flagship"><span> <img @error="imageLoadError"  :src="baseurl+'/'+seller.shop_verified.veryfied_banner" alt="Flagship"> </span></div>
                            <img class="shop_logo" @error="imageLoadError"  :src="thumbnailUrl+'/'+seller.logo" alt="">
                        </router-link>
                    </div> -->


                    <div class="sinlge_seller_box">
                        <div v-if="seller.shop_verified.if_veryfied" class="seller_flagship"><span> <img @error="imageLoadError"  :src="baseurl+'/'+seller.shop_verified.veryfied_banner" alt="Flagship"> </span></div>
                        <div class="row p-0 ">
                            <div class="col-5 col-sm-5 col-md-5 col-md-5 seller_border">
                                <img class="shop_logo" @error="imageLoadError"  :src="thumbnailUrl+'/'+seller.logo" alt="shop logo">
                            </div>
                            <div class="col-7 col-sm-7 col-md-7 col-md-7 rating-section">
                                <b>{{ seller.name }}</b>
                                <ul>
                                    <div class="star-rating average_percentage">
                                        <span :style="'width: '+parseInt(seller.ratings.seller_ratings)+'%'"></span>
                                    </div>
                                </ul>
                                <router-link :title="seller.name" :to="{ name: 'shop', params: {slug: seller.slug } }"><span class="btn btn-primary visit_store">Visit Store <i class="fa fa-chevron-right" aria-hidden="true"></i> </span></router-link>
                            </div>
                        </div>
                    </div>







                </div>
            </div>

            <div class="row order_pagination">
                <div class="col-md-12">
                    <pagination v-if="postrequest" :data="sellers" @pagination-change-page="load_seller_post"></pagination>
                    <pagination v-else :data="sellers" @pagination-change-page="load_seller"></pagination>

                </div>
            </div>
        </span>
        <span v-else>
            <div class="row promotion_page">
                <div class="col-md-12">
                    <img @error="imageLoadError"  src="/assets/images/notfound.png" alt="">
                </div>
            </div>
        </span>
    </div>
</section>
<section v-else id="register-page">
    <div class="container all_sellers">
         <!-- <img @error="imageLoadError"  src="/images/product.gif" alt=""> -->
        <div class='row'>
            <div class="col-md-10 col-md-10"></div>
            <div class="col-12 col-sm-12 col-md-3 col-lg-2">
                <div class="shimmer">
                    <div class="h_3 w_100per seller_shimer"></div>
                </div>
            </div>
        </div>
        <div class="row mb_25">
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
        </div>
        <div class="row mb_25">
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
        </div>
        <div class="row mb_25">
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
        </div>
        <div class="row mb_25">
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_20 w_100per"></div>  </div> </div>
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
import jquery from '../../../../../public/assets/js/jquery';

export default {
	data(){
		return{
			is_user: '',
            baseurl:'',
            thumbnailUrl:'',
            sellers:{},
            sellersLength:0,
            loading:false,
            postrequest:false,
		}
	},
	components:{
		Leftsidebar,
        pagination
	},
	methods:{
        load_seller(page=1){
			axios.get(this.$baseUrl+'/api/v1/get-all-sellers/?page='+page).then(response => {
				this.sellers = response.data.sellers;
				this.sellersLength = response.data.sellers.data.length;
                this.loading = true;
			});
            let currentRoute = this.$route.name;
            if(currentRoute == 'home'){
                $('.nav_wrapper').show();
            }else{
                $('.nav_wrapper').hide();
            }
        },
        load_seller_post(page=1){
            if($('.seller').val()){
                let formData = new FormData();
                formData.append('search', $('.seller').val());
                axios.post(this.$baseUrl+'/api/v1/get-all-sellers-post/?page='+page, formData).then(response => {
                    this.sellers = response.data.sellers;
                    this.sellersLength = response.data.sellers.data.length;
                    this.loading = true;
                    this.postrequest = true;
                });
                let currentRoute = this.$route.name;
                if(currentRoute == 'home'){
                    $('.nav_wrapper').show();
                }else{
                    $('.nav_wrapper').hide();
                }
            }
        },

        imageLoadError(event){
             event.target.src = "/images/notfound.png";
        },

    	scrollToTop() {
            window.scrollTo(0,0);
        },
    	clickable() {
            let that = this;
            $('.all_product_menu').click(function(){
                let page = 1;
                axios.get(that.$baseUrl+'/api/v1/get-all-sellers/?page='+page).then(response => {
                    that.sellers = response.data.sellers;
                    that.sellersLength = response.data.sellers.data.length;
                });
            });
        }
        
	},
	mounted(){
        this.scrollToTop();
        this.baseurl = this.$baseUrl;
        this.thumbnailUrl = this.$thumbnailUrl;
        document.title = "MabiY Shop | Sellers"; 
        this.clickable();
        this.load_seller();
	}

}
</script>