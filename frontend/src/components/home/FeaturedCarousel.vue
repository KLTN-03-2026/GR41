<script setup>
import { ref, computed, reactive, onMounted, onUnmounted, nextTick } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import { unwrapList } from '@/utils/apiHelpers'
import { formatNumber } from '@/utils/formatters'

const props = defineProps({
  items: { type: [Array, Object], default: () => [] },
  loading: { type: Boolean, default: false },
})

const scroller = ref(null)
const slides = computed(() => unwrapList(props.items).slice(0, 12))

// Append 3 clones of first slides for seamless forward-loop
const CLONES = 3
const loopSlides = computed(() => {
  const s = slides.value
  if (!s.length) return s
  return [...s, ...s.slice(0, CLONES)]
})

const activeDot = ref(0)
const tilts = reactive({})

/* ── 3D tilt ─────────────────────────────────── */
function cardStyle(id) {
  const t = tilts[id]
  if (!t) return {}
  return { transform: `perspective(900px) rotateY(${t.ry}deg) rotateX(${t.rx}deg) scale3d(1.02,1.02,1)` }
}

function onCardMove(e, id) {
  const r = e.currentTarget.getBoundingClientRect()
  const px = (e.clientX - r.left) / r.width - 0.5
  const py = (e.clientY - r.top) / r.height - 0.5
  tilts[id] = { ry: px * 10, rx: -py * 10 }
}

function onCardLeave(id) { delete tilts[id] }

/* ── Scroll helpers ──────────────────────────── */
function getItemWidth() {
  const el = scroller.value
  if (!el?.firstElementChild) return 260
  return el.firstElementChild.getBoundingClientRect().width
}

function scrollToIndex(i, behavior = 'smooth') {
  const el = scroller.value
  if (!el) return
  el.scrollTo({ left: i * (getItemWidth() + 16), behavior })
}

/* ── Auto-scroll (infinite forward loop) ─────── */
let autoTimer = null
let isWrapping = false
const paused = ref(false)

