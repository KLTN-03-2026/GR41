<script setup>
import { computed, ref, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query'
import { Icon } from '@iconify/vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import ImageUploader from '@/components/common/ImageUploader.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { profileService } from '@/services/profileService'
import { useToast } from '@/composables/useToast'
import { useAuthStore } from '@/stores/auth'

const toast = useToast()
const auth = useAuthStore()
const qc = useQueryClient()

const { data: profile, isLoading } = useQuery({
  queryKey: ['profile'],
  queryFn: profileService.getProfile,
})

const form = ref({ name: '', phone: '', student_code: '' })

watch(
  profile,
  (p) => {
    if (!p) return
    form.value = {
      name: p.name ?? auth.user?.name ?? '',
      phone: p.phone ?? '',
      student_code: p.student_code ?? '',
    }
  },
  { immediate: true },
)

const displayEmail = computed(
  () => profile.value?.email ?? auth.user?.email ?? '',
)

const updateMutation = useMutation({
  mutationFn: (payload) => profileService.updateProfile(payload),
  onSuccess: async () => {
    toast.success('Đã lưu hồ sơ')
    await qc.invalidateQueries({ queryKey: ['profile'] })
    await auth.fetchMe()
  },
  onError: (e) => toast.error(e?.message || 'Lỗi'),
})

const avatarMutation = useMutation({
  mutationFn: (url) => profileService.updateAvatar({ avatar: url }),
  onSuccess: async () => {
    toast.success('Đã cập nhật ảnh đại diện')
    await qc.invalidateQueries({ queryKey: ['profile'] })
    await auth.fetchMe()
  },
  onError: (e) => toast.error(e?.message || 'Lỗi upload'),
})

const updatePending = updateMutation.isPending
const avatarPending = avatarMutation.isPending

function onSubmit() {
  updateMutation.mutate({ ...form.value })
}

function onAvatarUploaded({ url }) {
  avatarMutation.mutate(url)
}
</script>

<template>
  <div class="relative min-h-[70vh] overflow-hidden bg-surface pb-20 pt-8 sm:pt-12">
    <div class="pointer-events-none absolute inset-0 opacity-[0.42]" aria-hidden="true">
      <div
        class="absolute -left-1/4 top-0 h-[400px] w-[560px] rounded-full bg-brand-400/22 blur-[100px]"
      />
      <div
        class="absolute -right-1/4 top-28 h-[360px] w-[460px] rounded-full bg-violet-400/18 blur-[88px]"
      />
      <div
        class="absolute bottom-0 left-1/4 h-56 w-[65%] rounded-full bg-cyan-500/12 blur-[72px]"
      />
    </div>

    <div class="relative mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <header
        class="animate-fade-up overflow-hidden rounded-2xl border border-white/60 bg-white/80 shadow-lift backdrop-blur-md"
      >
        <div
          class="relative bg-gradient-to-br from-brand-600/12 via-white to-violet-50/70 px-6 py-9 sm:px-10 sm:py-11"
        >
          <div
            class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-brand-500/12 blur-3xl"
          />
          <div
            class="pointer-events-none absolute bottom-0 right-1/4 h-28 w-40 rounded-full bg-violet-500/10 blur-2xl"
          />
          <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-xl">
              <p
                class="mb-2 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-brand-700/90"
              >
                <span
                  class="inline-flex h-7 w-7 items-center justify-center rounded-xl bg-brand-500/15 ring-1 ring-brand-500/20"
                >
                  <Icon icon="mdi:account-circle-outline" class="h-[18px] w-[18px] text-brand-600" />
                </span>
                Tài khoản
              </p>
              <h1 class="font-display text-3xl font-bold tracking-tight text-ink-900 sm:text-4xl">
                Hồ sơ cá nhân
              </h1>
              <p class="mt-2 max-w-lg text-sm leading-relaxed text-ink-500 sm:text-[15px]">
                Cập nhật thông tin hiển thị và ảnh đại diện. Email đăng nhập không đổi tại đây — chỉ có thể
                được quản trị viên chỉnh sửa trong hệ thống backend.
              </p>
            </div>
            <RouterLink
              :to="{ name: 'change-password' }"
              class="group inline-flex items-center gap-2 self-start rounded-xl border border-slate-200/90 bg-white/95 px-4 py-3 text-sm font-medium text-slate-700 shadow-soft transition hover:border-brand-300 hover:bg-brand-50/90 hover:text-brand-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 lg:self-end"
            >
              <Icon
                icon="mdi:shield-key-outline"
                class="h-5 w-5 text-slate-500 transition group-hover:text-brand-600"
              />
              Đổi mật khẩu
              <Icon
                icon="mdi:chevron-right"
                class="h-5 w-5 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-brand-600"
              />
            </RouterLink>
          </div>
        </div>
      </header>

      <!-- Loading -->
      <div
        v-if="isLoading"
        class="mt-10 flex min-h-[320px] items-center justify-center rounded-2xl border border-white/70 bg-white/70 shadow-soft backdrop-blur-sm"
      >
        <LoadingSpinner />
      </div>

      <!-- Content -->
      <div v-else class="mt-10 grid gap-8 lg:grid-cols-12 lg:gap-10">
        <!-- Avatar -->
        <section
          class="animate-fade-up lg:col-span-5"
          style="animation-delay: 0ms; animation-fill-mode: backwards"
        >
          <div
            class="profile-glass relative overflow-hidden rounded-2xl border border-white/70 bg-white/90 p-6 shadow-lift sm:p-8"
          >
            <div
              class="pointer-events-none absolute -right-6 top-0 h-32 w-32 rounded-full bg-gradient-to-br from-brand-400/20 to-transparent blur-2xl"
            />
            <h2 class="font-display text-lg font-semibold text-ink-900">Ảnh đại diện</h2>
            <p class="mt-1 text-sm text-ink-500">
              Ảnh hiển thị trên tài liệu và giao diện cá nhân.
            </p>
            <div
              class="relative mt-6 flex justify-center"
              :class="{ 'pointer-events-none opacity-80': avatarPending }"
            >
              <div
                class="profile-avatar-frame rounded-full p-1 transition duration-300 ease-out"
              >
                <ImageUploader
                  :model-value="profile?.avatar || auth.user?.avatar || ''"
                  label=""
                  aspect="avatar"
                  accept="image/*"
                  @uploaded="onAvatarUploaded"
                />
              </div>
              <div
                v-if="avatarPending"
                class="absolute inset-0 flex items-center justify-center rounded-full bg-white/60 backdrop-blur-[2px]"
              >
                <Icon icon="mdi:loading" class="h-10 w-10 animate-spin text-brand-600" />
              </div>
            </div>
            <p v-if="avatarPending" class="mt-4 text-center text-xs font-medium text-brand-700">
              Đang cập nhật ảnh…
            </p>
          </div>
        </section>

        <!-- Form -->
        <section
          class="animate-fade-up lg:col-span-7"
          style="animation-delay: 90ms; animation-fill-mode: backwards"
        >
          <div class="profile-glass rounded-2xl border border-white/70 bg-white/90 p-6 shadow-lift sm:p-8">
            <h2 class="font-display text-lg font-semibold text-ink-900">Thông tin liên hệ</h2>
            <p class="mt-1 text-sm text-ink-500">
              Chỉnh các trường bên dưới rồi nhấn lưu để đồng bộ trên toàn hệ thống.
            </p>

            <div
              class="mt-6 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3 text-sm shadow-inner"
            >
              <div class="flex items-start gap-3">
                <span
                  class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm ring-1 ring-slate-200/80"
                >
                  <Icon icon="mdi:email-outline" class="h-5 w-5" />
                </span>
                <div class="min-w-0 flex-1">
                  <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Email đăng nhập</p>
                  <p class="mt-0.5 truncate font-medium text-ink-800">{{ displayEmail }}</p>
                </div>
              </div>
            </div>

            <form class="mt-8 space-y-5" @submit.prevent="onSubmit">
              <div class="profile-field group">
                <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
                  <Icon icon="mdi:account-outline" class="h-4 w-4 text-slate-400 group-focus-within:text-brand-600" />
                  Họ tên
                </label>
                <InputText
                  v-model="form.name"
                  class="w-full rounded-xl border-slate-200 transition-shadow focus-within:shadow-md"
                  fluid
                  placeholder="Nhập họ tên đầy đủ"
                />
              </div>
              <div class="profile-field group">
                <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
                  <Icon icon="mdi:phone-outline" class="h-4 w-4 text-slate-400 group-focus-within:text-brand-600" />
                  Điện thoại
                </label>
                <InputText
                  v-model="form.phone"
                  class="w-full rounded-xl border-slate-200 transition-shadow focus-within:shadow-md"
                  fluid
                  placeholder="Số điện thoại liên hệ"
                />
              </div>
              <div class="profile-field group">
                <label class="mb-2 flex items-center gap-2 text-sm font-medium text-ink-700">
                  <Icon icon="mdi:card-account-details-outline" class="h-4 w-4 text-slate-400 group-focus-within:text-brand-600" />
                  Mã sinh viên
                </label>
                <InputText
                  v-model="form.student_code"
                  class="w-full rounded-xl border-slate-200 transition-shadow focus-within:shadow-md"
                  fluid
                  placeholder="Mã SV (nếu có)"
                />
              </div>

              <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 pt-6">
                <Button
                  type="submit"
                  label="Lưu thay đổi"
                  icon="pi pi-check"
                  :loading="updatePending"
                  class="min-w-[160px]"
                />
                <p class="text-xs text-slate-500">
                  <Icon icon="mdi:information-outline" class="mb-0.5 inline h-4 w-4 align-middle text-slate-400" />
                  Dữ liệu được lưu an toàn trên máy chủ.
                </p>
              </div>
            </form>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-glass {
  transition:
    box-shadow 0.35s cubic-bezier(0.22, 1, 0.36, 1),
    border-color 0.25s ease;
}

.profile-glass:hover {
  box-shadow:
    0 20px 50px -20px rgba(37, 99, 235, 0.15),
    0 0 0 1px rgba(37, 99, 235, 0.06);
}

.profile-avatar-frame {
  background: linear-gradient(
    135deg,
    rgba(37, 99, 235, 0.35) 0%,
    rgba(139, 92, 246, 0.25) 50%,
    rgba(37, 99, 235, 0.2) 100%
  );
  box-shadow: 0 12px 40px -12px rgba(37, 99, 235, 0.35);
}

@media (prefers-reduced-motion: reduce) {
  .animate-fade-up {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  header {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
  }

  .profile-glass:hover {
    box-shadow: inherit;
  }
}
</style>
