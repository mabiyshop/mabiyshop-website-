<template>
<div> 



<section id="fixed_bar">
  <div class="container">
    <div class="row mobile_search">
      <div class="col-4 col-sm-4">
        <ul>
          <li><router-link :to="{name: 'wishlist'}" title="Go to wishlist"> <i class="fa fa-heart" aria-hidden="true"></i><span v-if="wishlistVuex" class="mobile-note-count">  {{ wishlistVuex.total }} </span> 
            <p class="m-0">{{$t('Wishlist')}}</p>
          </router-link></li>
          <li><router-link :to="{name: 'compare-list'}" title="Go to compare list"> <i class="fa fa-exchange" aria-hidden="true"></i><span  v-if="wishlistVuex" class="mobile-note-count">  {{ compreVuex.total }} </span> 
            <p class="m-0">{{$t('Compare')}}</p>
          </router-link></li>
        </ul>
      </div>
      <div class="col-4 col-sm-4 text-center bg_grey_l">
        <router-link v-if="site_info.header_logo" :to="{name: 'home'}"><img @error="imageLoadError" :src="baseurl+'/'+site_info.header_logo" alt=""></router-link>
      </div>
      <div class="col-4 col-sm-4 text-right">
        <ul>
          <li>
            <a :href="'tel:'+site_info.cnf_phone1" target="__blank"> <i class="fa fa-phone" aria-hidden="true"></i> <p class="m-0">{{$t('Call')}}</p> </a>
          </li>
        
          <li> 
            <span  v-if="logged_in_user">
              <i class="fa fa-user mobile-account-btn" aria-hidden="true"></i> <p class="m-0">{{$t('Account')}}</p>
            </span>
             <span v-else>
               <router-link :to="{name: 'sign-up'}"><i class="fa fa-user mobile-account-btn" aria-hidden="true"></i> <p class="m-0">{{$t('Account')}} </p> </router-link>
             </span>
          </li>
        </ul>
        <div class="mobile-my-account" v-if="logged_in_user">

          <ul >
            <span >
              <li> <a @click.prevent="logout()">{{ $t('Logout') }}</a> </li>
              <li> <router-link :to="{name: 'myaccount'}">{{ $t('My Account') }}</router-link> </li>
            </span>
            <!-- <span v-else>
              <li> <router-link :to="{name: 'login'}"> {{ $t('Login') }} </router-link> </li>
              <li> <router-link :to="{name: 'sign-up'}"> {{ $t('Register') }} </router-link> </li>
            </span> -->
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="subscribe-area">
    <div class="container">
      <div class="row">
            <div class="col-xl-4">
              <div class="subscribe-text mb-15">
                <h2>NEWSLETTER</h2>
                <p class="text-white">Subscribe to our newsletter to get regular updates and offers directly to your email.</p>
              </div>
            </div>
            <div class="col-xl-8">
              <div class="subscribe-wrapper subscribe2-wrapper mb-15 mt-4">
                <div class="subscribe-form">
                  <form id="newsletter_form" @submit.prevent="newsletter()">
                    <input style="color:#fff;" placeholder="Enter your email address" type="email" id="newsletter_email" required>
                    <button type="submit" class="subscribe_button">SUBSCRIBE <i class="fa fa-caret-right"></i></button>
                  </form>
                </div>
              </div>
            </div>
        </div>

    </div>
</section>

