<template>
<div>


<section id="profile-page">
    <div class="container">
	<div class="col-md-12 account_wrapper bg_white">
		<div class="row profile">
			<div class="col-md-1">
				<img @error="imageLoadError" v-if="userData.avatar != null" :src="baseurl+'/'+userData.avatar" >
				<img @error="imageLoadError" v-else :src="baseurl+'/assets/images/avater.jpg'" >
                
			</div>
			<div class="col-md-11">
				<div class="username">
					<b>{{ userData.name  }}</b>
					<p>{{ userData.email }}</p>
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
                <div class="order_page single_order" >
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-2">
                            <h5>{{ $t('Product Details') }}</h5>
                            <div class="single_order_details mb-3 mt-4">
                                <div class="card" id="single_return_product">
                                    <div class="card-body pb-0">
                                        <ul v-if="single_order">
                                            <li> <b>{{ $t('Order ID') }}: </b> <span> #{{ return_products.id }} </span> </li>
                                            <li> <b>{{ $t('Date') }}: </b> <span> {{ return_products.created_at | formatDate }}</span> </li>
                                            <li> <b>{{ $t('Price') }}: </b> <span> BDT {{ return_products.price }} </span> </li>
                                            <li> <b>{{ $t('Qty') }}: </b> <span> {{ return_products.product_qty }} </span> </li>
                                            <li> <b>{{ $t('Shipping Cost') }}: </b> <span> BDT {{ return_products.shipping_cost }} </span> </li>
                                            <li> <b>{{ $t('Total Price') }}: </b> <span> BDT {{ (return_products.price*return_products.product_qty) + (return_products.shipping_cost*return_products.product_qty) }} </span> </li>
                                            <li> <b>{{ $t('Shipping Method') }}: </b> <span> {{ return_products.shipping_method }} </span> </li>
        
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    <div class="row return_request_div">
                        <form class="return_request_form">
                       
                        <h5> Create exchange or refund request</h5>
                        <div class="form-group">
                            <label>Return Type <span style="color:#f00">*</span></label>
                            <div class="input-group mb-2 mb-sm-0">
                                <select name="return_type" id="return_type" class="form-control">
                                    <option value="exchange">Exchange</option>

                                    <option v-if="allow_status.allow_refund == 1" value="refund">Refund</option>

                                </select>
                                <div class="validation_error" v-if="errors.return_type" v-html="errors.return_type[0]" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Why you want to exchange or refund this product? <span style="color:#f00">*</span></label>
                            <div class="input-group mb-2 mb-sm-0">
                                <textarea name="message" class="form-control" id="message" placeholder="Describe reason.."></textarea>
                                <div class="validation_error" v-if="errors.message" v-html="errors.message[0]" />
                            </div>
                        </div>


                        <div class="text-left">
                            <input type="submit" name="submit" :value="$t('SEND')" @click.prevent="returnRequest()" class="btn btn-primary btn-block rounded-0 py-2 return_request_form_submit">
                        </div>
                        </form>
                    </div>




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
    mode:'history',
	data(){
		return{
            dateTime:'',
            site_info:'',
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
            single_order:'',
            address:'',
            return_products:'',
            allow_status:'',
            order_id:'',
            product_id:'',
            errors:'',
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
        returnRequest(){
            let ids = this.$route.params.order_id;
            const myArray = ids.split(",");
            let order_id = myArray[0];
            let product_id = myArray[1];


            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    "X-localization": localStorage.getItem("lang"),
                    'Authorization': 'Bearer '+token
                }
            }
            let formData = new FormData();
            //Personal info
            formData.append('return_type', $('#return_type').find('option:selected').val());
            formData.append('message', $('#message').val());
            formData.append('order_id', order_id);
            formData.append('product_id', product_id);


            axios.post(this.$baseUrl+'/api/v1/return-request', formData, axiosConfig).then(response => {
                if(response.data.status == 1){
                    $('#title').val('');
                    $('#message').val('');
                    $('.validation_error').text('');
                    this.$router.push({name: 'orderDetails', params: {id: order_id } } );
                    swal({
                        title: 'Request sent successfully.',
                        icon: "success",
                        timer: 3000
                    })
                }if(response.data.status == 2){
                    $('#title').val('');
                    $('#message').val('');
                     $('.validation_error').text('');
                    swal({
                        title: 'You already requested to return.',
                        icon: "error",
                        timer: 3000
                    });
                }else{
                    this.errors = response.data.message;
                }
            });
        },



        date_time(){
            this.dateTime = new Date().toLocaleString();
            let ids = this.$route.params.order_id;
            const myArray = ids.split(",");
            this.order_id = myArray[0];
            this.product_id = myArray[1];
        },
        site_information(){
            axios.get(this.$baseUrl+'/api/v1/site-info').then(response => {
                this.site_info = response.data;
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
            let ids = this.$route.params.order_id;
            const myArray = ids.split(",");
            let order_id = myArray[0];
            let product_id = myArray[1];
            axios.get(this.$baseUrl + "/api/v1/get-return-product",{params: {order_id:order_id, product_id:product_id}}, axiosConfig).then((response) => {
                this.single_order = response.data.order;
                this.address = response.data.shipping_address;
                this.return_products = response.data.products;
                this.allow_status = response.data.products.product
            });
        },
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
	mounted(){
        this.scrollToTop();
        this.date_time();
         this.site_information();
		this.getUserDetails();
        this.baseurl = this.$baseUrl;
        this.load_single_order();
        document.title = "MabiY Shop | Product Return";  
	}
}
</script>