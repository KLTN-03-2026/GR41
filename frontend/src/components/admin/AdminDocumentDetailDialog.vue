<script setup>
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import Tag from 'primevue/tag'
import Skeleton from 'primevue/skeleton'
import Divider from 'primevue/divider'
import { documentService } from '@/services/documentService'

const props = defineProps({
  visible: { type: Boolean, default: false },
  documentId: { type: [Number, String], default: null },
})

const emit = defineEmits(['update:visible', 'edit', 'delete'])

const idRef = computed(() => (props.documentId !== null ? String(props.documentId) : null))
const shouldFetch = computed(() => props.visible && !!idRef.value)

const { data: raw, isFetching } = useQuery({
  queryKey: ['admin', 'document', idRef],
  queryFn: () => documentService.adminDetail(idRef.value),
  enabled: shouldFetch,
})

const doc = computed(() => raw.value?.document || raw.value || null)

function langLabel(code) {
  const map = { vi: 'Tiếng Việt', en: 'English' }
  return map[code] ?? code
}

function formatDate(iso) {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' })
}
</script>

<template>
  <Dialog
    :visible="visible"
    modal
    header="Chi tiết tài liệu"
    :style="{ width: 'min(56rem, 95vw)' }"
    :pt="{
      root: { class: 'max-h-[90vh] !flex !flex-col' },
      content: { class: '!flex-1 !min-h-0 !overflow-y-auto scrollbar-dialog' },
    }"
    @update:visible="(v) => emit('update:visible', v)"
  >
    <!-- Loading skeleton -->
    <div v-if="isFetching" class="space-y-4">
      <div class="flex gap-5">
        <Skeleton width="8rem" height="10rem" border-radius="0.75rem" />
        <div class="flex-1 space-y-3">
          <Skeleton width="70%" height="1.5rem" />
          <Skeleton width="40%" height="1rem" />
          <Skeleton width="30%" height="1rem" />
        </div>
      </div>
      <Skeleton width="100%" height="6rem" />
    </div>

    <!-- Content -->
    <div v-else-if="doc" class="space-y-5">
      <!-- Hero section -->
      <div class="flex gap-5">
        <img
          :src="doc.cover_image || '/vite.svg'"
          class="h-40 w-32 flex-shrink-0 rounded-xl object-cover shadow-md"
          alt="Cover"
        />
        <div class="min-w-0 flex-1 space-y-2">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <h2 class="text-xl font-bold leading-tight text-slate-800">{{ doc.title }}</h2>
            <Tag
              :value="doc.is_featured ? 'Nổi bật' : 'Thường'"
              :severity="doc.is_featured ? 'success' : 'secondary'"
            />
          </div>
          <p v-if="doc.author" class="text-sm text-slate-600">
            <span class="font-semibold text-slate-700">Tác giả:</span> {{ doc.author }}
          </p>
          <p v-if="doc.category" class="text-sm text-slate-600">
            <span class="font-semibold text-slate-700">Danh mục:</span> {{ doc.category.name }}
          </p>

          <!-- Stats row -->
          <div class="flex flex-wrap gap-4 pt-1">
            <div class="flex items-center gap-1.5 text-sm text-slate-500">
              <i class="pi pi-eye text-slate-400" />
              <span>{{ (doc.view_count ?? 0).toLocaleString() }} lượt xem</span>
            </div>
            <div class="flex items-center gap-1.5 text-sm text-slate-500">
              <i class="pi pi-download text-slate-400" />
              <span>{{ (doc.download_count ?? 0).toLocaleString() }} lượt tải</span>
            </div>
            <div v-if="doc.avg_rating" class="flex items-center gap-1.5 text-sm text-slate-500">
              <i class="pi pi-star-fill text-amber-400" />
              <span>{{ doc.avg_rating }} / 5</span>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="doc.tags?.length" class="flex flex-wrap gap-1.5 pt-1">
            <span
              v-for="tag in doc.tags"
              :key="tag.id"
              class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-blue-100"
            >
              {{ tag.name }}
            </span>
          </div>
        </div>
      </div>

      <Divider class="!my-2" />

      <!-- Description -->
      <div v-if="doc.description">
        <h3 class="mb-2 text-xs font-semibold uppercase tracking-widest text-slate-400">Mô tả</h3>
        <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ doc.description }}</p>
      </div>

      <!-- Metadata grid -->
      <div>
        <h3 class="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-400">
          Thông tin chi tiết
        </h3>
        <dl class="grid gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
          <div v-if="doc.publisher" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Nhà xuất bản</dt>
            <dd class="text-sm font-medium text-slate-700">{{ doc.publisher }}</dd>
          </div>
          <div v-if="doc.published_year" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Năm xuất bản</dt>
            <dd class="text-sm font-medium text-slate-700">{{ doc.published_year }}</dd>
          </div>
          <div v-if="doc.isbn" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">ISBN</dt>
            <dd class="text-sm font-medium text-slate-700">{{ doc.isbn }}</dd>
          </div>
          <div v-if="doc.pages" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Số trang</dt>
            <dd class="text-sm font-medium text-slate-700">{{ doc.pages }} trang</dd>
          </div>
          <div v-if="doc.language" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Ngôn ngữ</dt>
            <dd class="text-sm font-medium text-slate-700">{{ langLabel(doc.language) }}</dd>
          </div>
          <div v-if="doc.slug" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Slug</dt>
            <dd class="truncate font-mono text-sm font-medium text-slate-700">{{ doc.slug }}</dd>
          </div>
          <div v-if="doc.uploaded_by" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Người tải lên</dt>
            <dd class="text-sm font-medium text-slate-700">{{ doc.uploaded_by.name }}</dd>
          </div>
          <div class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Ngày tạo</dt>
            <dd class="text-sm font-medium text-slate-700">{{ formatDate(doc.created_at) }}</dd>
          </div>
          <div v-if="doc.updated_at" class="flex flex-col gap-0.5">
            <dt class="text-xs text-slate-400">Cập nhật lần cuối</dt>
            <dd class="text-sm font-medium text-slate-700">{{ formatDate(doc.updated_at) }}</dd>
          </div>
        </dl>
      </div>

      <!-- File link -->
      <div v-if="doc.file_url">
        <h3 class="mb-2 text-xs font-semibold uppercase tracking-widest text-slate-400">File tài liệu</h3>
        <a
          :href="doc.file_url"
          target="_blank"
          rel="noopener"
          class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition-colors hover:bg-blue-100"
        >
          <i class="pi pi-file-pdf text-red-500" />
          Xem / tải file PDF
          <i class="pi pi-external-link text-xs opacity-60" />
        </a>
      </div>
    </div>

    <template #footer>
      <Button label="Đóng" severity="secondary" text @click="emit('update:visible', false)" />
      <Button
        label="Sửa"
        icon="pi pi-pencil"
        @click="emit('edit', doc?.id)"
      />
      <Button
        label="Xóa"
        icon="pi pi-trash"
        severity="danger"
        outlined
        @click="emit('delete', doc?.id)"
      />
    </template>
  </Dialog>
</template>
