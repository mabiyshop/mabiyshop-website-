<template>
<div>


<section v-if="authenticated" id="profile-page">
    <div class="container">
	<div class="col-md-12 account_wrapper bg_white">
		<div class="row profile">
			<div class="col-md-1">
				<img @error="imageLoadError" v-if="userData.avatar != null" :src="baseurl+'/assets/images/'+userData.avatar" >
				<img @error="imageLoadError" v-else :src="baseurl+'/assets/images/avater.jpg'" >
			</div>
			<div class="col-md-11">
				<div class="username">
					<b>{{ userData.name  }}</b>
					<p>{{ userData.email }}</p>
				</div>
			</div>
		</div>
        <div class="row account_box">
            <div class="col-md-3 col-lg-3 profile-navigation">
                <div class="profile-nav">
                    <Leftsidebar></Leftsidebar>
                </div>
            </div>
            <div class="col-md-9 col-lg-9">
				<div id="tracking_page">
					<div class="card-body">
						<div class="track">
							<div  class="step active" > <span class="icon"> <i class="fa fa-check"></i> </span> <span class="text">{{ $t('Order Placed') }}</span> </div>
							<div  :class="['step', (single_product.status >= 1 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 1 ? 'fa-check' : 'fa-spinner')]"></i> </span> <span class="text"> {{ $t('Pending') }}</span> </div>
							<div v-if="single_product.status == 3" :class="['step', (single_product.status >= 3 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 3 ? 'fa-check' : 'fa-hand-pointer-o')]"></i> </span> <span class="text"> {{ $t('On Hold') }}</span> </div>
							<div v-else :class="['step', (single_product.status >= 2 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 2 ? 'fa-check' : 'fa-refresh')]"></i> </span> <span class="text"> {{ $t('Accepted') }}</span> </div>
							<div v-if="single_product.status == 4"  :class="['step', (single_product.status == 4 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status == 4 ? 'fa-check' : 'fa-times')]"></i> </span> <span class="text">{{ $t('Failed') }}</span> </div>
							<div v-if="single_product.status == 5" :class="['step canceled_single_shipping', (single_product.status == 5 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status == 5 ? 'fa-check' : 'fa-ban')]"></i> </span> <span class="text">{{ $t('Canceled') }}</span> </div>
							<div v-if="single_product.status != 6" :class="['step', (single_product.status >= 9 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 9 ? 'fa-check' : 'fa-truck')]"></i> </span> <span class="text">{{ $t('Out for Delivery') }}</span> </div>
							<div v-if="single_product.status != 17" :class="['step', (single_product.status >= 10 ||  single_product.status==6? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 10 || single_product.status==6 ? 'fa-check' : 'fa-gift')]"></i> </span> <span class="text">{{ $t('Delivered') }}</span> </div>
							<div v-if="single_product.status == 11 || single_product.status == 12 && single_product.status != 6" :class="['step', (single_product.status >= 11 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 11 ? 'fa-check' : 'fa-check-circle')]"></i> </span> <span class="text">{{ $t('Return Requested') }}</span> </div>
							<div v-if="single_product.status == 11 || single_product.status == 12 && single_product.status != 6" :class="['step', (single_product.status >= 12 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status >= 12 ? 'fa-check' : 'fa-check-circle')]"></i> </span> <span class="text">{{ $t('Return Accepted') }}</span> </div>
							<div v-if="single_product.status == 16 && single_product.status != 6" :class="['step', (single_product.status >= 12 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status == 16 ? 'fa-check' : 'fa-check-circle')]"></i> </span> <span class="text">{{ $t('Damage') }}</span> </div>
							<div v-if="single_product.status == 17 && single_product.status != 6" :class="['step', (single_product.status >= 17 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status == 17 ? 'fa-check' : 'fa-check-circle')]"></i> </span> <span class="text">{{ $t('Loss') }}</span> </div>
							<div v-if="single_product.status == 17 && single_product.status != 6" :class="['step']" > <span class="icon"> <i :class="['fa fa-gift']"></i> </span> <span class="text">{{ $t('Delivered') }}</span> </div>
							<div :class="['step', (single_product.status == 6 ? 'active' : '')]" > <span class="icon"> <i :class="['fa', (single_product.status == 6 ? 'fa-check' : 'fa-check-circle')]"></i> </span> <span class="text">{{ $t('Completed') }}</span> </div>
						</div>


						<div class="track_order_id"></div>
						<article class="card">
							<div class="card-body row">
								<div class="col"> <strong>{{ $t('Order ID') }} </strong> <br> MS{{single_order.created_at|prefix_year}}{{ single_order.id }} </div>
								<div class="col"> <strong>{{ $t('Shipping BY') }}</strong> <br><router-link :title="shopinfo.name" :to="{ name: 'shop', params: {slug: shopinfo.slug } }"> {{ shopinfo.name }} </router-link></div>
								<!-- <div class="col"> <strong>Shipping Method</strong> <br> {{ single_product.shipping_method.replace('_', ' ') }} </div> -->
								<div class="col"> <strong> {{ $t('Shipping Status') }} </strong> <br> <span v-for="(status, index) in allStatuses" :key="index"> <b v-if="single_product.status == status.id" :style="{color: status.color_code}">{{ status.title }}</b> </span>  </div>
							</div>
						</article>
					</div>
				</div>
				<div class="row pb-5">
					<div class="col-md-12 text-right"> 
						 <router-link :to="{ name: 'orderDetails', params: { id: single_order.id} }"><button class="btn btn-primary sm" > <i class="fa fa-arrow-left mr-2" aria-hidden="true"></i> {{ $t('Back') }}  </button> </router-link>
					</div>
				</div>


            </div>
        </div>
    </div>
	</div>
