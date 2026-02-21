import { createRouter, createWebHistory } from 'vue-router'
import Products from '@/views/Products.vue'

export default createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'products', component: Products },
  ],
})
