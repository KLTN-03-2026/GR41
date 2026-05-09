<script setup>
import { Icon } from '@iconify/vue'
import { RouterLink } from 'vue-router'
import logoText from '@/assets/images/logo-text.png'
import logoMark from '@/assets/images/logo.png'
</script>

<template>
  <div class="auth-shell relative flex min-h-screen flex-col overflow-hidden">
    <!-- Full-viewport background -->
    <div class="auth-bg-fixed pointer-events-none fixed inset-0 z-0" aria-hidden="true">
      <div class="absolute inset-0 bg-[linear-gradient(165deg,#eef2ff_0%,#f8fafc_42%,#f1f5f9_100%)]" />
      <div
        class="absolute inset-0 opacity-[0.92] mix-blend-multiply"
        style="
          background-image:
            radial-gradient(ellipse 120% 70% at 80% -10%, rgba(59, 130, 246, 0.28), transparent 52%),
            radial-gradient(ellipse 90% 65% at -5% 35%, rgba(139, 92, 246, 0.22), transparent 48%),
            radial-gradient(ellipse 70% 55% at 105% 85%, rgba(45, 212, 191, 0.14), transparent 46%);
        "
      />
      <!-- Diagonal sheen -->
      <div class="auth-bg-sheen absolute -inset-[35%] rotate-12 opacity-[0.09]" />
      <!-- Soft grid -->
      <div class="auth-bg-grid absolute inset-0 opacity-[0.42]" />
      <!-- Aurora band -->
      <div class="auth-bg-aurora absolute inset-x-0 top-0 h-[52%] opacity-60" />

      <!-- Floating blobs -->
      <div class="auth-blob auth-blob--a absolute left-[-12%] top-[6%] h-[min(480px,58vw)] w-[min(480px,58vw)] rounded-full bg-gradient-to-br from-brand-400/40 via-sky-400/25 to-transparent blur-[88px]" />
      <div class="auth-blob auth-blob--b absolute right-[-8%] top-[22%] h-[min(380px,48vw)] w-[min(380px,48vw)] rounded-full bg-gradient-to-bl from-violet-500/35 to-indigo-400/15 blur-[78px]" />
      <div class="auth-blob auth-blob--c absolute bottom-[-6%] left-[18%] h-[min(420px,52vw)] w-[min(420px,52vw)] rounded-full bg-gradient-to-tr from-emerald-400/28 to-cyan-400/12 blur-[92px]" />

      <!-- Decorative floating icons (thư viện / tri thức) + radar -->
      <div class="auth-bg-icons pointer-events-none absolute inset-0 overflow-visible">
        <div
          class="auth-deco auth-deco--a auth-radar-anchor absolute left-[5%] top-[20%] flex size-12 items-center justify-center overflow-visible text-brand-600/[0.18] sm:left-[8%] sm:size-14 sm:top-[24%]"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--brand" />
            <span class="auth-radar-ping auth-radar-ping--soft-brand" />
            <span class="auth-radar-ping auth-radar-ping--soft-brand auth-radar-ping--delay-a" />
          </span>
          <Icon icon="mdi:book-open-variant-outline" class="relative z-[1] size-12 shrink-0 sm:size-14" />
        </div>
        <div
          class="auth-deco auth-deco--b auth-radar-anchor absolute right-[7%] top-[16%] flex size-14 items-center justify-center overflow-visible text-violet-600/[0.16] sm:right-[10%] sm:size-16"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--violet" />
            <span class="auth-radar-ping auth-radar-ping--soft-violet" />
            <span class="auth-radar-ping auth-radar-ping--soft-violet auth-radar-ping--delay-b" />
          </span>
          <Icon icon="mdi:library-shelves" class="relative z-[1] size-14 shrink-0 sm:size-16" />
        </div>
        <div
          class="auth-deco auth-deco--c auth-radar-anchor absolute left-[12%] bottom-[18%] flex size-11 items-center justify-center overflow-visible text-sky-600/[0.15] sm:bottom-[22%] sm:size-[3.25rem]"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--sky" />
            <span class="auth-radar-ping auth-radar-ping--soft-sky" />
            <span class="auth-radar-ping auth-radar-ping--soft-sky auth-radar-ping--delay-a" />
          </span>
          <Icon icon="mdi:notebook-outline" class="relative z-[1] size-11 shrink-0 sm:size-[3.25rem]" />
        </div>
        <div
          class="auth-deco auth-deco--d auth-radar-anchor absolute right-[14%] bottom-[14%] flex size-12 items-center justify-center overflow-visible text-emerald-600/[0.14] sm:right-[18%] sm:size-14"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--emerald" />
            <span class="auth-radar-ping auth-radar-ping--soft-emerald" />
            <span class="auth-radar-ping auth-radar-ping--soft-emerald auth-radar-ping--delay-b" />
          </span>
          <Icon icon="mdi:certificate-outline" class="relative z-[1] size-12 shrink-0 sm:size-14" />
        </div>
        <div
          class="auth-deco auth-deco--e auth-radar-anchor absolute left-[38%] top-[12%] flex size-10 items-center justify-center overflow-visible text-indigo-500/[0.13] sm:left-[42%] sm:size-12"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--indigo" />
            <span class="auth-radar-ping auth-radar-ping--soft-indigo" />
            <span class="auth-radar-ping auth-radar-ping--soft-indigo auth-radar-ping--delay-a" />
          </span>
          <Icon icon="mdi:bookmark-multiple-outline" class="relative z-[1] size-10 shrink-0 sm:size-12" />
        </div>
        <div
          class="auth-deco auth-deco--f auth-radar-anchor absolute bottom-[26%] right-[30%] hidden size-[3rem] items-center justify-center overflow-visible text-cyan-600/[0.12] sm:flex"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--cyan" />
            <span class="auth-radar-ping auth-radar-ping--soft-cyan" />
            <span class="auth-radar-ping auth-radar-ping--soft-cyan auth-radar-ping--delay-b" />
          </span>
          <Icon icon="mdi:file-document-outline" class="relative z-[1] size-[3rem] shrink-0" />
        </div>
        <div
          class="auth-deco auth-deco--g auth-radar-anchor absolute bottom-[36%] right-[6%] flex size-[2.85rem] items-center justify-center overflow-visible text-amber-600/[0.12]"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--amber" />
            <span class="auth-radar-ping auth-radar-ping--soft-amber" />
            <span class="auth-radar-ping auth-radar-ping--soft-amber auth-radar-ping--delay-a" />
          </span>
          <Icon icon="mdi:compass-outline" class="relative z-[1] size-[2.85rem] shrink-0" />
        </div>
        <div
          class="auth-deco auth-deco--spin auth-radar-anchor absolute left-[24%] top-[48%] hidden size-12 items-center justify-center overflow-visible text-slate-500/[0.1] sm:flex"
        >
          <span class="auth-radar-effects" aria-hidden="true">
            <span class="auth-radar-beam auth-radar-beam--slate" />
            <span class="auth-radar-ping auth-radar-ping--soft-slate" />
            <span class="auth-radar-ping auth-radar-ping--soft-slate auth-radar-ping--delay-b" />
          </span>
          <Icon icon="mdi:head-lightbulb-outline" class="relative z-[1] size-12 shrink-0" />
        </div>

        <!-- Vòng tròn nhẹ chỉ báo không gian -->
        <div class="auth-deco-ring auth-deco-ring--1 absolute left-[18%] top-[42%] max-sm:opacity-70" />
        <div class="auth-deco-ring auth-deco-ring--2 absolute right-[18%] top-[48%]" />
        <div class="auth-deco-ring auth-deco-ring--3 absolute bottom-[20%] left-[44%] max-sm:hidden" />
      </div>

      <!-- Bottom vignette for depth -->
      <div
        class="absolute inset-x-0 bottom-0 h-[45%] bg-gradient-to-t from-slate-200/35 to-transparent mix-blend-multiply"
      />
    </div>

    <header
      class="relative z-20 border-b border-white/45 bg-white/55 shadow-[0_1px_0_0_rgba(255,255,255,0.6)] backdrop-blur-xl backdrop-saturate-150"
    >
      <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3.5 sm:px-6">
        <RouterLink
          to="/"
          class="group flex items-center gap-3 rounded-xl py-1 pr-2 transition hover:opacity-95"
        >
          <span
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-white/95 to-brand-50/90 shadow-md ring-1 ring-brand-200/50 transition group-hover:shadow-lg"
          >
            <img :src="logoMark" alt="" class="h-7 w-7 object-contain" width="28" height="28" />
          </span>
          <img
            :src="logoText"
            alt="Tri Thức Số"
            class="h-8 w-auto max-w-[min(200px,46vw)] object-contain object-left sm:h-9"
          />
        </RouterLink>
        <RouterLink
          to="/"
          class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-white/60 hover:text-brand-700"
        >
          <span class="hidden sm:inline">Về trang chủ</span>
          <span class="sm:hidden">Trang chủ</span>
        </RouterLink>
      </div>
    </header>

    <main class="relative z-10 flex flex-1 items-center justify-center px-4 py-10 sm:px-6 sm:py-14">
      <div
        class="auth-card w-full max-w-md rounded-3xl border border-white/70 bg-white/80 p-6 shadow-lift backdrop-blur-2xl sm:max-w-lg sm:p-8 md:max-w-xl md:p-9"
      >
        <RouterView />
      </div>
    </main>
  </div>
