<template>
<div>

<span v-if="loading1">

<!-- Banner slider start -->
<section id="banner_slider_section">
  <div class="container">
    <div class="row mouse_leave_div">
    
        <div class="col-lg-3 category_class">
          <div class="dropdown bg-color border-top-lr">
              <div class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <p class="text-uppercase text-center m-0 text-white p-2"> <b> {{ $t('Categories') }} </b> </p> 
              </div>
          </div>

          <div class="nav_wrapper border-bottom-8">
            <nav class="navbar navbar-expand-md navbar-light">
              <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav" id="layer_1">
                  <li v-for="(category1, index) in categories" :key="index" class="nav-item dropdown layer_1_item">
                    <span v-if="category1.categories.length > 0">
                      <router-link :to="{name: 'category', params: {slug: category1.slug } }" class="nav-link has_layer_2" :data-layertwoparent="category1.id" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      {{ category1.title }} <i class="fa fa-chevron-right" aria-hidden="true"></i>
                      </router-link> 
                    </span>

                    <span v-else>
                      <router-link :to="{name: 'category', params: {slug: category1.slug } }" class="nav-link has_layer_2">
                        {{ category1.title }}
                      </router-link>
                    </span>

                  </li>
                </ul>
              </div>
            </nav>
          </div>

          <div class="layers_2 every_wrapper" id="layer2_main">
            <span v-for="(category1, index) in categories" :key="index">
              <span v-if="category1.categories.length > 0">
                <ul>
                  <li v-for="category2 in category1.categories" :key="category2.id" :class="'layer_2_sinlge layertwochild'+category1.id"> 

                    <span v-if="category2.categories.length > 0">
                      <router-link :to="{name: 'category', params: {slug: category2.slug } }" class="nav-link has_layer_3" :data-layerthreeparent="category2.id">
                        {{ category2.title }}  <i class="fa fa-chevron-right" aria-hidden="true"></i>
                      </router-link>
                    </span>

                    <span v-else>
                      <router-link :to="{name: 'category', params: {slug: category2.slug } }" class="nav-link has_layer_3">
                        {{ category2.title }}
                      </router-link>
                    </span>


                  </li>
                </ul>
              </span>
            </span>
          </div>

          <div class="layers_3 every_wrapper" id="layer3_main">
            <span v-for="(category1, index) in categories" :key="index">
              <span v-if="category1.categories.length > 0">
                  <span v-for="category2 in category1.categories" :key="category2.id">
                    <span v-if="category2.categories.length > 0">
                      <ul>
                          <li v-for="category3 in category2.categories" :key="category3.id" :class="'layer_3_sinlge layerthreechild'+category2.id">
                          <router-link :to="{name: 'category', params: {slug: category3.slug } }" class="nav-link">
                            {{ category3.title }}  
                          </router-link>
                          </li>
                      </ul>
                    </span>
                  </span>
              </span>
            </span>
          </div>


        </div>





        <div class="col-lg-9 banner_slider_wrap">
            <div id="carousel1_indicator" class="carousel slide carousel-fade" data-ride="carousel">
              <div class="carousel-inner">
                <div v-for="(slider, index) in all_slider" :key="slider.id" :class="['carousel-item border-all-8', (index === 0 ? 'active' : '')]">
                <router-link  :to="slider.button_link">
                  <img @error="imageLoadError" class="d-block w-100 border-all-8" :src="baseurl+'/'+slider.image" alt="First slide">
                  </router-link> 
                </div>
              </div>
              <a class="carousel-control-prev" href="#carousel1_indicator" role="button" data-slide="prev">
                <span class="fa fa-caret-left text-dark fa-2x" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
              </a>
              <a class="carousel-control-next" href="#carousel1_indicator" role="button" data-slide="next">
                <span class="fa fa-caret-right text-dark fa-2x" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
              </a>
            </div>
        </div>
    </div>
  </div>
</section>
<!-- Banner slider end -->
</span>
<span v-else>
  <div style="margin-top: 10px;" class="container">
    <div class="row">
      <!-- <img @error="imageLoadError" :src="frontendUrl+'/images/home-1.gif'" alt="img"> -->
      <div class="col-sm-3 col-lg-3 col-md-3 col-lg-3">
          <div class="shimmer desktop_only">
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
              <div class="h_3 w_100per mb_3"></div>
          </div>
      </div>
      <div class="col-sm-9 col-lg-9 col-md-9 col-lg-9 pr-0">
        <div class="shimmer">
          <div class="h_55 w_100per"></div>
        </div>
      </div>
    </div>
  </div>
