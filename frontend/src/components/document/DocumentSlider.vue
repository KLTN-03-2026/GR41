<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import { unwrapList } from '@/utils/apiHelpers'

const props = defineProps({
  items: { type: [Array, Object], default: () => [] },
  loading: { type: Boolean, default: false },
})

const slides = computed(() => unwrapList(props.items).slice(0, 8))
</script>

<template>
  <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-blue to-blue-900 text-white shadow-xl">
    <div class="absolute inset-0 opacity-20 pattern-dots" />
    <div class="relative px-4 py-10 sm:px-8 lg:px-12">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <p class="text-sm font-medium uppercase tracking-wide text-blue-200">Nổi bật</p>
          <h2 class="mt-1 text-2xl font-bold sm:text-3xl">Tài liệu được đề xuất</h2>
        </div>
        <RouterLink to="/search" class="hidden items-center gap-1 text-sm text-blue-100 hover:text-white sm:flex">
          Xem thêm
          <Icon icon="mdi:arrow-right" class="h-4 w-4" />
        </RouterLink>
      </div>

      <div v-if="loading" class="grid gap-4 md:grid-cols-3">
        <div v-for="n in 3" :key="n" class="h-40 animate-pulse rounded-xl bg-white/10" />
      </div>

      <div v-else class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <RouterLink
          v-for="doc in slides"
          :key="doc.id"
          :to="{ name: 'document.detail', params: { slug: doc.slug } }"
          class="min-w-[260px] max-w-xs shrink-0 snap-start overflow-hidden rounded-xl bg-white/10 backdrop-blur transition hover:bg-white/20"
        >
          <div class="aspect-video overflow-hidden">
            <img
              :src="doc.cover_image || 'https://placehold.co/640x360/e2e8f0/64748b?'"
              class="h-full w-full object-cover"
              alt=""
            />
          </div>
          <div class="p-4">
            <p class="line-clamp-2 font-semibold">{{ doc.title }}</p>
            <p class="mt-1 line-clamp-1 text-sm text-blue-100">{{ doc.author }}</p>
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>
