// Import
import axios from 'axios'
import store from './store'
import Vue from 'vue'
import Cookies from 'js-cookie'

// Create
const service = axios.create({
    baseURL: '/api'
})

// Token
if (store.getters.getLoggedUser) {
    service.defaults.headers.common['Authorization'] = 'Bearer ' + store.getters.getLoggedUser.access_token
}

service.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

service.defaults.withCredentials = true;

// Request Interceptor
service.interceptors.request.use(config => {
    store.dispatch('displayLoader', true)

    // Set Token
    if (store.getters.getLoggedUser) {
        config.headers["Authorization"] = 'Bearer ' + store.getters.getLoggedUser.access_token;
    }

    // Set Cookie
    if ((
            config.method == 'post' || 
            config.method == 'put' || 
            config.method == 'delete'
            /* other methods you want to add here */
        ) &&
        !Cookies.get('XSRF-TOKEN')) {
        return setCSRFToken()
            .then(response => config)
            .catch(error => {
                //
            })
    }

    return config
}, error => {
    store.dispatch('displayLoader', false)

    return Promise.reject(error)
})

// Get Cookie
const setCSRFToken = () => {
    return service.get('/sanctum/csrf-cookie'); // resolves to '/api/csrf-cookie'.
}

// Export axios
export default service