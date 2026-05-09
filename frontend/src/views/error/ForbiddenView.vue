<script setup>
import { computed } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const pathShown = computed(() => {
  try {
    return decodeURIComponent(route.fullPath || route.path || '/')
  } catch {
    return route.fullPath || route.path || '/'
  }
})

const pathDisplay = computed(() => {
  const s = pathShown.value
  return s.length > 72 ? `${s.slice(0, 69)}…` : s
})

const loginTarget = computed(() => ({
  name: 'login',
  query: { redirect: pathShown.value || '/' },
}))
</script>

<template>
  <section
    class="relative isolate flex min-h-[min(94vh,900px)] h-screen flex-col overflow-hidden bg-slate-950 px-4 pb-20 sm:pb-24"
  >
    <div
      class="fb-aurora pointer-events-none absolute -inset-[38%] opacity-[0.92] blur-3xl motion-safe:animate-gradient-pan"
      aria-hidden="true"
    />

    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgb(254_202_202/0.08),transparent_52%)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgb(148_163_184/0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.06)_1px,transparent_1px)] bg-[size:48px_48px]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute right-[-15%] top-[-12%] h-[58%] w-[58%] rotate-[10deg] rounded-full bg-rose-500/18 blur-[120px]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute bottom-[-18%] left-[-8%] h-80 w-80 rounded-full bg-violet-500/16 blur-[100px] motion-safe:animate-pulse-soft"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute left-1/2 top-[8%] h-[clamp(260px,48vh,520px)] w-[clamp(520px,86vw,1000px)] -translate-x-1/2 bg-[radial-gradient(ellipse_at_center,rgb(244_114_182/0.12),transparent_70%)]"
      aria-hidden="true"
    />

    <!-- Rào trừu tượng: vạch cảnh báo + khóa -->
    <div class="pointer-events-none relative mx-auto mt-2 w-full max-w-2xl px-2" aria-hidden="true">
      <svg
        class="mx-auto block w-full max-w-xl overflow-visible opacity-95"
        viewBox="0 0 560 100"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <linearGradient id="fb-bar" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="rgb(251 113 133)" stop-opacity="0.35" />
            <stop offset="50%" stop-color="rgb(167 139 250)" stop-opacity="0.55" />
            <stop offset="100%" stop-color="rgb(251 191 36)" stop-opacity="0.32" />
          </linearGradient>
        </defs>
        <path
          d="M 24 52 L 536 52"
          class="fb-dash-line"
          stroke="url(#fb-bar)"
          stroke-linecap="round"
          stroke-width="2.5"
        />
        <rect
          height="34"
          width="34"
          x="263"
          y="35"
          fill="rgba(15,23,42,0.55)"
          rx="8"
          stroke="rgb(254 205 211)"
          stroke-opacity="0.75"
          stroke-width="1.5"
        />
        <path
          d="M 272 48 h 16 v 10 a 2 2 0 0 1 -2 2 h -12 a 2 2 0 0 1 -2 -2 V 48 Z M 276 48 V 44 a 4 4 0 0 1 8 0 v 4"
          stroke="rgb(254 249 250)"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.6"
        />
        <path d="M 275 58 h 10" stroke="rgb(254 249 250)" stroke-linecap="round" stroke-width="1.4" />
      </svg>
    </div>

    <span
      class="pointer-events-none absolute left-[9%] top-[22%] flex h-14 w-14 shrink-0 items-center justify-center motion-safe:animate-float motion-safe:[animation-delay:-1.5s]"
      aria-hidden="true"
    >
      <span class="fb-radar-effects">
        <span class="fb-radar-beam fb-radar-beam--rose" />
        <span class="fb-radar-ping fb-radar-ping--rose" />
        <span class="fb-radar-ping fb-radar-ping--rose fb-radar-ping--delay-a" />
      </span>
      <Icon icon="mdi:shield-lock-outline" class="fb-icon-deco fb-icon-deco-shield relative z-[1] h-full w-full" />
    </span>
    <span
      class="pointer-events-none absolute right-[10%] top-[28%] flex h-12 w-12 shrink-0 items-center justify-center motion-safe:animate-float motion-safe:[animation-delay:-3.6s]"
      aria-hidden="true"
    >
      <span class="fb-radar-effects">
        <span class="fb-radar-beam fb-radar-beam--amber" />
        <span class="fb-radar-ping fb-radar-ping--amber" />
        <span class="fb-radar-ping fb-radar-ping--amber fb-radar-ping--delay-a" />
      </span>
      <Icon icon="mdi:key-variant" class="fb-icon-deco fb-icon-deco-key relative z-[1] h-full w-full" />
    </span>
    <span
      class="pointer-events-none absolute bottom-[28%] left-[12%] flex h-14 w-14 shrink-0 items-center justify-center motion-safe:animate-float motion-safe:[animation-delay:-2.2s]"
      aria-hidden="true"
    >
      <span class="fb-radar-effects">
        <span class="fb-radar-beam fb-radar-beam--violet" />
        <span class="fb-radar-ping fb-radar-ping--violet" />
        <span class="fb-radar-ping fb-radar-ping--violet fb-radar-ping--delay-b" />
      </span>
      <Icon icon="mdi:door-lock" class="fb-icon-deco fb-icon-deco-door relative z-[1] h-full w-full" />
    </span>
    <span
      class="pointer-events-none absolute bottom-[30%] right-[14%] flex h-11 w-11 shrink-0 items-center justify-center motion-safe:animate-float motion-safe:[animation-delay:-4.2s]"
      aria-hidden="true"
    >
      <span class="fb-radar-effects">
        <span class="fb-radar-beam fb-radar-beam--rose" />
        <span class="fb-radar-ping fb-radar-ping--rose" />
        <span class="fb-radar-ping fb-radar-ping--rose fb-radar-ping--delay-b" />
      </span>
      <Icon icon="mdi:account-cancel-outline" class="fb-icon-deco fb-icon-deco-deny relative z-[1] h-full w-full" />
    </span>

    <div class="relative z-10 mx-auto flex w-full max-w-xl flex-col items-center text-center motion-safe:animate-fade-up">
      <div class="relative mb-11 flex justify-center pb-8 pt-6">
        <div
          class="fb-ring pointer-events-none absolute left-1/2 top-1/2 h-[154px] w-[154px] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-[0.9]"
          aria-hidden="true"
        />
        <div
          class="fb-ring-soft pointer-events-none absolute left-1/2 top-1/2 h-[182px] w-[182px] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-[0.45]"
          aria-hidden="true"
        />

        <div class="relative z-[1] flex h-[5.125rem] w-[5.125rem] motion-safe:animate-scale-in">
          <div
            class="absolute inset-[-8px] rounded-[1.9rem] bg-gradient-to-br from-rose-500/42 via-orange-400/35 to-violet-700/50 opacity-[0.38] blur-2xl motion-safe:animate-pulse-soft"
            aria-hidden="true"
          />
          <div
            class="relative flex h-full w-full items-center justify-center rounded-[1.35rem] border border-white/16 bg-white/[0.08] shadow-[0_28px_64px_-18px_rgb(225_29_72/0.55)] ring-1 ring-white/10 backdrop-blur-xl"
          >
            <Icon
              icon="mdi:shield-alert-outline"
              class="h-[2.85rem] w-[2.85rem] text-rose-100 [filter:drop-shadow(0_3px_10px_rgb(251_113_133/0.85))_drop-shadow(0_0_26px_rgb(167_139_250/0.45))]"
            />
          </div>
        </div>
      </div>

      <!-- 403 -->
      <div class="relative px-4">
        <p
          class="fb-403-dim pointer-events-none font-display select-none text-[clamp(6rem,26vw,7.85rem)] font-black uppercase leading-none tracking-[0.1em]"
          aria-hidden="true"
        >
          403
        </p>
        <p
          class="fb-403-main relative z-[1] mt-[-5.92rem] bg-gradient-to-b from-rose-100 via-fuchsia-200/92 to-orange-50/82 bg-[length:200%_240%] bg-clip-text font-display text-[clamp(6rem,26vw,7.85rem)] font-black uppercase leading-none tracking-[0.085em] text-transparent motion-safe:animate-gradient-pan drop-shadow-[0_24px_50px_rgb(244_63_94/0.35)] motion-reduce:drop-shadow-none sm:mt-[-6.75rem]"
        >
          403
        </p>
      </div>

      <div class="-mt-0.5 max-w-xl">
        <h1 class="font-display text-[clamp(1.45rem,4.2vw,1.95rem)] font-bold tracking-tight text-slate-100">
          Khu vực được kiểm soát
        </h1>
        <p class="mx-auto mt-4 max-w-md text-[0.9875rem] leading-relaxed text-slate-400">
          Tài khoản của bạn chưa đủ quyền để vào chỗ này. Nếu cần hỗ trợ đặc biệt, hãy ghi lại đường dẫn và liên hệ ban quản trị —
          không chia sẻ mật khẩu với ai.
        </p>

        <div
          class="mt-7 flex gap-3 rounded-2xl border border-dashed border-orange-400/34 bg-[linear-gradient(128deg,rgb(251_113_133/0.18),transparent_54%,rgb(167_139_250/0.12))] px-5 py-3.5 text-left text-[0.89rem] text-slate-300 sm:text-[0.9175rem]"
        >
          <Icon
            icon="mdi:gavel"
            class="h-[1.28rem] w-[1.28rem] shrink-0 text-amber-300 [filter:drop-shadow(0_0_14px_rgb(251_191_36/0.45))]"
            aria-hidden="true"
          />
          <span>
            <span class="font-semibold text-amber-200/98">Điều cần biết:&nbsp;</span>
            Quyền truy cập có thể thay đổi sau khi cập nhật vai trò. Thử đăng nhập đúng tài khoản hoặc về cổng công khai.
          </span>
        </div>

        <div class="mt-11 flex flex-col items-stretch gap-4 sm:flex-row sm:flex-wrap sm:justify-center sm:gap-4">
          <RouterLink
            to="/"
            class="group relative isolate inline-flex min-h-[52px] flex-1 items-center justify-center gap-2 overflow-hidden rounded-2xl bg-gradient-to-r from-rose-600 via-orange-600 to-indigo-600 px-[1.85rem] py-3 font-display text-sm font-semibold text-white shadow-[0_26px_50px_-12px_rgb(225_29_72/0.72)] outline-none ring-offset-2 ring-offset-slate-950 transition hover:brightness-110 focus-visible:ring-2 focus-visible:ring-rose-300 active:translate-y-[0.04rem] sm:flex-initial"
          >
            <span
              class="pointer-events-none absolute inset-x-[-38%] -top-[55%] h-[146%] -rotate-[19deg] bg-[linear-gradient(108deg,rgb(255_255_255/0)_34%,rgb(255_255_255/0.14)_50%,rgb(255_255_255/0)_71%)] opacity-90 motion-safe:blur-[20px]"
              aria-hidden="true"
            />
            <Icon
              icon="mdi:home-outline"
              class="relative h-5 w-5 shrink-0 text-white [filter:drop-shadow(0_1px_8px_rgb(0_0_0/0.35))] transition group-hover:rotate-[-10deg] group-hover:scale-105 motion-reduce:transition-none motion-reduce:group-hover:[transform:none]"
              aria-hidden="true"
            />
            <span class="relative">Về cổng thư viện</span>
          </RouterLink>

          <RouterLink
            v-if="!auth.isLoggedIn"
            :to="loginTarget"
            class="inline-flex min-h-[52px] flex-1 items-center justify-center gap-2 rounded-2xl border border-white/[0.22] bg-white/[0.07] px-8 py-3 font-display text-sm font-semibold text-rose-50/95 backdrop-blur-md transition hover:border-orange-400/52 hover:bg-white/[0.11] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-400/85 sm:flex-initial"
          >
            <Icon icon="mdi:login-variant" class="h-5 w-5 shrink-0 text-orange-100 [filter:drop-shadow(0_0_10px_rgb(251_146_60/0.45))]" aria-hidden="true" />
            Đăng nhập
          </RouterLink>

          <button
            type="button"
            class="inline-flex min-h-[52px] flex-1 items-center justify-center gap-2 rounded-2xl border border-transparent px-8 py-3 font-display text-sm font-semibold text-slate-400 transition hover:border-white/14 hover:bg-white/[0.05] hover:text-slate-100 active:translate-y-[0.035rem] sm:flex-initial"
            @click="router.back()"
          >
            <Icon
              icon="mdi:arrow-u-left-bottom"
              class="h-5 w-5 shrink-0 text-slate-300 [filter:drop-shadow(0_0_10px_rgb(148_163_184/0.35))]"
              aria-hidden="true"
            />
            Trang trước
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.fb-aurora {
  background-image: radial-gradient(at 74% -8%, rgb(251 113 133 / 0.2), transparent 52%),
    radial-gradient(at 8% 32%, rgb(167 139 250 / 0.16), transparent 48%),
    radial-gradient(at 88% 78%, rgb(251 146 36 / 0.12), transparent 54%),
    linear-gradient(118deg, rgb(15 23 42 / 0.99) 0%, rgb(30 27 53 / 0.96) 46%, rgb(89 42 71 / 0.42) 100%);
  background-size: 268% 268%;
}

