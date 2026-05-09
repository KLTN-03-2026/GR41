<script setup>
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import { Icon } from '@iconify/vue'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { loginSchema } from '@/utils/validators'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import logoMark from '@/assets/images/logo.png'
import logoText from '@/assets/images/logo-text.png'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()
const toast = useToast()

const { defineField, handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: toTypedSchema(loginSchema),
  initialValues: { email: '', password: '' },
})

const [email, emailAttrs] = defineField('email')
const [password, passwordAttrs] = defineField('password')

const onSubmit = handleSubmit(async (values) => {
  try {
    await auth.login(values.email, values.password)
    toast.success('Đăng nhập thành công')
    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : '/'
    router.push(redirect)
  } catch (e) {
    toast.error(e?.errors?.email?.[0] || e?.message || 'Đăng nhập thất bại')
  }
})
</script>

<template>
  <div class="login-root relative">
    <div
      class="pointer-events-none absolute inset-x-0 -top-8 h-40 bg-gradient-to-b from-brand-500/[0.07] to-transparent blur-2xl"
      aria-hidden="true"
    />

    <div class="relative text-center">
      <div
        class="login-brand mx-auto flex max-w-xs animate-fade-up flex-col items-center justify-center gap-3 sm:max-w-sm sm:flex-row sm:gap-4"
      >
        <div
          class="login-mark flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/12 to-indigo-500/10 p-2.5 shadow-sm ring-1 ring-brand-500/15"
        >
          <img :src="logoMark" alt="" class="h-full w-full object-contain" width="48" height="48" />
        </div>
        <img
          :src="logoText"
          alt="Tri Thức Số"
          class="h-9 w-auto max-w-[min(100%,220px)] object-contain object-left sm:h-10"
        />
      </div>

      <h1
        class="font-display mt-8 animate-fade-up text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl"
        style="animation-delay: 60ms; animation-fill-mode: backwards"
      >
        Đăng nhập
      </h1>
      <p
        class="mx-auto mt-2 max-w-sm animate-fade-up text-sm leading-relaxed text-ink-500"
        style="animation-delay: 110ms; animation-fill-mode: backwards"
      >
        Chào mừng trở lại — Đăng nhập để đồng bộ yêu thích, lịch sử và trải nghiệm cá nhân.
      </p>
    </div>

    <form
      class="relative mt-10 space-y-5 animate-fade-up"
      style="animation-delay: 160ms; animation-fill-mode: backwards"
      @submit.prevent="onSubmit"
    >
      <div class="login-field group">
        <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
          <Icon
            icon="mdi:email-outline"
            class="h-4 w-4 text-slate-400 transition group-focus-within:text-brand-600"
          />
          Email
        </label>
        <InputText
          v-model="email"
          v-bind="emailAttrs"
          type="email"
          placeholder="your@email.com"
          autocomplete="username"
          class="login-input-el w-full"
          fluid
        />
        <p v-if="errors.email" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
          <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
          {{ errors.email }}
        </p>
      </div>

      <div class="login-field group">
        <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
          <Icon
            icon="mdi:lock-outline"
            class="h-4 w-4 text-slate-400 transition group-focus-within:text-brand-600"
          />
          Mật khẩu
        </label>
        <Password
          v-model="password"
          v-bind="passwordAttrs"
          class="login-password-wrap w-full"
          input-class="login-input-el"
          fluid
          toggle-mask
          :feedback="false"
          :input-props="{ autocomplete: 'current-password' }"
        />
        <p v-if="errors.password" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
          <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
          {{ errors.password }}
        </p>
      </div>

      <div class="flex items-center justify-end pt-1">
        <RouterLink
          to="/forgot-password"
          class="group/link inline-flex items-center gap-1 text-sm font-medium text-brand-600 transition hover:text-brand-800"
        >
          Quên mật khẩu?
          <Icon
            icon="mdi:chevron-right"
            class="h-4 w-4 transition group-hover/link:translate-x-0.5"
          />
        </RouterLink>
      </div>

      <Button
        type="submit"
        label="Đăng nhập"
        icon="pi pi-sign-in"
        class="login-submit w-full"
        :loading="isSubmitting"
      />

      <p
        class="border-t border-slate-100 pt-6 text-center text-sm text-ink-500"
      >
        Chưa có tài khoản?
        <RouterLink
          to="/register"
          class="font-semibold text-brand-700 underline-offset-4 transition hover:text-brand-900 hover:underline"
        >
          Đăng ký miễn phí
        </RouterLink>
      </p>
    </form>
  </div>
</template>

<style scoped>
.login-root :deep(.login-input-el),
.login-root :deep(.p-inputtext) {
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
  padding-top: 0.65rem;
  padding-bottom: 0.65rem;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.login-root :deep(.login-input-el:focus),
.login-root :deep(.p-inputtext:focus),
.login-root :deep(.p-password .p-inputtext:focus) {
  border-color: rgb(147 197 253 / 0.9);
  box-shadow:
    0 0 0 3px rgb(59 130 246 / 0.15),
    0 1px 2px rgb(15 23 42 / 0.05);
}

.login-password-wrap :deep(.p-password),
.login-password-wrap :deep(.p-password-input-wrapper) {
  width: 100%;
}

.login-password-wrap :deep(.p-password .p-password-input-wrapper .p-password-input),
.login-password-wrap :deep(.p-password .p-inputtext) {
  width: 100%;
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
  padding-top: 0.65rem;
  padding-bottom: 0.65rem;
}

.login-submit {
  border-radius: 0.75rem !important;
  padding-top: 0.85rem !important;
  padding-bottom: 0.85rem !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 14px -4px rgb(37 99 235 / 0.45);
  transition:
    transform 0.18s ease,
    box-shadow 0.2s ease;
}

.login-submit:hover:not(:disabled) {
  box-shadow: 0 8px 22px -6px rgb(37 99 235 / 0.5);
}

.login-submit:active:not(:disabled) {
  transform: scale(0.985);
}

@media (prefers-reduced-motion: reduce) {
  .login-brand,
  h1,
  form,
  form + p {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  .login-submit {
    transition: none;
  }

  .login-submit:active:not(:disabled) {
    transform: none;
  }
}
</style>
