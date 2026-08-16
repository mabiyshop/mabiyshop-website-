
<template>
    <div>
	
	<section class="brand_banner">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="brand_banner_bg" v-bind:style="{ 'background-image': 'url(' + baseurl+sellerBanner + ')' }">
						<div class="row">
							<div class="col-md-4 brand_logo">
								<img @error="imageLoadError" :src="baseurl+'/'+sellerLogo">
							</div>
							<div class="col-md-4"></div>
							<div class="col-md-4"></div>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="brand_page_nav">
						<ul>
							<li> <a href="javascript:void(0)" class="active" id="brand_all_products">All Products</a> </li>
							<li> <a href="javascript:void(0)"  id="brand_profiles">Profile</a> </li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>



<section class="shop_section" id="register-page">
	<div class="container">
		<!-- <div v-if="total_products > 0" class="row search_products"> -->
			<div v-for="(data, index) in ProductBySeller.data" :key="index" class="col-6 col-sm-6 col-md-3 col-lg-2">
				<div class="search_single_item">
					<div class="search_image">
						<router-link :to="{ name: 'product', params: {slug: data.slug } }"><img @error="imageLoadError" :src="baseurl+'/'+data.default_image" alt=""></router-link>
					</div>
					<div class="search_details">
						<span>{{ data.title.substr(0, 22)+".." }} </span>

						<div class="now-price">BDT {{  data.price_after_offer }} <span class="old-price-inline"><del v-if="parseInt(data.price_after_offer.replace(/,/g, ''))  < parseInt(data.price.replace(/,/g, ''))">BDT {{ data.price }}</del></span></div>

						<div v-if="data.product_type == 'variable'" class="seach_button">
							<div class="col-md-12 details_btn">
								<router-link :to="{ name: 'product', params: {slug: data.slug } }" >Details</router-link>
							</div>
						</div>
						<div v-else class="seach_button">
							<input class="buy_now" type="submit" name="price" value="Buy Now" @click.prevent="addToCart(data.id,'buynow')">
							<input class="add_to_cart" type="submit" name="price"  @click.prevent="addToCart(data.id)">
						</div>

					</div>
				</div>
			</div>
		</div>

		<!-- <div  v-else class="row promotion_page">
			<div class="col-md-12">
				<img @error="imageLoadError" :src="baseurl+'/assets/images/product-not-found.png'" alt="">
			</div>
		</div> -->

		<div class="profile_section p-0" id="vendor_profile_page">
			<div class="col-md-12 profile_main_review">
				<div class="row">
					<div class="col-md-3">
						<div class="p-2 border-right h-100 ">
							<div class="">
								<h4>Seller Size</h4>
							</div>
							<div class="">
								<div class="progress progress-bar-vertical">
									<div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="height: 30%;">
									<span class="sr-only">30% Complete</span>
									</div>
								</div>
								
									<div class="progress progress-bar-vertical">
									<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="height: 60%;">
									<span class="sr-only">60% Complete</span>
									</div>
								</div>

								<div class="progress progress-bar-vertical">
									<div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height: 100%;">
									<span class="sr-only">60% Complete</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="p-2 border-right h-100 ">
							<div class="">
								<h4>Joined</h4>
							</div>
							<div class="profile_item">
								<span> {{ joined_time(SellerProfile.created_at) }} </span>
								<br>
								
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="p-2 border-right h-100 ">
							<div class="">
								<h4>Shipped on Time</h4>
							</div>
							<div class="profile_item">
								<span>98%</span>
								<br>
								<small>this is average for sellers in same category</small>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="p-2 h-100 ">
							<div class="profile_item">
								<h4>Chat</h4>
							</div>
							<div class="profile_item">
								<span>95%</span>
								<br>
								<small>Chat response rate</small>
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
                                <h4>Seller Ratings</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <b> {{ ratings.average_rating > 0 ? ratings.average_rating : 0 }}/5 </b>
                                <ul>
									<div class="star-rating">
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
													<li> <span class="verified"> <img @error="imageLoadError" :src="baseurl+'/uploads/verify.png'"> Verified Purchase</span></li>
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
											<li> <span class="verified"> <img @error="imageLoadError" :src="baseurl+'/uploads/verify.png'"> Verified</span></li>
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

	
		
    </div>	
</template>






<script>
import {bus} from '../app.js'
import axios from 'axios'
import Form from 'vform'
import moment from 'moment';
import pagination from 'laravel-vue-pagination';

export default {
	data(){
		return{
			name:'',
			productLength:'',
			total_products:0,
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
		}
	},
	components: {
		pagination
	},
	methods:{
		imageLoadError(event){
			event.target.src = "/images/notfound.png";
		},
		addToCart(product_id,buynow = ''){
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
				this.ProductBySeller = response.data.product;
				this.total_products = response.data.total_products;
				this.ratings = response.data.ratings;
				this.SellerProfile = response.data.profile;
				this.sellerLogo= response.data.profile.shopinfo.logo;
				this.sellerBanner= '/'+response.data.profile.shopinfo.banner;
			});
		},
		load_seller_products_comments(page=1){
			let slug = this.$route.params.slug;
			axios.get(this.$baseUrl+'/api/v1/get-seller-product-comments/'+slug+"?page="+page).then(response => {
				this.reviewedProducts = response.data.reveiwed_products;
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
		this.load_products_by_shop_slug();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY | Shop"; 
		this.load_seller_products_comments();
	}
}


</script>

