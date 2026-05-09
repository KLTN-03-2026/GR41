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
const open = ref(false)
const loadingSuggest = ref(false)
const canSubmit = computed(() => q.value.trim().length > 0)

/* ── Panel positioning ──────────────────────────────────── */
const inputWrapRef = ref(null)
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

onMounted(() => {
  window.addEventListener('scroll', measureInput, { passive: true })
  window.addEventListener('resize', measureInput, { passive: true })
})
onUnmounted(() => {
  window.removeEventListener('scroll', measureInput)
  window.removeEventListener('resize', measureInput)
})

/* ── Suggestions ────────────────────────────────────────── */
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
watch(q, () => { fetchSuggestions() })

function onSubmit() {
  if (!canSubmit.value) return
  open.value = false
  router.push({ name: 'search', query: { q: q.value.trim() } })
}

function pick(s) {
  q.value = typeof s === 'string' ? s : s.title || s.name || s.keyword || ''
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
        @focus="open = true"
        @blur="setTimeout(() => (open = false), 200)"
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
        v-if="open && (suggestions.length || loadingSuggest)"
        class="fixed z-[9999] max-h-72 overflow-y-auto overflow-x-hidden rounded-xl border border-slate-100 bg-white py-2 shadow-2xl scrollbar-dialog"
        :style="panelStyle"
      >
        <button
          v-for="(s, idx) in suggestions"
          :key="idx"
          type="button"
          class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-slate-700 transition-colors hover:bg-slate-50"
          @mousedown.prevent="pick(s)"
        >
          <Icon icon="mdi:magnify" class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" />
          {{ typeof s === 'string' ? s : s.title || s.name || s.keyword }}
        </button>
        <p
          v-if="loadingSuggest && !suggestions.length"
          class="flex items-center gap-2 px-4 py-2.5 text-xs text-slate-400"
        >
          <Icon icon="mdi:loading" class="h-3.5 w-3.5 animate-spin" />
          Đang gợi ý...
        </p>
      </div>
    </Teleport>
  </form>
</template>
