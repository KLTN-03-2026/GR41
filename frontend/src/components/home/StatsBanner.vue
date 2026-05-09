<script setup>
import { ref, watch } from 'vue'
import { useIntersectionObserver } from '@vueuse/core'
import { useQuery } from '@tanstack/vue-query'
import { formatNumber } from '@/utils/formatters'
import http from '@/services/http'

const target = ref(null)
const intersected = ref(false)
const started = ref(false)

const displayDocs = ref(0)
const displayUsers = ref(0)
const displayDownloads = ref(0)

const { data: stats } = useQuery({
  queryKey: ['stats', 'public'],
  queryFn: () => http.get('/stats').then((r) => r.data),
  staleTime: 10 * 60 * 1000,
})

function animateTo(setter, end, duration = 1500) {
  const t0 = performance.now()
  function frame(now) {
    const t = Math.min(1, (now - t0) / duration)
    const eased = 1 - (1 - t) ** 3
    setter(Math.round(end * eased))
    if (t < 1) requestAnimationFrame(frame)
    else setter(end)
  }
  requestAnimationFrame(frame)
}

function tryStart() {
  if (!intersected.value || !stats.value || started.value) return
  started.value = true
  animateTo((v) => (displayDocs.value = v), stats.value.total_documents ?? 0)
  animateTo((v) => (displayUsers.value = v), stats.value.total_users ?? 0)
  animateTo((v) => (displayDownloads.value = v), stats.value.total_downloads ?? 0)
}

watch(stats, tryStart)

useIntersectionObserver(
  target,
  ([e]) => {
    if (e?.isIntersecting && !intersected.value) {
      intersected.value = true
      tryStart()
    }
  },
  { threshold: 0.2 },
)
</script>

<template>
  <div ref="target" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div
      class="grid grid-cols-2 gap-6 rounded-3xl bg-white/60 px-5 py-8 shadow-soft ring-1 ring-ink-900/5 backdrop-blur-xl sm:px-8 md:grid-cols-4 md:gap-0 md:divide-x md:divide-ink-900/10 md:px-0 md:py-10"
    >
      <div class="flex flex-col items-center text-center md:px-6">
        <span class="text-2xl" aria-hidden="true">📚</span>
        <p class="font-display mt-2 text-2xl font-bold tabular-nums text-ink-900 md:text-3xl">
          {{ formatNumber(displayDocs) }}+
        </p>
        <p class="mt-1 text-sm text-ink-500">Tài liệu</p>
      </div>
      <div class="flex flex-col items-center text-center md:px-6">
        <span class="text-2xl" aria-hidden="true">👥</span>
        <p class="font-display mt-2 text-2xl font-bold tabular-nums text-ink-900 md:text-3xl">
          {{ formatNumber(displayUsers) }}+
        </p>
        <p class="mt-1 text-sm text-ink-500">Người dùng</p>
      </div>
      <div class="flex flex-col items-center text-center md:px-6">
        <span class="text-2xl" aria-hidden="true">📥</span>
        <p class="font-display mt-2 text-2xl font-bold tabular-nums text-ink-900 md:text-3xl">
          {{ formatNumber(displayDownloads) }}+
        </p>
        <p class="mt-1 text-sm text-ink-500">Lượt tải</p>
      </div>
      <div class="flex flex-col items-center text-center md:px-6">
        <span class="text-2xl" aria-hidden="true">🤖</span>
        <p class="font-display mt-2 text-2xl font-bold text-ink-900 md:text-3xl">24/7</p>
        <p class="mt-1 text-sm text-ink-500">Trợ lý AI</p>
      </div>
    </div>
  </div>
</template>
