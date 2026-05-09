<script setup>
import { ref } from 'vue'
import { useMutation } from '@tanstack/vue-query'
import RadioButton from 'primevue/radiobutton'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Button from 'primevue/button'
import { adminBroadcastService } from '@/services/adminChatbotService'
import { useToast } from '@/composables/useToast'
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue'
import AdminSurface from '@/components/admin/AdminSurface.vue'

const toast = useToast()

const target = ref('all')
const title = ref('')
const content = ref('')
const sendLoading = ref(false)

const sendMutation = useMutation({
  mutationFn: () =>
    adminBroadcastService.broadcast({
      target: target.value,
      title: title.value,
      content: content.value,
    }),
  onSuccess: () => {
    toast.success('Đã gửi thông báo')
    title.value = ''
    content.value = ''
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => { sendLoading.value = false },
})
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-8">
    <AdminPageHeader
      title="Broadcast thông báo"
      subtitle="Gửi thông báo tới nhóm đối tượng được chọn — người dùng nhận trong Trung tâm tin."
    />

    <section class="rounded-2xl border border-brand-200/60 bg-gradient-to-r from-brand-50/70 via-indigo-50/60 to-purple-50/50 p-4 text-sm text-slate-700 shadow-soft">
      Mẹo: tiêu đề ngắn gọn, nội dung rõ ràng theo CTA sẽ giúp tăng tỷ lệ đọc thông báo.
    </section>

    <AdminSurface :padded="true">
      <div class="space-y-6">
      <div class="space-y-3">
        <span class="text-sm font-semibold text-slate-800">Đối tượng</span>
        <div class="flex flex-wrap gap-4">
          <label class="flex items-center gap-2 rounded-xl border border-slate-200/80 bg-slate-50/70 px-3 py-2 transition hover:border-brand-200 hover:bg-brand-50/60">
            <RadioButton v-model="target" value="all" />
            Tất cả
          </label>
          <label class="flex items-center gap-2 rounded-xl border border-slate-200/80 bg-slate-50/70 px-3 py-2 transition hover:border-brand-200 hover:bg-brand-50/60">
            <RadioButton v-model="target" value="students" />
            Sinh viên
          </label>
          <label class="flex items-center gap-2 rounded-xl border border-slate-200/80 bg-slate-50/70 px-3 py-2 transition hover:border-brand-200 hover:bg-brand-50/60">
            <RadioButton v-model="target" value="teachers" />
            Giáo viên
          </label>
        </div>
      </div>

      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tiêu đề</label>
        <InputText v-model="title" class="w-full" fluid />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Nội dung</label>
        <Textarea v-model="content" rows="8" class="w-full" fluid />
      </div>

      <Button label="Gửi" icon="pi pi-send" :loading="sendLoading" @click="() => { sendLoading = true; sendMutation.mutate() }" />
      </div>
    </AdminSurface>
  </div>
</template>
