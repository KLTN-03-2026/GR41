<script setup>
import { computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import { toTypedSchema } from '@vee-validate/yup'
import { Icon } from '@iconify/vue'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { authService } from '@/services/authService'
import { useToast } from '@/composables/useToast'
import logoMark from '@/assets/images/logo.png'
import logoText from '@/assets/images/logo-text.png'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const token = computed(() => (typeof route.query.token === 'string' ? route.query.token : ''))
const email = computed(() => (typeof route.query.email === 'string' ? route.query.email : ''))

const canReset = computed(() => Boolean(token.value && email.value))

const schema = yup.object({
  password: yup.string().min(8, 'Tối thiểu 8 ký tự').required('Bắt buộc'),
  password_confirmation: yup
    .string()
    .oneOf([yup.ref('password')], 'Không khớp')
    .required('Bắt buộc'),
})

const { defineField, handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: toTypedSchema(schema),
  initialValues: { password: '', password_confirmation: '' },
})

const [password, paAttrs] = defineField('password')
const [password_confirmation, pcAttrs] = defineField('password_confirmation')

const onSubmit = handleSubmit(async (values) => {
  try {
    await authService.resetPassword({
      email: email.value,
      token: token.value,
      password: values.password,
      password_confirmation: values.password_confirmation,
    })
    toast.success('Đặt lại mật khẩu thành công')
    router.push('/login')
  } catch (e) {
    toast.error(e?.message || 'Không đặt lại được')
  }
})
</script>

<template>
  <div class="reset-root relative">
    <div
      class="pointer-events-none absolute inset-x-0 -top-8 h-40 bg-gradient-to-b from-sky-500/[0.07] via-brand-500/[0.06] to-transparent blur-2xl"
      aria-hidden="true"
    />

    <div class="relative text-center">
      <div
        class="reset-brand mx-auto flex max-w-xs animate-fade-up flex-col items-center justify-center gap-3 sm:max-w-sm sm:flex-row sm:gap-4"
      >
        <div
          class="reset-mark flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500/14 to-brand-500/12 p-2.5 shadow-sm ring-1 ring-sky-400/25"
        >
          <img :src="logoMark" alt="" class="h-full w-full object-contain" width="48" height="48" />
        </div>
        <img
          :src="logoText"
          alt="Tri Thức Số"
          class="h-9 w-auto max-w-[min(100%,220px)] object-contain object-left sm:h-10"
        />
      </div>

      <div
        class="reset-hero-icon mx-auto mt-5 flex h-12 w-12 animate-fade-up items-center justify-center rounded-full bg-sky-100/95 text-sky-800 ring-8 ring-sky-500/[0.08]"
        style="animation-delay: 45ms; animation-fill-mode: backwards"
        aria-hidden="true"
      >
        <Icon icon="mdi:shield-key-outline" class="h-7 w-7" />
      </div>

      <h1
        class="font-display mt-5 animate-fade-up text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl"
        style="animation-delay: 75ms; animation-fill-mode: backwards"
      >
        Đặt lại mật khẩu
      </h1>
      <p
        class="mx-auto mt-2 max-w-md animate-fade-up text-sm leading-relaxed text-ink-500"
        style="animation-delay: 115ms; animation-fill-mode: backwards"
      >
        Chọn mật khẩu mới mạnh, không trùng mật khẩu cũ. Sau khi lưu xong hãy đăng nhập lại trên thiết bị tin cậy.
      </p>
    </div>

    <div
      class="email-card mx-auto mt-8 max-w-md animate-fade-up rounded-xl border border-slate-200/90 bg-white/95 px-4 py-3 text-left shadow-sm ring-1 ring-slate-100"
      style="animation-delay: 140ms; animation-fill-mode: backwards"
    >
      <div class="flex items-start gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
          <Icon icon="mdi:email-outline" class="h-5 w-5" />
        </span>
        <div class="min-w-0 flex-1">
          <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
            Áp dụng cho email
          </p>
          <p class="mt-1 break-all font-medium text-ink-900">
            {{ email || '—' }}
          </p>
          <p
            v-if="!canReset && !email"
            class="mt-2 text-[13px] text-amber-800"
          >
            Thiếu email trong liên kết — không thể tiếp tục.
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="!canReset"
      class="reset-invalid-banner relative mx-auto mt-5 max-w-md animate-fade-up rounded-xl border border-amber-200/90 bg-gradient-to-br from-amber-50 to-orange-50/50 px-4 py-4 text-left text-[13px] leading-relaxed text-amber-950 shadow-sm ring-1 ring-amber-200/60"
      style="animation-delay: 165ms; animation-fill-mode: backwards"
      role="alert"
    >
      <div class="flex gap-3">
        <Icon icon="mdi:alert-circle-outline" class="mt-0.5 h-6 w-6 shrink-0 text-amber-700" />
        <div class="space-y-3">
          <p class="font-medium">
            Liên kết không đầy đủ hoặc đã hết hạn. Vui lòng yêu cầu gửi lại email hoặc mở đúng liên kết từ hộp thư.
          </p>
          <div class="flex flex-wrap gap-2">
            <RouterLink
              to="/forgot-password"
              class="inline-flex items-center rounded-lg bg-amber-600 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-amber-700"
            >
              Gửi lại email
            </RouterLink>
            <RouterLink
              to="/login"
              class="inline-flex items-center rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-900 transition hover:bg-amber-50"
            >
              Về đăng nhập
            </RouterLink>
          </div>
        </div>
      </div>
    </div>

    <form
      class="relative mt-8 space-y-5 animate-fade-up"
      style="animation-delay: 175ms; animation-fill-mode: backwards"
      :class="{ 'pointer-events-none opacity-55': !canReset }"
      @submit.prevent="onSubmit"
    >
      <div
        v-if="canReset"
        class="flex gap-3 rounded-xl border border-emerald-200/70 bg-emerald-50/60 px-3.5 py-3 text-left text-[13px] leading-snug text-emerald-950/90 ring-1 ring-emerald-200/50"
      >
        <Icon icon="mdi:check-decagram-outline" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-700" />
        <span>
          Liên kết có token hợp lệ trong thời gian quy định. Sau khi lưu, mật khẩu cũ không còn hiệu lực.
        </span>
      </div>

      <fieldset
        class="space-y-4 rounded-xl border border-slate-100/90 bg-gradient-to-br from-slate-50/90 to-white p-4 shadow-inner sm:p-5"
      >
        <legend class="sr-only">Mật khẩu mới</legend>

        <div class="reset-field group">
          <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
            <Icon
              icon="mdi:lock-plus-outline"
              class="h-4 w-4 text-slate-400 transition group-focus-within:text-sky-600"
            />
            Mật khẩu mới
          </label>
          <Password
            v-model="password"
            v-bind="paAttrs"
            class="reset-password-wrap w-full"
            input-class="reset-input-el"
            fluid
            toggle-mask
            :input-props="{ autocomplete: 'new-password', disabled: !canReset }"
          />
          <p class="mt-1 text-[11px] text-ink-400">Tối thiểu 8 ký tự.</p>
          <p v-if="errors.password" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
            <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
            {{ errors.password }}
          </p>
        </div>

        <div class="reset-field group">
          <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
            <Icon
              icon="mdi:lock-check-outline"
              class="h-4 w-4 text-slate-400 transition group-focus-within:text-sky-600"
            />
            Xác nhận mật khẩu
          </label>
          <Password
            v-model="password_confirmation"
            v-bind="pcAttrs"
            class="reset-password-wrap w-full"
            input-class="reset-input-el"
            fluid
            toggle-mask
            :feedback="false"
            :input-props="{ autocomplete: 'new-password', disabled: !canReset }"
          />
          <p v-if="errors.password_confirmation" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
            <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
            {{ errors.password_confirmation }}
          </p>
        </div>
      </fieldset>

      <Button
        type="submit"
        label="Lưu mật khẩu mới"
        icon="pi pi-check"
        class="reset-submit w-full"
        :loading="isSubmitting"
        :disabled="!canReset"
      />

      <RouterLink
        to="/login"
        class="flex items-center justify-center gap-2 border-t border-slate-100 pt-5 text-sm font-medium text-ink-600 transition hover:text-brand-800"
      >
        <Icon icon="mdi:arrow-left" class="h-4 w-4" />
        Quay lại đăng nhập
      </RouterLink>
    </form>
  </div>
