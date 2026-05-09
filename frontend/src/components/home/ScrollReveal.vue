<script setup>
import { ref, onMounted } from 'vue'
import { useIntersectionObserver } from '@vueuse/core'

defineProps({
  delay: { type: Number, default: 0 },
  y: { type: Number, default: 24 },
})

const target = ref(null)
const visible = ref(false)

onMounted(() => {
  const { stop } = useIntersectionObserver(
    target,
    ([entry]) => {
      if (entry?.isIntersecting) {
        visible.value = true
        stop()
      }
    },
    { threshold: 0.15 },
  )
})
</script>

<template>
  <div
    ref="target"
    :style="{ '--reveal-y': `${y}px`, '--reveal-delay': `${delay}ms` }"
    :class="['reveal', { 'reveal-in': visible }]"
  >
    <slot />
  </div>
</template>

<style scoped>
.reveal {
  opacity: 0;
  transform: translateY(var(--reveal-y));
  transition:
    opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1) var(--reveal-delay),
    transform 0.7s cubic-bezier(0.22, 1, 0.36, 1) var(--reveal-delay);
  will-change: opacity, transform;
}
.reveal-in {
  opacity: 1;
  transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
  .reveal {
    opacity: 1;
    transform: none;
    transition: none;
  }
}
</style>
