<template>
<div>

<section id="register-page">
    <div class="container">
        <div class="row" id="join_seller_row">
            <div class="col-md-1 col-lg-1"></div>
            <div class="col-md-10 col-lg-10">
                <div class="register-form seller_registration">
                    <h4>{{ $t('Join As Seller') }}</h4>
                        <form @submit.prevent="vendorRegister" class="vendor_regi_form"  enctype='multipart/form-data'>
                        <div class="row info_seperate">
                            <div class="col-lg-12 vendor_title">
                                 <h5 class="font-weight-bold"> {{ $t('Personal Information') }}  </h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Full Name') }} <span style="color:#f00">*</span></label>
                                    <input  type="text" name="name" class="form-control name" :placeholder="$t('Full Name')+'..'" required>
                                    <div class="validation_error" v-if="errors.name" v-html="errors.name[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Phone') }}<span style="color:#f00">*</span></label>
                                    <input type="text" name="phone" class="form-control phone" :placeholder="$t('Phone')+'..'" required>
                                    <div class="validation_error" v-if="errors.phone" v-html="errors.phone[0]" ></div>
                                </div>
                            </div>
                             <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Email') }}</label>
                                    <input type="email" name="email" class="form-control email" :placeholder="$t('Email')+'..'">
                                    <div class="validation_error" v-if="errors.email" v-html="errors.email[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('NID Front side') }} </label>
                                    <input  type="file"  name="nid_front_side" class="nid_front_side" @change="nid_front_side">
                                    <div class="validation_error" v-if="errors.nid_front_side" v-html="errors.nid_front_side[0]" ></div>
                                    <div class="priview_image">
                                        <img @error="imageLoadError" v-if="nid_front_side_preview" :src="nid_front_side_preview" />
                                    </div>
                                </div>
                            </div>
                           <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('NID Back Side') }}</label>
                                    <input  type="file" name="nid_back_side" class="nid_back_side" @change="nid_back_side">
                                    <div class="validation_error" v-if="errors.nid_back_side" v-html="errors.nid_back_side[0]" ></div>
                                    <div class="priview_image">
                                        <img @error="imageLoadError" v-if="nid_back_side_preview" :src="nid_back_side_preview" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Profile picture') }}</label>
                                    <input type="file" name="profile_picture" class="profile_picture"  @change="profile_picture">
                                    <div class="validation_error" v-if="errors.profile_picture" v-html="errors.profile_picture[0]" ></div>
                                    <div class="priview_image">
                                       <img @error="imageLoadError" v-if="profile_picture_preview" :src="profile_picture_preview" />
                                    </div>
                                </div>
                            </div> 


                          <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Password') }}  <span style="color:#f00">*</span></label>
                                    <input type="password" name="password"  class="form-control password" :placeholder="$t('Password')+'..'" required>
                                    <div class="validation_error" v-if="errors.password" v-html="errors.password[0]" ></div>
                                </div>
                          </div>
                          <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Confirm Password') }} <span style="color:#f00">*</span></label>
                                    <input type="password"  name="password_confirmation" class="form-control password_confirmation"  :placeholder="$t('Confirm Password')+'..'" required>
                                    <div class="validation_error" v-if="errors.password_confirmation" v-html="errors.password_confirmation[0]" ></div>
                                </div>
                          </div>

                        </div>


                        <div class="row info_seperate">
                            <div class="col-lg-12 vendor_title">
                                 <h5 class="font-weight-bold"> {{ $t('Shop Information') }}</h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Shop Name') }} <span style="color:#f00">*</span></label>
                                    <input type="text" name="shop_name" class="form-control shop_name" :placeholder="$t('Shop Name')+'..'" required>
                                    <div class="validation_error" v-if="errors.shop_name" v-html="errors.shop_name[0]" ></div>
                                </div>
                            </div>

                             <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Shop Phone') }}</label>
                                    <input type="text" name="shop_phone" class="form-control shop_phone" :placeholder="$t('Shop Phone')+'..'" >
                                    <div class="validation_error" v-if="errors.shop_phone" v-html="errors.shop_phone[0]" ></div>
                                </div>
                            </div>
                             <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Shop Email') }} </label>
                                    <input type="text" name="shop_email" class="form-control shop_email" :placeholder="$t('Shop Email')+'..'">
                                    <div class="validation_error" v-if="errors.shop_email" v-html="errors.shop_email[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Shop Logo') }}</label>
                                    <input  type="file" name="shop_logo" class="nid_front_side" @change="shop_logo" >
                                    <div class="validation_error" v-if="errors.shop_logo" v-html="errors.shop_logo[0]" ></div>
                                    <div class="priview_image">
                                       <img @error="imageLoadError" v-if="shop_logo_preview" :src="shop_logo_preview" />
                                    </div>
                                </div>
                            </div>
                           <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for=""> {{ $t('Shop Banner') }}</label>
                                    <input  type="file" name="shop_banner" class="nid_back_side" @change="shop_banner">
                                    <div class="validation_error" v-if="errors.shop_banner" v-html="errors.shop_banner[0]" ></div>
                                    <div class="priview_image">
                                       <img @error="imageLoadError" v-if="shop_banner_preview" :src="shop_banner_preview" />
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="">{{ $t('Trade License') }}</label>
                                    <input type="file" name="trade_license" @change="trade_license">
                                    <div class="validation_error" v-if="errors.trade_license" v-html="errors.trade_license[0]" ></div>
                                    <div class="priview_image">
                                       <img @error="imageLoadError" v-if="tradeLicense_preview" :src="tradeLicense_preview" />
                                    </div>
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
                                        <select @change.prevent="getUnion()" name="upazila" id="upazila" class="form-control" required>
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

                            <div class="col-12 col-md-8 col-lg-8">
                                <div class="form-group">
                                    <label for="">{{ $t('Address') }}</label>
                                        <textarea class="form-control" name="address" id="address" rows="3" :placeholder="$t('Address')+'..'" style="height:calc(1.5em + 0.75rem + 2px);"></textarea>
                                    <div class="validation_error" v-if="errors.address" v-html="errors.address[0]" ></div>
                                </div>
                            </div>
                        </div>

                        


                        <div class="row info_seperate">
                            <div class="col-lg-12 vendor_title">
                                 <h5 class="font-weight-bold"> {{ $t('Bank Account Details') }}  </h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">{{ $t('Bank Name') }} </label>
                                    <input type="text" name="bank_name" class="form-control bank_name" :placeholder="$t('Bank Name')+'..'">
                                    <div class="validation_error" v-if="errors.bank_name" v-html="errors.bank_name[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for=""> {{ $t('Account Name') }}</label>
                                    <input type="text" name="account_name" class="form-control account_name" :placeholder="$t('Account Name')+'..'">
                                    <div class="validation_error" v-if="errors.account_name" v-html="errors.account_name[0]" ></div>
                                </div>
                            </div>
                             <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for=""> {{ $t('Account Number') }}</label>
                                    <input type="text" name="account_number" class="form-control account_number" :placeholder="$t('Account Number')+'..'" >
                                    <div class="validation_error" v-if="errors.account_number" v-html="errors.account_number[0]" ></div>
                                </div>
                            </div>
                             <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for=""> {{ $t('Routing Number') }}  </label>
                                    <input type="text" name="routing_number" class="form-control routing_number"  :placeholder="$t('Routing Number')+'..'">
                                    <div class="validation_error" v-if="errors.routing_number" v-html="errors.routing_number[0]" ></div>
                                </div>
                            </div>
                        </div>




                        <div class="row info_seperate">
                            <div class="col-lg-12 vendor_title">
                                 <h5 class="font-weight-bold"> {{ $t('Mobile Financial Service Accounts') }} </h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for=""> {{ $t('Bkash Number') }} </label>
                                    <input type="text" name="bkash" class="form-control bkash" :placeholder="$t('Bkash Number')+'..'">
                                    <div class="validation_error" v-if="errors.bkash" v-html="errors.bkash[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">  {{ $t('Rocket Number') }} </label>
                                    <input type="text" name="rocket" class="form-control rocket" :placeholder="$t('Rocket Number')+'..'">
                                    <div class="validation_error" v-if="errors.rocket" v-html="errors.rocket[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for=""> {{ $t('Nagad Number') }} </label>
                                    <input type="text" name="nagad" class="form-control nagad" :placeholder="$t('Nagad Number')+'..'">
                                    <div class="validation_error" v-if="errors.nagad" v-html="errors.nagad[0]" ></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">{{ $t('Upay Number') }}  </label>
                                    <input type="text" name="upay" class="form-control upay" :placeholder="$t('Upay Number')+'..'">
                                    <div class="validation_error" v-if="errors.upay" v-html="errors.upay[0]" ></div>
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
                             <div class="col-lg-6"><b><a href="https://seller.mabiyshop.com" target="__blank"> {{ $t('Login as seller') }} </a></b>  </div>
                            
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
            nid_front_side_file:'',
            nid_front_side_preview:null,
            nid_back_side_preview:null,
            profile_picture_preview:null,
            shop_logo_preview:null,
            shop_banner_preview:null,
            tradeLicense_preview:null,
            nid_back_side_file:'',
            profile_picture_file:'',
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
    nid_front_side(e){
        const file = e.target.files[0]
        this.nid_front_side_file = file;
        this.nid_front_side_preview = URL.createObjectURL(file);
    },
    nid_back_side(e){
        const file = e.target.files[0]
        this.nid_back_side_file = file;
        this.nid_back_side_preview = URL.createObjectURL(file);
        
    },
    profile_picture(e){
        const file = e.target.files[0]
        this.profile_picture_file = file;
        this.profile_picture_preview = URL.createObjectURL(file);
    },
    shop_logo(e){
        const file = e.target.files[0]
        this.shopLogo = file;
        this.shop_logo_preview = URL.createObjectURL(file);
        
    },
    shop_banner(e){
        const file = e.target.files[0]
        this.shopbanner = file;
        this.shop_banner_preview = URL.createObjectURL(file);
    },
    trade_license(e){
        const file = e.target.files[0]
        this.tradeLicense = file;
        this.tradeLicense_preview = URL.createObjectURL(file);
    },
    vendorRegister(){
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
        formData.append('name', $('.name').val());
        formData.append('phone', $('.phone').val());
        formData.append('email', $('.email').val());
        formData.append('nid_front_side', this.nid_front_side_file);
        formData.append('nid_back_side', this.nid_back_side_file);
        formData.append('profile_picture', this.profile_picture_file);

        //Shop info
        formData.append('shop_name', $('.shop_name').val());
        formData.append('shop_phone', $('.shop_phone').val());
        formData.append('shop_email', $('.shop_email').val());
        formData.append('shop_division', $('#division').find('option:selected').val());
        formData.append('shop_district', $('#district').find('option:selected').val());
        formData.append('shop_area', $('#upazila').find('option:selected').val());
        formData.append('shop_union', $('#union').find('option:selected').val());
        formData.append('address', $('#address').val());
        formData.append('shop_logo', this.shopLogo);
        formData.append('shop_banner', this.shopbanner);
        formData.append('trade_license', this.tradeLicense);

        //Bank Account Details
        formData.append('bank_name', $('.bank_name').val());
        formData.append('account_name', $('.account_name').val());
        formData.append('account_number', $('.account_number').val());
        formData.append('routing_number', $('.routing_number').val());

        //Mobile bankign
        formData.append('bkash', $('.bkash').val());
        formData.append('nagad', $('.nagad').val());
        formData.append('rocket', $('.rocket').val());
        formData.append('upay', $('.upay').val());
        formData.append('password', $('.password').val());
        formData.append('password_confirmation', $('.password_confirmation').val());

        axios.post(this.$baseUrl+'/api/v1/vendor-register', formData, axiosConfig).then(response => {
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












