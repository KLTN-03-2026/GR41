<script setup>
import { ref, computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dialog from 'primevue/dialog'
import Dropdown from 'primevue/dropdown'
import { categoryService } from '@/services/categoryService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

const toast = useToast()
const qc = useQueryClient()

const { data: payload, isFetching } = useQuery({
  queryKey: ['admin', 'categories'],
  queryFn: () => categoryService.adminList({ per_page: 500 }),
})

const rows = computed(() => unwrapList(payload.value?.data ?? payload.value))
const rootCategories = computed(() => rows.value.filter((c) => !c.parent_id))
const childCategories = computed(() => rows.value.filter((c) => !!c.parent_id))

// Build tree-ordered list: parent → children → parent → children ...
const treeRows = computed(() => {
  const result = []
  for (const root of rootCategories.value.slice().sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))) {
    result.push({ ...root, _isRoot: true, _depth: 0 })
    const kids = childCategories.value
      .filter((c) => c.parent_id === root.id)
      .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
    for (const child of kids) {
      result.push({ ...child, _isRoot: false, _depth: 1, _parentName: root.name })
    }
  }
  return result
})

const dialog = ref(false)
const editing = ref(null)
const saveLoading = ref(false)
const form = ref({ name: '', parent_id: null, icon: '', sort_order: 0 })

const deleteTarget = ref(null)
const deleteLoading = ref(false)

const nameConflict = computed(() => {
  if (!form.value.name.trim()) return false
  return rows.value.some(
    (c) => c.name.toLowerCase() === form.value.name.trim().toLowerCase() && c.id !== editing.value?.id,
  )
})

// Parent options: only root categories (no nesting beyond 2 levels)
const parentOptions = computed(() => [
  { id: null, name: '— Danh mục gốc —' },
  ...rootCategories.value.map((c) => ({ id: c.id, name: c.name })),
])

function openCreate(defaultParentId = null) {
  editing.value = null
  form.value = { name: '', parent_id: defaultParentId, icon: '', sort_order: 0 }
  dialog.value = true
}

function openEdit(row) {
  editing.value = row
  form.value = {
    name: row.name,
    parent_id: row.parent_id ?? null,
    icon: row.icon || '',
    sort_order: row.sort_order ?? 0,
  }
  dialog.value = true
}

