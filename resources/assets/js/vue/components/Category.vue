<template>
    <div>
    
    <section id="search-page" v-if="loading">
        <div class="container">
            <!-- <div class="row">
                <div class="search_banner">
                    <img @error="imageLoadError" :src='baseurl+"/assets/images/banner-1.jpg"' alt="">
                </div>
            </div> -->
            <div class="row">
                <div class="col-md-12 search_page_orderBy">
                    <div class="row">
                        <div class="col-md-9 col-lg-9 pr-0">
                            <div class="searchby_category">
                                <b>Category:</b> <span class="title">{{ category.title }}</span>
                            </div>
                        </div>
                 
                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 pl-0">
                            <select class="orederbyselect" @change.prevent="load_search_product()">
                                <option disabled selected>- {{ $t('Sellect Order By') }} -</option>
                                <option value="lowtohigh">{{ $t('Low To High Price') }}</option>
                                <option value="hightolow">{{ $t('High To Low Price') }}</option>
                                <option value="atoz">{{ $t('Order: By A To Z') }}</option>
                                <option value="ztoa">{{ $t('Order: By Z To A') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
    
    
            <div class="row search">
                <div class="search_filter">
                    <h6>Filter <i class="fa fa-filter" aria-hidden="true"></i></h6>
                    <div class="serach_filter_chevron">
                        <i class="changeable_fa fa fa-chevron-right" aria-hidden="true"></i>
                    </div>
                </div>
    
                <div class="col-12 col-sm-12 col-md-3 col-lg-3 search_sidebar">
                    <div class="category_check">
                        <b>{{ $t('Price') }}</b>
                        
                        <div class="rang">
                            <div class="pricerang"> <input type="number" min="1"  class="form-control minprice" :placeholder="$t('Min')"> </div>
                            <div class="pricerang"> <input type="number" min="1"  class="form-control maxprice" :placeholder="$t('Max')"> </div>
                            <div class="pricerang"> <input type="submit" class="form-control sumitsearch" :value="$t('Search')" @click.prevent="load_search_product()"> </div>
                        </div>
                        
                    </div>
    
    
    
                    <div class="category_check">
                        <b>{{ $t('Categories') }}</b>
                        <ul class="category_parent layer_1 category_filter">
                            <li class="common_parent" v-for="(category1, index) in childcategories" :key="index"> 
                                <input type="checkbox" name="categorySelected" :value="category1.id" @click="load_search_product_category_click()"> <span :class="['default_cat', (category1.categories.length > 0 ? 'has_child' : '')]" >{{ category1.title }} <span v-if="category1.categories.length > 0"><i class="fa fa-chevron-right" aria-hidden="true"></i></span></span>
                                <ul class="category_parent layer_2 child" v-if="category1.categories.length > 0">
                                    <li  class="common_parent" v-for="(categorie2, index) in category1.categories" :key="index">
                                        <input type="checkbox" name="categorySelected" :value="categorie2.id" @click="load_search_product_category_click()"> <span :class="['default_cat', (categorie2.categories.length > 0 ? 'has_child' : '')]" >{{ categorie2.title }} <span v-if="categorie2.categories.length > 0"><i class="fa fa-chevron-right" aria-hidden="true"></i></span></span>
                                        <ul class="category_parent layer_3 child" v-if="categorie2.categories.length > 0">
                                            <li class="common_parent" v-for="(categorie3, index) in categorie2.categories" :key="index">
                                                <input type="checkbox" name="categorySelected" :value="categorie3.id" @click="load_search_product_category_click()"> <span>{{ categorie3.title }}</i></span></span>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
    
                            </li>
                        </ul>
    
                        <div class="moreBtn">
                             <div class="lessMore lessBtn" v-if="prev_page_url" @click.prevent="lessCategoris(prev_page_url)">{{ $t('Less') }}<i class="fa fa-chevron-up" aria-hidden="true"></i></div>
                            <div class="lessMore morebutn" v-if="next_page_url" @click.prevent="moreCategoris(next_page_url)">{{ $t('More') }}<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
                        </div>
                    </div>
    
    
    
    
    
    
                    <div class="category_check">
                        <b>{{ $t('Brand') }}</b>
                        <ul>
                            <li v-for="(brand, index) in brands" :key="index"> <input type="checkbox" name="brandSelected" :value="brand.id" @click="load_search_product()"> <span>{{ brand.title }}</span> </li>
                        </ul>
                        <div class="moreBtn">
                             <div class="lessMore lessBtn" v-if="brand_prev_page_url" @click.prevent="lessBrands(brand_prev_page_url)">{{ $t('Less') }}<i class="fa fa-chevron-up" aria-hidden="true"></i></div>
                            <div class="lessMore morebutn" v-if="brand_next_page_url" @click.prevent="moreBrands(brand_next_page_url)">{{ $t('More') }}<i class="fa fa-chevron-down" aria-hidden="true"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-9  col-lg-9 search_product_div"> 
                    <span v-if="searchProduct.data.length > 0">
                    <div class="row search_products category_search_page">
                        <div v-for="(data, index) in searchProduct.data" :key="index" class="col-6 col-sm-6 col-md-3 col-lg-3 mb-3">
                            <ProductGrid :data="data"></ProductGrid>
                            <QuickView :data="data" ></QuickView>
                        </div>
                    </div>
                   
                    <div class="row product_pagination">
                        <div class="col-md-12 text-center mb-3 mt-3">
                            <div class="moreBtn">
                                <!--<div class="btn btn-primary" v-if="prev_page_url" @click.prevent="lessProducts(prev_page_url)">Less more<i class="fa fa-chevron-up ml-3" aria-hidden="true"></i></div> -->
                                <div class="btn btn-primary load_more_btn" v-if="all_next_page_url" @click.prevent="moreProducts(all_next_page_url)"> {{ $t('Load More') }} <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i></div>
                            </div>
                        </div>
                    </div>
                    </span>
    
    
                    <span v-else>
                        <div class="row search_page_row">
                            <div class="col-md-12 product_not_found">
                                <img @error="imageLoadError" src="/images/no-item-in-cart.gif" alt="">
                                <h5>{{ $t('Product Not Found') }}</h5>
                            </div>
                        </div>
                    </span>
    
                </div>
            </div>
        </div>
    </section>
    
    
    
    <section id="search-page" v-else>
        <div class="container">
            <div class="row w_100per padding_problem">
                <div class="shimmer width_problem"> 
                    <div class="h_5 w_100per mb_5 mb_5"></div>
                </div>
            </div>
    
    
            <div class="row mb_30">
                <div class="col-md-3 col-lg-3">
        
                    <div class="shimmer"><div class="h_4 w_100per"></div></div>
                    <div class="shimmer"><div class="h_3 w_10"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_3 w_10"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                    <div class="shimmer"><div class="h_2 w_100per"></div></div>
                </div>
                <div class="col-md-9 col-lg-9">
                    <div class="row">
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
                        <div class="col-md-3 col-md-3 mb-3"> <div class="shimmer"> <div class="h_20 w_100per"></div>  <div class="h_2 w_100per"></div>  <div class="h_2 w_100per"></div> <div class="h_3 w_48per mr_5"></div><div class="h_3 w_48per"></div></div> </div>
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
    import ProductGrid from './parts/ProductGrid';
    import QuickView from './parts/QuickView.vue';
    
    export default {
        mode:'history',
    
        data(){
            return{
                is_user: '',
                orders:{},
                baseurl:'',
                searchProduct:{},
                content:'',
                categories:{},
                childcategories:{},
                next_page_url: '',
                prev_page_url:'',
                brands:[],
                brand_next_page_url: '',
                brand_prev_page_url: '',
                loading:false,
                thumbnailUrl:'',
                all_next_page_url:'',
                all_prev_page_url:'',
                x:'',
                category:'',
                slug:'',
    
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
           load_categories(){
                axios.get(this.$baseUrl+'/api/v1/search-categories').then(response => {
                    this.categories = response.data.data;
    
                    
                    this.next_page_url = response.data.next_page_url;
                    this.prev_page_url = response.data.prev_page_url;
                });
            },
            load_brands(){
                axios.get(this.$baseUrl+'/api/v1/get-search-brands').then(response => {
                    this.brands = response.data.data;
                    this.brand_next_page_url = response.data.next_page_url;
                    this.brand_prev_page_url = response.data.prev_page_url;
                });
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
            async moreBrands(url) {
                let   { data }  = await axios.get(url);
                let c = data.data;
                 c.forEach(element => {
                     this.brands.push(element);
                 });
                 this.brand_next_page_url = data.next_page_url;
                 this.brand_prev_page_url = data.prev_page_url;
            },
            async lessBrands(url) {
                let   { data }  = await axios.get(url);
                let c = data.data;
                 c.forEach(element => {
                     this.brands.pop(element);
                 });
                 this.brand_next_page_url = data.next_page_url;
                 this.brand_prev_page_url = data.prev_page_url;
            },
    
    
    
            load_category_by_slug(){
                let slug = this.$route.params.slug;
                axios.get(this.$baseUrl+'/api/v1/get-category-by-slug/'+slug).then(response => {
                    this.category = response.data.category;
                    this.childcategories = response.data.categories;
                });
            },
    
            load_search_product(){
                let content = this.$route.params.content;
                this.slug = this.$route.params.slug;
    
                let categories = Array();
                $("input:checkbox[name=categorySelected]:checked").each(function() {
                    categories.push($(this).val());
                });
    
                let brands = Array();
                $("input:checkbox[name=brandSelected]:checked").each(function() {
                    brands.push($(this).val());
                });
                let formData = new FormData();
                formData.append('slug', this.$route.params.slug);
                formData.append('productspage', null);
                formData.append('content', content);
                formData.append('page', 1);
                formData.append('upazila_id', localStorage.getItem('upazail_id'));
                formData.append('categories', categories);
                formData.append('brands', brands);;
                formData.append('orederbyselect', $('.orederbyselect').val());
                formData.append('minprice', $('.minprice').val());
                formData.append('maxprice', $('.maxprice').val()); 
                axios.post(this.$baseUrl+'/api/v1/get-search-product', formData).then(response => {
                    this.searchProduct = response.data.products;
                    this.all_next_page_url = response.data.products.next_page_url;
                    this.all_prev_page_url = response.data.products.prev_page_url;
                    this.loading = true;
                    //console.log('load_search_product ===slug=='+this.slug);
                });
    
                
            },
    
    
            load_search_product_category_click(page=1){
                let content = this.$route.params.content;
                let categories = Array();
                $("input:checkbox[name=categorySelected]:checked").each(function() {
                    categories.push($(this).val());
                });
    
                let brands = Array();
                $("input:checkbox[name=brandSelected]:checked").each(function() {
                    brands.push($(this).val());
                });
    
                let formData = new FormData();
                if(categories.length > 0){
                   formData.append('slug', null);
                }else{
                    formData.append('slug', this.slug);
                }
                formData.append('content', content);
                formData.append('categories', categories);
                formData.append('brands', brands);;
                formData.append('orederbyselect', $('.orederbyselect').val());
                formData.append('minprice', $('.minprice').val());
                formData.append('maxprice', $('.maxprice').val()); 
                formData.append('page', page);
                formData.append('upazila_id', localStorage.getItem('upazail_id'));
    
                axios.post(this.$baseUrl+'/api/v1/get-search-product', formData).then(response => {
                    this.searchProduct = response.data.products;
                    this.all_next_page_url = response.data.products.next_page_url;
                    this.all_prev_page_url = response.data.products.prev_page_url;
                    this.loading = true;
                    //console.log('load_search_product_category_click running..!');
                });
            },
    
    
    
    
            async moreProducts(all_next_page_url) {
                let lang = localStorage.getItem("lang");
                if(lang == 'bn'){
                $('.load_more_btn').html('লোড হচ্ছে.. <span class="spinner-border spinner-border-sm"></span>');
                }else{
                $('.load_more_btn').html('Loading.. <span class="spinner-border spinner-border-sm"></span>');
                }
    
                var url_string = all_next_page_url; 
                var url = new URL(url_string);
                var page = url.searchParams.get("page"); //This is page no.
    
    
                let content = this.$route.params.content;
                let categories = Array();
                $("input:checkbox[name=categorySelected]:checked").each(function() {
                    categories.push($(this).val());
                });
    
                let brands = Array();
                $("input:checkbox[name=brandSelected]:checked").each(function() {
                    brands.push($(this).val());
                });
                let formData = new FormData();
                formData.append('slug', this.$route.params.slug);
                formData.append('content', content); localStorage.setItem("serach_content", content);
                formData.append('page', page);
                formData.append('upazila_id', localStorage.getItem('upazail_id'));
                formData.append('categories', categories); localStorage.setItem("serach_categories", categories);
                formData.append('brands', brands); localStorage.setItem("serach_brands", brands);
                formData.append('orederbyselect', $('.orederbyselect').val());  localStorage.setItem("serach_orederbyselect", $('.orederbyselect').val());
                formData.append('minprice', $('.minprice').val()); localStorage.setItem("serach_minprice", $('.minprice').val());
                formData.append('maxprice', $('.maxprice').val()); localStorage.setItem("serach_maxprice", $('.maxprice').val());
                await axios.post(this.$baseUrl+'/api/v1/get-search-product', formData).then(response => {
                    let c = response.data.products.data;
                    let that = this;
                    c.forEach(element => {
                        that.searchProduct.data.push(element);
                        $('.load_more_btn').attr('disabled', false);
                        let lang = localStorage.getItem("lang");
                        if(lang == 'bn'){
                            $('.load_more_btn').html('আরো লোড করুন <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
                        }else{
                            $('.load_more_btn').html('Load More <i class="fa fa-chevron-down ml-3" aria-hidden="true"></i>');
                        }
                    });
    
    
                    this.loading = true;
                    this.all_next_page_url = response.data.products.next_page_url;
                    this.all_prev_page_url = response.data.products.prev_page_url;
    
                   // console.log('moreProducts running..!');
                });
            },
    
            scrollToTop() {
                window.scrollTo(0,0);
            }
         
    
        },
        watch:{
            $route(to, from){
                this.load_search_product();
                this.scrollToTop();
               // this.load_search_product_category_click();
                this.load_category_by_slug();
            },
            
        },
        computed:{
            logged_in_user(){
                return this.$store.getters.getLoadedUser.user;
            },
        },
        mounted(){
            this.load_search_product();
            //this.load_search_product_category_click();
            this.load_category_by_slug();
            this.scrollToTop();
            this.thumbnailUrl = this.$thumbnailUrl;
            // this.$forceUpdate(
            //     setTimeout(() => {
            //          this.load_search_product();
            //          console.log('forceUpdate exicuting..');
            //     },5000)
            //  );
    
            this.baseurl = this.$baseUrl;
            this.load_brands();
           
           this.load_categories();
           //this.load_categories_onChange();
           document.title = "MabiY Shop | Products";
        }
    
    }
    </script>