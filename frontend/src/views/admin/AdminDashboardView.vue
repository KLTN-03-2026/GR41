<script setup>
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import StatsCard from '@/components/admin/StatsCard.vue'
import ChartLineVisits from '@/components/admin/ChartLineVisits.vue'
import ChartPieCategory from '@/components/admin/ChartPieCategory.vue'
import ChartBarTopDocs from '@/components/admin/ChartBarTopDocs.vue'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { statsService } from '@/services/statsService'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { unwrapList } from '@/utils/apiHelpers'

const { data: overview, isLoading: loadOv } = useQuery({
  queryKey: ['admin', 'stats'],
  queryFn: statsService.overview,
})

const { data: charts, isLoading: loadCharts } = useQuery({
  queryKey: ['admin', 'stats', 'charts'],
  queryFn: statsService.charts,
})

const { data: keywordsRaw, isLoading: loadKw } = useQuery({
  queryKey: ['admin', 'stats', 'keywords'],
  queryFn: statsService.trendingKeywords,
})

const keywords = computed(() => {
  const v = keywordsRaw.value
  if (!v) return []
  if (Array.isArray(v.keywords)) return v.keywords
  return unwrapList(v)
})

const stats = computed(() => ({
  docs: overview.value?.total_documents ?? overview.value?.documents ?? 0,
  users: overview.value?.total_users ?? overview.value?.users ?? 0,
  dl: overview.value?.downloads_today ?? overview.value?.downloads ?? 0,
  bot: overview.value?.chatbot_questions_week ?? overview.value?.chatbot_week ?? 0,
}))
</script>

<template>
  <div class="space-y-10">
    <AdminPageHeader
      title="Tổng quan"
      subtitle="Theo dõi tài liệu, người dùng, traffic và hành vi chatbot trên một màn hình."
    />

    <div v-if="loadOv" class="flex justify-center py-24">
      <LoadingSpinner />
    </div>
    <div v-else class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
      <StatsCard
        label="Tài liệu"
        :value="stats.docs"
        icon="mdi:file-document-outline"
        :stagger-index="0"
      />
      <StatsCard
        label="Người dùng"
        :value="stats.users"
        icon="mdi:account-multiple-outline"
        accent="from-emerald-500 to-emerald-600"
        :stagger-index="1"
      />
      <StatsCard
        label="Tải trong ngày"
        :value="stats.dl"
        icon="mdi:download-outline"
        accent="from-amber-500 to-orange-600"
        :stagger-index="2"
      />
      <StatsCard
        label="Câu hỏi Chatbot (tuần)"
        :value="stats.bot"
        icon="mdi:robot-outline"
        accent="from-violet-500 to-purple-600"
        :stagger-index="3"
      />
    </div>

    <div v-if="loadCharts" class="flex justify-center py-16">
      <LoadingSpinner />
    </div>
    <div v-else class="grid gap-8 lg:grid-cols-2">
      <ChartLineVisits :data="charts" />
      <ChartPieCategory :data="charts" />
      <ChartBarTopDocs :data="charts" />
    </div>

    <section class="space-y-4 motion-safe:animate-fade-up motion-safe:[animation-delay:100ms] motion-reduce:animate-none">
      <h2 class="font-display text-lg font-semibold text-slate-900">Top từ khóa tuần qua</h2>
      <AdminSurface>
        <div v-if="loadKw" class="py-14 text-center text-sm text-slate-500">Đang tải...</div>
        <template v-else-if="keywords.length === 0">
          <p class="py-14 text-center text-sm text-slate-500">Chưa có dữ liệu.</p>
        </template>
        <DataTable v-else :value="keywords" size="small" striped-rows class="admin-datatable">
          <Column field="keyword" header="Từ khóa" />
          <Column field="count" header="Lượt tìm" />
        </DataTable>
      </AdminSurface>
    </section>
  </div>
</template>