</span>



<section id="promotional_banner">
  <div class="container">


    <div class="row promotional_banner_first">
      <div class="col-6 col-md-3 col-xl-3 col-xxl-3 side_big_banner">
        <!-- promotional Banner 1 start-->
        <div class="image_container side_promotion_banner" v-if="promotional_banner_1.status == '1'">
          <span v-if="promotional_banner_1.link_type == 'internal_url'" >
            <router-link :to="promotional_banner_1.link">
                <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_1.banner" alt="img">
            </router-link>
          </span>
          <span v-else>
            <a target="_blank" :href="promotional_banner_1.link">
              <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_1.banner" alt="img">
            </a>
          </span>
        </div>
        <!-- promotional Banner 1 End-->
      </div>
      <div class="col-6 col-md-3 col-xl-3 col-xxl-3 for_mobile">
        <!-- promotional Banner 2 start-->
        <div class="image_container side_promotion_banner" v-if="promotional_banner_2.status == '1'">
          <span v-if="promotional_banner_2.link_type == 'internal_url'" >
            <router-link :to="promotional_banner_2.link">
                <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_2.banner" alt="img">
            </router-link>
          </span>
          <span v-else>
            <a target="_blank" :href="promotional_banner_2.link">
              <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_2.banner" alt="img">
            </a>
          </span>
        </div>
        <!-- promotional Banner 2 End-->
      </div>





      <div class="col-12 col-md-6 col-xl-6 col-xxl-6">
        <div class="row">
          <div class="col-6 col-md-6 col-xl-6 col-xxl-6">
            <!-- promotional Banner 3 start-->
            <div class="image_container" v-if="promotional_banner_3.status == '1'">
              <span v-if="promotional_banner_3.link_type == 'internal_url'" >
                <router-link :to="promotional_banner_3.link">
                    <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_3.banner" alt="img">
                </router-link>
              </span>
              <span v-else>
                <a target="_blank" :href="promotional_banner_3.link">
                  <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_3.banner" alt="img">
                </a>
              </span>
            </div>
            <!-- promotional Banner 3 End-->
          </div>
          <div class="col-6 col-md-6 col-xl-6 col-xxl-6">
            <!-- promotional Banner 4 start-->
            <div class="image_container" v-if="promotional_banner_4.status == '1'">
              <span v-if="promotional_banner_4.link_type == 'internal_url'" >
                <router-link :to="promotional_banner_4.link">
                    <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_4.banner" alt="img">
                </router-link>
              </span>
              <span v-else>
                <a target="_blank" :href="promotional_banner_4.link">
                  <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_4.banner" alt="img">
                </a>
              </span>
            </div>
            <!-- promotional Banner 4 End-->
          </div>
        </div>
        <div class="row">
          <div class="col-6 col-md-6 col-xl-6 col-xxl-6">
            <!-- promotional Banner 5 start-->
            <div class="image_container" v-if="promotional_banner_5.status == '1'">
              <span v-if="promotional_banner_5.link_type == 'internal_url'" >
                <router-link :to="promotional_banner_5.link">
                    <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_5.banner" alt="img">
                </router-link>
              </span>
              <span v-else>
                <a target="_blank" :href="promotional_banner_5.link">
                  <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_5.banner" alt="img">
                </a>
              </span>
            </div>
            <!-- promotional Banner 5 End-->
          </div>
          <div class="col-6 col-md-6 col-xl-6 col-xxl-6">
            <!-- promotional Banner 6 start-->
            <div class="image_container" v-if="promotional_banner_6.status == '1'">
              <span v-if="promotional_banner_6.link_type == 'internal_url'" >
                <router-link :to="promotional_banner_6.link">
                    <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_6.banner" alt="img">
                </router-link>
              </span>
              <span v-else>
                <a target="_blank" :href="promotional_banner_6.link">
                  <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_6.banner" alt="img">
                </a>
              </span>
            </div>
            <!-- promotional Banner 6 End-->
          </div>
        </div>
      </div>
      <div class="col-12 col-md-3 col-xl-3 col-xxl-3 for_desktop side_big_banner">
        <!-- promotional Banner 2 start-->
        <div class="image_container side_promotion_banner" v-if="promotional_banner_2.status == '1'">
          <span v-if="promotional_banner_2.link_type == 'internal_url'" >
            <router-link :to="promotional_banner_2.link">
                <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_2.banner" alt="img">
            </router-link>
          </span>
          <span v-else>
            <a target="_blank" :href="promotional_banner_2.link">
              <img @error="imageLoadError" :src="baseurl+'/'+promotional_banner_2.banner" alt="img">
            </a>
          </span>
        </div>
        <!-- promotional Banner 2 End-->
      </div>
    </div>



  </div>
