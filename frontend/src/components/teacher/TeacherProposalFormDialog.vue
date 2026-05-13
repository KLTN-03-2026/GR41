<script setup>
import { ref, computed } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import MultiSelect from 'primevue/multiselect'
import Button from 'primevue/button'
import ImageUploader from '@/components/common/ImageUploader.vue'
import { proposalService } from '@/services/proposalService'
import { categoryService } from '@/services/categoryService'
import { tagService } from '@/services/tagService'
import { useToast } from '@/composables/useToast'
import { unwrapList } from '@/utils/apiHelpers'

const props = defineProps({
  visible: { type: Boolean, default: false },
})

const emit = defineEmits(['update:visible', 'saved'])

const toast = useToast()
const qc = useQueryClient()

const emptyForm = () => ({
  title: '',
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
})

const form = ref(emptyForm())
const saveLoading = ref(false)
const submitted = ref(false)
const currentYear = new Date().getFullYear()
const isbnPattern = /^(?:97[89][-\s]?)?\d{1,5}[-\s]?\d{1,7}[-\s]?\d{1,7}[-\s]?[\dX]$/i

const { data: cats } = useQuery({
  queryKey: ['categories', 'list'],
  queryFn: () => categoryService.list(),
})

const { data: tagsRaw } = useQuery({
  queryKey: ['tags', 'list'],
  queryFn: () => tagService.list({ per_page: 500 }),
})

const categories = computed(() => unwrapList(cats.value))
const tagOptions = computed(() => unwrapList(tagsRaw.value))

const saveMutation = useMutation({
  mutationFn: (payload) => proposalService.create(payload),
  onSuccess: () => {
    toast.success('Đề xuất đã được gửi, chờ Admin duyệt')
    qc.invalidateQueries({ queryKey: ['teacher', 'proposals'] })
    emit('update:visible', false)
    emit('saved')
    form.value = emptyForm()
    submitted.value = false
  },
  onError: (e) => toast.error(e?.message || 'Lỗi khi gửi đề xuất'),
  onSettled: () => { saveLoading.value = false },
})

function submit() {
  submitted.value = true
  if (!form.value.title.trim()) return toast.error('Vui lòng nhập tiêu đề')
  if (!form.value.description.trim()) return toast.error('Vui lòng nhập mô tả')
  if (!form.value.category_id) return toast.error('Vui lòng chọn danh mục')
  if (!form.value.file_url) return toast.error('Vui lòng upload file PDF')
  if (form.value.published_year && (form.value.published_year < 1900 || form.value.published_year > currentYear)) {
    return toast.error('Năm phải từ 1900 đến năm hiện tại')
  }
  if (form.value.isbn && !isbnPattern.test(form.value.isbn)) {
    return toast.error('ISBN không hợp lệ')
  }
  const { tags, ...rest } = form.value
  saveLoading.value = true
  saveMutation.mutate({ ...rest, tags })
}

function handleHide() {
  form.value = emptyForm()
  submitted.value = false
  emit('update:visible', false)
}

function showUploadError(error) {
  toast.error(error?.message || 'Upload không hợp lệ')
}
</script>

<template>
  <Dialog
    :visible="visible"
    modal
    header="Đề xuất tài liệu mới"
    :style="{ width: 'min(64rem, 96vw)' }"
    :pt="{
      root: { class: 'max-h-[92vh] !flex !flex-col' },
      content: { class: '!flex-1 !min-h-0 !overflow-y-auto overflow-x-hidden scrollbar-dialog' },
    }"
    @update:visible="handleHide"
  >
    <div class="space-y-6 py-1">
      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left: uploads -->
        <div class="space-y-4">
          <ImageUploader
            v-model="form.cover_image"
            label="Ảnh bìa"
            accept="image/*"
            :max-size-mb="5"
            @error="showUploadError"
          />
          <ImageUploader
            v-model="form.file_url"
            label="File PDF *"
            accept="application/pdf"
            aspect="cover"
            :max-size-mb="50"
            @error="showUploadError"
          />
          <p v-if="submitted && !form.file_url" class="text-xs font-medium text-rose-600">
            Vui lòng upload file PDF
          </p>
        </div>

        <!-- Right: fields -->
        <div class="space-y-4 lg:col-span-2">
          <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">
              Tiêu đề <span class="text-red-500">*</span>
            </label>
            <InputText v-model="form.title" class="w-full" fluid placeholder="Nhập tên tài liệu..." />
            <p v-if="submitted && !form.title.trim()" class="mt-1 text-xs font-medium text-rose-600">
              Vui lòng nhập tiêu đề
            </p>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-slate-700">
              Mô tả <span class="text-red-500">*</span>
            </label>
            <Textarea v-model="form.description" rows="4" class="w-full" fluid placeholder="Tóm tắt nội dung tài liệu..." />
            <p v-if="submitted && !form.description.trim()" class="mt-1 text-xs font-medium text-rose-600">
              Vui lòng nhập mô tả
            </p>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">
                Danh mục <span class="text-red-500">*</span>
              </label>
              <Dropdown
                v-model="form.category_id"
                :options="categories"
                option-label="name"
                option-value="id"
                placeholder="Chọn danh mục"
                show-clear
                class="w-full"
                :pt="{ overlay: { class: 'scrollbar-select-panel' } }"
              />
              <p v-if="submitted && !form.category_id" class="mt-1 text-xs font-medium text-rose-600">
                Vui lòng chọn danh mục
              </p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">Ngôn ngữ</label>
              <Dropdown
                v-model="form.language"
                :options="[{ label: 'Tiếng Việt', value: 'vi' }, { label: 'English', value: 'en' }]"
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
              <p
                v-if="submitted && form.published_year && (form.published_year < 1900 || form.published_year > currentYear)"
                class="mt-1 text-xs font-medium text-rose-600"
              >
                Năm phải từ 1900 đến năm hiện tại
              </p>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-slate-700">ISBN</label>
              <InputText v-model="form.isbn" class="w-full" fluid placeholder="978-..." />
              <p
                v-if="submitted && form.isbn && !isbnPattern.test(form.isbn)"
                class="mt-1 text-xs font-medium text-rose-600"
              >
                ISBN không hợp lệ
              </p>
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
              :pt="{ overlay: { class: 'scrollbar-select-panel' } }"
            />
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <Button label="Hủy" severity="secondary" text @click="handleHide" />
      <Button
        label="Gửi đề xuất"
        icon="pi pi-send"
        :loading="saveLoading"
        @click="submit"
      />
    </template>
  </Dialog>
</template>
