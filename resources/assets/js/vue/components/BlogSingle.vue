<template>
    <div>
    
    <section v-if="loading" id="blog_single_page">
        <div class="container all_sellers">

            <span v-if="singleBlog">
                <div class="row blog_single_page mt-2">
                    <div class="col-md-7 pl-0">
                       <div class="image-section">
                        <div class="mycontainer">
                            <div class="zoom-show" :href="baseurl+'/'+singleBlog.image">
                                <img @error="imageLoadError" :src="baseurl+'/'+singleBlog.image" id="show-img" class="blog_default_image"/>
                            </div>
    
                            <div v-if="gallery_images" class="small-img blog_small_image">
                                <img @error="imageLoadError" src="/images/online_icon_right@2x.png" class="icon-left" alt="" id="prev-img" />
                                <div class="small-container">
                                    <div id="small-img-roll" v-if="gallery_images">
                                        <img @error="imageLoadError" :src="baseurl+'/'+singleBlog.image" class="show-small-img" alt="" />
                                        <img @error="imageLoadError" v-for="img in gallery_images" :key="img" :src="baseurl+'/'+img" class="show-small-img" alt="" />
                                    </div>
                                  
                                </div>
                                <img @error="imageLoadError" src="/images/online_icon_right@2x.png" class="icon-right" alt="" id="next-img" />
                            </div>
                        </div>
    

                        </div>
                        <div class="blog_single_page_deatails pt-3">
                            <h5>{{ singleBlog.title }}</h5>
                            <p class="pb-5" v-html="singleBlog.description"></p>
                        </div>
                        <hr>
                        <div class="row">
                        <div class="latest_blogs">
                            <h5>{{ $t('Latest Blogs') }}</h5>
                            <div v-for="(data, index) in latestBlogs" :key="index" class="row p-0 mb-3">
                                <div class="col-3 col-sm-3 col-md-3 pr-0">
                                    <router-link :to="{ name: 'blogsingle', params: {slug: data.slug } }"> 
                                        <img @error="imageLoadError" :src="baseurl+'/'+data.image" alt="img">
                                    </router-link>
                                </div>
                                <div class="col-9 col-sm-9 col-md-9 pr-0">
                                    <router-link :to="{ name: 'blogsingle', params: {slug: data.slug } }">
                                        <b v-if="data.title">{{ data.title.substr(0, 100) }}</b>
                                        <p v-if="data.specification">{{ data.specification.substr(0, 100)+".." }}</p>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
            
                    <div class="col-md-5">
                        <h5 class="mt-3 text-uppercase text-success">{{ $t('Releted Products')}}</h5>
                        <div class="row search_products promotion">
                            <div v-for="(data, index) in products" :key="index" class="col-sm-6 col-md-61 col-lg-6">
                                <ProductGrid :data="data"></ProductGrid>
                                <QuickView :data="data" ></QuickView>
                            </div>
                        </div>
                    </div>

                </div>

            </span>
            <span v-else>
                <div class="row promotion_page">
                    <div class="col-md-12">
                        <img width="320px" @error="imageLoadError" src="/images/no-blog.webp" alt="">
                        <h4> {{ $t('Blog Not Found') }}  </h4> <p> <router-link :to="{name: 'blog'}" class="router-link-active"> {{ $t('Continue to blog list.') }} </router-link></p>
                    </div>
                </div>
            </span>
        </div>
    </section>
    
    
    <section v-else id="offer_page">
        <div class="container">
            <!-- <div class="row">
                <div class="col-md-12">
                   <img @error="imageLoadError" src="/images/Offer.gif" alt="">
                </div>
            </div> -->
    
            <div class="row blog_shimmer_responsive">
                <div class="col-md-7 col-lg-7">
                    <div class="shimmer"> <div class="h_30 w_100per mt_20"></div></div>
                    <div class="row">
                        <div class="col-md-2 col-lg-2 col-2 col-sm-2"> </div>
                        <div class="col-md-2 col-lg-2 col-2 col-sm-2"> <div class="shimmer"> <div class="h_10 w_100per"></div></div></div>
                        <div class="col-md-2 col-lg-2 col-2 col-sm-2"> <div class="shimmer"> <div class="h_10 w_100per"></div></div></div>
                        <div class="col-md-2 col-lg-2 col-2 col-sm-2"> <div class="shimmer"> <div class="h_10 w_100per"></div></div></div>
                        <div class="col-md-2 col-lg-2 col-2 col-sm-2"> <div class="shimmer"> <div class="h_10 w_100per"></div></div></div>
                        <div class="col-md-2 col-lg-2 col-2 col-sm-2"> </div>
                    </div>
                    <div class="shimmer"> <div class="h_3 w_100per mt_5"></div></div>
                    <div class="shimmer"> <div class="h_3 w_100per mt_5"></div></div>
                    <div class="shimmer"> <div class="h_3 w_100per mt_5"></div></div>
                    <div class="shimmer"> <div class="h_3 w_100per mt_5"></div></div>

                   
                    <div class="row mt-5">
                        <div class="col-md-4 col-lg-4">
                           <div class="shimmer"> <div class="h_15 w_100per mt_5"></div></div>
                        </div>
                        <div class="col-md-8 col-lg-8">
                           <div class="shimmer"> <div class="h_3 w_100per mt_3"></div></div>
                           <div class="shimmer"> <div class="h_3 w_100per mt_3"></div></div>
                           <div class="shimmer"> <div class="h_3 w_100per"></div></div>
                        </div>
                    </div>
                  

                </div>
                <div class="col-md-5 col-lg-5">
                    <div class="shimmer"> 
                        <div class="h_3 w_50per mt_20 mb_5"></div>
                    </div>  

                    <div class="row">
                        <div class="col-md-6 col-lg-6 mb-3">
                            <div class="shimmer"> 
                                <div class="h_20 w_100per mb_5"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_50per mt_2"></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <div class="shimmer"> 
                                <div class="h_20 w_100per mb_5"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_50per mt_2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6 mb-3">
                            <div class="shimmer"> 
                                <div class="h_20 w_100per mb_5"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_50per mt_2"></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <div class="shimmer"> 
                                <div class="h_20 w_100per mb_5"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_50per mt_2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6 mb-3">
                            <div class="shimmer"> 
                                <div class="h_20 w_100per mb_5"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_50per mt_2"></div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6 mb-3">
                            <div class="shimmer"> 
                                <div class="h_20 w_100per mb_5"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_2 w_100per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_15per mt_2"></div>
                                <div class="h_3 w_50per mt_2"></div>
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
    import axios from 'axios'
    import swal from 'sweetalert';
    import Leftsidebar from './default/Leftsidebar';
    import pagination from 'laravel-vue-pagination';
    import ProductGrid from './parts/ProductGrid';
