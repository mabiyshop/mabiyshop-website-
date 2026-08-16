<template>
<div>

<section v-if="loading" id="offer_page">
    <div class="container all_sellers">
        
        <div class="row offer_banner">
            <div class="col-md-12 mt-3 mb-3"> 
                <img @error="imageLoadError" :src="baseurl+'/'+regular_offer_banner" style="padding: 0 15px;" alt="img">
             </div>
        </div>
        <span v-if="regular_offer_status == 1">
            
                <div class="row">
                    <div v-for="(offer, index) in allCatergories" :key="index" class="col-md-6 col-lg-6 mb-3">
                        
                            <router-link :to="{ name: 'offerProduct', params: {slug: offer.slug } }">
                                <img @error="imageLoadError" v-if="offer.banner" :src="baseurl+'/'+offer.banner" class="w-100" alt="img">
                                <img  v-else src="/images/notfound.png" alt="img">
                                <!-- <div class="offer_cat_title">
                                    <p>{{ offer.title}}</p>
                                </div> -->
                            </router-link>
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


<section v-else id="offer_page">
    <div class="container offer_shimmer">
        <!-- <div class="row">
            <div class="col-md-12">
               <img @error="imageLoadError" src="/images/Offer.gif" alt="">
            </div>
        </div> -->
        <div class="row w_100per padding_problem">
            <div class="shimmer width_problem"> 
                <div class="h_30 w_100per mb_10"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6"> <div class="shimmer"> <div class="h_20 w_100per"></div></div></div>
            <div class="col-md-6"> <div class="shimmer"> <div class="h_20 w_100per"></div></div></div>
            <div class="col-md-6"> <div class="shimmer"> <div class="h_20 w_100per"></div></div></div>
            <div class="col-md-6"> <div class="shimmer"> <div class="h_20 w_100per"></div></div></div>
            <div class="col-md-6"> <div class="shimmer"> <div class="h_20 w_100per"></div></div></div>
            <div class="col-md-6"> <div class="shimmer"> <div class="h_20 w_100per"></div></div></div>
        </div>
        
    </div>
</section>



</div>
</template>

<script>
import axios from 'axios'
import swal from 'sweetalert';
import Leftsidebar from './default/Leftsidebar';
import pagination from 'laravel-vue-pagination';



export default {
	data(){
		return{
            loading:false,
			is_user: '',
            baseurl:'',
            regularOffer:'',
            allCatergories:[],
            regular_offer_banner:'',
		}
	},
	components:{
		Leftsidebar,
        pagination
	},
	methods:{
        imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },
        load_regular_offers(){
			axios.get(this.$baseUrl+'/api/v1/get-regular-offer').then(response => {
                this.regular_offer_banner = response.data.regular_offer_banner;
				this.regular_offer_status = response.data.regular_offer_status;
                this.allCatergories = response.data.regular_offer_categories;
                this.loading = true;
			});
        },
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
	mounted(){
        this.scrollToTop();
        this.load_regular_offers();
        this.baseurl = this.$baseUrl;
        document.title = "MabiY Shop | Offer"; 
	}

}
</script>