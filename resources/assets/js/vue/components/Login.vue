<template>
<div>

<section id="register-page">
    <div class="container mt-5 mb-5">
        <div v-if="site_info.social_login == 1" class="row">
            <div class="col-12 col-sm-12 col-md-1 col-lg-2 col-xl-2"></div>
            <div class="col-12 col-sm-12 col-md-10 col-lg-8 col-xl-8 bg_white">
                <div  class="row">
                    <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-7">
                        <div class="register-form with_social_media">
                            <h4>{{ $t('Login') }}</h4> 

                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li data-radiovalue="501" style="width: 50%;" class="nav-item whatlogin" role="presentation">
                                    <button style="width: 100%;" class="btn btn-primary " id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="false">{{$t('Password Login')}}</button>
                                </li>
                                <li data-radiovalue="500" style="width: 50%;" class="nav-item whatlogin" role="presentation">
                                    <button style="width: 100%;" class="btn btn-dark active" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">{{$t('OTP Login')}}</button>
                                </li>
                            </ul>

                            <hr>

                            <form  @submit.prevent="userLogin()">
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                        <div class="otp_logins">
                                            <div class="otp_before">
                                                <div class="form-group">
                                                    <label for=""> {{ $t('Mobile Number')}} </label>
                                                    <input id="phone" name="mobile_number" type="text" class="form-control mobile_number_login_page" :placeholder="$t('Enter Mobile Number')"> 
                                                    <button type="button" @click.prevent="generateOtp_login_page()" class="fa fa-lock btn btn-primary generate_otp_1 login_otp" aria-hidden="true">{{ $t('Generate OTP') }}</button>
                                                </div>
                                                <div class="form-group popupOtp_login_page_group">
                                                    <label for="">{{ $t('OTP') }}</label>
                                                    <input id="password" type="text" name="otp"  class="form-control"  :placeholder="$t('OTP')+'..'">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                        <div class="password_logins">
                                            <div class="form-group">
                                                <label for=""> {{ $t('Mobile Number / Email') }}</label>
                                                <input id="phone" type="text" name="phone" class="form-control" :placeholder="$t('Enter Mobile Number or Email')">
                                            </div>
                                            <div class="form-group">
                                                <label for="">{{ $t('Password') }}</label>
                                                <input id="password" type="password" name="password" class="form-control"  :placeholder="$t('Password')+'..'">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 p-0">
                                            <input type="checkbox" value="keep"> <span>{{ $t('Keep me sign in') }}</span>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 forgot-password p-0"> 
                                            <router-link :to="{name: 'forgotpassword'}">{{ $t('Forgot Password') }} ?</router-link>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="singin-with-google password_login" value="Sign in">
                                    <div class="singin-with-google otp_login" @click.prevent="popUpOtpLogin_login_page()">Sign in</div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-6 already-account">
                                        <p> {{ $t("Don't have an accont") }}</p>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 orsignup">
                                        <b class="text-uppercase"><router-link :to="{name: 'sign-up'}">{{ $t('Sign up now') }}</router-link></b> 
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                    <div class="col-12 col-sm-12 col-md-1 col-lg-1 col-xl-1 p-0 orLogin">
                        <h5 class="orLogin_item"><b>Or</b> <br> <small>{{ $t('Login With') }}</small></h5>
                    </div>

                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <div class="social_login mt-5">
                            <ul>
                                <li class="login_with_facebook"><a href="javascript:void(0)" @click="logInWithFacebook" ><i aria-hidden="true" class="fa fa-facebook"></i>  {{ $t('Login with Facebook') }}  </a></li>
                                <li id="googleButtonDiv"  class="login_with_google login_with_google_in_page">
                                    <a href="javascript:void(0)" @click="logInWithGoogleRedirect" ><i aria-hidden="true" class="fa fa-google"></i>{{ $t('Login with Google') }} </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-12 col-md-1 col-lg-2 col-xl-2"></div>
        </div>

        <div v-else class="row">
             <div class="col-12 col-sm-12 col-md-2 col-lg-3"></div>
            <div class="col-12 col-sm-12 col-md-8 col-lg-6">
                <div class="register-form">
                    <h4>{{ $t('Login') }}</h4>

                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li data-radiovalue="501" style="width: 50%;" class="nav-item whatlogin" role="presentation">
                            <button style="width: 100%;" class="btn btn-primary " id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="false">{{$t('Password Login')}}</button>
                        </li>
                        <li data-radiovalue="500" style="width: 50%;" class="nav-item whatlogin" role="presentation">
                            <button style="width: 100%;" class="btn btn-dark active" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true">{{$t('OTP Login')}}</button>
                        </li>
                    </ul>

                    <hr>

                    
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade " id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <form  @submit.prevent="userLogin()">
                                <div class="password_logins">
                                    <div class="form-group">
                                        <label for=""> {{ $t('Mobile Number / Email') }}</label>
                                        <input id="phone" type="text" name="phone" class="form-control" :placeholder="$t('Enter Mobile Number or Email')">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ $t('Password') }}</label>
                                        <input id="password" type="password" name="password" class="form-control"  :placeholder="$t('Password')+'..'">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="singin-with-google password_login" value="Sign in">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <form  @submit.prevent="userLoginByOtp()">
                                <div class="otp_logins">
                                    <div class="otp_before">
                                        <div class="form-group">
                                            <label for=""> {{ $t('Mobile Number')}} </label>
                                            <input id="phone" name="mobile_number" type="text" class="form-control mobile_number_login_page" :placeholder="$t('Enter Mobile Number')"> 
                                            <button type="button" @click.prevent="generateOtp_login_page()" class="fa fa-lock btn btn-primary generate_otp_1 login_otp" aria-hidden="true">{{ $t('Generate OTP') }}</button>
                                        </div>
                                        <div class="form-group popupOtp_login_page_group">
                                            <label for="">{{ $t('OTP') }}</label>
                                            <input id="password" type="text" name="otp"  class="form-control"  :placeholder="$t('OTP')+'..'">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="singin-with-google " value="Sign in">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-6 p-0">
                                <input type="checkbox" value="keep"> <span>{{ $t('Keep me sign in') }}</span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 forgot-password p-0"> 
                                <router-link :to="{name: 'forgotpassword'}">{{ $t('Forgot Password') }} ?</router-link>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 already-account">
                            <p> {{ $t("Don't have an accont") }} <b><router-link :to="{name: 'sign-up'}">{{ $t('Sign up now') }}</router-link></b> </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-2 col-lg-3"></div>

        </div>
        </div>
