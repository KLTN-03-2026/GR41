<script setup>
import { computed, ref, watch, onMounted, onUnmounted } from 'vue'
import { Icon } from '@iconify/vue'
import { useRoute, useRouter } from 'vue-router'
import { debounce } from '@/composables/useDebounce'
import { searchService } from '@/services/searchService'
import { unwrapList } from '@/utils/apiHelpers'

const route = useRoute()
const router = useRouter()
const q = ref(typeof route.query.q === 'string' ? route.query.q : '')
const suggestions = ref([])
const trendingKeywords = ref([])
const open = ref(false)
const loadingSuggest = ref(false)
const loadingTrending = ref(false)
const canSubmit = computed(() => q.value.trim().length > 0)
const isEmptyQuery = computed(() => q.value.trim().length === 0)
const panelItems = computed(() => (isEmptyQuery.value ? trendingKeywords.value : suggestions.value))
const panelLoading = computed(() => (isEmptyQuery.value ? loadingTrending.value : loadingSuggest.value))

/* ── Panel positioning ──────────────────────────────────── */
const inputWrapRef = ref(null)
const panelRef = ref(null)
const panelStyle = ref({})

function measureInput() {
  if (!inputWrapRef.value) return
  const r = inputWrapRef.value.getBoundingClientRect()
  panelStyle.value = {
    top: `${r.bottom + 8}px`,
    left: `${r.left}px`,
    width: `${r.width}px`,
  }
}

watch(open, (v) => { if (v) measureInput() })

function closePanel() {
  open.value = false
}

function handlePointerDownOutside(event) {
  const target = event.target
  if (inputWrapRef.value?.contains(target) || panelRef.value?.contains(target)) return
  closePanel()
}

onMounted(() => {
  window.addEventListener('scroll', measureInput, { passive: true })
  window.addEventListener('resize', measureInput, { passive: true })
  document.addEventListener('pointerdown', handlePointerDownOutside)
})
onUnmounted(() => {
  window.removeEventListener('scroll', measureInput)
  window.removeEventListener('resize', measureInput)
  document.removeEventListener('pointerdown', handlePointerDownOutside)
})

/* ── Suggestions ────────────────────────────────────────── */
function normalizeItem(item) {
  return typeof item === 'string' ? item : item?.keyword || item?.title || item?.name || ''
}

async function fetchTrendingKeywords() {
  if (trendingKeywords.value.length || loadingTrending.value) return
  loadingTrending.value = true
  try {
    const raw = await searchService.trending()
    const items = Array.isArray(raw?.trending) ? raw.trending : unwrapList(raw)
    trendingKeywords.value = items.slice(0, 8)
  } catch {
    trendingKeywords.value = []
  } finally {
    loadingTrending.value = false
  }
}

const fetchSuggestions = debounce(async () => {
  const term = q.value.trim()
  if (term.length < 2) { suggestions.value = []; return }
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

watch(() => route.query.q, (v) => { q.value = typeof v === 'string' ? v : '' })
watch(q, () => {
  const term = q.value.trim()
  if (term === '') {
    suggestions.value = []
    fetchTrendingKeywords()
    return
  }
  if (term.length < 2) {
    suggestions.value = []
    return
  }
  fetchSuggestions()
})

function onFocus() {
  open.value = true
  if (isEmptyQuery.value) fetchTrendingKeywords()
}

function onSubmit() {
  if (!canSubmit.value) return
  closePanel()
  router.push({ name: 'search', query: { q: q.value.trim() } })
}

function pick(s) {
  q.value = normalizeItem(s)
  onSubmit()
}
</script>

<template>
  <form class="relative w-full max-w-3xl" @submit.prevent="onSubmit">
    <div ref="inputWrapRef" class="relative">
      <Icon
        icon="mdi:magnify"
        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
      />
      <input
        v-model="q"
        type="search"
        autocomplete="off"
        placeholder="Tìm sách, tài liệu, chủ đề..."
        class="w-full rounded-full border border-slate-200 bg-white py-3.5 pl-12 pr-28 text-slate-900 shadow-inner outline-none ring-primary-blue/30 focus:border-primary-blue focus:ring-2"
        @focus="onFocus"
        @blur="setTimeout(closePanel, 120)"
        @keydown.escape="closePanel"
      />
      <button
        type="submit"
        :disabled="!canSubmit"
        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full px-5 py-2 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-100 enabled:bg-primary-blue enabled:hover:bg-blue-700"
      >
        Tìm
      </button>
    </div>

    <!--
      Teleport to <body> so the panel is NEVER inside any ancestor
      stacking context — it always renders on top at z-index 9999.
      position:fixed + measured coordinates track the input position.
    -->
    <Teleport to="body">
      <div
        v-if="open && (panelItems.length || panelLoading)"
        ref="panelRef"
        class="fixed z-[9999] max-h-72 overflow-y-auto overflow-x-hidden rounded-xl border border-slate-100 bg-white py-2 shadow-2xl scrollbar-dialog"
        :style="panelStyle"
      >
        <div
          v-if="isEmptyQuery"
          class="border-b border-slate-100 px-4 pb-2 pt-1 text-xs font-semibold uppercase tracking-wide text-slate-500"
        >
          Từ khóa thịnh hành
        </div>
        <button
          v-for="(s, idx) in panelItems"
          :key="idx"
          type="button"
          class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-slate-700 transition-colors hover:bg-slate-50"
          @mousedown.prevent="pick(s)"
        >
          <Icon
            :icon="isEmptyQuery ? 'mdi:trending-up' : 'mdi:magnify'"
            class="h-3.5 w-3.5 flex-shrink-0 text-slate-400"
          />
          <span class="min-w-0 flex-1 truncate">{{ normalizeItem(s) }}</span>
          <span
            v-if="isEmptyQuery && s.count"
            class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-500"
          >
            {{ s.count }}
          </span>
        </button>
        <p
          v-if="panelLoading && !panelItems.length"
          class="flex items-center gap-2 px-4 py-2.5 text-xs text-slate-400"
        >
          <Icon icon="mdi:loading" class="h-3.5 w-3.5 animate-spin" />
          {{ isEmptyQuery ? 'Đang tải từ khóa thịnh hành...' : 'Đang gợi ý...' }}
        </p>
      </div>
    </Teleport>
  </form>
</template>
