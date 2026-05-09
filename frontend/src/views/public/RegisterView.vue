<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import { Icon } from '@iconify/vue'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import { registerSchema } from '@/utils/validators'
import { useAuthStore } from '@/stores/auth'
import { useToast } from '@/composables/useToast'
import logoMark from '@/assets/images/logo.png'
import logoText from '@/assets/images/logo-text.png'

const router = useRouter()
const auth = useAuthStore()
const toast = useToast()

const { defineField, handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: toTypedSchema(registerSchema),
  initialValues: {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    student_code: '',
  },
})

const [name, nameAttrs] = defineField('name')
const [email, emailAttrs] = defineField('email')
const [password, passwordAttrs] = defineField('password')
const [password_confirmation, pcAttrs] = defineField('password_confirmation')
const [phone, phoneAttrs] = defineField('phone')
const [student_code, scAttrs] = defineField('student_code')

const onSubmit = handleSubmit(async (values) => {
  try {
    await auth.register(values)
    toast.success('Đăng ký thành công')
    router.push('/')
  } catch (e) {
    toast.error(e?.errors?.email?.[0] === 'The email has already been taken.' ? 'Email đã được sử dụng' : e?.message || 'Đăng ký thất bại')
  }
})
</script>

<template>
  <div class="register-root relative">
    <div
      class="pointer-events-none absolute inset-x-0 -top-8 h-44 bg-gradient-to-b from-emerald-500/[0.07] via-brand-500/[0.06] to-transparent blur-2xl"
      aria-hidden="true"
    />

    <div class="relative text-center">
      <div
        class="register-brand mx-auto flex max-w-xs animate-fade-up flex-col items-center justify-center gap-3 sm:max-w-sm sm:flex-row sm:gap-4"
      >
        
        <img
          :src="logoText"
          alt="Tri Thức Số"
          class="h-9 w-auto max-w-[min(100%,220px)] object-contain object-left sm:h-10"
        />
      </div>

      <div
        class="register-hero-icon mx-auto mt-5 flex h-12 w-12 animate-fade-up items-center justify-center rounded-full bg-emerald-100/95 text-emerald-700 ring-8 ring-emerald-600/[0.07]"
        style="animation-delay: 45ms; animation-fill-mode: backwards"
        aria-hidden="true"
      >
        <Icon icon="mdi:account-plus-outline" class="h-7 w-7" />
      </div>

      <h1
        class="font-display mt-5 animate-fade-up text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl"
        style="animation-delay: 70ms; animation-fill-mode: backwards"
      >
        Tạo tài khoản
      </h1>
      <p
        class="mx-auto mt-2 max-w-md animate-fade-up text-sm leading-relaxed text-ink-500"
        style="animation-delay: 115ms; animation-fill-mode: backwards"
      >
        Tham gia Tri Thức Số để lưu yêu thích, nhận gợi ý và tải tài liệu khi được phép.
      </p>
    </div>

    <form
      class="register-form-scroll relative mt-8 max-h-[min(68vh,calc(100dvh-200px))] space-y-6 overflow-y-auto pr-1 animate-fade-up sm:max-h-none sm:overflow-visible md:space-y-7"
      style="animation-delay: 165ms; animation-fill-mode: backwards"
      @submit.prevent="onSubmit"
    >
      <!-- Thông tin cơ bản -->
      <fieldset class="space-y-4 border-0 p-0">
        <legend
          class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-ink-400"
        >
          <Icon icon="mdi:card-account-details-outline" class="h-4 w-4 text-emerald-600" />
          Thông tin cơ bản
        </legend>
        <div class="register-field group">
          <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
            <Icon
              icon="mdi:account-outline"
              class="h-4 w-4 text-slate-400 transition group-focus-within:text-emerald-600"
            />
            Họ và tên
          </label>
          <InputText
            v-model="name"
            v-bind="nameAttrs"
            placeholder="Nguyễn Văn A"
            autocomplete="name"
            class="reg-input-el w-full"
            fluid
          />
          <p v-if="errors.name" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
            <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
            {{ errors.name }}
          </p>
        </div>
        <div class="register-field group">
          <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
            <Icon
              icon="mdi:email-outline"
              class="h-4 w-4 text-slate-400 transition group-focus-within:text-emerald-600"
            />
            Email đăng ký
          </label>
          <InputText
            v-model="email"
            v-bind="emailAttrs"
            type="email"
            placeholder="your@email.com"
            autocomplete="email"
            class="reg-input-el w-full"
            fluid
          />
          <p v-if="errors.email" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
            <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
            {{ errors.email }}
          </p>
        </div>
      </fieldset>

      <!-- Mật khẩu -->
      <fieldset class="space-y-4 rounded-xl border border-slate-100/90 bg-gradient-to-br from-slate-50/80 to-white p-4 shadow-inner sm:p-5">
        <legend
          class="mb-1 flex items-center gap-2 px-1 text-[11px] font-bold uppercase tracking-wider text-ink-400"
        >
          <Icon icon="mdi:shield-lock-outline" class="h-4 w-4 text-brand-600" />
          Bảo mật tài khoản
        </legend>
        <div class="grid gap-5 sm:grid-cols-2 sm:gap-4">
          <div class="register-field group">
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
              class="reg-password-wrap w-full"
              input-class="reg-input-el"
              fluid
              toggle-mask
              :input-props="{ autocomplete: 'new-password' }"
            />
            <p class="mt-1.5 text-[11px] text-ink-400">Ít nhất 8 ký tự.</p>
            <p v-if="errors.password" class="mt-1 flex items-center gap-1 text-xs font-medium text-red-600">
              <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
              {{ errors.password }}
            </p>
          </div>
          <div class="register-field group">
            <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
              <Icon
                icon="mdi:lock-check-outline"
                class="h-4 w-4 text-slate-400 transition group-focus-within:text-brand-600"
              />
              Xác nhận mật khẩu
            </label>
            <Password
              v-model="password_confirmation"
              v-bind="pcAttrs"
              class="reg-password-wrap w-full"
              input-class="reg-input-el"
              fluid
              toggle-mask
              :feedback="false"
              :input-props="{ autocomplete: 'new-password' }"
            />
            <p v-if="errors.password_confirmation" class="mt-1.5 flex items-center gap-1 text-xs font-medium text-red-600">
              <Icon icon="mdi:alert-circle-outline" class="h-3.5 w-3.5 shrink-0" />
              {{ errors.password_confirmation }}
            </p>
          </div>
        </div>
      </fieldset>

      <!-- Tuỳ chọn -->
      <fieldset class="space-y-4 border-0 p-0">
        <legend
          class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-ink-400"
        >
          <Icon icon="mdi:information-outline" class="h-4 w-4 text-slate-500" />
          Thông tin thêm <span class="font-normal lowercase text-slate-400">(tuỳ chọn)</span>
        </legend>
        <div class="grid gap-5 sm:grid-cols-2 sm:gap-4">
          <div class="register-field group">
            <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
              <Icon
                icon="mdi:phone-outline"
                class="h-4 w-4 text-slate-400 transition group-focus-within:text-emerald-600"
              />
              Điện thoại
            </label>
            <InputText
              v-model="phone"
              v-bind="phoneAttrs"
              placeholder="092x xxx xxx"
              autocomplete="tel"
              class="reg-input-el w-full"
              fluid
            />
          </div>
          <div class="register-field group">
            <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
              <Icon
                icon="mdi:school-outline"
                class="h-4 w-4 text-slate-400 transition group-focus-within:text-emerald-600"
              />
              Mã sinh viên
            </label>
            <InputText
              v-model="student_code"
              v-bind="scAttrs"
              placeholder="VD: DH12345"
              class="reg-input-el w-full"
              fluid
            />
          </div>
        </div>
      </fieldset>

      <Button
        type="submit"
        label="Đăng ký"
        icon="pi pi-user-plus"
        class="register-submit w-full"
        :loading="isSubmitting"
      />

      <p
        class="border-t border-slate-100 pb-1 pt-5 text-center text-sm text-ink-500 sm:pb-0"
      >
        Đã có tài khoản?
        <RouterLink
          to="/login"
          class="font-semibold text-brand-700 underline-offset-4 transition hover:text-brand-900 hover:underline"
        >
          Đăng nhập
        </RouterLink>
      </p>
    </form>
  </div>
