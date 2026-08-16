<template>
<div>

	<section v-if="wishlistData.total > 0" id="cart-page">
		<div class="container">
			<div class="row cart-page-container">
				<div class="col-12 col-sm-12 col-md-12 col-lg-12">
					<div class="row shoping-cart-text">
						<div class="col-12 col-sm-12 col-md-12">
							<h4>{{ $t('Wishlist') }}</h4>
						</div>
					</div>
					<div class="cart-calculation">
						<table  class="table text-left table-striped" width="100%">
							<thead>
								<tr>
									<th width="15%"> {{ $t('Image') }}</th>
									<th width="45%">{{ $t('Product Name') }}</th>
									<th width="20%"> {{ $t('Price') }}</th>
									<th width="20%" style="text-align: right;"> {{ $t('Action') }}</th>
								</tr>
							</thead>
						
						
							<tbody>
							
								<tr v-for="(wishlist, index) in wishlistData.wishlist" :key="index" class="cart_product_group">
								
									<td> <div class="product-cart-img"> <img @error="imageLoadError" :src="baseurl+'/'+wishlist.product.default_image" alt=""> </div> </td>
									<td> 
										<div class="table-item"> 
											<router-link :to="{ name: 'product', params: {slug: wishlist.product.slug } }"> {{ wishlist.product.title }} </router-link>
											<p>{{wishlist.product.short_description}}</p>
										</div> 
									</td>
									<td> 
										<div class="table-item">BDT {{ wishlist.price }}</div>
										<div class="old-price"> <del v-if="wishlist.price_before_offer > wishlist.price">BDT {{ wishlist.price_before_offer }}</del> </div>
									</td>
									
									
									<td style="text-align: right;"> 

										<button style="width: 60%;" class="btn btn-danger btn-sm text-white" title="Remove this item" @click.prevent="removeWishlist(wishlist.id)">
											<i class="fa fa-trash text-white"></i> Remove
										</button>

										<!-- <button v-if="wishlist.product.product_type != 'variable'" class="btn btn-primary btn-sm text-white" title="Add to cart" @click.prevent="addToCart(wishlist.product.id,wishlist.id)"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
										<button v-else class="btn btn-primary btn-sm text-white" title="Go to product"> <router-link :to="{ name: 'product', params: {slug: wishlist.product.slug } }"><i class="fa fa-info-circle"></i> Details</router-link></button> -->


										<span v-if="wishlist.product.in_stock > 0 && wishlist.product.qty > 0">
											<span v-if="wishlist.product.product_type == 'variable'">
												<router-link :to="{ name: 'product', params: {slug: wishlist.product.slug } }"> <button :class="'btn btn-primary btn-sm mb-2'"> {{ $t('Details') }}</button>  </router-link>
											</span>
											<span v-else class="compare_spinner">
												<button style="width:45%" :class="'btn btn-primary btn-sm disabledbtn'+wishlist.product.id" @click.prevent="addToCart(wishlist.product.id, wishlist.id)"> <i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }} </button>
											</span>
										</span>
										<span v-else>
											<br>
											<button class="btn btn-sm mb-2 out_of_stock"> {{ $t('Out Of Stock') }} </button>
											<br>
											<button  style="width: 60%;" :class="'btn btn-primary btn-sm disabledbtn'+wishlist.product.id" @click.prevent="restockRequest(wishlist.product.id, wishlist.product.seller_id)"> <i class="fa fa-bicycle mr-2"></i> {{ $t('Restock Request') }} </button>

										</span>





									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section v-else  id="cart-page-shimmer">
		<div class="container">
			<div class="row">
				<div v-if="wishlistData.total == 0" class="col-md-12">
					<p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
					<h4> {{ $t('No product in wishlist') }} ! </h4>
					<p> <router-link :to="{name: 'products'}"> {{ $t('Continue shopping') }}</router-link></p>
				</div>
				<div v-else class="col-md-12 simar_lodding">
					<div class="container">
						<div class="row cart-page-container">
							<div class="col-12 col-sm-12 col-md-12 col-lg-12">
								<div class="row shoping-cart-text">
									<div class="col-12 col-sm-12 col-md-12">

										<div class="shimmer">
											<div class="h_3 w_10 ml_10 mt_5"></div>
										</div>

									</div>
								</div>
								<div class="cart-calculation">
									<table  class="table text-left cart_table" width="100%">
										<thead>
											<tr>
												<th width="5%"> <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
												<th width="30%"> <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
												<th width="20%"> <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
												<th width="15%"> <div class="shimmer"><div class="h_2 w_5"></div></div></th>
												<th width="20%">  <div class="shimmer"><div class="h_2 w_5"></div></div> </th>
												<th width="5%" style="text-align: right;">  <div class="shimmer"><div class="h_2 w_5"></div></div></th>
											</tr>
										</thead>
									
									
										<tbody>
											<tr class="group_header mb-3">
												<td colspan="7">
													<small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
												</td>
											</tr> 
											<tr class="cart_product_group">
												<td> <div class="product-cart-img"><div class="shimmer"><div class="h_10 w_6"></div></div>  </div> </td>
												<td> <div class="shimmer"><div class="h_3 w_10"></div></div>  </td>
												<td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
												<td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
												<td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
												<td> <div class="shimmer"><div class="h_3 w_2"></div></div></td>
											</tr>

											<tr class="group_header mb-3">
												<td colspan="7">
													<small><div class="shimmer"><div class="h_2 w_7"></div></div>  </small>
												</td>
											</tr> 
											<tr class="cart_product_group">
												<td> <div class="product-cart-img"><div class="shimmer"><div class="h_10 w_6"></div></div>  </div> </td>
												<td> <div class="shimmer"><div class="h_3 w_10"></div></div>  </td>
												<td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
												<td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
												<td> <div class="shimmer"><div class="h_3 w_5"></div></div> </td>
												<td> <div class="shimmer"><div class="h_3 w_2"></div></div></td>
											</tr>
							
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
</template>