<section id="footer-section">
  <div class="container">
    <div class="row">
     <div class="col-md-4">
        <div class="site-logo footer-logo">
			    <router-link v-if="site_info.footer_logo" :to="{name: 'home'}"><img class="footer-logo-img" @error="imageLoadError" :src="baseurl+'/'+site_info.footer_logo" alt=""></router-link>
			    <router-link v-else :to="{name: 'home'}"><img class="footer-logo-img" @error="imageLoadError" src="/assets/images/logo.png" alt=""></router-link>
          <p>{{ site_info.cnf_appdesc }} </p>
		      <p v-if="site_info.accepted_payment_image"><img class="w-100" :src="baseurl+'/'+site_info.accepted_payment_image" alt="SSL Commerz logo"></p>
        </div>
      </div>
      <div class="col-md-2 quicklinks">
        <h4> {{ $t('Quick links') }} </h4>
        <ul>
          <li>  <router-link  :to="{ name: 'pages', params: {slug: 'about-us' } }"> {{ $t('About Us') }} </router-link> </li>
          <!-- <li>  <router-link :to="{ name: 'career'}"> {{ $t('Career') }} </router-link> </li> -->
          <!-- <li><router-link :to="{name: 'blog'}"> {{ $t('Blog') }} </router-link> </li> -->
          <li><router-link :to="{name: 'contact'}"> {{ $t('Help Center') }} </router-link> </li>
          <!-- <li><router-link :to="{name: 'offer'}"> {{ $t('Offers') }} </router-link></li> -->
          <li><router-link :to="{name: 'vendor'}">  {{ $t('Join As Seller') }} </router-link></li>

          <li><router-link :to="{name: 'corporate'}">  {{ $t('Join As Corporate User') }} </router-link></li>

          <li><a href="http://seller.mabiyshop.com" target="__blank">{{ $t('Login as seller') }}</a></li>
          <!-- <li><router-link :to="{name: 'voucher'}"> {{ $t('Voucher') }} </router-link></li> -->
        </ul>
      </div>
      <div class="col-md-4 footer-contact">
        <h4>{{ $t('Contact us') }}</h4>
        <ul>
          <li> <span>{{ site_info.cnf_address }}</span> </li>
          <li> <b>{{ $t('E-mail') }}: </b> <span>{{ site_info.cnf_email }}</span> </li>
          <li> <b> {{ $t('Mobile') }}: </b> <span v-if="site_info.cnf_phone1">{{ site_info.cnf_phone1 }}</span> <span v-if="site_info.cnf_phone2">,{{ site_info.cnf_phone2 }}</span> </li>

          <div class="social_media">
            <div class="social_media_icons_first">
              <ul>
                <li v-if="site_info.facebook"> <a :href="site_info.facebook" target="__blank" class="facebook_a"> <i class="fa fa-facebook-official" aria-hidden="true"></i>  </a> </li>
                <li v-if="site_info.twitter"> <a :href="site_info.twitter" target="__blank" class="twiter_a"> <i class="fa fa-twitter" aria-hidden="true"></i> </a> </li>
                <li v-if="site_info.instagram"> <a :href="site_info.instagram" target="__blank" class="instagram_a"> <i class="fa fa-instagram" aria-hidden="true"></i> </a> </li>
                <!-- <li v-if="site_info.facebook"> <a :href="site_info.facebook"> <i class="fa fa-linkedin" aria-hidden="true"></i> </a> </li> -->
                <li v-if="site_info.youtube"> <a :href="site_info.youtube" target="__blank"  class="youtube_a"> <i class="fa fa-youtube-play" aria-hidden="true"></i> </a> </li>
                <li v-if="site_info.pinterest"> <a :href="site_info.pinterest" target="__blank"><i class="fa fa-pinterest" aria-hidden="true"></i></a> </li>
                

                
              </ul>
            </div>
          </div>

        </ul>
      </div>

      <div class="col-md-2 quicklinks">
        <h4>{{ $t('Policies') }} </h4>
        <ul>
          <li v-if="site_info.privacy_policy">  <router-link  :to="{ name: 'pages', params: {slug: site_info.privacy_policy } }"> {{ $t('Privacy Policy') }} </router-link> </li>
          <li v-if="site_info.return_policy">  <router-link  :to="{ name: 'pages', params: {slug: site_info.return_policy } }"> {{ $t('Return Policy') }}  </router-link> </li>
          <li v-if="site_info.warranty_policy">  <router-link  :to="{ name: 'pages', params: {slug: site_info.warranty_policy } }"> {{ $t('Warranty Policy') }}  </router-link> </li>
          <li v-if="site_info.terms_of_use">  <router-link :to="{ name: 'pages', params: {slug: site_info.terms_of_use } }"> {{ $t('Terms and Conditions') }} </router-link> </li>
          


        </ul>
      </div>
    </div>
  </div>
</section>

<p class="text-center copy_text"><small>{{ site_info.copyright_text }} | Developed by <a class="text-white" href="https://varclone.com" target="_blank">VARCLONE</a> </small> </p>

</div>
</template>




<script>
	import Form from 'vform'
	import axios from 'axios'
  import {bus} from '../../app.js'
	
export default {
  data(){
    return{
      userLoged:null,
      categories:[],
      cartItem:[],
      cartLocalStorage:[],
      baseurl:'',
	    carts:'',
      sub_total:'',
      compareList:'',
      product_featured:[],
      site_info:'',
      static_pages:'',
    }
  },
    
  methods: {
   searchSubmit(){
     let content = $('.mobile-search-box').val();
     this.$router.push({
       name:'search',
       params: { content: content}
     });
   },


   newsletter(){
    let email = jQuery('#newsletter_email').val();

    axios.post(this.$baseUrl+'/api/v1/newsletter-subscribtion', {email:email}).then(response => {
      if(response.data.status == 1){
        swal({
          title: response.data.message,
          icon: "success",
          timer: 3000
        }).then(()=>{
          jQuery('#newsletter_email').val('');
        });
      }else{
        swal("Sorry" , response.data.message,  "error" );
      }
    });
  },

  logout(){
      // const response = await axios.get('/api/logout');
      this.$store.commit('SET_AUTHENTICATED', false);
      this.$store.commit('LOADED_USER', []);
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
      this.$router.push({name:'sign-up'});
    },
		load_featured_product(){
			axios.get(this.$baseUrl+'/api/v1/get-featured-product').then(response => {
				this.product_featured = response.data;
			});
		},
    imageLoadError(event){
          event.target.src = "/images/notfound.png";
    },
    site_information(){
      axios.get(this.$baseUrl+'/api/v1/site-info').then(response => {
        this.site_info = response.data;
      });
    },
    // load_static_pages(){
    //   axios.get(this.$baseUrl+'/api/v1/get-static-pages').then(response => {
    //     this.static_pages = response.data;
    //   });
    // }
  },

  computed:{
    cartData(){
      this.sub_total = this.$store.getters.getLoadedCart.sub_total;
      return this.$store.getters.getLoadedCart;
    },
    compreVuex(){
      return this.$store.getters.getLoadedCompare;
    },
    wishlistVuex(){
      return this.$store.getters.getLoadedWishlist;
    },
    logged_in_user(){
        return this.$store.getters.getLoadedUser.user;
    }
  },

  watch:{
			$route(to, from){
        this.site_information();
			}
		},

  mounted(){
    this.baseurl = this.$baseUrl;
    this.site_information();
    // this.load_static_pages();
  },



}
</script>
