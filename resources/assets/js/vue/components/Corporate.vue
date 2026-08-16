<template>
<div>

<section id="register-page">
    <div class="container">
        <div class="row" id="join_seller_row">
            <div class="col-md-1 col-lg-1"></div>
            <div class="col-md-10 col-lg-10">
                <div class="register-form seller_registration">
                    <h4>{{ $t('Join As Corporate User') }}</h4>
                        <form @submit.prevent="corporateUserRegister" class="vendor_regi_form"  enctype='multipart/form-data'>
                        <div class="row info_seperate">
                            <div class="col-lg-12 vendor_title">
                                 <h5 class="font-weight-bold"> {{ $t('Personal Information') }}  </h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Full Name') }} <span style="color:#f00">*</span></label>
                                    <input  type="text" name="user_name" class="form-control user_name" :placeholder="$t('Full Name')+'..'" required>
                                    <div class="validation_error" v-if="errors.user_name" v-html="errors.user_name[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Phone') }}<span style="color:#f00">*</span></label>
                                    <input type="text" name="user_phone" class="form-control user_phone" :placeholder="$t('Phone')+'..'" required>
                                    <div class="validation_error" v-if="errors.user_phone" v-html="errors.user_phone[0]" ></div>
                                </div>
                            </div>
                             <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Email') }}</label>
                                    <input type="email" name="company_email" class="form-control company_email" :placeholder="$t('Email')+'..'">
                                    <div class="validation_error" v-if="errors.company_email" v-html="errors.company_email[0]" ></div>
                                </div>
                            </div>
        
           
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Profile picture') }}</label>
                                    <input type="file" name="corporate_user_profile" class="corporate_user_profile"  @change="corporate_user_profilemethod">
                                    <div class="validation_error" v-if="errors.corporate_user_profile" v-html="errors.corporate_user_profile[0]" ></div>
                                    <div class="priview_image">
                                       <img @error="imageLoadError" v-if="corporate_user_profile_preview" :src="corporate_user_profile_preview" />
                                    </div>
                                </div>
                            </div> 


                          <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Password') }}  <span style="color:#f00">*</span></label>
                                    <input type="password" name="password"  class="form-control company_password" :placeholder="$t('Password')+'..'" required>
                                    <div class="validation_error" v-if="errors.password" v-html="errors.password[0]" ></div>
                                </div>
                          </div>
                          <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Confirm Password') }} <span style="color:#f00">*</span></label>
                                    <input type="password"  name="password_confirmation" class="form-control company_password_confirmation"  :placeholder="$t('Confirm Password')+'..'" required>
                                    <div class="validation_error" v-if="errors.password_confirmation" v-html="errors.password_confirmation[0]" ></div>
                                </div>
                          </div>
                        </div>


                        <div class="row info_seperate">
                            <div class="col-lg-12 vendor_title">
                                 <h5 class="font-weight-bold"> {{ $t('Company Information') }}</h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Company Name') }} <span style="color:#f00">*</span></label>
                                    <input type="text" name="company_name" class="form-control company_name" :placeholder="$t('Company Name')+'..'" required>
                                    <div class="validation_error" v-if="errors.company_name" v-html="errors.company_name[0]" ></div>
                                </div>
                            </div>

                             <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Company Phone') }}</label>
                                    <input type="text" name="company_phone" class="form-control company_phone" :placeholder="$t('Company Phone')+'..'" >
                                    <div class="validation_error" v-if="errors.company_phone" v-html="errors.company_phone[0]" ></div>
                                </div>
                            </div>
                             <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Company Email') }} </label>
                                    <input type="text" name="user_email" class="form-control user_email" :placeholder="$t('Company Email')+'..'">
                                    <div class="validation_error" v-if="errors.user_email" v-html="errors.user_email[0]" ></div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('DBID') }}<span style="color:#f00">*</span> </label>
                                    <input type="text" name="dbid" class="form-control dbid" :placeholder="$t('DBID')+'..'" required>
                                    <div class="validation_error" v-if="errors.dbid" v-html="errors.dbid[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Division') }}<span style="color:#f00">*</span></label>
                                        <select @change.prevent="getDistrict()" name="division" id="division" class="form-control" required>
                                                <option disabled selected>--Select Division--</option>
                                                <option value="68" >Dhaka</option>
                                                <option value="36">Chattogram</option>
                                                <option value="60">Rajshahi</option>
                                                <option value="65">Khulna</option>
                                                <option value="66">Barishal</option>
                                                <option value="67">Sylhet</option>
                                                <option value="69">Rangpur</option>
                                                <option value="6175">Mymensingh</option>
                                        </select>
                                    <div class="validation_error" v-if="errors.division" v-html="errors.division[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('District') }} <span style="color:#f00">*</span></label>
                                        <select @change.prevent="getUpazila()" name="district" id="district" class="form-control" required>
                                                <option disabled selected>--Select District--</option>
                                                <option data-removeable="true" v-for="(district,index) in districts" :key="index" :value="district.id">{{district.title}}</option>
                                        </select>
                                    <div class="validation_error" v-if="errors.district" v-html="errors.district[0]" ></div>
                                </div>
                            </div>


                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Upazila / Thana') }} <span style="color:#f00">*</span></label>
                                        <select @change.prevent="getUnion()" name="upazila" id="upazila" class="form-control upazila" required>
                                                <option disabled selected>--Select Upazila--</option>
                                                <option data-removeable="true" v-for="(upazila,index) in upazilas" :key="index" :value="upazila.id">{{upazila.title}}</option>
                                        </select>
                                    <div class="validation_error" v-if="errors.upazila" v-html="errors.upazila[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Union / Area') }} <span style="color:#f00">*</span></label>
                                        <select name="union" id="union" class="form-control" required>
                                                <option disabled selected>--Select Union--</option>
                                                <option data-removeable="true" v-for="(union,index) in unions" :key="index" :value="union.id">{{union.title}}</option>
                                        </select>
                                    <div class="validation_error" v-if="errors.union" v-html="errors.union[0]" ></div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Address') }}</label>
                                        <textarea class="form-control company_address" name="address" rows="3" :placeholder="$t('Address')+'..'" style="height:calc(1.5em + 0.75rem + 2px);"></textarea>
                                    <div class="validation_error" v-if="errors.address" v-html="errors.address[0]" ></div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="row">
                            <div class="col-lg-4"> </div>
                            <div class="col-12 col-sm-12 col-12 col-sm-6 col-md-4 col-lg-4">
								<div class="form-group">
                                    <input type="submit" class="singin-with-google" :value="$t('join now')">
                                </div>
                            </div>
                            <div class="col-lg-4"> </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-6 text-right"><p>{{ $t('Already have a accont') }} ? </p></div>
                             <div class="col-lg-6"><b><router-link :to="{name: 'login'}"> {{ $t('Login') }} </router-link> </b>  </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-1 col-lg-1"></div>
        </div>
    </div>
