<template>
<div>


<section v-if="authenticated" id="register-page">
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6 account_wrapper bg_white forget_pass">
                    <h4 class="text-center">{{ $t('Forgot Password') }}</h4>
                    <form id="change_password_form" @submit.prevent="forgot_password()">
                        <div class="form-section" id="myaccountform">
                            
                            <div class="otp_before mb-5">
                                <div class="form-group">
                                    <label for=""> {{ $t('Mobile Number / Email') }} </label>
                                    <input name="mobile_number"  id="mobile_number" type="text" class="form-control mobile_number"  :placeholder="$t('Enter Mobile Number or Email')"> 
                                    <button type="button" @click.prevent="generateOtp()" class="fa fa-lock btn btn-primary generate_otp_1 forget_pass_otp" aria-hidden="true">{{ $t('Generate OTP') }}</button>
                                </div>
                            </div>

                            <div class="otp_after">
                                <div class="form-group">
                                    <label for=""> {{ $t('OTP') }} </label>
                                    <input name="otp" type="text" class="form-control otp_code" :placeholder="$t('Enter OTP sent to your mobile / email')"> 
                                </div>

                                <div class="form-group">
                                    <label for="">{{ $t('New password') }}</label>
                                    <input name="new_password" type="password" class="form-control new_password"   :placeholder="$t('New password')+'..'"> <span class="info_firstname"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ $t('Retype Password') }}</label>
                                    <input name="retype_password" type="password" class="form-control retype_password"  :placeholder="$t('Retype Password')+'..'"> <span class="info_firstname"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                                </div>
                                <div class="form-group">
                                <p class="text-right"><button type="submit" class="btn btn-primary">{{ $t('Update') }}</button></p>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
            <div class="col-md-3"></div>
        </div>
	</div>
</section>




<section v-else id="register-page">
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
export default {
	data(){
		return{
			baseurl:'',
			authenticated: true,
		}
	},
	components:{
		Leftsidebar
	},
	methods:{
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
					this.authenticated = true;
                    // this.$router.push({name:'login'});
                    // swal ( "Oops", response.data.message, "error");
                }else{
                    this.authenticated = true;
                }
            });
        },
		generateOtp(){
            let formData = new FormData();
			formData.append('mobile_number', $('#mobile_number').val());
			formData.append('is_forgot_password', 1);

			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/generate-otp',formData, axiosConfig).then(response => {

				if(response.data.status == 1){
					swal({
						title: response.data.message,
						icon: "success",
						timer: 3000
					});

                    jQuery('.otp_before').hide();
                    jQuery('.otp_after').show();
                    
				}else{
					swal ( "Oops", response.data.message, "error");
				}
			});
		},

		forgot_password(){
			if($('.otp_code').val() && $('.new_password').val() && $('.retype_password').val()){
				if($('.new_password').val().length < 6){
					swal ( "Oops", 'New password can not be less than 6 character.', "error");
					return false;
				}

				let formData = new FormData();
				formData.append('mobile_number', $('#mobile_number').val());
				formData.append('otp_code', $('.otp_code').val());
				formData.append('new_password', $('.new_password').val());
				formData.append('retype_password', $('.retype_password').val());
				
				let token = localStorage.getItem("token");
				let axiosConfig = {
					headers: {
						'Content-Type': 'application/json;charset=UTF-8',
						"Access-Control-Allow-Origin": "*",
						'Authorization': 'Bearer '+token
					}
				}
				axios.post(this.$baseUrl+'/api/v1/forgot-password', formData, axiosConfig).then(response => {
					if(response.data.status == 1){
						swal({
							title: 'Password changed successfully.',
							icon: "success",
							timer: 3000
						});
						jQuery("#change_password_form")[0].reset();
                         this.$router.push({name:'login'});
					
					}else{
						swal ( "Oops", response.data.message, "error");
					}
				});
			}else{
				swal ( "Oops", 'Please fillup all input fields.', "error");
			}
		},
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
	mounted(){
		this.scrollToTop();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | Change Password";  
	}
}
</script>