</section>





<!-- flash sale start -->


<section class="products_shop flashsale_section " v-if="product_flashsale.status == 1 && flashsaleActive">
  <div class="product-main" :style="'background: url('+baseurl+'/'+product_flashsale.banner+')no-repeat'">
    <div class="container pb-3 pt-3">

        <div class="row countDown_section mb-3 border-8">
          <div class="col-md-3 col-lg-3 pl-1">
            <div class="title">
              <h3 v-html="product_flashsale.title"></h3>
            </div>
          </div>
          <div class="col-md-7 col-lg-7">
            <div id="app-timer" class="container-fluid">
            <div class="row justify-content-center">
              <div class="inding"> <b>  {{ $t('Ending in') }} :</b></div>
  
              <Timeitem v-for="(times, index) in times" :key="index"  v-bind:time="times"></Timeitem>
            </div>
            </div>
          </div>
          <div class="col-md-2 col-lg-2 carousel_btn_col">
            <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'promotion', params: {product_type: 'flashsale-products'} }">{{ $t('Shop more') }}<i class="fa fa-chevron-right" aria-hidden="true"></i></router-link></p>
          </div>
        </div>

      <div class="row">
        <div class="col-md-12">
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <div v-for="(data, index) in product_flashsale.items" :key="data.id" :data-length="index" class="swiper-slide" data-swiper-autoplay="5000">
                  <ProductGrid :data="data" ></ProductGrid>
                </div>
            </div>
            <div class="swiper-pagination"></div>
          </div>

          <div v-for="(data, index) in product_flashsale.items" :key="data.id" :data-length="index">
            <QuickView :data="data" ></QuickView>
          </div>
        
        </div>
      </div>

    </div>
  </div>
</section>
<!-- flash sale End -->




<!-- Featured product start -->

<section class="products_shop margin_bottom_custom" id="main" v-if="product_featured.status == 1">
  <div class="product-main">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="row">
          <div class="col-7 col-sm-6 col-md-6 col-lg-6">
            <div class="title">
              <h3 v-html="product_featured.title"></h3>
              
            </div>
          </div>

          <div class="col-5 col-sm-6 col-md-6 col-lg-6 carousel_btn_col">
            <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'promotion', params: {product_type: 'featured-products'} }">{{ $t('Shop more') }}<i class="fa fa-chevron-right" aria-hidden="true"></i></router-link></p>
          </div> 
        </div>
         
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide" data-swiper-autoplay="5000" v-for="(data, index) in product_featured.items" :key="data.id" :data-length="index">
                <ProductGrid :data="data" ></ProductGrid>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div> 

          <div v-for="(data, index) in product_featured.items" :key="data.id" :data-length="index">
            <QuickView :data="data" ></QuickView>
          </div>


        </div>
      </div>
      
    </div>
  </div>
</section>
<!-- Featured product End -->


<!-- App Banner start  -->
<section id="app_section" v-if="ad1.status == 1">
  <div class="container">

    <div v-if="ad1.link_type == 'internal_url'" class="app-banner mt-1">
      <router-link :to="ad1.link">
          <img @error="imageLoadError" :src="baseurl+'/'+ad1.banner" alt="img">
      </router-link>
    </div>
    <div v-else class="app-banner mt-1">
      <a target="_blank" :href="ad1.link">
        <img @error="imageLoadError" :src="baseurl+'/'+ad1.banner" alt="img">
      </a>
    </div>


  </div>
</section>
<!-- App Banner End -->



<!-- Onsale start -->

