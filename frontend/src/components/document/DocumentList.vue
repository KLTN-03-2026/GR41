<script setup>
import { computed } from 'vue'
import DocumentCard from '@/components/document/DocumentCard.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { unwrapList } from '@/utils/apiHelpers'

const props = defineProps({
  title: { type: String, required: true },
  items: { type: [Array, Object], default: null },
  loading: { type: Boolean, default: false },
})

const rows = computed(() => unwrapList(props.items))
</script>

<template>
  <section class="py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <h2 class="mb-6 text-xl font-bold text-slate-900 sm:text-2xl">{{ title }}</h2>
      <LoadingSpinner v-if="loading && rows.length === 0" />
      <div v-else-if="loading" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <DocumentCard v-for="n in 8" :key="'sk-' + n" loading />
      </div>
      <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <DocumentCard v-for="doc in rows" :key="doc.id || doc.slug" :doc="doc" />
      </div>
    </div>
  </section>
</template>