</section>

<section v-else id="profile-page">
    <div class="container">
	<div class="col-md-12 account_wrapper bg_white">
		<div class="row profile">
			<div class="col-md-1">
				<div class="shimmer">
					<div class="circle_100"></div>
				</div>
			</div>
			<div class="col-md-11">
				<div class="shimmer">
					<div class="h_2 w_10 mt_25"></div>
					<br/>
					<div class="h_2 w_10"></div>
				</div>
			</div>
		</div>
        <div class="row account_box">
            <div class="col-md-3 col-lg-3 profile-navigation">
                <div class="profile-nav">
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
					<div class="shimmer ">
						<div class="h_3 w_2 mb_10"></div>
						<div class="h_3 w_13 mb_10"></div>
					</div>
                </div>
            </div>
            <div class="col-md-9 col-lg-9">
                <div class="form-section" id="myaccountform">
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
                    <div class="shimmer "><div class="h_3 w_100per mb_10"></div></div>
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
import swal from 'sweetalert';
import Leftsidebar from './default/Leftsidebar';
import moment from 'moment';
import pagination from 'laravel-vue-pagination';

export default {
	mode:'history',
	data(){
		return{
			authenticated: false,
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
            orders:{},
			orders_status:'',
			single_order:'',
			single_product:'',
			shopinfo:'',
			allStatuses:'',
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
        load_single_order(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
            let ids = this.$route.params.order_id;
            const myArray = ids.split(",");
            let order_id = myArray[0];
            let product_id = myArray[1];
            axios.get(this.$baseUrl + "/api/v1/get-return-product",{params: {order_id:order_id, product_id:product_id}}, axiosConfig).then((response) => {
				if(response.data.status == 1){
					this.single_order = response.data.order;
					this.address = response.data.shipping_address;
					this.single_product = response.data.products;
					this.shopinfo = response.data.shopinfo;
					this.allStatuses = response.data.allStatuses;
				}else if(response.data.status == 2){
					swal ( "Oops", response.data.message, "error");
					this.$router.push({ name: 'myorder'});
				}else if(response.data.status == 3){
					swal ( "Oops", response.data.message, "error");
					this.$router.push({ name: 'orderDetails', params: { id: order_id } });
				}

            });
        },

		checkAuth(){
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer '+token
                }
            }
            axios.post(this.$baseUrl+'/api/v1/checkauth',{},axiosConfig).then(response =>{
                if(response.data.status != 1){
                    this.$router.push({name:'login'});
                    swal ( "Oops", response.data.message, "error");
                }else{
                    this.authenticated = true;
                }
            });
        },
		getUserDetails(){
			let token = localStorage.getItem("token");
			
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
			axios.get(this.$baseUrl+'/api/v1/get-user-details', axiosConfig).then(response =>{
				this.userData = response.data;
			}).catch(function(){
			  swal ( "Oops" ,  'Something went wrong',  "error" );
			});
		},
    	scrollToTop() {
            window.scrollTo(0,0);
        }


	},
	mounted(){
		this.scrollToTop();
		this.checkAuth();
		this.load_single_order();
        this.baseurl = this.$baseUrl;
		this.getUserDetails();
		document.title = "MabiY Shop | My Order Track";  
	}
}
</script>