<section class="products_shop margin_bottom_custom"  v-if="product_onsale.status == 1">
  <div class="product-main">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="row">
          <div class="col-7 col-sm-6 col-md-6 col-lg-6">
            <div class="title">
              <h3 v-html="product_onsale.title"></h3>
              
            </div>
          </div>

          <div class="col-5 col-sm-6 col-md-6 col-lg-6 carousel_btn_col">
            <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'promotion', params: {product_type: 'onsale-products'} }">{{ $t('Shop more') }}<i class="fa fa-chevron-right" aria-hidden="true"></i></router-link></p>
          </div> 


        </div>
         
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide" data-swiper-autoplay="5000" v-for="(data, index) in product_onsale.items" :key="data.id" :data-length="index">
                <ProductGrid :data="data" ></ProductGrid>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div> 

          <div v-for="(data, index) in product_onsale.items" :key="data.id" :data-length="index">
            <QuickView :data="data" ></QuickView>
          </div>


        </div>
      </div>
      
    </div>
  </div>
</section>
<!-- On sale End -->



<!-- Best selling start -->

<section class="products_shop margin_bottom_custom"  v-if="product_bestselling.status == 1">
  <div class="product-main">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="row">
          <div class="col-7 col-sm-6 col-md-6 col-lg-6">
            <div class="title">
              <h3 v-html="product_bestselling.title"></h3>
              
            </div>
          </div>

          <div class="col-5 col-sm-6 col-md-6 col-lg-6 carousel_btn_col">
            <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'promotion', params: {product_type: 'bestselling-products'} }">{{ $t('Shop more') }}<i class="fa fa-chevron-right" aria-hidden="true"></i></router-link></p>
          </div> 


        </div>
         
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide" data-swiper-autoplay="5000" v-for="(data, index) in product_bestselling.items" :key="data.id" :data-length="index">
                <ProductGrid :data="data" ></ProductGrid>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div> 

          <div v-for="(data, index) in product_bestselling.items" :key="data.id" :data-length="index">
            <QuickView :data="data" ></QuickView>
          </div>

        </div>
      </div>
      
    </div>
  </div>
</section>

<!-- Best selling End -->



<!-- App Banner start  -->
<section id="app_section" v-if="ad2.status == 1">
  <div class="container">
    <div v-if="ad2.link_type == 'internal_url'" class="app-banner mt-1">
      <router-link :to="ad2.link">
          <img @error="imageLoadError" :src="baseurl+'/'+ad2.banner" alt="img">
      </router-link>
      
    </div>

    <div v-else class="app-banner mt-1">
      <a target="_blank" :href="ad2.link">
        <img @error="imageLoadError" :src="baseurl+'/'+ad2.banner" alt="img">
      </a>
      
    </div>

  </div>
</section>
<!-- App Banner End -->


<!-- Most view start -->
<section class="products_shop margin_bottom_custom" v-if="product_mostviewed.status == 1">
  <div class="product-main">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="row">
          <div class="col-7 col-sm-6 col-md-6 col-lg-6">
            <div class="title">
              <h3 v-html="product_mostviewed.title"></h3>
              
            </div>
          </div>

          <div class="col-5 col-sm-6 col-md-6 col-lg-6 carousel_btn_col">
            <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'promotion', params: {product_type: 'most-viewed-products'} }">{{ $t('Shop more') }}<i class="fa fa-chevron-right" aria-hidden="true"></i></router-link></p>
          </div> 


        </div>
         
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
              <div class="swiper-slide" data-swiper-autoplay="5000" v-for="(data, index) in product_mostviewed.items" :key="data.id" :data-length="index">
                <ProductGrid :data="data" ></ProductGrid>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div> 

          <div v-for="(data, index) in product_mostviewed.items" :key="data.id" :data-length="index">
            <QuickView :data="data" ></QuickView>
          </div>


        </div>
      </div>
      
    </div>
  </div>
</section>
<!-- Most view  End -->

<!-- Featured product Start -->
<div class="margin_bottom_custom"  v-if="loading3">
<section v-if="featuredSellers.status == 1">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="row">
          <div class="col-7 col-sm-8 col-md-6 col-lg-6">
            <div class="title">
              <h3 v-html="featuredSellers.title"></h3>
            </div>
          </div>
          <div class="col-5 col-sm-4 col-md-6 col-lg-6 carousel_btn_col">
            <p class="text-right mb-0 more_to_show"> <router-link :to="{ name: 'sellers'}">{{ $t('All Sellers') }} <i aria-hidden="true" class="fa fa-chevron-right"></i></router-link></p>
          </div>
        </div>

        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide" data-swiper-autoplay="5000" v-for="(seller, index) in featuredSellers.items" :key="seller.id" :data-length="index">
                  <router-link  :to="{ name: 'shop', params: {slug: seller.shopinfo.slug } }"><img @error="imageLoadError" :src="thumbnailUrl+'/'+seller.shopinfo.logo" alt=""> </router-link> 
            </div>
          </div>
          <div class="swiper-pagination"></div>
        </div>

        </div>
      </div>
    
    </div>
