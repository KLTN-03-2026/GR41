<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { unwrapList } from '@/utils/apiHelpers'
import { formatNumber } from '@/utils/formatters'

const props = defineProps({
  items: { type: [Array, Object], default: null },
  loading: { type: Boolean, default: false },
})

const scroller = ref(null)
const rows = computed(() => unwrapList(props.items))
const activeDot = ref(0)

const CLONES = 3
const loopRows = computed(() => {
  const s = rows.value
  if (!s.length) return s
  return [...s, ...s.slice(0, CLONES)]
})

/* ── Auto-scroll (infinite loop) ──────────────── */
let autoTimer = null
let isWrapping = false
const paused = ref(false)

function getItemWidth() {
  const el = scroller.value
  if (!el?.firstElementChild) return 300
  return el.firstElementChild.getBoundingClientRect().width
}

function scrollToIndex(i, behavior = 'smooth') {
  const el = scroller.value
  if (!el) return
  el.scrollTo({ left: i * (getItemWidth() + 16), behavior })
}

function autoAdvance() {
  if (paused.value || isWrapping || !rows.value.length) return
  const next = activeDot.value + 1
  if (next >= rows.value.length) {
    isWrapping = true
    scrollToIndex(rows.value.length, 'smooth')
    setTimeout(() => {
      const el = scroller.value
      if (el) el.scrollLeft = 0
      activeDot.value = 0
      isWrapping = false
    }, 550)
  } else {
    activeDot.value = next
    scrollToIndex(next, 'smooth')
  }
}

function startAuto() {
  stopAuto()
  autoTimer = setInterval(autoAdvance, 2000)
}

function stopAuto() {
  if (autoTimer !== null) { clearInterval(autoTimer); autoTimer = null }
}

function scrollByDir(dir) {
  stopAuto()
  const total = rows.value.length
  if (!total) return
  const newIdx = activeDot.value + dir
  if (newIdx < 0) {
    activeDot.value = total - 1
    scrollToIndex(total - 1, 'smooth')
  } else if (newIdx >= total) {
    isWrapping = true
    scrollToIndex(total, 'smooth')
    setTimeout(() => {
      const el = scroller.value
      if (el) el.scrollLeft = 0
      activeDot.value = 0
      isWrapping = false
    }, 550)
  } else {
    activeDot.value = newIdx
    scrollToIndex(newIdx, 'smooth')
  }
  setTimeout(startAuto, 3000)
}

function onScroll() {
  if (isWrapping) return
  const el = scroller.value
  if (!el || !rows.value.length) return
  const idx = Math.round(el.scrollLeft / (getItemWidth() + 16))
  if (idx < rows.value.length) activeDot.value = idx
}

onMounted(() => nextTick(startAuto))
onUnmounted(stopAuto)

function rating(doc) {
  const r = doc?.avg_rating ?? doc?.average_rating
  if (r == null) return null
  return Number(r).toFixed(1)
}

function langBadge(lang) {
  if (!lang) return null
  const l = lang.toLowerCase()
  if (l === 'vi' || l === 'vietnamese') return { label: 'VI', cls: 'bg-blue-500/90 text-white' }
  if (l === 'en' || l === 'english') return { label: 'EN', cls: 'bg-emerald-500/90 text-white' }
  return { label: lang.slice(0, 2).toUpperCase(), cls: 'bg-slate-500/90 text-white' }
}
</script>

