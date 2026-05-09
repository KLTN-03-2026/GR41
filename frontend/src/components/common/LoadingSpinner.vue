<script setup>
defineProps({
  label: { type: String, default: 'Đang tải...' },
})
</script>

<template>
  <div class="flex flex-col items-center justify-center gap-3 py-12 text-slate-500">
    <div
      class="h-10 w-10 animate-spin rounded-full border-4 border-primary-blue border-t-transparent"
      aria-hidden="true"
    />
    <span class="text-sm">{{ label }}</span>
  </div>
</template>