</section>
</div>
<div v-else class="seller_shemer" id="seller_shemer_not_found">
  <div class="container">
    <div class="row"> <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_2 w_100per"></div></div></div></div>
    <div class="row">
      <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_20 w_100per"></div></div></div>
      <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_20 w_100per"></div></div></div>
      <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_20 w_100per"></div></div></div>
      <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_20 w_100per"></div></div></div>
      <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_20 w_100per"></div></div></div>
      <div class="col-sm-6 col-md-2 col-lg-2"><div class="shimmer"><div class="h_20 w_100per"></div></div></div>
    </div>
      <div class="row"> 
        <div class="col-md-10 col-lg-10"></div>
        <div class="col-md-2 col-lg-2"><div class="shimmer"><div class="h_2 w_100per"></div></div></div>
      </div>
  </div>
</div>
<!-- Featured product end -->

<br><br>


<!--App link start-->
<!-- <section id="family_section">
  <div class="container">
    <div class="row">
        <div class="col-md-4 family_big_img">
          <img @error="imageLoadError" :src="baseurl+'/uploads/images/app.jpg'" alt="img">
        </div>
        <div class="col-md-1"></div>

        <div class="col-md-7">
            <div class="title"> <h3>Be a part of our family</h3></div>
            <ul class="app_icon">
              <li><img @error="imageLoadError" :src="baseurl+'/uploads/images/android.jpg'" alt="img"></li>
              <li><img @error="imageLoadError" :src="baseurl+'/uploads/images/apple.jpg'" alt="img"></li>
            </ul>

            <div class="row ">
              <div class="col-md-12">
                <form class="get_app_link">
                  <div class="flag">
                    <img @error="imageLoadError" :src="baseurl+'/uploads/images/bangladesh.jpg'" alt="img"> <span>+88</span>
                  </div>
                  <input type="text" name="search2" class="app_link_input">
                  <div class="btn btn-primary app_link_btn" @click.prevent="app_link()">Get App Link</div>
                </form>
                <small style="color: #ababab;">E.g. +8801886790890</small>
              </div>
               
            </div>
        </div>
    </div>
  </div>
</section> -->
<!--App link End-->



<span v-if="applinkloading">
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
<section id="family_section" class="newsletter">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <div class="shimmer"><div class="h_30 w_100per"></div></div>
      </div>
      <div class="col-md-4">
        <div class="shimmer"><div class="h_30 w_100per"></div></div>
      </div>
    </div>
  </div>
</section>
</span>





<!--Just For You Start-->
<section class="jus_for_you">
    <div class="container">
        <div class="row p-0 mb-3">
          <div class="col-12 col-sm-12 col-md-12 col-lg-12 pl-2">
            <div class="title ">
              <h3>{{ $t('Products You May Like') }}</h3>
            </div>
          </div>
        </div>


        <span v-if="loadMoreProducts">
        <div class="row search_products promotion p-0">
            <div v-for="(data, index) in loadMoreProducts" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-2 mb-3">
              <ProductGrid :data="data" ></ProductGrid>
              <QuickView :data="data" ></QuickView>
            </div>
        </div>

        <div class="row product_pagination">
            <div class="col-md-12 text-center mb-3 mt-3">
                <div class="moreBtn">
                    <!--<div class="btn btn-primary" v-if="prev_page_url" @click.prevent="lessProducts(prev_page_url)">Less more<i class="fa fa-chevron-up ml-3" aria-hidden="true"></i></div> -->
                    <div class="btn btn-primary load_more_btn" v-if="next_page_url" @click.prevent="moreProducts(next_page_url)">{{ $t('Load More') }}<i class="fa fa-chevron-down ml-3" aria-hidden="true"></i></div>
                </div>
            </div>
        </div>
        </span>
        <span v-else>
            <div class="row promotion_page">
                <div class="col-md-12">
                    <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                    <h4> {{ $t('Product Not Found') }}  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
                </div>
            </div>
        </span>
    </div>
