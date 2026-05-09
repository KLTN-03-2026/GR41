<script setup>
import { ref, computed, watchEffect } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { notificationService } from '@/services/notificationService'
import NotificationItem from '@/components/notification/NotificationItem.vue'
import { useNotificationStore } from '@/stores/notification'
import { useAuthStore } from '@/stores/auth'

defineProps({
  light: { type: Boolean, default: false },
})

const router = useRouter()
const queryClient = useQueryClient()
const notifStore = useNotificationStore()
const auth = useAuthStore()
const panelOpen = ref(false)

const { data, isFetching } = useQuery({
  queryKey: ['notifications', 'dropdown'],
  queryFn: () => notificationService.list({ per_page: 5 }),
  refetchInterval: 30_000,
  enabled: computed(() => auth.isLoggedIn),
})

watchEffect(() => {
  const u = data.value?.unread_count
  if (typeof u === 'number') notifStore.setUnreadCount(u)
})

function toggle() {
  panelOpen.value = !panelOpen.value
  if (panelOpen.value) queryClient.invalidateQueries({ queryKey: ['notifications', 'dropdown'] })
}

function close() {
  panelOpen.value = false
}

const markRead = useMutation({
  mutationFn: (id) => notificationService.markRead(id),
  onSuccess: () => queryClient.invalidateQueries({ queryKey: ['notifications'] }),
})

function openItem(n) {
  markRead.mutate(n.id)
  const link = n.data?.link || n.link
  if (link) {
    window.open(link, '_blank')
    close()
    return
  }
  const slug = n.data?.document_slug
  if (slug) {
    router.push({ name: 'document.detail', params: { slug } })
    close()
    return
  }
  const docId = n.data?.document_id
  if (docId) {
    router.push({ name: 'document.detail', params: { slug: String(docId) } })
    close()
  }
}
</script>

<template>
  <div class="relative">
    <button
      type="button"
      class="relative rounded-full p-2 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
      :class="
        light ? '-m-1 text-white/90 hover:bg-white/10' : 'text-ink-600 hover:bg-ink-900/[0.04]'
      "
      aria-label="Thông báo"
      :aria-expanded="panelOpen"
      @click.stop="toggle"
    >
      <Icon icon="mdi:bell-outline" class="h-6 w-6" aria-hidden="true" />
      <span
        v-if="(data?.unread_count ?? 0) > 0"
        class="absolute right-1 top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white animate-pulse-soft motion-reduce:animate-none"
      >
        {{ (data?.unread_count ?? 0) > 9 ? '9+' : data?.unread_count }}
      </span>
    </button>

    <Transition name="slide-fade">
      <div
        v-show="panelOpen"
        class="absolute right-0 top-full z-50 mt-2 w-[min(360px,calc(100vw-2rem))] rounded-xl border border-ink-900/5 bg-white shadow-lift ring-1 ring-ink-900/5"
        @click.stop
      >
        <div class="flex items-center justify-between border-b border-ink-900/5 px-4 py-2">
          <span class="text-sm font-semibold text-ink-800">Thông báo</span>
          <RouterLink
            to="/notifications"
            class="text-xs font-medium text-brand-600 hover:text-brand-700"
            @click="close"
          >
            Xem tất cả
          </RouterLink>
        </div>
        <div class="max-h-96 overflow-y-auto">
          <div v-if="isFetching && !(data?.items?.length)" class="space-y-2 p-3">
            <div v-for="n in 3" :key="n" class="h-12 animate-pulse rounded-lg bg-surface-soft" />
          </div>
          <template v-else>
            <NotificationItem v-for="n in data?.items || []" :key="n.id" :item="n" @select="openItem" />
            <p v-if="!(data?.items?.length)" class="px-4 py-8 text-center text-sm text-ink-500">
              Không có thông báo.
            </p>
          </template>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition:
    opacity 0.22s ease,
    transform 0.22s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
@media (prefers-reduced-motion: reduce) {
  .slide-fade-enter-active,
  .slide-fade-leave-active {
    transition: none;
  }
}
</style>
