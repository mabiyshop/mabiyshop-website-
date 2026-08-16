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
					<b v-if="user.name">{{ user.name }}</b> <b v-else>{{ userData.name  }}</b>
					<p class="mb-0" v-if="user.email">{{ user.email }}</p> <p class="mb-0"  v-else>{{ userData.email }}</p>
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
						<div class="row">
							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
									<div class="icon">
									<i class="fa fa-futbol-o"></i>
									</div>
									<div class="stat">{{userData.loyalty_points}}</div>
									<div class="title">Loyalty Points</div>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
										<div class="icon">
											<i class="fa fa-shopping-cart"></i>
										</div>
										<div class="stat">{{userData.total_order}}</div>
										<div class="title">Completed Order</div>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
								<div class="card">
									<div class="tile">
										<div class="icon">
											<i class="fa fa-money"></i>
										</div>
										<div class="stat">BDT {{userData.total_spends}}</div>
										<div class="title">Total Spends</div>
									</div>
								</div>
							</div>
						</div>
				</div>
				<div class="row">
					<div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
						<div class="profile_information">
							<h5>{{ $t('Profile Information')}}</h5>
							<hr>
							<div class="form-section" id="myaccountform">
								<div class="form-group">
									<label for=""> {{ $t('Full Name') }} </label>
									<input type="text" class="form-control update_user_name" id="name" :value="userData.name" :placeholder="$t('Full Name')+'..'">
									<div class="validation_error" v-if="errors.name" v-html="errors.name[0]" />
								</div>

								<div class="form-group">
									<label for="">{{ $t('Email') }}</label>
									<input v-if="userData.email" type="email" class="form-control update_user_email" :value="userData.email" readonly> 
									<input v-else type="email" class="form-control" id="email" :placeholder="$t('Email')+'..'"> 
									<div class="validation_error" v-if="errors.email" v-html="errors.email[0]" />
								</div>
								<div class="form-group">
									<label for=""> {{ $t('Mobile number') }} </label>
									<input v-if="userData.phone" type="text" class="form-control update_user_phone" :value="userData.phone" readonly>
									<input v-else type="text" class="form-control" id="phone" :placeholder="$t('Enter Mobile Number')" >
									<div class="validation_error" v-if="errors.phone" v-html="errors.phone[0]" />
								</div>
								<div class="form-group">
									<label for="">{{ $t('Profile picture') }}</label>
									<br>
									<input type="file" id="image" @change="onimageChange">

									<div class="priview_image">
										<img @error="imageLoadError" v-if="profile_picture_preview" :src="profile_picture_preview" />
									</div>
								</div>
							</div>
						</div>

					</div>
					<div v-if="userData.user_type == '2'" class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
						
						<div class="company_information">
							<h5>{{ $t('Company Information') }}</h5>
							<hr>
							<ul>
								<span v-for="(company, index) in company_information" :key="index">
									<li v-if="company.meta_key == 'company_name'">
										<div class="form-group">
										<label for="">{{ $t('Company Name') }}</label>
										<input type="text" class="form-control company_name" :id="company.meta_key" :value="company.meta_value"> 
										</div>
									</li>
									<li v-if="company.meta_key == 'company_phone'">
										<div class="form-group">
										<label for="">{{ $t('Company Phone') }}</label>
										<input type="text" class="form-control company_phone" :id="company.meta_key" :value="company.meta_value"> 
										</div>
									</li>
									<li v-if="company.meta_key == 'company_email'">
										<div class="form-group">
										<label for="">{{ $t('Company Email') }}</label>
										<input type="text" class="form-control company_email" :id="company.meta_key" :value="company.meta_value"> 
										</div>
									</li>
									<li v-if="company.meta_key == 'company_division'">
										<div class="form-group">
											<label for="">{{ $t('Division') }}<span style="color:#f00">*</span></label>
												<select @change.prevent="getDistrict()" name="division" id="division" class="form-control company_division corporate_division" required>
														<option disabled>--Select Division--</option>
														<option :selected="company.meta_value == 68" value="68">Dhaka</option>
														<option :selected="company.meta_value == 36" value="36">Chattogram</option>
														<option :selected="company.meta_value == 60" value="60">Rajshahi</option>
														<option :selected="company.meta_value == 65" value="65">Khulna</option>
														<option :selected="company.meta_value == 66" value="66">Barishal</option>
														<option :selected="company.meta_value == 67" value="67">Sylhet</option>
														<option :selected="company.meta_value == 69" value="69">Rangpur</option>
														<option :selected="company.meta_value == 6175" value="6175">Mymensingh</option>
												</select>
											<div class="validation_error" v-if="errors.division" v-html="errors.division[0]" ></div>
										</div>
									</li>
									<li v-if="company.meta_key == 'company_district'">
				
										<div class="form-group">
											<label for="">{{ $t('District') }} <span style="color:#f00">*</span></label>
												<select  @change.prevent="getUpazila()" name="district" id="district2" class="form-control district_title" required>
													<option >--Select District--</option>
													<option data-removeable="true" v-for="(district,index) in districts" :key="index" :value="district.id">{{district.title}}</option>
													<option v-if="autoselect_district" data-removeable="true" v-for="(district,index) in selected_address.district" :key="index" :value="district.id" :selected="company.meta_value == district.id">{{district.title}}</option>
												</select>


											<div class="validation_error" v-if="errors.district" v-html="errors.district[0]" ></div>
										</div>
									</li>
									<li v-if="company.meta_key == 'company_area'">
							
										<div class="form-group">
											<label for="">{{ $t('Upazila / Thana') }} <span style="color:#f00">*</span></label>
												<select  @change.prevent="getUnion()" name="upazila" id="upazila2" class="form-control upazila_title" required>
													<option >--Select Upazila--</option>
													<option v-if="autoselect_upazila" data-removeable="true" v-for="(upazila, index) in selected_address.upazila" :key="index" :value="upazila.id" :selected="company.meta_value == upazila.id">{{upazila.title}}</option>
													<option data-removeable="true" v-for="(upazila,index) in upazilas" :key="index" :value="upazila.id">{{upazila.title}}</option>
												</select>
											<div class="validation_error" v-if="errors.upazila" v-html="errors.upazila[0]" ></div>
										</div>
									</li>
									<li v-if="company.meta_key == 'company_union'">
										<div class="form-group">
											<label for="">{{ $t('Union / Area') }} <span style="color:#f00">*</span></label>
												<select name="union" id="union2" class="form-control union_title" required>
													<option >--Select Union--</option>
													<option v-if="autoselect_union" data-removeable="true" v-for="(union, index) in selected_address.union" :key="index" :value="union.id" :selected="company.meta_value == union.id">{{union.title}}</option>
													<option data-removeable="true" v-for="(union,index) in unions" :key="index" :value="union.id">{{union.title}}</option>
												</select>
											<div class="validation_error" v-if="errors.union" v-html="errors.union[0]" ></div>
										</div>
									</li>
									<li v-if="company.meta_key == 'dbid'">
										<div class="form-group">
										<label for="">{{ $t('DBID') }}</label>
										<input type="text" class="form-control dbid" :id="company.meta_key" :value="company.meta_value"> 
										</div>
									</li>
								</span> 
							</ul>
						</div>
					</div>
				</div>
				<div class="row" style="margin: 10px 0;"> 
					<div class="form-group">
						<button class="btn btn-primary site_color1" @click="updateDeatails()">{{ $t('Update Profile') }}</button>
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
export default {
	data(){
		return{
			authenticated: false,
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
			errors: [],
			profile_picture_preview:'',
			company_information:'',
            districts:{},
            upazilas:{},
            unions:{},
			selected_address:'',
            autoselect_district:true,
            autoselect_upazila:true,
            autoselect_union:true,
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
		onimageChange(e){
			const file = e.target.files[0]
			// Do some client side validation...
			this.image = file;
			this.profile_picture_preview = URL.createObjectURL(file);

		},
		imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },

	   logout(){
			this.$store.commit('SET_AUTHENTICATED', false);
			this.$store.commit('LOADED_USER', []);
			this.$store.commit('LOADED_CART', []);
			this.$store.commit('LOADED_COMPARE', []);
			this.$store.commit('LOADED_NOTIFICATIONS', []);
			localStorage.removeItem("auth");
			localStorage.removeItem("cart");
			localStorage.removeItem("token");
			localStorage.removeItem("user_id");
			localStorage.setItem("cart", null);
			localStorage.setItem("auth", false);
			localStorage.setItem("token", false);
			this.$store.dispatch('loadedUser');
			this.$store.dispatch('loadedCart');
			this.$store.dispatch('loadedCompares');
			this.$store.dispatch('loadedNotifications');
			this.$store.dispatch('loadedVoucher');
			this.$store.dispatch('loadedUsableVoucher');
			this.$router.push({name:'login'});
		},
		
		updateDeatails(){
			let formData = new FormData();
			formData.append('name', $('.update_user_name').val());
			formData.append('email', $('update_user_email').val());
			formData.append('phone', $('update_user_phone').val());
			formData.append('company_name', $('.company_name').val());
			formData.append('company_phone', $('.company_phone').val());
			formData.append('company_email', $('.company_email').val());
			formData.append('company_division', $('.company_division').find('option:selected').val());
			formData.append('district_title', $('.district_title').find('option:selected').val());
			formData.append('upazila_title', $('.upazila_title').find('option:selected').val());
			formData.append('union_title', $('.union_title').find('option:selected').val());
			formData.append('dbid', $('.dbid').val());
			formData.append('image', this.image);

			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}


			axios.post(this.$baseUrl+'/api/v1/update-user-details', formData, axiosConfig).then(response => {
				if(response.data.status == 1){
					this.image = '';
					this.user = response.data.user;
					this.userData = response.data.user;
					this.profile_picture = response.data.user.avatar;
					this.errors = [];
					this.getUserDetails();
					swal({
						title: 'Profile has been successfully updated.',
						icon: "success",
						timer: 3000
					});
				}else{
					 this.errors = response.data.message;
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

				if(this.userData.user_type == 2){
					this.company_information = response.data.meta;
					let formData = new FormData();
					formData.append('division', response.data.meta[3].meta_value);
					formData.append('district', response.data.meta[4].meta_value);
					formData.append('upazila', response.data.meta[5].meta_value);
					formData.append('union', response.data.meta[6].meta_value);
					axios.post(this.$baseUrl+'/api/v1/get-selected-corporate-address', formData, axiosConfig).then(response =>{
						this.selected_address = response.data.selected_address;
						this.authenticated = true;
					});
				}

			})
		},
    	scrollToTop(){
            window.scrollTo(0,0);
        },
		async getDistrict(){
			let id =  jQuery('.company_division').find('option:selected').val();
			await axios.get(this.$baseUrl + "/api/v1/get-district/"+id).then((response) => {
					this.upazilas = {};
					this.unions = {};
					this.districts = response.data;
					this.autoselect_district = false;
				});
		},
		async getUpazila(){
			let id =  jQuery('.district_title').find('option:selected').val();
			await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
					this.unions = {};
					this.upazilas = response.data;
					this.autoselect_upazila = false;
				});
		},
		async getUnion(){
			let id =  jQuery('.upazila_title').find('option:selected').val();
			await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
				this.unions = response.data;
				this.autoselect_union = false;
			});
		},

	},
	computed:{
		logged_in_user(){
			return this.$store.getters.getLoadedUser.user;
		}
	},
	mounted(){
		this.scrollToTop();
		this.checkAuth();
		this.getUserDetails();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | My Account";  
	}
}
</script>