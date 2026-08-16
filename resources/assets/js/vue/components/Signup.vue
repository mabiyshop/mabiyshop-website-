<template>
<div>




<section id="register-page">
    <div class="container">
        <div class="row">
            <div class="m-auto col-12 col-sm-12 col-md-10 col-lg-8 col-xl-5">
                <div class="register-form mt-5 mb-5">
                    <h4>{{ $t('SIGN IN') }}</h4>
                    <hr>
                    <form @submit.prevent="signup()" class="signuppageForm">
                        <div class="otp_logins">
                            <div class="otp_before">
                                
                                <!-- <div class="form-group">
                                    <label for="">{{ $t('Full Name') }}<span style="color:#f00">*</span></label>
                                    <input v-model="signupForm.name" type="text" name="name" class="form-control" :placeholder="$t('Full Name')+'..'" required>
                                    <div class="validation_error" v-if="errors.name" v-html="errors.name[0]" />
                                </div> -->
                          
                                <div class="form-group">
                                    <label for=""> {{ $t('Mobile Number')}} </label>
                                    <input id="signup_page_generate_otp" name="mobile_number" type="text" class="form-control mobile_number_login_page" :placeholder="$t('Enter Email / Mobile Number')"> 
                                </div>
                                <div class="form-group">
                                    <button type="button" @click.prevent="generateOtp_login_page()" class="generate_otp_btn singin-with-google">{{ $t('Next') }}</button>
                                </div>
                                <div class="form-group popupOtp_login_page_group d-none">
                                    <label for="">{{ $t('OTP') }}</label>
                                    <input id="signup_page_otp"  type="text" name="otp"  class="form-control"  :placeholder="$t('OTP')+'..'">
                                    
                                    <input type="hidden" name="affiliate_referer" :value="affiliate_referer">
                                    <input type="submit" class="mt-3 singin-with-google" :value="$t('Sign up now')">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 already-account">
                                <p> {{ $t('Already have a accont') }} ? <b class="" ><router-link :to="{name: 'sign-up'}">{{ $t('Sign in now') }}</router-link></b> </p>
                            </div>
                        </div>
                    </form>
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
  data: () => ({
    signupForm: new Form({
      name: '',
	  phone: '',
	  email: '',
	  mobile_number: '',
	  otp: '',
	  password: '',
      password_confirmation: '',
      affiliate_referer: '',
      
    }),
    errors:{},
	show:false,
    errors: [],
  }),

  methods: {

        signup(){
            let session_key = localStorage.getItem("session_key");
			let phone = $('#signup_page_generate_otp').val();
			let otp = $('#signup_page_otp').val();
			let formData = new FormData();
			formData.append('mobile_number', phone);
			formData.append('otp', otp);
            formData.append('session_key', session_key);

            axios.post(this.$baseUrl + "/api/v1/user-register", formData).then(response =>{
                if(response.data.status == 2){
                    swal({
                        title: response.data.message,
                        icon: "error",
                        timer: 4000
                    });
                }else if(response.data.status == 1){
                    swal({
                        title: "Your account has been successfully created. You are logged in.",
                        icon: "success",
                        timer: 4000
                    }).then(()=>{
                        localStorage.setItem("token", response.data.token);
                        this.$store.dispatch('loadedUser');
                        this.$store.dispatch('loadedCart');
                        this.$store.dispatch('loadedCompares');
                        this.$store.dispatch('loadedNotifications');
                        this.$router.push({name:'myaccount'});
                    });
                }else{
                    this.errors = response.data.message;
                }
            }).catch(function(){
                swal({
                    title: response.data.message,
                    icon: "error",
                    timer: 3000
                });
            });
        },

        generateOtp_login_page(){

            let formData = new FormData();
			formData.append('mobile_number', $('#signup_page_generate_otp').val());
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/generate-otp-for-signup',formData, axiosConfig).then(response => {

				if(response.data.status == 1){
					swal({
						title: response.data.message,
						icon: "success",
						timer: 3000
					});
                    $('.generate_otp_btn').hide();
                    $('.popupOtp_login_page_group').removeClass('d-none');
                    $('.popupOtp_login_page_group').addClass('d-block');
				}else{
					swal ( "Oops", response.data.message, "error");
				}
			});
		},
    },
    mounted(){
        this.affiliate_referer = localStorage.getItem("affiliate_referer");
    }
    
}
</script>