@media (prefers-reduced-motion: reduce) {
  .fb-aurora {
    animation: none !important;
  }
}

.fb-icon-deco {
  opacity: 0.95;
}

.fb-icon-deco-shield {
  color: rgb(253 242 248);
  filter: drop-shadow(0 0 16px rgb(251 113 133 / 0.5)) drop-shadow(0 0 32px rgb(167 139 250 / 0.22));
}

.fb-icon-deco-key {
  color: rgb(255 251 235);
  filter: drop-shadow(0 0 16px rgb(251 146 36 / 0.48)) drop-shadow(0 0 26px rgb(251 113 133 / 0.15));
}

.fb-icon-deco-door {
  color: rgb(237 233 254);
  filter: drop-shadow(0 0 18px rgb(167 139 250 / 0.5)) drop-shadow(0 0 34px rgb(192 132 252 / 0.18));
}

.fb-icon-deco-deny {
  color: rgb(254 226 226);
  filter: drop-shadow(0 0 14px rgb(251 113 133 / 0.45));
}

@media (prefers-reduced-motion: reduce) {
  .fb-icon-deco-shield,
  .fb-icon-deco-key,
  .fb-icon-deco-door,
  .fb-icon-deco-deny {
    filter: none;
    color: rgb(226 232 240);
    opacity: 0.92;
  }
}

