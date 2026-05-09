<script setup>
defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
})
</script>

<template>
  <header class="motion-safe:animate-fade-up">
    <div
      class="relative overflow-hidden rounded-2xl border border-slate-200/80 bg-gradient-to-br from-white via-slate-50/90 to-brand-50/35 p-5 shadow-soft md:flex md:items-center md:justify-between md:p-6"
    >
      <div
        class="pointer-events-none absolute -right-10 -top-14 h-40 w-40 rounded-full bg-gradient-to-br from-brand-400/30 via-indigo-400/15 to-purple-400/20 blur-3xl motion-safe:animate-pulse-soft"
        aria-hidden="true"
      />
      <div class="pointer-events-none absolute -bottom-8 left-1/4 h-24 w-48 rounded-full bg-emerald-400/10 blur-2xl" aria-hidden="true" />
      <div class="relative min-w-0 flex-1">
        <div class="mb-1 flex items-center gap-2">
          <span
            class="inline-flex h-2 w-2 rounded-full bg-gradient-to-br from-brand-500 to-indigo-600 shadow-sm ring-2 ring-white"
            aria-hidden="true"
          />
          <span class="font-display text-[0.65rem] font-semibold uppercase tracking-[0.22em] text-slate-500">Quản trị</span>
        </div>
        <h1 class="font-display text-2xl font-bold tracking-tight text-slate-900 md:text-[1.7rem]">
          {{ title }}
        </h1>
        <p v-if="subtitle" class="mt-1.5 max-w-2xl text-sm leading-relaxed text-slate-600">
          {{ subtitle }}
        </p>
      </div>
      <div v-if="$slots.actions" class="relative mt-5 flex shrink-0 flex-wrap items-center gap-3 md:mt-0 md:pl-4">
        <slot name="actions" />
      </div>
    </div>
  </header>
</template>