</section>






</div>
</template>




<script>
import Form from 'vform'
import axios from 'axios'


export default {
	data(){
		return{
            errors:{},
            districts:{},
            upazilas:{},
            unions:{},
            show:false,
            errors: [],
            corporate_user_profile_preview:null,
            corporate_user_profile_file:'',
            shopLogo:'',
            shopbanner:'',
            tradeLicense:'',
		}
	},

  methods: {
    imageLoadError(event){
        event.target.src = "/images/notfound.png";
    },
   async getDistrict(){
        let id =  jQuery('#division').find('option:selected').val();
       await axios.get(this.$baseUrl + "/api/v1/get-district/"+id).then((response) => {
            this.upazilas = {};
            this.unions = {};
            this.districts = response.data;
        });
    },
   async getUpazila(){
        let id =  jQuery('#district').find('option:selected').val();
       await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
            this.unions = {};
            this.upazilas = response.data;
        });
    },
    async getUnion(){
        let id =  jQuery('#upazila').find('option:selected').val();
       await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
            this.unions = response.data;
        });
    },

    corporate_user_profilemethod(e){
        const file = e.target.files[0]
        this.corporate_user_profile_file = file;
        this.corporate_user_profile_preview = URL.createObjectURL(file);
    },

    corporateUserRegister(){
        let token = localStorage.getItem("token");
        let axiosConfig = {
            headers: {
                'Content-Type': 'application/json;charset=UTF-8',
                "Access-Control-Allow-Origin": "*",
                'Authorization': 'Bearer '+token
            }
        }

        let formData = new FormData();

        //Personal info
        formData.append('user_name', $('.user_name').val());
        formData.append('user_phone', $('.user_phone').val());
        formData.append('user_email', $('.user_email').val());
        formData.append('password', $('.company_password').val());
        formData.append('password_confirmation', $('.company_password_confirmation').val());
        formData.append('corporate_user_picture', this.corporate_user_profile_file);

        //Company info
        formData.append('company_name', $('.company_name').val());
        formData.append('company_phone', $('.company_phone').val());
        formData.append('company_email', $('.company_email').val());
        formData.append('company_division', $('#division').find('option:selected').val());
        formData.append('company_district', $('#district').find('option:selected').val());
        formData.append('company_area', $('#upazila').find('option:selected').val());
        formData.append('company_union', $('#union').find('option:selected').val());
        formData.append('company_address', $('.company_address').val());
        formData.append('dbid', $('.dbid').val());


        axios.post(this.$baseUrl+'/api/v1/corporate-user-register', formData, axiosConfig).then(response => {
            if(response.data.status == 1){
                swal({
                    title: 'Registration successfull.',
                    icon: "success",
                    timer: 3000
                }).then(()=>{
                    $('.vendor_regi_form')[0].reset();
                    $('.validation_error').text('');
                    $('.validation_error').html('');
                     this.$router.push({name:'home'});
                });
                
            }else if(response.data.status == 5){
                swal({
                    title: response.data.message,
                    icon: "error",
                    timer: 9000
                });
            }else{
                this.errors = response.data.message;
            }
        });
    },
    scrollToTop() {
        window.scrollTo(0,0);
    }

  },
    mounted(){
        this.scrollToTop();
        document.title = "MabiY Shop | Join as seller"; 
    }

}
</script>












