<template>
    <div>
    
    <section v-if="loading"  class="mt-5">
        <div class="container all_sellers">
                <div class="row">
                    <div class="col-md-8 col-lg-9">
                            <div v-if="loadBlogs.length > 0" class="row"> 
                                <span v-if="loading2" style="width: 100%;">
                                <div class="row">
                                    <div v-for="(data, index) in loadBlogs" :key="index" class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <div class="single_blog p-2">
                                                <router-link :to="{ name: 'blogsingle', params: {slug: data.slug } }"> <img @error="imageLoadError" :src="thumbnailUrl+'/'+data.image" alt=""> </router-link>
                                                <div class="single_blog_description">
                                                    <router-link :to="{ name: 'blogsingle', params: {slug: data.slug } }"> <h5> {{ data.title }} </h5> </router-link>
                                                    <div v-if="data.specification" class="blog_html_text pb-2" v-html="data.specification.substr(0, 150)"></div>
                                                    <p class="text-right"><router-link :to="{ name: 'blogsingle', params: {slug: data.slug } }" class="btn btn-primary text-white"> View More </router-link></p>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                </span>
                                <span v-else  style="width: 100%;">
                                    <div v-if="loadBlogs.length > 0" class="row">
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                                    </div>
                                    <div v-else class="row promotion_page blog_not_found blog_not_found_blog_page">
                                        <div class="col-md-12">
                                                <img @error="imageLoadError" src="/images/no-blog.webp" alt="">
                                            <h4> {{ $t('Blog Not Found') }} </h4>
                                        </div>
                                    </div>
                                </span>


                                <div v-if="loadBlogs.length > 0" class="row product_pagination" style="justify-content: center;text-align: center;width: 100%;">
                                    <div class="col-md-12 text-center mb-3 mt-3">
                                        <div class="moreBtn">
                                           
                                            <div class="btn btn-primary load_more_btn" v-if="next_page_url" @click.prevent="moreBlogs(next_page_url)">Load more<i class="fa fa-chevron-down ml-3" aria-hidden="true"></i></div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="row promotion_page blog_not_found blog_not_found_blog_page">
                                    <div class="col-md-12">
                                            <img @error="imageLoadError" src="/images/no-blog.webp" alt="">
                                        <h4> {{ $t('Blog Not Found') }} </h4>
                                    </div>
                                </div>


                            </div>

                            <div v-else class="row promotion_page blog_not_found blog_not_found_blog_page">
                                <div class="col-md-12">
                                        <img @error="imageLoadError" src="/images/no-blog.webp" alt="">
                                    <h4> {{ $t('Blog Not Found') }} </h4>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-4 col-lg-3">
                            <div class="right_side_bar">
                                <h5>Latest Categories</h5>
                                <ul class="category_list">
                                    <li v-for="(catlist,index) in loadBlogsCat" :key="index">
                                        <div @click.prevent="load_category_wise_blog(catlist.slug)" class="text-primary sligle_blog_post">
                                            <div class="media mb-2">
                                                <span class="pr-3">
                                                    <img width="60px" :src="thumbnailUrl+'/'+catlist.icon" alt="image">
                                                </span>
                                                <div class="media-body">
                                                    <p class="mt-3">{{catlist.title}}</p>
                                                </div>
                                            </div>
                                       </div>
                                    </li>
                                </ul>
                            </div>
                    </div>
                </div>


        </div>
    </section>
    
    
    <section v-else  class="mt-5">
        <div class="container">
            <div class="row blog_shimmer">
                <div class="col-lg-9 col-md-8 col-lg-112">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                        <div class="col-12 col-sm-12 col-md-4 col-lg-4"> <div class="shimmer"> <div class="h_35 w_100per mt_20"></div></div> </div>
                    </div>

                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_10 w_100per mt_20"></div></div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_5 w_100per mt_20"></div></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_10 w_100per mt_20"></div></div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_5 w_100per mt_20"></div></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_10 w_100per mt_20"></div></div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_5 w_100per mt_20"></div></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_10 w_100per mt_20"></div></div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_5 w_100per mt_20"></div></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_10 w_100per mt_20"></div></div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_5 w_100per mt_20"></div></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_10 w_100per mt_20"></div></div>
                        </div>
                        <div class="col-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="shimmer"> <div class="h_5 w_100per mt_20"></div></div>
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
    
    
    
    export default {
        data(){
            return{
                loading:false,
                is_user: '',
                baseurl:'',
                loadBlogs:{},
                loadBlogsCat:{},
                next_page_url: '',
                prev_page_url:'',
                blogCategories:'',
                bycat:false,
                category:'',
                thumbnailUrl:'',
                loading2:true,
            }
        },
        components:{
            Leftsidebar,
            pagination
        },
        methods:{
            imageLoadError(event){
                event.target.src = "/images/notfound.png";
            },
            // categoryWiseBlog(id){
            //     let page = 1;
            //     axios.get(this.$baseUrl+'/api/v1/get-blog-by-id/'+id+"?page="+page).then(response => {
            //         this.loadBlogs = response.data.blogs.data;
            //         this.next_page_url = response.data.blogs.next_page_url;
            //         this.prev_page_url = response.data.blogs.prev_page_url;
            //         this.category = response.data.category;
                    
            //         this.bycat = true;
            //     });
            // },
            load_properties(page=1){
                let axiosConfig = {
                    headers: {
                    'X-localization': localStorage.getItem('lang')
                    }
                }
                axios.get(this.$baseUrl+'/api/v1/get-blogs/?page='+page, axiosConfig).then(response => {
                    this.loadBlogs = response.data.blog.data;
                    this.loadBlogsCat = response.data.category;
                    this.next_page_url = response.data.blog.next_page_url;
                    this.prev_page_url = response.data.blog.prev_page_url;
                    this.loading = true;
                });
            },
            load_category_wise_blog(slug){
                this.loading2 = false;
                let axiosConfig = {
                    headers: {
                    'X-localization': localStorage.getItem('lang')
                    }
                }
                axios.get(this.$baseUrl+'/api/v1/get-category-wise-blogs/'+slug, axiosConfig).then(response => {
                    this.loadBlogs = response.data.blog.data;
                    this.loadBlogsCat = response.data.category;
                    this.next_page_url = response.data.blog.next_page_url;
                    this.prev_page_url = response.data.blog.prev_page_url;
                    this.loading = true;
                    this.loading2 = true;
                });
            },
    
            async moreBlogs(url) {
                $('.load_more_btn').attr('disabled', true);
                let lang = localStorage.getItem("lang");
                if(lang == 'bn'){
                    $('.load_more_btn').html('লোড হচ্ছে.. <span class="spinner-border spinner-border-sm"></span>');
                }else{
                    $('.load_more_btn').html('Loading.. <span class="spinner-border spinner-border-sm"></span>');
                }
                let   { data }  = await axios.get(url);
                let c = data.blog.data;
                c.forEach(element => {
                    this.loadBlogs.push(element);
                    $('.load_more_btn').attr('disabled', false);
                    let lang = localStorage.getItem("lang");
                    if(lang == 'bn'){
                        $('.load_more_btn').html('লোড হচ্ছে.. <span class="spinner-border spinner-border-sm"></span>');
                    }else{
                        $('.load_more_btn').html('Loading.. <span class="spinner-border spinner-border-sm"></span>');
                    }
    
                });
                this.next_page_url = data.next_page_url;
                this.prev_page_url = data.prev_page_url;
            },
    
    
            async byCatPagination(url) {
                let   { data }  = await axios.get(url);
                let c = data.blogs.data;
                c.forEach(element => {
                    this.loadBlogs.push(element);
                });
                this.next_page_url = data.blogs.next_page_url;
                this.prev_page_url = data.blogs.prev_page_url;
            },
    
    
    
            scrollToTop() {
                window.scrollTo(0,0);
            },
        },
    
        watch:{
            $route(to, from){
                this.load_properties();
                this.scrollToTop();
            }
        },
    
        mounted(){
            this.scrollToTop();
            this.thumbnailUrl = this.$thumbnailUrl;
            this.load_properties();
            this.baseurl = this.$baseUrl;
            document.title = "MabiY Shop | Blog"; 
        }
    
    }
    </script>