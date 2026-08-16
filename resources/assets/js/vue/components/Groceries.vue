<template>
<div>


<!--groceries slider start-->
<span v-if="loading1">


<section id="client_section">
    <div class="container text-center my-3 p-0">

        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                    <div v-for="(deals, index) in flashDeals" :key="index"  class="swiper-slide" data-swiper-autoplay="5000">
                        <router-link :to="{ name: 'flashdeal', params: {slug: deals.slug } }">
                            <div class="card card-body">
                                <img class="img-fluid" :src="baseurl+'/'+deals.banner" alt="img">
                                <div class="client_title">
                                    <span> <b>{{ deals.title }}</b> </span>
                                    <span class="groceries-fa-chevron-right"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                                </div>
                            </div>
                        </router-link>
                    </div>

            </div>
            <div class="swiper-pagination"></div>
        </div>
        
    </div>
</section>



</span>

<span v-else>
<div class="container mb_3 mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="shimmer">  <div class="h_30 w_100per mb_3"></div> </div>
        </div>
        <div class="col-md-4">
            <div class="shimmer">  <div class="h_30 w_100per mb_3"></div> </div>
        </div>
        <div class="col-md-4 p-0">
            <div class="shimmer">  <div class="h_30 w_100per mb_3"></div> </div>
        </div>
    </div>
</div>
</span>
<!--groceries slider end-->




<!--groceries_add1 start-->
<span v-if="adsLoading">
<section id="groceries_add1">
    <div class="container">
        <div class="row mt-3 mb-3">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <div v-if="ad1.grocery_ad_banner_1_link_type == 'internal_url'">
                <router-link :to="ad1.grocery_ad_banner_1_link">
                    <img @error="imageLoadError" :src="baseurl+'/'+ad1.grocery_ad_banner_1" alt="img">
                </router-link>
            </div>
            <div v-else class="app-banner mt-1">
                <a target="_blank" :href="ad1.grocery_ad_banner_1_link">
                    <img @error="imageLoadError" :src="baseurl+'/'+ad1.grocery_ad_banner_1" alt="img">
                </a>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
    </div>
</section>
</span>
<span v-else>
<div class="container mb_3">
    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <div class="shimmer">  <div class="h_5 w_100per mb_3"></div> </div>
        </div>
        <div class="col-md-4 p-0">  </div>
    </div>
</div>
</span>
<!--groceries_add1 end-->




<!--groceries_categories start-->
<span v-if="loading3">
<section id="groceries_categories">
    <div class="container">
        <div class="row mt-3 mb-3">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="title ">
                    <h2 class="text-center">Grocery Categories</h2>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
        <div class="row">
            <div class="col-6 col-sm-6 col-md-2 col-lg-2 text-center mb-3" v-for="(category1, index) in categories" :key="index">
                <div class="single_groceries_category">
                    <router-link :to="{name: 'category', params: {slug: category1.slug } }">
                        <img @error="imageLoadError"  :src="baseurl+'/'+category1.image"  alt="">
                        <b>{{ category1.title }}</b>
                    </router-link>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <div class="moreBtn">
                    <!-- <div class="btn btn-primary lessMore lessBtn" v-if="prev_page_url" @click.prevent="lessCategoris(prev_page_url)">{{ $t('Less') }}<i class="fa fa-chevron-up" aria-hidden="true"></i></div> -->
                    <div class="btn btn-primary lessMore morebutn" v-if="next_page_url" @click.prevent="moreCategoris(next_page_url)">{{ $t('More') }}<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>
</span>
<span v-else>
<div class="container mb_3">
    <div class="row mb_2">
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
    </div>
</div>
</span>
<!--groceries_categories end-->





<!-- flash sale start -->

