import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import http from '@/services/http'
import { TOKEN_KEY, USER_KEY, ROLES, CHAT_STORAGE_KEY } from '@/constants'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem(USER_KEY) || 'null'))
  const token = ref(localStorage.getItem(TOKEN_KEY) || '')

  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role?.slug === ROLES.ADMIN)
  const isTeacher = computed(() => user.value?.role?.slug === ROLES.TEACHER)

  function forceLogout() {
    token.value = ''
    user.value = null
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
    import('@/router').then(({ default: router }) => {
      const name = router.currentRoute.value.name
      const authNames = ['login', 'register', 'forgot-password', 'reset-password']
      if (!authNames.includes(name)) {
        router.push({ name: 'login', query: { redirect: router.currentRoute.value.fullPath } })
      }
    })
  }

  async function login(email, password) {
    const res = await http.post('/auth/login', { email, password })
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem(TOKEN_KEY, token.value)
    localStorage.setItem(USER_KEY, JSON.stringify(user.value))
  }

  async function register(payload) {
    await http.post('/auth/register', payload)
    await login(payload.email, payload.password)
  }

  async function fetchMe() {
    const res = await http.get('/auth/me')
    user.value = res.data
    localStorage.setItem(USER_KEY, JSON.stringify(user.value))
  }

  async function logout(redirect = true) {
    try {
      await http.post('/auth/logout')
    } catch (e) {
      /* ignore */
    }
    token.value = ''
    user.value = null
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
    localStorage.removeItem(CHAT_STORAGE_KEY)
    if (redirect) {
      const { default: router } = await import('@/router')
      router.push('/login')
    }
  }

  return {
    user,
    token,
    isLoggedIn,
    isAdmin,
    isTeacher,
    login,
    register,
    fetchMe,
    logout,
    forceLogout,
  }
})