</template>

<style scoped>
.auth-bg-sheen {
  background: linear-gradient(
    115deg,
    transparent 0%,
    rgba(255, 255, 255, 0.45) 42%,
    transparent 78%
  );
  animation: authSheen 14s ease-in-out infinite;
}

.auth-bg-grid {
  background-image:
    linear-gradient(rgba(148, 163, 184, 0.09) 1px, transparent 1px),
    linear-gradient(90deg, rgba(148, 163, 184, 0.09) 1px, transparent 1px);
  background-size: 52px 52px;
  mask-image: radial-gradient(ellipse 85% 70% at 50% 40%, black 35%, transparent 100%);
}

.auth-bg-aurora {
  background: linear-gradient(
    105deg,
    rgba(96, 165, 250, 0.2) 0%,
    rgba(167, 139, 250, 0.12) 40%,
    transparent 70%
  );
  filter: blur(28px);
  animation: authAurora 20s ease-in-out infinite alternate;
}

.auth-blob {
  will-change: transform;
  animation: authBlob 22s ease-in-out infinite;
}

.auth-blob--b {
  animation-duration: 26s;
  animation-delay: -4s;
}

.auth-blob--c {
  animation-duration: 24s;
  animation-delay: -9s;
}

/* --- Icon decorations --- */
.auth-deco {
  will-change: transform, opacity;
  filter: drop-shadow(0 4px 20px rgba(37, 99, 235, 0.08));
}

