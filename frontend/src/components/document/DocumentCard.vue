<script setup>
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import { formatNumber } from '@/utils/formatters'

defineProps({
  doc: { type: Object, required: true },
  loading: { type: Boolean, default: false },
})

function langMeta(lang) {
  const map = {
    vi: { short: 'VI', cls: 'lang-vi' },
    en: { short: 'EN', cls: 'lang-en' },
  }
  return map[lang] ?? { short: String(lang || '?').toUpperCase(), cls: 'lang-default' }
}
</script>

<template>
  <!-- ── Skeleton ────────────────────────────────────── -->
  <div
    v-if="loading"
    class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-card"
    aria-hidden="true"
  >
    <div class="aspect-[3/4] shimmer" />
    <div class="space-y-3 p-4">
      <div class="h-4 w-4/5 rounded-lg shimmer" />
      <div class="h-3.5 w-2/5 rounded-lg shimmer" />
      <div class="flex gap-1.5 pt-1">
        <div class="h-5 w-14 rounded-full shimmer" />
        <div class="h-5 w-16 rounded-full shimmer" />
      </div>
      <div class="flex gap-4 border-t border-slate-100 pt-3">
        <div class="h-3 w-14 rounded shimmer" />
        <div class="h-3 w-14 rounded shimmer" />
      </div>
    </div>
  </div>

  <!-- ── Card ───────────────────────────────────────── -->
  <RouterLink
    v-else-if="doc"
    :to="{ name: 'document.detail', params: { slug: doc.slug } }"
    class="doc-card group block overflow-hidden rounded-2xl border border-slate-200/70 bg-white"
  >
    <!-- ── Image zone ── -->
    <div class="relative aspect-[3/4] overflow-hidden bg-slate-100">
      <img
        :src="doc.cover_image || 'https://placehold.co/300x400/e2e8f0/64748b?text=Tài+liệu'"
        :alt="doc.title"
        class="doc-card__img h-full w-full object-cover"
        loading="lazy"
      />

      <!-- Static gradient — bottom heavy -->
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-900/85 via-slate-900/15 to-transparent" />

      <!-- Hover tint -->
      <div class="doc-card__tint pointer-events-none absolute inset-0 bg-gradient-to-t from-blue-900/70 via-blue-800/25 to-transparent opacity-0" />

      <!-- ── Top-left: language + year ── -->
      <div class="absolute left-3 top-3 flex flex-wrap gap-1.5">
        <span :class="['meta-chip', langMeta(doc.language).cls]">
          <Icon icon="mdi:translate" class="h-3 w-3 flex-shrink-0" />
          {{ langMeta(doc.language).short }}
        </span>
        <span v-if="doc.published_year" class="meta-chip meta-chip-glass">
          <Icon icon="mdi:calendar-month-outline" class="h-3 w-3 flex-shrink-0" />
          {{ doc.published_year }}
        </span>
      </div>

      <!-- ── Top-right: featured ── -->
      <span v-if="doc.is_featured" class="absolute right-3 top-3 meta-chip meta-chip-star">
        <Icon icon="mdi:star-four-points" class="h-3.5 w-3.5 flex-shrink-0" />
        Nổi bật
      </span>

      <!-- ── Bottom overlay: category + rating ── -->
      <div class="absolute bottom-0 left-0 right-0 p-3">
        <div class="flex items-end justify-between gap-2">
          <span class="meta-chip meta-chip-glass max-w-[68%] truncate">
            <Icon icon="mdi:folder-open-outline" class="h-3 w-3 flex-shrink-0" />
            {{ doc.category?.name || 'Chưa phân loại' }}
          </span>
          <span
            v-if="doc.avg_rating != null || doc.average_rating != null"
            class="meta-chip meta-chip-rating flex-shrink-0"
          >
            <Icon icon="mdi:star" class="h-3.5 w-3.5 text-amber-300 flex-shrink-0" />
            {{ Number(doc.avg_rating ?? doc.average_rating).toFixed(1) }}
          </span>
        </div>

        <!-- CTA — slides up on hover -->
        <div class="doc-card__cta mt-2.5">
          <span class="flex w-full items-center justify-center gap-2 rounded-xl bg-white/15 py-2 text-[11px] font-semibold tracking-wide text-white backdrop-blur-sm ring-1 ring-white/20 transition-colors group-hover:bg-white/25">
            <Icon icon="mdi:book-open-variant" class="h-4 w-4" />
            Xem chi tiết
            <Icon icon="mdi:arrow-right" class="h-3.5 w-3.5" />
          </span>
        </div>
      </div>
    </div>

    <!-- ── Card body ── -->
    <div class="space-y-3 p-4">
      <div>
        <h3 class="doc-card__title line-clamp-2 text-[15px] font-bold leading-snug text-slate-900">
          {{ doc.title }}
        </h3>
        <p class="mt-1.5 flex items-center gap-1 truncate text-xs text-slate-500">
          <Icon icon="mdi:account-circle-outline" class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" />
          {{ doc.author || 'Ẩn danh' }}
        </p>
      </div>

      <!-- Tags -->
      <div v-if="doc.tags?.length" class="flex flex-wrap gap-1">
        <span
          v-for="(tag, i) in doc.tags.slice(0, 2)"
          :key="tag.id || `${doc.id}-t${i}`"
          class="tag-pill"
        >
          <Icon icon="mdi:pound" class="h-2.5 w-2.5" />{{ tag.name || tag }}
        </span>
      </div>

      <!-- Stats -->
      <div class="flex items-center gap-3 border-t border-slate-100 pt-2.5 text-[11px] text-slate-500">
        <span class="inline-flex items-center gap-1">
          <Icon icon="mdi:eye-outline" class="h-3.5 w-3.5 text-slate-400" />
          {{ formatNumber(doc.view_count) }}
        </span>
        <span class="inline-flex items-center gap-1">
          <Icon icon="mdi:download-outline" class="h-3.5 w-3.5 text-slate-400" />
          {{ formatNumber(doc.download_count) }}
        </span>
        <span v-if="doc.pages" class="inline-flex items-center gap-1">
          <Icon icon="mdi:book-open-page-variant-outline" class="h-3.5 w-3.5 text-slate-400" />
          {{ doc.pages }} trang
        </span>
      </div>
    </div>
  </RouterLink>
