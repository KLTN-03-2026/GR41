<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { Icon } from '@iconify/vue'
import DocumentCard from '@/components/document/DocumentCard.vue'
import EmptyState from '@/components/common/EmptyState.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import Button from 'primevue/button'
import { profileService } from '@/services/profileService'
import { useToast } from '@/composables/useToast'
import { unwrapList, unwrapMeta } from '@/utils/apiHelpers'

const toast = useToast()
const qc = useQueryClient()
const removingId = ref(null)

const { data: payload, isLoading } = useQuery({
  queryKey: ['profile', 'favorites'],
  queryFn: () => profileService.favorites({ per_page: 48 }),
})

const items = computed(() => unwrapList(payload.value))
const meta = computed(() => unwrapMeta(payload.value))
const totalCount = computed(() => Number(meta.value.total ?? items.value.length) || 0)

function documentId(entry) {
  const doc = entry.document || entry
  return entry.document_id ?? doc?.id ?? entry.id
}

const unfav = useMutation({
  mutationFn: (id) => profileService.removeFavorite(id),
  onMutate: (id) => {
    removingId.value = id
  },
  onSettled: () => {
    removingId.value = null
  },
  onSuccess: () => {
    toast.success('Đã bỏ yêu thích')
    qc.invalidateQueries({ queryKey: ['profile', 'favorites'] })
  },
  onError: (e) => toast.error(e?.message || 'Không thể bỏ yêu thích. Thử lại sau.'),
})

/** Trong template `unfav.isPending` không unwrap — dùng ref trực tiếp */
const unfavPending = unfav.isPending

function onRemove(doc) {
  const id = documentId(doc)
  if (id != null) unfav.mutate(id)
}
</script>

<template>
  <div class="relative min-h-[60vh] overflow-hidden bg-surface pb-16 pt-8 sm:pt-12">
    <div
      class="pointer-events-none absolute inset-0 opacity-[0.45]"
      aria-hidden="true"
    >
      <div class="absolute -left-1/4 top-0 h-[420px] w-[580px] rounded-full bg-brand-400/25 blur-[100px]" />
      <div class="absolute -right-1/4 top-24 h-[380px] w-[480px] rounded-full bg-indigo-400/20 blur-[90px]" />
      <div class="absolute bottom-0 left-1/3 h-64 w-[70%] rounded-full bg-accent-500/15 blur-[80px]" />
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <header
        class="animate-fade-up overflow-hidden rounded-2xl border border-white/60 bg-white/80 shadow-lift backdrop-blur-md"
      >
        <div
          class="relative bg-gradient-to-br from-brand-600/10 via-white to-indigo-50/80 px-6 py-8 sm:px-10 sm:py-10"
        >
          <div
            class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-brand-500/15 blur-2xl"
          />
          <div
            class="pointer-events-none absolute bottom-0 left-1/2 h-24 w-48 -translate-x-1/2 rounded-full bg-indigo-400/10 blur-2xl"
          />
          <div class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
              <p
                class="mb-2 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-brand-700/90"
              >
                <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-brand-500/15">
                  <Icon icon="mdi:heart-multiple" class="h-4 w-4 text-brand-600" />
                </span>
                Bộ sưu tập của bạn
              </p>
              <h1 class="font-display text-3xl font-bold tracking-tight text-ink-900 sm:text-4xl">
                Yêu thích
              </h1>
              <p class="mt-2 text-sm text-ink-500 sm:text-base">
                Tài liệu đã lưu để đọc lại bất cứ lúc nào. Gỡ khỏi danh sách chỉ với một chạm.
              </p>
            </div>
            <div
              v-if="!isLoading && totalCount > 0"
              class="flex shrink-0 items-center gap-3 rounded-xl border border-slate-200/80 bg-white/90 px-4 py-3 shadow-soft"
            >
              <div
                class="flex h-11 w-11 items-center justify-center rounded-lg bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md"
              >
                <Icon icon="mdi:bookmark-heart" class="h-6 w-6" />
              </div>
              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Tổng số</p>
                <p class="font-display text-2xl font-bold tabular-nums text-ink-900">
                  {{ totalCount }}
                  <span class="text-base font-semibold text-slate-400">tài liệu</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </header>

      <div class="mt-10">
        <LoadingSpinner v-if="isLoading" />

        <EmptyState
          v-else-if="items.length === 0"
          icon="mdi:heart-outline"
          title="Chưa có tài liệu yêu thích"
          description="Lưu tài liệu từ trang chi tiết để xem lại nhanh chóng tại đây."
        >
          <RouterLink to="/" class="inline-flex">
            <Button label="Khám phá tài liệu" icon="pi pi-compass" />
          </RouterLink>
        </EmptyState>

        <div
          v-else
          class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        >
          <div
            v-for="(d, i) in items"
            :key="documentId(d)"
            class="group/card relative animate-fade-up"
            :style="{ animationDelay: `${Math.min(i * 50, 800)}ms` }"
          >
            <div
              class="relative overflow-visible rounded-xl transition duration-300 ease-out will-change-transform [animation-fill-mode:backwards]"
            >
              <div class="favorite-card-ring rounded-xl transition duration-300 ease-out group-hover/card:-translate-y-1">
                <DocumentCard :doc="d.document || d" />
              </div>
              <button
                type="button"
                class="absolute right-2 top-2 z-20 flex h-10 w-10 items-center justify-center rounded-full border border-white/70 bg-white/95 text-rose-500 shadow-md backdrop-blur-sm transition duration-200 hover:scale-110 hover:bg-rose-50 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 focus-visible:ring-offset-2 active:scale-95 disabled:pointer-events-none disabled:opacity-60"
                aria-label="Bỏ yêu thích"
                :disabled="unfavPending && removingId === documentId(d)"
                @click.stop.prevent="onRemove(d)"
              >
                <Icon
                  v-if="unfavPending && removingId === documentId(d)"
                  icon="mdi:loading"
                  class="h-5 w-5 shrink-0 animate-spin"
                />
                <Icon v-else icon="mdi:heart" class="h-5 w-5 shrink-0" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.favorite-card-ring {
  border-radius: 0.75rem;
  transition:
    box-shadow 0.35s cubic-bezier(0.22, 1, 0.36, 1),
    transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
}

.group\/card:hover .favorite-card-ring {
  box-shadow:
    0 20px 40px -16px rgba(37, 99, 235, 0.22),
    0 0 0 1px rgba(37, 99, 235, 0.12);
}

@media (prefers-reduced-motion: reduce) {
  .favorite-card-ring {
    transition: none;
  }

  header,
  .animate-fade-up {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }
}
</style>