.auth-deco--a {
  animation: authDecoFloatA 22s ease-in-out infinite;
}

.auth-deco--b {
  animation: authDecoFloatB 26s ease-in-out infinite;
  animation-delay: -5s;
}

.auth-deco--c {
  animation: authDecoFloatC 20s ease-in-out infinite;
  animation-delay: -8s;
}

.auth-deco--d {
  animation: authDecoFloatA 24s ease-in-out infinite;
  animation-delay: -3s;
}

.auth-deco--e {
  animation: authDecoBob 14s ease-in-out infinite;
}

.auth-deco--f {
  animation: authDecoFloatB 28s ease-in-out infinite;
  animation-delay: -12s;
}

.auth-deco--g {
  animation: authDecoBob 17s ease-in-out infinite;
  animation-delay: -6s;
}

.auth-deco--spin {
  animation: authDecoSlowSpin 48s linear infinite;
}

.auth-deco-ring {
  pointer-events: none;
  aspect-ratio: 1;
  width: clamp(140px, 22vw, 220px);
  border-radius: 50%;
  border: 1px solid rgba(99, 102, 241, 0.12);
  box-shadow:
    0 0 0 1px rgba(255, 255, 255, 0.35) inset,
    0 0 40px rgba(59, 130, 246, 0.06);
}

