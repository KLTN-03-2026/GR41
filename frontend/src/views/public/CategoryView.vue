<script setup>
import { ref, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import { keepPreviousData, useQuery } from '@tanstack/vue-query'
import DocumentCard from '@/components/document/DocumentCard.vue'
import DocumentFilter from '@/components/document/DocumentFilter.vue'
import Pagination from '@/components/common/Pagination.vue'
import Dropdown from 'primevue/dropdown'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { categoryService } from '@/services/categoryService'
import { documentService } from '@/services/documentService'
import { tagService } from '@/services/tagService'
import { unwrapList } from '@/utils/apiHelpers'

const route = useRoute()
const slug = computed(() => route.params.slug)
const page = ref(1)
const sort = ref('newest')

const filters = ref({
  category_id: null,
  year_from: null,
  year_to: null,
  language: '',
  tag_ids: [],
})

watch(slug, () => {
  page.value = 1
})

const sortOptions = [
  { label: 'Mới nhất', value: 'newest' },
  { label: 'Phổ biến', value: 'popular' },
  { label: 'Đánh giá', value: 'rating' },
]

const params = computed(() => ({
  category_slug: slug.value,
  page: page.value,
  sort: sort.value,
  ...filters.value,
}))

const { data: listPayload, isFetching } = useQuery({
  queryKey: ['documents', 'category', params],
  queryFn: () => documentService.list(params.value),
  placeholderData: keepPreviousData,
  enabled: computed(() => !!slug.value),
})

const rows = computed(() => unwrapList(listPayload.value))
const totalPages = computed(() => {
  const m = listPayload.value?.meta || {}
  const last = m.last_page
  if (last) return Number(last)
  return 1
})

const { data: cat } = useQuery({
  queryKey: ['category', slug],
  queryFn: () => categoryService.detail(slug.value),
  enabled: computed(() => !!slug.value),
})

const { data: cats } = useQuery({
  queryKey: ['categories', 'flat'],
  queryFn: categoryService.tree,
})

const categoriesFlat = computed(() => {
  const out = []
  for (const c of unwrapList(cats.value)) {
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
</script>

<template>
  <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-slate-900">{{ cat?.name || cat?.category?.name || 'Danh mục' }}</h1>
      <p class="mt-2 text-slate-600">{{ cat?.description }}</p>
    </div>

    <div class="grid gap-8 lg:grid-cols-[260px_1fr]">
      <DocumentFilter v-model="filters" :categories="categoriesFlat" :tags="tags" />
      <div>
        <div class="mb-4 flex justify-end">
          <Dropdown v-model="sort" :options="sortOptions" option-label="label" option-value="value" class="w-52" />
        </div>
        <LoadingSpinner v-if="isFetching && !rows.length" />
        <EmptyState v-else-if="!isFetching && rows.length === 0" title="Chưa có tài liệu trong danh mục" />
        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <DocumentCard v-for="d in rows" :key="d.id" :doc="d" />
        </div>
        <Pagination :page="page" :total-pages="totalPages" :loading="isFetching" @update:page="page = $event" />
      </div>
    </div>
  </div>
</template>