</section>

</div>
</template>




<script>
import axios from 'axios'
export default {
	data(){
		return{
            site_info:'',
			show:false,
		}
	},

    name:"facebookLogin",
	methods:{


        popUpOtpLogin_login_page(){
            let session_key = localStorage.getItem("session_key");
            let formData = new FormData();
			formData.append('mobile_number', $('#phone').val());
			formData.append('otp', $('#password').val());
			formData.append('session_key', session_key);
         
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/otp-login',formData, axiosConfig).then(response => {

				if(response.data.status == 1){
                swal({
                    title: response.data.message,
                    icon: "success",
                    timer: 3000
                });
               let user = response.data.customer;
               this.$store.commit('SET_USER', user);
               this.$store.commit('SET_AUTHENTICATED', true);
               localStorage.setItem("auth", true);
               localStorage.setItem("token", response.data.token);
               localStorage.setItem("user_id", response.data.customer.id);
               this.$store.dispatch('loadedUser');
               this.$store.dispatch('loadedCart');
               this.$store.dispatch('loadedCompares');
               this.$store.dispatch('loadedNotifications');
               this.$router.push({name:'myaccount'});
				}else{
					swal ( "Oops", response.data.message, "error");
				}
			});
        },

        generateOtp_login_page(){
            let formData = new FormData();
			formData.append('mobile_number', $('#pills-profile #phone').val());
			formData.append('otp_for_login', 1);

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
				}else{
					swal ( "Oops", response.data.message, "error");
				}
			});
		},







        async logInWithFacebook() {
            let that = this;

            await this.loadFacebookSDK(document, "script", "facebook-jssdk");
            await this.initFacebook();
            window.FB.login(function(response) {
                if(response.status == 'connected'){
                    if (response.authResponse) {
                        axios.post('https://api.biponi.com/api/v1/social-login/facebook', response.authResponse).then(function(result){
                            localStorage.setItem("token", result.data.token);
                            that.$store.dispatch('loadedUser');
                            that.$store.dispatch('loadedCart');
                            that.$store.dispatch('loadedCompares');
                            that.$store.dispatch('loadedNotifications');
                            that.$router.push({name:'myaccount'});
                        }).catch(function(e){
                            swal ( "Oops" ,  e,  "error" );
                        });

                    } else {
                        swal ( "Oops" ,  'Sorry! Facebook does\'t provide authentication for this user.',  "error" );
                    }
                }else{
                    swal ( "Oops" ,  'Sorry! Facebook login service is currently unavailable. Please try again later!',  "error" );
                }
            });
            return false;
        },
        async initFacebook() {
            window.fbAsyncInit = function() {
                window.FB.init({
                appId: "761135615074146",
                cookie: true,
                version: "v13.0"
                });
            };
        },
        async loadFacebookSDK(d, s, id) {
            var js,
                fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        },


        //Google Login
        async logInWithGoogle() {
                let that = this;
                window.onload = function () {
                    google.accounts.id.initialize({
                        client_id: "397239095845-llm31ean6e5v33s3r8lhucahvk1amnko.apps.googleusercontent.com", //MabiY Shop
                        callback: function handleCredentialResponse(response) {
                                if (response.credential) {
                                    axios.post('https://api.biponi.com/api/v1/social-login/google', {token:response.credential}).then(function(result){
                                        localStorage.setItem("token", result.data.token);
                                        that.$store.dispatch('loadedUser');
                                        that.$store.dispatch('loadedCart');
                                        that.$store.dispatch('loadedCompares');
                                        that.$store.dispatch('loadedNotifications');
                                        that.$router.push({name:'myaccount'});

                                    }).catch(function(e){
                                        swal ( "Oops" ,  e,  "error" );
                                    });

                                } else {
                                    swal ( "Oops" ,  'Sorry! Google does\'t provide authentication for this user.',  "error" );
                                }
                        }
                    });
                    google.accounts.id.renderButton(
                        document.getElementById("googleButtonDiv"),
                        { theme: "filled_blue", size: "large",width:'320',text:'continue_with' }
                    );
                    google.accounts.id.prompt();
                }
            },
        logInWithGoogleRedirect(){
            this.$router.go(this.$router.currentRoute);
        },

		userLogin(){
            let session_key = localStorage.getItem("session_key");
			let phone = $('#phone').val();
			let password = $('#password').val();
			let formData = new FormData();
			formData.append('phone', phone);
			formData.append('password', password);
            formData.append('session_key', session_key);

			if(phone == '' || password == ''){
				swal({
				  title: "Phone number and password is required.",
				  icon: "error",
				  timer: 3000
				});
			}else{
				axios.post(this.$baseUrl+'/api/v1/login', formData).then(response =>{
                    if(response.data.status == 1){
                        localStorage.setItem("token", response.data.token);
                        this.$store.dispatch('loadedUser');
                        this.$store.dispatch('loadedCart');
                        this.$store.dispatch('loadedCompares');
                        this.$store.dispatch('loadedNotifications');
                        this.$router.push({name:'myaccount'});
                        this.initChat();
    
                    }else{
                        swal({
                            title: response.data.message,
                            icon: "error",
                            timer: 3000
                        });
                    }
				}).catch(function(){
                    swal({
                        title: response.data.message,
                        icon: "error",
                        timer: 3000
                    });
				});
			}
		},
        userLoginByOtp(){
            let session_key = localStorage.getItem("session_key");
			let phone = $('#pills-profile #phone').val();
			let password = $('#pills-profile #password').val();
			let formData = new FormData();
			formData.append('phone', phone);
			formData.append('password', password);
            formData.append('session_key', session_key);
            formData.append('otp_login', 1);

			if(phone == '' || password == ''){
				swal({
				  title: "Phone number and password is required.",
				  icon: "error",
				  timer: 3000
				});
			}else{
				axios.post(this.$baseUrl+'/api/v1/login', formData).then(response =>{
                    if(response.data.status == 1){
                        localStorage.setItem("token", response.data.token);
                        this.$store.dispatch('loadedUser');
                        this.$store.dispatch('loadedCart');
                        this.$store.dispatch('loadedCompares');
                        this.$store.dispatch('loadedNotifications');
                        this.$router.push({name:'myaccount'});
                        this.initChat();
    
                    }else{
                        swal({
                            title: response.data.message,
                            icon: "error",
                            timer: 3000
                        });
                    }
				}).catch(function(){
                    swal({
                        title: response.data.message,
                        icon: "error",
                        timer: 3000
                    });
				});
			}
		},
        initChat(){
                let db = firebase.firestore();
                db.collection('ChatRoomUsers').get().then((snapshot) => {
            
                let allData = snapshot.docs.map(doc => doc.data());
                let formatedHtml = '';
                let selectedChat = '';
                let avater = '';
                let host = window.location.protocol + "//" + window.location.host;

                
                allData.forEach(function(val,key){

                    if(val.usersid[1] == localStorage.getItem('userID')){

                        if(val.usersid[0] == localStorage.getItem('sellerID')){
                            selectedChat = val.tousername;
                        }

                        let LocalDate = val.timestamp.toDate().toLocaleDateString('en-US');
                        let LocalTime = val.timestamp.toDate().toLocaleTimeString('en-US');
                        if(!val.toUserphoto !=''){
                            avater = host+'/chat/avater.png';
                        }else{
                            avater = localStorage.getItem('chatImageHead')+'/'+val.toUserphoto;
                        }
                        formatedHtml+='<div data-seller-id="'+val.usersid[0]+'" data-seller-name="'+val.tousername+'" class="row seller_section">'+
                                '<div class="col-md-2 col-lg-2 p-0">'+
                                '<img style="width:80%" src="'+avater+'" alt="avater">'+
                                '</div>'+
                                '<div class="col-md-10 col-lg-10">'+
                                '<span class="seller_item_title">'+val.tousername+'</span> <p>'+LocalDate+' '+LocalTime+'</p>'+
                                '</div>'+
                            '</div>';
                    }

                });

               // console.log(formatedHtml);

                setTimeout(function(){ 
                    jQuery('#chat_rooms_list').html(formatedHtml);
                    jQuery('.chating_title_dynamic').html(selectedChat);
                    jQuery(".chating_body").animate({ scrollTop: $('.chating_body').prop("scrollHeight")}, 1000);
                }, 2000);


            }).catch((error) => { console.log(error); });
        },
    	scrollToTop(){
            window.scrollTo(0,0);
        },
        site_information(){
         axios.get(this.$baseUrl+'/api/v1/site-info').then(response => {
            this.site_info = response.data;
            if(this.site_info.social_login == 1){
               this.initFacebook();
               this.logInWithGoogle();
            }
            
         });
       },
	},
	mounted(){
        this.scrollToTop();
		document.title = "MabiY Shop | Login"; 
        const plugin = document.createElement("script");
        plugin.setAttribute( "src","https://accounts.google.com/gsi/client");
        //plugin.async = true;
        document.body.appendChild(plugin);
        this.baseurl = this.$baseUrl;
        this.site_information();
    }

}
</script>