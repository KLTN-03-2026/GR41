<script setup>
import { ref, computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import Textarea from 'primevue/textarea'
import { proposalService } from '@/services/proposalService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import { formatDateTime } from '@/utils/formatters'

const toast = useToast()
const qc = useQueryClient()

const filterStatus = ref('pending')
const detailVisible = ref(false)
const selectedDoc = ref(null)
const rejectVisible = ref(false)
const rejectReason = ref('')
const approveTarget = ref(null)
const approveLoading = ref(false)
const rejectLoading = ref(false)

const { data, isLoading } = useQuery({
  queryKey: ['admin', 'proposals', filterStatus],
  queryFn: () => proposalService.adminList({ status: filterStatus.value, per_page: 100 }),
})

const rows = computed(() => unwrapList(data.value?.data ?? data.value))

const pendingCount = computed(() => data.value?.data?.pending_count ?? 0)

const TABS = [
  { key: 'pending', label: 'Chờ duyệt' },
  { key: 'published', label: 'Đã duyệt' },
  { key: 'rejected', label: 'Bị từ chối' },
  { key: 'all', label: 'Tất cả' },
]

const STATUS_META = {
  pending: { label: 'Chờ duyệt', class: 'bg-amber-100 text-amber-700' },
  published: { label: 'Đã duyệt', class: 'bg-emerald-100 text-emerald-700' },
  rejected: { label: 'Từ chối', class: 'bg-rose-100 text-rose-700' },
}

const approveMutation = useMutation({
  mutationFn: (id) => proposalService.adminApprove(id),
  onSuccess: () => {
    toast.success('Đã duyệt tài liệu — giảng viên đã được thông báo')
    qc.invalidateQueries({ queryKey: ['admin', 'proposals'] })
    approveTarget.value = null
    detailVisible.value = false
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { approveLoading.value = false },
})

const rejectMutation = useMutation({
  mutationFn: ({ id, reason }) => proposalService.adminReject(id, { reason }),
  onSuccess: () => {
    toast.success('Đã từ chối — giảng viên đã được thông báo')
    qc.invalidateQueries({ queryKey: ['admin', 'proposals'] })
    rejectVisible.value = false
    detailVisible.value = false
    rejectReason.value = ''
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { rejectLoading.value = false },
})

function openDetail(doc) {
  selectedDoc.value = doc
  detailVisible.value = true
}

function openReject() {
  rejectReason.value = ''
  rejectVisible.value = true
}

function confirmReject() {
  if (!rejectReason.value.trim()) return toast.error('Vui lòng nhập lý do từ chối')
  rejectLoading.value = true
  rejectMutation.mutate({ id: selectedDoc.value.id, reason: rejectReason.value.trim() })
}
</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Đề xuất tài liệu"
      subtitle="Xem xét và duyệt tài liệu do giảng viên gửi lên."
    >
      <template v-if="pendingCount > 0" #actions>
        <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700">
          {{ pendingCount }} chờ duyệt
        </span>
      </template>
    </AdminPageHeader>

    <!-- Tab filter -->
    <div class="flex flex-wrap gap-2">
      <button
        v-for="tab in TABS"
        :key="tab.key"
        class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
        :class="filterStatus === tab.key
          ? 'bg-brand-600 text-white shadow-sm'
          : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
        @click="filterStatus = tab.key"
      >
        {{ tab.label }}
      </button>
    </div>

    <AdminSurface>
      <DataTable
        :value="rows"
        :loading="isLoading"
        striped-rows
        paginator
        :rows="15"
        :rows-per-page-options="[15, 30, 50]"
        class="admin-datatable"
      >
        <Column header="Tài liệu" style="min-width: 260px">
          <template #body="{ data: doc }">
            <div class="flex items-center gap-3">
              <div class="h-10 w-7 shrink-0 overflow-hidden rounded bg-slate-100">
                <img v-if="doc.cover_image" :src="doc.cover_image" :alt="doc.title" class="h-full w-full object-cover" />
                <i v-else class="pi pi-file-pdf flex h-full w-full items-center justify-center text-base text-slate-300" />
              </div>
              <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-900">{{ doc.title }}</p>
                <p v-if="doc.author" class="text-xs text-slate-500">{{ doc.author }}</p>
              </div>
            </div>
          </template>
        </Column>

        <Column header="Giảng viên" style="width: 160px">
          <template #body="{ data: doc }">
            <span class="text-sm text-slate-700">{{ doc.proposer?.name || '—' }}</span>
          </template>
        </Column>

        <Column header="Trạng thái" style="width: 130px">
          <template #body="{ data: doc }">
            <span
              class="rounded-full px-2.5 py-0.5 text-xs font-medium"
              :class="STATUS_META[doc.status]?.class"
            >
              {{ STATUS_META[doc.status]?.label || doc.status }}
            </span>
          </template>
        </Column>

        <Column header="Ngày đề xuất" style="width: 150px">
          <template #body="{ data: doc }">
            <span class="text-xs text-slate-500">{{ formatDateTime(doc.created_at) }}</span>
          </template>
        </Column>

        <Column header="" style="width: 80px">
          <template #body="{ data: doc }">
            <Button icon="pi pi-eye" text rounded size="small" @click="openDetail(doc)" />
          </template>
        </Column>
      </DataTable>
    </AdminSurface>

    <!-- Detail Dialog -->
    <Dialog
      v-model:visible="detailVisible"
      modal
      header="Chi tiết đề xuất"
      class="w-[min(680px,96vw)]"
      :pt="{
        root: { class: 'max-h-[92vh] !flex !flex-col' },
        content: { class: '!flex-1 !min-h-0 !overflow-y-auto scrollbar-dialog' },
      }"
    >
      <div v-if="selectedDoc" class="space-y-6 py-2">
        <!-- Cover + title -->
        <div class="flex gap-4">
          <div class="h-28 w-20 shrink-0 overflow-hidden rounded-xl bg-slate-100 shadow-sm">
            <img
              v-if="selectedDoc.cover_image"
              :src="selectedDoc.cover_image"
              :alt="selectedDoc.title"
              class="h-full w-full object-cover"
            />
            <div v-else class="flex h-full w-full items-center justify-center">
              <i class="pi pi-file-pdf text-3xl text-slate-300" />
            </div>
          </div>
          <div class="min-w-0">
            <span
              class="mb-2 inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
              :class="STATUS_META[selectedDoc.status]?.class"
            >
              {{ STATUS_META[selectedDoc.status]?.label }}
            </span>
            <h2 class="text-lg font-bold text-slate-900">{{ selectedDoc.title }}</h2>
            <p v-if="selectedDoc.author" class="text-sm text-slate-500">{{ selectedDoc.author }}</p>
            <p class="mt-1 text-xs text-slate-400">
              Đề xuất bởi <strong>{{ selectedDoc.proposer?.name }}</strong>
              — {{ formatDateTime(selectedDoc.created_at) }}
            </p>
          </div>
        </div>

        <!-- Description -->
        <div v-if="selectedDoc.description" class="rounded-xl bg-slate-50 px-4 py-3">
          <p class="text-sm leading-relaxed text-slate-700">{{ selectedDoc.description }}</p>
        </div>

        <!-- Meta grid -->
        <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
          <div v-if="selectedDoc.publisher">
            <p class="text-xs text-slate-400">Nhà xuất bản</p>
            <p class="font-medium text-slate-700">{{ selectedDoc.publisher }}</p>
          </div>
          <div v-if="selectedDoc.published_year">
            <p class="text-xs text-slate-400">Năm</p>
            <p class="font-medium text-slate-700">{{ selectedDoc.published_year }}</p>
          </div>
          <div v-if="selectedDoc.language">
            <p class="text-xs text-slate-400">Ngôn ngữ</p>
            <p class="font-medium text-slate-700">{{ selectedDoc.language === 'vi' ? 'Tiếng Việt' : 'English' }}</p>
          </div>
          <div v-if="selectedDoc.pages">
            <p class="text-xs text-slate-400">Số trang</p>
            <p class="font-medium text-slate-700">{{ selectedDoc.pages }}</p>
          </div>
          <div v-if="selectedDoc.isbn">
            <p class="text-xs text-slate-400">ISBN</p>
            <p class="font-medium text-slate-700">{{ selectedDoc.isbn }}</p>
          </div>
        </div>

        <!-- PDF link -->
        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
          <i class="pi pi-file-pdf text-rose-500" />
          <a
            :href="selectedDoc.file_url"
            target="_blank"
            rel="noopener"
            class="flex-1 truncate text-sm font-medium text-brand-600 hover:underline"
          >
            Xem file PDF
          </a>
          <i class="pi pi-external-link text-xs text-slate-400" />
        </div>

        <!-- Rejection reason (if rejected) -->
        <div
          v-if="selectedDoc.status === 'rejected' && selectedDoc.rejection_reason"
          class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3"
        >
          <p class="text-xs font-semibold text-rose-700">Lý do từ chối:</p>
          <p class="mt-1 text-sm text-rose-600 whitespace-pre-wrap">{{ selectedDoc.rejection_reason }}</p>
        </div>
      </div>

      <template v-if="selectedDoc?.status === 'pending'" #footer>
        <Button label="Từ chối" icon="pi pi-times" severity="danger" outlined @click="openReject" />
        <Button
          label="Duyệt tài liệu"
          icon="pi pi-check"
          :loading="approveLoading"
          @click="() => { approveTarget = selectedDoc; approveLoading = true; approveMutation.mutate(selectedDoc.id) }"
        />
      </template>
    </Dialog>

    <!-- Reject reason dialog -->
    <Dialog
      v-model:visible="rejectVisible"
      modal
      header="Lý do từ chối"
      class="w-[min(460px,95vw)]"
    >
      <div class="space-y-3 py-2">
        <p class="text-sm text-slate-600">Giảng viên sẽ nhận được thông báo kèm lý do này.</p>
        <Textarea
          v-model="rejectReason"
          rows="4"
          class="w-full"
          fluid
          placeholder="Nhập lý do từ chối..."
          :autofocus="true"
        />
      </div>
      <template #footer>
        <Button label="Hủy" severity="secondary" text @click="rejectVisible = false" />
        <Button
          label="Xác nhận từ chối"
          severity="danger"
          :loading="rejectLoading"
          :disabled="!rejectReason.trim()"
          @click="confirmReject"
        />
      </template>
    </Dialog>
  </div>
</template>
