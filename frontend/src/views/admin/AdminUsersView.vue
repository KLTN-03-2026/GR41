<script setup>
import { ref, computed, watch } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { debounce } from '@/composables/useDebounce'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Tag from 'primevue/tag'
import { adminUserService } from '@/services/adminUserService'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminFilterBar from '@/components/admin/AdminFilterBar.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import AdminUserFormDialog from '@/components/admin/AdminUserFormDialog.vue'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import { ROLES } from '@/constants'

const toast = useToast()
const qc = useQueryClient()

const rawSearch = ref('')
const filters = ref({ q: '', role: null, status: null })
const page = ref(1)

watch(rawSearch, debounce((v) => {
  filters.value.q = v
  page.value = 1
}, 500))
const deleteTarget = ref(null)
const deleteLoading = ref(false)

const userFormVisible = ref(false)
const userFormEditId = ref(null)

const params = computed(() => ({ page: page.value, ...filters.value }))

const { data: payload, isFetching } = useQuery({
  queryKey: ['admin', 'users', params],
  queryFn: () => adminUserService.list(params.value),
})

const rows = computed(() => unwrapList(payload.value?.items ?? payload.value?.data ?? payload.value))
const userStats = computed(() => {
  const items = rows.value
  const active = items.filter((u) => u.status === 'active').length
  const banned = items.filter((u) => u.status === 'banned').length
  return {
    total: items.length,
    active,
    banned,
    admins: items.filter((u) => (u.role?.slug || u.role) === ROLES.ADMIN).length,
  }
})

const delMutation = useMutation({
  mutationFn: (id) => adminUserService.delete(id),
  onSuccess: () => {
    toast.success('Đã xóa')
    qc.invalidateQueries({ queryKey: ['admin', 'users'] })
    deleteTarget.value = null
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => {
    deleteLoading.value = false
  },
})

const statusTargetId = ref(null)

const statusMutation = useMutation({
  mutationFn: ({ id, status }) => adminUserService.updateStatus(id, { status }),
  onMutate: (vars) => {
    statusTargetId.value = vars.id
  },
  onSettled: () => {
    statusTargetId.value = null
  },
  onSuccess: () => qc.invalidateQueries({ queryKey: ['admin', 'users'] }),
})

watch(userFormVisible, (v) => {
  if (!v) userFormEditId.value = null
})

function openUserFormCreate() {
  userFormEditId.value = null
  userFormVisible.value = true
}

function openUserFormEdit(id) {
  userFormEditId.value = id
  userFormVisible.value = true
}

function confirmDelete() {
  if (deleteTarget.value == null) return
  deleteLoading.value = true
  delMutation.mutate(deleteTarget.value)
}

</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Người dùng"
      subtitle="Quản lý vai trò, trạng thái tài khoản và thông tin hồ sơ người dùng."
    >
      <template #actions>
        <Button label="Thêm người dùng" icon="pi pi-plus" @click="openUserFormCreate" />
      </template>
    </AdminPageHeader>

    <AdminFilterBar>
      <InputText v-model="rawSearch" placeholder="Email / tên..." class="w-64" />
      <Dropdown
        v-model="filters.role"
        :options="[
          { label: 'Admin', value: ROLES.ADMIN },
          { label: 'Giáo viên', value: ROLES.TEACHER },
          { label: 'Sinh viên', value: ROLES.STUDENT },
        ]"
        option-label="label"
        option-value="value"
        placeholder="Vai trò"
        show-clear
        class="w-44"
      />
      <Dropdown
        v-model="filters.status"
        :options="[
          { label: 'Hoạt động', value: 'active' },
          { label: 'Khóa', value: 'banned' },
        ]"
        option-label="label"
        option-value="value"
        placeholder="Trạng thái"
        show-clear
        class="w-44"
      />
    </AdminFilterBar>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-slate-500">Tổng người dùng</p>
        <p class="mt-2 text-2xl font-bold text-slate-900">{{ userStats.total }}</p>
      </div>
      <div class="rounded-2xl border border-emerald-200/70 bg-emerald-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Đang hoạt động</p>
        <p class="mt-2 text-2xl font-bold text-emerald-800">{{ userStats.active }}</p>
      </div>
      <div class="rounded-2xl border border-rose-200/70 bg-rose-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-rose-700">Đã khóa</p>
        <p class="mt-2 text-2xl font-bold text-rose-800">{{ userStats.banned }}</p>
      </div>
      <div class="rounded-2xl border border-violet-200/70 bg-violet-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-violet-700">Quản trị viên</p>
        <p class="mt-2 text-2xl font-bold text-violet-800">{{ userStats.admins }}</p>
      </div>
    </section>

    <AdminSurface>
      <DataTable :value="rows" :loading="isFetching" striped-rows paginator :rows="15" :rows-per-page-options="[15, 30, 50]" class="admin-datatable">
        <Column field="name" header="Tên" />
        <Column field="email" header="Email" />
        <Column header="Vai trò">
          <template #body="{ data }">
            {{ data.role?.slug || data.role }}
          </template>
        </Column>
        <Column header="Trạng thái">
          <template #body="{ data }">
            <Tag
              :value="data.status === 'active' ? 'Hoạt động' : 'Khóa'"
              :severity="data.status === 'active' ? 'success' : 'danger'"
            />
          </template>
        </Column>
        <Column header="">
          <template #body="{ data }">
            <Button icon="pi pi-pencil" text rounded title="Sửa" @click="openUserFormEdit(data.id)" />
            <Button
              v-if="data.status === 'active'"
              icon="pi pi-ban"
              text
              rounded
              severity="warning"
              title="Khóa tài khoản"
              :loading="statusMutation.isPending && statusTargetId === data.id"
              @click="statusMutation.mutate({ id: data.id, status: 'banned' })"
            />
            <Button
              v-else
              icon="pi pi-lock-open"
              text
              rounded
              severity="success"
              title="Mở khóa"
              :loading="statusMutation.isPending && statusTargetId === data.id"
              @click="statusMutation.mutate({ id: data.id, status: 'active' })"
            />
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              title="Xóa"
              @click="deleteTarget = data.id"
            />
          </template>
        </Column>
      </DataTable>
    </AdminSurface>

    <AdminUserFormDialog v-model:visible="userFormVisible" :edit-user-id="userFormEditId" />

    <ConfirmDialog
      :visible="deleteTarget !== null"
      message="Xóa người dùng?"
      :loading="deleteLoading"
      @update:visible="(v) => { if (!v) deleteTarget = null }"
      @confirm="confirmDelete"
    />
  </div>
</template>
