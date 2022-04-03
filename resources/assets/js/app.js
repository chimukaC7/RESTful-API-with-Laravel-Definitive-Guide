
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//register the vue components
Vue.component(
	'passport-personal-access-tokens',//name
	require('./components/passport/PersonalAccessTokens.vue'));

Vue.component(
	'passport-clients',//name
	require('./components/passport/Clients.vue'));

Vue.component(
	'passport-authorized-clients',//name
	require('./components/passport/AuthorizedClients.vue'));

//testing

//sdcfdg

const app = new Vue({
    el: '#app'
});