<template>
  <section
    class="py-10"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Mới nhất</p>
          <h2 class="font-display mt-1 text-3xl font-bold tracking-tight text-ink-900 md:text-4xl">
            Mới cập nhật
          </h2>
          <p class="mt-1 max-w-xl text-ink-500">Những tài liệu vừa được thêm vào hệ thống.</p>
        </div>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="flex h-10 w-10 items-center justify-center rounded-full border border-ink-900/10 bg-white/90 text-ink-700 shadow-soft transition hover:shadow-lift hover:opacity-100 active:scale-95"
            aria-label="Cuộn trái"
            @click="scrollByDir(-1)"
          >
            <Icon icon="mdi:chevron-left" class="h-6 w-6" aria-hidden="true" />
          </button>
          <button
            type="button"
            class="flex h-10 w-10 items-center justify-center rounded-full border border-ink-900/10 bg-white/90 text-ink-700 shadow-soft transition hover:shadow-lift hover:opacity-100 active:scale-95"
            aria-label="Cuộn phải"
            @click="scrollByDir(1)"
          >
            <Icon icon="mdi:chevron-right" class="h-6 w-6" aria-hidden="true" />
          </button>
        </div>
      </div>

      <LoadingSpinner v-if="loading && rows.length === 0" />
      <div v-else-if="loading" class="h-52 animate-pulse rounded-2xl bg-surface-sunken" />

      <!-- Cards -->
      <div v-else class="relative">
        <div
          ref="scroller"
          class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
          @scroll.passive="onScroll"
        >
          <RouterLink
            v-for="(doc, i) in loopRows"
            :key="`${doc.id}-${i}`"
            :to="{ name: 'document.detail', params: { slug: doc.slug } }"
            class="recent-card card-in group flex min-w-[min(100%,300px)] shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-ink-900/5 bg-white shadow-soft ring-1 ring-ink-900/[0.04] transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-lift motion-reduce:hover:translate-y-0 sm:min-w-[280px]"
            :style="{ '--card-delay': `${Math.min(i, 10) * 60}ms` }"
          >
            <!-- Cover image -->
            <div class="relative h-44 overflow-hidden bg-slate-100">
              <img
                :src="doc.cover_image || 'https://placehold.co/400x300/e2e8f0/64748b?'"
                :alt="doc.title"
                width="400"
                height="300"
                loading="lazy"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.06] motion-reduce:group-hover:scale-100"
              />
              <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-900/50 via-transparent to-transparent" />
              <!-- Lang badge -->
              <span
                v-if="langBadge(doc.language)"
                class="absolute right-2.5 top-2.5 rounded-full px-2 py-0.5 text-[10px] font-bold tracking-wide"
                :class="langBadge(doc.language).cls"
              >
                {{ langBadge(doc.language).label }}
              </span>
              <!-- Category pill -->
              <span
                v-if="doc.category?.name"
                class="absolute bottom-2.5 left-2.5 rounded-full bg-white/20 px-2 py-0.5 text-xs font-medium text-white backdrop-blur-sm ring-1 ring-white/20"
              >
                {{ doc.category.name }}
              </span>
            </div>

            <!-- Info -->
            <div class="flex flex-1 flex-col gap-2 p-3.5">
              <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-ink-900 group-hover:text-brand-600">
                {{ doc.title }}
              </h3>
              <p class="line-clamp-1 text-xs text-ink-500">{{ doc.author || '—' }}</p>
              <div class="mt-auto flex items-center gap-3 text-xs text-ink-400">
                <span class="inline-flex items-center gap-0.5">
                  <Icon icon="mdi:eye-outline" class="h-3.5 w-3.5" aria-hidden="true" />
                  {{ formatNumber(doc.view_count) }}
                </span>
                <span v-if="rating(doc)" class="inline-flex items-center gap-0.5 text-amber-500">
                  <Icon icon="mdi:star" class="h-3.5 w-3.5" aria-hidden="true" />
                  {{ rating(doc) }}
                </span>
                <span v-if="doc.published_year" class="ml-auto text-ink-300">
                  {{ doc.published_year }}
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
  from { opacity: 0; transform: translateY(16px) scale(0.98); }
  to { opacity: 1; transform: none; }
}
.card-in {
  animation: cardIn 0.55s cubic-bezier(0.22, 1, 0.36, 1) var(--card-delay) both;
}
@media (prefers-reduced-motion: reduce) {
  .card-in { animation: none; }
}
</style>
