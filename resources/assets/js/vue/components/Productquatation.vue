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
                    <div class="order_page"> 
                        <div v-if="orders_status > 0" class="user-orders" style="overflow-x:auto;">
                            <table class="table table-bordered user-orders-full" width="100%">
                                <thead>
                                <tr>
                                    <th width="10%">{{ $t('SL.') }}</th>
                                    <th width="15%">{{ $t('Date') }}</th>
                                    <th width="15%">{{ $t('Request ID') }}</th>
                                    <th width="10%">{{ $t('Qty') }}</th>
                                    <th width="10%"> {{ $t('Amount') }}</th>
                                    <th width="10%"> {{ $t('Discount') }}</th>
                                    <th width="15%"> {{ $t('Status') }}</th>
                                    <th width="20%"  style="text-align: right !important;"> {{ $t('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(order, index) in orders.data" :key="index">

                                    <td>{{index+1}}</td>
                                    <td>{{ order.created_at | formatDate }} </td>
                                    <td> MSC{{order.id}}</td>
                                    <td> {{order.qty}}</td>
                                    <td>BDT {{order.amount}}</td>
                                    <td>BDT {{ order.discount }}</td>
                                    <td> 
                                        <span class="badge" :style="{ background: order.statuses.color_code, color:'#fff'}" > {{ order.statuses.title }} </span>
                                    </td>
                                    <td style="text-align: right !important;"> 
                                        <router-link :to="{name: 'quatationDetails', params: {id: order.id } }">  <button class="btn btn-primary btn-sm mb-2">  {{ $t('Details') }}</button> </router-link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="user-orders no_item_center" style="overflow-x:auto;">
                            <h4 class="text-center"> {{ $t('No Product Quotation Found!') }} </h4>
                        </div>		
                    </div>
    
                    <div class="row order_pagination">
                        <div class="col-md-12">
                            <pagination :data="orders" @pagination-change-page="load_orders"></pagination>
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
    import moment from 'moment';
    import pagination from 'laravel-vue-pagination';
    
    export default {
        data(){
            return{
                authenticated: false,
                userData:'',
                image:'',
                profile_picture:'',
                baseurl:'',
                user:'',
                orders:{},
                orders_status:'',
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

    
            cancelOrder(order_id){
                let token = localStorage.getItem("token");
                let axiosConfig = {
                    headers: {
                        'Content-Type': 'application/json;charset=UTF-8',
                        "Access-Control-Allow-Origin": "*",
                        'Authorization': 'Bearer '+token
                    }
                }
                axios.get(this.$baseUrl+'/api/v1/cancel-order/'+order_id, axiosConfig).then(response => {
                    this.load_orders();
                    if(response.data.status == 1){
                        swal({
                            title: response.data.message,
                            icon: "success",
                            timer: 3000
                        });
                    }else{
                        swal("Sorry" , response.data.message,  "error" );
                    }
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
    
            
            load_orders(page=1){
                let token = localStorage.getItem("token");
                
                let axiosConfig = {
                  headers: {
                      'Content-Type': 'application/json;charset=UTF-8',
                      "Access-Control-Allow-Origin": "*",
                      'Authorization': 'Bearer '+token
                  }
                }
                axios.get(this.$baseUrl + "/api/v1/get-user-quatations/?page="+page, axiosConfig).then((response) => {
                    this.orders = response.data.orders;
                    this.orders_status = response.data.status;
                });
            },
            scrollToTop(){
                window.scrollTo(0,0);
            }
        },
        mounted(){
            this.scrollToTop();
            this.checkAuth();
            this.getUserDetails();
            this.baseurl = this.$baseUrl;
            this.load_orders();
            document.title = "MabiY Shop | Product Quotation";  
        }
    }
    </script>