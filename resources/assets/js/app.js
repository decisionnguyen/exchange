
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


Vue.component('example', require('./components/ExampleComponent.vue'));
Vue.component('checkrobotmodal', require('./components/CheckRobotModal.vue'));
Vue.component('markets', require('./components/Markets.vue'));
Vue.component('trade', require('./components/Trade.vue'));
Vue.component('trademyorder', require('./components/TradeMyOrder.vue'));
Vue.component('trademarkethistories', require('./components/TradeMarketHistories.vue'));
Vue.component('wallets', require('./components/Wallets.vue'));
Vue.component('accountsidebar', require('./components/AccountSidebar.vue'));
Vue.component('infomation', require('./components/Infomation.vue'));
Vue.component('changepassword', require('./components/ChangePassword.vue'));
Vue.component('twoauth', require('./components/TwoAuth.vue'));
Vue.component('withdrawsetting', require('./components/WithdrawSetting.vue'));
Vue.component('smsverification', require('./components/SmsVerification.vue'));
Vue.component('kycverification', require('./components/KycVerification.vue'));

const app = new Vue({
    el: '#app',
    data: {
    	SITE_URL : 'https://geniota.com'
    },
});