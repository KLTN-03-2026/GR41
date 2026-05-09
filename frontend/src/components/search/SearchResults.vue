<script setup>
import DocumentCard from '@/components/document/DocumentCard.vue'
import HighlightText from '@/components/common/HighlightText.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import { unwrapList } from '@/utils/apiHelpers'

const props = defineProps({
  results: { type: [Array, Object], default: () => [] },
  keyword: { type: String, default: '' },
  didYouMean: { type: String, default: '' },
  loading: { type: Boolean, default: false },
})

const rows = () => unwrapList(props.results)
</script>

<template>
  <div>
    <!-- Did-you-mean -->
    <p
      v-if="didYouMean"
      class="mb-5 flex items-center gap-2 rounded-xl border border-amber-200/70 bg-amber-50 px-4 py-3 text-sm text-amber-900"
    >
      <span class="text-base">💡</span>
      Bạn có muốn tìm:
      <strong class="font-semibold">{{ didYouMean }}</strong>?
    </p>

    <!-- Loading grid -->
    <div v-if="loading" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <DocumentCard v-for="n in 6" :key="n" loading />
    </div>

    <!-- Empty -->
    <EmptyState
      v-else-if="rows().length === 0"
      title="Không tìm thấy kết quả"
      description="Thử từ khóa khác hoặc điều chỉnh bộ lọc."
    />

    <!-- Results -->
    <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="(doc, index) in rows()"
        :key="doc.id"
        class="result-item"
        :style="{ '--delay': `${index * 50}ms` }"
      >
        <DocumentCard :doc="doc" />

        <!-- Description snippet below card -->
        <p
          v-if="doc.description"
          class="mt-2 line-clamp-2 px-1 text-[11px] leading-relaxed text-slate-500"
        >
          <HighlightText :text="doc.description" :keyword="keyword" />
        </p>
      </article>
    </div>
  </div>
</template>

<style scoped>
.result-item {
  animation: cardIn 480ms cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: var(--delay, 0ms);
}

@keyframes cardIn {
  from {
    opacity: 0;
    transform: translateY(16px) scale(0.97);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
