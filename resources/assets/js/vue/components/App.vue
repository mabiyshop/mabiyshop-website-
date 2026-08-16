<template>
    <div :class="'page_'+$route.name" id="app">
	
		<compHeader :homeCategories="fname"></compHeader>
		<router-view />
		<compFooter></compFooter>
	</div>
</template>



<script>
import compHeader from './default/Header';
import compFooter from './default/Footer';



export default {
	data(){
		return{
			fname:'',
			loadedData:[],
		}
	},
	components:{
		compHeader,
		compFooter
	},
	methods:{
		scrollToTop(){
			window.scrollTo(0,0);
		}
	},
	mounted(){
		this.scrollToTop();
		this.$store.dispatch('loadedUser');
		this.$store.dispatch('loadedCart');
		this.$store.dispatch('loadedCompares');
		
		
	},
}

</script>





