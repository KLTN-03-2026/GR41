<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Icon } from '@iconify/vue'
import logoMark from '@/assets/images/logo.png'

const email = ref('')
const newsletterOk = ref(false)

function submitNewsletter() {
  if (!email.value.trim()) return
  newsletterOk.value = true
  email.value = ''
  window.setTimeout(() => {
    newsletterOk.value = false
  }, 4000)
}

const footLink =
  'rounded-md text-ink-500 transition hover:text-brand-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400/50 focus-visible:ring-offset-2'
</script>

<template>
  <footer
    class="footer-surface relative mt-auto border-t border-ink-900/[0.06] bg-gradient-to-b from-slate-50/95 via-white to-white"
    role="contentinfo"
  >
    <div
      class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-500/20 to-transparent"
      aria-hidden="true"
    />

    <div class="mx-auto max-w-screen-2xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
      <div class="grid gap-12 md:grid-cols-2 lg:grid-cols-12 lg:gap-10 lg:gap-y-12">
        <!-- Cột thương hiệu -->
        <div class="lg:col-span-4">
          <RouterLink
            to="/"
            class="group inline-flex items-center gap-3 rounded-xl py-1 outline-none transition hover:opacity-[0.92] focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
          >
            <span
              class="footer-logo-ring flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-white to-brand-50/80 p-2 shadow-sm ring-1 ring-brand-200/55 transition group-hover:shadow-md group-hover:ring-brand-300/70"
            >
              <img
                :src="logoMark"
                alt=""
                class="h-full w-full object-contain"
                width="40"
                height="40"
                decoding="async"
              />
            </span>
            <span class="font-display text-xl font-bold tracking-tight text-ink-900 sm:text-[1.35rem]">
              Tri Thức Số
            </span>
          </RouterLink>

          <p class="mt-5 max-w-[22rem] text-sm leading-relaxed text-ink-500">
            Thư viện số tích hợp trợ lý AI — tra cứu tài liệu học tập nhanh chóng, mọi lúc.
          </p>

          <div class="mt-7 flex flex-wrap gap-2.5">
            <a
              href="https://facebook.com"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-ink-900/10 bg-white text-ink-500 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300/65 hover:bg-brand-600/[0.06] hover:text-brand-700 hover:shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 motion-reduce:hover:translate-y-0 motion-reduce:transition-none"
              aria-label="Facebook"
            >
              <Icon icon="mdi:facebook" class="h-[1.15rem] w-[1.15rem]" aria-hidden="true" />
            </a>
            <a
              href="https://github.com"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-ink-900/10 bg-white text-ink-500 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300/65 hover:bg-brand-600/[0.06] hover:text-brand-700 hover:shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 motion-reduce:hover:translate-y-0 motion-reduce:transition-none"
              aria-label="GitHub"
            >
              <Icon icon="mdi:github" class="h-[1.15rem] w-[1.15rem]" aria-hidden="true" />
            </a>
            <a
              href="https://youtube.com"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-ink-900/10 bg-white text-ink-500 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300/65 hover:bg-brand-600/[0.06] hover:text-brand-700 hover:shadow focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 motion-reduce:hover:translate-y-0 motion-reduce:transition-none"
              aria-label="YouTube"
            >
              <Icon icon="mdi:youtube" class="h-[1.15rem] w-[1.15rem]" aria-hidden="true" />
            </a>
          </div>
        </div>

        <!-- Liên kết -->
        <div class="grid grid-cols-2 gap-x-8 gap-y-10 sm:grid-cols-3 lg:col-span-5">
          <div>
            <p class="footer-col-title text-xs font-semibold uppercase tracking-wider text-ink-400">
              Khám phá
            </p>
            <ul class="mt-4 space-y-3 text-sm">
              <li>
                <RouterLink to="/search" :class="[footLink, 'inline-flex items-center gap-1.5']">
                  <Icon icon="mdi:magnify" class="h-4 w-4 shrink-0 text-brand-500/80" aria-hidden="true" />
                  Tìm kiếm
                </RouterLink>
              </li>
              <li>
                <RouterLink to="/" :class="[footLink, 'inline-flex items-center gap-1.5']">
                  <Icon icon="mdi:home-outline" class="h-4 w-4 shrink-0 text-brand-500/80" aria-hidden="true" />
                  Trang chủ
                </RouterLink>
              </li>
            </ul>
          </div>

          <div>
            <p class="footer-col-title text-xs font-semibold uppercase tracking-wider text-ink-400">
              Tài khoản
            </p>
            <ul class="mt-4 space-y-3 text-sm">
              <li>
                <RouterLink to="/login" :class="[footLink, 'inline-flex items-center gap-1.5']">
                  <Icon icon="mdi:login-variant" class="h-4 w-4 shrink-0 text-brand-500/80" aria-hidden="true" />
                  Đăng nhập
                </RouterLink>
              </li>
              <li>
                <RouterLink to="/register" :class="[footLink, 'inline-flex items-center gap-1.5']">
                  <Icon icon="mdi:account-plus-outline" class="h-4 w-4 shrink-0 text-brand-500/80" aria-hidden="true" />
                  Đăng ký
                </RouterLink>
              </li>
            </ul>
          </div>

          <div class="col-span-2 sm:col-span-1">
            <p class="footer-col-title text-xs font-semibold uppercase tracking-wider text-ink-400">
              Hỗ trợ
            </p>
            <ul class="mt-4 space-y-3 text-sm text-ink-500">
              <li class="flex gap-2">
                <Icon icon="mdi:email-outline" class="mt-0.5 h-4 w-4 shrink-0 text-ink-300" aria-hidden="true" />
                <span>Liên hệ nhóm đồ án</span>
              </li>
              <li class="flex gap-2">
                <Icon icon="mdi:file-document-outline" class="mt-0.5 h-4 w-4 shrink-0 text-ink-300" aria-hidden="true" />
                <span>Tài liệu &amp; API</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Newsletter -->
        <div class="lg:col-span-3">
          <div
            class="footer-news-card rounded-2xl border border-ink-900/[0.07] bg-white/85 p-5 shadow-[0_12px_40px_-28px_rgb(15_23_42/0.35)] backdrop-blur-sm ring-1 ring-white/70 sm:p-6"
          >
            <div class="flex items-start gap-3">
              <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600/10 text-brand-700"
              >
                <Icon icon="mdi:email-newsletter" class="h-6 w-6" aria-hidden="true" />
              </span>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-ink-900">Nhận tin mới</p>
                <p class="mt-1 text-xs leading-snug text-ink-500">
                  Gợi ý tài liệu và cập nhật tính năng (demo).
                </p>
              </div>
            </div>

            <form class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-stretch" @submit.prevent="submitNewsletter">
              <label class="sr-only" for="footer-email">Email</label>
              <input
                id="footer-email"
                v-model="email"
                type="email"
                autocomplete="email"
                placeholder="email@vidu.edu.vn"
                class="footer-input min-w-0 flex-1 rounded-xl border px-4 py-2.5 text-sm text-ink-900 outline-none transition placeholder:text-ink-300"
              />
              <button
                type="submit"
                class="footer-submit shrink-0 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-md transition hover:brightness-105 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 focus-visible:ring-offset-2 active:translate-y-[0.03rem] active:brightness-95 motion-reduce:active:translate-y-0"
              >
                Đăng ký
              </button>
            </form>

            <p
              v-if="newsletterOk"
              class="mt-3 flex items-center gap-1.5 text-xs font-medium text-emerald-700"
              role="status"
            >
              <Icon icon="mdi:check-circle-outline" class="h-4 w-4 shrink-0" aria-hidden="true" />
              Đã ghi nhận — kiểm tra hộp thư của bạn.
            </p>
          </div>
        </div>
      </div>

      <!-- Chân -->
      <div
        class="mt-14 flex flex-col items-center justify-between gap-4 border-t border-ink-900/[0.07] pt-8 text-xs text-ink-400 sm:flex-row sm:gap-3"
      >
        <p class="text-center sm:text-left">© {{ new Date().getFullYear() }} Tri Thức Số. Đồ án học thuật.</p>
        <p class="inline-flex items-center gap-1.5 tabular-nums">
          <Icon icon="mdi:translate" class="h-3.5 w-3.5 text-ink-300" aria-hidden="true" />
          Tiếng Việt · Vi-VN
        </p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer-surface {
  isolation: isolate;
}

.footer-col-title {
  letter-spacing: 0.06em;
}

.footer-input {
  border-color: rgb(226 232 240);
  background: rgb(248 250 252 / 0.92);
}

.footer-input:focus {
  border-color: rgb(147 197 253 / 0.85);
  box-shadow:
    0 0 0 3px rgb(59 130 246 / 0.12),
    0 1px 2px rgb(15 23 42 / 0.04);
}

.footer-submit {
  color: rgb(255 255 255);
  background: linear-gradient(165deg, rgb(59 130 246) 0%, rgb(29 78 216) 100%);
}
</style>