<span v-if="product_todays_deal_total > 0">
<span v-if="countloading">
<section class="mymultipleslider products_shop flashsale_section mb-4 daily_deals">
  <div class="product-main flashsale_bg">
    <div class="container pb-3 pt-3">

        <div class="row countDown_section mb-3 border-8">
          <div class="col-md-4 col-lg-4 pl-1">
            <div class="title grocery_title">
              <h3 style="border-left: 7px solid #ffffff;">{{ $t("Today's Deals") }}</h3>
            </div>
          </div>
          <div class="col-md-4 col-lg-4">
            <div id="app-timer" class="container-fluid m-auto text-center">
            <div class="row">
               
                <div class="inding"> <b>  {{ $t('Ending in') }} :</b></div>
                    <Timeitem v-for="(times, index) in times" :key="index"  v-bind:time="times"></Timeitem>
                </div>
               
            </div>
          </div>
          
          <div class="col-md-4 col-lg-4 carousel_btn_col">
            <div class="carousel_btn">
              <button type="button" class="btn btn-default leftlst"> <i class="fa fa-chevron-left" aria-hidden="true"></i></button>
              <button type="button" class="btn btn-default flash_widht rightlst" data-mrMinus='0'  data-carouseWith='0'> <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
            </div>
          </div>
        </div>

      <div class="row">
        <div class="col-md-12 caro_parent count_section">
          <div class="rescarousel-inner setWidth flash_slider" id="grocery_deals_slider">
            <div class="item" v-for="(data, index) in product_todays_deal.items" :key="data.id" :data-length="index">
                <ProductGrid :data="data" ></ProductGrid>
                <QuickView :data="data" ></QuickView>
            </div>
          </div>
        </div>
      </div>

       <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'promotion', params: {product_type: 'flashsale-products'} }">{{ $t('Shop more') }}<i class="fa fa-chevron-right" aria-hidden="true"></i></router-link></p>
    </div>
  </div>
</section>
</span>


<span v-else>
<div class="container mb_3">

    <div class="row mb_2">
        <div class="col-6 col-sm-4 col-md-4 col-lg-4 text-left float-left"> <div class="shimmer">  <div class="h_5 w_10 mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-4 col-lg-4">  <div class="shimmer">  <div class="h_5 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-4 col-lg-4 text-right float-right"> <div class="shimmer">  <div class="h_5 w_10 mb_1"></div> </div> </div>
    </div>
    <div class="row mb_2">
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> </div>
        <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> </div>
    </div>

</div>
</span>
</span>
<!-- flash sale End -->






<!--3 grid start-->
<span v-if="adsLoading">
<section id="groceries_page">
    <div class="container">
        <div class="row groceries_three_banner">
            <div class="col-md-8">
                <div v-if="ad2.grocery_ad_banner_2_link_type == 'internal_url'">
                    <router-link :to="ad2.grocery_ad_banner_2_link">
                        <img @error="imageLoadError" :src="baseurl+'/'+ad2.grocery_ad_banner_2" alt="img">
                    </router-link>
                </div>
                <div v-else class="app-banner mt-1">
                    <a target="_blank" :href="ad2.grocery_ad_banner_2_link">
                        <img @error="imageLoadError" :src="baseurl+'/'+ad2.grocery_ad_banner_2" alt="img">
                    </a>
                </div>

            </div>
            <div class="col-md-4">
                <div class="col-md-12 mb-3 p-0">
                    <div v-if="ad3.grocery_ad_banner_3_link_type == 'internal_url'">
                        <router-link :to="ad3.grocery_ad_banner_3_link">
                            <img @error="imageLoadError" :src="baseurl+'/'+ad3.grocery_ad_banner_3" alt="img">
                        </router-link>
                    </div>
                    <div v-else class="app-banner mt-1">
                        <a target="_blank" :href="ad3.grocery_ad_banner_3_link">
                            <img @error="imageLoadError" :src="baseurl+'/'+ad3.grocery_ad_banner_3" alt="img">
                        </a>
                    </div>
                </div>
                <div class="col-md-12 p-0">
                    <div v-if="ad4.grocery_ad_banner_4_link_type == 'internal_url'">
                        <router-link :to="ad4.grocery_ad_banner_4_link">
                            <img @error="imageLoadError" :src="baseurl+'/'+ad4.grocery_ad_banner_4" alt="img">
                        </router-link>
                    </div>
                    <div v-else class="app-banner mt-1">
                        <a target="_blank" :href="ad4.grocery_ad_banner_4_link">
                            <img @error="imageLoadError" :src="baseurl+'/'+ad4.grocery_ad_banner_4" alt="img">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</span>


