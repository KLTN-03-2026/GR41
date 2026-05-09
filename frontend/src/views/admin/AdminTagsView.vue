<script setup>
import { ref, computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dialog from 'primevue/dialog'
import { tagService } from '@/services/tagService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

const toast = useToast()
const qc = useQueryClient()

const { data: payload, isFetching } = useQuery({
  queryKey: ['admin', 'tags'],
  queryFn: () => tagService.adminList({ per_page: 500 }),
})

const rows = computed(() => unwrapList(payload.value?.data ?? payload.value))

const nameConflict = computed(() => {
  if (!form.value.name.trim()) return false
  return rows.value.some(
    (t) => t.name.toLowerCase() === form.value.name.trim().toLowerCase() && t.id !== editing.value?.id,
  )
})

const dialog = ref(false)
const editing = ref(null)
const form = ref({ name: '' })
const saveLoading = ref(false)

const deleteTarget = ref(null)
const deleteLoading = ref(false)

function openCreate() {
  editing.value = null
  form.value = { name: '' }
  dialog.value = true
}

function openEdit(row) {
  editing.value = row
  form.value = { name: row.name }
  dialog.value = true
}

const saveMutation = useMutation({
  mutationFn: () =>
    editing.value ? tagService.adminUpdate(editing.value.id, form.value) : tagService.adminCreate(form.value),
  onSuccess: () => {
    toast.success('Đã lưu')
    qc.invalidateQueries({ queryKey: ['admin', 'tags'] })
    dialog.value = false
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { saveLoading.value = false },
})

const delMutation = useMutation({
  mutationFn: (id) => tagService.adminDelete(id),
  onSuccess: () => {
    toast.success('Đã xóa')
    qc.invalidateQueries({ queryKey: ['admin', 'tags'] })
    deleteTarget.value = null
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { deleteLoading.value = false },
})

function requestDelete(tag) {
  deleteTarget.value = tag
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
      title="Tags"
      subtitle="Từ khóa gắn tài liệu — chỉnh sửa để đồng bộ facet tìm kiếm và gợi ý."
    >
      <template #actions>
        <Button label="Thêm tag" icon="pi pi-plus" @click="openCreate" />
      </template>
    </AdminPageHeader>

    <section class="rounded-2xl border border-slate-200/70 bg-gradient-to-r from-white to-violet-50/60 p-4 shadow-soft">
      <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-slate-500">Tổng tags</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ rows.length }}</p>
        </div>
        <div class="flex flex-wrap gap-1.5">
          <span
            v-for="t in rows.slice(0, 6)"
            :key="t.id"
            class="rounded-full bg-violet-100 px-2.5 py-0.5 text-xs font-medium text-violet-700"
          >
            {{ t.name }}
          </span>
          <span v-if="rows.length > 6" class="text-xs text-slate-400">+{{ rows.length - 6 }} khác</span>
        </div>
      </div>
    </section>

    <AdminSurface>
      <DataTable
        :value="rows"
        :loading="isFetching"
        striped-rows
        paginator
        :rows="15"
        :rows-per-page-options="[15, 30, 50]"
        class="admin-datatable"
      >
        <Column field="name" header="Tên tag" sortable />
        <Column header="" style="width: 120px">
          <template #body="{ data }">
            <Button icon="pi pi-pencil" text rounded @click="openEdit(data)" />
            <Button icon="pi pi-trash" severity="danger" text rounded @click="requestDelete(data)" />
          </template>
        </Column>
      </DataTable>
    </AdminSurface>

    <Dialog v-model:visible="dialog" modal header="Tag" class="w-[min(420px,95vw)]">
      <div class="space-y-4 py-2">
        <div>
          <label class="mb-1 block text-sm font-medium">Tên tag</label>
          <InputText v-model="form.name" placeholder="Nhập tên tag..." class="w-full" fluid />
          <p v-if="nameConflict" class="mt-1.5 text-xs text-rose-500">
            <span>⚠ Tên tag này đã tồn tại.</span>
          </p>
        </div>
        <Button
          label="Lưu"
          :loading="saveLoading"
          :disabled="nameConflict || !form.name.trim()"
          class="w-full"
          @click="() => { saveLoading = true; saveMutation.mutate() }"
        />
      </div>
    </Dialog>

    <ConfirmDialog
      :visible="deleteTarget !== null"
      :message="`Bạn có chắc muốn xóa tag &quot;${deleteTarget?.name}&quot;? Hành động không thể hoàn tác.`"
      :loading="deleteLoading"
      @update:visible="(v) => { if (!v) deleteTarget = null }"
      @confirm="confirmDelete"
    />
  </div>
</template>
