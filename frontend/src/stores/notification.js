import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notification', () => {
  const unreadCount = ref(0)

  function setUnreadCount(n) {
    unreadCount.value = typeof n === 'number' ? n : 0
  }

  return { unreadCount, setUnreadCount }
})
