<template>
<div>

<section id="success_page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
                    <i class="checkmark">✓</i>
                </div>
                    <h1>{{ $t('Success') }}</h1> 
                    <p> {{ $t('We received your purchase request') }}</p>
                    <!-- <p v-if="paymentData">{{paymentData}}</p> -->
                    <router-link :to="{ name: 'myaccount' }" class="btn btn-dark btn-sm mb-3 mt-3"> {{ $t('Go to my account') }} </router-link> 
                    <router-link  :to="{name: 'products'}"  class="btn btn-primary btn-sm">  {{ $t('Continue shopping') }}  </router-link>
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
			is_user: '',
            baseurl:'',
            sellers:{},
		}
	},
	components:{
		Leftsidebar,
        pagination
	},
	methods:{
    	scrollToTop() {
            window.scrollTo(0,0);
        }
	},
	mounted(){
        this.baseurl = this.$baseUrl;
	}

}
</script>