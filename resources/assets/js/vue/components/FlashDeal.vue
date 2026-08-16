<template>
    <div>
    
    <section v-if="loading" id="flash_deal_single_page">
        <div>
            <span v-if="flashDeals">
                <div>
                    <div class="deal_banner">
                        <img @error="imageLoadError" v-if="flashDeals.banner" :src="baseurl+'/'+flashDeals.banner">
                        <img  v-else src="/images/notfound.png" alt="img">
                    </div>
                </div>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-12 col-md-12 text-center timewrapper_parent">
                            <h3 class="mt-3">{{flashDeals.title}}</h3>
                            
                            <div class="timewrapper">
                                <div id="app-timer">
                                    <div class="row">
                                        <div class="inding"> <b>  {{ $t('Ending in') }} :</b></div>
                                        <Timeitem v-for="(times, index) in times" :key="index"  v-bind:time="times"></Timeitem>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                


        <!--Just For You Start-->
            <section v-if="flashDeals.show_category_wise != 1 " class="jus_for_you">
                <div class="container">
                    <span v-if="loadMoreProducts.length > 0">
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
                                <h4> {{ $t('Product Not Found') }}.  </h4> <p> <router-link :to="{name: 'products'}" class="router-link-active"> {{ $t('Continue shopping') }} </router-link></p>
                            </div>
                        </div>
                    </span>
                </div>
            </section>


            <section v-else class="jus_for_you">
               
                <div class="container">
                    <span v-if="loadCategoryWiseProducts">
                       
                        <div v-for="(catWiseProduct,index) in loadCategoryWiseProducts" :key="index" style="padding: 15px;">
                           
                           <div class="row cat_head">
                                <div class="col-6 col-sm-6 col-md-8 col-lg-8"><h5>{{index}} </h5></div>
                                <div class="col-6 col-sm-6 col-md-4 col-lg-4 text-right categoryviewall"> <router-link :to="{ name: 'categorywiseflashdeal', params: {category_id:catWiseProduct[0].category_id, slug: category_slug } }" class="btn btn-primary">{{$t('View All')}}</router-link></div>
                           </div>
                           

                           <div class="row search_products promotion p-0">
                                <div v-for="(data, index) in catWiseProduct" :key="index" class="col-6 col-sm-6 col-md-4 col-lg-2 mb-3">
                                    <ProductGrid :data="data" ></ProductGrid>
                                    <QuickView :data="data" ></QuickView>
                                </div>
                           </div>
                        </div>
                    </span>
                </div>

            </section>



            <!--Just For You End-->


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
    
    
    <section v-else id="flash_deal_single_page">
       
        <div class="offer_shimmer">
            <div class="shimmer"> <div class="h_35 w_100per"></div></div>
        </div>

        <div class="container offer_shimmer">
            <div class="row mb_2">
                <div class="col-6 col-sm-4 col-md-4 col-lg-4 text-left float-left">  </div>
                <div class="col-6 col-sm-4 col-md-4 col-lg-4">  <div class="shimmer">  <div class="h_5 w_100per mb_1"></div> </div> </div>
                <div class="col-6 col-sm-4 col-md-4 col-lg-4 text-right float-right"> </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
                <div class="col-12 col-sm-6 col-md-2 col-lg-2"> <div class="shimmer"> <div class="h_25 w_100per"></div></div></div>
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
    import Timeitem from './default/Timeitem';
    import moment from 'moment';
import QuickView from './parts/QuickView.vue';
    
    export default {
        data(){
            return{
                category_slug:'',
                loading:false,
                is_user: '',
                baseurl:'',
                flashDeals:[],
                loadMoreProducts:{},
                progress: 100,
                timeinterval: undefined,
                product_flashsale:[],
                loadMoreProducts:{},
                next_page_url: '',
                category_next_page_url: '',
                prev_page_url:'',
                product_mostviewed:'',
                loadCategoryWiseProducts:'',
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
            imageLoadError(event){
                event.target.src = "/images/notfound.png";
            },
            scrollToTop() {
                window.scrollTo(0,0);
            },
            load_flash_deals(){
                let slug = this.$route.params.slug;
                this.category_slug = this.$route.params.slug;

                let axiosConfig = {
                    headers: {
                    'X-localization': localStorage.getItem('lang')
                    }
                }
                axios.get(this.$baseUrl+'/api/v1/get-flash-deal/'+slug,axiosConfig).then(response => {
                    this.flashDeals = response.data.deals;

                    this.loadMoreProducts = response.data.products.data;
                    this.loadCategoryWiseProducts = response.data.category_wise_products;
                    this.next_page_url = response.data.products.next_page_url;
                    this.prev_page_url = response.data.products.prev_page_url;

                    let end_date = response.data.deals.end_date;
                    let expriredate = moment(end_date).format("MMM D, YYYY h:mm:ss");
                    this.endTime = expriredate.replace('-', '');

                    this.loading = true; 
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
        mounted(){
            this.scrollToTop();
            this.load_flash_deals();
            this.updateTimer();
            this.timeinterval = setInterval(this.updateTimer, 1000);
            this.baseurl = this.$baseUrl;
            document.title = "MabiY Shop | Flash Deal"; 
        }
    
    }
    </script>