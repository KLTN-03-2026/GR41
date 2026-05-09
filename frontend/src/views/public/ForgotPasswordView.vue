<script setup>
import { useForm } from 'vee-validate'
import { object, string } from 'yup'
import { toTypedSchema } from '@vee-validate/yup'
import { Icon } from '@iconify/vue'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { RouterLink } from 'vue-router'
import { authService } from '@/services/authService'
import { isClientEmailConfigured, sendPasswordResetEmail } from '@/services/sendMailService'
import { useToast } from '@/composables/useToast'
import logoMark from '@/assets/images/logo.png'
import logoText from '@/assets/images/logo-text.png'

const toast = useToast()

const schema = object({
  email: string().email('Email không hợp lệ').required('Bắt buộc'),
})

const { defineField, handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: toTypedSchema(schema),
  initialValues: { email: '' },
})

const [email, emailAttrs] = defineField('email')

const onSubmit = handleSubmit(async (values) => {
  try {
    const data = await authService.forgotPassword(values.email)
    const cm = data?.clientMail

    if (cm?.email && cm?.token) {
      if (!String(import.meta.env.VITE_APP_URL || '').trim()) {
        toast.error('Thiếu VITE_APP_URL trong .env — không tạo được liên kết đặt lại mật khẩu.')
        return
      }
      if (!isClientEmailConfigured()) {
        toast.error(
          'Chưa cấu hình EmailJS (VITE_EMAILJS_* trong .env). Không thể gửi email — liên hệ quản trị.',
        )
        return
      }
      const sent = await sendPasswordResetEmail(cm.email, { email: cm.email, token: cm.token })
      if (!sent) {
        toast.error('Máy chủ đã xử lý yêu cầu nhưng gửi email qua EmailJS thất bại. Thử lại sau.')
        return
      }
    }

    toast.success(
      'Nếu email đã đăng ký, bạn sẽ nhận được liên kết đặt lại mật khẩu (kiểm tra hộp thư và spam).',
    )
  } catch (e) {
    toast.error(e?.message || 'Không gửi được yêu cầu')
  }
})
</script>

<template>
  <div class="forgot-root relative">
    <div
      class="pointer-events-none absolute inset-x-0 -top-8 h-40 bg-gradient-to-b from-violet-500/[0.08] via-brand-500/[0.05] to-transparent blur-2xl"
      aria-hidden="true"
    />

    <div class="relative text-center">
      <div
        class="forgot-brand mx-auto flex max-w-xs animate-fade-up flex-col items-center justify-center gap-3 sm:max-w-sm sm:flex-row sm:gap-4"
      >
        <div
          class="forgot-mark flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500/12 to-brand-500/10 p-2.5 shadow-sm ring-1 ring-violet-400/20"
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
        class="forgot-hero-icon mx-auto mt-6 flex h-12 w-12 animate-fade-up items-center justify-center rounded-full bg-violet-100/90 text-violet-700 ring-8 ring-violet-500/[0.06]"
        style="animation-delay: 45ms; animation-fill-mode: backwards"
        aria-hidden="true"
      >
        <Icon icon="mdi:lock-reset" class="h-7 w-7" />
      </div>

      <h1
        class="font-display mt-5 animate-fade-up text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl"
        style="animation-delay: 70ms; animation-fill-mode: backwards"
      >
        Quên mật khẩu?
      </h1>
      <p
        class="mx-auto mt-2 max-w-sm animate-fade-up text-sm leading-relaxed text-ink-500"
        style="animation-delay: 120ms; animation-fill-mode: backwards"
      >
        Nhập email đã đăng ký — chúng tôi gửi liên kết đặt lại mật khẩu (có hiệu lực trong thời gian giới hạn).
      </p>
    </div>

    <form
      class="relative mt-9 space-y-5 animate-fade-up"
      style="animation-delay: 170ms; animation-fill-mode: backwards"
      @submit.prevent="onSubmit"
    >
      <div
        class="flex gap-3 rounded-xl border border-amber-200/70 bg-gradient-to-br from-amber-50/90 to-orange-50/40 px-3.5 py-3 text-left text-[13px] leading-snug text-amber-950/85 ring-1 ring-amber-200/40"
      >
        <Icon icon="mdi:lightbulb-on-outline" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" />
        <span>
          Dùng đúng email tài khoản Tri Thức Số. Sau khi gửi, kiểm tra cả <strong>hòm spam</strong> / Thư quảng cáo.
        </span>
      </div>

      <div class="forgot-field group">
        <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
          <Icon
            icon="mdi:email-outline"
            class="h-4 w-4 text-slate-400 transition group-focus-within:text-violet-600"
          />
          Email đăng ký
        </label>
        <InputText
          v-model="email"
          v-bind="emailAttrs"
          type="email"
          placeholder="your@email.com"
          autocomplete="email"
          class="forgot-input-el w-full"
          fluid
        />
        <p v-if="errors.email" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
          <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
          {{ errors.email }}
        </p>
      </div>

      <Button
        type="submit"
        label="Gửi email khôi phục"
        icon="pi pi-send"
        class="forgot-submit w-full"
        :loading="isSubmitting"
      />

      <RouterLink
        to="/login"
        class="group/back flex items-center justify-center gap-2 border-t border-slate-100 pt-6 text-sm font-medium text-ink-600 transition hover:text-brand-800"
      >
        <Icon
          icon="mdi:arrow-left"
          class="h-4 w-4 transition group-hover/back:-translate-x-0.5"
        />
        Quay lại đăng nhập
      </RouterLink>
    </form>
  </div>
</template>

<style scoped>
.forgot-root :deep(.forgot-input-el),
.forgot-root :deep(.p-inputtext) {
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
  padding-top: 0.65rem;
  padding-bottom: 0.65rem;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.forgot-root :deep(.forgot-input-el:focus),
.forgot-root :deep(.p-inputtext:focus) {
  border-color: rgb(167 139 250 / 0.85);
  box-shadow:
    0 0 0 3px rgb(139 92 246 / 0.14),
    0 1px 2px rgb(15 23 42 / 0.05);
}

.forgot-submit {
  border-radius: 0.75rem !important;
  padding-top: 0.85rem !important;
  padding-bottom: 0.85rem !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 14px -4px rgb(109 40 217 / 0.4);
  transition:
    transform 0.18s ease,
    box-shadow 0.2s ease;
}

.forgot-submit:hover:not(:disabled) {
  box-shadow: 0 8px 22px -6px rgb(109 40 217 / 0.45);
}

.forgot-submit:active:not(:disabled) {
  transform: scale(0.985);
}

@media (prefers-reduced-motion: reduce) {
  .forgot-brand,
  .forgot-hero-icon,
  h1,
  form {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  .forgot-submit {
    transition: none;
  }

  .forgot-submit:active:not(:disabled) {
    transform: none;
  }
}
</style>
