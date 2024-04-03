import {createApp} from 'vue'

import App from './App.vue'
import router from './router';
import { createPinia } from 'pinia'
import RadialProgressBar from "vue3-radial-progress";
const pinia = createPinia()

createApp(App).use(router).use(pinia).use(RadialProgressBar).mount('#app')




