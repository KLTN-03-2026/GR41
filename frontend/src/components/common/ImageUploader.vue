<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import { useUploadImage } from '@/composables/useUploadImage'

const props = defineProps({
  modelValue: { type: String, default: '' },
  accept: { type: String, default: 'image/*' },
  label: { type: String, default: 'Tải ảnh lên' },
  aspect: { type: String, default: 'square' },
  maxSizeMb: { type: Number, default: 5 },
})

const emit = defineEmits(['update:modelValue', 'uploaded', 'error'])

const { mutateAsync: upload, isPending } = useUploadImage()
const fileInput = ref(null)
const error = ref('')

const isPdf = () => props.accept.includes('pdf')
const acceptsFile = (file) => {
  if (props.accept === 'image/*') return file.type.startsWith('image/')
  if (props.accept.includes('pdf')) return file.type === 'application/pdf'
  return props.accept
    .split(',')
    .map((item) => item.trim())
    .filter(Boolean)
    .some((item) => file.type === item || file.name.toLowerCase().endsWith(item.toLowerCase()))
}

const handleSelect = async (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  error.value = ''
  if (!acceptsFile(file)) {
    error.value = isPdf() ? 'Chỉ chấp nhận file PDF.' : 'Chỉ chấp nhận file ảnh.'
    emit('error', new Error(error.value))
    if (fileInput.value) fileInput.value.value = ''
    return
  }
  if (file.size > props.maxSizeMb * 1024 * 1024) {
    error.value = isPdf()
      ? `File không quá ${props.maxSizeMb}MB.`
      : `Ảnh không quá ${props.maxSizeMb}MB.`
    emit('error', new Error(error.value))
    if (fileInput.value) fileInput.value.value = ''
    return
  }
  try {
    const result = await upload(file)
    emit('update:modelValue', result.url)
    emit('uploaded', result)
  } catch (err) {
    emit('error', err)
  } finally {
    if (fileInput.value) fileInput.value.value = ''
  }
}

const removeImage = () => emit('update:modelValue', '')
</script>

<template>
  <div class="space-y-2">
    <label v-if="label" class="block text-sm font-medium text-slate-700">{{ label }}</label>
    <div class="flex flex-wrap items-center gap-3">
      <div
        v-if="modelValue"
        class="relative overflow-hidden rounded-lg border border-slate-200 bg-slate-50"
        :class="{
          'h-32 w-32': aspect === 'square',
          'min-h-[72px] w-full max-w-xs md:w-72': aspect === 'cover' && isPdf(),
          'h-32 w-32': aspect === 'cover' && !isPdf(),
          'h-24 w-24 rounded-full': aspect === 'avatar',
        }"
      >
        <template v-if="isPdf()">
          <div class="flex h-full min-h-[72px] flex-col items-center justify-center gap-2 p-3 text-center">
            <Icon icon="mdi:file-pdf-box" class="h-10 w-10 text-red-600" />
            <a
              :href="modelValue"
              target="_blank"
              rel="noopener"
              class="break-all text-xs font-medium text-primary-blue underline"
            >
              Mở PDF
            </a>
          </div>
        </template>
        <img v-else :src="modelValue" class="h-full w-full object-cover" alt="" />
        <button
          type="button"
          class="absolute top-1 right-1 flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-sm leading-none text-white shadow hover:bg-red-600"
          aria-label="Xóa"
          @click="removeImage"
        >
          ×
        </button>
      </div>
      <label
        class="inline-flex cursor-pointer items-center rounded-lg bg-primary-blue px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 disabled:opacity-60"
        :class="{ 'pointer-events-none opacity-70': isPending }"
      >
        <input ref="fileInput" type="file" :accept="accept" class="hidden" @change="handleSelect" />
        {{ isPending ? 'Đang tải...' : modelValue ? 'Đổi file' : 'Chọn file' }}
      </label>
    </div>
    <p v-if="error" class="text-xs font-medium text-rose-600">{{ error }}</p>
  </div>
</template>
