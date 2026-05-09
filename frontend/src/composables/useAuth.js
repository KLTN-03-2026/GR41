import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

export function useAuth() {
  const store = useAuthStore()
  const { user, token, isLoggedIn, isAdmin, isTeacher } = storeToRefs(store)
  const roleSlug = computed(() => user.value?.role?.slug ?? null)

  return {
    user,
    token,
    isLoggedIn,
    isAdmin,
    isTeacher,
    roleSlug,
    login: store.login,
    register: store.register,
    logout: store.logout,
    fetchMe: store.fetchMe,
  }
}
