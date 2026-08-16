


<script>
    export default { 
       methods: {
        logout(){
        this.$store.commit('SET_AUTHENTICATED', false);
        this.$store.commit('LOADED_USER', []);
        localStorage.removeItem("auth");
        localStorage.removeItem("cart");
        localStorage.removeItem("token");
        localStorage.removeItem("user_id");
        localStorage.setItem("cart", null);
        localStorage.setItem("auth", false);
        localStorage.setItem("token", false);
        this.$router.push({name:'login'});
        }
       }
};

</script>