function autoAdvance() {
  if (paused.value || isWrapping || !slides.value.length) return
  const next = activeDot.value + 1
  if (next >= slides.value.length) {
    // Scroll to clone (seamless), then instant-jump back to slide 0
    isWrapping = true
    scrollToIndex(slides.value.length, 'smooth')
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

/* ── Arrow buttons ───────────────────────────── */
function scrollByDir(dir) {
  stopAuto()
  const total = slides.value.length
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

function goToSlide(i) {
  stopAuto()
  activeDot.value = i
  scrollToIndex(i, 'smooth')
  setTimeout(startAuto, 3000)
}

/* ── Scroll sync (for dot update during manual drag) */
function onScroll() {
  if (isWrapping) return
  const el = scroller.value
  if (!el || !slides.value.length) return
  const idx = Math.round(el.scrollLeft / (getItemWidth() + 16))
  if (idx < slides.value.length) activeDot.value = idx
}

/* ── Lifecycle ───────────────────────────────── */
onMounted(() => nextTick(startAuto))
onUnmounted(stopAuto)

/* ── Metadata helpers ────────────────────────── */
function docCategory(doc) {
  return doc?.category?.name || doc?.categories?.[0]?.name || 'Danh mục'
}

function docRating(doc) {
  const r = doc?.avg_rating ?? doc?.average_rating
  if (r == null) return null
  return Number(r).toFixed(1)
}
</script>

<template>
  <section
    id="featured-section"
    class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
  >
    <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Nổi bật</p>
        <h2 class="font-display mt-2 text-3xl font-bold tracking-tight text-ink-900 md:text-4xl">
          Tài liệu được đề xuất
        </h2>
        <p class="mt-2 max-w-xl text-ink-500">
          Bộ sưu tập được tuyển chọn — nội dung rõ ràng, dễ đọc.
        </p>
      </div>
      <div class="flex items-center gap-2">
        <button
          type="button"
          class="flex h-11 w-11 items-center justify-center rounded-full border border-ink-900/10 bg-white/90 text-ink-700 opacity-90 shadow-soft backdrop-blur transition hover:opacity-100 hover:shadow-lift focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 active:scale-95 motion-reduce:active:scale-100"
          aria-label="Cuộn trái"
          @click="scrollByDir(-1)"
        >
          <Icon icon="mdi:chevron-left" class="h-7 w-7" aria-hidden="true" />
        </button>
        <button
          type="button"
          class="flex h-11 w-11 items-center justify-center rounded-full border border-ink-900/10 bg-white/90 text-ink-700 opacity-90 shadow-soft backdrop-blur transition hover:opacity-100 hover:shadow-lift focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 active:scale-95 motion-reduce:active:scale-100"
          aria-label="Cuộn phải"
          @click="scrollByDir(1)"
        >
          <Icon icon="mdi:chevron-right" class="h-7 w-7" aria-hidden="true" />
        </button>
        <RouterLink
          to="/search"
          class="ml-2 hidden items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 sm:inline-flex"
        >
          Xem thêm
          <Icon icon="mdi:arrow-right" class="h-4 w-4" aria-hidden="true" />
        </RouterLink>
      </div>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="flex snap-x snap-mandatory gap-4 overflow-hidden pb-2">
      <div
        v-for="n in 4"
        :key="'sk-' + n"
        class="relative aspect-[3/4] min-w-[62%] shrink-0 snap-start overflow-hidden rounded-2xl bg-surface-soft sm:min-w-[48%] md:min-w-[38%] lg:min-w-[26%]"
      >
        <div class="absolute inset-0 bg-ink-300/20" />
        <div class="animate-shimmer motion-reduce:animate-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/40 to-transparent bg-[length:200%_100%]" />
      </div>
    </div>

    <!-- Carousel (loopSlides = real + CLONES copies of first items) -->
    <div
      v-else
      ref="scroller"
      class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
      @scroll.passive="onScroll"
    >
      <RouterLink
        v-for="(doc, i) in loopSlides"
        :key="`${doc.id}-${i}`"
        :to="{ name: 'document.detail', params: { slug: doc.slug } }"
        class="featured-card card-in group relative min-w-[62%] snap-start overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-ink-900/[0.06] transition duration-300 ease-out hover:z-10 hover:-translate-y-1 hover:shadow-lift motion-reduce:hover:translate-y-0 sm:min-w-[48%] md:min-w-[38%] lg:min-w-[26%]"
        :style="{ '--card-delay': `${Math.min(i, 11) * 80}ms`, ...cardStyle(doc.id) }"
        @mousemove="onCardMove($event, doc.id)"
        @mouseleave="onCardLeave(doc.id)"
      >
        <div class="relative aspect-[3/4] overflow-hidden bg-slate-100">
          <img
            :src="doc.cover_image || 'https://placehold.co/480x640/e2e8f0/64748b?'"
            :alt="doc.title"
            width="480"
            height="640"
            loading="lazy"
            class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.08] motion-reduce:group-hover:scale-100"
          />
          <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-900/70 via-transparent to-transparent to-60%" />

          <div class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-amber-500 to-amber-400 px-2.5 py-1 text-xs font-bold text-ink-900 shadow-soft">
            <Icon icon="mdi:star-four-points" class="h-3.5 w-3.5" aria-hidden="true" />
            Nổi bật
          </div>
          <div class="absolute right-3 top-3 rounded-full bg-white/20 px-2.5 py-1 text-xs font-medium text-white ring-1 ring-white/30 backdrop-blur-md">
            {{ docCategory(doc) }}
          </div>

          <div class="absolute bottom-0 left-0 right-0 p-4 text-left text-white">
            <h3 class="featured-title relative z-[1] line-clamp-2 text-lg font-bold leading-snug drop-shadow-sm">
              {{ doc.title }}
            </h3>
            <p class="mt-1 line-clamp-1 text-sm text-white/85">{{ doc.author || '—' }}</p>
            <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-white/75">
              <span class="inline-flex items-center gap-1">
                <Icon icon="mdi:eye-outline" class="h-4 w-4" aria-hidden="true" />
                {{ formatNumber(doc.view_count) }}
              </span>
              <span v-if="docRating(doc)" class="inline-flex items-center gap-1 text-amber-200">
                <Icon icon="mdi:star" class="h-4 w-4" aria-hidden="true" />
                {{ docRating(doc) }}
              </span>
            </div>
          </div>
        </div>
      </RouterLink>
    </div>

    <!-- Dots (only real slides) -->
    <div v-if="!loading && slides.length" class="mt-2 flex justify-center gap-2">
      <button
        v-for="n in Math.min(slides.length, 8)"
        :key="n - 1"
        type="button"
        class="h-2 rounded-full transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
        :class="n - 1 === activeDot ? 'w-8 bg-brand-600' : 'w-2 bg-ink-300 hover:bg-ink-400'"
        :aria-label="`Slide ${n}`"
        @click="goToSlide(n - 1)"
      />
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
.featured-card {
  transform-style: preserve-3d;
  will-change: transform;
}
.featured-title::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -2px;
  height: 2px;
  width: 0;
  background: linear-gradient(90deg, #6ee7b7, #67e8f9);
  transition: width 0.35s ease;
  border-radius: 2px;
}
.group:hover .featured-title::after { width: 100%; }
@media (prefers-reduced-motion: reduce) {
  .card-in { animation: none; }
  .featured-title::after { display: none; }
}
</style>
