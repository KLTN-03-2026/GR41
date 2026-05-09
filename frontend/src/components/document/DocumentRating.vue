<script setup>
import { ref } from 'vue'
import Rating from 'primevue/rating'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  documentId: { type: [Number, String], required: true },
  disabled: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const toast = useToast()
const stars = ref(5)
const comment = ref('')

function onSubmit() {
  if (props.disabled) {
    toast.info('Đăng nhập để đánh giá')
    return
  }
  emit('submit', { score: stars.value, comment: comment.value })
  comment.value = ''
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
    <h3 class="font-semibold text-slate-800">Đánh giá của bạn</h3>
    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center">
      <Rating v-model="stars" :cancel="false" :stars="5" />
      <Textarea
        v-model="comment"
        rows="3"
        class="flex-1"
        placeholder="Nhận xét (tuỳ chọn)"
      />
    </div>
    <Button label="Gửi đánh giá" class="mt-3" :disabled="disabled" @click="onSubmit" />
  </div>
</template>
