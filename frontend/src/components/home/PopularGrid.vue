<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { unwrapList } from '@/utils/apiHelpers'
import { formatNumber } from '@/utils/formatters'

const props = defineProps({
  items: { type: [Array, Object], default: null },
  loading: { type: Boolean, default: false },
})

const rows = computed(() => unwrapList(props.items))
const top = computed(() => rows.value.slice(0, 7))

function rating(doc) {
  const r = doc?.avg_rating ?? doc?.average_rating
  if (r == null) return null
  return Number(r).toFixed(1)
}

const RANK_META = [
  { emoji: '🥇', bg: 'from-amber-400 to-yellow-300', text: 'text-amber-900', border: 'ring-amber-300/60' },
  { emoji: '🥈', bg: 'from-slate-400 to-slate-300', text: 'text-slate-900', border: 'ring-slate-300/60' },
  { emoji: '🥉', bg: 'from-orange-400 to-amber-300', text: 'text-orange-900', border: 'ring-orange-300/60' },
]

function rankMeta(i) {
  return RANK_META[i] ?? { emoji: null, bg: 'from-slate-100 to-white', text: 'text-slate-600', border: 'ring-slate-200/40' }
}
</script>

<template>
  <section class="py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8 flex items-end justify-between gap-4">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-rose-500">Xu hướng</p>
          <h2 class="font-display mt-1 text-3xl font-bold tracking-tight text-ink-900 md:text-4xl">
            Phổ biến nhất
          </h2>
          <p class="mt-1 max-w-2xl text-ink-500">
            Tài liệu được xem và tải nhiều nhất tuần này.
          </p>
        </div>
        <RouterLink
          to="/search?sort=popular"
          class="hidden items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 sm:inline-flex"
        >
          Xem thêm <Icon icon="mdi:arrow-right" class="h-4 w-4" />
        </RouterLink>
      </div>

      <LoadingSpinner v-if="loading && top.length === 0" />

      <div v-else-if="loading" class="grid min-h-[300px] animate-pulse gap-4 rounded-2xl bg-surface-sunken lg:grid-cols-3" />

      <div v-else-if="top.length" class="grid gap-5 lg:grid-cols-3">
        <!-- LEFT: #1 Hero card -->
        <RouterLink
          v-if="top[0]"
          :to="{ name: 'document.detail', params: { slug: top[0].slug } }"
          class="pop-card card-in group relative flex flex-col overflow-hidden rounded-3xl bg-white shadow-xl ring-2 ring-amber-300/40 transition duration-300 hover:-translate-y-1 hover:shadow-2xl lg:row-span-2"
          :style="{ '--card-delay': '0ms' }"
        >
          <!-- Cover -->
          <div class="relative aspect-[3/4] overflow-hidden bg-slate-100">
            <img
              :src="top[0].cover_image || 'https://placehold.co/480x640/e2e8f0/64748b?'"
              :alt="top[0].title"
              width="480" height="640" loading="lazy"
              class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.06]"
            />
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />

            <!-- Rank badge -->
            <div class="absolute left-4 top-4 flex items-center gap-1.5 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 px-3 py-1.5 shadow-lg ring-2 ring-amber-300/60">
              <span class="text-base leading-none">🥇</span>
              <span class="text-xs font-black tracking-wide text-amber-900">#1 PHỔ BIẾN</span>
            </div>

            <!-- Flame decoration -->
            <div class="absolute right-4 top-4 rounded-full bg-rose-500/20 p-2 backdrop-blur-sm ring-1 ring-rose-400/30">
              <Icon icon="mdi:fire" class="h-5 w-5 text-rose-400" />
            </div>

            <!-- Info overlay -->
            <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
              <div v-if="top[0].category?.name" class="mb-2 inline-block rounded-full bg-white/20 px-2.5 py-0.5 text-xs backdrop-blur-sm">
                {{ top[0].category.name }}
              </div>
              <h3 class="font-display text-xl font-bold leading-snug drop-shadow-sm line-clamp-2">
                {{ top[0].title }}
              </h3>
              <p class="mt-1 line-clamp-1 text-sm text-white/80">{{ top[0].author || '—' }}</p>
              <div class="mt-3 flex items-center gap-3 text-xs text-white/70">
                <span class="inline-flex items-center gap-1">
                  <Icon icon="mdi:eye-outline" class="h-3.5 w-3.5" />
                  {{ formatNumber(top[0].view_count) }}
                </span>
                <span v-if="rating(top[0])" class="inline-flex items-center gap-1 text-amber-300">
                  <Icon icon="mdi:star" class="h-3.5 w-3.5" />
                  {{ rating(top[0]) }}
                </span>
              </div>
            </div>
          </div>

          <!-- CTA -->
          <div class="flex items-center justify-between bg-gradient-to-r from-amber-50 to-yellow-50 px-5 py-3.5">
            <span class="text-sm font-semibold text-amber-800">Xem tài liệu ngay</span>
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-400 text-amber-900 shadow transition group-hover:bg-amber-500">
              <Icon icon="mdi:arrow-right" class="h-4 w-4" />
            </span>
          </div>
        </RouterLink>

        <!-- RIGHT COLUMN: #2 – #7 compact cards -->
        <div class="flex flex-col gap-4 lg:col-span-2">
          <RouterLink
            v-for="(doc, slot) in top.slice(1, 7)"
            :key="doc.id"
            :to="{ name: 'document.detail', params: { slug: doc.slug } }"
            class="pop-card card-in group flex gap-4 overflow-hidden rounded-2xl bg-white p-3 shadow-soft ring-1 ring-ink-900/[0.06] transition duration-300 hover:-translate-y-0.5 hover:shadow-lift"
            :style="{ '--card-delay': `${(slot + 1) * 70}ms` }"
          >
            <!-- Cover thumbnail -->
            <div class="relative w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100">
              <div class="aspect-[3/4]">
                <img
                  :src="doc.cover_image || 'https://placehold.co/200x280/e2e8f0/64748b?'"
                  :alt="doc.title"
                  width="200" height="280" loading="lazy"
                  class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                />
              </div>
              <!-- Rank overlay on thumb -->
              <div
                class="absolute inset-0 flex items-end justify-end p-1"
              >
                <span
                  class="flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black shadow"
                  :class="`bg-gradient-to-br ${rankMeta(slot + 1).bg} ${rankMeta(slot + 1).text}`"
                >
                  {{ slot + 2 }}
                </span>
              </div>
            </div>

            <!-- Info -->
            <div class="flex min-w-0 flex-1 flex-col justify-center gap-1 py-0.5">
              <div class="flex items-start justify-between gap-2">
                <h3 class="line-clamp-2 flex-1 text-sm font-semibold leading-snug text-ink-900 group-hover:text-brand-600">
                  {{ doc.title }}
                </h3>
                <span v-if="rankMeta(slot + 1).emoji" class="shrink-0 text-lg leading-none">
                  {{ rankMeta(slot + 1).emoji }}
                </span>
              </div>
              <p class="line-clamp-1 text-xs text-ink-500">{{ doc.author || '—' }}</p>
              <div class="flex items-center gap-3 text-xs text-ink-400">
                <span class="inline-flex items-center gap-0.5">
                  <Icon icon="mdi:eye-outline" class="h-3.5 w-3.5" />
                  {{ formatNumber(doc.view_count) }}
                </span>
                <span v-if="rating(doc)" class="inline-flex items-center gap-0.5 text-amber-500">
                  <Icon icon="mdi:star" class="h-3.5 w-3.5" />
                  {{ rating(doc) }}
                </span>
                <span v-if="doc.category?.name" class="ml-auto hidden truncate text-[11px] text-ink-300 sm:block">
                  {{ doc.category.name }}
                </span>
              </div>
            </div>
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
@keyframes cardIn {
  from { opacity: 0; transform: translateY(20px) scale(0.96); }
  to { opacity: 1; transform: none; }
}
.card-in {
  animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) var(--card-delay) both;
}
@media (prefers-reduced-motion: reduce) {
  .card-in { animation: none; }
}
</style>
