<script setup>
import { RouterLink, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import { computed } from 'vue'
import { useUiStore } from '@/stores/ui'
import { useQuery } from '@tanstack/vue-query'
import { proposalService } from '@/services/proposalService'

const route = useRoute()
const ui = useUiStore()

const { data: pendingData } = useQuery({
  queryKey: ['admin', 'proposals', 'pendingCount'],
  queryFn: () => proposalService.adminPendingCount(),
  refetchInterval: 60_000,
})

const pendingCount = computed(() => pendingData.value?.data?.count ?? 0)

const links = [
  { to: '/admin', name: 'admin.dashboard', label: 'Dashboard', icon: 'mdi:view-dashboard-outline' },
  { to: '/admin/documents', name: 'admin.documents', label: 'Tài liệu', icon: 'mdi:file-document-multiple-outline' },
  { to: '/admin/proposals', name: 'admin.proposals', label: 'Đề xuất', icon: 'mdi:file-upload-outline', badge: pendingCount },
  { to: '/admin/users', name: 'admin.users', label: 'Người dùng', icon: 'mdi:account-group-outline' },
  { to: '/admin/categories', name: 'admin.categories', label: 'Danh mục', icon: 'mdi:folder-outline' },
  { to: '/admin/tags', name: 'admin.tags', label: 'Tags', icon: 'mdi:tag-multiple-outline' },
  { to: '/admin/chatbot/intents', name: 'admin.chatbot.intents', label: 'Chatbot Intents', icon: 'mdi:robot-outline' },
  { to: '/admin/chatbot/logs', name: 'admin.chatbot.logs', label: 'Chatbot Logs', icon: 'mdi:message-text-outline' },
  { to: '/admin/broadcast', name: 'admin.broadcast', label: 'Broadcast', icon: 'mdi:bullhorn-outline' },
]

function isActive(link) {
  if (link.to === '/admin') return route.path === '/admin' || route.path === '/admin/'
  return route.path === link.to || route.path.startsWith(`${link.to}/`)
}

const sidebarClass = computed(() =>
  [
    'fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-slate-200/80 bg-gradient-to-b from-white via-slate-50/95 to-brand-50/25 shadow-[4px_0_24px_-8px_rgba(15,23,42,0.08)] transition-transform duration-300 ease-out md:translate-x-0',
    ui.sidebarCollapsed ? '-translate-x-full md:translate-x-0' : 'translate-x-0',
  ].join(' '),
)

function closeMobileNav() {
  if (typeof window !== 'undefined' && window.matchMedia('(max-width: 767px)').matches) {
    ui.sidebarCollapsed = true
  }
}
</script>

<template>
  <!-- Overlay mobile -->
  <div
    v-if="!ui.sidebarCollapsed"
    class="fixed inset-0 z-40 bg-slate-900/35 backdrop-blur-[2px] transition-opacity md:hidden"
    aria-hidden="true"
    @click="ui.toggleSidebar()"
  />

  <aside :class="sidebarClass">
    <div
      class="relative flex h-[4.25rem] shrink-0 items-center gap-3 overflow-hidden border-b border-slate-200/70 px-4"
    >
      <div
        class="pointer-events-none absolute -right-8 -top-10 h-24 w-32 rounded-full bg-gradient-to-br from-brand-400/35 to-purple-400/25 blur-2xl"
        aria-hidden="true"
      />
      <div
        class="relative flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-indigo-700 text-white shadow-md ring-1 ring-white/20"
      >
        <Icon icon="mdi:shield-crown-outline" class="h-6 w-6" />
      </div>
      <div class="relative min-w-0">
        <span class="font-display text-[0.65rem] font-semibold uppercase tracking-widest text-slate-400">Console</span>
        <span class="block truncate font-display text-base font-bold text-slate-900">Tri Thức Số</span>
      </div>
      <button
        type="button"
        class="relative ml-auto rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 md:hidden"
        aria-label="Đóng menu"
        @click="ui.toggleSidebar()"
      >
        <Icon icon="mdi:close" class="h-5 w-5" />
      </button>
    </div>
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
      <RouterLink
        v-for="link in links"
        :key="link.to"
        :to="link.to"
        class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
        :class="
          isActive(link)
            ? 'bg-gradient-to-r from-brand-600 to-indigo-600 text-white shadow-md shadow-brand-500/25 ring-1 ring-white/15'
            : 'text-slate-600 hover:bg-white/90 hover:text-slate-900 hover:shadow-soft'
        "
        @click="closeMobileNav"
      >
        <span
          :class="
            isActive(link)
              ? 'rounded-lg bg-white/15 p-1'
              : 'rounded-lg bg-slate-100/80 p-1 text-slate-500 transition group-hover:bg-brand-50 group-hover:text-brand-700'
          "
        >
          <Icon :icon="link.icon" class="h-[1.125rem] w-[1.125rem] shrink-0" />
        </span>
        <span class="truncate">{{ link.label }}</span>
        <span
          v-if="link.badge && link.badge > 0"
          class="ml-auto flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[0.65rem] font-bold"
          :class="isActive(link) ? 'bg-white/25 text-white' : 'bg-amber-500 text-white'"
        >{{ link.badge }}</span>
        <Icon
          v-else
          icon="mdi:chevron-right"
          class="ml-auto hidden h-4 w-4 shrink-0 md:block"
          :class="
            isActive(link)
              ? 'opacity-90'
              : 'opacity-0 transition group-hover:translate-x-0.5 group-hover:opacity-60'
          "
          aria-hidden="true"
        />
      </RouterLink>
    </nav>
  </aside>
</template>
