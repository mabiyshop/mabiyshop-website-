<template>
    <div>
        <div class="row profile">
			<div class="col-md-1">
				<span v-if="profile_picture">
				<img @error="imageLoadError" :src="baseurl+'/assets/images/'+profile_picture">
				</span>
				<span v-else>
					<img @error="imageLoadError" :src="baseurl+'/assets/images/'+userData.avatar" >
				</span>
			</div>
			<div class="col-md-11">
				<div class="username">
					<b v-if="user.firstname">{{ user.firstname }}</b> <b v-else>{{ userData.firstname  }}</b>
					<p v-if="user.email">{{ user.email }}</p> <p v-else>{{ userData.email }}</p>
				</div>
			</div>
		</div>
    </div>
</template>


<script>
import Form from 'vform'
import axios from 'axios'
import swal from 'sweetalert';
export default {
	data(){
		return{
			userData:'',
			image:'',
			profile_picture:'',
			baseurl:'',
			user:'',
		}
	},
	methods:{
		imageLoadError(event){
			event.target.src = "/images/notfound.png";
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
			}).catch(function(){
			  swal ( "Oops" ,  'Something went wrong',  "error" );
			});
		}
	},
	computed:{
		logged_in_user(){
			return this.$store.getters.getLoadedUser.user;
		}
	},

	mounted(){
		this.getUserDetails();
		this.baseurl = this.$baseUrl;
	}
}
</script>