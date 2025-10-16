import { createRouter, createWebHistory } from 'vue-router'
import Default from '@/layouts/Default.vue'
import { useAuthStore } from '@/stores/auth'

const routes = [
    {
        path: '/',
        component: Default,
        meta: { 
            requiresAuth: true
        },
        children: [
            { 
                path: '', 
                name: 'Home', 
                component: () => import('@/views/Welcome.vue'),
                meta : {
                    requiresAuth : true
                } 
            },
            { 
                path: 'profile', 
                name: 'Profile', 
                component: () => import('@/views/Profile.vue'),
                meta : {
                    requiresAuth : true
                } 
            },
            { 
                path: 'admin', 
                meta : {
                    requiresAuth : true
                },
                children : [
                    { 
                        path: 'users', 
                        name: 'Users', 
                        component: () => import('@/views/admin/Users.vue'),
                        meta : {
                            requiresAuth : true
                        }
                    },
                    { 
                        path: 'users/edit/:id', 
                        name: 'User edit', 
                        component: () => import('@/views/admin/UserEdit.vue'),
                        props : true,
                        meta : {
                            requiresAuth : true
                        }
                    },
                    { 
                        path: 'pages', 
                        name: 'Pages', 
                        component: () => import('@/views/admin/Pages.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'menus', 
                        name: 'Menus', 
                        component: () => import('@/views/admin/Menus.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'roles', 
                        name: 'Roles', 
                        component: () => import('@/views/admin/Roles.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'permissions', 
                        name: 'Permissions', 
                        component: () => import('@/views/admin/Permissions.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'profiles', 
                        name: 'Profiles', 
                        component: () => import('@/views/admin/Profiles.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                ] 
            },
            { 
                path: 'pasos-y-pedales', 
                meta : {
                    requiresAuth : true
                },
                children : [
                    { 
                        path: 'recepcion', 
                        name: 'Recepcion', 
                        component: () => import('@/views/UnidadConvivenciaSocial/PasosPedales/Recepcion.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'asignaciones', 
                        name: 'Asignaciones', 
                        component: () => import('@/views/UnidadConvivenciaSocial/PasosPedales/Asignaciones.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'autorizaciones', 
                        name: 'Autorizaciones', 
                        component: () => import('@/views/UnidadConvivenciaSocial/PasosPedales/Autorizaciones.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'linea-de-tiempo', 
                        name: 'Time line', 
                        component: () => import('@/views/UnidadConvivenciaSocial/PasosPedales/TimeLine.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                    { 
                        path: 'formulario-de-solicitud', 
                        name: 'Solicitud', 
                        component: () => import('@/views/UnidadConvivenciaSocial/PasosPedales/Solicitud.vue'),
                        meta : {
                            requiresAuth : true
                        } 
                    },
                ] 
            },
            
        ]
    },
    { 
        path: '/login', 
        name: 'Login', 
        component: () => import('@/views/Login.vue') 
    },
    { 
        path: '/:catchAll(.*)', 
        component: () => import('@/views/NotFound.vue') 
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
})

router.beforeEach((to) => {
    const authStore = useAuthStore()
    
    if (to.meta.requiresAuth && !authStore.isLoggedIn) {
        return { name: 'Login' }
    }

    if (to.name === 'Login' && authStore.isLoggedIn) {
        return { name: 'Home' }
    }

    return true
})

export default router
