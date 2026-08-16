<template>
    <div>
        <section id="banner" style="background-image: url('/images/contact.jpg');">
            <div class="banner_overlay">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center banner_text">
                        <h1>{{ $t('Help Center') }}</h1>
                    </div>
                </div>
            </div>
            </div>
        </section>
    
    
    
    <section class="container mt-5">
    
       <div class="row">
            <!--Grid column-->
            <div class="col-sm-12 col-md-7">
             <!--Google map-->
             <div class="mb-4">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3650.051302001947!2d90.36295381496882!3d23.816774692154414!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c1fc448cf44f%3A0xfcd29aadc12d166!2sMabiY%20Shop!5e0!3m2!1sen!2sbd!4v1672466529339!5m2!1sen!2sbd" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
              </div>
             <!--Buttons-->
             <div class="row text-center">
                <div class="col-md-4">
                   <a class="site_bg px-3 py-2 rounded text-white mb-2 d-inline-block"><i class="fa fa-map-marker"></i></a>
                   <p>{{ site_info.cnf_address }}</p>
                </div>
                <div class="col-md-4">
                    <a class="site_bg px-3 py-2 rounded text-white mb-2 d-inline-block"><i class="fa fa-phone"></i></a></br>
                    <a v-if="site_info.cnf_phone1" class="text-dark hedaer_phone" :href="'tel:'+site_info.cnf_phone1"> <b>{{ site_info.cnf_phone1 }}</b></a><br>
                    <a v-if="site_info.cnf_phone2" class="text-dark hedaer_phone" :href="'tel:'+site_info.cnf_phone2"> <b>{{ site_info.cnf_phone2 }}</b></a>
                </div>
                <div class="col-md-4">
                   <a class="site_bg px-3 py-2 rounded text-white mb-2 d-inline-block"><i class="fa fa-envelope"></i></a>
                   <p>{{ site_info.cnf_email }}</p>
                </div>
             </div>
            </div>
            <!--Grid column-->
    
    
          <div class="col-sm-12 mb-4 col-md-5" id="contactus_form">
            <div class="card border_site rounded-0">
                <div class="card-header p-0">
                   <div class="site_bg text-white text-center py-2">
                      <h3><i class="fa fa-envelope"></i> {{ $t('Write Your Problem') }} </h3>
                      <p class="m-0">{{ $t('Send your message for any kind of problem') }} </p>
                   </div>
                </div>
    
               
                    <div class="card-body p-3">
                        <div class="form-group">
                        <label> {{ $t('Full Name') }} <span style="color:#f00">*</span> </label>
                        <div class="input-group">
                            <input value="" type="text" name="name" class="form-control" id="name" :placeholder="$t('Full Name')+'..'">
                            <div class="validation_error" v-if="errors.name" v-html="errors.name[0]" />
                        </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('Phone') }} <span style="color:#f00">*</span></label>
                            <div class="input-group mb-2 mb-sm-0">
                                <input type="text" name="phone" class="form-control" id="phone" :placeholder="$t('Phone')+'..'">
                                <div class="validation_error" v-if="errors.phone" v-html="errors.phone[0]" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('Email') }}</label>
                            <div class="input-group mb-2 mb-sm-0">
                                <input type="email" name="email" class="form-control" id="email" :placeholder="$t('Email')+'..'">
                                <div class="validation_error" v-if="errors.email" v-html="errors.email[0]" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label> {{ $t('Subject') }} </label>
                            <div class="input-group mb-2 mb-sm-0">
                                <input type="text" name="subject" class="form-control" id="subject" :placeholder="$t('Subject')+'..'">
                                <div class="validation_error" v-if="errors.subject" v-html="errors.subject[0]" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ $t('Messages') }} <span style="color:#f00">*</span></label>
                            <div class="input-group mb-2 mb-sm-0">
                                <textarea type="text" class="form-control" name="message" id="message" :placeholder="$t('Write your message')" rows="3" cols=""></textarea>
                                <br>
                                <div class="validation_error" v-if="errors.message" v-html="errors.message[0]" />
                            </div>
                        </div>
                        <div class="text-center">
                            <input type="submit" name="submit" :value="$t('SEND')" @click.prevent="contactMessage()" class="btn btn-primary btn-block rounded-0 py-2">
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
    import Vue from 'vue';
    
    
    export default {
        data(){
            return{
                show:false,
                errors:'',
                site_info:'',
            }
        },
    
        methods:{
            contactMessage(){
                let formData = new FormData();
                //Personal info
                formData.append('name', $('#name').val());
                formData.append('phone', $('#phone').val());
                formData.append('email', $('#email').val());
                formData.append('subject', $('#subject').val());
                formData.append('message', $('#message').val());
                axios.post(this.$baseUrl+'/api/v1/contact', formData).then(response => {
                    if(response.data.status == 1){
                        swal({
                            title: 'Message sent successfully.',
                            icon: "success",
                            timer: 3000
                        }).then(()=>{
                            $('#name').val('');
                            $('#phone').val('');
                            $('#email').val('');
                            $('#subject').val('');
                            $('#message').val('');
                            $('.validation_error').hide();
    
                            
                        });
                    }else{
                        this.errors = response.data.message;
                    }
                });
            },
    
            site_information(){
                let axiosConfig = {
                headers: {
                    'X-localization': localStorage.getItem('lang')
                }
                }
                axios.get(this.$baseUrl+'/api/v1/site-info', axiosConfig).then(response => {
                    this.site_info = response.data;
                    this.navbars = response.data.navbars
                    if(this.site_info.social_login == 1){
                    this.initFacebook();
                    this.logInWithGoogle();
                    }
                    
                });
            },
    
            scrollToTop(){
                window.scrollTo(0,0);
            }
    
        },
        mounted(){
            this.scrollToTop();
            document.title = "MabiY Shop | Help-Center"; 
            this.site_information();
        }
    
    }
    </script>
    
    
    
    
    
    
    
    