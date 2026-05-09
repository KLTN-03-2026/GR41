<script setup>
import { ref, computed, watch } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Tag from 'primevue/tag'
import { documentService } from '@/services/documentService'
import { categoryService } from '@/services/categoryService'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminFilterBar from '@/components/admin/AdminFilterBar.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import AdminDocumentFormDialog from '@/components/admin/AdminDocumentFormDialog.vue'
import AdminDocumentDetailDialog from '@/components/admin/AdminDocumentDetailDialog.vue'
import { useToast } from '@/composables/useToast'
import { unwrapList, unwrapMeta } from '@/utils/apiHelpers'

const toast = useToast()
const qc = useQueryClient()

const filters = ref({ q: '', category_id: null, year: null, is_featured: null })
const page = ref(1)
const perPage = ref(15)

// Form dialog
const formDialogVisible = ref(false)
const editDocumentId = ref(null)

// Detail dialog
const detailDialogVisible = ref(false)
const detailDocumentId = ref(null)

// Delete confirm
const deleteTarget = ref(null)
const deleteLoading = ref(false)

const { data: cats } = useQuery({
  queryKey: ['admin', 'categories'],
  queryFn: () => categoryService.adminList({ per_page: 500 }),
})

const categoryOpts = computed(() => unwrapList(cats.value))

const params = computed(() => ({
  page: page.value,
  per_page: perPage.value,
  ...filters.value,
}))

const { data: payload, isFetching } = useQuery({
  queryKey: ['admin', 'documents', params],
  queryFn: () => documentService.adminList(params.value),
})

const rows = computed(() => unwrapList(payload.value?.items ?? payload.value?.data ?? payload.value))
const paginationMeta = computed(() => unwrapMeta(payload.value))
const totalRecords = computed(() => paginationMeta.value.total ?? 0)
const paginatorRows = computed(() => paginationMeta.value.per_page ?? perPage.value)
const documentStats = computed(() => {
  const items = rows.value
  return {
    featured: items.filter((d) => d.is_featured).length,
    views: items.reduce((sum, d) => sum + Number(d.view_count || 0), 0),
    downloads: items.reduce((sum, d) => sum + Number(d.download_count || 0), 0),
  }
})

const first = computed(() => {
  const m = paginationMeta.value
  const cp = m.current_page
  const pp = m.per_page ?? perPage.value
  if (!cp || cp < 1) return 0
  return (cp - 1) * pp
})

watch(
  () => ({ ...filters.value }),
  () => { page.value = 1 },
  { deep: true },
)

function onPage(e) {
  page.value = e.page + 1
  perPage.value = e.rows
}

