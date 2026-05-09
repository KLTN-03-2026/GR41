import axios from 'axios'
import { API_BASE_URL, TOKEN_KEY } from '@/constants'
import { toast } from 'vue-sonner'

const http = axios.create({
  baseURL: API_BASE_URL,
  headers: { Accept: 'application/json' },
  timeout: 20000,
})

http.interceptors.request.use((config) => {
  const token = localStorage.getItem(TOKEN_KEY)
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

http.interceptors.response.use(
  (res) => {
    if (res.data && typeof res.data === 'object' && 'success' in res.data) {
      if (res.data.success) {
        const { success: _s, data, ...rest } = res.data
        return { ...res, data, _meta: rest }
      }
      return Promise.reject(res.data)
    }
    return res
  },
  async (err) => {
    const status = err.response?.status
    const payload = err.response?.data
    if (status === 401 && localStorage.getItem(TOKEN_KEY)) {
      const { useAuthStore } = await import('@/stores/auth')
      useAuthStore().forceLogout()
    }
    if (status === 403) toast.error('Bạn không có quyền truy cập')
    if (status >= 500) toast.error('Lỗi máy chủ, thử lại sau')
    return Promise.reject(payload || err)
  },
)

export default http