</template>

<style scoped>
.register-form-scroll {
  scrollbar-gutter: stable;
  scrollbar-width: thin;
  scrollbar-color: rgb(148 163 184 / 0.5) transparent;
}

.register-form-scroll::-webkit-scrollbar {
  width: 6px;
}

.register-form-scroll::-webkit-scrollbar-thumb {
  border-radius: 999px;
  background-color: rgb(148 163 184 / 0.45);
}

.register-form-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.register-root :deep(.reg-input-el),
.register-root :deep(.p-inputtext) {
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
  padding-top: 0.6rem;
  padding-bottom: 0.6rem;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.register-root :deep(.reg-input-el:focus),
.register-root :deep(.p-inputtext:focus),
.register-root :deep(.p-password .p-inputtext:focus) {
  border-color: rgb(16 185 129 / 0.45);
  box-shadow:
    0 0 0 3px rgb(16 185 129 / 0.12),
    0 1px 2px rgb(15 23 42 / 0.05);
}

.reg-password-wrap :deep(.p-password),
.reg-password-wrap :deep(.p-password-input-wrapper) {
  width: 100%;
}

.reg-password-wrap :deep(.p-password .p-inputtext) {
  width: 100%;
  border-radius: 0.75rem;
  border-color: rgb(226 232 240);
  padding-top: 0.6rem;
  padding-bottom: 0.6rem;
}

.register-submit {
  border-radius: 0.75rem !important;
  padding-top: 0.85rem !important;
  padding-bottom: 0.85rem !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 16px -4px rgb(5 150 105 / 0.38);
  transition:
    transform 0.18s ease,
    box-shadow 0.2s ease;
}

.register-submit:hover:not(:disabled) {
  box-shadow: 0 8px 26px -6px rgb(5 150 105 / 0.42);
}

.register-submit:active:not(:disabled) {
  transform: scale(0.985);
}

@media (prefers-reduced-motion: reduce) {
  .register-brand,
  .register-hero-icon,
  h1,
  form {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  .register-submit {
    transition: none;
  }

  .register-submit:active:not(:disabled) {
    transform: none;
  }
}
</style>
