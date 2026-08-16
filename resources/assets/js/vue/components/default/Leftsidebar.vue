<template>
    <div v-if="userData.user_type">
        <ul v-if="userData.user_type == 1" >
            <li> <router-link :to="{name: 'myaccount'}">  <i class="fa fa-id-card-o" aria-hidden="true"></i> <span> {{ $t('My Profile') }} </span> </router-link> </li>
            <li> <router-link :to="{name: 'myorder'}"> <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> <span>{{ $t('Orders') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'myaddress'}">  <i class="fa fa-truck" aria-hidden="true"></i> <span> {{ $t('Shipping information') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'myavouchers'}">  <i class="fa fa-gift" aria-hidden="true"></i> <span> {{ $t('Voucher') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'mycoupons'}">  <i class="fa fa-gift" aria-hidden="true"></i> <span> {{ $t('Coupons') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'myaffiliate'}">  <i class="fa fa-bullhorn" aria-hidden="true"></i> <span> {{ $t('Affiliate') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'changepassword'}"> <i class="fa fa-unlock-alt" aria-hidden="true"></i> <span> {{ $t('Change Password') }}</span> </router-link> </li>
            <li @click.prevent="logout()"> <a href="#"> <i class="fa fa-power-off" aria-hidden="true"></i> <span>{{ $t('Logout') }}</span> </a> </li>
        </ul>

        <ul v-else >
            <li> <router-link :to="{name: 'myaccount'}">  <i class="fa fa-id-card-o" aria-hidden="true"></i> <span> {{ $t('My Profile') }} </span> </router-link> </li>
            <li> <router-link :to="{name: 'productquatation'}"> <i class="fa fa-book" aria-hidden="true"></i> <span>{{ $t('Product Quotation') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'myaffiliate'}">  <i class="fa fa-bullhorn" aria-hidden="true"></i> <span> {{ $t('Affiliate') }}</span> </router-link> </li>
            <li> <router-link :to="{name: 'changepassword'}"> <i class="fa fa-unlock-alt" aria-hidden="true"></i> <span> {{ $t('Change Password') }}</span> </router-link> </li>
            <li @click.prevent="logout()"> <a href="#"> <i class="fa fa-power-off" aria-hidden="true"></i> <span>{{ $t('Logout') }}</span> </a> </li>
        </ul>
        
    </div>
</template>





<script>
import axios from 'axios';

export default {
  data(){
		return{
			userData:'',
		}
	},
  methods: {
	   logout(){
			// const response = await axios.get('/api/logout');
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
			})
		},
  },
  mounted(){
    this.getUserDetails();
  }
}
</script>