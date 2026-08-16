<template>
<div>


<section v-if="authenticated" id="profile-page">
    <div class="container">
	<div class="col-md-12 account_wrapper bg_white">
		<div class="row profile">
			<div class="col-md-1">
				<img @error="imageLoadError" :src="baseurl+'/'+userData.avatar" >
			</div>
			<div class="col-md-11">
				<div class="username">
					<b>{{ userData.name  }}</b>
					<p class="mb-0">{{ userData.email }}</p>
					<p v-if="userData.user_type == 2"> <span class="badge badge-danger">{{$t('Corporate Customer')}}</span> </p>
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
                <div class="order_page"> 
                    <div class="row">
						<div class="col-6">
							<h5 class="text-left mb-0 text-uppercase mt-3 text-primary">My Coupons</h5>
						</div>
						<div class="col-6">
							<p class="text-right"> <button data-toggle="modal" data-target="#couponBuyModal" class="btn btn-primary"> <i class="fa fa-gift"></i> Buy Coupon</button> </p>

							<!-- Modal -->
							<div class="modal fade" id="couponBuyModal" tabindex="-1" aria-labelledby="couponBuyModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="withdrawalModalLabel">Buy coupon with loyalty points</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group">
                                                <label for="withdrawalAmount">Coupon<span class="text-danger">*</span></label>
                                                <select name="coupon" class="form-control" id="coupon">
                                                    <option disabled selected>-- Select Coupon --</option>
                                                    <option v-for="(coupon, index) in coupon_for_sale" :key="index" :value="coupon.id">
                                                        {{coupon.code }} - {{coupon.deduction_points}} Points
                                                    </option>
                                                </select>
                                            </div>

                                            <p class="text-right"><button @click.prevent="buyNow()" type="button" class="btn btn-primary">Buy Now</button></p>
                                        </div>
            
                                    </div>
                                </div>
							</div>
						</div>
                    </div>
                    <div v-if="mycoupons && availableCoupons"  style="overflow-x:auto;">
                        <table class="table table-bordered user-orders-full" width="100%">
                            <thead>
                            <tr>
                                <th width="10%">{{ $t('ID') }}</th>
                                <th width="10%">{{ $t('Code') }}</th>
                                <th width="30%">{{ $t('Amount') }}</th>
                                <th width="20%">{{ $t('Use Type') }}</th>
                                <th width="30%">{{ $t('Expire Date') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            	<tr v-for="(mycoupons, index) in mycoupons" :key="index" >
									<td>#{{index + 1}}</td>
									<td>{{mycoupons.code}}</td>
									<td>BDT {{mycoupons.amount}}</td>
									<td>
                                        <span v-if="mycoupons.use_type == 1" class="badge badge-info">Global</span>
                                        <span v-else class="badge badge-info">Loyalty</span>
                                    </td>
									<td>{{mycoupons.expire}}</td>
								</tr>	
                            </tbody>
                        </table>
                    </div>
					<div v-else class="user-orders no_item_center" style="overflow-x:auto;">
						<h4 class="text-center"> {{ $t('Coupon Not Found !') }} </h4>
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
import axios from 'axios'
import swal from 'sweetalert';
import Leftsidebar from './default/Leftsidebar';
import pagination from 'laravel-vue-pagination';

export default {
	data(){
		return{
			authenticated: false,
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
            mycoupons:{},
            coupon_for_sale:{},
			availableCoupons:false
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

        load_my_coupons(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}

            axios.get(this.$baseUrl + "/api/v1/get-my-coupons", axiosConfig).then((response) => {
                this.mycoupons = response.data.my_coupons;
                this.coupon_for_sale = response.data.coupon_for_sale;
				if(response.data.my_coupons.length){
					this.availableCoupons = true;
				}
				
            });
        },
    	scrollToTop(){
            window.scrollTo(0,0);
        },

        buyNow(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			let formData = new FormData();
			let coupon = $('#coupon').val();

			if(coupon == ''){
				swal ( "Oops","All * mark fields are required!","error");
			}else{
				formData.append('coupon', coupon);

				axios.post(this.$baseUrl+'/api/v1/by-coupon-using-loyaltypoin', formData, axiosConfig).then(response => {
					if(response.data.status == 1){
						jQuery('.close').trigger('click');
						swal({
							title: response.data.message,
							icon: "success",
							timer: 3000
						});
					}else{
						swal({
							title: response.data.message,
							icon: "error",
							timer: 3000
						});
					}
				});
			}
        },


	},
	mounted(){
		this.scrollToTop();
		this.checkAuth();
		this.getUserDetails();
        this.baseurl = this.$baseUrl;
        this.load_my_coupons();
		document.title = "MabiY Shop | My Coupons";  
	}
}
</script>