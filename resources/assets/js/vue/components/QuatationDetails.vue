<template>
<div>

<section id="profile-page">
    <div class="container">
	<div class="col-md-12 account_wrapper bg_white">
		<div class="row profile">
			<div class="col-md-1">
				<img @error="imageLoadError" v-if="userData.avatar != null" :src="baseurl+'/'+userData.avatar" >
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
            <div class="col-md-2 col-lg-2 profile-navigation">
                <div class="profile-nav">
                    <Leftsidebar></Leftsidebar>
                </div>
            </div>
            <div class="col-md-10 col-lg-10">
                <div class="order_page single_order">
                    <div class='row mb-3 mt-3'>
                        <div class="col-12 col-sm-12 col-md-6">
                            <h5>{{ $t('Quotation Details')}}</h5>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6">

                        <p  class="text-right">
                            <button  v-if="groupedQuatationStatus.id == 1" data-toggle="modal" data-target="#exampleModal" class="btn btn-primary">{{$t('Quotation Action')}}</button>

                            <button v-if="groupedQuatationStatus.id == 6"  type="button" class="btn btn-dark" @click.prevent="printInvoice()"> {{ $t('Print Invoice') }}</button> 

                        </p>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Quotation Action</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form @submit.prevent="QuotationAction()">
                                            <div class="form-group">
                                                <label for="email">Action</label>
                                                <select name="quotation_action" id="quotation_action" class="form-control">
                                                    <option disabled selected>-- Select any action --</option>
                                                    <option value="2">Accept for delivery</option>
                                                    <option value="5">Cancel this quotation</option>
                                                    <option value="1">Request for review</option>
                                                </select>
                                                <div class="validation_error" v-if="errors.quotation_action_status" v-html="errors.quotation_action_status[0]" ></div>
                                            </div>

                                            <div class="accept_div">
                                                <div class="form-group">
                                                    <label for="pwd">Workorder</label>
                                                    <input type="file" id="Workorderfile"  @change="Workorderfilemethod" class="form-control">
                                                    <input type="hidden" id="corporate_request_id" :value="corporate_request_id">
                                                
                                                    <div class="validation_error" v-if="errors.workorderfile" v-html="errors.workorderfile[0]" ></div>
                                                </div>

                                                <div class="form-group">
                                                    <label for="pwd">Preferable Date For Delivery</label>
                                                    <input type="datetime-local"  name="preferable_date_for_delivery" id="preferable_date_for_delivery" class="form-control">
                                                    <div class="validation_error" v-if="errors.preferable_date_for_delivery" v-html="errors.preferable_date_for_delivery[0]" ></div>
                                                </div>
                                            
                                                <div class="form-group">
                                                    <label for="pwd">{{$t('Delivery Address')}}</label>
                                                    <textarea class="form-control" id="delivery_address" placeholder="Delivery Address.."></textarea>
                                                    <div class="validation_error" v-if="errors.delivery_address" v-html="errors.delivery_address[0]" ></div>
                                                </div>

                                            </div>

                                            <div class="form-group">
                                                <label for="pwd">Note</label>
                                                <textarea class="form-control" id="note" placeholder="Describe anything special.."></textarea>
                                                <div class="validation_error" v-if="errors.note" v-html="errors.note[0]" ></div>
                                            </div>

                                        
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>

                                    </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mt-2">
                           
                            <div v-if="groupedQuatation" class="single_order_details mb-3 ">
                                <div style="min-height: 240px !important" class="card">
                                    <div class="card-body pb-0">
                                        <ul>
                                            <li> <b>{{ $t('Request ID') }}: </b> <span> MSC{{groupedQuatation.id}}</span>  </li>
                                            <li> <b>{{ $t('Request Date') }}: </b> <span> {{ groupedQuatation.created_at | formatDate }}</span> </li>
                                            <li> <b>{{ $t('Total Quantity') }}: </b> <span> {{ groupedQuatation.qty }}</span> </li>
                                            <li> <b>{{ $t('Total Price') }}: </b> <span> BDT {{ groupedQuatation.amount }}</span> </li>
                                            <li> <b>{{ $t('Total Discount') }}: </b> <span>BDT {{ groupedQuatation.discount }}</span> </li>
                                            <li> <b>{{ $t('Payment Amount') }}: </b> <span>BDT {{ groupedQuatation.amount - groupedQuatation.discount }}</span> </li>

                                            <li v-if="groupedQuatation.payment_status"> <b>{{ $t('Payment Status') }}: </b> <span>{{ groupedQuatation.payment_status.title}}</span> </li>

                                            
                                            <li v-if="groupedQuatation.delivery_address"> <b>{{ $t('Delivery Address') }}: </b> <span> {{ groupedQuatation.delivery_address }}</span> </li>
                                            
                                            <li v-if="groupedQuatation.preferable_date_for_delivery"> <b>{{ $t('Preferable Date For Delivery') }}: </b> <span> {{ groupedQuatation.preferable_date_for_delivery | formatDate }}</span> </li>
                                            <li v-if=" groupedQuatation.delivery_date"> <b>{{ $t('Delivery Date') }}: </b> <span> {{ groupedQuatation.delivery_date | formatDate }}</span> </li>

                                            <li> <b>{{ $t('Status') }}: </b> <span>{{ groupedQuatation.statuses.title }}</span> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                                <div class="single_order_details mb-4">
                                    <ul v-if="single_order">
                                        <div class="user-orders border_only" style="overflow-x:auto;" id="single_order_details">
                                            <table class="table table-bordered mt-2" width="100%">
                                                <thead>
                                                <tr>
                                                    <th width="35%">{{ $t('Product') }}</th>
                                                    <th width="15%">{{ $t('Price') }}</th>
                                                    <th width="5%"> {{ $t('Qty') }}</th>
                                                    <th width="5%"> {{ $t('Discount') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(order, index) in single_order" :key="index">
                                                        <td> <router-link v-if="order.productname" class="text-dark" :to="{ name: 'product', params: {slug: order.productname.slug } }">{{order.productname.title}}</router-link> </td>
                                                        <td> <span v-if="order.productname">BDT {{order.price}}</span>  </td>
                                                        <td> <span v-if="order.productname">{{order.qty}}</span>  </td>
                                                        <td> <span v-if="order.productname">BDT {{order.discount}}</span>  </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </ul>
                                </div>
                        </div>

                        <div v-if="groupedQuatation.negotiations"  class="col-md-12 mt-2">

                            <h5>{{ $t('Quotation Negotiations')}}</h5>
                           <div class="single_order_details mb-3">
                               <div class="card">
                                   <div class="card-body pb-5 pt-5">
                                 
                                        <ul class="chat">


                                            
                                            <span v-for="(negotiation,index) in groupedQuatation.negotiations" :key="index">


                                            <li v-if="negotiation.username" class="left clearfix"><span class="chat-img pull-left">
                                                <img :src="baseurl+'/'+negotiation.username.avatar" alt="User Avatar" class="img-circle avt mr-3" />
                                            </span>
                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        <strong class="primary-font text-uppercase">{{negotiation.username.name}}</strong> 
                                                        <small class="pull-right text-muted"> 
                                                            <span class="fa fa-clock-o mr-1"></span>{{negotiation.created_at | formatDate}}
                                                        </small>
                                                    </div>
                                                    <p>
                                                        {{negotiation.note}}
                                                    </p>
                                                </div>
                                            </li>



                                            <li v-else class="right clearfix"><span class="chat-img pull-right">
                                                <img :src="baseurl+'/'+negotiation.adminname.avatar" alt="Admin Avatar" class="img-circle avt ml-3" />
                                            </span>
                                                <div class="chat-body clearfix">
                                                    <div class="header">
                                                        <small class=" text-muted"> <span class="fa fa-clock-o mr-1"></span>{{negotiation.created_at | formatDate}}</small>
                                                        <strong class="pull-right primary-font text-uppercase">{{negotiation.adminname.name}} <br><small class="group_user">Administrator</small> </strong>
                                                        
                                                    </div>
                                                    <p>
                                                        {{negotiation.note}}
                                                    </p>
                                                </div>
                                            </li>
                                        </span>

                                            
                                        </ul>

                                   </div>
                               </div>
                           </div>
                           
                       </div>

                    </div>


                </div>
            </div>
        </div>

    </div>
	</div>
</section>



<section  id="print_invoice">
    <table style="width: 650px;margin:30px auto">
        <tbody>
            <tr>
                <td style="width: 375px;padding-top: 0px; padding-bottom: 10px;">
                    <p style="margin-bottom: 5px;"><strong style="text-transform:uppercase;font-size: 14px;">Delivery Information</strong><br>
                    <div v-if="groupedQuatation.delivery_address" style="font-size: 12px;padding-right: 0px;">
                        {{groupedQuatation.delivery_address}}
                    </div>
                    </p>
                </td>
                <td style="width: 245px;text-align:right;padding-left: 0px; font-size: 12px; padding-bottom: 10px;">
                    <p style="font-size:12px; padding: 0;margin: 0;width: 280px;">
                        <!-- <h3 style="text-align: right;margin-bottom: 0;"> INVOICE</h3> -->
                        <img :src="baseurl+'/'+site_info.header_logo" alt="" width="70" />
                        <br>
                        <span style="font-weight: 600;"></span> {{ site_info.cnf_address }} 
                        <br>
                        <span style="font-weight: 600;">  HP: </span> {{ site_info.phone_number }} 
                        <br>
                        <span style="font-weight: 600;">  E-mail: </span> {{ site_info.cnf_email }} 
                    </p>
                </td>
                
            </tr>
            <tr>
                <td style="width: 100%;position: relative;"><hr style="width: 100%;margin: 0px;width: 650px;margin: 0px;position: absolute;z-index: 9999;background: #fdfdfd;"></td>
            </tr>
            
        </tbody>
        
    </table>
    

    <table style="width: 650px;margin:50px auto">
        <tbody>
            <tr>
                <td style="width: 650px;">
                    <div class="row" style="display: flex; -ms-flex-wrap: wrap;flex-wrap: wrap;padding: 0!important;">
                        <div class="col-md-6" style="flex: 0 0 65%;max-width: 65%;position: relative;text-align: right!important;padding: 0!important;"> <span style="text-transform: uppercase;font-size: 20px;font-weight: 600;">Corporate Invoice</span>  </div>
                        <div class="col-md-6" style="flex: 0 0 35%;max-width: 35%;position: relative;text-align: right!important;padding: 0!important;">  <span style="text-transform: uppercase;font-size: 14px;font-weight: 600;">  Order ID: MSC{{groupedQuatation.id}}</span> </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>





    <table id="product" style="width: 650px;margin:50px auto">
        <tbody>
            <tr style="padding: 5px;">
                <td style="width: 70px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong>  SL</strong></td>
                <td style="width: 400px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Item</strong></td>
                <td style="width: 200px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Qty</strong></td>
                <td style="width: 200px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Price</strong></td>
                <td style="width: 200px; text-align: left;text-transform: uppercase;font-size: 12px;"><strong> Discount</strong></td>
                <td style="width: 120px; text-align: right;text-transform: uppercase;font-size: 12px;"><strong>Sub Total</strong></td>
            </tr>
            

            <tr style="padding: 5px;border-bottom:1px solid #ebebeb;" v-if="single_order" v-for="(productData, index) in single_order" :key="index">
                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;">{{ index+1 }}</td>

                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;">  
                    {{ productData.productname.title }}
                    <br/><b>SKU:</b> {{ productData.product_sku }}
                    <span  v-if="productData.productname.product_type == 'variable'">
                        <br/>
                        <span style="margin-right:5px;" v-for="(vOption,key) in productData.product_options" :key="key"> <b>{{key}}</b> : {{vOption}}</span>
                    </span>
                </td>

                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;"> <span style="font-size: 12px;"> {{ productData.qty }}</span></td>
                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;"> <span style="font-size: 12px;">BDT {{ productData.price }}</span></td>
                <td style="width: 70px; text-align: left;font-size: 12px;padding-top: 5px;"> <span style="font-size: 12px;">BDT {{ productData.discount }}</span></td>
                <td style="width: 120px; text-align: right;font-size: 12px;">BDT {{ productData.price*productData.qty - productData.discount }}</td>
            </tr>

        </tbody>
    </table>

    <table style="width: 650px;text-align:right; margin:50px auto">
        <tbody>
            <tr>
                <td style="width: 210px;">&nbsp;</td>
                <td style="width: 80px;">
                    <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Sub Total:&nbsp;</strong>BDT 
                        {{ (groupedQuatation.amount) - groupedQuatation.discount }} 
                    </p>

                    <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Shipping Cost (+) :&nbsp;</strong>BDT 
                        {{ groupedQuatation.shipping_cost }} 
                    </p>
          
                   
                    <span>
                        <span v-if="groupedQuatation.payment_status == 6">
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Paid:&nbsp;</strong>BDT {{ (groupedQuatation.amount+groupedQuatation.shipping_cost) - groupedQuatation.discount }} </p>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Due:&nbsp;</strong>BDT 0.00 </p>
                        </span>
                        <span v-else>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Paid:&nbsp;</strong>BDT 0</p>
                            <p style="margin: 1px;font-size: 12px;"><strong style="float: left;">Due:&nbsp;</strong>BDT {{ (groupedQuatation.amount+groupedQuatation.shipping_cost)  - groupedQuatation.discount }}</p>
                        </span>
                    </span>

                </td>
            </tr>
        </tbody>
    </table>

    <div style="width: 650px;text-align:left; margin:50px auto;">
            <p style="font-size:12px;text-align: left;"><b>Payment Details</b> </p>
            <div style="font-size:12px !important;" v-html="groupedQuatation.company_bank_information"></div>
    </div>



    <table style="width: 650px;text-align:left; margin:50px auto">
        <tbody style="text-align: center;">
            <p style="font-size:12px;text-align: center;">Thank you for being with us. Stay connected with <b>kholabazaar.com.bd</b> </p>
            <p style="text-align: center;"><img :src="barcode" alt=""  width="150px" height="30px"></p>
        </tbody>
    </table>


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
            dateTime:'',
            site_info:'',
			userData:'',
			image:'',
			profile_picture:'',
            workorderfile:'',
			baseurl:'',
			user:'',
            single_order:'',
            order_products:[],
            address:'',
            groupedQuatation:'',
            groupedQuatationStatus:'',
            statuses:[],
            showCommentForm:1,
            purchased:true,
            rate:0,
            files: '',
            review_preview:[],
            errors: [],
            errors:{},
            barcode:'',
            corporate_request_id:'',
		}
	},
	components:{
		Leftsidebar,
        pagination
	},
	methods:{
        Workorderfilemethod(e){
            const file = e.target.files[0]
            this.workorderfile = file;
            //this.nid_front_side_preview = URL.createObjectURL(file);
        },

        QuotationAction(){
            let formData = new FormData();
			let id =  jQuery('#quotation_action').find('option:selected').val();

            formData.append('quotation_action_status', id);
            formData.append('preferable_date_for_delivery', $('#preferable_date_for_delivery').val());
            formData.append('note', $('#note').val());
            formData.append('delivery_address', $('#delivery_address').val());
            formData.append('corporate_request_id', $('#corporate_request_id').val());
            formData.append('workorderfile', this.workorderfile);
            
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/quotation-action',formData, axiosConfig).then(response => {
				if(response.data.status == 1){
                     this.load_single_order();
                    swal({
                        title: response.data.message,
                        icon: "success",
                        timer: 3000
                    }).then(()=>{
                        $('.close').trigger('click');
                    });
                }else{
                    this.errors = response.data.message;
                }
              
			});
        },

        handleFilesUpload(e){
            const file = e.target.files
            this.files = file;
            let images = [];
            for (let [key, value] of Object.entries(file)) {
                images.push(URL.createObjectURL(value))
            }
            this.review_preview = images;
        },


        imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },
        date_time(){
            this.dateTime = new Date().toLocaleString();
        },
        site_information(){
            axios.get(this.$baseUrl+'/api/v1/site-info').then(response => {
                this.site_info = response.data;
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

		
        load_single_order(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
			  headers: {
				  'Content-Type': 'application/json;charset=UTF-8',
				  "Access-Control-Allow-Origin": "*",
				  'Authorization': 'Bearer '+token
			  }
			}
            let requeat_id = this.$route.params.id;
            this.corporate_request_id = requeat_id;

            axios.get(this.$baseUrl + "/api/v1/get-single-quatation/"+requeat_id, axiosConfig).then((response) => {
                axios.get("/api/barcode/"+'MSC'+response.data.groupedQuatation.id).then((response) => {
                        this.barcode = response.data
                });
                this.single_order = response.data.quatation;
                this.groupedQuatation = response.data.groupedQuatation;
                this.groupedQuatationStatus = response.data.groupedQuatation.statuses;

      
            });
        },

        printInvoice(){
            var divToPrint= jQuery('#print_invoice').html();
            var newWin=window.open('','Print-Window');
            newWin.document.open();
            newWin.document.write('<html><link href=""><body onload="window.print()">'+divToPrint+'</body></html>');
            newWin.document.close();
            setTimeout(function(){newWin.close();},100);

        },

    	scrollToTop(){
            window.scrollTo(0,0);
        }


	},

    watch:{
        $route(to, from){
            this.scrollToTop();
        }
    },
	mounted(){
        this.scrollToTop();
        this.date_time();
        this.site_information();
        this.getUserDetails();
        this.baseurl = this.$baseUrl;
        this.load_single_order();
        document.title = "MabiY Shop | Quatation Details";  
	}
}
</script>