<script setup>
import { ref, computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dialog from 'primevue/dialog'
import InputSwitch from 'primevue/inputswitch'
import Chips from 'primevue/chips'
import Dropdown from 'primevue/dropdown'
import { adminChatbotService } from '@/services/adminChatbotService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import ConfirmDialog from '@/components/common/ConfirmDialog.vue'

const toast = useToast()
const qc = useQueryClient()

const { data: payload, isFetching } = useQuery({
  queryKey: ['admin', 'chatbot', 'intents'],
  queryFn: () => adminChatbotService.intents({ per_page: 200 }),
})

const rows = computed(() => unwrapList(payload.value?.data ?? payload.value))
const placeholderHints = [
  '{{popular_documents}}',
  '{{new_documents}}',
  '{{categories_list}}',
  '{{user_name}}',
]
const dataSourceOptions = [
  { label: 'Không dùng data source', value: '' },
  { label: 'Tài liệu phổ biến', value: 'documents.popular' },
  { label: 'Tài liệu mới nhất', value: 'documents.recent' },
  { label: 'Danh mục', value: 'categories.list' },
]
const intentStats = computed(() => {
  const items = rows.value
  const active = items.filter((i) => i.is_active).length
  return {
    total: items.length,
    active,
    inactive: items.length - active,
  }
})

const dialog = ref(false)
const editing = ref(null)
const deleteTarget = ref(null)
const formSubmitted = ref(false)
const form = ref({
  intent_key: '',
  name: '',
  keywords: [],
  response_template: '',
  data_source: '',
  is_active: true,
})

function openCreate() {
  editing.value = null
  formSubmitted.value = false
  form.value = {
    intent_key: '',
    name: '',
    keywords: [],
    response_template: '',
    data_source: '',
    is_active: true,
  }
  dialog.value = true
}

function openEdit(row) {
  editing.value = row
  formSubmitted.value = false
  form.value = {
    intent_key: row.intent_key || '',
    name: row.name || '',
    keywords: row.keywords || [],
    response_template: row.response_template || '',
    data_source: row.data_source || '',
    is_active: !!row.is_active,
  }
  dialog.value = true
}

const saveLoading = ref(false)
const deleteLoading = ref(false)
const formInvalid = computed(
  () =>
    !form.value.intent_key.trim() ||
    !form.value.name.trim() ||
    !form.value.response_template.trim() ||
    form.value.keywords.length === 0,
)
const deleteMessage = computed(
  () => `Bạn chắc chắn xóa intent "${deleteTarget.value?.intent_key || ''}"?`,
)

const saveMutation = useMutation({
  mutationFn: () =>
    editing.value
      ? adminChatbotService.updateIntent(editing.value.id, form.value)
      : adminChatbotService.createIntent(form.value),
  onSuccess: () => {
    toast.success(editing.value ? 'Cập nhật thành công' : 'Thêm intent thành công')
    qc.invalidateQueries({ queryKey: ['admin', 'chatbot', 'intents'] })
    dialog.value = false
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { saveLoading.value = false },
})

const toggleMutation = useMutation({
  mutationFn: (row) => adminChatbotService.toggleIntent(row.id),
  onSuccess: () => {
    toast.success('Cập nhật trạng thái thành công')
    qc.invalidateQueries({ queryKey: ['admin', 'chatbot', 'intents'] })
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
})
const togglePending = toggleMutation.isPending

const deleteMutation = useMutation({
  mutationFn: (id) => adminChatbotService.deleteIntent(id),
  onSuccess: () => {
    toast.success('Xóa intent thành công')
    qc.invalidateQueries({ queryKey: ['admin', 'chatbot', 'intents'] })
    deleteTarget.value = null
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { deleteLoading.value = false },
})

function requestDelete(row) {
  deleteTarget.value = row
}

function confirmDelete() {
  if (!deleteTarget.value) return
  deleteLoading.value = true
  deleteMutation.mutate(deleteTarget.value.id)
}

function submitForm() {
  formSubmitted.value = true
  if (formInvalid.value) return
  saveLoading.value = true
  saveMutation.mutate()
}
</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Chatbot intents"
      subtitle="Ánh xạ câu hỏi người dùng tới phản hồi có cấu trúc — bật/tắt từng intent trong bảng."
    >
      <template #actions>
        <Button label="Thêm intent" icon="pi pi-plus" @click="openCreate" />
      </template>
    </AdminPageHeader>

    <section class="grid gap-4 sm:grid-cols-3">
      <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-slate-500">Tổng intents</p>
        <p class="mt-2 text-2xl font-bold text-slate-900">{{ intentStats.total }}</p>
      </div>
      <div class="rounded-2xl border border-emerald-200/70 bg-emerald-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-emerald-700">Đang hoạt động</p>
        <p class="mt-2 text-2xl font-bold text-emerald-800">{{ intentStats.active }}</p>
      </div>
      <div class="rounded-2xl border border-slate-300/70 bg-slate-100/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-slate-700">Đang tắt</p>
        <p class="mt-2 text-2xl font-bold text-slate-800">{{ intentStats.inactive }}</p>
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
        <Column field="intent_key" header="Key" sortable />
        <Column field="name" header="Tên" sortable />
        <Column header="Keywords">
          <template #body="{ data }">
            <div class="flex max-w-xs flex-wrap gap-1.5">
              <span
                v-for="kw in (data.keywords || []).slice(0, 4)"
                :key="kw"
                class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700"
              >
                {{ kw }}
              </span>
              <span v-if="(data.keywords || []).length > 4" class="text-xs text-slate-400">
                +{{ data.keywords.length - 4 }}
              </span>
            </div>
          </template>
        </Column>
        <Column header="Response">
          <template #body="{ data }">
            <span class="line-clamp-2 max-w-xs text-sm text-slate-600">
              {{ data.response_template }}
            </span>
          </template>
        </Column>
        <Column field="data_source" header="Data source" sortable />
        <Column header="Hoạt động">
          <template #body="{ data }">
            <InputSwitch
              :model-value="data.is_active"
              :disabled="togglePending"
              @update:model-value="toggleMutation.mutate(data)"
            />
          </template>
        </Column>
        <Column header="Thao tác" style="width: 120px">
          <template #body="{ data }">
            <Button icon="pi pi-pencil" text rounded aria-label="Sửa" @click="openEdit(data)" />
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              aria-label="Xóa"
              @click="requestDelete(data)"
            />
          </template>
        </Column>
      </DataTable>
    </AdminSurface>

    <Dialog v-model:visible="dialog" modal header="Intent" class="w-[min(640px,96vw)]">
      <div class="grid gap-4 py-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">intent_key</label>
          <InputText v-model="form.intent_key" placeholder="wifi_password" class="w-full" fluid />
          <p v-if="formSubmitted && !form.intent_key.trim()" class="mt-1 text-xs text-rose-500">
            Vui lòng nhập intent_key.
          </p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">name</label>
          <InputText v-model="form.name" placeholder="Mật khẩu Wifi" class="w-full" fluid />
          <p v-if="formSubmitted && !form.name.trim()" class="mt-1 text-xs text-rose-500">
            Vui lòng nhập name.
          </p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">keywords</label>
          <Chips v-model="form.keywords" class="w-full" separator="," />
          <p class="mt-1 text-xs text-slate-500">Nhấn Enter để thêm chip, bấm X để xóa keyword.</p>
          <p v-if="formSubmitted && form.keywords.length === 0" class="mt-1 text-xs text-rose-500">
            Vui lòng nhập keywords.
          </p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">response_template</label>
          <Textarea
            v-model="form.response_template"
            rows="6"
            class="w-full"
            fluid
            placeholder="Mẫu phản hồi"
          />
          <p class="mt-1 text-xs text-slate-500">
            Placeholder hỗ trợ:
            <span
              v-for="hint in placeholderHints"
              :key="hint"
              class="ml-1 rounded bg-slate-100 px-1.5 py-0.5 font-mono"
            >
              {{ hint }}
            </span>
          </p>
          <p v-if="formSubmitted && !form.response_template.trim()" class="mt-1 text-xs text-rose-500">
            Vui lòng nhập response_template.
          </p>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">data_source</label>
          <Dropdown
            v-model="form.data_source"
            :options="dataSourceOptions"
            option-label="label"
            option-value="value"
            class="w-full"
          />
        </div>
        <div class="flex items-center gap-3">
          <span class="text-sm font-medium">Kích hoạt</span>
          <InputSwitch v-model="form.is_active" />
        </div>
        <Button
          label="Lưu"
          :loading="saveLoading"
          @click="submitForm"
        />
      </div>
    </Dialog>

    <ConfirmDialog
      :visible="deleteTarget !== null"
      :message="deleteMessage"
      :loading="deleteLoading"
      @update:visible="(v) => { if (!v) deleteTarget = null }"
      @confirm="confirmDelete"
    />
  </div>
</template>
