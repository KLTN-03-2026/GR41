<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { debounce } from '@/composables/useDebounce'
import { searchService } from '@/services/searchService'
import { unwrapList } from '@/utils/apiHelpers'

const router = useRouter()
const q = ref('')
const open = ref(false)
const suggestions = ref([])
const loadingSuggest = ref(false)
const focused = ref(false)
const phraseIdx = ref(0)

const PLACEHOLDER_PHRASES = [
  'Tìm sách lập trình Java...',
  'Tìm tài liệu AI...',
  'Hỏi trợ lý ảo...',
  'Khám phá kho tri thức...',
]

const CHIPS = ['Java', 'Python', 'Lập trình Web', 'AI', 'Tiếng Anh']

let phraseTimer = null

const fetchSuggestions = debounce(async () => {
  const term = q.value.trim()
  if (term.length < 2) {
    suggestions.value = []
    return
  }
  loadingSuggest.value = true
  try {
    const raw = await searchService.suggestions(term)
    suggestions.value = Array.isArray(raw?.suggestions) ? raw.suggestions : unwrapList(raw)
  } catch {
    suggestions.value = []
  } finally {
    loadingSuggest.value = false
  }
}, 300)

watch(q, () => fetchSuggestions())

onMounted(() => {
  phraseTimer = setInterval(() => {
    if (!focused.value && q.value.trim() === '') {
      phraseIdx.value = (phraseIdx.value + 1) % PLACEHOLDER_PHRASES.length
    }
  }, 3000)
})

onUnmounted(() => {
  if (phraseTimer) clearInterval(phraseTimer)
})

const showAnimatedPlaceholder = computed(() => !focused.value && !q.value.trim())
const displayPlaceholder = computed(() => PLACEHOLDER_PHRASES[phraseIdx.value])

function onSubmit() {
  open.value = false
  router.push({ name: 'search', query: { q: q.value.trim() } })
}

function pick(s) {
  q.value = typeof s === 'string' ? s : s.title || s.name || s.keyword || ''
  onSubmit()
}

function chipSearch(term) {
  q.value = term
  onSubmit()
}

function openAiChat() {
  window.dispatchEvent(new CustomEvent('tri-thuc-open-chat'))
}
</script>

