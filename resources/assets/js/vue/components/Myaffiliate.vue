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
                <div class="affiliate"> 
                    <div>
                       
					   <div class="row">
						<div class="col-6">
							<h5 class="text-left mb-0 text-uppercase mt-3 text-primary">Affiliate Dashboard</h5>
						</div>
						<div class="col-6">
							<p class="text-right"> <button data-toggle="modal" data-target="#withdrawalModal" class="btn btn-primary"> <i class="fa fa-money"></i> Request Withdrawal</button> </p>



							<!-- Modal -->
							<div class="modal fade" id="withdrawalModal" tabindex="-1" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="withdrawalModalLabel">Affiliate Withdrawal</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">

										<div class="form-group">
											<label for="withdrawalAmount">Withdrawal Amount<span class="text-danger">*</span></label>
											<input type="amount" class="form-control" placeholder="Amount you want to withdraw" id="withdrawalAmount">
										</div>

										<div class="form-group">
											<label for="withdrawalAmount">Channel<span class="text-danger">*</span></label>
											<select name="channel" class="form-control" id="channel">
												<option disabled selected>-- Select Channel --</option>
												<option value="bkash">bKash</option>
												<option value="nagad">Nagad</option>
												<option value="rocket">Rocket</option>
												<option value="bank">Bank Account</option>
											</select>
										</div>

										<div class="form-group">
											<label for="withdrawalAmount">Account Details <span class="text-danger">*</span> </label>
											<textarea name="account_details" id="account_details" class="form-control" cols="10" placeholder="MFS or Bank Account Details" ></textarea>
										</div>
										<p class="text-right"><button @click.prevent="withdrawalRequest()" type="button" class="btn btn-primary">Submit Request</button></p>
								</div>
		
								</div>
							</div>
							</div>



						</div>
					   </div>
						<div v-if="affiliate" class="row justify-content-center">
							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
									<div class="icon">
									<i class="fa fa-user"></i>
									</div>
									<div class="stat">{{affiliate.signups}}</div>
									<div class="title">Signups</div>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
										<div class="icon">
											<i class="fa fa-pause"></i>
										</div>
										<div class="stat">BDT {{affiliate.pending_maturation}}</div>
										<div class="title">Pending Maturation</div>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
										<div class="icon">
											<i class="fa fa-hand-rock-o"></i>
										</div>
										<div class="stat">BDT {{affiliate.amount_withdrawn}}</div>
										<div class="title">Amount Withdrawn</div>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
										<div class="icon">
											<i class="fa fa-money"></i>
										</div>
										<div class="stat">BDT {{affiliate.balance}}</div>
										<div class="title">Balance</div>
									</div>
								</div>
							</div>
						</div>


						<div class="link-section mt-3">
							<div class="panel panel-default panel-form">
								<div class="custompanel-body">
									<div class="input-group input-group-xlg input-group-vertical-sm">
										<div class="input-group-addon mt-1 mr-3">Unique Referral Link</div>
										<input class="form-control text-primary bg-white" type="text" readonly="" :value="frontendUrl+'?referer='+userData.id">
									</div>
								</div>
							</div>
						</div>

						<h4 class="mt-4"></h4>

						<!-- Nav tabs -->

						<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
							<li class="nav-item" role="presentation">
								<a class="aff_tab active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">My Referrals</a>
							</li>
							<li class="nav-item" role="presentation">
								<a class="aff_tab" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">My Withdrawals</a>
							</li>
						</ul>

						<div class="tab-content" id="pills-tabContent">
							<div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<td>SL.</td>
													<td>Product Name</td>
													<td>Buyer Name</td>
													<td>Commission</td>
													<td>Status</td>
												</tr>
											</thead>
											<tbody>

												<tr v-for="(history,index) in affiliate.history" :key="index">
													<td>{{index+1}}</td>
													<td> 
														<p v-if="history.product" v-for="(product,index) in history.product" :key="index">
															<router-link class="text-primary" v-if="product" :to="{ name: 'product', params: {slug: product.slug } }">{{product.title}}</router-link>
														</p>
														
													</td>
													<td> <span v-if="history.buyer">{{history.buyer.name}}</span> </td>
													<td>BDT {{history.commission_amount}}</td>
													<td>
														<span v-if="history.status == 6" class="badge badge-primary">Matured</span>
														<span v-else class="badge badge-warning">Pending</span>
													</td>
												</tr>

											</tbody>
										</table>
									</div>


							</div>
							<div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
								
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<td>SL.</td>
													<td>Amount</td>
													<td>Channel</td>
													<td>Account Details</td>
													<td>Note</td>
													<td>Status</td>
												</tr>
											</thead>
											<tbody>

												<tr v-for="(withdrawal,index) in affiliate.withdrawals" :key="index">
													<td>{{index+1}}</td>
													<td>BDT {{withdrawal.amount}}</td>
													<td>{{withdrawal.channel}}</td>
													<td>{{withdrawal.account_details}}</td>
													<td>{{withdrawal.note}}</td>
													
													<td>
														<span v-if="withdrawal.status == 1" class="badge badge-warning">Pending</span>
														<span v-else-if="withdrawal.status == 6" class="badge badge-primary">Settled</span>
													</td>
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
                </div>
            </div>
            <div class="col-md-9 col-lg-9">
                <div class="form-section" id="myaccountform">
                   

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
			frontendUrl:'',
			user:'',
            affiliate:{},
			availableAffiliate:false
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

        load_my_affiliate(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}

            axios.get(this.$baseUrl + "/api/v1/get-my-affiliate-details", axiosConfig).then((response) => {
                this.affiliate = response.data;
            });
        },
    	scrollToTop(){
            window.scrollTo(0,0);
        },
		withdrawalRequest(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			let formData = new FormData();
			let amount = $('#withdrawalAmount').val();
			let channel = $('#channel').find('option:selected').val();
			let account_details = $('#account_details').val();

			if(amount == '' || channel == '' || account_details == ''){
				swal ( "Oops","All * mark fields are required!","error");
			}else{
				formData.append('amount', amount);
				formData.append('channel', channel);
				formData.append('account_details', account_details);

				axios.post(this.$baseUrl+'/api/v1/affiliate-withdrawal-request', formData, axiosConfig).then(response => {
					if(response.data.status == 1){
						jQuery('.close').trigger('click');
						swal({
							title: response.data.message,
							icon: "success",
							timer: 3000
						});
					}else{
						swal ( "Please check" ,  response.data.message,  "error");
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
		this.frontendUrl = this.$frontendUrl;
        this.load_my_affiliate();
		document.title = "MabiY Shop | My Affiliate";  
	}
}
</script>