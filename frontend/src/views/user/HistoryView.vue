<script setup>
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import TabView from 'primevue/tabview'
import TabPanel from 'primevue/tabpanel'
import { formatDateTime } from '@/utils/formatters'
import { profileService } from '@/services/profileService'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import { unwrapList } from '@/utils/apiHelpers'

const { data: payload, isLoading, isError } = useQuery({
  queryKey: ['profile', 'history'],
  queryFn: () => profileService.history(),
})

const historyPayload = computed(() => payload.value?.data ?? payload.value ?? {})
const searches = computed(() => unwrapList(historyPayload.value?.search_history))
const views = computed(() => unwrapList(historyPayload.value?.view_history))
const searchCount = computed(() => searches.value.length)
const viewCount = computed(() => views.value.length)
const lastSearchAt = computed(() => searches.value[0]?.searched_at || searches.value[0]?.created_at || null)
const lastViewAt = computed(() => views.value[0]?.viewed_at || views.value[0]?.created_at || null)
</script>

<template>
  <div class="history-page mx-auto max-w-5xl px-4 py-8 sm:px-6 md:py-10">
    <section
      class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-gradient-to-br from-white via-brand-50/35 to-indigo-50/45 p-6 shadow-soft md:p-7"
    >
      <div class="history-blob pointer-events-none absolute -right-10 -top-16 h-44 w-44 rounded-full bg-brand-300/35 blur-3xl" />
      <div class="history-blob pointer-events-none absolute -bottom-12 left-8 h-36 w-36 rounded-full bg-indigo-300/30 blur-3xl [animation-delay:1.4s]" />

      <div class="relative">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Hồ sơ người dùng</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">Lịch sử hoạt động</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">
          Theo dõi các từ khóa đã tìm và tài liệu đã xem gần đây để tiếp tục học tập nhanh hơn.
        </p>
      </div>

      <div class="relative mt-5 grid gap-3 sm:grid-cols-2">
        <div class="rounded-2xl border border-white/70 bg-white/75 p-4 backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md">
          <p class="text-xs uppercase tracking-wide text-slate-500">Tìm kiếm</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ searchCount }}</p>
          <p class="mt-1 text-xs text-slate-500">
            Gần nhất: {{ lastSearchAt ? formatDateTime(lastSearchAt) : 'Chưa có' }}
          </p>
        </div>
        <div class="rounded-2xl border border-white/70 bg-white/75 p-4 backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-md">
          <p class="text-xs uppercase tracking-wide text-slate-500">Đã xem</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ viewCount }}</p>
          <p class="mt-1 text-xs text-slate-500">
            Gần nhất: {{ lastViewAt ? formatDateTime(lastViewAt) : 'Chưa có' }}
          </p>
        </div>
      </div>
    </section>

    <LoadingSpinner v-if="isLoading" />
    <EmptyState
      v-else-if="isError"
      title="Không thể tải lịch sử"
      description="Vui lòng thử tải lại trang sau ít phút."
    />
    <TabView v-else class="mt-8">
      <TabPanel header="Tìm kiếm">
        <div class="mb-4 rounded-2xl border border-slate-200/80 bg-white/85 p-3 text-sm text-slate-600 shadow-soft">
          <span class="font-semibold text-slate-800">Mẹo:</span> Chạm vào từ khóa bạn hay dùng để tìm lại nhanh các tài liệu liên quan.
        </div>
        <EmptyState v-if="searches.length === 0" title="Chưa có lịch sử tìm kiếm" />
        <ul v-else class="space-y-3">
          <li
            v-for="(s, i) in searches"
            :key="s.id || i"
            class="history-item group flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white px-4 py-3 text-sm shadow-soft"
            :style="{ animationDelay: `${i * 45}ms` }"
          >
            <div class="min-w-0">
              <p class="truncate font-semibold text-slate-800">{{ s.keyword || s.query || s.q || '(trống)' }}</p>
              <p class="mt-1 text-xs text-slate-500">Kết quả phù hợp: {{ s.result_count ?? 0 }}</p>
            </div>
            <span class="shrink-0 rounded-lg bg-slate-50 px-2.5 py-1 text-xs text-slate-500 transition group-hover:bg-brand-50 group-hover:text-brand-700">
              {{ formatDateTime(s.searched_at || s.created_at) }}
            </span>
          </li>
        </ul>
      </TabPanel>
      <TabPanel header="Đã xem">
        <div class="mb-4 rounded-2xl border border-slate-200/80 bg-white/85 p-3 text-sm text-slate-600 shadow-soft">
          <span class="font-semibold text-slate-800">Gợi ý:</span> Danh sách này giúp bạn quay lại tài liệu đang đọc dở chỉ với 1 lần nhấn.
        </div>
        <EmptyState v-if="views.length === 0" title="Chưa có lịch sử xem" />
        <ul v-else class="space-y-3">
          <li
            v-for="(v, i) in views"
            :key="v.document?.id || i"
            class="history-item group flex items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white px-4 py-3 text-sm shadow-soft"
            :style="{ animationDelay: `${i * 45}ms` }"
          >
            <span class="line-clamp-2 font-medium text-slate-800">{{ v.document?.title || v.title }}</span>
            <span class="shrink-0 rounded-lg bg-slate-50 px-2.5 py-1 text-xs text-slate-500 transition group-hover:bg-indigo-50 group-hover:text-indigo-700">
              {{ formatDateTime(v.viewed_at || v.created_at) }}
            </span>
          </li>
        </ul>
      </TabPanel>
    </TabView>
  </div>
</template>

<style scoped>
.history-item {
  animation: historyItemIn 420ms ease both;
}

.history-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
  border-color: rgba(59, 130, 246, 0.2);
}

.history-blob {
  animation: blobFloat 6.5s ease-in-out infinite;
}

@keyframes historyItemIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes blobFloat {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}
</style>