/** Radar chỉ trong ô = kích thước icon; không tham gia layout flow (ô cha absolute cố định). */
.fb-radar-effects {
  pointer-events: none;
  position: absolute;
  inset: 0;
  overflow: visible;
}

.fb-radar-beam {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 0;
  width: 300%;
  height: 300%;
  margin-left: -150%;
  margin-top: -150%;
  border-radius: 50%;
  transform-origin: 50% 50%;
  mask-image: radial-gradient(circle closest-side, #000 0%, #000 78%, transparent 82%);
  animation: fb-radar-sweep 6.8s linear infinite;
}

.fb-radar-beam--rose {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 44deg,
    rgb(251 113 133 / 0.78) 56deg,
    rgb(244 114 182 / 0.22) 72deg,
    transparent 86deg,
    transparent 360deg
  );
}

.fb-radar-beam--amber {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 40deg,
    rgb(251 191 36 / 0.72) 52deg,
    rgb(253 186 116 / 0.2) 68deg,
    transparent 84deg,
    transparent 360deg
  );
  animation-duration: 7.4s;
}

.fb-radar-beam--violet {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 42deg,
    rgb(167 139 250 / 0.78) 54deg,
    rgb(196 181 253 / 0.22) 70deg,
    transparent 88deg,
    transparent 360deg
  );
  animation-duration: 6.2s;
  animation-direction: reverse;
}

