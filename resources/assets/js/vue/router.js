import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from './components/Home';
import Cart from './components/Cart';
import Wishlist from './components/Wishlist';
import Checkout from './components/Checkout';
import Product from './components/Product';
import Login from './components/Login';
import Forgotpassword from './components/Forgotpassword';
import Signup from './components/Signup';
import Myaccount from './components/Myaccount';
import Myorder from './components/Myorder';
import Productquatation from './components/Productquatation';
import Myaddress from './components/Myaddress';
import Changepassword from './components/Changepassword';
import Search from './components/Search';
import Promotion from './components/Promotion';
import Category from './components/Category';
import Seller from './components/Seller';
import corporate from './components/Corporate';

import Compare from './components/Compare';
import orderDetails from './components/OrderDetails';
import quatationDetails from './components/QuatationDetails';
import vendor from './components/Vendor';
import success from './components/Success';
import failed from './components/Failed';
import updateaddress from './components/MyAddressUpdate';
import allshops from './components/shop/Allshops';
import Shop from './components/Shop';
import products from './components/Products';
import voucher from './components/Voucher';
import offer from './components/Offer';
import offerProduct from './components/Offerproduct';
import offerpromotional from './components/OfferPromotional';
import notfound from './components/404';
import page from './components/page';
import blog from './components/Blog';
import blogsingle from './components/BlogSingle';
import Myvoucher from './components/Myvoucher';
import Mycoupon from './components/Mycoupon';
import Myaffiliate from './components/Myaffiliate';
import contact from './components/Contact';
import returnproduct from './components/Return';
import track from './components/Track';
import groceries from './components/Groceries';
import FlashDeals from './components/FlashDeals';
import FlashDeal from './components/FlashDeal';
import CategoryWiseFlashdeal from './components/CategoryWiseFlashdeal';


 

Vue.use(VueRouter);
const routes = [
    { path: '/', name: 'home', component: Home },
    { path: '/cart', name: 'cart', component: Cart },
    { path: '/wishlist', name: 'wishlist', component: Wishlist },
    { path: '/checkout', name: 'checkout', component: Checkout },
    { path: '/product/:slug', name: 'product', component: Product },
    { path: '/login', name: 'login', component: Login },
    { path: '/forgot-password', name: 'forgotpassword', component: Forgotpassword },
    { path: '/sign-up', name: 'sign-up', component: Signup },
    { path: '/my-account', name: 'myaccount', component: Myaccount },
    { path: '/my-address', name: 'myaddress', component: Myaddress },
    { path: '/my-order', name: 'myorder', component: Myorder },
    { path: '/my-product-quatation', name: 'productquatation', component: Productquatation },
    { path: '/my-vouchers', name: 'myavouchers', component: Myvoucher },
    { path: '/my-coupons', name: 'mycoupons', component: Mycoupon },
    { path: '/my-affiliate', name: 'myaffiliate', component: Myaffiliate },
    { path: '/flash-deals', name: 'flashdeals', component: FlashDeals },

    { path: '/flash-deal/:slug', name: 'flashdeal', component: FlashDeal },
    { path: '/category-wise-flash-deal/:category_id/:slug', name: 'categorywiseflashdeal', component: CategoryWiseFlashdeal },
    

    { path: '/groceries', name: 'groceries', component: groceries },
    { path: '/shop/:slug', name: 'shop', component: Shop },
    { path: '/change-password', name: 'changepassword', component: Changepassword },
    { path: '/search/:content', name: 'search', component: Search },
    { path: '/promotion/:product_type', name: 'promotion', component: Promotion },
    { path: '/category/:slug', name: 'category', component: Category },
    { path: '/sellers', name: 'sellers', component: Seller },
    { path: '/corporate-user', name: 'corporate', component: corporate },
    
    { path: '/compare-list', name: 'compare-list', component: Compare },
    { path: '/order-details/:id', name: 'orderDetails', component: orderDetails },
    { path: '/quatation-details/:id', name: 'quatationDetails', component: quatationDetails },
    { path: '/vendor-register', name: 'vendor', component: vendor },
    { path: '/success', name: 'success', component: success },
    { path: '/fail', name: 'failed', component: failed },
    { path: '/cancel', name: 'cancel', component: failed },
    { path: '/update-address/:address_id', name: 'updateaddress', component: updateaddress },
    { path: '/all-shops', name: 'allshops', component: allshops },
    { path: '/products', name: 'products', component: products },
    { path: '/vouchers', name: 'voucher', component: voucher },
    { path: '/offers', name: 'offer', component: offer },
    { path: '/offer/products/:slug', name: 'offerProduct', component: offerProduct },
    { path: '/offer/promotional', name: 'offerpromotional', component: offerpromotional },
    { path: '/pages/:slug', name: 'pages', component: page},
    { path: '/blog/:slug', name: 'blogsingle', component: blogsingle},
    { path: '/blog', name: 'blog', component: blog},
    { path: '/help-center', name: 'contact', component: contact },
    { path: '/order-return/:order_id', name: 'return', component: returnproduct, props: true },
    { path: '/order-track/:order_id', name: 'track', component: track},
    { path: '*', name: 'notfound', component: notfound },
    
];





export default new VueRouter({
    mode: 'history',
    routes,
});