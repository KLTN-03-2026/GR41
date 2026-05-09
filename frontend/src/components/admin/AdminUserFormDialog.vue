<script setup>
import { ref, computed, watch } from 'vue'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import ImageUploader from '@/components/common/ImageUploader.vue'
import { adminUserService } from '@/services/adminUserService'
import { metaService } from '@/services/metaService'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  visible: { type: Boolean, default: false },
  /** null = tạo mới; có id = sửa */
  editUserId: { type: [Number, String], default: null },
})

const emit = defineEmits(['update:visible'])

const toast = useToast()
const qc = useQueryClient()

const idParam = computed(() =>
  props.editUserId !== null && props.editUserId !== undefined && props.editUserId !== ''
    ? String(props.editUserId)
    : null,
)
const isEdit = computed(() => !!idParam.value)

const emptyForm = () => ({
  name: '',
  email: '',
  password: '',
  role_id: null,
  phone: '',
  student_code: '',
  status: 'active',
  avatar: '',
})

const form = ref(emptyForm())

const { data: roles } = useQuery({
  queryKey: ['meta', 'roles'],
  queryFn: metaService.roles,
})

const roleOptions = computed(() => roles.value || [])

const shouldLoadDetail = computed(() => props.visible && isEdit.value)

const { data: detail } = useQuery({
  queryKey: ['admin', 'user', idParam],
  queryFn: () => adminUserService.detail(idParam.value),
  enabled: shouldLoadDetail,
})

watch(
  () => [props.visible, props.editUserId],
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
    const u = d.user || d
    form.value = {
      name: u.name || '',
      email: u.email || '',
      password: '',
      role_id: u.role?.id ?? null,
      phone: u.phone || '',
      student_code: u.student_code || '',
      status: u.status || 'active',
      avatar: u.avatar || '',
    }
  },
  { immediate: true },
)

const saveLoading = ref(false)

const saveMutation = useMutation({
  mutationFn: (payload) =>
    isEdit.value ? adminUserService.update(idParam.value, payload) : adminUserService.create(payload),
  onSuccess: () => {
    toast.success('Đã lưu')
    qc.invalidateQueries({ queryKey: ['admin', 'users'] })
    if (idParam.value) qc.invalidateQueries({ queryKey: ['admin', 'user', idParam.value] })
    emit('update:visible', false)
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
  onSettled: () => {
    saveLoading.value = false
  },
})

function submit() {
  const payload = { ...form.value }
  if (isEdit.value && !payload.password) delete payload.password
  saveLoading.value = true
  saveMutation.mutate(payload)
}

</script>

<template>
  <Dialog
    :visible="visible"
    modal
    :header="isEdit ? 'Sửa người dùng' : 'Thêm người dùng'"
    class="admin-user-dialog"
    :style="{ width: 'min(36rem, 95vw)' }"
    :pt="{
      root: { class: 'max-h-[90vh] !flex !flex-col' },
      content: { class: '!flex-1 !min-h-0 !overflow-y-auto overflow-x-hidden' },
    }"
    @update:visible="(v) => emit('update:visible', v)"
  >
    <div class="space-y-4 pb-1 pr-1">
      <p class="mb-4 text-sm text-slate-600">
        Avatar, vai trò và trạng thái — mật khẩu chỉ bắt buộc khi tạo mới.
      </p>
      <ImageUploader v-model="form.avatar" label="Avatar" aspect="avatar" accept="image/*" />
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Tên</label>
        <InputText v-model="form.name" class="w-full" fluid />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
        <InputText v-model="form.email" class="w-full" fluid />
      </div>
      <div v-if="!isEdit">
        <label class="mb-1 block text-sm font-medium text-slate-700">Mật khẩu</label>
        <Password v-model="form.password" class="w-full" fluid toggle-mask />
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Vai trò</label>
        <Dropdown
          v-model="form.role_id"
          :options="roleOptions"
          option-label="name"
          option-value="id"
          placeholder="Chọn vai trò"
          class="w-full"
        />
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Điện thoại</label>
          <InputText v-model="form.phone" class="w-full" fluid />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-700">Mã SV</label>
          <InputText v-model="form.student_code" class="w-full" fluid />
        </div>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Trạng thái</label>
        <Dropdown
          v-model="form.status"
          :options="[
            { label: 'Hoạt động', value: 'active' },
            { label: 'Khóa', value: 'banned' },
          ]"
          option-label="label"
          option-value="value"
          class="w-full"
        />
      </div>
    </div>
    <template #footer>
      <Button label="Hủy" severity="secondary" text @click="emit('update:visible', false)" />
      <Button label="Lưu" :loading="saveLoading" @click="submit" />
    </template>
  </Dialog>
</template>
