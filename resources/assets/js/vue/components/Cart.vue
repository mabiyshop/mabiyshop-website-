<template>
<div>

	<section v-if="cartData.sub_total > 0" id="cart-page">
		<div class="container">
			<div class="row cart-page-container">
				<div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
					<div class="row shoping-cart-text">
						<div class="col-12 col-sm-12 col-md-12">
							<h4>{{ $t('Shopping Cart') }}</h4>
						</div>
					</div>
					<div class="cart-calculation">
						<table  class="table text-left cart_table" width="100%">
							<thead>
								<tr>
									<th width="5%"> {{ $t('Image') }}</th>
									<th width="30%">{{ $t('Product Name') }}</th>
									<th width="20%"> {{ $t('Price') }}</th>
									<th width="15%"> {{ $t('Quantity') }}</th>
									<th width="20%"> {{ $t('Total') }}</th>
									<th width="5%" style="text-align: right;"> {{ $t('Remove') }}</th>
								</tr>
							</thead>
						
						
							<tbody v-for="(cartgroup,index) in cartData.cart" :key="index">
								
								<tr class="group_header mb-3">
									<td colspan="7">
										<small>{{ $t('Shipped by') }} :  <b> <router-link class="text-dark" :to="{ name: 'shop', params: {slug: cartgroup.shop_info.shop_slug } }" >{{cartgroup.shop_info.shop_name}}</router-link></b></small>
									</td>
								</tr> 

								<tr v-for="(cart, index) in cartgroup.items" :key="index" class="cart_product_group">
								
									<td> <div class="product-cart-img"> <img @error="imageLoadError" :src="baseurl+'/'+cart.product.default_image" alt=""> </div> </td>
									<td> 
										<div class="table-item"> 
											<router-link :to="{ name: 'product', params: {slug: cart.product.slug } }"> {{ cart.product.title }} </router-link>
											<span  v-if="cart.product_type == 'variable'">
												<p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in cart.variable_options" :key="key"> <b>{{key}}</b> : {{vOption}}</p>
											</span>
											<span  v-if="cart.product_type == 'digital'">
												<p v-if="cart.variable_options" class="mb-0 text-capitalize font-13"> <b>Contact Number</b> : {{cart.variable_options}}</p>
											</span>
										</div> 
									</td>
									<td> 
										<div class="table-item">BDT {{ cart.price }}</div>
										<div class="old-price"> <del v-if="cart.price_before_offer > cart.price">BDT {{ cart.price_before_offer }}</del> </div>
									</td>
									
									<td v-if="cart.product.product_type == 'simple' || cart.product.product_type == 'variable'"> 
										<div class="table-item">
											<div class="full-quantity">
												<button @click.prevent="updateQty(cart.row_id, -1)" class="cart_minus_btn"> <div class="crt cart-minus"> - </div> </button> 
												<div class="crt cart-qty"> <input :id="'Product'+index" type="text" :data-catQty="cart.qty" class="cart-qty-input" readonly :value="cart.qty" :data-rowid="cart.row_id" :data-productid="cart.product_id" :data-userid="cart.user_id"> </div>
												<button @click.prevent="updateQty(cart.row_id, 1)" class="cart_minus_btn"><div class="crt cart-plus" > + </div></button> 
											</div>
										</div>
									</td>
									<td v-else style="text-align: center;"> - </td>

									<td> 
										<div class="table-item">BDT {{ cart.price*cart.qty }} </div>
									</td>
									
									<td style="text-align: right;"> 
										<div class="table-item product-remove" @click.prevent="removeItem(cart.row_id)">
										<i class="fa fa-trash"></i>
										</div> 
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
					<div class="note">
						<h3>{{ $t('Order Summary') }}</h3>
					</div>
					<div class="payment-calculation">
						<ul>
							<li> <b>{{ $t('Sub Total') }}  </b>  <span v-if="sub_total"> BDT {{ sub_total }}</span> <span v-else> BDT {{ cartData.sub_total }} </span></li>
							<li> <b>{{ $t('Total Item') }}(s) </b>  <span>{{ cartData.total_items }}</span></li>
							<li> <b> {{ $t('Total Price') }}</b>  <span>BDT {{ sub_total }}  </span></li>
							
						</ul>
					</div>
					<div class="procced-checkout">
						<ul>
							<li> <button class="btn btn-primary site_color1" @click.prevent="proceed()">{{ $t('Proceed To Checkout') }}</button> </li>
						</ul>    
					</div>
				</div>
			</div>
		</div>
	</section>

	

	<section v-else  id="cart-page-shimmer">

		<div class="container">
			<div class="row">
				<div v-if="cartData.sub_total == 0 || cartData.status == 0" class="col-md-12">
					<p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
					<h4> {{ $t('No product in cart') }} ! </h4>
					<p> <router-link :to="{name:'products'}"> {{ $t('Continue shopping') }} </router-link></p>
				</div>
				<div v-else class="col-md-12 simar_lodding">
					<div class="container">
						<div class="row cart-page-container">
							<div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
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
							<div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
								<div class="note">
									<div class="shimmer"><div class="h_3 w_100per mb_5"></div></div>
								</div>
								<div class="payment-calculation">
									<ul>
										<li> <div class="shimmer"><div class="h_2 w_100per mb_10"></div></div> </li>
										<li> <div class="shimmer"><div class="h_2 w_100per mb_10"></div></div> </li>
										<li>  <div class="shimmer"><div class="h_2 w_100per mb_10"></div></div> </li>
										
									</ul>
								</div>
								<div class="procced-checkout">
									<ul>
										<li>  <div class="shimmer"><div class="h_3 w_100per mb_10"></div></div> </li>
									</ul>    
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
		imageLoadError(event){
			event.target.src = "/images/notfound.png";
		},
		proceed(){
			this.$router.push({name:'checkout'});
		},
		updateQty($rowId, $update){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			let formData = new FormData();
			formData.append('rowId', $rowId);
			formData.append('update', $update);
			axios.post(this.$baseUrl+'/api/v1/update-qty', formData,axiosConfig).then(response =>{
				if(response.data.status == '1'){
					this.$store.dispatch('loadedCart');
				}else{
					swal ( "Oops", response.data.message, "error");
				}

			});
		},
		updateShippingOption(shipping_method, shipping_cost, rowId){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			let formData = new FormData();
			formData.append('shipping_method', shipping_method);
			formData.append('shipping_cost', shipping_cost);
			formData.append('rowId', rowId);
			axios.post(this.$baseUrl+'/api/v1/update-shipping-option', formData,axiosConfig).then(response =>{
				if(response.data.status == '1'){
					this.$store.dispatch('loadedCart');
					swal({
						title: "Shipping method updated Successfully.",
						icon: "success",
						timer: 3000
					});
				}else{
					swal ( "Oops", response.data.message, "error");
				}

			});
		},
		removeItem($row_id){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
			let formData = new FormData();
			formData.append('row_id', $row_id);

			axios.post(this.$baseUrl+'/api/v1/remove-cart-item',formData,axiosConfig).then(response =>{
				if(response.data.status == 1){
					this.$store.dispatch('loadedCart');
				}else{
					swal ("Oops" ,response.data.message,  "error");
				}
			});
		},		
		scrollToTop(){
			window.scrollTo(0,0);
		}

	},
	computed:{
		cartData(){
			this.sub_total = this.$store.getters.getLoadedCart.sub_total;
			this.loading = 1;
			return this.$store.getters.getLoadedCart;
		},
		user(){
		  return this.$store.getters.getLoadedUser.id;
		},
		authenticated(){
		  return this.$store.getters.getAuthenticated;
		}
	},
	
	mounted(){
		this.scrollToTop();
		const plugin = document.createElement("script");
		plugin.setAttribute( "src",this.$frontendUrl+"/assets/js/parts/cart.js");
		plugin.async = true;
		document.body.appendChild(plugin);
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | Cart";  
	}
  
}
</script>





