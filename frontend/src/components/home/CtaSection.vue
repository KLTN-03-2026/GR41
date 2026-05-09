<script setup>
import { RouterLink } from 'vue-router'

function openAi() {
  window.dispatchEvent(new CustomEvent('tri-thuc-open-chat'))
}
</script>

<template>
  <section class="relative isolate overflow-hidden py-16 md:py-20">
    <div
      class="absolute inset-0 bg-hero-mesh bg-[length:200%_200%] animate-gradient-pan motion-reduce:animate-none opacity-[0.97]"
      aria-hidden="true"
    />
    <div
      class="absolute inset-0 bg-gradient-to-b from-ink-900/50 via-ink-900/65 to-ink-900/85"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute right-[8%] top-10 text-amber-300/30 motion-reduce:animate-none animate-float"
      aria-hidden="true"
    >
      <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
        <path
          d="M12 2l2.4 7.4h7.6l-6 4.6 2.3 7L12 17.8 5.7 21l2.3-7-6-4.6h7.6L12 2z"
        />
      </svg>
    </div>

    <div
      class="relative z-10 mx-auto max-w-4xl px-4 text-center sm:px-6"
    >
      <h2 class="font-display text-3xl font-bold tracking-tight text-white md:text-4xl">
        Sẵn sàng khám phá kho tri thức?
      </h2>
      <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-white/85">
        Đăng ký miễn phí để nhận gợi ý tài liệu cá nhân hóa và trò chuyện cùng trợ lý AI.
      </p>
      <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
        <RouterLink
          to="/register"
          class="inline-flex rounded-full bg-white px-8 py-3.5 text-sm font-semibold text-brand-700 shadow-lift transition hover:bg-brand-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white active:scale-[0.97] motion-reduce:active:scale-100"
        >
          Đăng ký miễn phí
        </RouterLink>
        <button
          type="button"
          class="inline-flex rounded-full border-2 border-white/90 bg-transparent px-8 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white active:scale-[0.97] motion-reduce:active:scale-100"
          @click="openAi"
        >
          Tìm hiểu Trợ lý AI
        </button>
      </div>
    </div>
  </section>
</template>