</template>

<style scoped>
.reset-root :deep(.reset-input-el),
.reset-root :deep(.p-inputtext) {
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
  padding-top: 0.65rem;
  padding-bottom: 0.65rem;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.reset-root :deep(.reset-input-el:focus:not(:disabled)),
.reset-root :deep(.p-inputtext:focus:not(:disabled)),
.reset-root :deep(.p-password .p-inputtext:focus:not(:disabled)) {
  border-color: rgb(14 165 233 / 0.55);
  box-shadow:
    0 0 0 3px rgb(14 165 233 / 0.14),
    0 1px 2px rgb(15 23 42 / 0.05);
}

.reset-password-wrap :deep(.p-password),
.reset-password-wrap :deep(.p-password-input-wrapper) {
  width: 100%;
}

.reset-password-wrap :deep(.p-password .p-inputtext) {
  width: 100%;
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
}

.reset-submit {
  border-radius: 0.75rem !important;
  padding-top: 0.85rem !important;
  padding-bottom: 0.85rem !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 16px -4px rgb(14 165 233 / 0.4);
  transition:
    transform 0.18s ease,
    box-shadow 0.2s ease;
}

.reset-submit:hover:not(:disabled) {
  box-shadow: 0 8px 26px -6px rgb(14 165 233 / 0.42);
}

.reset-submit:active:not(:disabled) {
  transform: scale(0.985);
}

@media (prefers-reduced-motion: reduce) {
  .reset-root .animate-fade-up {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  .reset-submit {
    transition: none;
  }

  .reset-submit:active:not(:disabled) {
    transform: none;
  }
}
</style>