.auth-deco-ring--1 {
  animation: authRingPulse 16s ease-in-out infinite;
}

.auth-deco-ring--2 {
  animation: authRingPulse 19s ease-in-out infinite;
  animation-delay: -4s;
  border-color: rgba(16, 185, 129, 0.1);
}

.auth-deco-ring--3 {
  width: clamp(100px, 16vw, 160px);
  animation: authRingPulse 14s ease-in-out infinite;
  animation-delay: -7s;
  border-color: rgba(14, 165, 233, 0.11);
}

/* Radar quanh từng icon nền — ô kích thước cố định, không ảnh hưởng header/card */
.auth-radar-effects {
  pointer-events: none;
  position: absolute;
  inset: 0;
  overflow: visible;
}

.auth-radar-beam {
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
  animation: auth-radar-sweep 6.8s linear infinite;
}

.auth-radar-beam--brand {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 42deg,
    rgb(59 130 246 / 0.44) 54deg,
    rgb(99 102 241 / 0.16) 70deg,
    transparent 88deg,
    transparent 360deg
  );
  animation-duration: 6.5s;
}

.auth-radar-beam--violet {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 42deg,
    rgb(139 92 246 / 0.44) 54deg,
    rgb(167 139 250 / 0.15) 70deg,
    transparent 88deg,
    transparent 360deg
  );
  animation-duration: 6.4s;
  animation-direction: reverse;
}

.auth-radar-beam--sky {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 40deg,
    rgb(14 165 233 / 0.4) 52deg,
    rgb(56 189 248 / 0.14) 68deg,
    transparent 84deg,
    transparent 360deg
  );
  animation-duration: 7s;
}

.auth-radar-beam--emerald {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 42deg,
    rgb(16 185 129 / 0.4) 54deg,
    rgb(52 211 153 / 0.14) 70deg,
    transparent 88deg,
    transparent 360deg
  );
  animation-duration: 6.6s;
}

.auth-radar-beam--indigo {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 42deg,
    rgb(99 102 241 / 0.4) 54deg,
    rgb(129 140 248 / 0.14) 70deg,
    transparent 88deg,
    transparent 360deg
  );
  animation-duration: 6.9s;
  animation-direction: reverse;
}

.auth-radar-beam--cyan {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 40deg,
    rgb(6 182 212 / 0.38) 52deg,
    rgb(34 211 238 / 0.12) 68deg,
    transparent 84deg,
    transparent 360deg
  );
  animation-duration: 7.2s;
}

.auth-radar-beam--amber {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 40deg,
    rgb(245 158 11 / 0.4) 52deg,
    rgb(251 191 36 / 0.12) 68deg,
    transparent 84deg,
    transparent 360deg
  );
  animation-duration: 6.7s;
}

.auth-radar-beam--slate {
  background: conic-gradient(
    from 0deg,
    transparent 0deg,
    transparent 44deg,
    rgb(100 116 139 / 0.32) 56deg,
    rgb(148 163 184 / 0.1) 72deg,
    transparent 88deg,
    transparent 360deg
  );
  animation-duration: 8s;
  animation-direction: reverse;
}

@keyframes auth-radar-sweep {
  to {
    transform: rotate(360deg);
  }
}

.auth-radar-ping {
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
  animation: auth-radar-ping-wave 3.5s ease-out infinite;
  will-change: transform, opacity;
}

.auth-radar-ping--soft-brand {
  border-color: rgb(37 99 235 / 0.28);
  box-shadow: 0 0 0 1px rgb(59 130 246 / 0.08);
}

.auth-radar-ping--soft-violet {
  border-color: rgb(139 92 246 / 0.28);
  box-shadow: 0 0 0 1px rgb(167 139 250 / 0.08);
}

.auth-radar-ping--soft-sky {
  border-color: rgb(14 165 233 / 0.28);
  box-shadow: 0 0 0 1px rgb(56 189 248 / 0.08);
}

.auth-radar-ping--soft-emerald {
  border-color: rgb(16 185 129 / 0.28);
  box-shadow: 0 0 0 1px rgb(52 211 153 / 0.08);
}