import QuickView from './parts/QuickView.vue';
    

    export default {
        data(){
            return{
                loading:false,
                is_user: '',
                baseurl:'',
                singleBlog: '',
                latestBlogs:'',
                category_name:'',
                gallery_images: "",
                products:{},
            }
        },
        components:{
    Leftsidebar,
    pagination,
    ProductGrid,
    QuickView
},
        methods:{
            imageLoadError(event){
                event.target.src = "/images/notfound.png";
            },
            load_single_blog(){
                let axiosConfig = {
                    headers: {
                    'X-localization': localStorage.getItem('lang')
                    }
                }
    
    
                let slug = this.$route.params.slug;
                axios.get(this.$baseUrl + "/api/v1/get-single-blog/"+slug, axiosConfig).then((response) => {
                    this.loading = true;
                    this.singleBlog  = response.data.blog;

                    if(response.data.blog.gallery_images){
                        this.gallery_images = response.data.blog.gallery_images.split(",");
                    }else{
                        this.gallery_images = null;
                    }
                    this.latestBlogs  = response.data.latestBlogs;
                    this.products = response.data.relatedProducts;
                    this.category_name = response.data.category_name;

                    setTimeout(function() {
                        $('.show-small-img:first-of-type').click();
                        $(".show-small-img").removeAttr('alt');
                        $(".show-small-img:first").attr('alt', 'now');
                    },500);
    
                });
            },
            scrollToTop() {
                window.scrollTo(0,0);
            }
        },
    
        watch:{
            $route(to, from){
                this.load_single_blog();
                this.scrollToTop();
            } 
        },
        mounted(){
            this.scrollToTop();
            //ZOOM image
            $(document).ready(function() {
                setTimeout(function() {
                    $('.circle-1').css({ 'background': '#0093d9' });
                    $('.circle-1-text').css({ 'color': '#0093d9' });
                    $('.circle-1').html('<i class="fa fa-check" aria-hidden="true"></i>');
                    $('#big-img').css({ 'background': '#fff' });
                    $('[id=\'big-img\']').css({ 'background': '#fff' });
                    $('.zoom-show').zoomImage();
                    $('.show-small-img:first-of-type').css({ 'border': 'solid 1px #951b25', 'padding': '2px' });
                    $('.show-small-img:first-of-type').attr('alt', 'now').siblings().removeAttr('alt');
                    $('.show-small-img').click(function() {
                        $('#show-img').attr('src', $(this).attr('src'));
                        $('[id=\'big-img\']').attr('src', $(this).attr('src'));
                        $(this).attr('alt', 'now').siblings().removeAttr('alt');
                        $(this).css({ 'border': 'solid 1px #951b25', 'padding': '2px' }).siblings().css({ 'border': 'none', 'padding': '0' });
                        if ($('#small-img-roll').children().length > 4) {
                            if ($(this).index() >= 3 && $(this).index() < $('#small-img-roll').children().length - 1) {
                                $('#small-img-roll').css('left', -($(this).index() - 2) * 76 + 'px');
                            } else if ($(this).index() == $('#small-img-roll').children().length - 1) {
                                $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px');
                            } else {
                                $('#small-img-roll').css('left', '0');
                            }
                        }
                    });
                },2000);
            });
            this.load_single_blog();
            this.baseurl = this.$baseUrl;
            document.title = "MabiY Shop | Blog"; 
        }
    
    }
    </script>