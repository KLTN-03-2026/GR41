<script setup>
import { formatDateTime } from '@/utils/formatters'

defineProps({
  item: { type: Object, required: true },
})

const emit = defineEmits(['select'])
</script>

<template>
  <button
    type="button"
    class="group flex w-full items-start gap-4 border-b border-slate-100/80 px-5 py-4 text-left transition-colors last:border-b-0"
    :class="item.is_read ? 'bg-white hover:bg-slate-50/60' : 'bg-brand-50/50 hover:bg-brand-50/80'"
    @click="emit('select', item)"
  >
    <!-- Icon -->
    <div
      class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
      :class="item.is_read ? 'bg-slate-100 text-slate-400' : 'bg-brand-100 text-brand-600'"
    >
      <i class="pi pi-bell text-sm" />
    </div>

    <!-- Body -->
    <div class="min-w-0 flex-1">
      <div class="flex items-start justify-between gap-2">
        <span
          class="text-sm leading-snug"
          :class="item.is_read ? 'font-medium text-slate-600' : 'font-semibold text-slate-900'"
        >
          {{ item.title || item.message }}
        </span>
        <span
          v-if="!item.is_read"
          class="mt-1 h-2 w-2 shrink-0 rounded-full bg-brand-500"
        />
      </div>
      <p v-if="item.content" class="mt-0.5 line-clamp-1 text-xs text-slate-500">
        {{ item.content }}
      </p>
      <span class="mt-1.5 block text-xs text-slate-400">{{ formatDateTime(item.created_at) }}</span>
    </div>

    <!-- Chevron -->
    <i class="pi pi-chevron-right mt-2 shrink-0 text-xs text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-slate-400" />
  </button>
</template>
