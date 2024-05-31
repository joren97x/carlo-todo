import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const routes = [
	{
		path: '/',
		component: () => import('../components/Index.vue'),
        meta: {
            requiresAuth: true
        }
	},
	{
		path: '/login',
		name: 'login',
		component: () => import('../components/Login.vue')
	},
	{
		path: '/register',
		name: 'register',
		component: () => import('../components/Register.vue')
	},
]

const router = createRouter({
	history: createWebHistory(),
	routes
})

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()
    if(to.matched.some(record => record.meta.requiresAuth)) {
        if(!authStore.isAuthenticated) {
            next('/login')
        }
        else {
            next()
        }
    }
    else {
        if(authStore.auth && (to.path == '/login' || to.path == '/register')) {
            console.log('yo wtf')
            next('/')
        }
        else {
            next()
        }
    }

})

export default router