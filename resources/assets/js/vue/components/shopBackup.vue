
<template>
    <div>


	<span v-if="loading">


	<section class="brand_banner">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="brand_banner_bg" v-bind:style="{ 'background-image': 'url(' + baseurl+sellerBanner + ')' }">
						<div class="row">
							<div class="col-md-4 brand_logo">
								<img @error="imageLoadError" :src="thumbnailUrl+'/'+sellerLogo">
							</div>
							<div class="col-md-4"></div>
							<div class="col-md-4"></div>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="brand_page_nav">
						<ul>
							<li> <a href="javascript:void(0)" class="active" id="brand_all_product"> {{ $t('All Products') }} </a> </li>
							<li> <a href="javascript:void(0)"  id="brand_profile">{{ $t('Profile') }} </a> </li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

<section class="shop_section" id="register-page">
	<div class="container">

		<div v-if="ProductBySeller" class="row search_products promotion">
			<div v-for="(data, index) in ProductBySeller" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-2">
				<ProductGrid :data="data" class="shop_page"></ProductGrid>
				<QuickView :data="data"></QuickView>

			</div>
			<div class="row product_pagination" style="width: 100%;">
				<div class="col-md-12 text-center mb-3 mt-3">
					<div class="moreBtn">
						<!--<div class="btn btn-primary" v-if="prev_page_url" @click.prevent="lessProducts(prev_page_url)">Less more<i class="fa fa-chevron-up ml-3" aria-hidden="true"></i></div> -->
						<div class="btn btn-primary load_more_btn" v-if="next_page_url" @click.prevent="moreProducts(next_page_url)">{{ $t('Load More') }}<i class="fa fa-chevron-down ml-3" aria-hidden="true"></i></div>
					</div>
				</div>
			</div>
		</div>
		<div  v-else class="row promotion_page">
			<div class="col-md-12">
				<img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
				<h4> {{ $t('Product Not Found') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
			</div>
		</div>




		<div class="profile_section p-0" id="vendor_profile_page">
			<div class="col-md-12 profile_main_review">
				<div class="row">

					<div class="col-12 col-sm-3 col-md-3">
						<div class="p-2 border-right h-100 ">
							<div class="">
								<h4>{{ $t('Joined') }}</h4>
							</div>
							<div class="profile_item">
								<span> {{ joined_time(SellerProfile.created_at) }} </span>
								<br>
								
							</div>
						</div>
					</div>

					<div class="col-12 col-sm-3 col-md-3">
						<div class="p-2 border-right h-100 ">
							<div class="">
								<h4>{{ $t('Seller Ratings') }}</h4>
							</div>
							<div class="profile_item">
                                <span v-if="seller_ratings_info.seller_ratings > 85">{{ seller_ratings_info.seller_ratings }}%</span>
                                <span v-else>85%</span>
								<br>
							</div>
						</div>
					</div>
					<div class="col-12 col-sm-3 col-md-3">
						<div class="p-2 border-right h-100 ">
							<div class="">
								<h4> {{ $t('Shipped on Time') }}</h4>
							</div>
							<div class="profile_item">
                                <span v-if="seller_ratings_info.shipped_on_time > 80">{{ seller_ratings_info.shipped_on_time }}%</span>
                                <span v-else>80%</span>
								<br>
								<!-- <small>this is average for sellers in same category</small> -->
							</div>
						</div>
					</div>
					<div class="col-12 col-sm-3 col-md-3">
						<div class="p-2 h-100 ">
							<div class="profile_item">
								<h4> {{ $t('Chat') }}</h4>
							</div>
							<div class="profile_item">
                                <span v-if="seller_ratings_info.chat_response_rate > 90">{{ seller_ratings_info.chat_response_rate }}%</span>
                                <span v-else>90%</span>
								<br>
								<!-- <small>Chat response rate</small> -->
							</div>
						</div>
					</div>
				</div>
			</div>


		<!--Vendor product rating start-->
        <section id="product-rating bg-none">
            <div class="container p-0">
			<div class="row signle_page_aling">
			<div class="col-md-12 product-rating-content">
                <div class="row rating-section vendor_bg_white">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <h4> {{ $t('Seller Ratings')}}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <b> {{ ratings.average_rating > 0 ? ratings.average_rating : 0 }}/5 </b>
                                <ul>
									<div class="star-rating average_percentage">
									  <span :style="ratings.average_percentage"></span>
									</div>
                                </ul>
                                <p>{{ ratings.total_ratings > 0 ? ratings.total_ratings : 0 }} Rating(s)</p>
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
                                    <div class="progress-bar" :style="ratings.five_star_percentage"></div>
                                </div>
                                <span>{{ratings.five_star ? ratings.five_star :0 }}</span>
								
                                <div class="progress">
                                    <div class="progress-bar" :style="ratings.four_star_percentage"></div>
                                </div>
                                <span>{{ratings.four_star ? ratings.four_star :0 }}</span>
								
                                <div class="progress">
                                    <div class="progress-bar" :style="ratings.three_star_percentage"></div>
                                </div>
                                <span>{{ratings.three_star ? ratings.three_star :0  }}</span>
								
                                 <div class="progress">
                                    <div class="progress-bar" :style="ratings.two_star_percentage"></div>
                                </div>
                                <span>{{ratings.two_star  ? ratings.two_star :0 }}</span>
                                <div class="progress">
                                    <div class="progress-bar" :style="ratings.one_star_percentage"></div>
                                </div>
                                <span>{{ratings.one_star  ? ratings.one_star :0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div v-if="ratings.total_ratings > 0" class="row review-section vendor_bg_white vendor_product_review">
                    <div class="col-md-12">
                        <h4>Product Ratings and Reviews({{ ratings.total_ratings > 0 ? ratings.total_ratings : 0 }}) </h4>
                    </div>
                    <div class="col-md-12">
					
						<!--Single comment start-->
						<span v-if="reviewedProducts.data">
                        <div v-for="(review, index) in reviewedProducts.data" :key="index" class="review-box">

							<div class="media comment_single">
								<router-link :to="{ name: 'product', params: {slug: review.product.slug } }"> <img @error="imageLoadError" :src="baseurl+'/'+review.product.default_image" alt=""> </router-link>
								<div class="media-body">
									<router-link :to="{ name: 'product', params: {slug: review.product.slug } }"> <h5>{{ review.product.title  }}</h5> </router-link>
									<div class="all-star comment_single_stars">
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




									<div class="row">
										<div class="col-md-12">
											<div class="all-star">
												<ul class="review_user_info">
													<li><b>By: </b></li>
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
										<div v-for="(img1, index) in review.image" :key="index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img1 != 'no-image'"> <img @error="imageLoadError" :src="baseurl+'/uploads/comments/'+img1"> </span> </div>
									</div>









								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-md-12">
								<pagination :data="reviewedProducts" @pagination-change-page="load_seller_products_comments"></pagination>
							</div>
						</div>
						</span>
						
						
						<div v-for="(review, index) in temporaryData" :key="index" class="review-box">
						

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
											<li><b>By: </b></li>
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
								<div v-for="(img2, index) in review.image" :key="index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img2"> {{ img2 }} <img @error="imageLoadError" :src="baseurl+'/uploads/comments/'+img2"> </span> </div>
							</div>
						</div>
						<!--Single comment end-->
                    </div>
                </div>

            </div>
            </div>
            </div>
        </section>
		<!--vendor product rating end-->
		</div>
	</div>
</section>

</span>
<span v-else id="offer_page">
    <div class="container">
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
</span>



		
		
    </div>	
</template>






<script>
import {bus} from '../app.js'
import axios from 'axios'
import Form from 'vform'
import moment from 'moment';
import pagination from 'laravel-vue-pagination';
import ProductGrid from './parts/ProductGrid';
import QuickView from './parts/QuickView.vue';
export default {
	data(){
		return{
			loading:false,
			name:'',
			productLength:'',
			ProductBySeller:{},
			SellerProfile:'',
			baseurl:'',
			cartForm: new Form({
			  product_id: 7,
			  price:2500,
			}),
			sellerLogo:'',
			sellerBanner:'',
			ratings:'',
			SingleReview:'',
			Total_Comments:'',
			temporaryComment:false,
			temporaryData:'',
			reviewedProducts:{},
			seller_ratings_info:'',
			thumbnailUrl:'',
			next_page_url: '',
			prev_page_url:'',


		}
	},
	components: {
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
		load_products_by_shop_slug(){
			let slug = this.$route.params.slug;
			axios.get(this.$baseUrl+'/api/v1/get-products-by-seller-slug/'+slug).then(response => {
				this.ProductBySeller = response.data.product.data;
				this.ratings = response.data.ratings;
				this.SellerProfile = response.data.profile;
				this.sellerLogo= response.data.profile.shopinfo.logo;
				this.sellerBanner= '/'+response.data.profile.shopinfo.banner;
				this.loading = true;
				this.next_page_url = response.data.product.next_page_url;
				this.prev_page_url = response.data.product.prev_page_url;
			});
		},


    async moreProducts(url) {
        $('.load_more_btn').attr('disabled', true);
        let lang = localStorage.getItem("lang");
		if(lang == 'bn'){
			$('.load_more_btn').html('লোড হচ্ছে.. <span class="spinner-border spinner-border-sm"></span>');
		}else{
			$('.load_more_btn').html('Loading.. <span class="spinner-border spinner-border-sm"></span>');
		}

        axios.get(url).then(response => {
			let c = response.data.product.data;
			this.pro = c;
			let that = this;
			c.forEach(element => {
				that.ProductBySeller.push(element);
				$('.load_more_btn').attr('disabled', false);
				let lang = localStorage.getItem("lang");
				if(lang == 'bn'){
					$('.load_more_btn').html('আরো লোড করুন <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
				}else{
					$('.load_more_btn').html('Load More <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
				}
			});
			this.next_page_url = response.data.product.next_page_url;
			this.prev_page_url = response.data.product.prev_page_url;
		});
    },











		load_seller_products_comments(page=1){
			let slug = this.$route.params.slug;
			axios.get(this.$baseUrl+'/api/v1/get-seller-product-comments/'+slug+"?page="+page).then(response => {
				this.reviewedProducts = response.data.reveiwed_products;
				this.seller_ratings_info  = response.data.seller_ratings_info;
			});
		},





		joined_time(date){
			let fromTime = moment(date).diff(moment(), "milliseconds");
			let duration = moment.duration(fromTime);
			let years = duration.years() / -1;
			let months = duration.months() / -1;
			let days = duration.days() / -1;
			if (years > 0) {
				var Ys = years == 1? years + " year and ": years + " years and ";
				var Ms = months == 1? months + " month": months + " months";
				return Ys + Ms;
			}else {
				if(months > 0){
					return months == 1? months + " month": months + " months";
				}else{
					return days == 1? days + " day": days + " days";
				}	
			}
		},
    	scrollToTop(){
            window.scrollTo(0,0);
        }
	},
	
	computed: {
		userDatas(){
		  return this.$store.getters.getLoadedUser.data;
		},
        logged_in_user(){
            return this.$store.getters.getLoadedUser.user;
        }
	},

	mounted(){
		this.scrollToTop();
		this.thumbnailUrl = this.$thumbnailUrl;
		this.load_products_by_shop_slug();
		this.baseurl = this.$baseUrl;
		this.thumbnailUrl = this.$thumbnailUrl;
		document.title = "MabiY Shop | Shop"; 
		this.load_seller_products_comments();
	}
}


</script>

