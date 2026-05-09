<script setup>
import { ref, computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
import { adminChatbotService } from '@/services/adminChatbotService'
import { formatDateTime } from '@/utils/formatters'
import { unwrapList } from '@/utils/apiHelpers'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminFilterBar from '@/components/admin/AdminFilterBar.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'

const filters = ref({
  intent_id: null,
  date_from: null,
  date_to: null,
})

const params = computed(() => ({ ...filters.value }))

const { data: payload, isFetching, refetch } = useQuery({
  queryKey: ['admin', 'chatbot', 'logs', params],
  queryFn: () => adminChatbotService.logs(params.value),
})

const rows = computed(() => unwrapList(payload.value?.data ?? payload.value))
const logStats = computed(() => ({
  total: rows.value.length,
  withUser: rows.value.filter((r) => !!(r.user?.name || r.user_id)).length,
  withIntent: rows.value.filter((r) => !!(r.matched_intent || r.intent_id)).length,
}))

const { data: intentsRaw } = useQuery({
  queryKey: ['admin', 'chatbot', 'intents', 'opts'],
  queryFn: () => adminChatbotService.intents({ per_page: 200 }),
})

const intents = computed(() => unwrapList(intentsRaw.value?.data ?? intentsRaw.value))
</script>

<template>
  <div class="space-y-8">
    <AdminPageHeader
      title="Chatbot logs"
      subtitle="Soi lịch sử hội thoại, lọc theo intent và khoảng thời gian để xử lý sự cố."
    >
      <template #actions>
        <Button
          icon="pi pi-refresh"
          label="Làm mới"
          severity="secondary"
          :loading="isFetching"
          @click="refetch()"
        />
      </template>
    </AdminPageHeader>

    <AdminFilterBar>
      <Dropdown
        v-model="filters.intent_id"
        :options="intents"
        option-label="name"
        option-value="id"
        placeholder="Intent"
        show-clear
        class="w-56"
        :pt="{ overlay: { class: 'scrollbar-select-panel' } }"
      />
      <Calendar v-model="filters.date_from" show-button date-format="dd/mm/yy" placeholder="Từ ngày" />
      <Calendar v-model="filters.date_to" show-button date-format="dd/mm/yy" placeholder="Đến ngày" />
    </AdminFilterBar>

    <section class="grid gap-4 sm:grid-cols-3">
      <div class="rounded-2xl border border-slate-200/80 bg-white/90 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-slate-500">Bản ghi trên trang</p>
        <p class="mt-2 text-2xl font-bold text-slate-900">{{ logStats.total }}</p>
      </div>
      <div class="rounded-2xl border border-sky-200/70 bg-sky-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-sky-700">Có thông tin user</p>
        <p class="mt-2 text-2xl font-bold text-sky-800">{{ logStats.withUser }}</p>
      </div>
      <div class="rounded-2xl border border-violet-200/70 bg-violet-50/70 p-4 shadow-soft transition duration-300 hover:-translate-y-0.5 hover:shadow-lg">
        <p class="text-xs uppercase tracking-wide text-violet-700">Có intent match</p>
        <p class="mt-2 text-2xl font-bold text-violet-800">{{ logStats.withIntent }}</p>
      </div>
    </section>

    <AdminSurface>
      <DataTable :value="rows" :loading="isFetching" striped-rows paginator :rows="15" class="admin-datatable">
      <Column field="question" header="Câu hỏi" />
      <Column field="answer" header="Trả lời" />
      <Column field="matched_intent" header="Intent" />
      <Column header="User">
        <template #body="{ data }">
          {{ data.user?.name || data.user_id }}
        </template>
      </Column>
      <Column header="Thời gian">
        <template #body="{ data }">
          {{ formatDateTime(data.created_at) }}
        </template>
      </Column>
    </DataTable>
    </AdminSurface>
  </div>
</template>
