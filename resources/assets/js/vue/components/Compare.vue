<template>
<div>

<section id="register-page" v-if="compares.status >= 0">
    <div class="container">
        <div class="row">
            <div  v-if="compares.total > 0" class="col-md-12 compare_wrapper table-responsive">
                <table class="table table-bordered" width="100%">
                    <thead>
                    <tr>
                        <th width="15%">{{ $t('Product') }}</th>
                        <th width="10%"> {{ $t('Price') }}</th>
                        <th width="10%">{{ $t('Product Type') }}</th>
                        
                        <th width="15%">{{ $t('Specification') }}</th>
                        
                        <th width="10%">{{ $t('Custom Options') }}</th>
                        <th width="20%">{{ $t('Product Description') }}</th>
                        <th width="15%">{{ $t('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody class="compare_table">
                        <tr v-for="(data, index) in compares.compares" :key="index">
                            <td>
                               <router-link :to="{ name: 'product', params: {slug: data.product.slug } }">
                                <div class="compare_image">
                                      <img @error="imageLoadError" :src="baseurl+'/'+data.product.default_image" />
                                </div>
                                
                                <p>{{ data.product.title }}</p>
                               </router-link>
                            </td>
                            <td><span>BDT {{ data.product.price_after_offer }}</span></td>
                            <td>{{ data.product.product_type }}</td>
                           
                            <td> 
                                <span v-for="(sp, index) in data.specification" :key="index">
                                  <p v-if="sp.meta_value"> 
                                    <strong class="text-capitalize">{{ sp.meta_key.replace(/_/g, " ") }}</strong> : {{sp.meta_value}} 
                                  </p> 
                                </span>
                            </td>
                            

                            <td>
                              <span v-for="(metas, index) in data.meta" :key="index"> 
                                <span v-if="metas.meta_key == 'custom_options'">
                                  <span v-for="(options, index) in metas.decoded_custom_options" :key="index"> 
                                    <ul>
                                      <li>
                                        <b>{{options.title}} : </b> <br> <span v-for="(val, index) in options.value" :key="index"> <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>  {{ val.title }} <br></span>
                                        </li>
                                    </ul>
                                  </span>
                                </span>
                              </span>
                            </td>
                            <td> {{  data.product.short_description.substr(0, 150)+".." }}</td>
                            <td>
                              <span v-if="data.product.in_stock > 0 && data.product.qty > 0">
                                <span v-if="data.product.product_type == 'variable'">
                                  <router-link  style="width: 100%;" :to="{ name: 'product', params: {slug: data.product.slug } }"> <button :class="'btn btn-primary btn-sm mb-2'"> {{ $t('Details') }}</button>  </router-link>
                              </span>
                                <span v-else class="compare_spinner">
                                  <button  style="width: 100%;" :class="'btn btn-primary btn-sm mb-2 disabledbtn'+data.product.id" @click.prevent="addToCart(data.product.id)"> <i class="fa fa-shopping-basket"></i> {{ $t('Add To Cart') }} </button>
                                </span>
                              </span>
                              <span v-else>
                                <button  style="width: 100%;" class="btn btn-sm mb-2 out_of_stock text-center"> {{ $t('Out Of Stock') }} </button>
                                <br>
                                <button style="width: 100%;" :class="'mt-2 btn btn-primary btn-sm disabledbtn'+data.product.id" @click.prevent="restockRequest(data.product.id, data.product.seller_id)"> <i class="fa fa-bicycle mr-2"></i> {{ $t('Restock Request') }} </button>
                              </span>

                              <br>
                              <button class="btn btn-danger btn-sm compare_spinner mt-1" style="width: 100%;"  @click.prevent="removeCompare(data.product.id)"> <i class="fa fa-trash" aria-hidden="true"></i> {{ $t('Remove') }}</button>


                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="col-md-12 compare_not_found_wrapper">
              <div class="row promotion_page">
                  <div class="col-md-12 text-center">
                      <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                      <br>
                      <h3>{{ $t('No product found in your compare list') }}.</h3>
                  </div>
              </div>
            </div>
        </div>
    </div>
</section>

<section id="register-page" v-else>
  <div class="container">
    <div class="row compare_not_found">
      <table class="table table-bordered" width="100%">
          <thead>
          <tr>
              <th width="20%"> <div class="shimmer"><div class="h_2 w_100per"></div></div> </th>
              <th width="10%"> <div class="shimmer"><div class="h_2 w_100per"></div></div> </th>
              <th width="10%"> <div class="shimmer"><div class="h_2 w_100per"></div></div> </th>
              
              <th width="15%"> <div class="shimmer"><div class="h_2 w_100per"></div></div> </th>
              
              <th width="10%"> <div class="shimmer"><div class="h_2 w_100per"></div></div> </th>
              <th width="20%"> <div class="shimmer"><div class="h_2 w_100per"></div></div></th>
              <th width="10%"> <div class="shimmer"><div class="h_2 w_100per"></div></div></th>
          </tr>
          </thead>
          <tbody class="compare_table">
            <tr>
                <td> 
                  <div class="shimmer">  
                    <div class="h_15 w_100per"></div> 
                    <div class="h_3 w_100per"></div> 
                  </div>
                </td>
                <td> <div class="shimmer">  <div class="h_3 w_100per"></div> </div></td>
                <td> <div class="shimmer">  <div class="h_3 w_100per"></div> </div></td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>

                <td> 
                  <div class="shimmer"> 
                      <div class="h_3 w_100per"></div> 
                      <div class="h_3 w_100per"></div> 
                      <div class="h_3 w_100per"></div> 
                  </div>
                </td>
            </tr>
            <tr>
                <td> 
                  <div class="shimmer">  
                    <div class="h_15 w_100per"></div> 
                    <div class="h_3 w_100per"></div> 
                  </div>
                </td>
                <td> <div class="shimmer">  <div class="h_3 w_100per"></div> </div></td>
                <td> <div class="shimmer">  <div class="h_3 w_100per"></div> </div></td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>

                <td> 
                  <div class="shimmer"> 
                      <div class="h_3 w_100per"></div> 
                      <div class="h_3 w_100per"></div> 
                      <div class="h_3 w_100per"></div> 
                  </div>
                </td>
            </tr>
            <tr>
                <td> 
                  <div class="shimmer">  
                    <div class="h_15 w_100per"></div> 
                    <div class="h_3 w_100per"></div> 
                  </div>
                </td>
                <td> <div class="shimmer">  <div class="h_3 w_100per"></div> </div></td>
                <td> <div class="shimmer">  <div class="h_3 w_100per"></div> </div></td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>
                <td> 
                  <div class="shimmer"> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                      <div class="h_2 w_100per"></div> 
                  </div>
                </td>

                <td> 
                  <div class="shimmer"> 
                      <div class="h_3 w_100per"></div> 
                      <div class="h_3 w_100per"></div> 
                      <div class="h_3 w_100per"></div> 
                  </div>
                </td>
            </tr>
          </tbody>
      </table>
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
      sellers:{},
      compareList:[],
		}
	},
	components:{
		Leftsidebar,
        pagination
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





    addToCart(product_id){
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


      removeCompare($product_id){
          let session_key = localStorage.getItem("session_key");
          let formData = new FormData();
          formData.append('product_id', $product_id);
          formData.append('session_key', session_key);
          let token = localStorage.getItem("token");
          let axiosConfig = {
            headers: {
              'Content-Type': 'application/json;charset=UTF-8',
              "Access-Control-Allow-Origin": "*",
              'Authorization': 'Bearer '+token
            }
          }
          axios.post(this.$baseUrl+'/api/v1/remove-compare', formData, axiosConfig).then(response =>{
            if(response.data.status == '1'){
              this.$store.dispatch('loadedCompares');
              swal({
                title: "Product removed from compare list Successfully.",
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




      scrollToTop() {
          window.scrollTo(0,0);
        }
      },
      computed:{
          compares(){
              return this.$store.getters.getLoadedCompare;
          },
          logged_in_user(){
              return this.$store.getters.getLoadedUser.user;
          }
      },
      mounted(){
        this.baseurl = this.$baseUrl;
        this.scrollToTop();
        document.title = "MabiY Shop | Compare"; 
      }

}
</script>