.auth-radar-ping--soft-indigo {
  border-color: rgb(99 102 241 / 0.28);
  box-shadow: 0 0 0 1px rgb(129 140 248 / 0.08);
}

.auth-radar-ping--soft-cyan {
  border-color: rgb(6 182 212 / 0.28);
  box-shadow: 0 0 0 1px rgb(34 211 238 / 0.08);
}

.auth-radar-ping--soft-amber {
  border-color: rgb(245 158 11 / 0.28);
  box-shadow: 0 0 0 1px rgb(251 191 36 / 0.08);
}

.auth-radar-ping--soft-slate {
  border-color: rgb(100 116 139 / 0.22);
  box-shadow: 0 0 0 1px rgb(148 163 184 / 0.06);
}

.auth-radar-ping--delay-a {
  animation-delay: 1.15s;
}

.auth-radar-ping--delay-b {
  animation-delay: 2.05s;
}

@keyframes auth-radar-ping-wave {
  0% {
    opacity: 0.48;
    transform: translate(-50%, -50%) scale(0.72);
  }
  100% {
    opacity: 0;
    transform: translate(-50%, -50%) scale(2.15);
  }
}

.auth-radar-anchor .auth-radar-beam {
  opacity: 0.38;
}

@keyframes authSheen {
  0%,
  100% {
    opacity: 0.07;
    transform: translate(-3%, -2%);
  }
  50% {
    opacity: 0.13;
    transform: translate(4%, 3%);
  }
}

@keyframes authAurora {
  0% {
    transform: translateX(-8%) skewX(-2deg);
  }
  100% {
    transform: translateX(12%) skewX(1deg);
  }
}

@keyframes authBlob {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
    opacity: 0.92;
  }
  33% {
    transform: translate(6%, -4%) scale(1.04);
    opacity: 1;
  }
  66% {
    transform: translate(-4%, 6%) scale(0.98);
    opacity: 0.95;
  }
}

@keyframes authDecoFloatA {
  0%,
  100% {
    transform: translate(0, 0) rotate(-8deg);
    opacity: 0.75;
  }
  33% {
    transform: translate(14px, -12px) rotate(5deg);
    opacity: 1;
  }
  66% {
    transform: translate(-10px, 14px) rotate(-3deg);
    opacity: 0.82;
  }
}

@keyframes authDecoFloatB {
  0%,
  100% {
    transform: translate(0, 0) rotate(6deg);
    opacity: 0.7;
  }
  50% {
    transform: translate(-16px, 18px) rotate(-8deg);
    opacity: 0.95;
  }
}

@keyframes authDecoFloatC {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
    opacity: 0.72;
  }
  40% {
    transform: translate(10px, -20px) scale(1.05);
    opacity: 0.98;
  }
  70% {
    transform: translate(-12px, 8px) scale(0.97);
    opacity: 0.8;
  }
}

@keyframes authDecoBob {
  0%,
  100% {
    transform: translateY(0);
    opacity: 0.65;
  }
  50% {
    transform: translateY(-16px);
    opacity: 0.95;
  }
}

@keyframes authDecoSlowSpin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes authRingPulse {
  0%,
  100% {
    transform: scale(1);
    opacity: 0.45;
  }
  50% {
    transform: scale(1.06);
    opacity: 0.75;
  }
}

@media (prefers-reduced-motion: reduce) {
  .auth-bg-sheen,
  .auth-bg-aurora,
  .auth-blob,
  .auth-deco,
  .auth-deco-ring,
  .auth-radar-beam,
  .auth-radar-ping {
    animation: none !important;
  }

  .auth-bg-aurora {
    transform: none;
  }

  .auth-blob {
    opacity: 0.85;
  }

  .auth-deco {
    opacity: 0.55;
  }

  .auth-deco-ring {
    opacity: 0.35;
  }

  .auth-radar-ping {
    opacity: 0.14;
    transform: translate(-50%, -50%) scale(1.05);
  }

  .auth-radar-anchor .auth-radar-beam {
    opacity: 0.18;
    transform: rotate(22deg);
  }
}
</style>
