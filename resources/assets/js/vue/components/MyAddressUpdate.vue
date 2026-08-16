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

            <div class="form-title">
            <h5><b>{{ $t('Update Address') }}</b></h5>
            </div>
            <div class="d-flex flex-column text-center">
                <div class="tab-content">
                    <div id="menu1">
                        <div class="col-md-12">
                                <form @submit.prevent="addNewAddress()">
                                <div class="options">
                                    <div class="row text-left">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ $t('Full Name') }} <span style="color:#f00">*</span></label>
                                                <input :value="singleAddress.shipping_first_name" type="text" class="form-control shipping_first_name2" :placeholder="$t('Full Name')+'..'" required>
                                                <input :value="singleAddress.id" type="hidden" class="address_id2" required>
                                                <div class="validation_error" v-if="errors.shipping_first_name" v-html="errors.shipping_first_name[0]" />
                                            </div>
                                        </div>

                                       <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">  {{ $t('Phone') }} <span style="color:#f00">*</span></label>
                                                <input :value="singleAddress.shipping_phone" type="text" class="form-control popup_phone2" :placeholder="$t('Phone')+'..'" required>
                                                <div class="validation_error" v-if="errors.shipping_phone" v-html="errors.shipping_phone[0]" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('Division') }}<span style="color:#f00">*</span></label>
                                                         
                                                        <select  @change.prevent="getDistrict()" name="division" id="division2" class="form-control" required>
                                                                <option disabled>--Select Division--</option>
                                                                <option :selected="singleAddress.shipping_division == 68" value="68">Dhaka</option>
                                                                <option :selected="singleAddress.shipping_division == 36" value="36">Chattogram</option>
                                                                <option :selected="singleAddress.shipping_division == 60" value="60">Rajshahi</option>
                                                                <option :selected="singleAddress.shipping_division == 65" value="65">Khulna</option>
                                                                <option :selected="singleAddress.shipping_division == 66" value="66">Barishal</option>
                                                                <option :selected="singleAddress.shipping_division == 67" value="67">Sylhet</option>
                                                                <option :selected="singleAddress.shipping_division == 69" value="69">Rangpur</option>
                                                                <option :selected="singleAddress.shipping_division == 6175" value="6175">Mymensingh</option>
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_division" v-html="errors.shipping_division[0]" />
                                                        
                                                </div>
                                        </div>
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('District') }}<span style="color:#f00">*</span></label>
                                                        <select  @change.prevent="getUpazila()" name="district" id="district2" class="form-control" required>
                                                            <option >--Select District--</option>
                                                            <option data-removeable="true" v-for="(district,index) in districts" :key="index" :value="district.id">{{district.title}}</option>
                                                            <option v-if="autoselect_district" data-removeable="true" v-for="(district,index) in selected_address.district" :key="index" :value="district.id" :selected="singleAddress.shipping_district == district.id">{{district.title}}</option>
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_district" v-html="errors.shipping_district[0]" />
                                                </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('Upazila / Thana') }}<span style="color:#f00">*</span></label>
                                                        <select  @change.prevent="getUnion()" name="upazila" id="upazila2" class="form-control" required>
                                                            <option >--Select Upazila--</option>
                                                            <option v-if="autoselect_upazila" data-removeable="true" v-for="(upazila, index) in selected_address.upazila" :key="index" :value="upazila.id" :selected="singleAddress.shipping_thana == upazila.id">{{upazila.title}}</option>
                                                            <option data-removeable="true" v-for="(upazila,index) in upazilas" :key="index" :value="upazila.id">{{upazila.title}}</option>
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_thana" v-html="errors.shipping_thana[0]" />
                                                </div>
                                        </div>
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('Union / Area') }}</label>
                                                        <select name="union" id="union2" class="form-control" >
                                                            <option value="0">--Select Union--</option>
                                                            <option v-if="autoselect_union" data-removeable="true" v-for="(union, index) in selected_address.union" :key="index" :value="union.id" :selected="singleAddress.shipping_union == union.id">{{union.title}}</option>
                                                            <option data-removeable="true" v-for="(union,index) in unions" :key="index" :value="union.id">{{union.title}}</option>
                                                            
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_union" v-html="errors.shipping_union[0]" />
                                                </div>
                                        </div>
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('Post code') }}</label>
                                                   <input :value="singleAddress.shipping_postcode" type="text" class="form-control popup_post_code2" :placeholder="$t('Post code')+'..'" >
                                                   <div class="validation_error" v-if="errors.shipping_postcode" v-html="errors.shipping_postcode[0]" />
                                                </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for=""> {{ $t('Email') }}</label>
                                                <input :value="singleAddress.shipping_email" type="text" class="form-control shipping_email2" :placeholder="$t('Email')+'..'">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for=""> {{ $t('Address') }} <span style="color:#f00">*</span></label>
                                                <textarea :value="singleAddress.shipping_address" name="" id="" cols="30" rows="3" class="form-control shipping_address2"  :placeholder="$t('Road , house, address')+'..'"  required></textarea>
                                                <div class="validation_error" v-if="errors.shipping_address" v-html="errors.shipping_address[0]" />
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-right"> <button type="submit" class="btn btn-dark">{{ $t('Update Address') }}</button> </p>
                                </div>
                                </form>
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
            addresses: {},
            districts:{},
            upazilas:{},
            unions:{},
            errors:{},
            errors: [],
            singleAddress:'',
            selected_address:'',
            selected_Opazila:null,
            authenticated: false,
            autoselect_district:true,
            autoselect_upazila:true,
            autoselect_union:true,
		}
	},
	components:{
		Leftsidebar
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
        addNewAddress(){
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer '+token
                }
            }
            let formData = new FormData();
            formData.append('address_id', $('.address_id2').val());
            formData.append('shipping_first_name', $('.shipping_first_name2').val());
            // formData.append('shipping_last_name', $('.shipping_last_name2').val());
            formData.append('shipping_division', $('#division2').find('option:selected').val());
            formData.append('shipping_district', $('#district2').find('option:selected').val());
            formData.append('shipping_thana', $('#upazila2').find('option:selected').val());
            formData.append('shipping_union', $('#union2').find('option:selected').val());
            formData.append('shipping_postcode', $('.popup_post_code2').val());
            formData.append('shipping_phone', $('.popup_phone2').val());
            formData.append('shipping_email', $('.shipping_email2').val());
            formData.append('shipping_address', $('.shipping_address2').val());
            formData.append('is_edit', 1);
            axios.post(this.$baseUrl+'/api/v1/add-new-address', formData, axiosConfig).then(response => {
                if(response.data.status == 1){
                    swal({
                        title: 'Address updated successfully.',
                        icon: "success",
                        timer: 3000
                    });
                    this.$store.dispatch('loadedUser');
                    this.$router.push({name:'myaddress'});
                }else{
                    this.errors = response.data.message;
                }
            });
        },
        change_address($address_id){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			let formData = new FormData();
			formData.append('address_id', $address_id);
            axios.post(this.$baseUrl+'/api/v1/update-default-address', formData, axiosConfig).then(response => {
				if(response.data.status == 1){
                    this.$store.dispatch('loadedCart');
                    this.$store.dispatch('loadedUser');
                    jQuery('.close').trigger('click');   
               }else{
                    swal ( "Please check" ,  response.data.message,  "error");
                }
			});
        },
    
        async getDistrict(){
            let id =  jQuery('#division2').find('option:selected').val();
            await axios.get(this.$baseUrl + "/api/v1/get-district/"+id).then((response) => {
                    this.upazilas = {};
                    this.unions = {};
                    this.districts = response.data;
                    this.autoselect_district = false;
                });
        },
        

        async getUpazila(){
            let id =  jQuery('#district2').find('option:selected').val();
            await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
                    this.unions = {};
                    this.upazilas = response.data;
                    this.autoselect_upazila = false;
                });
            },
            async getUnion(){
                let id =  jQuery('#upazila2').find('option:selected').val();
                    await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
                    this.unions = response.data;
                    this.autoselect_union = false;
                });
            },

	   
		load_single_address(){
            let address_id = this.$route.params.address_id;
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
			axios.get(this.$baseUrl+'/api/v1/get-single-address/'+address_id, axiosConfig).then(response =>{
                this.selected_Opazila = response.data.singleAddress.shipping_district;
                $('#division option[value="'+response.data.singleAddress.shipping_division+'"]').attr({"selected": true});
                $('#district option[value="'+response.data.singleAddress.shipping_district+'"]').attr({"selected": true});
                $('#upazila option[value="'+response.data.singleAddress.shipping_thana+'"]').attr({"selected": true});
                // $('#district option[value="'+response.data.singleAddress.shipping_union+'"]').attr({"selected": true});
               // $( "#upazila" ).trigger( "change");
				this.singleAddress = response.data.singleAddress;
				this.selected_address = response.data.selected_address;
                this.authenticated = true;
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
    	scrollToTop(){
            window.scrollTo(0,0);
        }



	},
	computed:{
        logged_in_user(){
            return this.$store.getters.getLoadedUser.user;
        },

        logged_in_user_address(){
            let x = this.$store.getters.getLoadedUser.address;
            let res = 0;
            if(x != undefined){
                if(x.length != 0){
                    res = this.$store.getters.getLoadedUser.address;
                }
            }
            return res;
        },
	},
	mounted(){
        this.scrollToTop();
		this.load_single_address();
		this.getUserDetails();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | Update Address";  
	}
}
</script>