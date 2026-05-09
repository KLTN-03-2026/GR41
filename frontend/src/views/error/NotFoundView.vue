<script setup>
import { computed } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const toast = useToast()

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

async function copyPath() {
  try {
    await navigator.clipboard.writeText(pathShown.value)
    toast.success('Đã sao chép đường dẫn')
  } catch {
    toast.error('Không thể sao chép (trình duyệt chặn quyền).')
  }
}
</script>

<template>
  <section
    class="relative isolate flex min-h-[min(94vh,920px)] h-screen flex-col overflow-hidden bg-slate-950 px-4 pb-20 sm:pb-24"
  >
    <!-- Aurora -->
    <div class="nf-aurora pointer-events-none absolute -inset-[40%] opacity-90 blur-3xl motion-safe:animate-gradient-pan" aria-hidden="true" />

    <div
      class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_0%,rgb(241_245_249/0.12),transparent_55%)]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgb(148_163_184/0.065)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.065)_1px,transparent_1px)] bg-[size:48px_48px]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute left-[-20%] top-[-10%] h-[55%] w-[65%] rotate-[-8deg] rounded-full bg-brand-400/22 blur-[120px]"
      aria-hidden="true"
    />
    <div
      class="pointer-events-none absolute left-1/2 top-[6%] h-[clamp(280px,50vh,580px)] w-[clamp(580px,88vw,1100px)] -translate-x-1/2 bg-[radial-gradient(ellipse_at_center,rgb(148_163_253/0.14),transparent_72%)]"
      aria-hidden="true"
    />

    <!-- Minh họa tuyến đường (SVG thuần, không bọc Vue Icon) -->
    <div class="pointer-events-none relative mx-auto mt-4 w-full max-w-3xl flex-none px-2" aria-hidden="true">
      <svg
        class="nf-route-svg mx-auto block max-h-[176px] w-full overflow-visible drop-shadow-[0_0_28px_rgba(96,165,250,0.18)]"
        viewBox="0 0 640 176"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <linearGradient id="nf-g1" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="rgb(186 230 253)" stop-opacity="0.95" />
            <stop offset="45%" stop-color="rgb(165 180 252)" stop-opacity="0.88" />
            <stop offset="100%" stop-color="rgb(196 181 253)" stop-opacity="0.75" />
          </linearGradient>
        </defs>
        <path
          d="M 28 138 C 120 138 148 72 226 74 C 300 76 342 148 438 148 C 512 148 548 118 596 126"
          class="nf-path-flow"
          stroke="url(#nf-g1)"
          stroke-linecap="round"
          stroke-width="2.5"
        />
        <circle
          cx="28"
          cy="138"
          r="8"
          fill="rgb(240 249 255)"
          stroke="rgb(186 230 253)"
          stroke-width="1.5"
          opacity="0.98"
        />
        <g transform="translate(576 118)">
          <circle
            cx="26"
            cy="26"
            r="24"
            fill="rgba(30,41,59,0.55)"
            stroke="rgb(216 180 254)"
            stroke-opacity="0.85"
            stroke-width="1.5"
          />
          <path
            d="M 15 17 L 37 39 M 37 17 L 15 39"
            stroke="rgb(254 249 250)"
            stroke-linecap="round"
            stroke-width="2.25"
            opacity="0.98"
          />
        </g>
      </svg>
    </div>

    <!-- Icon xa — tone sáng + glow để không chìm trên nền tím/indigo -->
    <Icon
      icon="mdi:file-document-outline"
      class="nf-icon-deco nf-icon-deco-doc absolute left-[8%] top-[20%] h-14 w-14 motion-safe:animate-float motion-safe:[animation-delay:-1s]"
      aria-hidden="true"
    />
    <Icon
      icon="mdi:bookmark-multiple-outline"
      class="nf-icon-deco nf-icon-deco-pin absolute right-[11%] top-[26%] h-11 w-11 motion-safe:animate-float motion-safe:[animation-delay:-3.4s]"
      aria-hidden="true"
    />
    <Icon
      icon="mdi:compass-outline"
      class="nf-icon-deco nf-icon-deco-compass absolute bottom-[26%] left-[13%] h-16 w-16 motion-safe:animate-float motion-safe:[animation-delay:-2.1s]"
      aria-hidden="true"
    />
    <Icon
      icon="mdi:terrain"
      class="nf-icon-deco nf-icon-deco-terrain absolute bottom-[32%] right-[15%] h-12 w-12 motion-safe:animate-float motion-safe:[animation-delay:-4s]"
      aria-hidden="true"
    />

    <div class="relative z-10 mx-auto flex w-full flex-col items-center text-center motion-safe:animate-fade-up">
      <!-- Orbital + Badge -->
      <div class="relative mb-11 flex justify-center pb-8 pt-8">
        <div
          class="nf-ring pointer-events-none absolute left-1/2 top-1/2 h-[148px] w-[148px] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-[0.93]"
          aria-hidden="true"
        />
        <div
          class="nf-ring-soft pointer-events-none absolute left-1/2 top-1/2 h-[174px] w-[174px] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-45"
          aria-hidden="true"
        />

        <div class="relative z-[1] flex h-[5.125rem] w-[5.125rem] motion-safe:animate-scale-in">
          <div
            class="absolute inset-[-8px] rounded-[1.9rem] bg-gradient-to-br from-sky-500/42 via-brand-600/43 to-indigo-800/52 opacity-[0.32] blur-2xl motion-safe:animate-pulse-soft"
            aria-hidden="true"
          />
          <div
            class="relative flex h-full w-full items-center justify-center rounded-[1.35rem] border border-white/18 bg-white/[0.09] shadow-[0_28px_64px_-20px_rgb(37_99_235/0.62)] ring-1 ring-white/[0.1] backdrop-blur-xl"
          >
            <Icon
              icon="mdi:map-marker-question-outline"
              class="h-[2.75rem] w-[2.75rem] text-white [filter:drop-shadow(0_2px_8px_rgb(34_211_238/0.75))_drop-shadow(0_0_22px_rgb(96_165_250/0.55))]"
            />
          </div>
        </div>
      </div>

      <!-- 404 -->
      <div class="relative px-4">
        <p
          class="nf-404-dim pointer-events-none font-display select-none text-[clamp(6rem,26vw,7.85rem)] font-black uppercase leading-none tracking-[0.12em]"
          aria-hidden="true"
        >
          404
        </p>
        <p
          class="nf-404-main relative z-[1] mt-[-6.05rem] bg-gradient-to-b from-white via-sky-100/95 to-cyan-200/88 bg-[length:200%_260%] bg-clip-text font-display text-[clamp(6rem,26vw,7.85rem)] font-black uppercase leading-none tracking-[0.1em] text-transparent motion-safe:animate-gradient-pan drop-shadow-[0_24px_60px_rgb(56_189_248/0.34)] sm:mt-[-6.92rem]"
        >
          404
        </p>
      </div>

      <div class="-mt-1">
        <h1 class="font-display text-[clamp(1.45rem,4.2vw,1.92rem)] font-bold tracking-tight text-slate-100">
          Địa điểm này chưa có trên bản đồ
        </h1>
        <p class="mx-auto mt-4 max-w-lg text-[0.9875rem] leading-relaxed text-slate-400">
          Liên kết có thể hết hiệu lực, URL gõ sai, hoặc trang được chuyển đi.
          Sao chép đường dẫn bên dưới để báo quản trị, hoặc chọn một lối ra an toàn.
        </p>

        <div
          class="mt-7 flex flex-wrap items-center gap-2 rounded-2xl border border-dashed border-white/26 bg-[linear-gradient(125deg,rgb(251_146_60/0.16),transparent_54%,rgb(59_130_246/0.12))] px-5 py-3.5 text-left text-[0.8925rem] text-slate-300 sm:text-[0.9175rem]"
        >
          <Icon
            icon="mdi:lighthouse-on"
            class="h-[1.28rem] w-[1.28rem] shrink-0 text-amber-200 [filter:drop-shadow(0_0_14px_rgb(251_191_36/0.55))]"
            aria-hidden="true"
          />
          <span>
            <span class="font-semibold text-amber-200/98">Định vị lại:&nbsp;</span>
            Kiểm tra chính tả trong thanh địa chỉ, hoặc mở
            <RouterLink
              class="font-medium text-sky-300 underline decoration-sky-500/58 underline-offset-[3px] transition hover:text-sky-50"
              to="/search"
            >
              cổng tìm kiếm
            </RouterLink>.
          </span>
        </div>

        <div class="mt-11 flex flex-col items-stretch gap-4 sm:flex-row sm:flex-wrap sm:justify-center sm:gap-4">
          <RouterLink
            to="/"
            class="group relative isolate inline-flex min-h-[52px] items-center justify-center gap-2 overflow-hidden rounded-2xl bg-gradient-to-r from-brand-500 via-indigo-500 to-violet-600 px-[2rem] py-3 font-display text-sm font-semibold text-white shadow-[0_26px_50px_-12px_rgb(79_70_229/0.78)] outline-none ring-offset-2 ring-offset-slate-950 transition hover:brightness-110 focus-visible:ring-2 focus-visible:ring-sky-300 active:translate-y-[0.04rem]"
          >
            <span
              class="pointer-events-none absolute inset-x-[-35%] -top-[60%] h-[148%] -rotate-[19deg] bg-[linear-gradient(108deg,rgb(255_255_255/0)_32%,rgb(255_255_255/0.16)_50%,rgb(255_255_255/0)_70%)] opacity-90 motion-safe:blur-[22px]"
              aria-hidden="true"
            />
            <Icon
              icon="mdi:home-outline"
              class="relative h-5 w-5 shrink-0 text-white [filter:drop-shadow(0_1px_6px_rgb(0_0_0/0.35))] transition group-hover:rotate-[-11deg] group-hover:scale-105 motion-reduce:transition-none motion-reduce:group-hover:[transform:none]"
              aria-hidden="true"
            />
            <span class="relative">Về cổng thư viện</span>
          </RouterLink>

          <RouterLink
            to="/search"
            class="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-2xl border border-white/[0.26] bg-white/[0.07] px-8 py-3 font-display text-sm font-semibold text-sky-50 backdrop-blur-md transition hover:border-brand-400/54 hover:bg-white/[0.12]"
          >
            <Icon icon="mdi:magnify" class="h-5 w-5 shrink-0 text-sky-100 [filter:drop-shadow(0_0_10px_rgb(56_189_248/0.5))]" aria-hidden="true" />
            Tìm tài liệu
          </RouterLink>

          <button
            type="button"
            class="inline-flex min-h-[52px] items-center justify-center gap-2 rounded-2xl border border-transparent px-8 py-3 font-display text-sm font-semibold text-slate-400 transition hover:border-white/12 hover:bg-white/[0.05] hover:text-slate-100 active:translate-y-[0.04rem]"
            @click="router.back()"
          >
            <Icon icon="mdi:arrow-u-left-bottom" class="h-5 w-5 shrink-0 text-slate-200 [filter:drop-shadow(0_0_10px_rgb(148_163_184/0.35))]" aria-hidden="true" />
            Trang trước
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
/* Icon trang trí: sáng + glow — tránh chìm trên nền slate/tím */
.nf-icon-deco {
  opacity: 0.95;
}