@keyframes fb-radar-sweep {
  to {
    transform: rotate(360deg);
  }
}

.fb-radar-ping {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 105%;
  height: 105%;
  box-sizing: border-box;
  border-radius: 50%;
  border-style: solid;
  border-width: 1.5px;
  transform: translate(-50%, -50%);
  animation: fb-radar-ping-wave 3.5s ease-out infinite;
  will-change: transform, opacity;
}

.fb-radar-ping--rose {
  border-color: rgb(251 113 133 / 0.55);
  box-shadow: 0 0 0 1px rgb(251 113 133 / 0.12);
}

.fb-radar-ping--amber {
  border-color: rgb(251 191 36 / 0.52);
  box-shadow: 0 0 0 1px rgb(251 191 36 / 0.1);
}

.fb-radar-ping--violet {
  border-color: rgb(167 139 250 / 0.52);
  box-shadow: 0 0 0 1px rgb(167 139 250 / 0.12);
}

.fb-radar-ping--delay-a {
  animation-delay: 1.15s;
}

.fb-radar-ping--delay-b {
  animation-delay: 2.05s;
}

@keyframes fb-radar-ping-wave {
  0% {
    opacity: 0.58;
    transform: translate(-50%, -50%) scale(0.72);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -50%) scale(2.35);
  }
}

