<template>
<div>

<section v-if="loading"  id="offer_page">
    <div class="container all_sellers">
        <div class="row offer_banner">
            <div class="col-md-12 mt-3 mb-3"> 
                <img @error="imageLoadError" :src="baseurl+'/'+promotionalOffer.promotional_offer_banner" alt="img">
             </div>
        </div>
        <span v-if="promotionalOffer.promotional_offer_status == 1">
            <div class="col-md-12"> 
                <div class="row">
                    <div v-for="(offer, index) in allCatergories" :key="index" class="col-md-2 col-lg-2 mb-3">
                        <div class="single_offer_category">
                            <router-link :to="{ name: 'offerProduct', params: {slug: offer.slug } }">
                                <img @error="imageLoadError" v-if="offer.image" :src="baseurl+'/'+offer.image" alt="img">
                                <img @error="imageLoadError" v-else :src="baseurl+'/media/notfound.png'" alt="img">
                                <div class="offer_cat_title">
                                    <p>{{ offer.title}}</p>
                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 
            <div class="row order_pagination">
                <div class="col-md-12">
                   <pagination :data="allCatergories" @pagination-change-page="load_regular_offers"></pagination>
                </div>
            </div>
            -->
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
        <div class="row w_100per padding_problem">
            <div class="shimmer width_problem"> 
                <div class="h_40 w_100per mb_10"></div>
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

export default {
	data(){
		return{
			is_user: '',
            baseurl:'',
            promotionalOffer:'',
            allCatergories:{},
            loading:false,
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
        load_promotional_offers(){
			axios.get(this.$baseUrl+'/api/v1/get-promotional-offer').then(response => {
				this.promotionalOffer = response.data;
                this.allCatergories = response.data.promotional_offer_categories;
                this.loading = true;
			});
        },
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
    
	mounted(){
        this.scrollToTop();
        this.load_promotional_offers();
        this.baseurl = this.$baseUrl;
        document.title = "MabiY Shop | Promotional Offer"; 
	}

}
</script>