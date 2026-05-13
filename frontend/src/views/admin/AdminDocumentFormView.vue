<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
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
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'

function slugify(s) {
  return String(s || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '')
}

const route = useRoute()
const router = useRouter()
const toast = useToast()
const qc = useQueryClient()

const idParam = computed(() => route.params.id)
const isEdit = computed(() => !!idParam.value)

const form = ref({
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

const { data: detail } = useQuery({
  queryKey: ['admin', 'document', idParam],
  queryFn: () => documentService.adminDetail(idParam.value),
  enabled: isEdit,
})

watch(
  detail,
  (d) => {
    if (!d) return
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
    isEdit.value ? documentService.adminUpdate(idParam.value, payload) : documentService.adminCreate(payload),
  onSuccess: () => {
    toast.success('Đã lưu')
    qc.invalidateQueries({ queryKey: ['admin', 'documents'] })
    router.push({ name: 'admin.documents' })
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
})

function submit() {
  if (!form.value.file_url) return toast.error('Cần upload file PDF (Cloudinary)')
  const { tags, ...rest } = form.value
  saveMutation.mutate({ ...rest, tags })
}
</script>

<template>
  <div class="mx-auto max-w-3xl space-y-8">
    <AdminPageHeader
      :title="isEdit ? 'Sửa tài liệu' : 'Thêm tài liệu'"
      subtitle="Slug, metadata và file PDF — lưu để cập nhật giao diện công khai ngay khi xong."
    />

    <section class="rounded-2xl border border-brand-200/60 bg-gradient-to-r from-brand-50/70 via-indigo-50/60 to-purple-50/50 p-4 text-sm text-slate-700 shadow-soft">
      Tối ưu nhập liệu: điền metadata trước, sau đó tải ảnh bìa và file PDF để đảm bảo tài liệu hiển thị đầy đủ.
    </section>

    <AdminSurface :padded="true">
      <div class="space-y-4">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tiêu đề</label>
        <InputText v-model="form.title" class="w-full" fluid />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Slug</label>
        <InputText v-model="form.slug" class="w-full" fluid />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Mô tả</label>
        <Textarea v-model="form.description" rows="6" class="w-full" fluid />
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Danh mục</label>
          <Dropdown
            v-model="form.category_id"
            :options="categories"
            option-label="name"
            option-value="id"
            placeholder="Chọn"
            class="w-full"
            :pt="{
              panel: { class: 'scrollbar-dialog scrollbar-select-panel' },
              overlay: { class: 'scrollbar-dialog scrollbar-select-panel' },
            }"
          />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Ngôn ngữ</label>
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
          <label class="mb-1 block text-sm font-medium text-slate-700">Tác giả</label>
          <InputText v-model="form.author" class="w-full" fluid />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">NXB</label>
          <InputText v-model="form.publisher" class="w-full" fluid />
        </div>
      </div>
      <div class="grid gap-4 sm:grid-cols-3">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Năm XB</label>
          <InputText v-model.number="form.published_year" class="w-full" fluid />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">ISBN</label>
          <InputText v-model="form.isbn" class="w-full" fluid />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Trang</label>
          <InputText v-model.number="form.pages" class="w-full" fluid />
        </div>
      </div>

      <div class="flex items-center gap-3">
        <span class="text-sm font-medium text-slate-700">Nổi bật</span>
        <InputSwitch v-model="form.is_featured" />
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tags</label>
        <MultiSelect
          v-model="form.tags"
          :options="tagOptions"
          option-label="name"
          option-value="id"
          display="chip"
          filter
          class="w-full"
          :pt="{
            panel: { class: 'scrollbar-dialog scrollbar-select-panel' },
            overlay: { class: 'scrollbar-dialog scrollbar-select-panel' },
          }"
        />
      </div>

      <ImageUploader v-model="form.cover_image" label="Ảnh bìa" accept="image/*" :max-size-mb="5" />
      <ImageUploader
        v-model="form.file_url"
        label="File PDF"
        accept="application/pdf"
        aspect="cover"
        :max-size-mb="50"
      />

      <Button label="Lưu" icon="pi pi-check" :loading="saveMutation.isPending" @click="submit" />
      </div>
    </AdminSurface>
  </div>
</template>
