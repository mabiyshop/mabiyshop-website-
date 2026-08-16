<template>
<div>

<section v-if="loading" id="register-page">
    <div class="container static_page">
        <div class="row">
           <div class="col-md-12  text-center pb-3 static_page_title">
                <h2> {{ pageTitle }} </h2>
           </div>
        </div>
        <div class="row">

            <div id="default_page_content" v-html="pageContent"></div>


        </div>
    </div>
</section>
<section v-else id="offer_page">
    <div class="container offer_shimmer page_shimmer">
        <div class="row w_100per padding_problem">
            <div class="shimmer width_problem"> 
                <div class="h_5 w_100per mb_40 mt_30"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 padding_problem"> 
                <div class="shimmer"> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
                    <div class="h_2 w_100per mb_15"></div> 
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
			pageContent: '',
            loading:false,
		}
	},
	components:{
		Leftsidebar,
        pagination
	},
	methods:{
        load_page_content(){

            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    "X-localization": localStorage.getItem("lang"),
                }
            }


            let slug = this.$route.params.slug;
            axios.get(this.$baseUrl+'/api/v1/get-page-content/'+slug,axiosConfig).then(response => {
                if(response.data.status == '0'){
                    this.$router.push({name:'notfound'});
                }else{
                    this.pageContent = response.data.content.description;
                    this.pageTitle = response.data.content.title;
                }
                this.loading = true;
            });
        },
        scrollToTop(){
            window.scrollTo(0,0);
        },
	},
    watch:{
        $route(to, from){
            this.load_page_content();
            this.scrollToTop();
        }
    },
	mounted(){
        this.baseurl = this.$baseUrl;
        this.load_page_content();
       //this.load_categories_onChange();
       document.title = "MabiY Shop | "+this.$route.params.slug;
       this.scrollToTop();
	}

}
</script>