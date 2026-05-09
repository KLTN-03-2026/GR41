<script setup>
import { computed, ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { onClickOutside, useWindowScroll } from '@vueuse/core'
import { Icon } from '@iconify/vue'
import NotificationBell from '@/components/notification/NotificationBell.vue'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const route = useRoute()
const { y } = useWindowScroll()

const onHero = computed(() => route.name === 'home' && y.value < 96)

const accountMenuOpen = ref(false)
const accountMenuRoot = ref(null)

onClickOutside(accountMenuRoot, () => {
  accountMenuOpen.value = false
})

const initials = computed(() => {
  const n = auth.user?.name || '?'
  return n
    .split(' ')
    .map((s) => s[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})

function logout() {
  accountMenuOpen.value = false
  auth.logout(true)
}
</script>

<template>
  <header
    class="sticky top-0 z-40 transition-[background-color,box-shadow,color,border-color] duration-300"
    :class="
      onHero
        ? 'border-b border-transparent text-white shadow-none bg-gradient-to-br from-blue-500 via-blue-400/70 to-blue-300/50'
   
        : 'border-b border-ink-900/5 bg-white/70 shadow-sm ring-1 ring-ink-900/5 backdrop-blur-xl'
    "
  >
    <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
      <RouterLink
        to="/"
        class="flex shrink-0 items-center gap-2 font-display font-semibold transition-transform hover:scale-105 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded-lg"
        :class="onHero ? 'text-white' : 'text-ink-900'"
      >
        <img src="../../assets/images/logo-text.png" alt="Tri Thức Số" class="h-11 w-auto" />
      </RouterLink>

      <nav class="hidden flex-1 items-center gap-8 md:flex">
        <RouterLink
          to="/search"
          class="nav-link text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded"
          :class="onHero ? 'text-white/90 hover:text-white' : 'text-ink-600 hover:text-ink-900'"
          active-class="router-link-active-nav"
        >
          Tìm kiếm
        </RouterLink>
        <RouterLink
          v-if="auth.isAdmin"
          to="/admin"
          class="nav-link text-sm font-semibold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded"
          :class="onHero ? 'text-emerald-200 hover:text-white' : 'text-accent-600 hover:text-accent-700'"
        >
          Quản trị
        </RouterLink>
        <RouterLink
          v-if="auth.isTeacher"
          to="/teacher/proposals"
          class="nav-link text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 rounded"
          :class="onHero ? 'text-white/90 hover:text-white' : 'text-ink-600 hover:text-ink-900'"
          active-class="router-link-active-nav"
        >
          Đề xuất tài liệu
        </RouterLink>
      </nav>

      <div class="ml-auto flex items-center gap-2 sm:gap-3">
        <NotificationBell :light="onHero" />

        <template v-if="auth.isLoggedIn">
          <div ref="accountMenuRoot" class="relative">
            <button
              type="button"
              class="flex items-center gap-2 rounded-full border py-1 pl-1 pr-3 transition hover:ring-2 hover:ring-brand-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
              :class="
                onHero
                  ? 'border-white/30 bg-white/10 text-white backdrop-blur-md'
                  : 'border-ink-900/10 bg-white shadow-sm'
              "
              aria-label="Menu tài khoản"
              aria-haspopup="true"
              aria-controls="account-menu-dropdown"
              :aria-expanded="accountMenuOpen"
              @click="accountMenuOpen = !accountMenuOpen"
            >
              <span
                class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white"
                :class="onHero ? 'bg-white/25' : 'bg-brand-600'"
              >
                {{ initials }}
              </span>
              <span
                class="hidden max-w-[120px] truncate text-sm lg:inline"
                :class="onHero ? 'text-white' : 'text-ink-700'"
              >{{ auth.user?.name }}</span>
              <Icon
                icon="mdi:chevron-down"
                class="h-4 w-4"
                :class="onHero ? 'text-white/70' : 'text-ink-400'"
                aria-hidden="true"
              />
            </button>
            <div
              v-show="accountMenuOpen"
              id="account-menu-dropdown"
              class="absolute right-0 top-full z-50 mt-1 min-w-[208px] rounded-xl border border-ink-900/5 bg-white py-1 shadow-lift ring-1 ring-ink-900/5"
            >
              <RouterLink
                to="/profile"
                class="block px-4 py-2.5 text-sm text-ink-700 hover:bg-surface-soft"
                @click="accountMenuOpen = false"
              >
                Hồ sơ
              </RouterLink>
              <RouterLink
                v-if="auth.isTeacher"
                to="/teacher/proposals"
                class="block px-4 py-2.5 text-sm text-ink-700 hover:bg-surface-soft"
                @click="accountMenuOpen = false"
              >
                Đề xuất tài liệu
              </RouterLink>
              <RouterLink
                to="/profile/favorites"
                class="block px-4 py-2.5 text-sm text-ink-700 hover:bg-surface-soft"
                @click="accountMenuOpen = false"
              >
                Yêu thích
              </RouterLink>
              <RouterLink
                to="/profile/history"
                class="block px-4 py-2.5 text-sm text-ink-700 hover:bg-surface-soft"
                @click="accountMenuOpen = false"
              >
                Lịch sử
              </RouterLink>
              <button
                type="button"
                class="w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50"
                @click="logout"
              >
                Đăng xuất
              </button>
            </div>
          </div>
        </template>
        <template v-else>
          <RouterLink
            to="/login"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
            :class="onHero ? 'text-white hover:bg-white/10' : 'text-brand-600 hover:bg-brand-50'"
          >
            Đăng nhập
          </RouterLink>
          <RouterLink
            to="/register"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
            :class="
              onHero
                ? 'bg-white text-brand-700 hover:bg-brand-50 focus-visible:ring-white'
                : 'bg-brand-600 text-white hover:bg-brand-700 focus-visible:ring-brand-500'
            "
          >
            Đăng ký
          </RouterLink>
        </template>
      </div>
    </div>

    <div
      class="flex border-t px-4 pb-3 md:hidden"
      :class="onHero ? 'border-white/10' : 'border-ink-900/5'"
    >
      <RouterLink
        to="/search"
        class="flex flex-1 items-center justify-center gap-2 py-2 text-sm font-medium"
        :class="onHero ? 'text-white/90' : 'text-ink-600'"
      >
        <Icon icon="mdi:magnify" class="h-5 w-5" aria-hidden="true" />
        Tìm kiếm
      </RouterLink>
      <RouterLink
        v-if="auth.isLoggedIn"
        to="/profile"
        class="flex flex-1 items-center justify-center gap-2 py-2 text-sm font-medium"
        :class="onHero ? 'text-white/90' : 'text-ink-600'"
      >
        <Icon icon="mdi:account-outline" class="h-5 w-5" aria-hidden="true" />
        Hồ sơ
      </RouterLink>
    </div>
  </header>
</template>

<style scoped>
.nav-link {
  position: relative;
}
.nav-link::after {
  content: '';
  position: absolute;
  left: 50%;
  bottom: -4px;
  width: 0;
  height: 2px;
  border-radius: 2px;
  background: currentColor;
  transition:
    width 0.25s ease,
    left 0.25s ease;
  transform: translateX(-50%);
}
.nav-link:hover::after,
.router-link-active-nav::after {
  width: 100%;
  left: 50%;
}
@media (prefers-reduced-motion: reduce) {
  .nav-link::after {
    transition: none;
  }
}
</style>
