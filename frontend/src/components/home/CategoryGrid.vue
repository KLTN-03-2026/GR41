<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import { unwrapList } from '@/utils/apiHelpers'
import { getCategoryTheme } from '@/utils/categoryTheme'

const props = defineProps({
  categories: { type: Array, default: () => [] },
})

const list = computed(() =>
  unwrapList(props.categories).map((cat, i) => ({
    cat,
    theme: getCategoryTheme(cat, i),
  })),
)

function docCount(cat) {
  return cat?.documents_count ?? cat?.count ?? null
}

function subTags(cat) {
  const ch = cat?.children
  if (!Array.isArray(ch) || !ch.length) return []
  return ch.slice(0, 3)
}
</script>

<template>
  <section class="py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <h2 class="font-display mb-2 text-3xl font-bold tracking-tight text-ink-900 md:text-4xl">
        Danh mục
      </h2>
      <p class="mb-8 max-w-2xl text-ink-500">
        Khám phá theo lĩnh vực — mỗi danh mục có màu sắc riêng.
      </p>

      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <RouterLink
          v-for="{ cat, theme } in list"
          :key="cat.id"
          :to="{ name: 'category', params: { slug: cat.slug } }"
          class="cat-card group relative aspect-[4/3] overflow-hidden rounded-2xl p-6 shadow-soft ring-1 transition duration-300 ease-out hover:scale-[1.02] hover:shadow-lift motion-reduce:hover:scale-100"
          :class="[theme.skin.card, 'ring-ink-900/5']"
        >
          <div
            class="pointer-events-none absolute -bottom-8 -right-8 h-32 w-32 rounded-full opacity-40 blur-2xl transition duration-500 group-hover:scale-125 group-hover:opacity-70 motion-reduce:transition-none"
            :class="theme.skin.blob"
            aria-hidden="true"
          />
          <div
            class="relative flex h-14 w-14 items-center justify-center rounded-2xl text-[1.75rem] transition duration-300 group-hover:rotate-[8deg] motion-reduce:group-hover:rotate-0"
            :class="theme.skin.iconBox"
          >
            <Icon :icon="theme.icon" class="h-8 w-8" aria-hidden="true" />
          </div>
          <h3 class="relative mt-4 font-display text-lg font-bold text-ink-900">
            {{ cat.name }}
          </h3>
          <p class="relative mt-1 text-sm text-ink-500">
            <template v-if="docCount(cat) != null">{{ docCount(cat) }} tài liệu</template>
            <template v-else>Danh mục</template>
          </p>
          <div v-if="subTags(cat).length" class="relative mt-3 flex flex-wrap gap-1.5">
            <span
              v-for="ch in subTags(cat)"
              :key="ch.id"
              class="rounded-full px-2 py-0.5 text-xs font-medium"
              :class="theme.skin.chip"
            >
              {{ ch.name }}
            </span>
          </div>
          <span
            class="cat-arrow absolute bottom-5 right-5 flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-lg text-ink-700 opacity-0 shadow-sm ring-1 ring-ink-900/10 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100 motion-reduce:opacity-100"
            aria-hidden="true"
          >
            →
          </span>
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<style scoped>
.cat-arrow {
  transform: translateX(8px);
}
.group:hover .cat-arrow {
  transform: translateX(0);
}
@media (prefers-reduced-motion: reduce) {
  .cat-arrow {
    transform: none;
    opacity: 1;
  }
}
</style>