@media (prefers-reduced-motion: reduce) {
  .fb-radar-beam,
  .fb-radar-ping {
    animation: none !important;
  }

  .fb-radar-ping {
    opacity: 0.18;
    transform: translate(-50%, -50%) scale(1.05);
  }

  .fb-radar-beam {
    opacity: 0.22;
    transform: rotate(28deg);
  }
}

@keyframes fb-ring-spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes fb-ring-spin-reverse {
  to {
    transform: rotate(-360deg);
  }
}

.fb-ring {
  background: repeating-conic-gradient(
    from -12deg at 50% 50%,
    rgb(251 146 146 / 0.18) 0deg 22deg,
    transparent 22deg 36deg,
    rgb(251 146 146 / 0.1) 36deg 48deg,
    transparent 48deg 64deg,
    rgb(216 180 254 / 0.15) 64deg 90deg,
    transparent 90deg 120deg,
    rgb(253 164 175 / 0.13) 120deg 150deg,
    transparent 150deg 200deg,
    rgb(251 207 232 / 0.1) 200deg 220deg,
    transparent 220deg 360deg
  );
  mask-image: radial-gradient(circle farthest-side, transparent calc(70% - 2px), #000 calc(71.5%));
  animation: fb-ring-spin 50s linear infinite;
}

.fb-ring-soft {
  mask-image: radial-gradient(circle farthest-side, transparent calc(66% - 2px), #000 calc(68%));
  border: 2px dashed rgba(251, 113, 133, 0.28);
  background: radial-gradient(circle, rgb(244 63 94 / 0.26) 0%, transparent 70%);
  opacity: 0.5;
  animation: fb-ring-spin-reverse 64s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .fb-ring,
  .fb-ring-soft {
    animation: none !important;
  }
}

@keyframes fb-dash {
  to {
    stroke-dashoffset: -520;
  }
}

.fb-dash-line {
  stroke-dasharray: 7 16;
  animation: fb-dash 34s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .fb-dash-line {
    animation: none !important;
  }
}

.fb-403-dim {
  opacity: 0.11;
  letter-spacing: 0.045em;
  text-shadow:
    0 0 72px rgb(251 113 133 / 0.35),
    0 22px 58px rgb(124 58 237 / 0.2);
}

@media (prefers-reduced-motion: reduce) {
  .fb-403-main {
    animation: none !important;
    background-image: none !important;
    background-clip: border-box !important;
    -webkit-text-fill-color: rgb(229 231 235) !important;
    color: rgb(229 231 235) !important;
    filter: none;
    text-shadow: none;
  }
}
</style>
