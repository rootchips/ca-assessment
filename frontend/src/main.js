import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useUploadStore } from '@/stores/uploadStore'
import './style.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

const uploadStore = useUploadStore(pinia)
uploadStore.bindRealtime()

app.mount('#app')