</section>
<!--Just For You End-->


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

<!--Service process-->
<section id="service_process">
  <div class="container">
    <div class="row service_process">
      <div class="col-6 col-sm-6 col-md-3 col-lg-3 text-center">
        <span v-if="maylikeLoading">
        <img @error="imageLoadError" :src="baseurl+'/uploads/images/original-authenic-products.png'">
        </span>
        <span v-else>
          <div class="shimmer"><div class="h_50p w_50p"></div></div>
        </span>
        <br>
        <b style="text-transform:uppercase">original authenic products</b>
      </div>
      <div class="col-6 col-sm-6 col-md-3 col-lg-3 text-center">
        <span v-if="maylikeLoading">
        <img @error="imageLoadError" :src="baseurl+'/uploads/images/fast-delivery-guranteed.png'">
        </span>
        <span v-else>
          <div class="shimmer"><div class="h_50p w_50p"></div></div>
        </span>
        <br>
        <b style="text-transform:uppercase">fast delivery guranteed</b>
      </div>
      <div class="col-6 col-sm-6 col-md-3 col-lg-3 text-center">
        <span v-if="maylikeLoading">
        <img @error="imageLoadError" :src="baseurl+'/uploads/images/easy-return-policy.png'" >
        </span>
        <span v-else>
          <div class="shimmer"><div class="h_50p w_50p"></div></div>
        </span>
        <br>
        <b style="text-transform:uppercase">easy return policy</b>
      </div>
      <div class="col-6 col-sm-6 col-md-3 col-lg-3 text-center">
        <span v-if="maylikeLoading">
        <img @error="imageLoadError" :src="baseurl+'/uploads/images/cash-on-delivery-available.png'" >
        </span>
        <span v-else>
          <div class="shimmer"><div class="h_50p w_50p"></div></div>
        </span>
        <br>
        <b style="text-transform:uppercase">cash on delivery available</b>
      </div>
    </div>
  </div>
</section>
<!--Order Process End-->



</div>
</template>


<script>
import {bus} from '../app.js'
import axios from 'axios'
import Form from 'vform'
import Timeitem from './default/Timeitem';
import moment from 'moment';
import ProductGrid from './parts/ProductGrid';
import QuickView from './parts/QuickView';



