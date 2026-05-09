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
import { adminChatbotService } from '@/services/adminChatbotService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'

const toast = useToast()
const qc = useQueryClient()

const { data: payload, isFetching } = useQuery({
  queryKey: ['admin', 'chatbot', 'intents'],
  queryFn: () => adminChatbotService.intents({ per_page: 200 }),
})

const rows = computed(() => unwrapList(payload.value?.data ?? payload.value))
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

const saveMutation = useMutation({
  mutationFn: () =>
    editing.value
      ? adminChatbotService.updateIntent(editing.value.id, form.value)
      : adminChatbotService.createIntent(form.value),
  onSuccess: () => {
    toast.success('Đã lưu')
    qc.invalidateQueries({ queryKey: ['admin', 'chatbot', 'intents'] })
    dialog.value = false
  },
  onSettled: () => { saveLoading.value = false },
})

const toggleMutation = useMutation({
  mutationFn: (row) => adminChatbotService.updateIntent(row.id, { is_active: !row.is_active }),
  onSuccess: () => qc.invalidateQueries({ queryKey: ['admin', 'chatbot', 'intents'] }),
})
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
      <DataTable :value="rows" :loading="isFetching" striped-rows class="admin-datatable">
      <Column field="intent_key" header="Key" />
      <Column field="name" header="Tên" />
      <Column header="Hoạt động">
        <template #body="{ data }">
          <Button
            :label="data.is_active ? 'On' : 'Off'"
            :severity="data.is_active ? 'success' : 'secondary'"
            size="small"
            @click="toggleMutation.mutate(data)"
          />
        </template>
      </Column>
      <Column header="">
        <template #body="{ data }">
          <Button icon="pi pi-pencil" text rounded @click="openEdit(data)" />
        </template>
      </Column>
    </DataTable>
    </AdminSurface>

    <Dialog v-model:visible="dialog" modal header="Intent" class="w-[min(640px,96vw)]">
      <div class="grid gap-4 py-2">
        <InputText v-model="form.intent_key" placeholder="intent_key" class="w-full" fluid />
        <InputText v-model="form.name" placeholder="Tên hiển thị" class="w-full" fluid />
        <div>
          <label class="mb-1 block text-xs font-medium text-slate-600">Keywords</label>
          <Chips v-model="form.keywords" class="w-full" />
        </div>
        <Textarea v-model="form.response_template" rows="6" class="w-full" fluid placeholder="Mẫu phản hồi" />
        <InputText v-model="form.data_source" placeholder="data_source" class="w-full" fluid />
        <div class="flex items-center gap-3">
          <span class="text-sm font-medium">Kích hoạt</span>
          <InputSwitch v-model="form.is_active" />
        </div>
        <Button label="Lưu" :loading="saveLoading" @click="() => { saveLoading = true; saveMutation.mutate() }" />
      </div>
    </Dialog>
  </div>
</template>
