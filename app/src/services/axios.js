import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'
import { handleError, setToast } from '@/helpers'


axios.defaults.withCredentials = true
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

axios.interceptors.request.use(config => {
    const authStore = useAuthStore()
    if (authStore.accessToken) {
        config.headers.Authorization = `Bearer ${authStore.accessToken}`
    }
    return config
}, error => Promise.reject(error))

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401 && error.config.url !== '/login') {
            const authStore = useAuthStore()
            authStore.logout()
            router.replace({ name: 'Login' })
        }

        handleError(error)
        
        return Promise.reject(error)
    }
)

export default axios
