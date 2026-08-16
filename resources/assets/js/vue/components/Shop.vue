
<template>
    <div>


<span v-if="loading">
	<section class="brand_banner">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="brand_banner_bg" v-bind:style="{ 'background-image': 'url(' + baseurl+sellerbanner+ ')' }">
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
					<div class="brand_page_nav brand_page_nav_shop">
						<ul>
							<li> <a href="javascript:void(0)"  data-tab-name="section_shop" class="triger_tab active" id="brand_all_product"> {{ $t('All Products') }} </a> </li>
							<li> <a href="javascript:void(0)"  data-tab-name="profile_section" class="triger_tab" id="brand_profile">{{ $t('Profile') }} </a> </li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="shop_section filter_responsive">
		<div class="container">

		<div class="section_shop">
			<!-- Seller product start -->
			<div v-if="ProductBySeller" class="row">
				<section id="search-page" class="shop_responsive" style="width: 100%;">
					<div class="container p-0">
						<div class="row p-0">
							<div class="col-md-12 search_page_orderBy">
								<div class="row">
								<div class="col-md-3 pr-0"></div>
								<div class="col-md-6 p-0"></div>
								<div class="col-12 col-sm-12 col-md-3 pl-0 mb-2">
									<select class="orederbyselect" @change.prevent="load_search_product()">
										<option disabled selected>- {{ $t('Sellect Order By') }} -</option>
										<option value="lowtohigh">{{ $t('Low to high price') }}</option>
										<option value="hightolow">{{ $t('High to Low  price') }}</option>
										<option value="atoz">{{ $t('Order: By A To Z') }}</option>
										<option value="ztoa">{{ $t('Order: By Z To A') }}</option>
									</select>
								</div>
								</div>
							</div>
						</div>
						<div class="row search filter_responsive_row">
							<div class="search_filter">
								<h6>Filter <i class="fa fa-filter" aria-hidden="true"></i></h6>
								<div class="serach_filter_chevron">
									<i class="changeable_fa fa fa-chevron-right" aria-hidden="true"></i>
								</div>
							</div>
							<div class="col-12 col-sm-12 col-md-3 search_sidebar">
								<div class="category_check">
									<b>{{ $t('Price') }}</b>
									<div class="rang">
										<div class="pricerang"> <input type="number" min="1"  class="form-control minprice" :placeholder="$t('Min')"> </div>
										<div class="pricerang"> <input type="number" min="1"  class="form-control maxprice" :placeholder="$t('Max')"> </div>
										<div class="pricerang"> <input type="submit" class="form-control sumitsearch" :value="$t('Search')" @click.prevent="load_search_product()"> </div>
									</div>
								</div>
								<div class="category_check">
									<b>{{ $t('Categories') }}</b>
									<ul>
										<li v-for="(cat, index) in categories" :key="index"> <input type="checkbox" name="categorySelected" :value="cat.id" @click="load_search_product()"> <span>{{ cat.title }}</span> </li>
									</ul>

									<div class="moreBtn">
										<div class="lessMore lessBtn" v-if="prev_page_url" @click.prevent="lessCategoris(prev_page_url)">{{ $t('Less') }}<i class="fa fa-chevron-up" aria-hidden="true"></i></div>
										<div class="lessMore morebutn" v-if="next_page_url" @click.prevent="moreCategoris(next_page_url)">{{ $t('More') }}<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
									</div>
								</div>
								<div class="category_check">
									<b>{{ $t('Brand') }}</b>
									<ul>
										<li v-for="(brand, index) in brands" :key="index"> <input type="checkbox" name="brandSelected" :value="brand.id" @click="load_search_product()"> <span>{{ brand.title }}</span> </li>
									</ul>
									<div class="moreBtn">
										<div class="lessMore lessBtn" v-if="brand_prev_page_url" @click.prevent="lessBrands(brand_prev_page_url)">{{ $t('Less') }}<i class="fa fa-chevron-up" aria-hidden="true"></i></div>
										<div class="lessMore morebutn" v-if="brand_next_page_url" @click.prevent="moreBrands(brand_next_page_url)">{{ $t('More') }}<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
									</div>
								</div>
							</div>

							<div class="col-12 col-sm-12 col-md-9 col-lg-9 search_product_div"> 
								<span v-if="searchProduct.data">
								<div class="row search_products">
									<div v-for="(data, index) in searchProduct.data" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-3 mb-3">
										<ProductGrid :data="data"></ProductGrid>
										<QuickView :data="data"></QuickView>
									</div>
								</div>

								<div class="row product_pagination">
									<div class="col-md-12 text-center mb-3 mt-3">
										<div class="moreBtn">
											<div class="btn btn-primary load_more_btn" v-if="all_next_page_url" @click.prevent="moreProducts(all_next_page_url)"> {{ $t('Load More') }} <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i></div>
										</div>
									</div>
								</div>
								</span>
								<span v-else>
									<div class="row search_page_row">
										<div class="col-md-12 product_not_found">
											<img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
											<h5>{{ $t('No product found for this keyword') }}: <b>{{ this.$route.params.content }}</b> </h5>
										</div>
									</div>
								</span>
							</div>
						</div>
					</div>
				</section>
			</div>
			<!-- Seller product End -->
			<div v-else class="row promotion_page">
				<div class="col-md-12">
					<img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
					<h4> {{ $t('Product Not Found') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
				</div>
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
                                <span v-if="seller_ratings_info.seller_ratings">{{ seller_ratings_info.seller_ratings }}%</span>
                                <span v-else>0%</span>
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
                                <span v-if="seller_ratings_info.shipped_on_time">{{ seller_ratings_info.shipped_on_time }}%</span>
                                <span v-else>0%</span>
								<br>
								<!-- <small>this is average for sellers in same category</small> -->
							</div>
						</div>
					</div>
					<!-- <div class="col-12 col-sm-3 col-md-3">
						<div class="p-2 h-100 ">
							<div class="profile_item">
								<h4> {{ $t('Chat') }}</h4>
							</div>
							<div class="profile_item">
                                <span v-if="seller_ratings_info.chat_response_rate > 90">{{ seller_ratings_info.chat_response_rate }}%</span>
                                <span v-else>90%</span>
								<br>
							</div>
						</div>
					</div> -->
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
                        <div v-for="(review, index) in reviewedProducts.data" :key="index" :class="['review-box', (review.parent_id == 1 ? 'thisischildcomment' : '')]">

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
										<div v-for="(img1, index) in review.image" :key="index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img1 != 'no-image'"> <img @error="imageLoadError" :src="baseurl+'/'+img1"> </span> </div>
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
								<div v-for="(img2, index) in review.image" :key="index" class="col-1 col-sm-1 col-md-1 col-lg-1"> <span v-if="img2"> {{ img2 }} <img @error="imageLoadError" :src="baseurl+'/'+img2"> </span> </div>
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
	<div class="row w_100per padding_problem">
		<div class="shimmer width_problem"> 
			<div class="h_30 w_100per mb_10"></div>
		</div>
	</div>

	<div class="row mb_30">
		<div class="col-md-3 col-lg-3">
			<div class="shimmer"><div class="h_4 w_100per"></div></div>
			<div class="shimmer"><div class="h_3 w_10"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_3 w_10"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
			<div class="shimmer"><div class="h_2 w_100per"></div></div>
		</div>
		<div class="col-md-9 col-lg-9">
			<div class="row">
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
				<div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
			</div>
		</div>
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
			sellerbanner:'',
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


			is_user: '',
            searchProduct:{},
            content:'',
            categories:{},
            brands:[],
            brand_next_page_url: '',
            brand_prev_page_url: '',
            all_next_page_url:'',
		}
	},
	components: {
    pagination,
    ProductGrid,
    QuickView
},
	methods:{
 		load_categories(){
            axios.get(this.$baseUrl+'/api/v1/search-categories').then(response => {
                this.categories = response.data.data;

                
                this.next_page_url = response.data.next_page_url;
                this.prev_page_url = response.data.prev_page_url;
            });
        },
		load_brands(){
		    axios.get(this.$baseUrl+'/api/v1/get-search-brands').then(response => {
				this.brands = response.data.data;
                this.brand_next_page_url = response.data.next_page_url;
                this.brand_prev_page_url = response.data.prev_page_url;
			});
		},
        async moreCategoris(url) {
            let   { data }  = await axios.get(url);
            let c = data.data;
             c.forEach(element => {
                 this.categories.push(element);
             });
             this.next_page_url = data.next_page_url;
             this.prev_page_url = data.prev_page_url;
        },

        async lessCategoris(url) {
            let   { data }  = await axios.get(url);
            let c = data.data;
             c.forEach(element => {
                 this.categories.pop(element);
             });
             this.next_page_url = data.next_page_url;
             this.prev_page_url = data.prev_page_url;
        },
        async moreBrands(url) {
            let   { data }  = await axios.get(url);
            let c = data.data;
             c.forEach(element => {
                 this.brands.push(element);
             });
             this.brand_next_page_url = data.next_page_url;
             this.brand_prev_page_url = data.prev_page_url;
        },
        async lessBrands(url) {
            let   { data }  = await axios.get(url);
            let c = data.data;
             c.forEach(element => {
                 this.brands.pop(element);
             });
             this.brand_next_page_url = data.next_page_url;
             this.brand_prev_page_url = data.prev_page_url;
        },
		load_search_product(page=1){
            let content = this.$route.params.content;
			let shop_slug = this.$route.params.slug;

            let categories = Array();
            $("input:checkbox[name=categorySelected]:checked").each(function() {
                categories.push($(this).val());
            });

            let brands = Array();
            $("input:checkbox[name=brandSelected]:checked").each(function() {
                brands.push($(this).val());
            });

            let formData = new FormData();
            formData.append('content', content); localStorage.setItem("serach_content", content);
            formData.append('page', 1);
            formData.append('upazila_id', localStorage.getItem('upazail_id'));
            formData.append('shop_slug', shop_slug); localStorage.setItem("shop_slug", shop_slug);
            formData.append('categories', categories); localStorage.setItem("serach_categories", categories);
            formData.append('brands', brands); localStorage.setItem("serach_brands", brands);
            formData.append('orederbyselect', $('.orederbyselect').val());  localStorage.setItem("serach_orederbyselect", $('.orederbyselect').val());
            formData.append('minprice', $('.minprice').val()); localStorage.setItem("serach_minprice", $('.minprice').val());
            formData.append('maxprice', $('.maxprice').val()); localStorage.setItem("serach_maxprice", $('.maxprice').val());
			axios.post(this.$baseUrl+'/api/v1/get-search-product', formData).then(response => {
				this.searchProduct = response.data.products;
                this.loading = true;
                this.all_next_page_url = response.data.products.next_page_url;
                this.all_prev_page_url = response.data.products.prev_page_url;
			});
        },

        async moreProducts(all_next_page_url) {
			$('.load_more_btn').attr('disabled', true);
			let lang = localStorage.getItem("lang");
			if(lang == 'bn'){
				$('.load_more_btn').html('লোড হচ্ছে.. <span class="spinner-border spinner-border-sm"></span>');
			}else{
			$('.load_more_btn').html('Loading.. <span class="spinner-border spinner-border-sm"></span>');
			}

            var url_string = all_next_page_url; 
            var url = new URL(url_string);
            var page = url.searchParams.get("page"); //This is page no.


            let content = this.$route.params.content;
			let shop_slug = this.$route.params.slug;

            let categories = Array();
            $("input:checkbox[name=categorySelected]:checked").each(function() {
                categories.push($(this).val());
            });

            let brands = Array();
            $("input:checkbox[name=brandSelected]:checked").each(function() {
                brands.push($(this).val());
            });
            let formData = new FormData();
            formData.append('content', content); localStorage.setItem("serach_content", content);
            formData.append('page', page);
            formData.append('upazila_id', localStorage.getItem('upazail_id'));
			formData.append('shop_slug', shop_slug); localStorage.setItem("shop_slug", shop_slug);
            formData.append('categories', categories); localStorage.setItem("serach_categories", categories);
            formData.append('brands', brands); localStorage.setItem("serach_brands", brands);
            formData.append('orederbyselect', $('.orederbyselect').val());  localStorage.setItem("serach_orederbyselect", $('.orederbyselect').val());
            formData.append('minprice', $('.minprice').val()); localStorage.setItem("serach_minprice", $('.minprice').val());
            formData.append('maxprice', $('.maxprice').val()); localStorage.setItem("serach_maxprice", $('.maxprice').val());
			await axios.post(this.$baseUrl+'/api/v1/get-search-product', formData).then(response => {
                let c = response.data.products.data;
                let that = this;
                c.forEach(element => {
                    that.searchProduct.data.push(element);
                    $('.load_more_btn').attr('disabled', false);
                    let lang = localStorage.getItem("lang");
                    if(lang == 'bn'){
                        $('.load_more_btn').html('আরো লোড করুন <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
                    }else{
                        $('.load_more_btn').html('Load More <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
                    }
                });
                this.all_next_page_url = response.data.products.next_page_url;
			});
        },
		imageLoadError(event){
			event.target.src = "/images/notfound.png";
		},
		load_products_by_shop_slug(){
			let slug = this.$route.params.slug;
			axios.get(this.$baseUrl+'/api/v1/get-products-by-seller-slug/'+slug).then(response => {
				this.ratings = response.data.ratings;
				this.SellerProfile = response.data.profile;
				this.sellerLogo= response.data.profile.shopinfo.logo;
				this.sellerbanner = '/'+response.data.profile.shopinfo.banner;
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
		document.title = "MabiY | Shop"; 
		this.load_seller_products_comments();

        this.$forceUpdate(
            setTimeout(() => {
                 this.load_search_product();
            },300)
         );
        this.load_brands();
        this.load_search_product();
        this.load_categories();


	}
}


</script>

