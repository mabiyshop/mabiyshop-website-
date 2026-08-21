<template>
   <div>

<div class="modal fade" id="location_modal" tabindex="-1" role="dialog" aria-labelledby="location_modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="location_modalTitle">
            <span onclick="getLocation()" class="getlocation"> <i class="fa fa-map-marker"></i>  {{ $t('My Location') }}</span> <span v-if="site_info.upazila_title" class="remove_location" >({{ site_info.upazila_title }} <span @click.prevent="removeLocation()" title="Remove your current location.">x</span>)</span> <b>{{ $t('or') }}</b> <span> {{ $t('Select Your Area') }}</span>
            
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="location_submit()">
      <div class="modal-body">
         <div class="options">
            <div class="row text-left">
            <div class="col-md-12">
            <div class="form-group">
               <label for="">  {{ $t('Division') }}<span style="color:#f00">*</span></label>
               <select  @change.prevent="getLocationDistrict()" name="division" class="form-control location_division" required>
                  <option disabled selected>--Select Division--</option>
                  <option class="Dhaka_select" value="68">Dhaka</option>
                  <option class="Chattogram_select" value="36">Chattogram</option>
                  <option class="Rajshahi_select" value="60">Rajshahi</option>
                  <option class="Khulna_select" value="65">Khulna</option>
                  <option class="Barishal_select" value="66">Barishal</option>
                  <option class="Sylhet_select" value="67">Sylhet</option>
                  <option class="Rangpur_select" value="69">Rangpur</option>
                  <option class="Mymensingh_select" value="6175">Mymensingh</option>
               </select>
               <div class="validation_error" v-if="errors.shipping_division" v-html="errors.shipping_division[0]" /></div>
            </div>
            <div class="col-md-12">
               <div class="form-group">
                  <label for=""> {{ $t('District') }} <span style="color:#f00">*</span></label>
                  <select  @change.prevent="getLocationUpazila()" name="district" class="form-control location_district" required>
                     <option disabled selected>--Select District--</option>
                     <option data-removeable="true" v-for="(district,index) in districts" :key="index" :class="district.title+'_select'" :value="district.id">{{district.title}}</option>
                  </select>
                  <div class="validation_error" v-if="errors.shipping_district" v-html="errors.shipping_district[0]" /></div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
               <div class="form-group">
                  <label for="">{{ $t('Upazila / Thana') }} <span style="color:#f00">*</span></label> 
                  <select  @change.prevent="getLocationUnion()" name="upazila" class="form-control location_upazail" required>
                     <option disabled selected>--Select Upazila--</option>
                     <option data-removeable="true" v-for="(upazila,index) in upazilas" :key="index" :class="upazila.title+'_select'" :value="upazila.id">{{upazila.title}}</option>
                  </select>
                  <div class="validation_error" v-if="errors.shipping_thana" v-html="errors.shipping_thana[0]" />
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <button type="submit" class="btn btn-primary" style="border-radius:5px;">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>


   <div class="desktop_nav">
      <section id="searchbar_section">
         <div class="container">
            <div class="row">
               <div class="col-xl-2 col-lg-2 col-md-2">
                  <div class="site-logo">
                     <router-link v-if="site_info.header_logo" :to="{name: 'home'}"><img @error="imageLoadError" :src="baseurl+'/'+site_info.header_logo" alt=""></router-link>
                     <router-link v-else :to="{name: 'home'}"><img @error="imageLoadError" src="/assets/images/logo.png" alt=""></router-link>
                  </div>
               </div>
               <div class="col-xl-2 col-lg-2 col-md-3">
                  <span v-if="site_info.header_offer_banner_status > 0">
                     <div v-if="site_info.header_offer_banner_linktype == 'internal_url'">
                           <router-link :to="site_info.header_offer_banner_link">
                              <img class="sale_banner_gif" @error="imageLoadError" :src="baseurl+'/'+site_info.header_offer_banner_image" alt="img">
                           </router-link>
                     </div>
                     <div v-else class="app-banner mt-1">
                           <a target="_blank" :href="site_info.header_offer_banner_link">
                              <img class="sale_banner_gif" @error="imageLoadError" :src="baseurl+'/'+site_info.header_offer_banner_image" alt="img">
                           </a>
                     </div>
                  </span>
               </div>
               <div class="col-xl-4 col-lg-4 col-md-3">
                  <form @submit.prevent="searchSubmit()">
                     <div class="row searchbox">
                        <div class="col-md-10">
                           <input type="text" name="" @keyup.prevent="search_suggest()" class="searchContent" id="myInput" :placeholder="$t('Search')+'..'">
                        </div>
                        <div class="col-md-2">
                           <button type="submit" class="searchboxbtn"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                        </div>
                     </div>
                  </form>


                  <div class="search_suggest_wrapper">
                     <div v-if="suggetionProductstatus" class="suggest_cross">x </div>
                     <div class="row">
                        <div class="product_serach_section" v-if="suggetionProductstatus" >
                           <div class="product_search_title">
                              Products
                           </div>
                           <div class="col-md-12" v-for="(data, index) in suggetionProduct.products" :key="index">
                              <router-link :to="{ name: 'product', params: {slug: data.slug } }">
                                 <div class="media search_suggest">
                                    <div class="media-left">
                                       <img @error="imageLoadError" v-if="data.default_image" :src="baseurl+'/'+data.default_image" alt="">
                                       <img @error="imageLoadError" v-else :src="baseurl+'/media/notfound.png'" alt="">
                                    </div>
                                    <div class="media-body">
                                       <b class="media-heading">{{ data.title }}</b> 
                                       <div class="now-price">BDT {{  data.price_after_offer }} <span class="old-price-inline"><del v-if="parseInt(data.price_after_offer.replace(/,/g, ''))  < parseInt(data.price.replace(/,/g, ''))">BDT {{ data.price }}</del></span></div>
                                    </div>
                                 </div>
                              </router-link>
                           </div>
                        </div>

                        <div class="shop_serach_section" v-if="suggetionShops">
                           <div class="product_search_title">
                              Shops
                           </div>
                           <router-link v-for="(seller, index) in suggetionShops" :key="index" :to="{ name: 'shop', params: {slug: seller.slug } }">  <div class="shops_item"> {{ seller.name }} </div></router-link>
                        </div>
                        <div class="shop_serach_section" v-if="suggetionCategories">
                           <div class="product_search_title">
                              Categories
                           </div>
                           <router-link v-for="(category, index) in suggetionCategories" :key="index" :to="{name: 'category', params: {slug: category.slug } }">  <div class="shops_item"> {{ category.title }} </div></router-link>
                        </div>
                     </div>
                  </div>



               </div>
               <div class="col-xl-4 col-lg-4 col-md-4 justify-content-end d-flex">
                 <div class="user-box top_right_icon">
                     <ul>
                       
                        <!-- <li class="comparenumberFull hover_element" >
                           <a data-toggle="modal" data-target="#location_modal" href="#" title="Select your area." style="color: #255ca8;">  <i class="fa fa-map-marker" aria-hidden="true"></i><span  v-if="site_info.upazila_title" id="location_upazila_title"> {{ site_info.upazila_title }} </span></a>
                        </li> -->
                        <li class="comparenumberFull hover_element">
                           <router-link :to="{name: 'compare-list'}" title="Go to compare list"> <i class="fa fa-retweet" aria-hidden="true"></i><span  v-if="wishlistVuex" class="comparenumber">  {{ compreVuex.total }} </span> </router-link>
                        </li>
                        <li class="comparenumberFull hover_element">
                           <router-link :to="{name: 'wishlist'}" title="Go to wishlist"> <i class="fa fa-heart" aria-hidden="true"></i><span v-if="wishlistVuex" class="comparenumber">  {{ wishlistVuex.total }} </span> </router-link>
                        </li>
                     </ul>
                  </div>

                  <div class="media">
                     <a :title="site_info.cnf_phone" :href="'tel:'+site_info.cnf_phone">
                        <img src="/images/call_now.png" class="mr-3" alt=""/>
                     </a>
                     <div class="media-body ">
                        <p class="text-secondary m-0 hotline">HOTLINE</p>
                        <a v-if="site_info.cnf_phone1" class="text-dark hedaer_phone" :href="'tel:'+site_info.cnf_phone1"> <b>{{ site_info.cnf_phone1 }}</b></a><br>
                        <a v-if="site_info.cnf_phone2" class="text-dark hedaer_phone" :href="'tel:'+site_info.cnf_phone2"> <b>{{ site_info.cnf_phone2 }}</b></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <section id="navbar_section">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-md-7 col-lg-7">
                        <div class="main-menu main-menu-first">
                           <ul>
                              <li class="desktop-bars-btn">
                                 <a href="javascript:void(0)"> {{ $t('Categories') }} </a>
                              </li>
                              <li v-for="(navbar, index) in navbars" :key="index" v-if="navbar.status == '1'">
                                 <span v-if="navbar.link_type == 'Internal'" >
                                    <router-link :to="{name: navbar.link}">  {{ navbar.title }} </router-link>
                                 </span>
                                 <span v-else>
                                    <a target="_blank" :href="navbar.link">{{ navbar.title }}</a>
                                 </span>
                              </li>
                           </ul>
                        </div>
                     </div>
                     <div class="col-md-5 col-lg-5">
                        <div class="user-box">
                           <ul v-if="logged_in_user" >
                              <li class="language_li">
                                 <div class="switch-button">
                                    <ul>
                                       <li> {{ $t('En') }}  </li>
                                       <li class="align-middle">  
                                          <label class="switch">
                                          <input type="checkbox" id="mycheckbox" class="lang_selector" value="1" v-model="lang" @change.prevent="changeLanguage()">
                                          <span class="slider round"></span>
                                          </label>
                                       </li>
                                       <li> {{ $t('Bn') }} </li>
                                    </ul>
                                 </div>
                              </li>
                              <li class="notificationfull hover_element">
                                 <i class="fa fa-bell" aria-hidden="true">
                                 <span v-if="notificationsData.notification_total > 0" class="user-notification">{{ notificationsData.notification_total }}</span></i>
                                 <div class="notification-dropdwon-parent">
                                    <h4>{{ $t('Notifications') }} </h4>
                                    <hr>
                                    <div class="notification-dropdwon-child" id="notification_ul">
                                       <ul v-if="notificationsData.notification_total > 0" >
                                          <li v-for="(notify, index) in notificationsData.notification" :key="index" @click.prevent="viewNotification(notify.id,notify.decoded_description)">
                                             <span>{{index+1}}</span> 
                                             <p :title="notify.decoded_description.message">{{  notify.decoded_description.message }}</p>
                                          </li>
                                       </ul>
                                       <ul v-else class="nothing_notify">
                                          <li> <small> {{ $t('No notification found') }}</small> </li>
                                       </ul>
                                    </div>
                                 </div>
                              </li>
                              <li class="userfull hover_element pr-3"> <i class="fa fa-user" aria-hidden="true"></i> <span>{{logged_in_user.name}}</span></li>
                           </ul>
                           <ul v-else>
                                 <li class="language_li">
                                    <div class="switch-button">
                                       <ul>
                                          <li> {{ $t('En') }}  </li>
                                          <li class="align-middle">  
                                             <label class="switch">
                                             <input type="checkbox" id="mycheckbox" class="lang_selector" value="1" v-model="lang" @change.prevent="changeLanguage()">
                                             <span class="slider round"></span>
                                             </label>
                                          </li>
                                          <li> {{ $t('Bn') }} </li>
                                       </ul>
                                    </div>
                                 </li>
                                 <li>
                                    <router-link class="nav_login" :to="{name: 'sign-up'}">  <i class="fa fa-sign-in" aria-hidden="true"></i> <span> {{ $t('Login')+'/'+$t('Register') }}</span> </router-link>
                                 </li>
                           </ul>
                        </div>
                        <div v-if="logged_in_user" class="user-account">
                         
                         
                         
                           <ul v-if="logged_in_user.user_type == 1">
                              <li>
                                 <router-link :to="{name: 'myaccount'}">  <i class="fa fa-id-card-o" aria-hidden="true"></i> <span>{{ $t('My Account') }}</span> </router-link>
                              </li>
                              <li>
                                 <router-link :to="{name: 'myorder'}"> <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> <span> {{ $t('My Orders') }}</span> </router-link>
                              </li>
                              <li>
                                 <router-link :to="{name: 'myaddress'}">  <i class="fa fa-truck" aria-hidden="true"></i> <span> {{ $t('My Shipping Information') }}</span> </router-link>
                              </li>
                              <li>
                                 <router-link :to="{name: 'myavouchers'}">  <i class="fa fa-gift" aria-hidden="true"></i> <span>  {{ $t('Voucher') }} </span> </router-link>
                              </li>
                              <li>
                                 <router-link :to="{name: 'changepassword'}"> <i class="fa fa-unlock-alt" aria-hidden="true"></i> <span>{{ $t('Change Password') }}</span> </router-link>
                              </li>
                              <li @click.prevent="Headerlogout()"> <a href="#"> <i class="fa fa-power-off" aria-hidden="true"></i> <span>{{ $t('Logout') }}</span> </a> </li>
                           </ul>

                           <ul v-else>
                              <li>
                                 <router-link :to="{name: 'myaccount'}">  <i class="fa fa-id-card-o" aria-hidden="true"></i> <span>{{ $t('My Account') }}</span> </router-link>
                              </li>
                              <li> 
                                 <router-link :to="{name: 'productquatation'}"> <i class="fa fa-book" aria-hidden="true"></i> <span>{{ $t('Product Quotation') }}</span> </router-link>
                              </li>
                              <li>
                                 <router-link :to="{name: 'changepassword'}"> <i class="fa fa-unlock-alt" aria-hidden="true"></i> <span>{{ $t('Change Password') }}</span> </router-link>
                              </li>
                              <li @click.prevent="Headerlogout()"> <a href="#"> <i class="fa fa-power-off" aria-hidden="true"></i> <span>{{ $t('Logout') }}</span> </a> </li>
                           </ul>


                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
   <a style="display:none;" class="btn btn-primary" data-toggle="modal" data-target="#loginModal" id="popupLoignModal"></a>
   <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
     
   
     <div v-if="site_info.social_login == 1" class="modal-dialog modal-dialog-centered login_popup"   role="document">
         <div class="modal-content">
            <div class="modal-header border-bottom-0">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="row">
               <div class="col-md-6 pr-0">
                  <div class="modal-body">
                     <div class="register-form popup_register_form">
                        <!-- <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                              <li data-radiovalue="501" style="width: 50%;" class="nav-item whatloginPopup" role="presentation">
                                 <button style="width: 100%;" class="btn btn-primary " id="pills-pass-tab" data-toggle="pill" data-target="#pills-password" type="button" role="tab" aria-controls="pills-password" aria-selected="false">{{$t('Password Login')}}</button>
                              </li>
                              <li data-radiovalue="500" style="width: 50%;" class="nav-item whatloginPopup" role="presentation">
                                 <button style="width: 100%;" class="btn btn-dark active" id="pills-otp-tab" data-toggle="pill" data-target="#pills-otp" type="button" role="tab" aria-controls="pills-otp" aria-selected="true">{{$t('OTP Login')}}</button>
                              </li>
                        </ul>
                        <hr> -->
                        <!-- <form @submit.prevent="popupPasswordLogin()"> -->
                        <form @submit.prevent="signup">
                              <div class="tab-content" id="pills-tabContent">
                                 <!-- <div class="tab-pane fade " id="pills-password" role="tabpanel" aria-labelledby="pills-pass-tab">
                                    <div class="password_logins">
                                          <div class="form-group">
                                             <label for=""> {{ $t('Mobile Number / Email') }}</label>
                                             <input type="text" name="phone" class="form-control phone" :placeholder="$t('Enter Mobile Number or Email')">
                                          </div>
                                          <div class="form-group">
                                             <label for="">{{ $t('Password') }}</label>
                                             <input type="password" name="password" class="form-control password"  :placeholder="$t('Password')+'..'">
                                          </div>
                                    </div>
                                 </div>
                                 <div class="tab-pane fade show active" id="pills-otp" role="tabpanel" aria-labelledby="pills-otp-tab">
                                    <div class="otp_logins">
                                          <div class="otp_before">
                                             <div class="form-group">
                                                <label for=""> {{ $t('Mobile Number')}} </label>
                                                <input name="mobile_number" type="text" class="form-control mobile_number_login_page mobile_number" :placeholder="$t('Enter Mobile Number')"> 
                                                <button type="button" @click.prevent="PopupGenerateOtp()" class="fa fa-lock btn btn-primary generate_otp_1 login_otp login_otp_popup" aria-hidden="true">{{ $t('Generate OTP') }}</button>
                                             </div>
                                             <div class="form-group popupOtp_login_page_group">
                                                <label for="">{{ $t('OTP') }}</label>
                                                <input type="text" name="otp"  class="form-control popupOtp_login"  :placeholder="$t('OTP')+'..'">
                                             </div>
                                          </div>
                                    </div>
                                 </div> -->
                                 <div class="form-group">
                                    <label for=""> {{ $t('Mobile Number')}} </label>
                                    <input id="login_page_generate_otp" v-model="signupForm.mobile_number" name="mobile_number" type="text" class="form-control mobile_number_login_page" :placeholder="$t('Enter Email / Mobile Number')"> 
                                 </div>
                                 <div class="form-group">
                                    <button type="button" @click.prevent="generateOtp_login_page()" class="generate_otp_btn singin-with-google">{{ $t('Next') }}</button>
                                 </div>
                                 <div class="form-group popupOtp_login_page_group d-none">
                                    <label for="">{{ $t('OTP') }}</label>
                                    <input id="popupOtp_login_page"  v-model="signupForm.otp" type="text" name="otp"  class="form-control"  :placeholder="$t('OTP')+'..'">
                                    
                                    <input type="hidden" name="affiliate_referer" :value="affiliate_referer">
                                    <input type="submit" class="mt-3 singin-with-google" :value="$t('Sign up now')">
                                 </div>
                              </div>
                              <div class="form-group">
                                 <input type="submit" class="singin-with-google " value="Sign in">
                                 <!-- <div class="singin-with-google otp_login otp_login_popup" @click.prevent="PopupOTPSignIn()">Sign in</div> -->
                              </div>
                        </form>
                     </div>

                  </div>
               </div>
             
               <div class="col-md-1 p-0 orLogin">
                  <h5> <b>Or</b> <br> <small> {{ $t('Login With') }}</small> </h5>
               </div>

               <div  class="col-md-5 p-0">
                  <div class="social_login">
                     <ul>
                        <li class="login_with_facebook"><a href="javascript:void(0)" @click="logInWithFacebook" ><i aria-hidden="true" class="fa fa-facebook"></i>  {{ $t('Login with Facebook') }}  </a></li>
                        <li id="googleButtonDiv"  class="login_with_google login_with_google_in_popup">
                           <a href="javascript:void(0)" @click="logInWithGoogleRedirect" ><i aria-hidden="true" class="fa fa-google"></i>{{ $t('Login with Google') }} </a>
                        </li>
                     </ul>
                  </div>
               </div>
           
            </div>
         </div>
      </div>


      <div v-else class="modal-dialog modal-dialog-centered"  role="document">
         <div class="modal-content">
            <div class="modal-header border-bottom-0">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="row popup_register_form_row">
               <div class="col-md-12 col-12">
                  <div class="form-title text-center">
                     <h5><b>{{ $t('Please login to continue') }}</b></h5>
                  </div>
               </div>
                <div class="register-form popup_register_form">
                    <!-- <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li data-radiovalue="501" style="width: 50%;" class="nav-item whatloginPopup" role="presentation">
                            <button style="width: 100%;" class="btn btn-primary " id="pills-pass-tab" data-toggle="pill" data-target="#pills-password" type="button" role="tab" aria-controls="pills-password" aria-selected="false">{{$t('Password Login')}}</button>
                        </li>
                        <li data-radiovalue="500" style="width: 50%;" class="nav-item whatloginPopup" role="presentation">
                            <button style="width: 100%;" class="btn btn-dark active" id="pills-otp-tab" data-toggle="pill" data-target="#pills-otp" type="button" role="tab" aria-controls="pills-otp" aria-selected="true">{{$t('OTP Login')}}</button>
                        </li>
                    </ul>
                    <hr> -->
                    <!-- <form  @submit.prevent="popupPasswordLogin()"> -->
                     <form @submit.prevent="signup()">
                        <div class="tab-content" id="pills-tabContent">
                            <!-- <div class="tab-pane fade " id="pills-password" role="tabpanel" aria-labelledby="pills-pass-tab">
                                <div class="password_logins">
                                    <div class="form-group">
                                        <label for=""> {{ $t('Mobile Number / Email') }}</label>
                                        <input type="text" name="phone" class="form-control phone" :placeholder="$t('Enter Mobile Number or Email')">
                                    </div>
                                    <div class="form-group">
                                        <label for="">{{ $t('Password') }}</label>
                                        <input type="password" name="password" class="form-control password"  :placeholder="$t('Password')+'..'">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade show active" id="pills-otp" role="tabpanel" aria-labelledby="pills-otp-tab">
                                <div class="otp_logins">
                                    <div class="otp_before">
                                        <div class="form-group">
                                            <label for=""> {{ $t('Mobile Number')}} </label>
                                            <input name="mobile_number" type="text" class="form-control mobile_number_login_page mobile_number" :placeholder="$t('Enter Mobile Number')"> 
                                            <button type="button" @click.prevent="PopupGenerateOtp()" class="fa fa-lock btn btn-primary generate_otp_1 login_otp login_otp_popup" aria-hidden="true">{{ $t('Generate OTP') }}</button>
                                        </div>
                                        <div class="form-group popupOtp_login_page_group">
                                            <label for="">{{ $t('OTP') }}</label>
                                            <input type="text" name="otp"  class="form-control popupOtp_login"  :placeholder="$t('OTP')+'..'">
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                              <div class="form-group">
                                 <label for=""> {{ $t('Mobile Number')}} </label>
                                 <input id="login_page_generate_otp" name="mobile_number" type="text" class="form-control mobile_number_login_page" :placeholder="$t('Enter Email / Mobile Number')"> 
                              </div>
                              <div class="form-group">
                                 <button type="button" @click.prevent="generateOtp_login_page()" class="generate_otp_btn singin-with-google">{{ $t('Next') }}</button>
                              </div>
                              <div class="form-group popupOtp_login_page_group d-none">
                                 <label for="">{{ $t('OTP') }}</label>
                                 <input id="popupOtp_login_page" type="text" name="otp"  class="form-control"  :placeholder="$t('OTP')+'..'">
                                 
                                 <input type="hidden" name="affiliate_referer" :value="affiliate_referer">
                                 <input type="submit" class="mt-3 singin-with-google" :value="$t('Sign in now')">
                              </div>
                        </div>
                        <div class="form-group">
                            <!-- <input type="submit" class="singin-with-google password_login password_login_popup" value="Sign in"> -->
                            <!-- <div class="singin-with-google otp_login otp_login_popup" @click.prevent="PopupOTPSignIn()">Sign in</div> -->
                        </div>
                    </form>
                </div>

            </div>
         </div>
      </div>
   </div>

   <!--Mobile menu start-->
   <div class="mobile_nav">
      <div class="container">
         <div class="row">
   
            <div class="mobile-categories">
               <div class="categories-close">
                  <i class="fa fa-times" aria-hidden="true"></i>
               </div>
               <div class="categories_text"> <b> {{ $t('Categories') }}</b></div>
               <div id="mobile_menu">
                  <div class="row hidden_in_mobile">
                     <div class="col-6"><p class="text-left cat_tt">Categories</p></div>
                     <div class="col-6"><p class="text-right cat_cc"> <i class="fa fa-close"></i> </p></div>
                  </div>
                  


                  <ul class="mdn-accordion blue-accordion-theme"  id="layer_0">
                     <li v-for="(category1, index) in categories" :key="index" class="sub-level" v-if="category1.categories.length > 0" >
                        
                        <input class="accordion-toggle" type="checkbox" :name ="'group-'+index" :id="'group-'+index">
                        <router-link  class="close_mobile_nav accordion-title link_title" :to="{name: 'category', params: {slug: category1.slug } }">{{ category1.title }}</router-link>
                        <label class="accordion-title link_level" :for="'group-'+index"> </label>
                      


                        <ul id="layer_1">
                           <li v-for="category2 in category1.categories" :key="category2.id" v-if="category2.categories.length > 0" class="sub-level">
                              <input class="accordion-toggle" type="checkbox" :name ="'sub-group-'+category2.id" :id="'sub-group-'+category2.id">
                              <!-- <label class="accordion-title" :for="'sub-group-'+category2.id">{{ category2.title }}</label> -->

                              <router-link  class="close_mobile_nav accordion-title link_title" :to="{name: 'category', params: {slug: category2.slug } }">{{ category2.title }}</router-link>
                              <label class="accordion-title link_level" :for="'sub-group-'+category2.id"> </label>


                              <ul id="layer_2">
                                 <li v-for="category3 in category2.categories" :key="category3.id" v-if="category3.categories.length > 0" class="sub-level">
                                    <input class="accordion-toggle" type="checkbox" :name ="'sub-group-level-'+category3.id" :id="'sub-group-level-'+category3.id">
                                    <!-- <label class="accordion-title" :for="'sub-group-level-'+category3.id">{{ category3.title }}</label> -->
                                    <router-link  class="close_mobile_nav accordion-title link_title" :to="{name: 'category', params: {slug: category3.slug } }">{{ category3.title }}</router-link>
                                    <label class="accordion-title link_level" :for="'sub-group-level-'+category3.id"> </label>


                                    <ul id="layer_3">
                                       <li v-for="category4 in category3.categories" :key="category4.id" >
                                          <router-link :to="{name: 'category', params: {slug: category4.slug } }">{{ category4.title }}</router-link>
                                       </li>
                                    </ul>
                                 </li>
                                 <li v-else>
                                    <router-link class="close_mobile_nav width100" :to="{name: 'category', params: {slug: category3.slug } }" >{{ category3.title }} </router-link>
                                 </li>
                              </ul>
                           </li>
                           <li v-else>
                              <router-link class="close_mobile_nav width100" :to="{name: 'category', params: {slug: category2.slug } }" >{{ category2.title }} </router-link>
                           </li>
                        </ul>
                     </li>
                     <li v-else>
                        <router-link class="close_mobile_nav width100" :to="{name: 'category', params: {slug: category1.slug } }">{{ category1.title }}</router-link>
                     </li>
                  </ul>
               </div>
            </div>


            <div class="mobile-main-menu">
               <div class="main-menu-close">
                  <i class="fa fa-times" aria-hidden="true"></i>
               </div>
               <ul>
                  <li class="main-menu-text"> <b> {{ $t('Main menu') }}</b></li>
                  <li class="padding_top">
                     <router-link :to="{name: 'sellers'}"> {{ $t('All Shops') }} </router-link>
                  </li>
                  <li>
                     <router-link :to="{name: 'products'}"> {{ $t('Products') }} </router-link>
                  </li>
        
                  <!-- 
                  <li v-if="offer_title.regular_offer">
                     <router-link :to="{name: 'offer'}"> {{ offer_title.regular_offer }} </router-link>
                  </li>
                  <li v-if="offer_title.promotional_offer">
                     <router-link :to="{name: 'offerpromotional'}" class="cyclone_offer"> {{ offer_title.promotional_offer }} </router-link>
                  </li>
                  <li>
                     <router-link :to="{name: 'voucher'}">  {{ $t('Voucher') }} </router-link>
                  </li>
                   -->

                  <li>
                     <router-link :to="{name: 'flashdeals'}"> {{ $t('Flash Deals') }} </router-link>
                  </li>

                  <li>
                     <router-link :to="{name: 'groceries'}"> {{ $t('Groceries') }} </router-link>
                  </li>


                  <li style="background: #00563a;padding: 16px 0px 1px 20px;color:#fff;">
                        <p> 
                           {{ $t('En') }}

                           <label class="switch">
                              <input type="checkbox" class="lang_selector" v-model="lang" @change.prevent="changeLanguage()">
                              <span class="slider round"></span>
                           </label>

                              {{ $t('Bn') }}
                        </p>

                  </li>
               </ul>
            </div>

            <div class="col-md-12 search_in">
               <ul>
                  <li class="width10">
                     <div class="categories-bars-btn">
                        <i class="fa fa-bars mt-2" aria-hidden="true"></i>
                     </div>
                  </li>
                  
                  <li class="width70">
                     <form @submit.prevent="mobile_searh_searchSubmit()">
                        <input type="text" class="mobile_search_input mobileSearchContent" @keyup.prevent="mobile_search_suggest()" :placeholder="$t('Search...')">
                        <button class="mobile_search_button" @click="mobile_searh_searchSubmit()">
                              <i class="fa fa-search" aria-hidden="true" ></i>
                        </button>
                        </form>


                        <div class="mobile_search_suggest_wrapper">
                           <div v-if="suggetionProductstatus" class="mobile_suggest_cross"> Close</div>
                           <div class="row">
                              <div class="product_serach_section" v-if="suggetionProductstatus" >
                                 <div class="product_search_title">
                                    Products
                                 </div>
                                 <div class="col-md-12" v-for="(data, index) in suggetionProduct.products" :key="index">
                                    <router-link :to="{ name: 'product', params: {slug: data.slug } }">
                                       <div class="media search_suggest mobile_search_suggest_media">
                                          <div class="media-left">
                                             <img @error="imageLoadError" v-if="data.default_image" :src="baseurl+'/'+data.default_image" alt="">
                                             <img @error="imageLoadError" v-else :src="baseurl+'/media/notfound.png'" alt="">
                                          </div>
                                          <div class="media-body">
                                             <b class="media-heading">{{ data.title }}</b> 
                                             <div class="now-price">BDT {{  data.price_after_offer }} <span class="old-price-inline"><del v-if="parseInt(data.price_after_offer.replace(/,/g, ''))  < parseInt(data.price.replace(/,/g, ''))">BDT {{ data.price }}</del></span></div>
                                          </div>
                                       </div>
                                    </router-link>
                                 </div>
                              </div>

                              <div class="shop_serach_section" v-if="suggetionShops">
                                 <div class="product_search_title">
                                    Shops
                                 </div>
                                 <router-link v-for="(seller, index) in suggetionShops" :key="index" :to="{ name: 'shop', params: {slug: seller.slug } }">  <div class="shops_item"> {{ seller.name }} </div></router-link>
                              </div>
                              <div class="shop_serach_section" v-if="suggetionCategories">
                                 <div class="product_search_title">
                                    Categories
                                 </div>
                                 <router-link v-for="(category, index) in suggetionCategories" :key="index" :to="{name: 'category', params: {slug: category.slug } }">  <div class="shops_item"> {{ category.title }} </div></router-link>
                              </div>
                           </div>
                        </div>




                  </li>
                  
                  <li class="width10">
                     <div class="text-right mobile-main-menu-btn pr-0">
                        <i class="fa fa-th-large mt-2" aria-hidden="true"></i>
                     </div>
                  </li>
               </ul>
            </div> 
         </div>
      </div>
   </div>
   <!--Mobile menu end-->
   <div class="left_cart_icon ui-widget-content">

      <span v-if="cartData">
         <span class="count_item" v-if="cartData.total_items > 0">  {{ cartData.total_items }}</span>
         <span class="count_item" v-else>0</span>
      </span>

      <p> <img width="25" src="/images/cart_ico.png" alt="Cart icon"> </p>
      <p class="mt-1" v-if="cartData.sub_total">BDT {{ cartData.sub_total }}</p>
   </div>


   <div class="left_cart">
   <div class="cartfull">
   <!-- <router-link :to="{name: 'cart'}"><i class="fa fa-shopping-cart" aria-hidden="true"> </i></router-link> -->
   <span class="cart_close"> <i class="fa fa-times"></i></span>
   <p class="cart_title">{{ $t('Shopping Cart') }}</p>
   <div class="table-child">
        <section v-if="cartData.sub_total > 0" id="cart-page">
          <div class="container-fluid">
            <div class="row cart-page-container">
              <div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
                <div class="cart-calculation">
                  <table  class="table text-left cart_table" width="100%">
                    <thead>
                      <tr>
                        <th width="95%"> {{ $t('Product Details') }}</th>
                        <!-- <th width="30%">{{ $t('Product Name') }}</th> -->
                        <!-- <th width="20%"> {{ $t('Price') }}</th>
                        <th width="15%"> {{ $t('Quantity') }}</th> -->
                        <!-- <th width="20%"> {{ $t('Total') }}</th> -->
                        <th width="5%" style="text-align: right;"> {{ $t('Remove') }}</th>
                      </tr>
                    </thead>
                  
                  
                    <tbody v-for="(cartgroup,index) in cartData.cart" :key="index">
                      <tr class="group_header mb-3">
                        <td colspan="7">
                          <small>{{ $t('Shipped by') }} :  <b> <router-link class="text-dark" :to="{ name: 'shop', params: {slug: cartgroup.shop_info.shop_slug } }" >{{cartgroup.shop_info.shop_name}}</router-link></b></small>
                        </td>
                      </tr> 
                      <tr v-for="(cart, index) in cartgroup.items" :key="index" class="cart_product_group">
                        <td> 


                           <div class="media">
                              <div class="media-left product-cart-img"> 
                                 <img @error="imageLoadError" :src="baseurl+'/'+cart.product.default_image" alt=""> 
                              </div>
                              <div class="media-body product-cart-details">
                                 <router-link class="media-heading" :to="{ name: 'product', params: {slug: cart.product.slug } }"> {{ cart.product.title }} </router-link>
                                 
                                 <span  v-if="cart.product_type == 'variable'">
                                    <br><small v-if="cart.variable_sku" class="mb-0 text-capitalize font-13"> <b>SKU:</b> {{cart.variable_sku}}</small>
                                 </span>
                                 <span v-else>
                                    <br><small v-if="cart.product.sku" class="mb-0 text-capitalize font-13"> <b>SKU:</b> {{cart.product.sku}}</small>
                                 </span>


                                 <div class="left_side_cart_qty">
                                    <div class="table-item">
                                       <div class="full-quantity">
                                          <button @click.prevent="updateQty(cart.row_id, -1)" class="cart_minus_btn"> <div class="crt cart-minus"> - </div> </button> 
                                          <div class="crt cart-qty"> <input :id="'Product'+index" type="text" :data-catQty="cart.qty" class="cart-qty-input" readonly :value="cart.qty" :data-rowid="cart.row_id" :data-productid="cart.product_id" :data-userid="cart.user_id"> </div>
                                          <button @click.prevent="updateQty(cart.row_id, 1)" class="cart_minus_btn"><div class="crt cart-plus" > + </div></button> 
                                       </div>
                                    </div>
                                 </div>
                                 <div class="table-item">BDT {{ cart.price }}   &nbsp;<span class="old-price"> <del v-if="cart.price_before_offer > cart.price">BDT {{ cart.price_before_offer }}</del> </span></div>
                                 
                                 <div class="table-item left_cart_varient_section">
                                    <span  v-if="cart.product_type == 'simple'">
                                       <p class="mb-0 text-capitalize font-13" > <b>Weight</b> : {{  cart.total_weight }} {{ cart.weight_unit }}</p>
                                    </span>
                                    <span  v-if="cart.product_type == 'variable'">
                                       <p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in cart.variable_options" :key="key"> <b>{{key}}</b> : {{vOption}}</p>
                                    </span>
                                    <span  v-if="cart.product_type == 'digital'">
                                       <p v-if="cart.variable_options" class="mb-0 text-capitalize font-13"> <b>Contact Number</b> : {{cart.variable_options}}</p>
                                    </span>

                                    <span  v-if="cart.product_type == 'service'">
                                       <p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in cart.variable_options" :key="key"> <b>{{ key.replace('_',' ') }}</b> : {{vOption}}</p>
                                    </span>

                                 </div>
                              </div>
                           </div>



                        </td>

   
                        
                        <td style="text-align: right;"> 
                          <div class="table-item product-remove addagain" :id="'cart_'+cart.row_id" @click.prevent="removeItem(cart.row_id)">
                              <i class="fa fa-trash"></i>
                          </div> 
                        </td>

                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
                <div class="note">
                  <h5 class="text-uppercase cart_summary_title">{{ $t('Order Summary') }}</h5>
                </div>
                <div class="payment-calculation">
                  <ul>
                    <li> <b>{{ $t('Sub Total') }}  </b>  <span v-if="sub_total"> BDT {{ sub_total }}</span> <span v-else> BDT {{ cartData.sub_total }} </span></li>
                    <li> <b>{{ $t('Total Item') }}(s) </b>  <span>{{ cartData.total_items }}</span></li>
                    <li> <b> {{ $t('Total Price') }}</b>  <span>BDT {{ sub_total }}  </span></li>
                    
                  </ul>
                </div>

                <span v-if="logged_in_user">
                     <span v-if="logged_in_user.user_type == 2">
                        <p class="text-right"><button  @click="requestForQuatation()" class="btn btn-primary site_color1">{{ $t('Request For Quatation') }}</button> </p>
                     </span>
                     <span v-else>
                        <p class="text-right"><button  @click="showCheckoutSection()" class="btn btn-primary site_color1 show_checkout_section">{{ $t('Proceed To Checkout') }}</button> </p>
                     </span>
                </span>
                 <span v-else>
                  <p class="text-right"><button  @click="showCheckoutSection()" class="btn btn-primary site_color1 show_checkout_section">{{ $t('Proceed To Checkout') }}</button> </p>
                 </span>
               
              
               </div>
            </div>
          </div>
        </section>

        <section v-else id="cart-page-noitem">
          <div class="container-fluid">
            <div class="cart-page-container">
                <p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
                  <h4 class="text-uppercase text-secondary"> {{ $t('No product in cart') }} ! </h4>
            </div>
         </div>
      </section>
   
   <div id="checkout_section" v-if="loading" p-0>
      <section v-if="cartData.sub_total > 0" id="cart-page">
         <div class="container-fluid">
            <div class="row mt-1">
               <div class="back_to_cart"> <i class="fa fa-arrow-left" aria-hidden="true"></i> {{ $t('Back To Cart') }} </div> 
                <span class="reload_calculation d-none">Reload Price</span>
            </div>
            <div class="row cart-page-container">
               <div class="col-12 col-sm-12 col-md-7 col-lg-7 pr-0 pl-0">

                  <div class="cart-calculation">
                     <table  class="table text-left cart_table" width="100%">
                        <thead>
                           <tr>
                              <th width="100%">{{ $t('Product Details') }}</th>
                              <!-- <th width="25%"> {{ $t('Price') }}</th>
                              <th class="text-center" width="10%"> {{ $t('Quantity') }}</th> -->
                           </tr>
                        </thead>
                        <tbody v-for="(cartgroup, index) in cartData.cart" :key="index">
                           <tr class="group_header mb-3">
                              <td colspan="7">
                                 <small>
                                    {{ $t('Shipped by') }} : 
                                    <b>
                                       <router-link class="text-dark" :to="{ name: 'shop', params: {slug: cartgroup.shop_info.shop_slug } }" >{{cartgroup.shop_info.shop_name}}</router-link>
                                    </b>
                                 </small>
                              </td>
                           </tr>

                           <!-- Checkout Items Start -->
                           <tr v-for="(cart, index) in cartgroup.items" :key="index" class="cart_product_group">
                              <td>
                                 <div class="table-item">
                                    <div class="media">
                                       <img @error="imageLoadError"  class="mr-3 product-cart-img" :src="baseurl+'/'+cart.product.default_image" alt="">
                                       <div class="media-body">
                                          <h5 class="mt-0">
                                             <router-link :to="{ name: 'product', params: {slug: cart.product.slug } }"> {{ cart.product.title }} </router-link>
                                             
                                             <span v-if="cart.product.is_grocery == 'grocery'" >
                                                <span class="grocery_shipping_cost badge" :data-shipping-cost="cartData.grocery_shipping_cost">Grocery</span>
                                             </span>

                                          </h5>
                                          <div v-if="site_info.default_shipping_enable != '1'">
                                             <div v-if="cart.product.is_grocery != 'grocery' && cart.product_type != 'digital' && cart.product_type != 'service' && logged_in_user_address != 0 && logged_in_user.default_address_id != null" class="select_shipping_options">
                                                <span v-if="logged_in_user">

                                                   <ul v-if="cart.shipping_options != 0 && cartData.pickpoint == 0" class="list-group list-group-horizontal">
                                                      
                                                      <li :data-product-id="cart.product_id"  data-shipping-method="free_shipping" :data-shipping-cost="0" :data-qty="cart.qty" v-if="cart.shipping_options.free_shipping == 'on'" class="list-group-item" :title="$t('Est. Arrival: Within 7 to 15 days')"> BDT 0 <br> {{ $t('Free Shipping') }} <br> <small>Est. Arrival: Within 7 to 15 days</small> </li>


                                                      <li :data-product-id="cart.product_id" data-shipping-method="standard_shipping" :data-shipping-cost="cart.shipping_options.standard_shipping" :data-qty="cart.qty" class="list-group-item selected_shipping" :title="$t('Est. Arrival: Within 4 to 7 days')">BDT {{ cart.shipping_options.standard_shipping }}  <br>{{ $t('Standard Shipping') }} <br> <small>Est. Arrival: Within 4 to 7 days</small></li>


                                                      <li :data-product-id="cart.product_id" data-shipping-method="express_shipping" :data-shipping-cost="cart.shipping_options.express_shipping" :data-qty="cart.qty" class="list-group-item" :title="$t('Est. Arrival: Within 1 to 3 days')">BDT {{ cart.shipping_options.express_shipping }} <br> {{ $t('Express Shipping') }} <br> <small>Est. Arrival: Within 1 to 3 days</small></li>
                                                   </ul>

                                                   <span v-if="cartData.pickpoint == 1" id="pickPointCost" :data-pickpoint-cost='cartData.shipping_cost' ></span>

                                                </span>
                                             </div>
                                          </div>

                                          <span  v-if="cart.product_type == 'variable'">
                                             <p class="badge badge-primary mr-2 mb-0" v-for="(vOption,key) in cart.variable_options" :key="key"> <b>{{key}}</b> : {{vOption}}</p>
                                          </span>
                                          <span  v-if="cart.product_type == 'digital'">
                                             <p v-if="cart.variable_options" class="badge badge-primary mr-2 mb-0"> <b>{{ $t('Contact Number') }}</b> : {{cart.variable_options}}</p>
                                          </span>
                                          <div class="mb-0 text-capitalize font-13"> <b>Qty:</b> {{ cart.qty }}</div>
                                          <div class="mb-0 text-capitalize font-13"><b>Price:</b> BDT {{ cart.price }}</div>
                                          <span  v-if="cart.product_type == 'service'">
                                             <p class="mb-0 text-capitalize font-13" v-for="(vOption,key) in cart.variable_options" :key="key"> <b>{{ key.replace('_',' ') }}</b> : {{vOption}}</p>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                              </td>

                           </tr>  <!-- Checkout Items Ends -->


                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="col-12 col-sm-12 col-md-5 col-lg-5 payment legacy-mobile-checkout">
                  <h5 class="text-uppercase cart_summary_title">{{ $t('Shipping information') }}</h5>


                  

                  <span v-if="selected_pickpoint">

                        <span v-if="logged_in_user">
                              <div v-if="selected_pickpoint != null" class="address_details">
                                 
                                 <ul>
                                    <li>
                                       <div class="row p-0">
                                          <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>

                                          <div class="col-lg-10 p-0 pl-1"> {{  selected_pickpoint.title }} <span v-if="selected_pickpoint.division">{{ selected_pickpoint.division.title }}</span>,<span v-if="selected_pickpoint.district"> {{ selected_pickpoint.district.title }}</span>, <span v-if="selected_pickpoint.upazila"> {{ selected_pickpoint.upazila.title }}</span>, <span v-if="selected_pickpoint.union">{{ selected_pickpoint.union.title }}</span> {{ selected_pickpoint.address }}  <span class="badge badge-danger">{{$t('Pickup Point')}}</span></div>
                                          <div class="col-lg-1 pl-0 pt-1 pr-0"><i class="fa fa-pencil address_btn" data-toggle="modal" data-target="#addressModal" aria-hidden="true"></i></div>
                                       </div>
                                    </li>
                                    <li> <b><i class="fa fa-phone" aria-hidden="true"></i></b> <span>{{selected_pickpoint.phone}}</span></li>
                                    <li v-if="selected_pickpoint.email"> <b><i class="fa fa-envelope-o" aria-hidden="true"></i></b> <span>{{selected_pickpoint.email}}</span></li>
                                 </ul> 

                              </div>
                              <div v-else class="address_details_alt">
                                 <div  class="row p-0">
                                       <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                                       <div class="col-lg-10 p-0">
                                          <p class="required_addtess" data-required-address="true">&nbsp;{{ $t('You need to add your shipping address') }}.</p>
                                       </div>
                                       <div class="col-lg-1  pl-0 pt-1 pr-0"><i class="fa fa-pencil address_btn" data-toggle="modal" data-target="#addressModal" aria-hidden="true"></i></div>
                           
                                    </div> 
                              </div>  
                        </span>
                           <div v-else  class="address_details_alt">
                              <div v-if="logged_in_user" class="row p-0">
                                 <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                                 <div v-if="logged_in_user.default_address_id == null" class="col-lg-10 p-0">
                                    <p class="required_addtess" data-required-address="true">{{ $t('You need to select your default shipping address') }}.</p>
                                 </div>
                                 <div v-else class="col-lg-10 p-0">
                                    <p class="required_addtess" data-required-address="true">{{ $t('You need to add your shipping address') }}.</p>
                                 </div>
                                 <div class="col-lg-1 pl-0"><i class="fa fa-pencil address_btn" data-toggle="modal" data-target="#addressModal" aria-hidden="true"></i></div>
                              </div>
                               <div v-else>
                                  <p class="required_addtess" data-required-login="true" v-if="logged_in_user">{{ $t('You have to') }} <a href=""> login </a> {{ $t('first to add your shipping address') }}.</p>
                               </div>
                           </div>

                  </span>
                  <span v-else>
                  
                              <span v-if="logged_in_user">
                              <div v-if="logged_in_user_address != 0 && logged_in_user.default_address_id != null" class="address_details">
                                 
                                 <ul  v-for="(address,index) in logged_in_user_address" :key="index" v-if="logged_in_user.default_address_id == address.id" >
                                    <li>
                                       <div class="row p-0">
                                          <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                                          <div class="col-lg-10 p-0 pl-1">{{ formatShippingAddress(address) }}</div>

                                          <div class="col-lg-1 pl-0 pt-1 pr-0"><i class="fa fa-pencil address_btn" data-toggle="modal" data-target="#addressModal" aria-hidden="true"></i></div>
                                       </div>
                                    </li>
                                    <li> <b><i class="fa fa-phone" aria-hidden="true"></i></b> <span>{{address.shipping_phone}}</span></li>
                                    <li v-if="address.shipping_email"> <b><i class="fa fa-envelope-o" aria-hidden="true"></i></b> <span>{{address.shipping_email}}</span> </li>
                                 </ul>

                              </div>
                              <div v-else class="address_details_alt">
                                 <div  class="row p-0">
                                       <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                                       <div class="col-lg-10 p-0">
                                          <p class="required_addtess" data-required-address="true">&nbsp;{{ $t('You need to add your default shipping address') }}.</p>
                                       </div>
                                       <div class="col-lg-1  pl-0 pt-1 pr-0"><i class="fa fa-pencil address_btn" data-toggle="modal" data-target="#addressModal" aria-hidden="true"></i></div>
                           
                                    </div> 
                              </div>  
                           </span>
                           <div v-else  class="address_details_alt">
                              <div v-if="logged_in_user" class="row p-0">
                                 <div class="col-lg-1 pr-0"><b><i class="fa fa-map-marker" aria-hidden="true"></i></b></div>
                                 <div v-if="logged_in_user.default_address_id == null" class="col-lg-10 p-0">
                                    <p class="required_addtess" data-required-address="true">{{ $t('You need to select your default shipping address') }}.</p>
                                 </div>
                                 <div v-else class="col-lg-10 p-0">
                                    <p class="required_addtess" data-required-address="true">{{ $t('You need to add your shipping address') }}.</p>
                                 </div>
                                 <div class="col-lg-1 pl-0"><i class="fa fa-pencil address_btn" data-toggle="modal" data-target="#addressModal" aria-hidden="true"></i></div>
                              </div>
                               <div v-else><p class="required_addtess" data-required-login="true" v-if="logged_in_user">{{ $t('You have to') }} <a href="" class="text-info" @click.prevent="showLoginPopup()"> login </a> {{ $t('first to add your shipping address') }}.</p></div>
                           </div>

                  </span>





                  <div class="note">
                     <h5 class="text-uppercase cart_summary_title mt-2">  {{ $t('Write a note') }} </h5>
                     <textarea type="text" v-model="note" class="form-control form_note" rows="3" :placeholder="$t('Write a note here')+'..'"></textarea>
                  </div>
                  <div v-if="collectedVoucher.length" class="collect_voucher_modal">
                     <h5 class="text-uppercase cart_summary_title">{{ $t('Collected Voucher') }}</h5>
                     <div  v-for="(cv,index) in collectedVoucher" :key="index">
                        <p><input type="hidden" name="collected_voucher" class="collected_voucher_radio" :value="cv.voucher_id" required> 
                           <img @error="imageLoadError" style="width:100%" :src="baseurl+'/'+cv.voucher.banner" alt="">
                        </p>
                     </div>
                  </div>
                  <div class="voucher_button mt-3">
                     <ul class="list-group list-group-horizontal">
                        <li v-if="useableVouchers.length" style="width:100%" class="list-group-item">
                           <a data-toggle="modal" data-target=".use_voucher_modal" href="javascript:void(0)">
                              <p class=" mb-0"> {{ $t('Use Voucher') }}</p>
                           </a>
                        </li>
                     </ul>
                  </div>


                  <div class="paymentmethod mt-3">
                     <h5 class="text-uppercase cart_summary_title"> {{ $t('Payment Method') }}</h5>
                     <ul class="list-group list-group-horizontal">
                        <!-- <li data-payment-method="online_payment" class="list-group-item  online_payment" >
                           <p class="text-center mb-0"> <img @error="imageLoadError" src="/images/ssl.png" alt=""> <br><b>{{ $t('Online Payment') }}</b></p>
                        </li> -->
                        <li data-payment-method="cash_on_delivery" class="list-group-item selected_payment cash_on_delivery">
                           <p class="text-center mb-0"> <img @error="imageLoadError" src="/images/cod1.png" alt=""> <br><b> {{ $t('Cash On Delivery') }}</b></p>
                        </li>
                     </ul>
                  </div>

                  <div class="payment-calculation mt-3 mb-4">
                     <h5 class="text-uppercase cart_summary_title">{{ $t('Order Summary') }}</h5>
                     <ul>
                        <li :data-subtotal-amount="sub_total" class="data_sub_total"> <b> {{ $t('Sub Total') }} </b>  <span v-if="sub_total"> BDT&nbsp;{{ sub_total }}</span> <span v-else> BDT {{ cartData.sub_total }} </span></li>
                       
                        <li v-if="site_info.default_shipping_enable != '1'" :data-shipping-cost="cartData.shipping_cost" class="shipping_cost_li"> <b>{{ $t('Shipping Cost') }} (+)</b>  <span>BDT&nbsp;<span class="calculatedShipping">{{ cartData.shipping_cost }}</span></span></li>

                        <li v-if="site_info.default_shipping_enable == '1'" :data-shipping-cost="cartData.shipping_cost" class="shipping_cost_li1"> <b>{{ $t('Shipping Cost') }} (+)</b>  <span>BDT&nbsp;<span class="calculatedShipping1">{{ cartData.shipping_cost }}</span></span></li>

                        <li v-if="cartData.packaging_cost > 0" :data-packaging-cost-amount="cartData.packaging_cost" class="data_packaging_cost"> <b> {{ $t('Packaging Cost') }} (+)</b> <span> BDT&nbsp;{{ cartData.packaging_cost }}</span> </li>

                        <li v-if="cartData.security_charge > 0" :data-security-charge-amount="cartData.security_charge" class="data_security_charge"> <b> {{ $t('Security Charge') }} (+)</b>  <span> BDT&nbsp;{{ cartData.security_charge }}</span> </li>

                        <li v-if="cartData.vat > 0" :data-vat-amount="cartData.vat" class="data_vat"> <b> {{ $t('Vat') }} (+)</b>  <span> BDT&nbsp;{{ cartData.vat }}</span> </li>

                        <li v-if="coupon_discount.status == 1 && Number(coupon_discount.amount) > 0" class="coupon_discount" :data-coupon-discount="Number(coupon_discount.amount)" > <b> {{ $t('Coupon Discount') }} (-)<br>  ({{coupon_discount.code}}) <a  @click.prevent="removeCoupon()" class="text-danger" href="javascript:void(0)"> {{ $t('Remove') }} </a></b>  <span>BDT {{ Number(coupon_discount.amount) }}</span></li>

                        <li data-coupon-discount="0" v-else class="coupon_discount"  > <b>{{ $t('Coupon Discount') }} (-) </b>  <span>BDT 0</span></li>
                        <li data-voucher-discount="0" class="show_voucher_discount"  ><b> {{ $t('Voucher Discount') }} (-)</b><span class="v_amount">BDT 0</span></li>

                        <li> <b class="totaprice" id="totalPrice" :data-total-price="sub_total+cartData.shipping_cost" > {{ $t('Total') }} </b>  <span> BDT&nbsp;<span class="calculatedTotal"> {{ finalCalculatedTotal}}</span></span></li>
                     </ul>

                     <span v-if="site_info.partial_payment_enable == '1'">
                     <div class="partial_payment mt-3">
                        <h5 class="text-uppercase cart_summary_title"> {{ $t('Partial Payment') }} (BDT)</h5>
                        <input id="partial_payment" type="text" class="form-control" :placeholder="$t('Amount you want to pay now')+'..'"  aria-describedby="basic-addon2">
                     </div>
                     </span>

                  </div>

                  <div class="procced-checkout mt-3">
                     <ul>
                        <li>
                          <button class="btn btn-secondary site_color2 back_to_cart back_to_cartbtn"> {{ $t('Cart') }} </button> 
                          <button class="btn btn-primary site_color1 proceed_to_pay" @click.prevent="proceedToPay()" id="confirm_purchase_btn">PLACE ORDER • ৳{{ finalCalculatedTotal }}</button>
                        </li>
                     </ul>
                  </div>
                  <div id="legacyCartCouponBlock" class="legacy-cart-coupon">
                     <button type="button" class="legacy-cart-coupon-toggle" @click="legacyCouponExpanded = !legacyCouponExpanded">আপনার কি কোনো কুপন আছে?</button>
                     <div v-if="legacyCouponExpanded" class="input-group mt-2 mb-0">
                        <input id="legacyCartCouponCode" type="text" class="form-control" :placeholder="$t('Write a coupon code here')+'..'" aria-describedby="legacyCartCouponApply">
                        <div class="input-group-append">
                           <span class="input-group-text d-block" @click.prevent="applyCouponCode()" id="legacyCartCouponApply">{{ $t('Apply') }}</span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <section v-else  id="cart-page-shimmer">
         <div class="container">
            <div class="row">
               <div v-if="cartData.sub_total == 0 || cartData.status == 0" class="col-md-12">
                  <p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
                  <h4> {{ $t('No product in cart') }} ! </h4>
                  <p>
                     <router-link :to="{name:'products'}"> {{ $t('Continue shopping') }} </router-link>
                  </p>
               </div>
               <div v-else class="col-md-12 simar_lodding">
                  <div class="container">
                     <div class="row cart-page-container">
                        <div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
                           <div class="row shoping-cart-text">
                              <div class="col-12 col-sm-12 col-md-12">
                                 <div class="shimmer">
                                    <div class="h_3 w_10 ml_10 mt_5"></div>
                                 </div>
                              </div>
                           </div>
                           <div class="cart-calculation">
                              <table  class="table text-left cart_table" width="100%">
                                 <thead>
                                    <tr>
                                       <th width="5%">
                                          <div class="shimmer">
                                             <div class="h_2 w_5"></div>
                                          </div>
                                       </th>
                                       <th width="60%"></th>
                                       <th width="5%"> </th>
                                       <th width="5%"> </th>
                                       <th width="20%">
                                          <div class="shimmer">
                                             <div class="h_2 w_5"></div>
                                          </div>
                                       </th>
                                       <th width="5%" style="text-align: right;">
                                          <div class="shimmer">
                                             <div class="h_2 w_5"></div>
                                          </div>
                                       </th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr class="group_header mb-3">
                                       <td colspan="7">
                                          <small>
                                             <div class="shimmer">
                                                <div class="h_2 w_7"></div>
                                             </div>
                                          </small>
                                       </td>
                                    </tr>
                                    <tr class="cart_product_group">
                                       <td>
                                          <div class="product-cart-img">
                                             <div class="shimmer">
                                                <div class="h_10 w_6 mr_5"></div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                       </td>
                                       <td> </td>
                                       <td> </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                    </tr>
                                    <tr class="group_header mb-3">
                                       <td colspan="7">
                                          <small>
                                             <div class="shimmer">
                                                <div class="h_2 w_7"></div>
                                             </div>
                                          </small>
                                       </td>
                                    </tr>
                                    <tr class="cart_product_group">
                                       <td>
                                          <div class="product-cart-img">
                                             <div class="shimmer">
                                                <div class="h_10 w_6 mr_5"></div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                       </td>
                                       <td> </td>
                                       <td> </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                    </tr>
                                    <tr class="group_header mb-3">
                                       <td colspan="7">
                                          <small>
                                             <div class="shimmer">
                                                <div class="h_2 w_7"></div>
                                             </div>
                                          </small>
                                       </td>
                                    </tr>
                                    <tr class="cart_product_group">
                                       <td>
                                          <div class="product-cart-img">
                                             <div class="shimmer">
                                                <div class="h_10 w_6 mr_5"></div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                       </td>
                                       <td> </td>
                                       <td> </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
                           <div class="note">
                              <div class="shimmer">
                                 <div class="h_2 w_48per mb_5"></div>
                              </div>
                           </div>
                           <div class="payment-calculation">
                              <ul>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_10 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_7 w_100per mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_100per mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_48per mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_7 w_48per f_left"></div>
                                       <div class="h_7 w_48per f_right mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                           <div class="procced-checkout">
                              <ul>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_100per mb_10"></div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>
   <div v-else>
      <section id="cart-page-shimmer">
         <div class="container">
            <div class="row">
               <div v-if="cartData.sub_total == 0 || cartData.status == 0" class="col-md-12">
                  <p> <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">  </p>
                  <h4> {{ $t('No product in cart') }} ! </h4>
                  <p>
                     <router-link :to="{name:'products'}"> {{ $t('Continue shopping') }} </router-link>
                  </p>
               </div>
               <div v-else class="col-md-12 simar_lodding">
                  <div class="container">
                     <div class="row cart-page-container">
                        <div class="col-12 col-sm-12 col-md-8 col-lg-9 pr-0">
                           <div class="row shoping-cart-text">
                              <div class="col-12 col-sm-12 col-md-12">
                                 <div class="shimmer">
                                    <div class="h_3 w_10 ml_10 mt_5"></div>
                                 </div>
                              </div>
                           </div>
                           <div class="cart-calculation">
                              <table  class="table text-left cart_table" width="100%">
                                 <thead>
                                    <tr>
                                       <th width="5%">
                                          <div class="shimmer">
                                             <div class="h_2 w_5"></div>
                                          </div>
                                       </th>
                                       <th width="60%"></th>
                                       <th width="5%"> </th>
                                       <th width="5%"> </th>
                                       <th width="20%">
                                          <div class="shimmer">
                                             <div class="h_2 w_5"></div>
                                          </div>
                                       </th>
                                       <th width="5%" style="text-align: right;">
                                          <div class="shimmer">
                                             <div class="h_2 w_5"></div>
                                          </div>
                                       </th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <tr class="group_header mb-3">
                                       <td colspan="7">
                                          <small>
                                             <div class="shimmer">
                                                <div class="h_2 w_7"></div>
                                             </div>
                                          </small>
                                       </td>
                                    </tr>
                                    <tr class="cart_product_group">
                                       <td>
                                          <div class="product-cart-img">
                                             <div class="shimmer">
                                                <div class="h_10 w_6 mr_5"></div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                       </td>
                                       <td> </td>
                                       <td> </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                    </tr>
                                    <tr class="group_header mb-3">
                                       <td colspan="7">
                                          <small>
                                             <div class="shimmer">
                                                <div class="h_2 w_7"></div>
                                             </div>
                                          </small>
                                       </td>
                                    </tr>
                                    <tr class="cart_product_group">
                                       <td>
                                          <div class="product-cart-img">
                                             <div class="shimmer">
                                                <div class="h_10 w_6 mr_5"></div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                       </td>
                                       <td> </td>
                                       <td> </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                    </tr>
                                    <tr class="group_header mb-3">
                                       <td colspan="7">
                                          <small>
                                             <div class="shimmer">
                                                <div class="h_2 w_7"></div>
                                             </div>
                                          </small>
                                       </td>
                                    </tr>
                                    <tr class="cart_product_group">
                                       <td>
                                          <div class="product-cart-img">
                                             <div class="shimmer">
                                                <div class="h_10 w_6 mr_5"></div>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                          <div class="shimmer">
                                             <div class="h_10 w_6 mr_5 f_left"></div>
                                          </div>
                                       </td>
                                       <td> </td>
                                       <td> </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="shimmer">
                                             <div class="h_3 w_5"></div>
                                          </div>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-3 payment">
                           <div class="note">
                              <div class="shimmer">
                                 <div class="h_2 w_48per mb_5"></div>
                              </div>
                           </div>
                           <div class="payment-calculation">
                              <ul>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_10 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_7 w_100per mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_100per mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_48per mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_7 w_48per f_left"></div>
                                       <div class="h_7 w_48per f_right mb_10"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_2 w_100per mb_5"></div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                           <div class="procced-checkout">
                              <ul>
                                 <li>
                                    <div class="shimmer">
                                       <div class="h_3 w_100per mb_10"></div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </div>

   <!-- Address Modal start -->
   <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
   <div class="modal-content">
   <div class="modal-header border-bottom-0">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
   <div class="form-title text-center">
      <h5><b>{{ $t('Add New Address') }}</b></h5>
   </div>
   <div class="d-flex flex-column text-center">
      <ul class="nav nav-tabs">
         <li class="btn btn-dark active"><a data-toggle="tab" href="#home">{{ $t('Address book') }}</a></li>
         <li class="btn btn-dark"><a data-toggle="tab" href="#menu1"> <i class="fa fa-plus"></i> {{ $t('Add new address') }}</a></li>
      </ul>
      <div class="tab-content">
         <div id="home" class="tab-pane fade in active">
           
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th scope="col"> {{ $t('Full Name') }} </th>
                     <th scope="col"> {{ $t('Phone') }}</th>
                     <th scope="col"> {{ $t('Address') }}</th>
                     <th scope="col"> {{ $t('Defalut') }}</th>
                  </tr>
               </thead>
               <tbody v-if="logged_in_user_address.length > 0">
                  <tr v-for="(address, index) in logged_in_user_address" :key="index" @click.prevent="change_address(address.id,0)">
                     <td> {{ address.shipping_first_name }}  {{ address.shipping_last_name }}  </td>
                     <td> {{ address.shipping_phone }} </td>
                    <td>{{ formatShippingAddress(address) }}</td>
                     <td>
                        <span v-if="logged_in_user.default_address_id == address.id">
                           <div class="select_address" title="It is your default address"> </div>
                        </span>
                        <span v-else>
                           <div class="unselect_address" title="Make this address default"> </div>
                        </span>
                     </td>
                  </tr>
               </tbody>
            </table>

            <!-- <h5><b>{{ $t('Select address from our pickup point') }}</b></h5>

            <table class="table table-hover">
               <thead>
                  <tr>
                     <th scope="col"> {{ $t('Pick Point') }} </th>
                     <th scope="col"> {{ $t('Phone') }}</th>
                     <th scope="col"> {{ $t('Address') }}</th>
                     <th scope="col"> {{ $t('Defalut') }}</th>
                  </tr>
               </thead>
               <tbody v-if="pickpoint_address.length > 0">
                  <tr v-for="(address, index) in pickpoint_address" :key="index" @click.prevent="change_address(address.id,1)">
                     <td> {{ address.title }} </td>
                     <td> {{ address.phone }} </td>

                     <td><span v-if="address.division">{{ address.division.title }}</span>,<span v-if="address.district"> {{ address.district.title }}</span>, <span v-if="address.upazila"> {{ address.upazila.title }}</span>, <span v-if="address.union">{{ address.union.title }}</span></td>
                     <td>
                        <span v-if="logged_in_user.default_address_id == address.id">
                           <div class="select_address" title="It is your default address"> </div>
                        </span>
                        <span v-else>
                           <div class="unselect_address" title="Make this address default"> </div>
                        </span>
                     </td>
                  </tr>
               </tbody>
            </table> -->


         </div>
         <div id="menu1" class="tab-pane fade">
            <div class="col-md-12">
               <form @submit.prevent="addNewAddress()">
                  <div class="options">
                     <div class="row text-left">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label for=""> {{ $t('Full Name') }}<span style="color:#f00">*</span></label>
                                 <input type="text" class="form-control shipping_first_name0" placeholder="আপনার পূর্ণ নাম লিখুন" required>
                              <div class="validation_error" v-if="errors.shipping_first_name" v-html="errors.shipping_first_name[0]" />
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="">  {{ $t('Phone') }} <span style="color:#f00">*</span></label>
                                 <input type="text" class="form-control popup_phone0" placeholder="01XXXXXXXXX" @input="normalizePhoneDigits" required>
                                 <div class="validation_error" v-if="errors.shipping_phone" v-html="errors.shipping_phone[0]" />
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-12">
                              <div class="form-group">
                                 <label for="">  {{ $t('Address') }} <span style="color:#f00">*</span></label>
                                 <textarea
                                    v-model="shippingAddressInput"
                                    @input="onShippingAddressInput"
                                    @blur="resolveAddressOnBlur"
                                    cols="30"
                                    rows="3"
                                    class="form-control shipping_address0"
                                    placeholder="পূর্ণ ঠিকানা লিখুন: থানা, জেলা, ইউনিয়ন/এলাকা, হোল্ডিং/অফিস নং, রোড"
                                    required
                                 ></textarea>
                                 <div class="validation_error" v-if="errors.shipping_address" v-html="errors.shipping_address[0]" />

                                 <small v-if="resolverLoading" class="form-text text-muted">
                                    এলাকা শনাক্ত করা হচ্ছে...
                                 </small>
                                 <div v-show="showShippingAddressWarning" class="mt-2" style="width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #efb45a;border-radius:5px;background:#fff8e8;color:#805400;font-size:13px;line-height:1.35;overflow-wrap:anywhere;">
                                    ⚠️ জেলা-থানা সিলেক্ট হয়েছে। সঠিক ডেলিভারির জন্য বাড়ি/রোড/গ্রাম অথবা কাছাকাছি পরিচিত স্থানের তথ্য লিখুন
                                 </div>
                                 <div v-if="hasSelectedAddressLocation && hasUsefulAddressDetail" class="mt-2" style="width:100%;box-sizing:border-box;padding:6px 9px;border:1px solid #79bd79;border-radius:5px;background:#edf9ed;color:#267326;font-size:13px;line-height:1.35;overflow-wrap:anywhere;font-weight:600;">
                                    ✓ এলাকা শনাক্ত হয়েছে: {{ selectedAddressThanaTitle }}, {{ selectedAddressDistrictTitle }}
                                 </div>
                                 <small v-else-if="resolverMatchType === 'ambiguous'" class="form-text text-warning">
                                    নিচ থেকে সঠিক থানা/এরিয়া নির্বাচন করুন।
                                 </small>
                                 <div v-else-if="resolverMatchType === 'district_only'" class="mt-2" style="width:100%;box-sizing:border-box;padding:6px 9px;border:1px solid #7fc4d9;border-radius:5px;background:#eef9fc;color:#246477;font-size:13px;line-height:1.35;overflow-wrap:anywhere;">
                                    জেলা শনাক্ত হয়েছে। নিচ থেকে থানা/এরিয়া নির্বাচন করে পূর্ণ ঠিকানা দিন।
                                 </div>
                                 <small v-else-if="resolverMatchType === 'none'" class="form-text text-danger">
                                    ঠিকানা থেকে এলাকা শনাক্ত করা যায়নি। নিচ থেকে জেলা ও থানা নির্বাচন করুন।
                                 </small>
                                 <small v-if="resolverError" class="form-text text-danger">
                                    এলাকা শনাক্ত করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।
                                 </small>
                                 <small v-if="cartContainsGrocery" class="form-text text-warning">
                                    গ্রোসারি ডেলিভারির জন্য একটি বিস্তারিত সংরক্ষিত ঠিকানা ও এলাকা প্রয়োজন।
                                 </small>
                              </div>
                           </div>
                        </div>
                        <div class="row text-left">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="header_shipping_district">{{ $t('District') }} <span style="color:#f00">*</span></label>
                                 <select id="district0" @change="onManualDistrictChange" class="form-control" required>
                                    <option value="">--Select District--</option>
                                    <option v-for="district in districts" :key="district.id" :value="district.id">{{ district.title }}</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="header_shipping_thana">{{ $t('Thana / Area') }} <span style="color:#f00">*</span></label>
                                 <select id="upazila0" @change="onManualThanaChange" class="form-control" required>
                                    <option value="" disabled>--Select Thana / Area--</option>
                                    <option data-removeable="true" v-for="(upazila,index) in upazilas" :key="index" :value="upazila.id">{{ upazila.title }}</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="row text-left">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="union0">{{ $t('Specific Area') }} ({{ $t('Optional') }})</label>
                                 <select id="union0" @change="onManualUnionChange" class="form-control">
                                    <option value="">--Select Specific Area--</option>
                                    <option v-for="union in unions" :key="union.id" :value="union.id">{{ union.title }}</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <p class="text-right"> <button type="submit" class="btn btn-dark"> {{ $t('Add new address') }}</button> </p>
                     </div>
               </form>
               </div>
               </div>
               </div>
               </div>
               </div>
               </div>
               </div>
               </div>



               <div v-if="useableVouchers.length" class="modal fade use_voucher_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                     <div class="modal-content">
                        <form @submit.prevent="checkUseableVoucher">
                           <div class="modal-header border-bottom-0 abs_right ">
                              <button type="button" class="close custom_close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                           </div>
                           <div class="modal-body">
                              <h3 class="text-center"> {{ $t('Use Voucher') }} </h3>
                              <p class="text-center"> {{ $t('Please select the voucher') }}</p>
                              <div class="row">
                                 <div  class="col-md-6" v-for="(cv,index) in useableVouchers" :key="index">
                                    <p><input type="radio" name="useable_vouchers" class="useable_vouchers_radio" :data-voucher-discount="Number(cv.voucher.amount)" :value="cv.voucher_id" required> <img @error="imageLoadError" style="width:80%" :src="baseurl+'/'+cv.voucher.banner" alt=""></p>
                                 </div>
                              </div>
                           </div>
                           <div class="modal-footer border-top-0 ">
                              <p class="text-right"><button type="submit" class="btn btn-dark"> {{ $t('Apply') }} </button></p>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- Address Modal start -->


