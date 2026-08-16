<template>
<div>

<section v-if="loading" id="offer_page">
    <div class="container mt-5">
    
        <span v-if="flashDeals.length > 0">
            <div class="row">
                <div v-for="(deals, index) in flashDeals" :key="index" class="col-md-6 col-lg-6 mb-3">
                    <router-link :to="{ name: 'flashdeal', params: {slug: deals.slug } }">
                        <img @error="imageLoadError" v-if="deals.banner" :src="baseurl+'/'+deals.banner" class="w-100" alt="img">
                        <img  v-else src="/images/notfound.png" alt="img">
                    </router-link>
                </div>
            </div>
        </span>
        <span v-else>
            <div class="row promotion_page">
                <div class="col-md-12">
                    <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                    <h4> {{ $t('No deals found') }}.  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
                </div>
            </div>
        </span>
    </div>
</section>


<section v-else id="offer_page">
    <div class="container offer_shimmer">
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
            flashDeals:[],
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
    	scrollToTop() {
            window.scrollTo(0,0);
        },
        load_flash_deals(){
			axios.get(this.$baseUrl+'/api/v1/get-flash-deals').then(response => {
				this.flashDeals = response.data;
                this.loading = true;
			});
		},
	},
	mounted(){
        this.scrollToTop();
        this.load_flash_deals();
        this.baseurl = this.$baseUrl;
        document.title = "MabiY Shop | Flash Deals"; 
	}

}
</script>