<script>
import Form from 'vform'
import axios from 'axios'

export default {
	data(){
		return{
			carts:'',
			baseurl:'',
			sub_total:'',
			cartCount:'',
			loading:0,
		}
	},

	methods:{
		restockRequest(product_id, seller_id){
			$('.disabledbtn'+product_id).attr('disabled', true);
			$('.disabledbtn'+product_id).html('<div class="spinner-border spinner-border-sm"></div>');
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
	
			axios.post(this.$baseUrl+'/api/v1/restock-request', {product_id:product_id,seller_id:seller_id,session_key:session_key},axiosConfig ).then(response => {
				if(response.data.status == 1){
					swal({
						title: response.data.message,
						icon: "success",
						timer: 10000
					}).then(()=>{
						$('.disabledbtn'+product_id).attr('disabled', false);
						if(lang == 'bn'){
							$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> রিস্টক রিকোয়েস্ট');
						}else{
							$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> Restock Request');
						}
					});
				}else{
					swal( "Oops", response.data.message, "error");
					$('.disabledbtn'+product_id).attr('disabled', false);
					if(lang == 'bn'){
						$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> রিস্টক রিকোয়েস্ট');
					}else{
						$('.disabledbtn'+product_id).html('<i class="fa fa-bicycle"></i> Restock Request');
					}
				}
			});
		},

		imageLoadError(event){
			event.target.src = "/images/notfound.png";
		},	
		scrollToTop(){
			window.scrollTo(0,0);
		},
		wishlist(){
			this.$store.dispatch('loadedWishlist');
		},
		addToCart(product_id,wishlist_id){
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
			let that = this;
			axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty:1,session_key:session_key},axiosConfig ).then(response => {
				$('.disabledbtn'+product_id).attr('disabled', true);
				if(response.data.status == 1){
					this.$store.dispatch('loadedCart');
					jQuery('.back_to_cart').trigger('click');
					swal({
					title: "Product added to cart Successfully.",
					icon: "success",
					timer: 3000
					}).then(()=>{
						that.removeWishlist(wishlist_id);
						$('.disabledbtn'+product_id).attr('disabled', false);
						if(lang == 'bn'){
							$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
						}else{
							$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
						}
					});
				}else{
					swal ( "Oops", response.data.message, "error");
					$('.disabledbtn'+product_id).attr('disabled', false);
					if(lang == 'bn'){
						$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
					}else{
						$('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
					}
				}
			});
		},

		removeWishlist(wishlist_id){
          let session_key = localStorage.getItem("session_key");
          let formData = new FormData();
          formData.append('wishlist_id', wishlist_id);
          formData.append('session_key', session_key);
          let token = localStorage.getItem("token");
          let axiosConfig = {
            headers: {
              'Content-Type': 'application/json;charset=UTF-8',
              "Access-Control-Allow-Origin": "*",
              'Authorization': 'Bearer '+token
            }
          }
          axios.post(this.$baseUrl+'/api/v1/remove-wishlist', formData, axiosConfig).then(response =>{
            if(response.data.status == '1'){
              this.$store.dispatch('loadedWishlist');
              swal({
                title: "Product has been removed from your wishlist.",
                icon: "success",
                timer: 3000
              });
            }else{
              swal ( "Oops", response.data.message, "error");
            }
          }).catch(function(){
            swal ( "Oops" ,  'Something went wrong.',  "error");
          });
      },

	},
	computed:{
		wishlistData(){
			this.loading = 1;
			return this.$store.getters.getLoadedWishlist;
		},
		user(){
		  return this.$store.getters.getLoadedUser.id;
		},
		authenticated(){
		  return this.$store.getters.getAuthenticated;
		}
	},
	
	mounted(){
		this.wishlist();
		this.scrollToTop();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | Wishlist";  
	}
  
}
</script>





