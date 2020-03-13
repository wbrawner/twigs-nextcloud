import Vue from 'vue'
import App from './App.vue'
import store from './store'
import router from './router'

Vue.prototype.t = window.t
Vue.prototype.n = window.n
Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount('#twigs-app')