.nf-icon-deco-doc {
  color: rgb(224 242 254);
  filter: drop-shadow(0 0 16px rgb(34 211 238 / 0.5)) drop-shadow(0 0 32px rgb(56 189 250 / 0.22));
}

.nf-icon-deco-pin {
  color: rgb(237 233 254);
  filter: drop-shadow(0 0 16px rgb(167 139 250 / 0.55)) drop-shadow(0 0 28px rgb(196 181 253 / 0.2));
}

.nf-icon-deco-compass {
  color: rgb(204 251 241);
  filter: drop-shadow(0 0 18px rgb(45 212 191 / 0.48)) drop-shadow(0 0 36px rgb(34 211 238 / 0.2));
}

.nf-icon-deco-terrain {
  color: rgb(250 232 255);
  filter: drop-shadow(0 0 16px rgb(232 121 249 / 0.45)) drop-shadow(0 0 30px rgb(192 132 252 / 0.2));
}

@media (prefers-reduced-motion: reduce) {
  .nf-icon-deco-doc,
  .nf-icon-deco-pin,
  .nf-icon-deco-compass,
  .nf-icon-deco-terrain {
    filter: none;
    color: rgb(226 232 240);
    opacity: 0.92;
  }
}

.nf-aurora {
  background-image: radial-gradient(at 70% -10%, rgb(56 189 248 / 0.26), transparent 54%),
    radial-gradient(at 10% 30%, rgb(167 139 250 / 0.18), transparent 48%),
    radial-gradient(at 86% 70%, rgb(34 211 238 / 0.14), transparent 54%),
    linear-gradient(
      118deg,
      rgb(15 23 42 / 0.98) 0%,
      rgb(30 41 59 / 0.96) 48%,
      rgb(51 65 120 / 0.42) 100%
    );
  background-size: 260% 260%;
}

