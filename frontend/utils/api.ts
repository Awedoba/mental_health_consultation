import axios, { AxiosInstance, AxiosError } from 'axios'

const config = useRuntimeConfig()

export const api: AxiosInstance = axios.create({
  baseURL: config.public.apiBase,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = useCookie('auth_token')
    if (token.value) {
      config.headers.Authorization = `Bearer ${token.value}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      const token = useCookie('auth_token')
      token.value = null
      navigateTo('/login')
    }
    return Promise.reject(error)
  }
)

export default api

