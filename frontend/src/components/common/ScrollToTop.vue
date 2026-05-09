<script setup>
import { computed } from 'vue'
import { useWindowScroll } from '@vueuse/core'
import { Icon } from '@iconify/vue'

const { y } = useWindowScroll()
const show = computed(() => y.value > 600)

function scrollUp() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <Transition name="fade-up">
    <button
      v-if="show"
      type="button"
      class="fixed bottom-[7.5rem] right-4 z-[55] flex h-12 w-12 items-center justify-center rounded-full border border-ink-900/10 bg-white text-ink-700 shadow-lift backdrop-blur-md transition hover:bg-surface-soft focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 active:scale-95 motion-reduce:active:scale-100 md:right-6"
      aria-label="Lên đầu trang"
      @click="scrollUp"
    >
      <Icon icon="mdi:chevron-up" class="h-7 w-7" aria-hidden="true" />
    </button>
  </Transition>
</template>

<style scoped>
.fade-up-enter-active,
.fade-up-leave-active {
  transition:
    opacity 0.35s ease,
    transform 0.35s ease;
}
.fade-up-enter-from,
.fade-up-leave-to {
  opacity: 0;
  transform: translateY(12px);
}
@media (prefers-reduced-motion: reduce) {
  .fade-up-enter-active,
  .fade-up-leave-active {
    transition: none;
  }
}
</style>