export default {
	
	data(){
		return{
      how_to_order_from_website_text:'',
      how_to_order_from_website_images:'',
      mydate:'',
      flashsaleActive:false,
      loading1: false,
      loading2:false,
      loading3:false,
      categories:[],
      ad1:'',
      ad2:'',
      maylikeLoading:'',
      promotional_banner_1:'',
      promotional_banner_2:'',
      promotional_banner_3:'',
      promotional_banner_4:'',
      promotional_banner_5:'',
      promotional_banner_6:'',
			user_id:'',
			name:'',
			all_slider:[],
      applinkloading:false,
			featuredSellers:[],
			product_onsale:[],
			product_featured:[],
			product_bestselling:[],
			baseurl:'',
			thumbnailUrl:'',
      frontendUrl:'',
			cartForm: new Form({
			  product_id: '',
			}),
			
			//startTime: new Date().toString().split(' ').splice(1,3).join(' '),
      startTime: moment(new Date()).format("MMM D, YYYY h:mm:ss"),
			endTime: 'Jan 23, 9090 4:01:00',
			times: [
			  { id: 0, text: "D", time: 1 },
			  { id: 1, text: "H", time: 1 },
			  { id: 2, text: "M", time: 1 },
			  { id: 3, text: "S", time: 1 }
			],
			progress: 100,
			timeinterval: undefined,
      product_flashsale:[],
      loadMoreProducts:{},
      next_page_url: '',
      category_next_page_url: '',
      prev_page_url:'',
      product_mostviewed:''
		}
	},
	components:{
    Timeitem,
    ProductGrid,
    QuickView
},
	methods:{
      imageLoadError(event){
        event.target.src = "/images/notfound.png";
      },

      initiateSliders(){
        var swiper = new Swiper(".mySwiper", {
          slidesPerView: 6,
          spaceBetween: 30,
          autoplay:true,
          breakpoints: {
            320: {
              slidesPerView: 2,
              spaceBetween: 10
            },
            640: {
              slidesPerView: 3,
              spaceBetween: 10
            },
            1200: {
              slidesPerView: 5,
              spaceBetween: 10
            },
            1920: {
              slidesPerView: 6,
              spaceBetween: 10
            }
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
        });
      },
      
    load_categories(){
      let axiosConfig = {
        headers: {
          'X-localization': localStorage.getItem('lang')
        }
      }
      axios.get(this.$baseUrl+'/api/v1/categories', axiosConfig).then(response => {
        this.categories = response.data;
        this.category_next_page_url = response.next_page_url;
      });
    },

    load_add(){
      axios.get(this.$baseUrl+'/api/v1/get-add').then(response => {
        this.ad1 = response.data.ad1;
        this.ad2 = response.data.ad2;
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

    load_promotional_banner(){
      axios.get(this.$baseUrl+'/api/v1/get-promotional-banner').then(response => {
        this.promotional_banner_1 = response.data.promotional_banner_1;
        this.promotional_banner_2 = response.data.promotional_banner_2;
        this.promotional_banner_3 = response.data.promotional_banner_3;
        this.promotional_banner_4 = response.data.promotional_banner_4;
        this.promotional_banner_5 = response.data.promotional_banner_5;
        this.promotional_banner_6 = response.data.promotional_banner_6;
        this.how_to_order_from_website_text = response.data.how_to_order_from_website.how_to_order_from_website_description;
        this.how_to_order_from_website_images = response.data.how_to_order_from_website.how_to_order_from_website_image;


      });
    },

    scrollToTop(){
				window.scrollTo(0,0);
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


    addToCompare(product_id){
				let user  = this.$store.getters.getLoadedUser.user;
				if(!user){
					$('#popupLoignModal').trigger('click');
					$('#dyanamicInput').html('<input type="hidden" id="addToCartProductId" value="'+product_id+'"> <input type="hidden" id="PopupCompare" value="compare">');
				}else{
					let token = localStorage.getItem("token");
					let axiosConfig = {
						headers: {
							'Content-Type': 'application/json;charset=UTF-8',
							"Access-Control-Allow-Origin": "*",
							'Authorization': 'Bearer '+token
						}
					}
					this.produt_id = product_id;
					axios.post(this.$baseUrl+'/api/v1/add-to-compare', {product_id:product_id}, axiosConfig).then(response => {
						if(response.data.status == 1){
							this.checkCompare = 1;
							$('.alreadycompared').css({'color':'#c7c7c7'});
							this.$store.dispatch('loadedCompares');
							swal({
								title: "Successfully added to your compare list.",
								icon: "success",
								timer: 3000
							});
						}else{
							swal("Sorry" , response.data.message,  "error" );
						}
					});
				}
			},
			
		
		
		addToCart(product_id){
      $('.disabledbtn'+product_id).attr('disabled', true);
      $('.disabledbtn'+product_id).html('<span class="spinner-border spinner-border-sm"></span>');
      let session_key = localStorage.getItem("session_key");
      let token = localStorage.getItem("token");
      let axiosConfig = {
        headers: {
          'Content-Type': 'application/json;charset=UTF-8',
          "Access-Control-Allow-Origin": "*",
          'Authorization': 'Bearer '+token
        }
      }

      let lang = localStorage.getItem("lang");
        axios.post(this.$baseUrl+'/api/v1/add-to-cart', {product_id:product_id,qty:1,session_key:session_key},axiosConfig ).then(response => {
            if(response.data.status == 1){
              this.$store.dispatch('loadedCart');
			  jQuery('.back_to_cart').trigger('click');
              swal({
                title: "Product added to cart Successfully.",
                icon: "success",
                timer: 3000
              }).then(()=>{
                $('.disabledbtn'+product_id).attr('disabled', true);
                if(lang == 'bn'){
                  $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
                }else{
                  $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
                }
              });
            }else{
              swal ( "Oops", response.data.message, "error");
              $('.disabledbtn'+product_id).attr('disabled', false);
              if(lang == 'bn'){
                $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> যুক্ত করুন');
              }else{
                $('.disabledbtn'+product_id).html('<i class="fa fa-shopping-basket"></i> Add To Cart');
              }
            }
        });
		},
    
		load_Slider(){
			axios.get(this.$baseUrl+'/api/v1/sliders').then(response => {
				this.all_slider = response.data;
        $('.nav_wrapper').show();
        this.loading1 = true;
			});
		},

		load_onsale_product(){
      let axiosConfig = {
          headers: {
            'X-localization': localStorage.getItem('lang')
          }
        }

			axios.get(this.$baseUrl+'/api/v1/get-onsale-product',axiosConfig).then(response => {
				this.product_onsale = response.data;
			});
		},

		load_mostviewed_product(){
      let axiosConfig = {
          headers: {
            'X-localization': localStorage.getItem('lang')
          }
        }

			axios.get(this.$baseUrl+'/api/v1/get-mostViewed-product',axiosConfig).then(response => {
				this.product_mostviewed = response.data;
			});
		},


		load_flashsale_product(){
        let axiosConfig = {
          headers: {
            'X-localization': localStorage.getItem('lang')
          }
        }

			axios.get(this.$baseUrl+'/api/v1/get-flashsale-product',axiosConfig).then(response => {
        let strin = response.data.expired_date;
        let mtime = strin.replace('T', ' ');
        let expriredate = moment(mtime).format("MMM D, YYYY h:mm:ss");
        this.mydate =expriredate;
        this.endTime = expriredate.replace('-', '');


				this.product_flashsale = response.data;
			});
		},

		load_featured_product(){

      let axiosConfig = {
        headers: {
          'X-localization': localStorage.getItem('lang')
        }
      }


			axios.get(this.$baseUrl+'/api/v1/get-featured-product', axiosConfig).then(response => {
				this.product_featured = response.data;
			});
		},
		load_bestselling_product(){
			axios.get(this.$baseUrl+'/api/v1/get-bestSelling-product').then(response => {
				this.product_bestselling = response.data;
			});
		},
		load_featured_sellers(){
			axios.get(this.$baseUrl+'/api/v1/featured-seller').then(response => {
				this.featuredSellers = response.data;
        this.loading3 = true;
        this.applinkloading = true;
        
			});
		},
    
    load_more_products(page=1){
      let axiosConfig = {
        headers: {
          'X-localization': localStorage.getItem('lang')
        }
      }
			axios.get(this.$baseUrl+'/api/v1/get-all-product/?page='+page, axiosConfig).then(response => {
				this.loadMoreProducts = response.data.data;
        this.next_page_url = response.data.next_page_url;
        this.prev_page_url = response.data.prev_page_url;
        this.maylikeLoading = true;
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
        let c = data.data;
          c.forEach(element => {
              this.loadMoreProducts.push(element);
              
              $('.load_more_btn').attr('disabled', false);
              let lang = localStorage.getItem("lang");
              if(lang == 'bn'){
                $('.load_more_btn').html('আরো লোড করুন <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
              }else{
                $('.load_more_btn').html('Load More <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
              }
             
          });
          this.next_page_url = data.next_page_url;
          this.prev_page_url = data.prev_page_url;
    },

	},

	computed: {
    logged_in_user(){
        return this.$store.getters.getLoadedUser.user;
    }
	},

  updated(){
    this.initiateSliders();
  },
 

	mounted(){
    this.scrollToTop();
    this.load_Slider();
    this.load_add();
    this.load_promotional_banner();
    this.updateTimer();
    this.timeinterval = setInterval(this.updateTimer, 1000);
    this.cartForm.user_id = localStorage.getItem("user_id");
    this.load_categories();
    this.load_onsale_product();
    this.load_mostviewed_product();
    this.load_featured_sellers();
    this.load_flashsale_product();
    this.baseurl = this.$baseUrl;
    this.thumbnailUrl = this.$thumbnailUrl;
    this.frontendUrl = this.$frontendUrl;
    this.load_featured_product();
    this.load_bestselling_product();
    this.load_more_products();
    document.title = "MabiY Shop | Home";

    //Affiliate
    const params = new Proxy(new URLSearchParams(window.location.search), {
      get: (searchParams, prop) => searchParams.get(prop),
    });
    let referer = params.referer;
    if(referer){
      localStorage.setItem('affiliate_referer',referer)
    }


    

    setTimeout(function(){
      let sliderHeight = $('#carousel1_indicator').height();
      $('.nav_wrapper').css({'height': (sliderHeight-37.5)+'px','overflow':'scroll' });
      $('.layers_2').css({'height': (sliderHeight+2.5)+'px' });
      $('.layers_3').css({'height': (sliderHeight+2.5)+'px' });
      
    }, 5000);

	}

}


</script>