<template>
  <section
    class="relative isolate flex min-h-[90vh] items-center justify-center overflow-hidden bg-hero-mesh bg-[length:200%_200%] animate-gradient-pan motion-reduce:animate-none pb-24 pt-28 text-white md:pt-32"
  >
    <div
      class="pointer-events-none absolute inset-0 opacity-[0.08] motion-reduce:opacity-[0.05] [background-image:radial-gradient(#fff_1px,transparent_1px)] [background-size:32px_32px]"
      aria-hidden="true"
    />

    <div
      class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-cyan-400/30 blur-3xl mix-blend-screen animate-float motion-reduce:animate-none"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -right-32 top-1/3 h-[28rem] w-[28rem] rounded-full bg-fuchsia-500/20 blur-3xl mix-blend-screen animate-float motion-reduce:animate-none [animation-delay:2s]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute -bottom-32 left-1/3 h-[26rem] w-[26rem] rounded-full bg-emerald-400/25 blur-3xl mix-blend-screen animate-float motion-reduce:animate-none [animation-delay:4s]"
      aria-hidden="true"
    />

    <div class="relative z-10 mx-auto w-full max-w-3xl px-4 text-center sm:px-6">
      <div
        class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm shadow-soft ring-1 ring-white/20 backdrop-blur-md motion-reduce:transform-none"
      >
        <span
          class="inline-block h-2 w-2 rounded-full bg-emerald-400 animate-pulse-soft motion-reduce:animate-none"
          aria-hidden="true"
        />
        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-white/90">
          🤖 Tích hợp Trợ lý AI
        </span>
      </div>

      <h1
        class="font-display text-4xl font-bold leading-[1.1] tracking-tight text-white sm:text-5xl md:text-6xl lg:text-7xl"
      >
        Thư viện số &amp;<br />
        Trợ lý <span class="bg-gradient-to-r from-emerald-300 to-cyan-300 bg-clip-text text-transparent">AI</span> cho học tập
      </h1>

      <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-white/80 sm:text-lg">
        Hàng ngàn tài liệu phục vụ giảng dạy và học tập — tra cứu trong vài giây, được gợi ý thông minh.
      </p>

      <form
        class="relative mx-auto mt-10 max-w-2xl"
        @submit.prevent="onSubmit"
      >
        <div class="absolute -left-52 top-1/2 z-20 hidden -translate-y-1/2 lg:block motion-reduce:hidden">
          <div
            class="animate-float motion-reduce:animate-none w-40 rotate-[-6deg] overflow-hidden rounded-xl border border-white/20 bg-white/15 shadow-lift backdrop-blur-md [animation-delay:1s]"
            aria-hidden="true"
          >
            <div class="aspect-[3/4] bg-slate-800/50">
              <img
                src="https://placehold.co/120x160/1e3a8a/93c5fd?text=Java"
                alt=""
                width="120"
                height="160"
                class="h-full w-full object-cover"
                loading="lazy"
              />
            </div>
            <p class="px-2 py-1.5 text-left text-xs font-medium text-white">Lập trình Java</p>
          </div>
        </div>
        <div
          class="absolute -right-40 top-1/3 z-20 hidden translate-y-2 lg:block motion-reduce:hidden"
          aria-hidden="true"
        >
          <div
            class="animate-float motion-reduce:animate-none w-36 rotate-[8deg] overflow-hidden rounded-xl border border-white/20 bg-white/10 shadow-lift backdrop-blur-md [animation-delay:2.5s]"
          >
            <div class="aspect-[3/4] bg-slate-800/50">
              <img
                src="https://placehold.co/120x160/312e81/a5b4fc?text=AI"
                alt=""
                width="120"
                height="160"
                class="h-full w-full object-cover"
                loading="lazy"
              />
            </div>
            <p class="px-2 py-1.5 text-left text-xs font-medium text-white">Trí tuệ nhân tạo</p>
          </div>
        </div>

        <div
          class="group relative z-10 flex h-16 items-center gap-1 rounded-full border border-slate-200/80 bg-white pl-5 pr-2 shadow-glow transition duration-300 ease-out focus-within:scale-[1.01] focus-within:ring-4 focus-within:ring-brand-300/40 motion-reduce:focus-within:scale-100"
        >
          <Icon
            icon="mdi:magnify"
            class="h-6 w-6 shrink-0 text-brand-600 transition motion-reduce:animate-none"
            :class="showAnimatedPlaceholder ? 'animate-pulse-soft opacity-70' : 'opacity-100'"
            aria-hidden="true"
          />
          <div class="relative min-w-0 flex-1 text-left">
            <label class="sr-only" for="hero-search">Tìm kiếm tài liệu</label>
            <input
              id="hero-search"
              v-model="q"
              type="search"
              autocomplete="off"
              class="h-12 w-full bg-transparent text-base text-ink-900 outline-none placeholder:text-transparent"
              @focus="open = true; focused = true"
              @blur="setTimeout(() => { open = false; focused = false }, 200)"
            >
            <div
              v-if="showAnimatedPlaceholder"
              class="pointer-events-none absolute left-0 top-1/2 flex -translate-y-1/2 items-center gap-0.5 text-slate-400"
            >
              <span class="text-base">{{ displayPlaceholder }}</span>
              <span class="inline-block h-5 w-px bg-brand-500 animate-caret motion-reduce:animate-none" />
            </div>
            <span v-else class="sr-only">{{ displayPlaceholder }}</span>
          </div>

          <div class="hidden h-8 w-px shrink-0 bg-slate-200 sm:block" aria-hidden="true" />

          <button
            type="button"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-brand-600 opacity-70 transition hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 active:scale-95 motion-reduce:active:scale-100 sm:h-11 sm:w-11"
            aria-label="Hỏi trợ lý AI"
            @mousedown.prevent="openAiChat"
          >
            <Icon icon="mdi:sparkles" class="h-6 w-6" aria-hidden="true" />
          </button>

          <button
            type="submit"
            class="inline-flex h-12 shrink-0 items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-brand-500 px-5 text-sm font-semibold text-white shadow-soft transition hover:shadow-lift focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 focus-visible:ring-offset-2 active:scale-[0.97] motion-reduce:active:scale-100"
            aria-label="Tìm kiếm"
          >
            Tìm
            <Icon icon="mdi:arrow-right" class="h-5 w-5 transition group-hover:translate-x-1" aria-hidden="true" />
          </button>
        </div>

        <div
          v-if="open && suggestions.length"
          class="absolute z-30 mt-2 max-h-72 w-full overflow-auto rounded-2xl border border-slate-100/80 bg-white py-2 shadow-lift"
        >
          <button
            v-for="(s, idx) in suggestions"
            :key="idx"
            type="button"
            class="flex w-full px-4 py-2.5 text-left text-sm text-ink-700 hover:bg-surface-soft"
            @mousedown.prevent="pick(s)"
          >
            {{ typeof s === 'string' ? s : s.title || s.name || s.keyword }}
          </button>
          <p v-if="loadingSuggest" class="px-4 py-2 text-xs text-ink-400">Đang gợi ý...</p>
        </div>
      </form>

      <div class="mt-6 flex flex-wrap justify-center gap-2">
        <button
          v-for="c in CHIPS"
          :key="c"
          type="button"
          class="rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-sm text-white/95 ring-1 ring-white/15 backdrop-blur-md transition hover:bg-white/20 hover:shadow-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 active:scale-[0.97] motion-reduce:active:scale-100"
          @click="chipSearch(c)"
        >
          {{ c }}
        </button>
      </div>

      <a
        href="#featured-section"
        class="mt-16 inline-flex flex-col items-center gap-2 text-sm font-medium text-white/85 transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/50 rounded-lg px-2"
      >
        <span class="motion-reduce:animate-none animate-bounce" aria-hidden="true">
          <Icon icon="mdi:chevron-down" class="h-8 w-8" />
        </span>
        Khám phá
      </a>
    </div>

    <svg
      class="absolute bottom-0 left-0 z-[1] h-[72px] w-full text-surface md:h-[100px] motion-reduce:opacity-100"
      viewBox="0 0 1440 120"
      preserveAspectRatio="none"
      aria-hidden="true"
    >
      <path fill="currentColor" d="M0,80 C360,140 1080,20 1440,80 L1440,120 L0,120 Z" />
    </svg>
  </section>
</template>
