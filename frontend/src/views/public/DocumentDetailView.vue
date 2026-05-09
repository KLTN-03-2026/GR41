<script setup>
import { computed, unref } from 'vue'
import { useRoute } from 'vue-router'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { Icon } from '@iconify/vue'
import Button from 'primevue/button'
import DocumentRating from '@/components/document/DocumentRating.vue'
import DocumentReviewList from '@/components/document/DocumentReviewList.vue'
import DocumentCard from '@/components/document/DocumentCard.vue'
import { documentService } from '@/services/documentService'
import { useAuthStore } from '@/stores/auth'
import { formatNumber } from '@/utils/formatters'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'

const route = useRoute()
const slug = computed(() => route.params.slug)
const auth = useAuthStore()
const toast = useToast()
const queryClient = useQueryClient()

const { data: raw, isLoading } = useQuery({
  queryKey: ['document', slug],
  queryFn: () => documentService.detail(slug.value),
})

const doc = computed(() => {
  const r = raw.value
  if (!r) return null
  return r.document || r.data || r
})

const docId = computed(() => doc.value?.id)

const isFavorited = computed(() => !!doc.value?.is_favorited)

const favoriteBtnClass = computed(() =>
  isFavorited.value
    ? '!border-rose-300 !bg-gradient-to-br !from-rose-50 !to-rose-100/90 !text-rose-800 !font-bold !shadow-sm !ring-1 !ring-rose-200/70 hover:!from-rose-100 hover:!to-rose-100'
    : '',
)

const { data: related } = useQuery({
  queryKey: ['document', 'related', docId],
  queryFn: () => documentService.related(docId.value),
  enabled: computed(() => !!docId.value),
})

function patchDocumentFavorite(next) {
  queryClient.setQueryData(['document', unref(slug)], (old) => {
    if (!old) return old
    const apply = (node) =>
      node && typeof node === 'object' ? { ...node, is_favorited: next } : node
    if (old.document != null) return { ...old, document: apply(old.document) }
    if (old.data != null) return { ...old, data: apply(old.data) }
    return apply(old)
  })
}