</template>

<style scoped>
/* ══════════════════════════════════════════════════════════
   CARD SHELL
══════════════════════════════════════════════════════════ */
.doc-card {
  box-shadow:
    0 1px 3px rgba(15, 23, 42, 0.06),
    0 4px 12px rgba(15, 23, 42, 0.05);
  transition:
    transform 0.38s cubic-bezier(0.34, 1.38, 0.64, 1),
    box-shadow 0.38s ease,
    border-color 0.25s ease;
  will-change: transform;
}

.doc-card:hover {
  transform: translateY(-7px);
  border-color: rgba(59, 130, 246, 0.32);
  box-shadow:
    0 0 0 1px rgba(59, 130, 246, 0.14),
    0 8px 20px rgba(37, 99, 235, 0.1),
    0 20px 44px rgba(37, 99, 235, 0.13);
}

/* ══════════════════════════════════════════════════════════
   IMAGE
══════════════════════════════════════════════════════════ */
.doc-card__img {
  transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  will-change: transform;
}
.doc-card:hover .doc-card__img {
  transform: scale(1.07);
}

/* ══════════════════════════════════════════════════════════
   HOVER OVERLAYS
══════════════════════════════════════════════════════════ */
.doc-card__tint {
  transition: opacity 0.38s ease;
}
.doc-card:hover .doc-card__tint {
  opacity: 1;
}

/* ══════════════════════════════════════════════════════════
   CTA BUTTON REVEAL
══════════════════════════════════════════════════════════ */
.doc-card__cta {
  transform: translateY(10px);
  opacity: 0;
  transition:
    opacity 0.3s ease,
    transform 0.3s cubic-bezier(0.34, 1.3, 0.64, 1);
}
.doc-card:hover .doc-card__cta {
  opacity: 1;
  transform: translateY(0);
}

/* ══════════════════════════════════════════════════════════
   TITLE
══════════════════════════════════════════════════════════ */
.doc-card__title {
  transition: color 0.2s ease;
}
.doc-card:hover .doc-card__title {
  color: #2563eb;
}

/* ══════════════════════════════════════════════════════════
   META CHIPS (badges on image)
══════════════════════════════════════════════════════════ */
.meta-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.22rem;
  border-radius: 999px;
  padding: 0.2rem 0.6rem;
  font-size: 0.6rem;
  font-weight: 700;
  line-height: 1.3;
  letter-spacing: 0.02em;
  white-space: nowrap;
}

/* Glass — year, category */
.meta-chip-glass {
  background: rgba(10, 18, 40, 0.52);
  color: #e2e8f0;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Language — Tiếng Việt */
.lang-vi {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.45);
}

/* Language — English */
.lang-en {
  background: linear-gradient(135deg, #059669, #047857);
  color: #fff;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.45);
}

/* Language — fallback */
.lang-default {
  background: rgba(10, 18, 40, 0.52);
  color: #e2e8f0;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.18);
}

/* Featured star */
.meta-chip-star {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #fff;
  box-shadow: 0 2px 10px rgba(245, 158, 11, 0.5);
  animation: starGlow 3s ease-in-out infinite;
}

/* Rating */
.meta-chip-rating {
  background: rgba(10, 18, 40, 0.52);
  color: #fde68a;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(245, 158, 11, 0.28);
  font-weight: 800;
}

/* ══════════════════════════════════════════════════════════
   TAG PILLS (card body)
══════════════════════════════════════════════════════════ */
.tag-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.15rem;
  border-radius: 999px;
  padding: 0.18rem 0.55rem;
  font-size: 0.67rem;
  font-weight: 600;
  background: rgb(239 246 255);
  color: #2563eb;
  border: 1px solid rgb(219 234 254);
  transition: background 0.2s, color 0.2s;
}
.doc-card:hover .tag-pill {
  background: rgb(219 234 254);
  color: #1d4ed8;
}

/* ══════════════════════════════════════════════════════════
   SKELETON SHIMMER
══════════════════════════════════════════════════════════ */
.shimmer {
  background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
  background-size: 200% 100%;
  animation: shimmerSlide 1.6s ease-in-out infinite;
}

.shadow-card {
  box-shadow:
    0 1px 3px rgba(15, 23, 42, 0.06),
    0 4px 12px rgba(15, 23, 42, 0.05);
}

/* ══════════════════════════════════════════════════════════
   ANIMATIONS
══════════════════════════════════════════════════════════ */
@keyframes shimmerSlide {
  from { background-position: 200% center; }
  to   { background-position: -200% center; }
}

@keyframes starGlow {
  0%, 100% { box-shadow: 0 2px 10px rgba(245, 158, 11, 0.5); }
  50%       { box-shadow: 0 2px 18px rgba(245, 158, 11, 0.8), 0 0 0 4px rgba(245, 158, 11, 0.12); }
}
</style>
