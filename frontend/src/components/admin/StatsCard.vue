<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'
import { formatNumber } from '@/utils/formatters'

const props = defineProps({
  label: { type: String, required: true },
  value: { type: [Number, String], default: 0 },
  icon: { type: String, default: 'mdi:chart-box-outline' },
  accent: { type: String, default: 'from-blue-500 to-blue-600' },
  /** 0–3: stagger animation */
  staggerIndex: { type: Number, default: 0 },
})

const display = computed(() =>
  typeof props.value === 'number' ? formatNumber(props.value) : props.value,
)

const staggerClass = computed(() => {
  const i = Math.min(Math.max(props.staggerIndex, 0), 3)
  const delays = [
    '',
    'motion-safe:[animation-delay:70ms]',
    'motion-safe:[animation-delay:140ms]',
    'motion-safe:[animation-delay:210ms]',
  ]
  return delays[i]
})
</script>

<template>
  <div
    :class="[
      'motion-safe:animate-fade-up motion-reduce:animate-none',
      staggerClass,
      'group relative overflow-hidden rounded-2xl border border-slate-200/80 bg-white/95 p-6 shadow-soft backdrop-blur-sm transition-all duration-300',
      'hover:-translate-y-0.5 hover:border-slate-200 hover:shadow-lift motion-reduce:hover:translate-y-0',
    ]"
  >
    <div
      class="pointer-events-none absolute -right-8 -top-12 h-32 w-32 rounded-full bg-gradient-to-br opacity-[0.18] motion-safe:transition-transform motion-safe:duration-700 group-hover:scale-110 group-hover:opacity-[0.28]"
      :class="accent"
    />
    <div class="relative flex items-start justify-between gap-4">
      <div class="min-w-0 flex-1">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ label }}</p>
        <p class="mt-2 font-display text-3xl font-bold tracking-tight text-slate-900">{{ display }}</p>
      </div>
      <div
        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-inner ring-1 ring-white/10 motion-safe:transition-transform motion-safe:duration-300 group-hover:scale-105"
        :class="accent"
      >
        <Icon :icon="icon" class="h-7 w-7" />
      </div>
    </div>
  </div>
</template>
