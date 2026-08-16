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
                    <div v-if="myvouchers.my_vouchers && availableVoucher"  style="overflow-x:auto;">
                        <table class="table table-bordered user-orders-full" width="100%">
                            <thead>
                            <tr>
                                <th width="10%">{{ $t('ID') }}</th>
                                <th width="30%">{{ $t('Voucher') }}</th>
                                <th width="10%">{{ $t('Amount') }}</th>
                                <th width="20%">{{ $t('Valid From') }}</th>
                                <th width="20%">{{ $t('Valid To') }}</th>
                                <th width="10%">{{ $t('Status') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            	<tr v-for="(myvoucher, index) in myvouchers.my_vouchers" :key="index" >
									<td>#{{myvoucher.voucher.id}}</td>
									<td><img @error="imageLoadError" width="100%" :src="baseurl+'/'+myvoucher.voucher.banner"/></td>
									<td>BDT {{myvoucher.voucher.amount}}</td>
									<td>{{myvoucher.voucher.valid_from}}</td>
									<td>{{myvoucher.voucher.valid_to}}</td>
									<td>
										<span v-if="myvoucher.status == 1" class="badge badge-info">Ready for use</span>
										<span v-else-if="myvoucher.status == 2" class="badge badge-success">Used</span>
										<span v-else class="badge badge-danger" >Needs to active</span>
									</td>
								</tr>	
                            </tbody>
                        </table>
                    </div>
					<div v-else class="user-orders no_item_center" style="overflow-x:auto;">
						<h4 class="text-center"> {{ $t('Voucher Not Found !') }} </h4>
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
            myvouchers:{},
			availableVoucher:false
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

        load_my_vouchers(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}

            axios.get(this.$baseUrl + "/api/v1/get-my-collected-vouchers", axiosConfig).then((response) => {
                this.myvouchers = response.data;
				if(response.data.my_vouchers.length){
					this.availableVoucher = true;
				}
				
            });
        },
    	scrollToTop(){
            window.scrollTo(0,0);
        }


	},
	mounted(){
		this.scrollToTop();
		this.checkAuth();
		this.getUserDetails();
        this.baseurl = this.$baseUrl;
        this.load_my_vouchers();
		document.title = "MabiY Shop | My Vochers";  
	}
}
</script>