<span v-else>
<div class="container mb_3">
    <div class="row mb_2">
        <div class="col-12 col-sm-12 col-md-6 col-lg-6"> 
            <div class="shimmer">  <div class="h_50 w_100per mb_1"></div> </div> 
        </div>


        <div class="col-12 col-sm-12 col-md-6 col-lg-6">  
            <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> 
            <div class="shimmer">  <div class="h_25 w_100per mb_1"></div> </div> 
        </div>

    </div>
</div>
</span>
<!--3 grid end-->




<!--add 2 start-->
<section id="groceries_add2">
    <div class="container">
        <div class="row mt-3 mb-3">
            <div class="col-md-12">
                    <div v-if="ad5.grocery_ad_banner_5_link_type == 'internal_url'">
                        <router-link :to="ad5.grocery_ad_banner_5_link">
                            <img @error="imageLoadError" :src="baseurl+'/'+ad5.grocery_ad_banner_5" alt="img">
                        </router-link>
                    </div>
                    <div v-else class="app-banner mt-1">
                        <a target="_blank" :href="ad5.grocery_ad_banner_5_link">
                            <img @error="imageLoadError" :src="baseurl+'/'+ad5.grocery_ad_banner_5" alt="img">
                        </a>
                    </div>
            </div>
        </div>
    </div>
</section>
<!--add 2 end-->