</template>
<script>
   import Vue from 'vue';
   import Form from 'vform'
   import axios from 'axios'
   import {bus} from '../../app.js'
import jquery from '../../../../../../public/assets/js/jquery.js';
   
   export default {
     data(){
       return{
         signupForm: new Form({
            name: '',
            phone: '',
            email: '',
            mobile_number: '',
            otp: '',
            password: '',
            password_confirmation: '',
            affiliate_referer: '',
         }),
         errors:{},
         show:false,
         errors: [],
         navbars:'',
         static_pages:'',
         site_info:'',
         userLoged:null,
         categories:[],
         cartItem:[],
         cartLocalStorage:[],
         baseurl:'',
   	    carts:'',
         sub_total:'',
         compareList:'',
         notification:'',
         offer_title:'',
         suggetionProduct:'',
         suggetionShops:'',
         suggetionCategories:'',
        //  useableVouchers:'',
        //  collectedVoucher:'',
         StorageLanguade:'',
         lang:'',
         loading:false,
   			agree:false,
   			note:'',
   			coupon_discount:{},
   			cartCount:'',
               addresses: {},
               districts:{},
               upazilas:[],
               unions:{},
               shippingAddressInput:'',
               resolverLoading:false,
               resolverMatchType:'',
               resolverCandidates:[],
               selectedResolverCandidateIndex:'',
               resolvedLocation:null,
               resolvedShippingDistrict:null,
               resolvedShippingThana:null,
               resolvedShippingUnion:null,
               resolvedCityId:null,
               resolvedZoneId:null,
               resolverError:false,
               resolverDebounceTimer:null,
               resolverRequestSequence:0,
               resolverLastRequestedAddress:'',
               manualLocationOverride:false,
               customerWarningSelectionRevision:0,
               errors:{},
               errors: [],
               finalCalculatedTotal:0,
               legacyCouponExpanded:false,
               suggetionProductstatus:false,
       }
     },
       
     methods: {
        normalizePhoneDigits(event){
           event.target.value = event.target.value.replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit));
        },
         signup(){
            let session_key = localStorage.getItem("session_key");
            let phone = $('#login_page_generate_otp').val();
            let otp = $('#popupOtp_login_page').val();
            let formData = new FormData();
            formData.append('mobile_number', phone);
            formData.append('otp', otp);
            formData.append('session_key', session_key);

            axios.post(this.$baseUrl + "/api/v1/user-register", formData).then(response =>{
               if(response.data.status == 2){
                  swal({
                     title: response.data.message,
                     icon: "error",
                     timer: 4000
                  });
               }else if(response.data.status == 1){
                     swal({
                     title: "Your account has been successfully created. You are logged in.",
                     icon: "success",
                     timer: 4000
                     }).then(()=>{
                        localStorage.setItem("token", response.data.token);
                        this.$store.dispatch('loadedUser');
                        this.$store.dispatch('loadedCart');
                        this.$store.dispatch('loadedCompares');
                        this.$store.dispatch('loadedNotifications');
                        $('.close').trigger('click');
                        $('.cart_close').trigger('click');
                        this.$router.push({name:'myaccount'});
                        jQuery('.left_cart_icon').trigger('click');
                        jQuery('#addressModal').modal('show');
                        jQuery('#addressModal a[href$="#menu1"]').trigger('click');
                     });
               }else{
                  this.errors = response.data.message;
               }
            }).catch(function(){
                swal({
                    title: response.data.message,
                    icon: "error",
                    timer: 3000
                });
            });
         },

         generateOtp_login_page(){

               let formData = new FormData();
            formData.append('mobile_number', $('#login_page_generate_otp').val());
            let token = localStorage.getItem("token");
            let axiosConfig = {
               headers: {
                  'Content-Type': 'application/json;charset=UTF-8',
                  "Access-Control-Allow-Origin": "*",
                  'Authorization': 'Bearer '+token
               }
            }
            axios.post(this.$baseUrl+'/api/v1/generate-otp-for-signup',formData, axiosConfig).then(response => {

               if(response.data.status == 1){
                  swal({
                     title: response.data.message,
                     icon: "success",
                     timer: 3000
                  });
                     $('.generate_otp_btn').hide();
                     $('.popupOtp_login_page_group').removeClass('d-none');
                     $('.popupOtp_login_page_group').addClass('d-block');
               }else{
                  swal ( "Oops", response.data.message, "error");
               }
            });
         },

           async getLocationDistrict(){
               
               await axios.get(this.$baseUrl + "/api/v1/get-district/"+0).then((response) => {
                   this.upazilas.splice(0, this.upazilas.length);
                   this.unions = {};
                   this.districts = response.data;
               });
           },
           
           async getLocationUpazila(){
               let id =  jQuery('.location_district').find('option:selected').val();
               await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id).then((response) => {
                       this.unions = {};
                       const upazilas = Array.isArray(response.data) ? response.data : [];
                       this.upazilas.splice(0, this.upazilas.length, ...upazilas);
                   });
            },
            async getLocationUnion(){
               let id =  jQuery('.location_upazail').find('option:selected').val();
               await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
                  this.unions = response.data;
               });
           },


         removeLocation(){
               localStorage.setItem("upazail_id", null);
               window.location.reload();
         },
         location_submit(){
            let division_id = jQuery('.location_division').find('option:selected').val();
            let district_id = jQuery('.location_district').find('option:selected').val();
            let upazail_id = jQuery('.location_upazail').find('option:selected').val();
            if(isNaN(division_id)){
               swal( "Sorry" ,  'Select division first.',  "error" );
            }else if(isNaN(district_id)){
               swal( "Sorry" ,  'Select district first.',  "error" );
            }else if(isNaN(upazail_id)){
               swal( "Sorry" ,  'Select upazila.',  "error" );
            }else{
               localStorage.setItem("upazail_id", upazail_id);
               window.location.reload();
            }
         },

        load_static_pages(){
            axios.get(this.$baseUrl+'/api/v1/get-static-pages').then(response => {
                this.static_pages = response.data;
            });
        },


          async logInWithFacebook() {
               let that = this;
   
               await this.loadFacebookSDK(document, "script", "facebook-jssdk");
               await this.initFacebook();
               window.FB.login(function(response) {
                   if(response.status == 'connected'){
                       if (response.authResponse) {
                           axios.post('https://api.nurtaj.com/api/v1/social-login/facebook', response.authResponse).then(function(result){
                               localStorage.setItem("token", result.data.token);
                               that.$store.dispatch('loadedUser');
                               that.$store.dispatch('loadedCart');
                               that.$store.dispatch('loadedCompares');
                               that.$store.dispatch('loadedNotifications');
                               that.$router.push({name:'myaccount'});
                           }).catch(function(e){
                               swal ( "Oops" ,  e,  "error" );
                           });
   
                       } else {
                           swal ( "Oops" ,  'Sorry! Facebook does\'t provide authentication for this user.',  "error" );
                       }
                   }else{
                       swal ( "Oops" ,  'Sorry! Facebook login service is currently unavailable. Please try again later!',  "error" );
                   }
               });
               return false;
           },
           async initFacebook() {
               window.fbAsyncInit = function() {
                   window.FB.init({
                   appId: "761135615074146",
                   cookie: true,
                   version: "v13.0"
                   });
               };
           },
           async loadFacebookSDK(d, s, id) {
               var js,
                   fjs = d.getElementsByTagName(s)[0];
               if (d.getElementById(id)) {
                   return;
               }
               js = d.createElement(s);
               js.id = id;
               js.src = "https://connect.facebook.net/en_US/sdk.js";
               fjs.parentNode.insertBefore(js, fjs);
           },
   
   
           //Google Login
           async logInWithGoogle() {
                   let that = this;
                   window.onload = function () {
                       google.accounts.id.initialize({
                           client_id: "397239095845-llm31ean6e5v33s3r8lhucahvk1amnko.apps.googleusercontent.com",
                           callback: function handleCredentialResponse(response) {
                                   if (response.credential) {
                                       axios.post('https://api.nurtaj.com/api/v1/social-login/google', {token:response.credential}).then(function(result){
                                           localStorage.setItem("token", result.data.token);
                                           that.$store.dispatch('loadedUser');
                                           that.$store.dispatch('loadedCart');
                                           that.$store.dispatch('loadedCompares');
                                           that.$store.dispatch('loadedNotifications');
                                           that.$router.push({name:'myaccount'});
   
                                       }).catch(function(e){
                                           swal ( "Oops" ,  e,  "error" );
                                       });
   
                                   } else {
                                       swal ( "Oops" ,  'Sorry! Google does\'t provide authentication for this user.',  "error" );
                                   }
                           }
                       });
                       google.accounts.id.renderButton(
                           document.getElementById("googleButtonDiv"),
                           { theme: "filled_blue", size: "large",width:'320',text:'continue_with' }  // customization attributes
                       );
                       google.accounts.id.prompt(); // also display the One Tap dialog
                   }
               },
           logInWithGoogleRedirect(){
               this.$router.go(this.$router.currentRoute);
           },
   
   
   
       load_promotional_offer_title(){
           axios.get(this.$baseUrl+'/api/v1/get-promotion-title').then(response => {
             this.offer_title = response.data;
           });
       },
   
       changeLanguage(){
         if(this.lang){
           localStorage.setItem("lang", 'bn');
          // this.$i18n.locale = localStorage.getItem("lang");
           this.$router.go();
         }else{
           localStorage.setItem("lang", 'en');
           //this.$i18n.locale = localStorage.getItem("lang");
           this.$router.go();
         }
       },
   
   		removeItem($row_id){
            $('#cart_'+$row_id).html('<i class="fa fa-spinner fa-spin text-danger"></i>');
            let session_key = localStorage.getItem("session_key");
   			let token = localStorage.getItem("token");
   			let axiosConfig = {
   			  headers: {
   				  'Content-Type': 'application/json;charset=UTF-8',
   				  "Access-Control-Allow-Origin": "*",
   				  'Authorization': 'Bearer '+token
   			  }
   			}
   			let formData = new FormData();
   			formData.append('row_id', $row_id);
            formData.append('session_key', session_key);
   
   			axios.post(this.$baseUrl+'/api/v1/remove-cart-item',formData,axiosConfig).then(response =>{
   				if(response.data.status == 1){
    
               this.$store.dispatch('loadedCart');
               this.$store.dispatch('loadedVoucher');
               this.$store.dispatch('loadedUsableVoucher');

               $('#cart_'+$row_id).html('');
               $('.addagain').html('<i class="fa fa-trash"></i>');
               

   				}else{
   					swal ("Oops" ,response.data.message,  "error");
   				}
   			});
   		},
   
       viewNotification(notification_id,notification){
           let token = localStorage.getItem("token");
           let axiosConfig = {
             headers: {
               'Content-Type': 'application/json;charset=UTF-8',
               "Access-Control-Allow-Origin": "*",
               'Authorization': 'Bearer '+token
             }
           }
           axios.post(this.$baseUrl+'/api/v1/view-notification', {notification_id:notification_id}, axiosConfig).then(response => {
             if(response.data.status == 1){
               this.$store.dispatch('loadedNotifications');

               if(notification.type == 'order'){
                  this.$router.push({name: 'orderDetails', params: {id: notification.order_id } });
               }else if(notification.type == 'deal'){
                  this.$router.push({ name: 'flashdeal', params: {slug: notification.deal_slug } });
               }else if(notification.type == 'cart'){
                  jQuery('.left_cart_icon').trigger('click');
               }

               
             }else{
               swal("Sorry" , response.data.message,  "error" );
             }
           }); 
       },
      updateQty($rowId, $update){
          let token = localStorage.getItem("token");
          let session_key = localStorage.getItem("session_key");
          let axiosConfig = {
            headers: {
              'Content-Type': 'application/json;charset=UTF-8',
              "Access-Control-Allow-Origin": "*",
              'Authorization': 'Bearer '+token
            }
          }
          let formData = new FormData();
          formData.append('rowId', $rowId);
          formData.append('update', $update);
          formData.append('session_key', session_key);
          axios.post(this.$baseUrl+'/api/v1/update-qty', formData,axiosConfig).then(response =>{
            if(response.data.status == '1'){
              this.$store.dispatch('loadedCart');
            }else{
              swal ( "Oops", response.data.message, "error");
            }

          });
      },


		popupPasswordLogin(){
            let session_key = localStorage.getItem("session_key");
			let phone = $('.phone').val();
			let password = $('.password').val();
			let formData = new FormData();
			formData.append('phone', phone);
			formData.append('password', password);
            formData.append('session_key', session_key);
            
			if(phone == '' || password == ''){
				swal({
				  title: "Phone number and password is required.",
				  icon: "error",
				  timer: 3000
				});
			}else{
				axios.post(this.$baseUrl+'/api/v1/login', formData).then(response =>{
                    if(response.data.status == 1){
                        localStorage.setItem("token", response.data.token);
                        this.$store.dispatch('loadedUser');
                        this.$store.dispatch('loadedCart');
                        this.$store.dispatch('loadedCompares');
                        this.$store.dispatch('loadedNotifications');
                         $('.close').trigger('click');
                         console.log('dispatching in passwor login..!');
                        this.$router.push({name:'myaccount'});
                        this.initChat();
    
                    }else{
                        swal({
                            title: response.data.message,
                            icon: "error",
                            timer: 3000
                        });
                    }
				}).catch(function(){
                    swal({
                        title: response.data.message,
                        icon: "error",
                        timer: 3000
                    });
				});
			}
		},

      PopupOTPSignIn(){
         let session_key = localStorage.getItem("session_key");
         let formData = new FormData();
         formData.append('mobile_number', $('.mobile_number').val());
			formData.append('otp', $('.popupOtp_login').val());
			formData.append('session_key', session_key);
         
			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/otp-login',formData, axiosConfig).then(response => {

				if(response.data.status == 1){
                swal({
                    title: response.data.message,
                    icon: "success",
                    timer: 3000
                });
               let user = response.data.customer;
               this.$store.commit('SET_USER', user);
               this.$store.commit('SET_AUTHENTICATED', true);
               localStorage.setItem("auth", true);
               localStorage.setItem("token", response.data.token);
               localStorage.setItem("user_id", response.data.customer.id);
               this.$store.dispatch('loadedUser');
               this.$store.dispatch('loadedCart');
               this.$store.dispatch('loadedCompares');
               this.$store.dispatch('loadedNotifications');
               console.log('dispatching in OTP login..!');
                $('.close').trigger('click');
               this.$router.push({name:'myaccount'});
				}else{
					swal ( "Oops", response.data.message, "error");
				}
			});
        },

        PopupGenerateOtp(){
            let formData = new FormData();
			   formData.append('mobile_number', $('.mobile_number').val());

			let token = localStorage.getItem("token");
			let axiosConfig = {
				headers: {
					'Content-Type': 'application/json;charset=UTF-8',
					"Access-Control-Allow-Origin": "*",
					'Authorization': 'Bearer '+token
				}
			}
			axios.post(this.$baseUrl+'/api/v1/generate-otp',formData, axiosConfig).then(response => {

				if(response.data.status == 1){
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



       imageLoadError(event){
             event.target.src = "/images/notfound.png";
       },
   
      mobile_searh_searchSubmit(){
         let content = $('.mobile_search_input').val();
  
         if(content){
           this.$router.push({
             name:'search',
             params: { content: content}
           });
         }else{
           this.$router.push({name:'products'});
         }
      },
   
      searchSubmit(){
         let content = $('.searchContent').val();
  
         if(content){
           this.$router.push({
             name:'search',
             params: { content: content}
           });
         }else{
           this.$router.push({name:'products'});
         }
      },


      Headerlogout(){
         // const response = await axios.get('/api/Headerlogout');
         this.$store.commit('SET_AUTHENTICATED', false);
         this.$store.commit('LOADED_USER', []);
         this.$store.commit('LOADED_CART', []);
         this.$store.commit('LOADED_COMPARE', []);
         this.$store.commit('LOADED_NOTIFICATIONS', []);
         localStorage.removeItem("auth");
         localStorage.removeItem("cart");
         localStorage.removeItem("token");
         localStorage.removeItem("userID");
         localStorage.removeItem("userName");
         this.$store.dispatch('loadedUser');
         this.$store.dispatch('loadedCart');
         this.$store.dispatch('loadedCompares');
         this.$store.dispatch('loadedNotifications');
         this.$store.dispatch('loadedVoucher');
         this.$store.dispatch('loadedUsableVoucher');

         localStorage.setItem("token", false);
         this.$router.push({name:'sign-up'});
       },
       load_categories(){
         let axiosConfig = {
           headers: {
             'X-localization': localStorage.getItem('lang')
           }
         }
         axios.get(this.$baseUrl+'/api/v1/categories', axiosConfig).then(response => {
           this.categories = response.data;
           this.next_page_url = response.next_page_url;
         });
       },
       site_information(){
         let axiosConfig = {
           headers: {
             'X-localization': localStorage.getItem('lang')
           }
         }
         axios.get(this.$baseUrl+'/api/v1/site-info?upazail_id='+localStorage.getItem('upazail_id'), axiosConfig).then(response => {
            this.site_info = response.data;
            this.navbars = response.data.navbars
            if(this.site_info.social_login == 1){
               this.initFacebook();
               this.logInWithGoogle();
            }
            
         });
       },
   
      search_suggest(){
         $('.search_suggest_wrapper').show();
         let searchContent = $('.searchContent').val();

         if(searchContent.length > 2){
            axios.get(this.$baseUrl+'/api/v1/get-search-suggetion/'+searchContent+'?upazila_id='+localStorage.getItem('upazail_id')).then(response => {
             this.suggetionProduct = response.data;
             if(response.data.status == 1){
               $('.search_suggest_wrapper').show();
               this.suggetionProductstatus = true;
             }else{
               this.suggetionProductstatus = false;
               $('.search_suggest_wrapper').show();
             }
             this.suggetionShops = response.data.shops;
             this.suggetionCategories = response.data.categories;
           });
         }else{
           $('.search_suggest_wrapper').hide();
         }
      },

      mobile_search_suggest(){
         $('.mobile_search_suggest_wrapper').show();
         let searchContent = $('.mobileSearchContent').val();

         if(searchContent.length > 2){
           axios.get(this.$baseUrl+'/api/v1/get-search-suggetion/'+searchContent+'?upazila_id='+localStorage.getItem('upazail_id')).then(response => {
             this.suggetionProduct = response.data;
             if(response.data.status == 1){
               $('.mobile_search_suggest_wrapper').show();
               this.suggetionProductstatus = true;
             }else{
               this.suggetionProductstatus = false;
               $('.mobile_search_suggest_wrapper').show();
             }
             this.suggetionShops = response.data.shops;
             this.suggetionCategories = response.data.categories;
           });
         }else{
           $('.mobile_search_suggest_wrapper').hide();
         }
      },


       scrollToTop(){
         window.scrollTo(0,0);
       },
   
       loading_method(){
   		 this.loading = true;
   	},
      showLoginPopup(){
         jQuery('#popupLoignModal').trigger('click');
         return true;
      },

      proceedToPay(){
         jQuery('.cart_close').trigger('click');
         this.$router.push({name: 'checkout'});
         return true;
      },
   
           checkUseableVoucher(){
               let usedVoucher = 0;
               jQuery('.close').trigger('click');
                jQuery('.useable_vouchers_radio').each(function(key,val){
                   if (jQuery(this).is(':checked')) {
                       usedVoucher = jQuery(this).attr('data-voucher-discount');
                   }
               });
               jQuery('.show_voucher_discount').attr('data-voucher-discount',Number(usedVoucher));
               jQuery('.show_voucher_discount .v_amount').html('BDT '+usedVoucher);
               this.calculateFinalAmount();
           },
   
           calculateFinalAmount(){
               calculateShipping();
           },
         imageLoadError(event){
   			event.target.src = "/images/notfound.png";
		},
         formatShippingAddress(address){
               let location = [];
               if(address.upazila && address.upazila.title){
                  location.push(address.upazila.title);
               }
               if(address.district && address.district.title){
                  location.push(address.district.title);
               }
               if(address.union && address.union.title){
                  location.push(address.union.title);
               }
               if(address.shipping_address && location.length){
                  return location.join(', ') + ' — ' + address.shipping_address;
               }
               return address.shipping_address || location.join(', ');
         },
         clearResolvedLocation(clearSelection = true){
               this.resolverMatchType = '';
               this.resolverCandidates = [];
               this.selectedResolverCandidateIndex = '';
               this.resolvedLocation = null;
               this.resolvedShippingUnion = null;
               this.resolvedCityId = null;
               this.resolvedZoneId = null;
               this.resolverError = false;
                if(clearSelection){
                  this.resolvedShippingDistrict = null;
                  this.resolvedShippingThana = null;
                  this.upazilas.splice(0, this.upazilas.length);
                  jQuery('#district0, #upazila0').val('');
               }
         },
         onShippingAddressInput(){
               if(this.resolverDebounceTimer){
                  clearTimeout(this.resolverDebounceTimer);
               }

               this.resolverRequestSequence++;
               this.resolverLoading = false;
               this.resolverLastRequestedAddress = '';
               this.clearResolvedLocation(!this.manualLocationOverride);

               if(this.shippingAddressInput.trim().length > 0){
                  this.resolverDebounceTimer = setTimeout(() => {
                     this.resolveAddressLocation();
                  }, 600);
               }
         },
         resolveAddressOnBlur(){
               if(this.resolverDebounceTimer){
                  clearTimeout(this.resolverDebounceTimer);
                  this.resolverDebounceTimer = null;
               }

               if(this.shippingAddressInput.trim().length > 0 && !this.resolvedLocation){
                  this.resolveAddressLocation();
               }
         },
         resolveAddressLocation(){
               const address = this.shippingAddressInput.trim();

               if(address.length === 0){
                  this.clearResolvedLocation(!this.manualLocationOverride);
                  this.resolverMatchType = address.length ? 'none' : '';
                  return;
               }

               if(this.resolverLoading && this.resolverLastRequestedAddress === address){
                  return;
               }

               const requestId = ++this.resolverRequestSequence;
               const canAutoSelect = !this.manualLocationOverride;
               this.resolverLoading = true;
               this.resolverError = false;
               this.resolverLastRequestedAddress = address;

               axios.post(this.$baseUrl+'/api/v1/resolve-address-location', {
                  address: address
               }).then(response => {
                  if(requestId !== this.resolverRequestSequence || address !== this.shippingAddressInput.trim()){
                     return;
                  }

                  const data = response.data || {};
                  this.clearResolvedLocation(false);
                  this.resolverCandidates = Array.isArray(data.candidates) ? data.candidates : [];
                  this.resolverMatchType = data.match_type || 'none';

                  if(canAutoSelect && !this.manualLocationOverride && this.resolverMatchType === 'strong' && this.resolverCandidates.length){
                     return this.applyResolvedCandidate(this.resolverCandidates[0], requestId);
                  }else if(canAutoSelect && !this.manualLocationOverride && this.resolverMatchType === 'district_only' && data.district){
                     return this.applyDistrictOnly(data.district, requestId);
                  }else if(canAutoSelect && !this.manualLocationOverride && this.resolverMatchType === 'ambiguous'){
                     return this.applySafeAmbiguousDistrict(requestId);
                  }
               }).catch(() => {
                  if(requestId !== this.resolverRequestSequence){
                     return;
                  }

                  this.clearResolvedLocation(!this.manualLocationOverride);
                  this.resolverError = true;
               }).then(() => {
                  if(requestId === this.resolverRequestSequence){
                     this.resolverLoading = false;
                  }
               });
         },
         async loadAddressDistricts(){
               const response = await axios.get(this.$baseUrl + '/api/v1/get-district/0');
               this.districts = Array.isArray(response.data) ? response.data : [];
         },
         async loadAddressUnions(upazilaId){
               const response = await axios.get(this.$baseUrl + '/api/v1/get-union/' + upazilaId);
               const unions = Array.isArray(response.data) ? response.data : [];
               if(String(this.resolvedShippingThana) !== String(upazilaId)){
                  return [];
               }
               this.unions = unions;
               await this.$nextTick();
               const unionSelect = document.getElementById('union0');
               if(unionSelect){
                  const renderedIds = Array.from(unionSelect.options)
                     .slice(1)
                     .map(option => String(option.value));
                  const optionsMissing = renderedIds.length !== unions.length
                     || unions.some(union => !renderedIds.includes(String(union.id)));

                  if(optionsMissing){
                     while(unionSelect.options.length > 1){
                        unionSelect.remove(1);
                     }
                     const options = document.createDocumentFragment();
                     unions.forEach(union => {
                        const option = document.createElement('option');
                        option.value = union.id;
                        option.textContent = union.title;
                        options.appendChild(option);
                     });
                     unionSelect.appendChild(options);
                  }
               }
               return unions;
         },
         async applyResolvedCandidate(candidate, resolverRequestId){
               if(resolverRequestId !== this.resolverRequestSequence || this.manualLocationOverride){
                  return;
               }
               this.resolvedLocation = candidate;
               if(!Array.isArray(this.districts) || !this.districts.length){
                  await this.loadAddressDistricts();
               }
               if(resolverRequestId !== this.resolverRequestSequence || this.manualLocationOverride){
                  return;
               }
               const matchedDistrict = this.districts.find(district => String(district.id) === String(candidate.district_id));
               if(!matchedDistrict){
                  return;
               }
               this.resolvedShippingDistrict = matchedDistrict.id;
               this.resolvedShippingThana = null;
               this.resolvedShippingUnion = null;
               this.resolvedCityId = candidate.city_id;
               this.resolvedZoneId = candidate.zone_id;
               await this.$nextTick();
               jQuery('#district0').val(String(matchedDistrict.id));
               await this.getUpazila(matchedDistrict.id);
               await this.$nextTick();
               if(resolverRequestId === this.resolverRequestSequence && !this.manualLocationOverride){
                  const matchedUpazila = this.upazilas.find(upazila => String(upazila.id) === String(candidate.upazila_id));
                  this.resolvedShippingThana = matchedUpazila ? matchedUpazila.id : null;
                  await this.$nextTick();
                  const upazilaSelect = document.getElementById('upazila0');
                  const matchedOption = matchedUpazila && upazilaSelect
                     ? Array.from(upazilaSelect.options).find(option => String(option.value) === String(matchedUpazila.id))
                     : null;
                  if(!matchedOption){
                     this.resolvedShippingThana = null;
                  }
                  if(upazilaSelect){
                     upazilaSelect.value = matchedOption ? matchedOption.value : '';
                  }
                  this.customerWarningSelectionRevision++;
                  if(matchedOption){
                     const unions = await this.loadAddressUnions(matchedUpazila.id);
                     if(
                        resolverRequestId === this.resolverRequestSequence
                        && !this.manualLocationOverride
                        && String(this.resolvedShippingThana) === String(matchedUpazila.id)
                     ){
                        const matchedUnion = candidate.matched_area_id
                           ? unions.find(union => String(union.id) === String(candidate.matched_area_id))
                           : null;
                        this.resolvedShippingUnion = matchedUnion ? matchedUnion.id : null;
                        await this.$nextTick();
                        const unionSelect = document.getElementById('union0');
                        const matchedUnionOption = matchedUnion && unionSelect
                           ? Array.from(unionSelect.options).find(option => String(option.value) === String(matchedUnion.id))
                           : null;
                        if(!matchedUnionOption){
                           this.resolvedShippingUnion = null;
                        }
                        if(unionSelect){
                           unionSelect.value = matchedUnionOption ? matchedUnionOption.value : '';
                        }
                        this.customerWarningSelectionRevision++;
                     }
                  }
               }
         },
         async applySafeAmbiguousDistrict(resolverRequestId){
               const districtIds = Array.from(new Set(this.resolverCandidates.map(candidate => String(candidate.district_id))));
               if(districtIds.length !== 1){
                  return;
               }
               const candidate = this.resolverCandidates[0];
               this.resolvedShippingDistrict = candidate.district_id;
               this.resolvedShippingThana = null;
               await this.$nextTick();
               jQuery('#district0').val(String(candidate.district_id));
               await this.getUpazila(candidate.district_id);
               if(resolverRequestId !== this.resolverRequestSequence || this.manualLocationOverride){
                  return;
               }
         },
         async applyDistrictOnly(district, resolverRequestId){
               this.resolvedShippingDistrict = district.district_id;
               this.resolvedShippingThana = null;
               this.resolvedCityId = district.city_id;
               this.resolvedZoneId = null;
               await this.$nextTick();
               jQuery('#district0').val(String(district.district_id));
               await this.getUpazila(district.district_id);
               if(resolverRequestId !== this.resolverRequestSequence || this.manualLocationOverride){
                  return;
               }
         },
         async onManualDistrictChange(event){
               this.resolvedShippingDistrict = event.target.value || null;
               this.manualLocationOverride = true;
               this.resolverRequestSequence++;
               this.clearResolvedLocation(false);
               this.resolvedShippingThana = null;
               this.upazilas.splice(0, this.upazilas.length);
               await this.getUpazila(this.resolvedShippingDistrict);
         },
         async onManualThanaChange(event){
               this.resolvedShippingThana = event.target.value || null;
               this.resolvedShippingUnion = null;
               this.unions = [];
               const unionSelect = document.getElementById('union0');
               if(unionSelect){
                  while(unionSelect.options.length > 1){
                     unionSelect.remove(1);
                  }
                  unionSelect.value = '';
               }
               this.manualLocationOverride = true;
               this.resolverRequestSequence++;
               this.resolvedLocation = null;
               this.resolverMatchType = '';
               this.resolvedCityId = null;
               this.resolvedZoneId = null;
               if(this.resolvedShippingThana){
                  await this.loadAddressUnions(this.resolvedShippingThana);
               }
         },
         onManualUnionChange(event){
               const unionId = event.target.value || null;
               const matchedUnion = (Array.isArray(this.unions) ? this.unions : [])
                  .find(union => String(union.id) === String(unionId));
               this.resolvedShippingUnion = matchedUnion ? matchedUnion.id : null;
         },
         addNewAddress(){
               if(this.cartContainsGrocery){
                  swal("Please check", "Please use a detailed saved address with an area for grocery delivery.", "error");
                  return;
               }

               if(!this.resolvedShippingDistrict){
                  swal("Please check", "ডেলিভারি চার্জ নির্ধারণের জন্য জেলা নির্বাচন করুন।", "error");
                  return;
               }

               if(!this.resolvedShippingThana){
                  swal("Please check", "ডেলিভারি চার্জ নির্ধারণের জন্য থানা/এরিয়া নির্বাচন করুন।", "error");
                  return;
               }

               let token = localStorage.getItem("token");
               let axiosConfig = {
                   headers: {
                       'Content-Type': 'application/json;charset=UTF-8',
                       "Access-Control-Allow-Origin": "*",
                       'Authorization': 'Bearer '+token
                   }
               }
               let formData = new FormData();
               formData.append('shipping_first_name', $('.shipping_first_name0').val());
               formData.append('shipping_phone', $('.popup_phone0').val());
               formData.append('shipping_address', this.shippingAddressInput);
               formData.append('shipping_district', this.resolvedShippingDistrict);
               formData.append('shipping_thana', this.resolvedShippingThana);
               if(this.resolvedShippingUnion){
                  formData.append('shipping_union', this.resolvedShippingUnion);
               }
   
               axios.post(this.$baseUrl+'/api/v1/add-new-address', formData, axiosConfig).then(response => {
                   if(response.data.status == 1){
                       swal({
                           title: 'New address added successfull.',
                           icon: "success",
                           timer: 3000
                        });
                        this.shippingAddressInput = '';
                        this.clearResolvedLocation();
                        this.manualLocationOverride = false;
                        this.$store.dispatch('loadedUser');
                       this.$store.dispatch('loadedCart');
                       jQuery('a[href="#home"]').trigger('click');
                   }else{
                       this.errors = response.data.message;
                   }
               });
           },
           change_address(address_id,pickpoint){
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
   			formData.append('pickpoint', pickpoint);

               axios.post(this.$baseUrl+'/api/v1/update-default-address', formData, axiosConfig).then(response => {
   				if(response.data.status == 1){
                       this.$store.dispatch('loadedCart');
                       this.$store.dispatch('loadedUser');
                       this.$store.dispatch('loadedCart');
                       jQuery('.close').trigger('click');   
                  }else{
                       swal ( "Please check" ,  response.data.message,  "error");
                   }
   			});
           },
       
   
   
           async getDistrict(){
               let id =  jQuery('#division0').find('option:selected').val();
               await axios.get(this.$baseUrl + "/api/v1/get-district/"+id).then((response) => {
                   this.upazilas.splice(0, this.upazilas.length);
                   this.unions = {};
                   this.districts = response.data;
               });
           },
           
           async getUpazila(districtId = null){
               const id = districtId || jQuery('#district0').find('option:selected').val();
               const response = await axios.get(this.$baseUrl + "/api/v1/get-upazila/"+id);
               this.unions = {};
               const upazilas = Array.isArray(response.data) ? response.data : [];
               this.upazilas.splice(0, this.upazilas.length, ...upazilas);
               await this.$nextTick();
               const upazilaSelect = document.getElementById('upazila0');
               if(this.upazilas.length && upazilaSelect){
                  const renderedIds = Array.from(upazilaSelect.options)
                     .slice(1)
                     .map(option => String(option.value));
                  const optionsMissing = renderedIds.length !== this.upazilas.length
                     || this.upazilas.some(upazila => !renderedIds.includes(String(upazila.id)));

                  if(optionsMissing){
                     while(upazilaSelect.options.length > 1){
                        upazilaSelect.remove(1);
                     }
                     const options = document.createDocumentFragment();
                     this.upazilas.forEach(upazila => {
                        const option = document.createElement('option');
                        option.value = upazila.id;
                        option.textContent = upazila.title;
                        options.appendChild(option);
                     });
                     upazilaSelect.appendChild(options);
                  }
               }
               if(upazilaSelect && !this.resolvedShippingThana){
                  upazilaSelect.value = '';
               }
               return this.upazilas;
            },
            async getUnion(){
               let id =  jQuery('#upazila0').find('option:selected').val();
               await axios.get(this.$baseUrl + "/api/v1/get-union/"+id).then((response) => {
                  this.unions = response.data;
               });
           },
   
   
   		applyCouponCode(){
               let token = localStorage.getItem("token");
   			let axiosConfig = {
   				headers: {
   					'Content-Type': 'application/json;charset=UTF-8',
   					"Access-Control-Allow-Origin": "*",
   					'Authorization': 'Bearer '+token
   				}
   			}
   
   			let formData = new FormData();
			formData.append('coupon', $('#legacyCartCouponCode').val());
               axios.post(this.$baseUrl+'/api/v1/get-coupon-amount', formData,axiosConfig).then(response => {
                   if(response.data.status == 1){
                       this.coupon_discount = response.data;
                       jQuery('.coupon_discount').attr('data-coupon-discount',response.data.amount);
                        
                       this.legacyCouponExpanded = false;
                       jQuery('.coupon_discount').show();
                       this.calculateFinalAmount();
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

         save_search_content(){
            let that = this;
            var typingTimer;
            var doneTypingInterval = 5000; 
            $('#myInput').keyup(function(){
               clearTimeout(typingTimer);
               if ($('#myInput').val()) {
                  typingTimer = setTimeout(doneTyping, doneTypingInterval);
               }
            });
            function doneTyping(){
               let token = localStorage.getItem("token");
               let axiosConfig = {
                  headers: {
                     'Content-Type': 'application/json;charset=UTF-8',
                     "Access-Control-Allow-Origin": "*",
                     'Authorization': 'Bearer '+token
                  }
               }
               let session_key = localStorage.getItem("session_key");
               let formData = new FormData();
               formData.append('searchContent', $('#myInput').val());
               formData.append('session_key', session_key);
               let inpt =  $('#myInput').val();
               if(inpt.length > 2){
                  axios.post(that.$baseUrl+'/api/v1/save-what-user-search', formData, axiosConfig).then(response =>{
                     console.log(response.data);
                  });
               }
            }
         },

         removeCoupon(){
               this.legacyCouponExpanded = false;
               jQuery('.coupon_discount').hide();
               jQuery('.coupon_discount').attr('data-coupon-discount',0)
               let that = this;
               setTimeout(function(){ 
                   that.calculateFinalAmount();
                },200);
           },

         showCheckoutSection(){
            if(!this.logged_in_user || !this.logged_in_user.id){
                jQuery('.cart_close').trigger('click');
                this.$router.push({name: 'checkout'});
                return;
            }
            this.calculateFinalAmount();
            this.$store.dispatch('loadedVoucher');
            this.$store.dispatch('loadedUsableVoucher');
         },

         requestForQuatation(){
            jQuery(this).attr('disabled');
   			let token = localStorage.getItem("token");
   			let axiosConfig = {
   				headers: {
   					'Content-Type': 'application/json;charset=UTF-8',
   					"Access-Control-Allow-Origin": "*",
   					'Authorization': 'Bearer '+token
   				}
   			}
   			axios.post(this.$baseUrl+'/api/v1/request-for-quatation','',axiosConfig).then(response =>{
   				if(response.data.status == '1'){
   					this.$store.dispatch('loadedCart');
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
   
     },

     updated(){
         $('.reload_calculation').trigger('click');
      },
   
     computed:{
         selectedAddressDistrictTitle(){
            this.customerWarningSelectionRevision;
            if(typeof document !== 'undefined'){
               const select = document.getElementById('district0');
               if(select && select.value && select.selectedOptions.length){
                  return select.selectedOptions[0].textContent.trim();
               }
            }
            const district = (Array.isArray(this.districts) ? this.districts : [])
               .find(item => String(item.id) === String(this.resolvedShippingDistrict));
            if(district){ return district.title; }
            return '';
         },
         selectedAddressThanaTitle(){
            this.customerWarningSelectionRevision;
            if(typeof document !== 'undefined'){
               const select = document.getElementById('upazila0');
               if(select && select.value && select.selectedOptions.length){
                  return select.selectedOptions[0].textContent.trim();
               }
            }
            const upazila = (Array.isArray(this.upazilas) ? this.upazilas : [])
               .find(item => String(item.id) === String(this.resolvedShippingThana));
            if(upazila){ return upazila.title; }
            return '';
         },
         selectedAddressUnionTitle(){
            this.customerWarningSelectionRevision;
            if(typeof document !== 'undefined'){
               const select = document.getElementById('union0');
               if(select && select.value && select.selectedOptions.length){
                  return select.selectedOptions[0].textContent.trim();
               }
            }
            const union = (Array.isArray(this.unions) ? this.unions : [])
               .find(item => String(item.id) === String(this.resolvedShippingUnion));
            if(union){ return union.title; }
            return '';
         },
         hasSelectedAddressLocation(){
            this.customerWarningSelectionRevision;
            const districtSelect = typeof document !== 'undefined' ? document.getElementById('district0') : null;
            const upazilaSelect = typeof document !== 'undefined' ? document.getElementById('upazila0') : null;
            const districtId = this.resolvedShippingDistrict || (districtSelect ? districtSelect.value : null);
            const upazilaId = this.resolvedShippingThana || (upazilaSelect ? upazilaSelect.value : null);
            return Boolean(districtId && upazilaId);
         },
         showShippingAddressWarning(){
            return this.hasSelectedAddressLocation && !this.hasUsefulAddressDetail;
         },
         hasUsefulAddressDetail(){
            if(!this.hasSelectedAddressLocation){ return false; }
            let residual = this.shippingAddressInput.toLowerCase()
               .replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit))
               .replace(/[,.;:|/\\_\-()]+/g, ' ')
               .replace(/\s+/g, ' ').trim();
            const detailTerms = ['village','union','area','road','street','lane','house','holding','office','building','shop','market','mosque','school','college','institution','sector','block','floor','flat','para','moholla','গ্রাম','ইউনিয়ন','এলাকা','রোড','সড়ক','গলি','বাড়ি','বাসা','হোল্ডিং','অফিস','ভবন','দোকান','মার্কেট','বাজার','মসজিদ','স্কুল','কলেজ','প্রতিষ্ঠান','সেক্টর','ব্লক','তলা','ফ্লোর','ফ্ল্যাট','পাড়া','মহল্লা'];
            const districtEquivalents = {
               '3': ['barguna', 'বরগুনা'],
               '14': ['dhaka', 'ঢাকা'],
               '19': ['gazipur', 'গাজীপুর']
            };
            const thanaEquivalents = {
               '67': ['keraniganj', 'কেরানীগঞ্জ'],
               '81': ['mirpur 10', 'mirpur 11', 'মিরপুর ১০', 'মিরপুর ১১'],
               '94': ['mohammadpur', 'মোহাম্মদপুর'],
               '122': ['savar', 'সাভার'],
               '142': ['turag', 'তুরাগ', 'dharangartek', 'dharangertek', 'ধরঙ্গারটেক'],
               '181': ['amtali', 'amtoli', 'amtoly', 'আমতলী'],
               '371': ['tongi', 'টঙ্গি', 'টঙ্গী'],
               '562': ['mohammadpur', 'মোহাম্মদপুর']
            };
            const structuredEquivalents = this.resolvedLocation && Array.isArray(this.resolvedLocation.location_evidence)
               ? this.resolvedLocation.location_evidence
               : [];
            const equivalents = [this.selectedAddressDistrictTitle, this.selectedAddressThanaTitle, this.selectedAddressUnionTitle]
               .concat(districtEquivalents[String(this.resolvedShippingDistrict)] || [])
               .concat(thanaEquivalents[String(this.resolvedShippingThana)] || [])
               .concat(this.resolvedLocation && this.resolvedLocation.matched_area_title ? [this.resolvedLocation.matched_area_title] : [])
               .concat(structuredEquivalents)
               .filter(Boolean)
               .map(value => String(value).toLowerCase()
                  .replace(/[০-৯]/g, digit => '০১২৩৪৫৬৭৮৯'.indexOf(digit))
                  .replace(/[,.;:|/\\_\-()]+/g, ' ').replace(/\s+/g, ' ').trim())
               .sort((left, right) => right.length - left.length);
            equivalents.forEach(equivalent => {
               const escaped = equivalent.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '\\s+');
               residual = residual.replace(new RegExp('(^|\\s)' + escaped + '(?=\\s|$)', 'gu'), ' ');
            });
            residual = residual.replace(/\s+/g, ' ').trim();
            const addressTokens = residual.split(/\s+/).filter(Boolean);
            if(detailTerms.some(term => addressTokens.indexOf(term) !== -1)){
               return true;
            }
            return /[0-9০-৯]/u.test(residual);
         },
         compreVuex(){
            return this.$store.getters.getLoadedCompare;
         },
         wishlistVuex(){
            return this.$store.getters.getLoadedWishlist;
         },
         user(){
            return this.$store.getters.getLoadedUser.user;
         },
         logged_in_user(){
            return this.$store.getters.getLoadedUser.user;
         },
         notificationsData(){
            return this.$store.getters.getLoadedNotifications;
         },
         collectedVoucher(){
               return this.$store.getters.getLoadedVocher;
         },
         useableVouchers(){
               return this.$store.getters.getLoadedUseableVocher;
         },
      
         cartData(){
            const cart = this.$store.getters.getLoadedCart;
            if(!cart){
               this.sub_total = '';
               this.finalCalculatedTotal = 0;
               return {};
            }
            this.sub_total = cart.sub_total;
            this.finalCalculatedTotal = this.sub_total + cart.shipping_cost;
            return cart;
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
         pickpoint_address(){
               let x = this.$store.getters.getLoadedUser.pickpoints;
               let res = 0;
               if(x != undefined){
                  if(x.length != 0){
                        res = this.$store.getters.getLoadedUser.pickpoints;
                  }
               }
               return res;
         },
         selected_pickpoint(){
            let x = this.$store.getters.getLoadedUser.selected_pickpoint;
               let res = 0;
               if(x != undefined){
                  if(x.length != 0){
                        res = this.$store.getters.getLoadedUser.selected_pickpoint;
                  }
               }
               return res;
          },
         cartContainsGrocery(){
               const loadedCart = this.$store.getters.getLoadedCart;
               const groups = loadedCart && Array.isArray(loadedCart.cart) ? loadedCart.cart : [];

               return groups.some(group => {
                  const items = Array.isArray(group.items) ? group.items : [];
                  return items.some(item => {
                     return item.product && item.product.is_grocery === 'grocery';
                  });
               });
         },
      },
      beforeDestroy(){
         if(this.resolverDebounceTimer){
            clearTimeout(this.resolverDebounceTimer);
         }
         this.resolverRequestSequence++;
      },
      watch:{
       $route(to, from){
         $('.search_suggest_wrapper').hide();
         $('.mobile_search_suggest_wrapper').hide();

         this.scrollToTop();
         $('#php').val();
         $('#password').val();
         if(this.$route.name == 'home'){
           $('.nav_wrapper').show();
         }else{
           $('.nav_wrapper').hide();
         }
         
      }
       
     },
     mounted(){
      // var upazail_id = localStorage.getItem("upazail_id");
      // if(!upazail_id || upazail_id == 'null'){
      //    window.onload = function () {
      //       OpenBootstrapPopup();
      //    };
      //    function OpenBootstrapPopup() {
      //       $("#location_modal").modal('show');
      //    }
      // }
      this.getLocationDistrict(); 
      this.loadAddressDistricts();

      this.affiliate_referer = localStorage.getItem("affiliate_referer") ?? '';
      this.$store.dispatch('loadedNotifications');
      this.$store.dispatch('loadedWishlist');
      this.save_search_content();
      this.scrollToTop();
      const plugin = document.createElement("script");
      plugin.setAttribute( "src",this.$frontendUrl+"/assets/js/custom.js");
      plugin.async = true;
      document.body.appendChild(plugin);
      this.$i18n.locale = localStorage.getItem("lang");
      if(localStorage.getItem("lang") == 'bn'){
         $( ".lang_selector" ).prop( "checked", true );
         $('body').addClass('bangla');
      }
      this.site_information();
      this.load_categories();
      this.baseurl = this.$baseUrl;
      this.load_promotional_offer_title();
      this.loading_method();
      this.load_static_pages();

     },
   }
</script>

<style scoped>
@media (max-width: 767px) {
   .legacy-mobile-checkout {
      display: flex;
      flex-direction: column;
      min-width: 0;
      padding: 14px 14px 104px;
      overflow-x: hidden;
      background: #f5f8f8;
   }
   .legacy-mobile-checkout > .note { order: 3; }
   .legacy-mobile-checkout > .collect_voucher_modal,
   .legacy-mobile-checkout > .voucher_button { order: 3; }
   .legacy-mobile-checkout > .payment-calculation { order: 4; }
   .legacy-mobile-checkout > .paymentmethod { order: 5; }
   .legacy-mobile-checkout > .procced-checkout { order: 6; }
   .legacy-mobile-checkout > .legacy-cart-coupon { order: 7; }
   .legacy-mobile-checkout .address_details,
   .legacy-mobile-checkout .address_details_alt,
   .legacy-mobile-checkout .note,
   .legacy-mobile-checkout .payment-calculation,
   .legacy-mobile-checkout .paymentmethod,
   .legacy-mobile-checkout .collect_voucher_modal,
   .legacy-mobile-checkout .voucher_button {
      width: 100%;
      min-width: 0;
      margin-top: 12px !important;
      padding: 15px;
      border: 1px solid #e1e9e8;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 5px 18px rgba(28, 66, 63, .05);
      overflow-wrap: anywhere;
   }
   .legacy-mobile-checkout .legacy-cart-coupon {
      width: 100%;
      min-width: 0;
      margin: 12px 0 24px;
      padding: 14px 15px;
      border: 1px solid #e1e9e8;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 5px 18px rgba(28, 66, 63, .05);
      overflow: visible;
   }
   .legacy-mobile-checkout .legacy-cart-coupon-toggle {
      display: block;
      width: 100%;
      padding: 0;
      border: 0;
      color: #0f8f87;
      background: transparent;
      text-align: left;
   }
   .legacy-mobile-checkout .legacy-cart-coupon .input-group { flex-wrap: nowrap; }
   .legacy-mobile-checkout .legacy-cart-coupon .form-control { min-width: 0; }
   .legacy-mobile-checkout .cart_summary_title {
      margin: 0 0 11px !important;
      padding: 0;
      border: 0;
      font-size: 16px;
      line-height: 1.35;
      text-decoration: none;
   }
   .legacy-mobile-checkout textarea,
   .legacy-mobile-checkout input,
   .legacy-mobile-checkout .input-group {
      max-width: 100%;
      min-width: 0;
   }
    .legacy-mobile-checkout .paymentmethod .list-group {
       display: flex;
       flex-direction: row;
       gap: 10px;
    }
    .legacy-mobile-checkout .paymentmethod .list-group-item {
       flex: 1 1 50%;
       min-width: 0;
       text-align: center;
    }
    .legacy-mobile-checkout .paymentmethod img {
       max-width: 100%;
       height: auto;
    }
   .legacy-mobile-checkout .procced-checkout {
      width: 100%;
      margin: 14px 0 0 !important;
      padding: 0;
      position: static !important;
      transform: none;
      float: none !important;
      clear: both;
   }
   .legacy-mobile-checkout .procced-checkout ul,
   .legacy-mobile-checkout .procced-checkout li {
      width: 100%;
      margin: 0;
      padding: 0;
   }
   .legacy-mobile-checkout .back_to_cartbtn { display: none; }
   .legacy-mobile-checkout .proceed_to_pay {
      position: static !important;
      inset: auto !important;
      transform: none;
      float: none !important;
      display: block;
      box-sizing: border-box;
      width: 100%;
      max-width: 100%;
      min-height: 48px;
      margin: 0 auto 4px;
      border-radius: 10px;
   }
}
</style>
