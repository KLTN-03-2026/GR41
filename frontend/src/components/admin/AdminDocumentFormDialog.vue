<script setup>
import { ref, computed, watch } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import MultiSelect from 'primevue/multiselect'
import InputSwitch from 'primevue/inputswitch'
import Button from 'primevue/button'
import ImageUploader from '@/components/common/ImageUploader.vue'
import { documentService } from '@/services/documentService'
import { categoryService } from '@/services/categoryService'
import { tagService } from '@/services/tagService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'

function slugify(s) {
  return String(s || '')
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

const props = defineProps({
  visible: { type: Boolean, default: false },
  editDocumentId: { type: [Number, String], default: null },
})

const emit = defineEmits(['update:visible', 'saved'])

const toast = useToast()
const qc = useQueryClient()

const idParam = computed(() =>
  props.editDocumentId !== null && props.editDocumentId !== undefined
    ? String(props.editDocumentId)
    : null,
)
const isEdit = computed(() => !!idParam.value)

const emptyForm = () => ({
  title: '',
  slug: '',
  description: '',
  category_id: null,
  author: '',
  publisher: '',
  published_year: null,
  isbn: '',
  language: 'vi',
  pages: null,
  file_url: '',
  cover_image: '',
  tags: [],
  is_featured: false,
})

const form = ref(emptyForm())
const saveLoading = ref(false)

const shouldLoadDetail = computed(() => props.visible && isEdit.value)

const { data: detail } = useQuery({
  queryKey: ['admin', 'document', idParam],
  queryFn: () => documentService.adminDetail(idParam.value),
  enabled: shouldLoadDetail,
})

watch(
  () => [props.visible, props.editDocumentId],
  ([vis]) => {
    if (!vis) {
      saveLoading.value = false
      return
    }
    if (!isEdit.value) form.value = emptyForm()
  },
)

watch(
  detail,
  (d) => {
    if (!d || !shouldLoadDetail.value) return
    const doc = d.document || d
    form.value = {
      title: doc.title || '',
      slug: doc.slug || '',
      description: doc.description || '',
      category_id: doc.category_id ?? doc.category?.id ?? null,
      author: doc.author || '',
      publisher: doc.publisher || '',
      published_year: doc.published_year ?? null,
      isbn: doc.isbn || '',
      language: doc.language || 'vi',
      pages: doc.pages ?? null,
      file_url: doc.file_url || '',
      cover_image: doc.cover_image || '',
      tags: (doc.tags || []).map((t) => t.id ?? t),
      is_featured: !!doc.is_featured,
    }
  },
  { immediate: true },
)

watch(
  () => form.value.title,
  (t) => {
    if (!isEdit.value) form.value.slug = slugify(t)
  },
)

const { data: cats } = useQuery({
  queryKey: ['admin', 'categories'],
  queryFn: () => categoryService.adminList({ per_page: 500 }),
})

const { data: tagsRaw } = useQuery({
  queryKey: ['admin', 'tags'],
  queryFn: () => tagService.list({ per_page: 500 }),
})

const categories = computed(() => unwrapList(cats.value))
const tagOptions = computed(() => unwrapList(tagsRaw.value))

const saveMutation = useMutation({
  mutationFn: (payload) =>
    isEdit.value
      ? documentService.adminUpdate(idParam.value, payload)
      : documentService.adminCreate(payload),
  onSuccess: () => {
    toast.success('Đã lưu')
    qc.invalidateQueries({ queryKey: ['admin', 'documents'] })
    emit('update:visible', false)
    emit('saved')
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => {
    saveLoading.value = false
  },
})

function submit() {
  if (!form.value.file_url) return toast.error('Cần upload file PDF trước khi lưu')
  const { tags, ...rest } = form.value
  saveLoading.value = true
  saveMutation.mutate({ ...rest, tags })
}
</script>

<template>
  <Dialog
    :visible="visible"
    modal
    :header="isEdit ? 'Chỉnh sửa tài liệu' : 'Thêm tài liệu mới'"
    :style="{ width: 'min(64rem, 96vw)' }"
    :pt="{
      root: { class: 'max-h-[92vh] !flex !flex-col' },
      content: { class: '!flex-1 !min-h-0 !overflow-y-auto overflow-x-hidden scrollbar-dialog' },
    }"
    @update:visible="(v) => emit('update:visible', v)"
  >
    <div class="space-y-6 py-1">
      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left column: media uploads -->
        <div class="space-y-4">
          <ImageUploader v-model="form.cover_image" label="Ảnh bìa" accept="image/*" :max-size-mb="5" />
          <ImageUploader
            v-model="form.file_url"
            label="File PDF"
            accept="application/pdf"
            aspect="cover"
            :max-size-mb="50"
          />
        </div>

        <!-- Right columns: form fields -->
        <div class="space-y-4 lg:col-span-2">
          <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">
              Tiêu đề <span class="text-red-500">*</span>
            </label>
            <InputText v-model="form.title" class="w-full" fluid placeholder="Nhập tên tài liệu..." />
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Slug (URL)</label>
            <InputText v-model="form.slug" class="w-full" fluid placeholder="tu-dong-tao-tu-tieu-de" />
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Mô tả</label>
            <Textarea
              v-model="form.description"
              rows="4"
              class="w-full"
              fluid
              placeholder="Tóm tắt nội dung tài liệu..."
            />
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Danh mục</label>
              <Dropdown
                v-model="form.category_id"
                :options="categories"
                option-label="name"
                option-value="id"
                placeholder="Chọn danh mục"
                show-clear
                class="w-full"
                :pt="{
                  panel: { class: 'scrollbar-dialog scrollbar-select-panel' },
                  overlay: { class: 'scrollbar-dialog scrollbar-select-panel' },
                }"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Ngôn ngữ</label>
              <Dropdown
                v-model="form.language"
                :options="[
                  { label: 'Tiếng Việt', value: 'vi' },
                  { label: 'English', value: 'en' },
                ]"
                option-label="label"
                option-value="value"
                class="w-full"
              />
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Tác giả</label>
              <InputText v-model="form.author" class="w-full" fluid placeholder="Tên tác giả..." />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Nhà xuất bản</label>
              <InputText v-model="form.publisher" class="w-full" fluid placeholder="NXB..." />
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-3">
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Năm xuất bản</label>
              <InputText v-model.number="form.published_year" class="w-full" fluid placeholder="2024" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">ISBN</label>
              <InputText v-model="form.isbn" class="w-full" fluid placeholder="978-..." />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Số trang</label>
              <InputText v-model.number="form.pages" class="w-full" fluid placeholder="250" />
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">Tags</label>
            <MultiSelect
              v-model="form.tags"
              :options="tagOptions"
              option-label="name"
              option-value="id"
              display="chip"
              filter
              class="w-full"
              placeholder="Chọn tags..."
              :pt="{
                panel: { class: 'scrollbar-dialog scrollbar-select-panel' },
                overlay: { class: 'scrollbar-dialog scrollbar-select-panel' },
              }"
            />
          </div>

          <div
            class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
          >
            <InputSwitch v-model="form.is_featured" />
            <div>
              <p class="text-sm font-semibold text-slate-700">Nổi bật</p>
              <p class="text-xs text-slate-500">Hiển thị trong khu vực tài liệu nổi bật</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <Button label="Hủy" severity="secondary" text @click="emit('update:visible', false)" />
      <Button label="Lưu tài liệu" icon="pi pi-check" :loading="saveLoading" @click="submit" />
    </template>
  </Dialog>
</template>