const delMutation = useMutation({
  mutationFn: (id) => documentService.adminDelete(id),
  onSuccess: () => {
    toast.success('Đã xóa')
    qc.invalidateQueries({ queryKey: ['admin', 'documents'] })
    deleteTarget.value = null
    detailDialogVisible.value = false
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => {
    deleteLoading.value = false
  },
})

const toggleFeatured = useMutation({
  mutationFn: ({ id, value }) => documentService.adminUpdate(id, { is_featured: value }),
  onSuccess: () => qc.invalidateQueries({ queryKey: ['admin', 'documents'] }),
})

function openAdd() {
  editDocumentId.value = null
  formDialogVisible.value = true
}

function openEdit(id) {
  editDocumentId.value = id
  formDialogVisible.value = true
  detailDialogVisible.value = false
}

function openDetail(id) {
  detailDocumentId.value = id
  detailDialogVisible.value = true
}

function requestDelete(id) {
  deleteTarget.value = id
  detailDialogVisible.value = false
}

function confirmDelete() {
  if (deleteTarget.value != null) {
    deleteLoading.value = true
    delMutation.mutate(deleteTarget.value)
  }
}
</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Quản lý tài liệu"
      subtitle="Lọc, nổi bật và chỉnh sửa tài liệu — số liệu xem/tải được đồng bộ theo thời gian thực."
    >
      <template #actions>
        <Button label="Thêm tài liệu" icon="pi pi-plus" @click="openAdd" />
      </template>
    </AdminPageHeader>

    <AdminFilterBar>
      <InputText v-model="filters.q" placeholder="Từ khóa..." class="w-56" />
      <Dropdown
        v-model="filters.category_id"
        :options="categoryOpts"
        option-label="name"
        option-value="id"
        placeholder="Danh mục"
        show-clear
        class="w-48"
      />
      <InputText v-model.number="filters.year" placeholder="Năm" class="w-28" />
      <Dropdown
        v-model="filters.is_featured"
        :options="[
          { label: 'Nổi bật', value: true },
          { label: 'Thường', value: false },
        ]"
        option-label="label"
        option-value="value"
        placeholder="Featured"
        show-clear
        class="w-40"
      />
    </AdminFilterBar>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-slate-500">Tổng tài liệu</p>
        <p class="mt-2 text-2xl font-bold text-slate-900">{{ totalRecords }}</p>
      </div>
      <div class="rounded-2xl border border-amber-200/70 bg-amber-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-amber-700">Nổi bật trên trang</p>
        <p class="mt-2 text-2xl font-bold text-amber-800">{{ documentStats.featured }}</p>
      </div>
      <div class="rounded-2xl border border-sky-200/70 bg-sky-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-sky-700">Lượt xem trên trang</p>
        <p class="mt-2 text-2xl font-bold text-sky-800">{{ documentStats.views }}</p>
      </div>
      <div class="rounded-2xl border border-emerald-200/70 bg-emerald-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Lượt tải trên trang</p>
        <p class="mt-2 text-2xl font-bold text-emerald-800">{{ documentStats.downloads }}</p>
      </div>
    </section>

    <AdminSurface>
      <DataTable
        lazy
        :value="rows"
        :loading="isFetching"
        striped-rows
        class="admin-datatable"
        paginator
        :rows="paginatorRows"
        :total-records="totalRecords"
        :rows-per-page-options="[10, 15, 25, 50]"
        :first="first"
        @page="onPage"
      >
        <Column header="Ảnh">
          <template #body="{ data }">
            <img
              :src="data.cover_image || '/vite.svg'"
              class="h-12 w-10 rounded object-cover"
              alt=""
            />
          </template>
        </Column>
        <Column field="title" header="Tiêu đề" />
        <Column field="author" header="Tác giả" />
        <Column header="Danh mục">
          <template #body="{ data }">{{ data.category?.name }}</template>
        </Column>
        <Column field="view_count" header="Xem" />
        <Column field="download_count" header="Tải" />
        <Column header="Nổi bật">
          <template #body="{ data }">
            <Tag
              :value="data.is_featured ? 'On' : 'Off'"
              :severity="data.is_featured ? 'success' : 'secondary'"
              class="cursor-pointer"
              @click="toggleFeatured.mutate({ id: data.id, value: !data.is_featured })"
            />
          </template>
        </Column>
        <Column header="">
          <template #body="{ data }">
            <Button
              v-tooltip.top="'Xem chi tiết'"
              icon="pi pi-eye"
              text
              rounded
              severity="info"
              @click="openDetail(data.id)"
            />
            <Button
              v-tooltip.top="'Chỉnh sửa'"
              icon="pi pi-pencil"
              text
              rounded
              @click="openEdit(data.id)"
            />
            <Button
              v-tooltip.top="'Xóa'"
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              @click="requestDelete(data.id)"
            />
          </template>
        </Column>
      </DataTable>
    </AdminSurface>

    <!-- Form dialog: add / edit -->
    <AdminDocumentFormDialog
      v-model:visible="formDialogVisible"
      :edit-document-id="editDocumentId"
    />

    <!-- Detail dialog -->
    <AdminDocumentDetailDialog
      v-model:visible="detailDialogVisible"
      :document-id="detailDocumentId"
      @edit="openEdit"
      @delete="requestDelete"
    />

    <!-- Delete confirm -->
    <ConfirmDialog
      :visible="deleteTarget !== null"
      message="Bạn có chắc muốn xóa tài liệu này? Hành động không thể hoàn tác."
      :loading="deleteLoading"
      @update:visible="(v) => { if (!v) deleteTarget = null }"
      @confirm="confirmDelete"
    />
  </div>
</template>
