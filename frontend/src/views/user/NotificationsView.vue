<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import NotificationItem from '@/components/notification/NotificationItem.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import { notificationService } from '@/services/notificationService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import { formatDateTime } from '@/utils/formatters'

const toast = useToast()
const qc = useQueryClient()
const route = useRoute()
const router = useRouter()
const page = ref(Math.max(1, Number(route.query.page) || 1))
const filter = ref('all')
const detailVisible = ref(false)
const selectedItem = ref(null)
const markAllLoading = ref(false)

const { data, isLoading } = useQuery({
  queryKey: ['notifications', 'list', page],
  queryFn: () => notificationService.list({ page: page.value, per_page: 20 }),
})

const items = computed(() => unwrapList(data.value?.items ?? data.value))
const unreadCount = computed(() => data.value?.unread_count ?? 0)

const filteredItems = computed(() => {
  if (filter.value === 'unread') return items.value.filter((n) => !n.is_read)
  return items.value
})

const totalPages = computed(() => {
  const m = data.value?.meta || {}
  return Number(m.last_page) || 1
})

const markAllMutation = useMutation({
  mutationFn: notificationService.markAllRead,
  onSuccess: () => {
    toast.success('Đã đánh dấu đã đọc tất cả')
    qc.invalidateQueries({ queryKey: ['notifications'] })
  },
  onSettled: () => { markAllLoading.value = false },
})

const markReadMutation = useMutation({
  mutationFn: (id) => notificationService.markRead(id),
  onSuccess: () => { qc.invalidateQueries({ queryKey: ['notifications'] }) },
})

watch(
  () => route.query.page,
  (value) => {
    page.value = Math.max(1, Number(value) || 1)
  },
)

function setPage(nextPage) {
  const normalized = Math.min(totalPages.value, Math.max(1, nextPage))
  page.value = normalized
  router.push({
    name: 'notifications',
    query: normalized > 1 ? { page: normalized } : {},
  })
}

function notificationTarget(item) {
  const link = item.data?.url || item.data?.link || item.link
  if (link) return link
  const slug = item.data?.document_slug
  if (slug) return { name: 'document.detail', params: { slug } }
  return null
}

function selectItem(item) {
  if (!item.is_read) markReadMutation.mutate(item.id)
  const target = notificationTarget(item)
  if (target) {
    if (typeof target === 'string') {
      if (target.startsWith('/')) router.push(target)
      else window.open(target, '_blank')
    } else {
      router.push(target)
    }
    return
  }
  selectedItem.value = item
  detailVisible.value = true
}
</script>

<template>
  <div class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
    <!-- Header -->
    <div class="mb-8">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100">
            <i class="pi pi-bell text-lg text-brand-600" />
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-900">Thông báo</h1>
            <p class="text-xs text-slate-500">
              <template v-if="unreadCount > 0">{{ unreadCount }} chưa đọc</template>
              <template v-else>Không có thông báo mới</template>
            </p>
          </div>
        </div>
        <Button
          v-if="unreadCount > 0"
          label="Đánh dấu tất cả đã đọc"
          icon="pi pi-check-square"
          text
          size="small"
          :loading="markAllLoading"
          @click="() => { markAllLoading = true; markAllMutation.mutate() }"
        />
      </div>

      <!-- Filter chips -->
      <div class="mt-5 flex gap-2">
        <button
          class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
          :class="filter === 'all'
            ? 'bg-brand-600 text-white shadow-sm'
            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
          @click="filter = 'all'"
        >
          Tất cả
        </button>
        <button
          class="flex items-center gap-2 rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
          :class="filter === 'unread'
            ? 'bg-brand-600 text-white shadow-sm'
            : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
          @click="filter = 'unread'"
        >
          Chưa đọc
          <span
            v-if="unreadCount > 0"
            class="rounded-full px-1.5 py-0.5 text-xs font-bold leading-none"
            :class="filter === 'unread' ? 'bg-white/25 text-white' : 'bg-brand-500 text-white'"
          >{{ unreadCount }}</span>
        </button>
      </div>
    </div>

    <!-- List -->
    <LoadingSpinner v-if="isLoading" />
    <template v-else>
      <EmptyState
        v-if="filteredItems.length === 0"
        :title="filter === 'unread' ? 'Không có thông báo chưa đọc' : 'Không có thông báo nào'"
        icon="mdi:bell-off-outline"
      />
      <div v-else class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
        <NotificationItem
          v-for="n in filteredItems"
          :key="n.id"
          :item="n"
          @select="selectItem"
        />
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-6 flex items-center justify-center gap-2">
        <Button
          icon="pi pi-chevron-left"
          text
          rounded
          size="small"
          :disabled="page <= 1"
          @click="setPage(page - 1)"
        />
        <span class="text-sm text-slate-500">{{ page }} / {{ totalPages }}</span>
        <Button
          icon="pi pi-chevron-right"
          text
          rounded
          size="small"
          :disabled="page >= totalPages"
          @click="setPage(page + 1)"
        />
      </div>
    </template>

    <!-- Detail dialog -->
    <Dialog
      v-model:visible="detailVisible"
      modal
      :header="selectedItem?.title || 'Thông báo'"
      class="w-[min(520px,95vw)]"
      @hide="selectedItem = null"
    >
      <div v-if="selectedItem" class="space-y-4">
        <div class="flex items-center gap-1.5 text-xs text-slate-400">
          <i class="pi pi-clock" />
          {{ formatDateTime(selectedItem.created_at) }}
        </div>
        <div class="rounded-xl bg-slate-50/80 p-4 text-sm leading-relaxed text-slate-700 whitespace-pre-wrap">
          {{ selectedItem.content || selectedItem.message || 'Không có nội dung.' }}
        </div>
      </div>
    </Dialog>
  </div>
</template>
