<template>
<div>




<section v-if="authenticated"  id="profile-page">
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
				<form id="change_password_form" @submit.prevent="change_password()">
                <div class="form-section" id="myaccountform">
					
                    <div class="form-group">
                        <label for="">{{ $t('New password') }} <span style="color:red;">*</span></label>
                        <input name="new_password" type="password" class="form-control new_password"  :placeholder="$t('New password')+'..'"> <span class="info_firstname"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                    </div>
                    <div class="form-group">
                        <label for="">{{ $t('Retype Password') }} <span style="color:red;">*</span></label>
                        <input name="retype_password" type="password" class="form-control retype_password" :placeholder="$t('Retype Password')+'..'"> <span class="info_firstname"><i class="fa fa-pencil" aria-hidden="true"></i></span>
                    </div>
					

					<div class="form-group">
						<button type="submit" class="btn btn-primary">{{ $t('Update') }}</button>
					</div>
                </div>
				</form>
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
export default {
	data(){
		return{
			is_user: '',
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
			authenticated: false
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
                    this.$router.push({name:'login'});
                    swal ( "Oops", response.data.message, "error");
                }else{
                    this.authenticated = true;
                }
            });
        },
		imageLoadError(event){
			event.target.src = "/images/notfound.png";
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

		generateOtp(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/generate-otp', {}, axiosConfig).then(response => {

				if(response.data.status == 1){
					swal({
						title: response.data.message,
						icon: "success",
						timer: 3000
					});
					//$('#phone_group').hide();
				}else{
					swal ( "Oops", response.data.message, "error");
				}
			});
		},

		change_password(){
			if($('.new_password').val() && $('.retype_password').val()){
				if($('.new_password').val().length < 6){
					swal ( "Oops", 'New password can not be less than 6 character.', "error");
					return false;
				}

				let formData = new FormData();
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
				axios.post(this.$baseUrl+'/api/v1/change-password', formData, axiosConfig).then(response => {
					if(response.data.status == 1){
						swal({
							title: 'Password changed successfully.',
							icon: "success",
							timer: 3000
						});
						jQuery("#change_password_form")[0].reset();
					
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
		this.getUserDetails();
		this.checkAuth();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | Change Password";  
	}
}
</script>