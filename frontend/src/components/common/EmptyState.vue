<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  title: { type: String, default: 'Không có dữ liệu' },
  description: { type: String, default: '' },
  icon: { type: String, default: 'mdi:inbox-outline' },
})
</script>

<template>
  <div
    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center"
  >
    <div class="mb-4 rounded-full bg-slate-100 p-6">
      <Icon :icon="icon" class="h-14 w-14 text-slate-400" />
    </div>
    <h3 class="text-lg font-semibold text-slate-800">{{ title }}</h3>
    <p v-if="description" class="mt-2 max-w-md text-sm text-slate-500">{{ description }}</p>
    <div class="mt-6">
      <slot />
    </div>
  </div>
</template>
