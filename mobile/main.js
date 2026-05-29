import Vue from 'vue'
import App from './App'
import SafeImage from './components/safe-image.vue'

Vue.config.productionTip = false
Vue.component('safe-image', SafeImage)

App.mpType = 'app'

const app = new Vue({
  ...App
})
app.$mount()