const favMutation = useMutation({
  mutationFn: () => documentService.toggleFavorite(docId.value),
  onSuccess: (payload) => {
    const fav = payload?.is_favorited
    if (typeof fav === 'boolean') patchDocumentFavorite(fav)
    toast.success(fav ? 'Đã thêm vào yêu thích' : 'Đã bỏ khỏi yêu thích')
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
})
/** Ref riêng — trong template `favMutation.isPending` không unwrap (object thường) → luôn truthy */
const favPending = favMutation.isPending

const dlMutation = useMutation({
  mutationFn: () => documentService.download(docId.value),
  onSuccess: (payload) => {
    const url = typeof payload === 'string' ? payload : payload?.file_url || payload?.url
    if (url) window.open(url, '_blank')
    else toast.error('Không có URL tải')
  },
  onError: (e) => toast.error(e?.message || 'Không tải được'),
})
const dlPending = dlMutation.isPending

const rateMutation = useMutation({
  mutationFn: (body) => documentService.rate(docId.value, body),
  onSuccess: () => {
    toast.success('Đã gửi đánh giá')
    queryClient.invalidateQueries({ queryKey: ['document', slug] })
  },
  onError: (e) => toast.error(e?.message || 'Không gửi được đánh giá'),
})

function onDownload() {
  if (!auth.isLoggedIn) {
    toast.info('Đăng nhập để tải xuống')
    return
  }
  dlMutation.mutate()
}

function onFavorite() {
  if (!auth.isLoggedIn) {
    toast.info('Đăng nhập để lưu yêu thích')
    return
  }
  favMutation.mutate()
}

function onRate(payload) {
  rateMutation.mutate({ score: payload.score, comment: payload.comment })
}

const reviews = computed(() => unwrapList(doc.value?.reviews || raw.value?.reviews))

const relatedList = computed(() => unwrapList(related.value))

const metaRows = computed(() => [
  { key: 'author', label: 'Tác giả', icon: 'mdi:account-outline', value: doc.value?.author },
  { key: 'publisher', label: 'NXB', icon: 'mdi:domain', value: doc.value?.publisher },
  { key: 'year', label: 'Năm', icon: 'mdi:calendar-outline', value: doc.value?.published_year },
  { key: 'pages', label: 'Trang', icon: 'mdi:book-open-page-variant-outline', value: doc.value?.pages },
  { key: 'language', label: 'Ngôn ngữ', icon: 'mdi:translate', value: doc.value?.language },
  { key: 'isbn', label: 'ISBN', icon: 'mdi:identifier', value: doc.value?.isbn },
])
</script>

<template>
  <div class="relative min-h-screen">
    <!-- Nền trang trí -->
    <div
      class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[min(480px,55vh)] overflow-hidden"
      aria-hidden="true"
    >
      <div
        class="absolute -left-16 top-0 h-80 w-80 rounded-full bg-brand-400/25 blur-3xl motion-reduce:blur-none"
      />
      <div
        class="absolute right-0 top-20 h-72 w-72 rounded-full bg-indigo-500/15 blur-3xl motion-reduce:blur-none"
      />
      <div
        class="absolute inset-0 bg-gradient-to-b from-brand-50/90 via-surface/95 to-surface motion-reduce:from-surface"
      />
    </div>

    <div class="relative mx-auto max-w-7xl px-4 pb-16 pt-8 sm:px-6 lg:px-8 lg:pb-24 lg:pt-10">
      <!-- Skeleton -->
      <div v-if="isLoading" class="grid gap-10 lg:grid-cols-[minmax(0,320px)_1fr]">
        <div class="space-y-4 rounded-3xl border border-ink-900/5 bg-white/80 p-4 shadow-soft backdrop-blur-sm">
          <div class="aspect-[3/4] animate-pulse rounded-2xl bg-gradient-to-br from-slate-200 to-slate-100" />
          <div class="h-11 animate-pulse rounded-xl bg-slate-200/90" />
          <div class="h-11 animate-pulse rounded-xl bg-slate-100" />
        </div>
        <div class="space-y-6 pt-2">
          <div class="h-10 max-w-xl animate-pulse rounded-lg bg-slate-200" />
          <div class="grid gap-3 sm:grid-cols-2">
            <div v-for="n in 6" :key="n" class="h-14 animate-pulse rounded-xl bg-slate-100" />
          </div>
          <div class="h-24 animate-pulse rounded-xl bg-slate-50" />
        </div>
      </div>

      <template v-else-if="doc">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,300px)_1fr] lg:gap-14">
          <!-- Cột ảnh & CTA -->
          <aside
            class="animate-fade-up motion-reduce:animate-none motion-reduce:opacity-100 lg:sticky lg:top-24 lg:self-start"
          >
            <div
              class="group overflow-hidden rounded-3xl border border-ink-900/[0.06] bg-white shadow-lift ring-1 ring-ink-900/[0.04]"
            >
              <div class="relative overflow-hidden bg-surface-sunken">
                <img
                  :src="doc.cover_image || 'https://placehold.co/600x800/e2e8f0/64748b?text=Cover'"
                  class="aspect-[3/4] w-full object-cover transition duration-700 ease-out motion-safe:group-hover:scale-[1.03]"
                  alt=""
                />
                <div
                  class="pointer-events-none absolute inset-0 bg-gradient-to-t from-ink-900/20 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                />
              </div>
              <div class="space-y-3 p-5">
                <Button
                  label="Tải xuống"
                  icon="pi pi-download"
                  class="w-full !rounded-xl !py-2.5 !font-semibold !shadow-soft !transition active:scale-[0.98] motion-reduce:active:scale-100"
                  :loading="dlPending"
                  :disabled="dlPending"
                  @click="onDownload"
                />
                <Button
                  type="button"
                  :label="isFavorited ? 'Đã yêu thích' : 'Yêu thích'"
                  :icon="isFavorited ? 'pi pi-heart-fill' : 'pi pi-heart'"
                  :outlined="!isFavorited"
                  class="w-full !rounded-xl !py-2.5 !transition-all !duration-300 active:scale-[0.98] motion-reduce:active:scale-100"
                  :class="favoriteBtnClass"
                  :loading="favPending"
                  :disabled="favPending"
                  @click="onFavorite"
                />
              </div>
            </div>
          </aside>

          <!-- Nội dung -->
          <article class="min-w-0 space-y-8">
            <header
              class="animate-fade-up delay-75 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0"
            >
              <h1
                class="font-display text-3xl font-bold tracking-tight text-ink-900 sm:text-4xl lg:text-[2.35rem] lg:leading-[1.15]"
              >
                {{ doc.title }}
              </h1>
            </header>

            <!-- Thống kê nhanh -->
            <div
              class="flex animate-fade-up flex-wrap gap-3 delay-100 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0"
            >
              <span
                class="inline-flex items-center gap-2 rounded-full border border-ink-900/[0.06] bg-white/90 px-4 py-2 text-sm font-medium text-ink-700 shadow-soft backdrop-blur-sm"
              >
                <Icon icon="mdi:eye-outline" class="h-[1.1rem] w-[1.1rem] text-brand-600" />
                {{ formatNumber(doc.view_count) }}
                <span class="font-normal text-ink-500">lượt xem</span>
              </span>
              <span
                class="inline-flex items-center gap-2 rounded-full border border-ink-900/[0.06] bg-white/90 px-4 py-2 text-sm font-medium text-ink-700 shadow-soft backdrop-blur-sm"
              >
                <Icon icon="mdi:download-outline" class="h-[1.1rem] w-[1.1rem] text-brand-600" />
                {{ formatNumber(doc.download_count) }}
                <span class="font-normal text-ink-500">lượt tải</span>
              </span>
              <span
                v-if="doc.avg_rating != null || doc.average_rating != null"
                class="inline-flex items-center gap-2 rounded-full border border-amber-200/80 bg-gradient-to-r from-amber-50 to-orange-50/90 px-4 py-2 text-sm font-semibold text-amber-800 shadow-soft"
              >
                <Icon icon="mdi:star" class="h-[1.1rem] w-[1.1rem] text-amber-500" />
                {{ Number(doc.avg_rating ?? doc.average_rating).toFixed(1) }}
                <span class="font-normal text-amber-700/90">/5</span>
              </span>
            </div>

            <!-- Meta lưới -->
            <div
              class="grid animate-fade-up gap-3 delay-100 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0 sm:grid-cols-2"
            >
              <div
                v-for="row in metaRows"
                :key="row.key"
                class="flex items-start gap-3 rounded-2xl border border-ink-900/[0.06] bg-white/85 px-4 py-3 shadow-soft backdrop-blur-sm transition hover:border-brand-200/60 hover:shadow-md motion-reduce:transition-none"
              >
                <span
                  class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"
                >
                  <Icon :icon="row.icon" class="h-5 w-5" />
                </span>
                <div class="min-w-0">
                  <p class="text-xs font-medium uppercase tracking-wide text-ink-400">{{ row.label }}</p>
                  <p class="truncate font-medium text-ink-900">{{ row.value ?? '—' }}</p>
                </div>
              </div>
            </div>

            <!-- Tags -->
            <div
              class="animate-fade-up flex flex-wrap gap-2 delay-150 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0"
            >
              <span
                v-if="doc.category?.name"
                class="inline-flex items-center rounded-full bg-brand-600/10 px-4 py-1.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-500/20 transition hover:bg-brand-600/15"
              >
                {{ doc.category.name }}
              </span>
              <span
                v-for="t in doc.tags || []"
                :key="t.id || t"
                class="inline-flex items-center rounded-full bg-surface-soft px-4 py-1.5 text-xs font-medium text-ink-700 ring-1 ring-ink-900/5 transition hover:bg-white hover:ring-brand-200/50"
              >
                {{ t.name || t }}
              </span>
            </div>

            <!-- Mô tả -->
            <div
              class="animate-fade-up delay-200 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0"
            >
              <div
                class="rounded-2xl border border-ink-900/[0.06] bg-white/90 p-6 shadow-soft ring-1 ring-ink-900/[0.03] backdrop-blur-sm"
              >
                <div class="mb-4 flex items-center gap-3 border-l-4 border-brand-500 pl-4">
                  <h3 class="font-display text-lg font-bold text-ink-900">Mô tả</h3>
                </div>
                <p class="whitespace-pre-wrap leading-relaxed text-ink-700">
                  {{ doc.description || 'Đang cập nhật...' }}
                </p>
              </div>
            </div>

            <div
              class="animate-fade-up space-y-8 delay-200 motion-reduce:animate-none motion-reduce:opacity-100 motion-reduce:delay-0"
            >
              <DocumentRating
                :document-id="doc.id"
                :disabled="!auth.isLoggedIn"
                @submit="onRate"
              />
              <DocumentReviewList :reviews="reviews" :loading="false" />
            </div>
          </article>
        </div>

        <!-- Liên quan -->
        <section
          class="mt-20 border-t border-ink-900/10 pt-12 lg:mt-24 lg:pt-16"
        >
          <div class="mb-10 animate-fade-up motion-reduce:animate-none motion-reduce:opacity-100">
            <h2
              class="font-display relative inline-block text-2xl font-bold tracking-tight text-ink-900 after:absolute after:-bottom-1 after:left-0 after:h-1 after:w-12 after:rounded-full after:bg-gradient-to-r after:from-brand-500 after:to-indigo-500 sm:text-3xl"
            >
              Tài liệu liên quan
            </h2>
            <p class="mt-2 max-w-xl text-sm text-ink-500">
              Gợi ý thêm dựa trên chủ đề và danh mục của tài liệu.
            </p>
          </div>
          <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div
              v-for="(d, i) in relatedList"
              :key="d.id"
              class="animate-fade-up motion-reduce:animate-none motion-reduce:opacity-100"
              :style="{ animationDelay: `${Math.min(i, 6) * 70}ms` }"
            >
              <DocumentCard :doc="d" />
            </div>
          </div>
        </section>
      </template>

      <p
        v-else
        class="animate-fade-in py-24 text-center text-lg text-ink-500 motion-reduce:animate-none"
      >
        Không tìm thấy tài liệu.
      </p>
    </div>
  </div>
</template>
