<template>
<div>


<section v-if="loading" id="voucher-page">
    <div class="container">
		<span v-if="vouchers">
		<div class="row" v-for="(category, index) in vouchers" :key="index" v-if="category.voucher.length > 0">
			<div class="offer_title">
				<h4> {{ category.title }}  </h4>
			</div>
			<div class="col-md-6 single_voucher" v-if="category.voucher" v-for="(data, index) in category.voucher" :key="index">
				<div class="voucher">
					<div class="collect_voucher">
						<b v-if="data.collected == 1" class="collected">  {{ $t('Collected') }} <i class="fa fa-check ml-1" aria-hidden="true"></i></b>
						<b v-else-if="data.collected == 2" class="used_voucher">  {{ $t('Used') }} <i class="fa fa-check ml-1" aria-hidden="true"></i></b>
						<b v-else class="collect_available" @click.prevent="collect_voucher(data.id)">{{ $t('Collect') }} </b>
					</div>
					<img @error="imageLoadError" :src="baseurl+'/'+data.banner" alt="img">
				</div>
			</div>
		</div>
		</span>

		<span v-else>
		<div class="row">
			<div class="col-md-12 voucher_not_found">
				<img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
				<h4> {{ $t('Voucher Not Found !') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
			</div>
		</div>
		</span>
    </div>
</section>

<section v-else id="voucher-page">
    <div class="container">
        <div class="row">
			<div class="col-md-4 col-lg-4"></div>
			<div class="col-md-4 col-lg-4">
				<div class="shimmer"> 
					<div class="h_4 w_100per mt_30"></div>
				</div>
			</div>
			<div class="col-md-4 col-lg-4"></div>
        </div>


		<div class="row">
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
		</div>

        <div class="row">
			<div class="col-md-4 col-lg-4"></div>
			<div class="col-md-4 col-lg-4">
				<div class="shimmer"> 
					<div class="h_4 w_100per mt_30"></div>
				</div>
			</div>
			<div class="col-md-4 col-lg-4"></div>
        </div>

		<div class="row">
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="shimmer"> 
					<div class="h_20 w_100per"></div>
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
export default {
	data(){
		return{
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
			errors: [],
            vouchers:'',
			loading:false,
		}
	},
	methods:{
		imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },
        load_vouchers(){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
            axios.get(this.$baseUrl+'/api/v1/get-voucher', axiosConfig).then(response => {
                this.vouchers = response.data.voucher_category;
				this.loading = true;
            });
        },
		collect_voucher(voucher_id){
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}

            axios.post(this.$baseUrl+'/api/v1/collect-voucher',{ voucher_id:voucher_id }, axiosConfig).then(response => {
				if(response.data.status == 1){
					this.load_vouchers();
					swal({
						title: "Voucher collected Successfully.",
						icon: "success",
						timer: 3000
					});
				}else{
					swal ( "Oops", response.data.message, "error");
				}
            });
		},
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
	computed:{
		logged_in_user(){
			return this.$store.getters.getLoadedUser.user;
		}
	},
	mounted(){
		this.scrollToTop();
        this.load_vouchers();
		this.baseurl = this.$baseUrl;
		document.title = "MabiY Shop | Voucher";  
	}
}
</script>