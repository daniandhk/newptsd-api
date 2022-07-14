import store from '../../store';
import * as api from '../../api';

export default [
    {
        path: '/login',
        name: 'login',
        component: () => import('../../views/pages/auth/login'),
        meta: {
            beforeResolve(routeTo, routeFrom, next) {
                // If the user is already logged in
                if (store.getters.getLoggedUser) {
                    // Redirect to the home page instead
                    return api.validateUser().then(response => {
                        response.data.data.access_token = store.getters.getLoggedUser.access_token
                        store.commit('LOGGED_USER', response.data.data)

                        let role = store.getters.getRoleUser
                        if (!role) {
                            store.commit('ROLE_USER', response.data.data.role)
                        }
                        next({ name: 'home' })
                    })
                    .catch(error => {
                        store.dispatch('logOut')
                        next({params: { tokenExpired: true }})
                    })
                    
                } else {
                    // Continue to the login page
                    next()
                }
            },
        },
    },
    {
        path: '/register/:token?',
        name: 'register',
        component: () => import('../../views/pages/auth/register'),
        meta: {
            beforeResolve(routeTo, routeFrom, next) {
                // If the user is already logged in
                if (store.getters.getLoggedUser) {
                    // Redirect to the home page instead
                    return api.validateUser().then(response => {
                        response.data.data.access_token = store.getters.getLoggedUser.access_token
                        store.commit('LOGGED_USER', response.data.data)

                        let role = store.getters.getRoleUser
                        if (!role) {
                            store.commit('ROLE_USER', response.data.data.role)
                        }
                        next({ name: 'home' })
                    })
                    .catch(error => {
                        store.dispatch('logOut')
                        next({params: { tokenExpired: true }})
                    })
                    
                } else {
                    // Continue to the register page
                    next()
                }
            },
        },
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../../views/pages/auth/forgot-password'),
    },
    {
        path: '/reset-password/:token',
        name: 'reset-password',
        component: () => import('../../views/pages/auth/reset-password'),
    },
    {
        path: '/logout',
        name: 'logout',
        meta: {
            authRequired: true,
            beforeResolve(routeTo, routeFrom, next) {
                let loggedUser = store.getters.getLoggedUser
                if(loggedUser){
                    api.logout().then(response => {
                        //
                    })
                    .catch(error => {
                        //
                    });
                }
                store.dispatch('logOut')
                const authRequiredOnPreviousRoute = routeFrom.matched.some(
                    (route) => route.push('/login')
                )
                // Navigate back to previous page, or home as a fallback
                next(authRequiredOnPreviousRoute ? { name: 'home' } : { ...routeFrom })
            },
        },
    },
    {
        path: '/settings/change-password',      
        name: 'change-password',
        meta: {
            authRequired: true,
        },
        component: () => import('../../views/pages/settings/change-password')
    },
]
