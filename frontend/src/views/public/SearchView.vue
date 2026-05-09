<script setup>
import { ref, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import { keepPreviousData, useQuery } from '@tanstack/vue-query'
import { Icon } from '@iconify/vue'
import SearchBar from '@/components/search/SearchBar.vue'
import SearchResults from '@/components/search/SearchResults.vue'
import TrendingKeywords from '@/components/search/TrendingKeywords.vue'
import DocumentFilter from '@/components/document/DocumentFilter.vue'
import Pagination from '@/components/common/Pagination.vue'
import Dropdown from 'primevue/dropdown'
import { searchService } from '@/services/searchService'
import { categoryService } from '@/services/categoryService'
import { tagService } from '@/services/tagService'
import { unwrapList } from '@/utils/apiHelpers'

const route = useRoute()
const q = ref(typeof route.query.q === 'string' ? route.query.q : '')
const page = ref(1)
const sort = ref('relevance')

const filters = ref({
  category_id: null,
  year_from: null,
  year_to: null,
  language: '',
  tag_ids: [],
})

watch(
  () => route.query.q,
  (v) => {
    q.value = typeof v === 'string' ? v : ''
    page.value = 1
  },
)

const sortOptions = [
  { label: 'Liên quan nhất', value: 'relevance' },
  { label: 'Mới nhất', value: 'newest' },
  { label: 'Phổ biến', value: 'popular' },
  { label: 'Đánh giá', value: 'rating' },
]

const params = computed(() => ({
  q: q.value.trim(),
  page: page.value,
  sort: sort.value,
  category: filters.value.category_id || undefined,
  year_from: filters.value.year_from ?? undefined,
  year_to: filters.value.year_to ?? undefined,
  language: filters.value.language || undefined,
  tag: filters.value.tag_ids?.length ? filters.value.tag_ids[0] : undefined,
}))

watch(
  filters,
  () => {
    page.value = 1
  },
  { deep: true },
)

watch(sort, () => {
  page.value = 1
})

const { data: searchData, isFetching, isPending } = useQuery({
  queryKey: ['search', params],
  queryFn: () => searchService.search(params.value),
  placeholderData: keepPreviousData,
})

const results = computed(() =>
  unwrapList(searchData.value?.items ?? searchData.value?.data ?? searchData.value),
)
const didYouMean = computed(
  () => searchData.value?.did_you_mean || searchData.value?.didYouMean || '',
)
const totalResults = computed(() => {
  const m = searchData.value?.meta || searchData.value?.pagination || {}
  if (m.total != null) return Number(m.total)
  return results.value.length
})

const totalPages = computed(() => {
  const m = searchData.value?.meta || searchData.value?.pagination || {}
  const last = m.last_page || m.total_pages
  if (last) return Number(last)
  const total = m.total || results.value.length
  const perPage = m.per_page || 12
  return Math.max(1, Math.ceil(total / perPage))
})

const hasQuery = computed(() => q.value.trim().length > 0)

const { data: cats } = useQuery({
  queryKey: ['categories', 'flat'],
  queryFn: async () => {
    const t = await categoryService.tree()
    return unwrapList(t)
  },
})

const categoriesFlat = computed(() => {
  const out = []
  for (const c of cats.value || []) {
    out.push({ id: c.id, name: c.name })
    for (const ch of c.children || []) out.push({ id: ch.id, name: `— ${ch.name}` })
  }
  return out
})

const { data: tagsRaw } = useQuery({
  queryKey: ['tags'],
  queryFn: () => tagService.list({ per_page: 200 }),
})

const tags = computed(() => unwrapList(tagsRaw.value))

const { data: trending, isLoading: trendLoad } = useQuery({
  queryKey: ['search', 'trending'],
  queryFn: searchService.trending,
})

const trendingKeywords = computed(() => {
  const v = trending.value
  if (!v) return []
  if (Array.isArray(v.trending)) return v.trending
  return unwrapList(v)
})

</script>

<template>
  <div class="relative min-h-screen">
    <div
      class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[min(420px,52vh)] overflow-hidden"
      aria-hidden="true"
    >
      <div class="absolute -left-20 top-0 h-72 w-72 rounded-full bg-brand-400/20 blur-3xl motion-reduce:blur-none" />
      <div
        class="absolute right-0 top-16 h-64 w-64 rounded-full bg-indigo-500/15 blur-3xl motion-reduce:blur-none"
      />
      <div
        class="absolute inset-0 bg-gradient-to-b from-brand-50/95 via-surface to-surface motion-reduce:from-surface"
      />
    </div>

    <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-8 sm:px-6 lg:px-8 lg:pb-28 lg:pt-12">
      <header
        class="mx-auto mb-10 max-w-4xl text-center animate-fade-up motion-reduce:animate-none motion-reduce:opacity-100"
      >
        <p
          class="mb-2 inline-flex items-center gap-2 rounded-full border border-brand-200/80 bg-white/80 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-700 shadow-soft backdrop-blur-sm"
        >
          <Icon icon="mdi:library-outline" class="h-4 w-4" />
          Thư viện số
        </p>
        <h1 class="font-display text-3xl font-bold tracking-tight text-ink-900 sm:text-4xl">
          {{ hasQuery ? 'Kết quả tìm kiếm' : 'Khám phá tài liệu' }}
        </h1>
        <p class="mx-auto mt-3 max-w-2xl text-sm leading-relaxed text-ink-500 sm:text-base">
          {{
            hasQuery
              ? 'Xem và lọc theo danh mục, năm, ngôn ngữ và nhãn — có hiệu lực ngay khi bạn chọn.'
              : 'Duyệt toàn bộ kho hoặc nhập từ khóa để thu hẹp. Bộ lọc bên trái cập nhật kết quả tức thì.'
          }}
        </p>
      </header>

      <div class="mb-12 flex justify-center animate-fade-up delay-75 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0">
        <SearchBar />
      </div>

      <div class="grid gap-10 lg:grid-cols-[280px_1fr] lg:gap-12 xl:grid-cols-[300px_1fr]">
        <div
          class="animate-fade-up space-y-6 delay-100 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0 lg:sticky lg:top-24 lg:self-start"
        >
          <DocumentFilter v-model="filters" :categories="categoriesFlat" :tags="tags" />
          <TrendingKeywords :keywords="trendingKeywords" :loading="trendLoad" />
        </div>

        <div class="min-w-0 animate-fade-up delay-150 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0">
          <div
            class="mb-6 flex flex-col gap-4 rounded-2xl border border-ink-900/[0.06] bg-white/90 p-4 shadow-soft backdrop-blur-sm sm:flex-row sm:flex-wrap sm:items-center sm:justify-between sm:gap-6 sm:p-5"
          >
            <div class="space-y-1">
              <p class="text-sm text-ink-600">
                <template v-if="hasQuery">
                  Kết quả cho:
                  <strong class="font-semibold text-ink-900">{{ q }}</strong>
                </template>
                <template v-else> Hiển thị toàn bộ tài liệu phù hợp bộ lọc </template>
              </p>
              <p class="text-xs text-ink-400">
                {{ totalResults }} kết quả
                <span v-if="isFetching" class="ml-2 inline-flex items-center gap-1 text-brand-600">
                  <Icon icon="mdi:loading" class="h-3.5 w-3.5 animate-spin" />
                  Đang cập nhật…
                </span>
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <label class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-ink-400">
                <Icon icon="mdi:sort-variant" class="h-4 w-4 text-brand-600" />
                Sắp xếp
              </label>
              <Dropdown
                v-model="sort"
                :options="sortOptions"
                option-label="label"
                option-value="value"
                class="sort-dropdown w-full min-w-[13rem] sm:w-auto"
              />
            </div>
          </div>

          <SearchResults
            :results="results"
            :keyword="q"
            :did-you-mean="didYouMean"
            :loading="isPending"
          />

          <div class="mt-10">
            <Pagination
              :page="page"
              :total-pages="totalPages"
              :loading="isFetching"
              @update:page="page = $event"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* PrimeVue dropdown — bo góc đồng bộ theme */
:deep(.sort-dropdown.p-dropdown) {
  border-radius: 0.75rem;
  border-color: rgba(15, 23, 42, 0.08);
  box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
:deep(.sort-dropdown.p-dropdown:not(.p-disabled):hover) {
  border-color: rgba(37, 99, 235, 0.35);
}
:deep(.sort-dropdown .p-dropdown-label) {
  padding-block: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
}
</style>