@media (prefers-reduced-motion: reduce) {
  .nf-aurora {
    animation: none !important;
  }
}

@keyframes nf-ring-spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes nf-ring-spin-reverse {
  to {
    transform: rotate(-360deg);
  }
}

.nf-ring {
  background: repeating-conic-gradient(
    from 0deg at 50% 50%,
    rgb(148 163 253 / 0.16) 0deg 18deg,
    transparent 18deg 32deg,
    rgb(56 189 248 / 0.12) 32deg 50deg,
    transparent 50deg 58deg,
    rgb(96 165 250 / 0.14) 58deg 90deg,
    transparent 90deg 120deg,
    rgb(129 140 248 / 0.12) 120deg 154deg,
    transparent 154deg 180deg,
    rgb(191 219 254 / 0.1) 180deg 200deg,
    transparent 200deg 360deg
  );
  mask-image: radial-gradient(circle farthest-side, transparent calc(71% - 2px), #000 calc(72%));
  animation: nf-ring-spin 48s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .nf-ring,
  .nf-ring-soft {
    animation: none !important;
  }
}

.nf-ring-soft {
  mask-image: radial-gradient(circle farthest-side, transparent calc(67% - 2px), #000 calc(68%));
  border: 2px dashed rgba(148, 163, 253, 0.24);
  background: radial-gradient(circle, rgb(59 130 246 / 0.22) 0%, transparent 70%);
  opacity: 0.48;
  animation: nf-ring-spin-reverse 62s linear infinite;
}

@keyframes nf-route-flow {
  to {
    stroke-dashoffset: -420;
  }
}

.nf-path-flow {
  stroke-dasharray: 7 17;
  animation: nf-route-flow 36s linear infinite;
}

@media (prefers-reduced-motion: reduce) {
  .nf-path-flow {
    animation: none !important;
  }

  .nf-404-main {
    animation: none !important;
    background-image: none !important;
    -webkit-background-clip: border-box;
    background-clip: border-box;
    color: rgb(226 232 240) !important;
    -webkit-text-fill-color: rgb(226 232 240);
    text-shadow: none;
    filter: none;
  }
}

.nf-404-dim {
  opacity: 0.12;
  letter-spacing: 0.06em;
  text-shadow:
    0 0 80px rgb(56 189 248 / 0.35),
    0 24px 64px rgb(15 118 209 / 0.18);
}

/* micro feedback */
.nf-copy:active {
  opacity: 0.92;
}
</style>