<!--Products You May Like Start-->
<section class="jus_for_you">
    <div class="container">
        <div class="row p-0 mb-3">
          <div class="col-12 col-sm-12 col-md-12 col-lg-12 pl-2">
            <div class="title">
              <h3 class="todaydeals">Products You May Like</h3>
            </div>
          </div>
        </div>
        
        <span v-if="todayDeals">
        <div class="row search_products promotion p-0">
            <div v-for="(data, index) in todayDeals" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-2 mb-3">
              <ProductGrid :data="data" ></ProductGrid>
              <QuickView :data="data" ></QuickView>
              
            </div>
        </div>

        <div class="row product_pagination">
            <div class="col-md-12 text-center mb-3 mt-3">
                <div class="moreBtn">
                    <div class="btn btn-primary load_more_btn" v-if="allnext_page_url" @click.prevent="moreProducts(allnext_page_url)">{{ $t('Load More') }}<i class="fa fa-chevron-down ml-3" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
        </span>
        <span v-else>

            <span v-if="maylikeproducts > 0">
            <div class="container mb_3">
                <div class="row mb_2">
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                </div>
                <div class="row">
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                    <div class="col-6 col-sm-4 col-md-2 col-lg-2">  <div class="shimmer">  <div class="h_15 w_100per mb_1"></div> </div> </div>
                </div>
            </div>
            </span>
            <span v-else>
                <div class="row groccerymaylikeNotfound">
                    <div class="col-md-12">
                        <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                        <h4> {{ $t('Product Not Found') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
                    </div>
                </div>
            </span>




        </span>
    </div>
</section>
<!--Products You May Like End-->


<!--sponsored link start-->
<span v-if="adsLoading">
<section id="sponsored">
    <div class="container">
        <div class="row mt-3 mb-3">

            <div class="col-md-4">
                <div class="sponsored_single">
                    <div class="single_img">
                        <img @error="imageLoadError" :src="baseurl+'/'+sponsored_1.grocery_ad_banner_6" alt="">
                    </div>
                    <div class="sponsored_single_details">
                        <div class="row">
                            <div class="col-md-9">
                                <h4>{{ sponsored_1.grocery_ad_banner_6_title }}</h4>
                                <p>{{ sponsored_1.grocery_ad_banner_6_details }}</p>
                            </div>
                            <div class="col-md-3 text-right sponsored_small_img"> 
                                <img @error="imageLoadError" :src="baseurl+'/'+sponsored_1.grocery_ad_banner_6_sponsor_by"  alt="">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-9"><a :href="sponsored_1.grocery_ad_banner_6_link"><b>Shop Now</b></a> </div>
                            <div class="col-md-3 text-right"> <span>Sponsored</span> </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="sponsored_single">
                    <div class="single_img">
                        <img @error="imageLoadError" :src="baseurl+'/'+sponsored_2.grocery_ad_banner_7" alt="">
                    </div>
                    <div class="sponsored_single_details">
                        <div class="row">
                            <div class="col-md-9">
                                <h4>{{ sponsored_2.grocery_ad_banner_7_title }}</h4>
                                <p>{{ sponsored_2.grocery_ad_banner_7_details }}</p>
                            </div>
                            <div class="col-md-3 text-right sponsored_small_img"> 
                                <img @error="imageLoadError" :src="baseurl+'/'+sponsored_2.grocery_ad_banner_7_sponsor_by"  alt="">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-9"><a :href="sponsored_2.grocery_ad_banner_7_link"><b>Shop Now</b></a> </div>
                            <div class="col-md-3 text-right"> <span>Sponsored</span> </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="sponsored_single">
                    <div class="single_img">
                        <img @error="imageLoadError" :src="baseurl+'/'+sponsored_3.grocery_ad_banner_8" alt="">
                    </div>
                    <div class="sponsored_single_details">
                        <div class="row">
                            <div class="col-md-9">
                                <h4>{{ sponsored_3.grocery_ad_banner_8_title }}</h4>
                                <p>{{ sponsored_3.grocery_ad_banner_8_details }}</p>
                            </div>
                            <div class="col-md-3 text-right sponsored_small_img"> 
                                <img @error="imageLoadError" :src="baseurl+'/'+sponsored_3.grocery_ad_banner_8_sponsor_by"  alt="">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-9"><a :href="sponsored_3.grocery_ad_banner_8_link"><b>Shop Now</b></a> </div>
                            <div class="col-md-3 text-right"> <span>Sponsored</span> </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>

</span>
<span v-else>
<div class="container mb_3">
    <div class="row mb_2">
        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> 
            <div class="shimmer">  <div class="h_40 w_100per mb_1"></div> </div> 
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> 
            <div class="shimmer">  <div class="h_40 w_100per mb_1"></div> </div> 
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> 
            <div class="shimmer">  <div class="h_40 w_100per mb_1"></div> </div> 
        </div>
    </div>
</div>
</span>

<!--sponsored link end-->


<!--app link start-->
<span v-if="adsLoading">
<section id="family_section" class="newsletter">
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="newsText">
        <h1>Be a part of our family</h1>
        <ul class="app_icon">
          <li><img @error="imageLoadError" :src="baseurl+'/uploads/images/android3.png'" alt="img"></li>
          <li><img @error="imageLoadError" :src="baseurl+'/uploads/images/apple3.png'" alt="img"></li>
        </ul>
        <div class="paper_plan">
          <img :src="baseurl+'/uploads/images/cart_icon.png'" >
        </div>
      </div>
    </div>
    <div class="col-md-4">
        <div class="newsSign">
          <form>
            <div class="input-field">
              <label for="name">Name</label> <br>
              <input type="text"  class="app_name" name="name">
            </div>

            <div class="input-field">
              <label for="exampleInputEmail">Mobile*</label><br>
              <input type="email" name="email" class="app_link_input">
            </div>
            <div class="btn btn-primary app_link_btn" @click.prevent="app_link()">Get App Link</div>
          </form>
        </div>
    </div>
  </div>
</div>
</section>

</span>
<span v-else>
<div class="container mb_3">
    <div class="row mb_2">
        <div class="col-12 col-sm-12 col-md-8 col-lg-8"> 
            <div class="shimmer">  <div class="h_35 w_100per mb_1"></div> </div> 
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> 
            <div class="shimmer">  <div class="h_35 w_100per mb_1"></div> </div> 
        </div>
    </div>
</div>
</span>
<!--app link start-->



<!--Order Process start-->
<span v-if="adsLoading">


<!--Order Process start-->
<section id="order_process">
  <div class="container">
    <div class="row">
       <div class="col-md-4">
       <div class="title"> <h3>How to order from MabiY Shop ?</h3></div>
        <span v-html="how_to_order_from_website_text"></span>
       </div>
       <div class="col-md-8">
            
            <span v-if="maylikeLoading">
            <div id="demo" class="carousel slide" data-ride="carousel">
              <!-- Indicators -->
              <ul class="carousel-indicators order_process">
                <li data-target="#demo"  v-for="(slider, index) in how_to_order_from_website_images" :key="index" :class="['slider_dot', (index === 0 ? 'active' : '')]" :data-slide-to="index"></li>

              </ul>
 
              <!-- The slideshow -->
              <div class="carousel-inner">
                <div v-for="(slider, index) in how_to_order_from_website_images" :key="index" :class="['carousel-item', (index === 0 ? 'active' : '')]">
                  <img @error="imageLoadError" :src="baseurl+'/'+slider" alt="Place order">
                </div>



              </div>
            </div>
            </span>

            <span v-else>
              <div class="shimmer"><div class="h_30 w_100per"></div></div>
            </span>


       </div>
    </div>
  </div>
</section>
<!--Order Process End-->



</span>
<span v-else>
<div class="container mb_3">
    <div class="row mb_2">
        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> 
            <div class="shimmer">  <div class="h_35 w_100per mb_1"></div> </div> 
        </div>
        <div class="col-12 col-sm-12 col-md-8 col-lg-8"> 
            <div class="shimmer">  <div class="h_35 w_100per mb_1"></div> </div> 
        </div>
    </div>
</div>
</span>
<!--Order Process End-->


</div>
</template>

<script>
import Form from 'vform'
import axios from 'axios'
import swal from 'sweetalert';
import Leftsidebar from './default/Leftsidebar';
import moment from 'moment';
import pagination from 'laravel-vue-pagination';
import ProductGrid from './parts/ProductGrid';
import Timeitem from './default/Timeitem';
import QuickView from './parts/QuickView.vue';


export default {
    mode:'history',

	data(){
		return{
            how_to_order_from_website_text:'',
            how_to_order_from_website_images:'',
            maylikeLoading:'',
            adsLoading:false,
			is_user: '',
            flashDeals:'',
            loading:false,
            thumbnailUrl:'',
            baseurl:'',
            next_page_url: '',
            prev_page_url:'',
            categories:{},
            product_todays_deal:[],
            flashsaleActive:false,
            //startTime: new Date().toString().split(' ').splice(1,3).join(' '),
            startTime: moment(new Date()).format("MMM D, YYYY h:mm:ss"),
			endTime: 'Jan 23, 2090 4:01:00',
			times: [
			  { id: 0, text: "D", time: 1 },
			  { id: 1, text: "H", time: 1 },
			  { id: 2, text: "M", time: 1 },
			  { id: 3, text: "S", time: 1 }
			],
			progress: 100,
			timeinterval: undefined,
            todayDeals:'',
            todayDealsStatus:'',
            allnext_page_url:'',
            all:'',
            loading1:false,
            loading2:true,
            loading3:false,
            ad1:'',
            ad2:'',
            ad3:'',
            ad4:'',
            ad5:'',
            sponsored_1:'',
            sponsored_2:'',
            sponsored_3:'',
            countloading:false,
            product_todays_deal_total:'',
            maylikeproducts:1,

		}
	},
	components:{
    Leftsidebar,
    pagination,
    ProductGrid,
    Timeitem,
    QuickView
},
	methods:{
        load_promotional_banner(){
            axios.get(this.$baseUrl+'/api/v1/get-promotional-banner').then(response => {
                this.how_to_order_from_website_text = response.data.how_to_order_from_website.how_to_order_from_website_description;
                this.how_to_order_from_website_images = response.data.how_to_order_from_website.how_to_order_from_website_image;
                this.maylikeLoading = true;
            });
        },
		updateTimer: function() {
		  if (
			this.times[3].time > 0 ||
			this.times[2].time > 0 ||
			this.times[1].time > 0 ||
			this.times[0].time > 0
		  ) {
			this.getTimeRemaining();
			this.updateProgressBar();
		  } else {
			clearTimeout(this.timeinterval);
			  this.times[3].time = this.times[2].time = this.times[1].time = this.times[0].time = 0;
			 this.progress = 0;
		  }
		},
		getTimeRemaining: function() {
		  let t = Date.parse(new Date(this.endTime)) - Date.parse(new Date());
            if(t >= 0){
                this.times[3].time = Math.floor(t / 1000 % 60); //seconds
                this.times[2].time = Math.floor(t / 1000 / 60 % 60); //minutes
                this.times[1].time = Math.floor(t / (1000 * 60 * 60) % 24); //hours
                this.times[0].time = Math.floor(t / (1000 * 60 * 60 * 24)); //days
            }else {
                this.times[3].time = this.times[2].time = this.times[1].time = this.times[0].time = 0;
                this.progress = 0;
            }
		},
		updateProgressBar: function() {
            let startTime = Date.parse(new Date(this.startTime));
            let currentTime = Date.parse(new Date());
            let interval = parseFloat(
                (currentTime - startTime) / (this.endTime - startTime) * 100
            ).toFixed(2);
            this.progress = 100-interval;
            let x = new Date();
            let nows = moment(x).format("MMM D, YYYY h:mm:ss");
            let start = new Date(moment(x).format("MM/D/YYYY HH:mm:ss"));
            let end = new Date(moment(this.endTime).format("MM/D/YYYY HH:mm:ss"));
            var diff = end.getTime() - start.getTime(); 
            //var diff_in_day = diff / (1000 * 60 * 60 * 24);
            var diff_in_seconds = diff/1000;;
            if(diff_in_seconds > 0){
                this.flashsaleActive = true;
            // console.log('offer continue  Diff in Second = '+diff_in_seconds);
            }else{ 
                this.flashsaleActive = false;
                // console.log('expired Diff in Second = '+diff_in_seconds);
            }
		},

        initiateSliders(){
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 3,
            spaceBetween: 30,
            autoplay:true,
            breakpoints: {
                320: {
                slidesPerView: 1,
                spaceBetween: 10
                },
                640: {
                slidesPerView: 2,
                spaceBetween: 10
                },
                1200: {
                slidesPerView: 3,
                spaceBetween: 10
                },
                1920: {
                slidesPerView: 3,
                spaceBetween: 10
                }
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            });
        },

		load_flashsale_product(){
            let axiosConfig = {
            headers: {
                'X-localization': localStorage.getItem('lang')
            }
            }
            axios.get(this.$baseUrl+'/api/v1/get-daily-grocery-deals-product',axiosConfig).then(response => {
                this.product_todays_deal = response.data;

                this.product_todays_deal_total = response.data.items.length;

                let expriredate = moment(new Date()).format("MMM D, YYYY")+' 23:59:00';
                this.endTime = expriredate.replace('-', '');
                
                let that = this;
                setTimeout(function(){
                    that.countloading = true;
                },700);
            });
		},

        app_link(){
            var name = $('.app_name').val();
            var moible = $('.app_link_input').val();
            if(!moible || moible.length > 11){
            swal( "Oops" ,  'Plesae provide a valid mobile number.',  "error");
            }else{
            let formData = new FormData();
            formData.append('mobile', moible);
            formData.append('name', name);
            
            axios.post(this.$baseUrl+'/api/v1/app-link-request', formData).then(response =>{
                if(response.data.status == '1'){
                $('.app_link_input').val('');
                $('.app_name').val('');
                swal({
                    title: response.data.message,
                    icon: "success",
                    timer: 5000
                });
                }else{
                swal ("Oops", response.data.message, "error");
                }
            }).catch(function(){
                swal ( "Oops" ,  'Something went wrong.',  "error");
            });
            }
        },

       load_categories(){
            axios.get(this.$baseUrl+'/api/v1/get-grocery-categories').then(response => {
                this.categories = response.data.data;
                this.next_page_url = response.data.next_page_url;
                this.prev_page_url = response.data.prev_page_url;
                this.loading3 = true;
            });
        },

        load_grocery_products_you_may_like(){
            axios.get(this.$baseUrl+'/api/v1/get-grocery-products-you-may-like').then(response => {
                this.todayDeals = response.data.products.data;
                this.maylikeproducts = response.data.status;
                this.allnext_page_url = response.data.products.next_page_url;
            });
        },

        async moreProducts(url) {
            $('.load_more_btn').attr('disabled', true);
            let lang = localStorage.getItem("lang");
            if(lang == 'bn'){
                $('.load_more_btn').html('লোড হচ্ছে.. <span class="spinner-border spinner-border-sm"></span>');
            }else{
            $('.load_more_btn').html('Loading.. <span class="spinner-border spinner-border-sm"></span>');
            }

            let   { data }  = await axios.get(url);
            let c = data.products.data;
            this.all = data.products.data;
            c.forEach(element => {
                this.todayDeals.push(element);
                
                $('.load_more_btn').attr('disabled', false);
                let lang = localStorage.getItem("lang");
                if(lang == 'bn'){
                    $('.load_more_btn').html('আরো লোড করুন <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
                }else{
                    $('.load_more_btn').html('Load More <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
                }
                
            });
            this.allnext_page_url = data.products.next_page_url;
        },

        async moreCategoris(url) {
            let   { data }  = await axios.get(url);
            let c = data.data;
             c.forEach(element => {
                 this.categories.push(element);
             });
             this.next_page_url = data.next_page_url;
             this.prev_page_url = data.prev_page_url;
        },

        async lessCategoris(url) {
            let   { data }  = await axios.get(url);
            let c = data.data;
             c.forEach(element => {
                 this.categories.pop(element);
             });
             this.next_page_url = data.next_page_url;
             this.prev_page_url = data.prev_page_url;
        },

		load_flash_deals(){
			axios.get(this.$baseUrl+'/api/v1/get-grocery-flash-deals').then(response => {
				this.flashDeals = response.data.plain_list;
                this.loading1 = true;
			});
		},

        imageLoadError(event){
            event.target.src = "/images/notfound.png";
        },
    	scrollToTop() {
            window.scrollTo(0,0);
        },
    	ads_and_sponsored() {
			axios.get(this.$baseUrl+'/api/v1/get-grocery-ads-and-sponsored').then(response => {
				this.ad1 = response.data.ad1;
				this.ad2 = response.data.ad2;
				this.ad3 = response.data.ad3;
				this.ad4 = response.data.ad4;
				this.ad5 = response.data.ad5;
				this.sponsored_1 = response.data.sponsored_1;
				this.sponsored_2 = response.data.sponsored_2;
				this.sponsored_3 = response.data.sponsored_3;
                let that = this;
                setTimeout(function(){
                    that.adsLoading = true;
                },500);
                

			});
        }

	},
    watch:{
        $route(to, from){
          
        },
        
    },
    updated(){
        this.initiateSliders();
    },
    computed:{
        logged_in_user(){
            return this.$store.getters.getLoadedUser.user;
        },
    },
	mounted(){
        const plugin = document.createElement("script");
        plugin.setAttribute( "src", this.$frontendUrl+"/assets/js/carousel.js");
        plugin.async = true;
        document.body.appendChild(plugin);
        this.load_promotional_banner();
        this.load_flash_deals();
        this.load_grocery_products_you_may_like();
        this.load_categories();
        this.scrollToTop();
        this.thumbnailUrl = this.$thumbnailUrl;
        this.baseurl = this.$baseUrl;
        this.load_flashsale_product();
        this.updateTimer();
        this.timeinterval = setInterval(this.updateTimer, 1000);
        this.ads_and_sponsored();
       document.title = "MabiY Shop | Groceries";
	}

}
</script>