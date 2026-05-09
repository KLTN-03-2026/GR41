<script setup>
import { computed } from 'vue'
import { RouterLink, RouterView, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import AppSidebar from '@/components/common/AppSidebar.vue'
import { useUiStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const ui = useUiStore()
const auth = useAuthStore()

const displayName = computed(() => auth.user?.name || 'Admin')

const adminPageLabel = computed(() => {
  const map = {
    'admin.dashboard': 'Tổng quan',
    'admin.documents': 'Tài liệu',
    'admin.documents.new': 'Thêm tài liệu',
    'admin.documents.edit': 'Sửa tài liệu',
    'admin.users': 'Người dùng',
    'admin.categories': 'Danh mục',
    'admin.tags': 'Tags',
    'admin.chatbot.intents': 'Chatbot intents',
    'admin.chatbot.logs': 'Chatbot logs',
    'admin.broadcast': 'Broadcast',
  }
  return map[route.name] ?? 'Admin'
})
</script>

<template>
  <div
    class="relative flex min-h-screen overflow-x-hidden bg-slate-50 before:pointer-events-none before:fixed before:inset-0 before:bg-[radial-gradient(circle_at_1px_1px,rgb(148_163_184/0.12)_1px,transparent_0)] before:bg-[length:28px_28px] after:pointer-events-none after:fixed after:inset-0 after:bg-gradient-to-br after:from-brand-50/40 after:via-transparent after:to-purple-50/30"
  >
    <AppSidebar />
    <div class="relative flex min-h-screen flex-1 flex-col md:pl-64">
      <!-- Mobile -->
      <header
        class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200/80 bg-white/85 px-4 py-3 shadow-sm backdrop-blur-md motion-safe:animate-fade-in md:hidden"
      >
        <button
          type="button"
          class="rounded-xl p-2 text-slate-600 transition-colors hover:bg-slate-100 active:scale-[0.98]"
          aria-label="Menu"
          @click="ui.sidebarCollapsed = false"
        >
          <Icon icon="mdi:menu" class="h-6 w-6" />
        </button>
        <span class="max-w-[50%] truncate font-display text-sm font-semibold text-slate-800">{{
          displayName
        }}</span>
        <RouterLink to="/" class="text-sm font-medium text-brand-700 transition hover:text-brand-600">
          Trang chủ
        </RouterLink>
      </header>

      <!-- Desktop: slim bar -->
      <div
        class="sticky top-0 z-20 hidden items-center justify-between border-b border-slate-200/70 bg-white/75 px-6 py-3 backdrop-blur-lg md:flex md:px-8"
      >
        <nav class="flex min-w-0 items-center gap-2 text-sm" aria-label="Phân cấp">
          <span class="shrink-0 font-medium text-slate-400">Admin</span>
          <Icon icon="mdi:chevron-right" class="h-4 w-4 shrink-0 text-slate-300" aria-hidden="true" />
          <span class="truncate font-display font-semibold text-slate-800">{{ adminPageLabel }}</span>
        </nav>
        <RouterLink
          to="/"
          class="group inline-flex items-center gap-2 rounded-xl border border-slate-200/90 bg-white/90 px-3.5 py-2 text-sm font-medium text-slate-700 shadow-soft transition hover:border-brand-200 hover:bg-brand-50/80 hover:text-brand-800 hover:shadow-md active:scale-[0.98]"
        >
          <Icon
            icon="mdi:home-outline"
            class="h-4 w-4 transition group-hover:text-brand-600"
          />
          Xem website
        </RouterLink>
      </div>

      <main class="relative flex-1 p-4 motion-safe:animate-fade-in md:p-8">
        <RouterView />
      </main>
    </div>
  </div>
</template>
