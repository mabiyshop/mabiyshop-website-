import Vue from 'vue';
import Vuex, { Store } from 'vuex';
import axios from 'axios';
import { each } from 'lodash';


Vue.use(Vuex);

var base_url = 'https://api.mabiyshop.com';
 

export default new Store({
    state: {
        message: 'Welcome',
        user: {},
        authenticated: false,
        loaded_user: {},
        loaded_cart: {},
        loaded_wishlist: {},
        loaded_compare: {},
        loaded_notifications: {},
        loaded_ssr: {},
        loaded_voucher: {},
        loaded_useable_voucher: {},
    },
    getters: {

        getMessage(state) {
            return state.message;
        },
        getUser(state) {
            return state.user;
        },
        getAuthenticated(state) {
            return state.authenticated;
        },
        getLoadedUser(state) {
            return state.loaded_user;
        },
        getLoadedSsr(state) {
            return state.loaded_ssr;
        },
        getLoadedCart(state) {
            return state.loaded_cart;
        },
        getLoadedWishlist(state) {
            return state.loaded_wishlist;
        },
        getLoadedCompare(state) {
            return state.loaded_compare;
        },
        getLoadedVocher(state) {
            return state.loaded_voucher;
        },
        getLoadedUseableVocher(state) {
            return state.loaded_useable_voucher;
        },
        
        getLoadedNotifications(state) {
            return state.loaded_notifications;
        },
        

    },
    mutations: {
        SET_USER(state, data) {
            state.user = data;
        },
        SET_AUTHENTICATED(state, data) {
            state.authenticated = data;
        },
        LOADED_USER(state, data) {
            state.loaded_user = data;
        },
        LOADED_CART(state, data) {
            state.loaded_cart = data;
        },
        LOADED_WISHLIST(state, data) {
            state.loaded_wishlist = data;
        },
        LOADED_COMPARE(state, data) {
            state.loaded_compare = data;
        },
        LOADED_VOUCHER(state, data) {
            state.loaded_voucher = data;
        },
        LOADED_USEABLE_VOUCHER(state, data) {
            state.loaded_useable_voucher = data;
        },
        LOADED_NOTIFICATIONS(state, data) {
            state.loaded_notifications = data;
        },

        LOADED_SSR(state, data) {
            state.loaded_ssr = data;
        },

    },
    actions: {
        loadedUser({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/inituser', axiosConfig).then((response) => {
                localStorage.setItem('userID',response.data.user.id); 
                localStorage.setItem('userName',response.data.user.name);
                commit('LOADED_USER', response.data);
            }).catch(() => {
                commit('LOADED_USER', []);
            })
        },
        loadedCart({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let session_key = localStorage.getItem("session_key");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/initcart?session_key='+session_key, axiosConfig)
                .then((response) => {
                    commit('LOADED_CART', response.data);
                    dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
                    const carts = response.data.cart || [];  // Make sure response.data.products is an array
                    var transformedProducts =[];
                    
                    $.each(carts, function (index, value) {
                        $.each(value.items, function (ind, val) {
                            let array = {
                                        'name': val.product.title,
                                        'id': val.product_id,
                                        'price': val.price,
                                        'brand': val.product.brand_title,
                                        'category': val.product.category_title,
                                        'variant': val.variable_options.Weight,
                                        'quantity': val.qty,
                                        'coupon': ''
                                    };
                            transformedProducts.push(array);
                        })
                    });

                    dataLayer.push({
                        event: "initial_checkout",
                        ecommerce: {
                            'Initial_checkout': {
                            'actionField': {
                                'id': '',                         // Transaction ID. Required for purchases and refunds.
                                'affiliation': '',
                                'revenue': response.data.sub_total,                     // Total transaction value (incl. tax and shipping)
                                'tax': response.data.vat,
                                'shipping': response.data.shipping_cost,
                                'coupon': ''
                            },
                            'products': transformedProducts
                            }
                        }
                    });
                }).catch(() => {
                    commit('LOADED_CART', null);
                })
        },
        loadedWishlist({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let session_key = localStorage.getItem("session_key");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/initwishlist?session_key='+session_key, axiosConfig)
            .then((response) => {
                commit('LOADED_WISHLIST', response.data);
            }).catch(() => {
                commit('LOADED_WISHLIST', null);
            })
        },
        loadedCompares({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let session_key = localStorage.getItem("session_key");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/initCompare?session_key='+session_key, axiosConfig)
                .then((response) => {
                    commit('LOADED_COMPARE', response.data);
                }).catch(() => {
                    commit('LOADED_COMPARE', null);
                })
        },
        loadedVoucher({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/initcollectedvoucher', axiosConfig)
                .then((response) => {
                    commit('LOADED_VOUCHER', response.data);
                }).catch(() => {
                    commit('LOADED_VOUCHER', null);
                })
        },
        loadedUsableVoucher({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/inituseablevoucher', axiosConfig)
                .then((response) => {
                    commit('LOADED_USEABLE_VOUCHER', response.data);
                }).catch(() => {
                    commit('LOADED_USEABLE_VOUCHER', null);
                })
        },
        


        loadedNotifications({ commit, dispatch }) {
            let token = localStorage.getItem("token");
            let axiosConfig = {
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    "Access-Control-Allow-Origin": "*",
                    'Authorization': 'Bearer ' + token
                }
            }
            return axios.get(base_url + '/api/v1/get/customer/notifications', axiosConfig)
                .then((response) => {
                    commit('LOADED_NOTIFICATIONS', response.data);
                }).catch(() => {
                    commit('LOADED_NOTIFICATIONS', null);
                })
        }
    }


});
