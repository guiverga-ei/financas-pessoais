import { createRouter, createWebHistory } from 'vue-router'
// import HomeView from '../views/HomeView.vue'

import Transactions from '../components/TransactionsPage.vue'
import Categories from '../components/CategoriesPage.vue'
// import Home from '../components/Home.vue'
import Dashboard from '@/components/DashboardPage.vue'
import AccountsList from '@/components/AccountsPage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Dashboard
    },
    {
      path: '/transactions',
      name: 'Transactions',
      component: Transactions
    },
    {
      path: '/categories',
      name: 'Categories',
      component: Categories
    },
    {
      path: '/accounts',
      name: 'Accounts',
      component: AccountsList
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
    }
  ]
})

export default router
