<script setup>
import { ref, computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Calendar from 'primevue/calendar'
import Dropdown from 'primevue/dropdown'
import Dialog from 'primevue/dialog'
import { adminChatbotService } from '@/services/adminChatbotService'
import { formatDateTime } from '@/utils/formatters'
import { unwrapList, unwrapMeta } from '@/utils/apiHelpers'
import { renderChatMessage } from '@/utils/chatMarkdown'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminFilterBar from '@/components/admin/AdminFilterBar.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import EmptyState from '@/components/common/EmptyState.vue'

const filters = ref({
  intent_id: null,
  date_from: null,
  date_to: null,
})
const appliedFilters = ref({ ...filters.value })
const page = ref(1)
const perPage = ref(20)
const dateError = ref('')
const selectedLog = ref(null)

function toApiDate(value) {
  if (!value) return null
  const date = value instanceof Date ? value : new Date(value)
  if (Number.isNaN(date.getTime())) return null
  const month = `${date.getMonth() + 1}`.padStart(2, '0')
  const day = `${date.getDate()}`.padStart(2, '0')
  return `${date.getFullYear()}-${month}-${day}`
}

const params = computed(() => ({
  intent_id: appliedFilters.value.intent_id || undefined,
  date_from: toApiDate(appliedFilters.value.date_from) || undefined,
  date_to: toApiDate(appliedFilters.value.date_to) || undefined,
  page: page.value,
  per_page: perPage.value,
}))

const { data: payload, isFetching, refetch } = useQuery({
  queryKey: ['admin', 'chatbot', 'logs', params],
  queryFn: () => adminChatbotService.logs(params.value),
})

const rows = computed(() => unwrapList(payload.value?.data ?? payload.value))
const meta = computed(() => unwrapMeta(payload.value?.data ?? payload.value))
const totalRecords = computed(() => Number(meta.value.total ?? rows.value.length))
const first = computed(() => (page.value - 1) * perPage.value)
const detailVisible = computed({
  get: () => selectedLog.value !== null,
  set: (value) => {
    if (!value) selectedLog.value = null
  },
})
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

function validateDates() {
  dateError.value = ''
  if (filters.value.date_from && filters.value.date_to) {
    const from = new Date(filters.value.date_from)
    const to = new Date(filters.value.date_to)
    if (from > to) {
      dateError.value = 'Ngày bắt đầu phải <= ngày kết thúc.'
      return false
    }
  }
  return true
}

function applyFilters() {
  if (!validateDates()) return
  page.value = 1
  appliedFilters.value = { ...filters.value }
}

function onPage(event) {
  page.value = event.page + 1
  perPage.value = event.rows
}

function openDetail(log) {
  selectedLog.value = log
}

function displayUser(log) {
  return log.user?.name || log.user_id || 'Guest'
}

function displayIntent(log) {
  return log.matched_intent || 'Fallback'
}
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
      <Button label="Lọc" icon="pi pi-filter" @click="applyFilters" />
    </AdminFilterBar>
    <p v-if="dateError" class="-mt-6 text-sm font-medium text-rose-600">{{ dateError }}</p>

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
      <DataTable
        :value="rows"
        :loading="isFetching"
        striped-rows
        paginator
        lazy
        :rows="perPage"
        :first="first"
        :total-records="totalRecords"
        :rows-per-page-options="[20, 50, 100]"
        class="admin-datatable"
        @page="onPage"
        @row-click="openDetail($event.data)"
      >
        <Column field="question" header="Câu hỏi">
          <template #body="{ data }">
            <span class="line-clamp-2 max-w-xs">{{ data.question }}</span>
          </template>
        </Column>
        <Column field="answer" header="Trả lời">
          <template #body="{ data }">
            <span class="line-clamp-2 max-w-xs text-slate-600">{{ data.answer }}</span>
          </template>
        </Column>
        <Column header="Intent">
          <template #body="{ data }">
            <span
              class="rounded-full px-2.5 py-1 text-xs font-semibold"
              :class="data.matched_intent ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700'"
            >
              {{ displayIntent(data) }}
            </span>
          </template>
        </Column>
        <Column header="User">
          <template #body="{ data }">
            {{ displayUser(data) }}
          </template>
        </Column>
        <Column header="Thời gian">
          <template #body="{ data }">
            {{ formatDateTime(data.created_at) }}
          </template>
        </Column>
        <template #empty>
          <EmptyState title="Không có nhật ký phù hợp" icon="mdi:message-alert-outline" />
        </template>
      </DataTable>
    </AdminSurface>

    <Dialog
      v-model:visible="detailVisible"
      modal
      header="Chi tiết nhật ký chatbot"
      class="w-[min(720px,96vw)]"
    >
      <div v-if="selectedLog" class="space-y-4 text-sm">
        <div>
          <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Câu hỏi</p>
          <p class="rounded-xl bg-slate-50 p-3 text-slate-900">{{ selectedLog.question }}</p>
        </div>
        <div>
          <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Câu trả lời</p>
          <div
            class="prose prose-sm max-w-none rounded-xl bg-slate-50 p-3 text-slate-800 [&_a]:text-blue-600 [&_ul]:list-disc [&_ul]:pl-5"
            v-html="renderChatMessage(selectedLog.answer)"
          />
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <div class="rounded-xl border border-slate-200 p-3">
            <p class="text-xs text-slate-500">Intent</p>
            <p class="mt-1 font-semibold text-slate-900">{{ displayIntent(selectedLog) }}</p>
          </div>
          <div class="rounded-xl border border-slate-200 p-3">
            <p class="text-xs text-slate-500">User</p>
            <p class="mt-1 font-semibold text-slate-900">{{ displayUser(selectedLog) }}</p>
          </div>
          <div class="rounded-xl border border-slate-200 p-3">
            <p class="text-xs text-slate-500">Timestamp</p>
            <p class="mt-1 font-semibold text-slate-900">{{ formatDateTime(selectedLog.created_at) }}</p>
          </div>
        </div>
      </div>
    </Dialog>
  </div>
</template>
