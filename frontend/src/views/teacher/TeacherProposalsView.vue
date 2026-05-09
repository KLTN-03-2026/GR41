<script setup>
import { ref, computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import Button from 'primevue/button'
import { proposalService } from '@/services/proposalService'
import { unwrapList } from '@/utils/apiHelpers'
import { useToast } from '@/composables/useToast'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import TeacherProposalFormDialog from '@/components/teacher/TeacherProposalFormDialog.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import { formatDateTime } from '@/utils/formatters'

const toast = useToast()
const qc = useQueryClient()

const formVisible = ref(false)
const deleteTarget = ref(null)
const deleteLoading = ref(false)
const filterStatus = ref('all')

const { data, isLoading } = useQuery({
  queryKey: ['teacher', 'proposals'],
  queryFn: () => proposalService.list({ per_page: 100 }),
})

const allItems = computed(() => unwrapList(data.value?.data ?? data.value))

const items = computed(() => {
  if (filterStatus.value === 'all') return allItems.value
  return allItems.value.filter((d) => d.status === filterStatus.value)
})

const counts = computed(() => ({
  all: allItems.value.length,
  pending: allItems.value.filter((d) => d.status === 'pending').length,
  published: allItems.value.filter((d) => d.status === 'published').length,
  rejected: allItems.value.filter((d) => d.status === 'rejected').length,
}))

const delMutation = useMutation({
  mutationFn: (id) => proposalService.delete(id),
  onSuccess: () => {
    toast.success('Đã xóa đề xuất')
    qc.invalidateQueries({ queryKey: ['teacher', 'proposals'] })
    deleteTarget.value = null
  },
  onError: (e) => toast.error(e?.message || 'Không thể xóa đề xuất'),
  onSettled: () => { deleteLoading.value = false },
})

const STATUS_META = {
  pending: { label: 'Chờ duyệt', bg: 'bg-amber-100 text-amber-700', dot: 'bg-amber-400' },
  published: { label: 'Đã duyệt', bg: 'bg-emerald-100 text-emerald-700', dot: 'bg-emerald-500' },
  rejected: { label: 'Bị từ chối', bg: 'bg-rose-100 text-rose-700', dot: 'bg-rose-500' },
}

const TABS = [
  { key: 'all', label: 'Tất cả' },
  { key: 'pending', label: 'Chờ duyệt' },
  { key: 'published', label: 'Đã duyệt' },
  { key: 'rejected', label: 'Bị từ chối' },
]
</script>

<template>
  <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <!-- Header -->
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Đề xuất tài liệu</h1>
        <p class="mt-1 text-sm text-slate-500">Quản lý các tài liệu bạn đã đề xuất lên hệ thống.</p>
      </div>
      <Button label="Đề xuất mới" icon="pi pi-plus" @click="formVisible = true" />
    </div>

    <!-- Stats -->
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
      <div
        v-for="tab in TABS"
        :key="tab.key"
        class="cursor-pointer rounded-xl border p-3 text-center transition-all"
        :class="filterStatus === tab.key
          ? 'border-brand-300 bg-brand-50 shadow-sm'
          : 'border-slate-200/80 bg-white hover:border-slate-300'"
        @click="filterStatus = tab.key"
      >
        <p class="text-2xl font-bold text-slate-900">{{ counts[tab.key] }}</p>
        <p class="mt-0.5 text-xs text-slate-500">{{ tab.label }}</p>
      </div>
    </div>

    <!-- List -->
    <LoadingSpinner v-if="isLoading" />
    <EmptyState
      v-else-if="items.length === 0"
      :title="filterStatus === 'all' ? 'Chưa có đề xuất nào' : `Không có đề xuất ${TABS.find(t => t.key === filterStatus)?.label?.toLowerCase()}`"
    />
    <div v-else class="space-y-3">
      <div
        v-for="doc in items"
        :key="doc.id"
        class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md"
      >
        <div class="flex gap-4 p-4">
          <!-- Cover -->
          <div class="h-20 w-14 shrink-0 overflow-hidden rounded-lg bg-slate-100">
            <img
              v-if="doc.cover_image"
              :src="doc.cover_image"
              :alt="doc.title"
              class="h-full w-full object-cover"
            />
            <div v-else class="flex h-full w-full items-center justify-center">
              <i class="pi pi-file-pdf text-2xl text-slate-300" />
            </div>
          </div>

          <!-- Info -->
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-start gap-2">
              <span
                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
                :class="STATUS_META[doc.status]?.bg"
              >
                <span class="h-1.5 w-1.5 rounded-full" :class="STATUS_META[doc.status]?.dot" />
                {{ STATUS_META[doc.status]?.label }}
              </span>
            </div>
            <h3 class="mt-1.5 font-semibold text-slate-900 line-clamp-1">{{ doc.title }}</h3>
            <p v-if="doc.author" class="mt-0.5 text-xs text-slate-500">{{ doc.author }}</p>
            <p class="mt-1 text-xs text-slate-400">Đề xuất {{ formatDateTime(doc.created_at) }}</p>

            <!-- Rejection reason -->
            <div
              v-if="doc.status === 'rejected' && doc.rejection_reason"
              class="mt-3 rounded-lg border border-rose-200/80 bg-rose-50/70 px-3 py-2"
            >
              <p class="text-xs font-medium text-rose-700">Lý do từ chối:</p>
              <p class="mt-0.5 text-xs text-rose-600 whitespace-pre-wrap">{{ doc.rejection_reason }}</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex shrink-0 items-start gap-1 pt-1">
            <Button
              v-if="doc.status === 'pending'"
              v-tooltip.top="'Xóa đề xuất'"
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              size="small"
              @click="deleteTarget = doc"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Form dialog -->
    <TeacherProposalFormDialog v-model:visible="formVisible" />

    <!-- Delete confirm -->
    <ConfirmDialog
      :visible="deleteTarget !== null"
      title="Xóa đề xuất"
      :message="`Bạn có chắc muốn xóa đề xuất &quot;${deleteTarget?.title}&quot;? Hành động không thể hoàn tác.`"
      :loading="deleteLoading"
      @update:visible="(v) => { if (!v) deleteTarget = null }"
      @confirm="() => { deleteLoading = true; delMutation.mutate(deleteTarget.id) }"
    />
  </div>
</template>
