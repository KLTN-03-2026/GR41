<script setup>
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { changePasswordSchema } from '@/utils/validators'
import { profileService } from '@/services/profileService'
import { useToast } from '@/composables/useToast'

const toast = useToast()

const { defineField, handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: toTypedSchema(changePasswordSchema),
  initialValues: {
    current_password: '',
    password: '',
    password_confirmation: '',
  },
})

const [current_password, cAttrs] = defineField('current_password')
const [password, pAttrs] = defineField('password')
const [password_confirmation, pcAttrs] = defineField('password_confirmation')

const onSubmit = handleSubmit(async (values) => {
  try {
    await profileService.changePassword(values)
    toast.success('Đã đổi mật khẩu')
  } catch (e) {
    toast.error(e?.message || 'Không đổi được')
  }
})
</script>

<template>
  <div class="mx-auto max-w-lg px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-slate-900">Đổi mật khẩu</h1>
    <form class="mt-8 space-y-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm" @submit.prevent="onSubmit">
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Mật khẩu hiện tại</label>
        <Password v-model="current_password" v-bind="cAttrs" class="w-full" fluid toggle-mask :feedback="false" />
        <p v-if="errors.current_password" class="mt-1 text-xs text-red-600">{{ errors.current_password }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Mật khẩu mới</label>
        <Password v-model="password" v-bind="pAttrs" class="w-full" fluid toggle-mask />
        <p v-if="errors.password" class="mt-1 text-xs text-red-600">{{ errors.password }}</p>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-slate-700">Xác nhận</label>
        <Password v-model="password_confirmation" v-bind="pcAttrs" class="w-full" fluid toggle-mask :feedback="false" />
        <p v-if="errors.password_confirmation" class="mt-1 text-xs text-red-600">{{ errors.password_confirmation }}</p>
      </div>
      <Button type="submit" label="Lưu" :loading="isSubmitting" />
    </form>
  </div>
</template>