const saveMutation = useMutation({
  mutationFn: () =>
    editing.value
      ? categoryService.adminUpdate(editing.value.id, form.value)
      : categoryService.adminCreate(form.value),
  onSuccess: () => {
    toast.success('Đã lưu')
    qc.invalidateQueries({ queryKey: ['admin', 'categories'] })
    dialog.value = false
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { saveLoading.value = false },
})

const delMutation = useMutation({
  mutationFn: (id) => categoryService.adminDelete(id),
  onSuccess: () => {
    toast.success('Đã xóa')
    qc.invalidateQueries({ queryKey: ['admin', 'categories'] })
    deleteTarget.value = null
  },
  onError: (e) => toast.error(e?.message || 'Không thể xóa danh mục này'),
  onSettled: () => { deleteLoading.value = false },
})

function requestDelete(cat) {
  deleteTarget.value = cat
}

function confirmDelete() {
  if (deleteTarget.value != null) {
    deleteLoading.value = true
    delMutation.mutate(deleteTarget.value.id)
  }
}
</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Danh mục"
      subtitle="Cây danh mục cha — con, thứ tự hiển thị và biểu tượng tùy chọn."
    >
      <template #actions>
        <Button label="Thêm danh mục" icon="pi pi-plus" @click="openCreate()" />
      </template>
    </AdminPageHeader>

    <!-- Stats -->
    <section class="grid gap-4 sm:grid-cols-3">
      <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-soft">
        <p class="text-xs uppercase tracking-wide text-slate-500">Tổng danh mục</p>
        <p class="mt-2 text-2xl font-bold text-slate-900">{{ rows.length }}</p>
      </div>
      <div class="rounded-2xl border border-brand-200/70 bg-brand-50/70 p-4 shadow-soft">
        <p class="text-xs uppercase tracking-wide text-brand-700">Danh mục gốc</p>
        <p class="mt-2 text-2xl font-bold text-brand-800">{{ rootCategories.length }}</p>
      </div>
      <div class="rounded-2xl border border-indigo-200/70 bg-indigo-50/70 p-4 shadow-soft">
        <p class="text-xs uppercase tracking-wide text-indigo-700">Danh mục con</p>
        <p class="mt-2 text-2xl font-bold text-indigo-800">{{ childCategories.length }}</p>
      </div>
    </section>

    <!-- Tree table -->
    <AdminSurface>
      <DataTable
        :value="treeRows"
        :loading="isFetching"
        striped-rows
        paginator
        :rows="20"
        :rows-per-page-options="[20, 50, 100]"
        class="admin-datatable"
      >
        <Column header="Danh mục" style="min-width: 220px">
          <template #body="{ data }">
            <div class="flex items-center gap-2">
              <!-- Root indicator -->
              <template v-if="data._isRoot">
                <span class="flex h-2.5 w-2.5 shrink-0 rounded-full bg-brand-500" />
                <span class="font-semibold text-slate-900">{{ data.name }}</span>
                <span v-if="data.icon" class="text-base">{{ data.icon }}</span>
              </template>
              <!-- Child indicator -->
              <template v-else>
                <span class="ml-3 shrink-0 text-slate-300">└</span>
                <span class="h-2 w-2 shrink-0 rounded-full bg-slate-300" />
                <span class="text-slate-700">{{ data.name }}</span>
              </template>
            </div>
          </template>
        </Column>
        <Column header="Quan hệ" style="width: 160px">
          <template #body="{ data }">
            <span v-if="data._isRoot" class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
              Gốc
            </span>
            <span v-else class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs text-slate-600">
              Con của: {{ data._parentName }}
            </span>
          </template>
        </Column>
        <Column field="sort_order" header="Thứ tự" style="width: 90px" />
        <Column header="" style="width: 180px">
          <template #body="{ data }">
            <div class="flex items-center gap-1">
              <Button
                v-if="data._isRoot"
                v-tooltip.top="'Thêm danh mục con'"
                icon="pi pi-plus"
                text
                rounded
                size="small"
                severity="secondary"
                @click="openCreate(data.id)"
              />
              <Button icon="pi pi-pencil" text rounded size="small" @click="openEdit(data)" />
              <Button icon="pi pi-trash" severity="danger" text rounded size="small" @click="requestDelete(data)" />
            </div>
          </template>
        </Column>
      </DataTable>
    </AdminSurface>

    <!-- Form dialog -->
    <Dialog v-model:visible="dialog" modal :header="editing ? 'Sửa danh mục' : 'Thêm danh mục'" class="w-[min(520px,95vw)]">
      <div class="space-y-4 py-2">
        <!-- Name with conflict warning -->
        <div>
          <label class="mb-1 block text-sm font-medium">Tên danh mục <span class="text-rose-500">*</span></label>
          <InputText v-model="form.name" class="w-full" fluid placeholder="Nhập tên danh mục..." />
          <p v-if="nameConflict" class="mt-1.5 text-xs text-rose-500">
            ⚠ Tên danh mục này đã tồn tại.
          </p>
        </div>

        <!-- Parent selector -->
        <div>
          <label class="mb-1 block text-sm font-medium">Thuộc danh mục</label>
          <Dropdown
            v-model="form.parent_id"
            :options="parentOptions"
            option-label="name"
            option-value="id"
            class="w-full"
            :pt="{ overlay: { class: 'scrollbar-select-panel' } }"
          />
          <p class="mt-1 text-xs text-slate-500">Để trống nếu là danh mục gốc.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="mb-1 block text-sm font-medium">Icon (emoji)</label>
            <InputText v-model="form.icon" class="w-full" fluid placeholder="📚" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium">Thứ tự hiển thị</label>
            <InputText v-model.number="form.sort_order" type="number" class="w-full" fluid />
          </div>
        </div>

        <Button
          label="Lưu danh mục"
          :loading="saveLoading"
          :disabled="nameConflict || !form.name.trim()"
          class="w-full"
          @click="() => { saveLoading = true; saveMutation.mutate() }"
        />
      </div>
    </Dialog>

    <ConfirmDialog
      :visible="deleteTarget !== null"
      :message="`Bạn có chắc muốn xóa danh mục &quot;${deleteTarget?.name}&quot;? Tất cả danh mục con có thể bị ảnh hưởng.`"
      :loading="deleteLoading"
      @update:visible="(v) => { if (!v) deleteTarget = null }"
      @confirm="confirmDelete"
    />
  </div>
</template>
