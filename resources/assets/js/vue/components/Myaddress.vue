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
    
                <div class="form-title text-center">
                <h5><b>{{ $t('Select Address from Address book') }}</b></h5>
                </div>
                <div class="fulltab">
                    <ul class="border-0 mb-3">
                        <li id="address_list" class="btn btn-primary active mr-3 "><a data-toggle="tab" href="#list">{{ $t('Address book') }}</a></li>
                        <li id="address_add" class="btn btn-primary"><a data-toggle="tab" href="#addnew"> <i class="fa fa-plus"></i> {{ $t('Add new address') }}</a></li>
                    </ul>
                    
                    <div id="list" class="mytab list_tab">
                        <table  width="100%" class="table table-hover">
                            <thead>
                                <tr>
                                    <th  width="15%"> {{ $t('Full Name') }} </th>
                                    <th  width="15%"> {{ $t('Phone') }}</th>
                                    <th  width="35%"> {{ $t('Address') }}</th>
                                    <th  width="10%"> {{ $t('Defalut') }}</th>
                                    <th  width="25%">{{ $t('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <tr v-for="(address, index) in logged_in_user_address" :key="index">
                                    <td> {{ address.shipping_first_name }}  {{ address.shipping_last_name }}  </td>
                                    <td> {{ address.shipping_phone }} </td>
                                    <td> 
                                        <span v-if="address.division">{{ address.division.title }}</span>,  
                                        <span v-if="address.district"> {{ address.district.title }}</span>, 
                                        <span v-if="address.upazila"> {{ address.upazila.title }}</span>, 
                                        <span v-if="address.union > 0">{{ address.union.title }} </span> <br/> 
                                        <span v-if="address.shipping_address">{{ address.shipping_address }}</span> 
                                    </td>
                                    <td> 
                                        <span v-if="logged_in_user.default_address_id == address.id">
                                            <div class="select_address" title="It is your default address"> </div>
                                        </span>
                                        <span v-else>
                                            <div class="unselect_address" title="Make this address default"> </div>
                                        </span>
    
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm mb-1" @click.prevent="change_address(address.id)">{{ $t('Defalut') }}</button> 
                                            <button type="button" class="btn btn-info btn-sm  mb-1" @click.prevent="update_adress_page(address.id)"> {{ $t('Edit') }}</button> 
                                            <button type="button" class="btn btn-danger btn-sm mb-1" @click.prevent="deletetAdress(address.id)"> {{ $t('Delete') }}</button>
                                        </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                    <div id="add" class="mytab add_tab">
                        <div class="col-md-12">
                                <form @submit.prevent="addNewAddress()">
                                <div class="options">
                                    <div class="row text-left">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ $t('Full Name') }}<span style="color:#f00">*</span></label>
                                                <input type="text" class="form-control shipping_first_name1" :placeholder="$t('Full Name')+'..'" required>
                                                <div class="validation_error" v-if="errors.shipping_first_name" v-html="errors.shipping_first_name[0]" />
                                            </div>
                                        </div>
    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">  {{ $t('Phone') }} <span style="color:#f00">*</span></label>
                                                <input type="text" class="form-control popup_phone1" :placeholder="$t('Phone')+'..'" required>
                                                <div class="validation_error" v-if="errors.shipping_phone" v-html="errors.shipping_phone[0]" />
                                            </div>
                                        </div>
    
                                        
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('District') }}<span style="color:#f00">*</span></label>
                                                        <select  @change.prevent="getUpazila()" name="district" id="district1" class="form-control">
                                                                <option disabled selected>--Select District--</option>
                                                                <option data-removeable="true" v-for="(district,index) in districts" :key="index" :value="district.id">{{district.title}}</option>
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_district" v-html="errors.shipping_district[0]" />
                                                </div>
                                        </div>
    
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for=""> {{ $t('Upazila / Thana') }} <span style="color:#f00">*</span></label>
                                                        <select  @change.prevent="getUnion()" name="upazila" id="upazila1" class="form-control">
                                                                <option disabled selected>--Select Upazila--</option>
                                                                <option data-removeable="true" v-for="(upazila,index) in upazilas" :key="index" :value="upazila.id">{{upazila.title}}</option>
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_thana" v-html="errors.shipping_thana[0]" />
                                                </div>
                                        </div>
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('Union / Area') }}</label>
                                                        <select name="union" id="union1" class="form-control">
                                                                <option value="" selected>--Select Union--</option>
                                                                <option data-removeable="true" v-for="(union,index) in unions" :key="index" :value="union.id">{{union.title}}</option>
                                                        </select>
                                                        <div class="validation_error" v-if="errors.shipping_union" v-html="errors.shipping_union[0]" />
                                                </div>
                                        </div>
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="">{{ $t('Post code') }}</label>
                                                    <input type="text" class="form-control popup_post_code1" :placeholder="$t('Post code')+'..'" >
                                                    <div class="validation_error" v-if="errors.shipping_postcode" v-html="errors.shipping_postcode[0]" />
                                                </div>
                                        </div>
    
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">  {{ $t('Email') }} </label>
                                                <input type="text" class="form-control shipping_email1" :placeholder="$t('Email')+'..'" >
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for=""> {{ $t('Address') }} <span style="color:#f00">*</span></label>
                                                <textarea name="" id="" cols="30" rows="3" class="form-control shipping_address1" :placeholder="$t('Address')+'..'" required></textarea>
                                                <div class="validation_error" v-if="errors.shipping_address" v-html="errors.shipping_address[0]" />
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-right"> <button type="submit" class="btn btn-primary">{{ $t('Add new address') }}</button> </p>
                                </div>
                                </form>
    
    
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
                authenticated: false
            }
        },
        components:{
            Leftsidebar
        },
        methods:{
            imageLoadError(event){
                event.target.src = "/images/notfound.png";
            },
            proceedToPay(){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                  headers: {
                      'Content-Type': 'application/json;charset=UTF-8',
                      "Access-Control-Allow-Origin": "*",
                      'Authorization': 'Bearer '+token
                  }
                }
    
                if(jQuery('.required_addtess').attr('data-required-address') == 'true'){
                    swal ( "Oops" , 'Please select your default shipping address!',  "error");
                    return true;
                }
    
                if (jQuery('.agree').is(':checked')) {
    
                        let shipping_method  = [];
    
                        jQuery('.select_shipping_options .selected_shipping').each(function(key,val){
                            shipping_method[key] = { 
                                product_id : jQuery(this).attr('data-product-id'),
                                shipping_method: jQuery(this).attr('data-shipping-method'),
                                shipping_cost: jQuery(this).attr('data-shipping-cost'),
                            }
                        });
    
                        let formData = {
                            note: jQuery('.form_note').val(),
                            coupon: jQuery('#couponeCode').val(),
                            payment_method : jQuery('.paymentmethod .selected_payment').attr('data-payment-method'),
                            shipping_method : shipping_method
                        }
         
                        axios.post(this.$baseUrl+'/api/v1/order', formData, axiosConfig).then(response => {
    
                            if(response.data.status == 1){
                                swal({
                                    title: 'Your order has been placed successfully.',
                                    icon: "success",
                                    timer: 3000
                                });
                                this.$store.dispatch('loadedCart');
                                this.$router.push({name:'orderDetails',params: {id: response.data.invoice.order_id } });
                            }else if( response.data.status == 302){
                                window.location.href = response.data.url;
                            }else{
                                swal ( "Oops" , 'Order Failed! Please try again later',  "error");
                            }
                        });
                }else{
                    swal ( "Oops" , 'Please accept terms and conditions!',  "error");
                }
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
                formData.append('shipping_first_name', $('.shipping_first_name1').val());
                formData.append('shipping_last_name', $('.shipping_last_name2').val());
                formData.append('shipping_division', $('#division1').find('option:selected').val());
                formData.append('shipping_district', $('#district1').find('option:selected').val());
                formData.append('shipping_thana', $('#upazila1').find('option:selected').val());
                formData.append('shipping_union', $('#union1').find('option:selected').val());
                formData.append('shipping_postcode', $('.popup_post_code1').val());
                formData.append('shipping_phone', $('.popup_phone1').val());
                formData.append('shipping_email', $('.shipping_email1').val());
                formData.append('shipping_address', $('.shipping_address1').val());
                
    
    
                axios.post(this.$baseUrl+'/api/v1/add-new-address', formData, axiosConfig).then(response => {
                    if(response.data.status == 1){
                        swal({
                            title: 'New address added successfull.',
                            icon: "success",
                            timer: 3000
                        });
                        this.$store.dispatch('loadedUser');
                        jQuery('#address_list').trigger('click');
                        
                    }else{
                        this.errors = response.data.message;
                    }
                });
            },
            change_address(address_id){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                let formData = new FormData();
                formData.append('address_id', address_id);
                formData.append('pickpoint', 0);
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
            let id =  jQuery('#division1').find('option:selected').val();
            await axios.get(this.$baseUrl + "/api/v1/get-district/"+0).then((response) => {
                    this.upazilas = {};
                    this.unions = {};
                    this.districts = response.data;
                });
            },
            
    
            async getUpazila(){
                let id =  jQuery('#district1').find('option:selected').val();
                await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
                        this.unions = {};
                        this.upazilas = response.data;
                    });
                },
                async getUnion(){
                    let id =  jQuery('#upazila1').find('option:selected').val();
                await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
                        this.unions = response.data;
                    });
            },
    
            updateShippingOption(shipping_method, shipping_cost, rowId){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                let formData = new FormData();
                formData.append('shipping_method', shipping_method);
                formData.append('shipping_cost', shipping_cost);
                formData.append('rowId', rowId);
                axios.post(this.$baseUrl+'/api/v1/update-shipping-option', formData,axiosConfig).then(response =>{
                    if(response.data.status == '1'){
                        this.$store.dispatch('loadedCart');
                        swal({
                            title: "Shipping method updated Successfully.",
                            icon: "success",
                            timer: 3000
                        });
                    }else{
                        swal ( "Oops", response.data.message, "error");
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
            deletetAdress(address_id){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                  headers: {
                      'Content-Type': 'application/json;charset=UTF-8',
                      "Access-Control-Allow-Origin": "*",
                      'Authorization': 'Bearer '+token
                  }
                }
                axios.get(this.$baseUrl+'/api/v1/delete-address/'+address_id, axiosConfig).then(response =>{
                    if(response.data.status == '1'){
                        this.$store.dispatch('loadedUser');
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
            update_adress_page(address_id){
                this.$router.push({
                    name:'updateaddress',
                    params: { address_id: address_id}
                });
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
    
           const plugin = document.createElement("script");
           plugin.setAttribute( "src",this.$frontendUrl+"/assets/js/custom.js");
           plugin.async = true;
           document.body.appendChild(plugin);

            this.getDistrict();
    
            this.scrollToTop();
            this.checkAuth();
            this.getUserDetails();
            this.baseurl = this.$baseUrl;
            document.title = "MabiY Shop | Update Address";  
        }
    }
    </script>