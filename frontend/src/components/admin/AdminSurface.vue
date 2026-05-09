<script setup>
defineProps({
  padded: { type: Boolean, default: false },
})
</script>

<template>
  <div
    :class="[
      'overflow-hidden rounded-2xl border border-slate-200/70 bg-white/95 shadow-soft backdrop-blur-sm motion-safe:animate-fade-up [animation-delay:90ms] motion-reduce:animate-none',
      padded ? 'p-4 md:p-5' : '',
    ]"
  >
    <slot />
  </div>
</template>
