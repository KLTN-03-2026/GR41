<script setup>
import { Icon } from '@iconify/vue'
import { RouterLink } from 'vue-router'

defineProps({
  keywords: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})
</script>

<template>
  <div
    class="overflow-hidden rounded-2xl border border-ink-900/[0.07] bg-white/95 shadow-soft ring-1 ring-ink-900/[0.04] backdrop-blur-sm transition-shadow hover:shadow-md motion-reduce:transition-none"
  >
    <div
      class="flex items-center gap-3 border-b border-ink-900/[0.06] bg-gradient-to-r from-emerald-50/90 to-white px-5 py-4"
    >
      <span
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent-emerald text-white shadow-soft"
      >
        <Icon icon="mdi:trending-up" class="h-5 w-5" />
      </span>
      <div>
        <div class="font-display text-sm font-bold text-ink-900">Xu hướng tuần này</div>
        <p class="text-xs text-ink-500">Từ khóa được tìm nhiều</p>
      </div>
    </div>
    <div class="p-5">
      <ul v-if="loading" class="flex flex-wrap gap-2">
        <li v-for="n in 6" :key="n" class="h-8 w-24 animate-pulse rounded-full bg-surface-soft" />
      </ul>
      <ul v-else class="flex flex-wrap gap-2">
        <li v-for="(k, i) in keywords" :key="i" class="animate-fade-in motion-reduce:animate-none" :style="{ animationDelay: `${i * 45}ms` }">
          <RouterLink
            :to="{ name: 'search', query: { q: typeof k === 'string' ? k : k.keyword || k.name } }"
            class="inline-flex rounded-full border border-ink-900/[0.06] bg-surface-soft px-3 py-1.5 text-xs font-medium text-ink-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-800 active:scale-[0.98] motion-reduce:transition-none motion-reduce:active:scale-100"
          >
            {{ typeof k === 'string' ? k : k.keyword || k.name }}
          </RouterLink>
        </li>
      </ul>
    </div>
  </div>
</template>
