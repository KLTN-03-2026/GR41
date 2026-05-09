<script setup>
import { formatDateTime } from '@/utils/formatters'
import { Icon } from '@iconify/vue'

defineProps({
  reviews: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})
</script>

<template>
  <div class="space-y-4">
    <h3 class="text-lg font-semibold text-slate-800">Đánh giá từ cộng đồng</h3>
    <div v-if="loading" class="space-y-3">
      <div v-for="n in 3" :key="n" class="animate-pulse rounded-lg border border-slate-100 bg-white p-4">
        <div class="h-4 w-1/3 rounded bg-slate-200" />
        <div class="mt-2 h-3 w-full rounded bg-slate-100" />
      </div>
    </div>
    <ul v-else class="space-y-3">
      <li
        v-for="r in reviews"
        :key="r.id"
        class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm"
      >
        <div class="flex items-start justify-between gap-2">
          <div>
            <p class="font-medium text-slate-800">{{ r.user?.name || 'Người dùng' }}</p>
            <p class="text-xs text-slate-500">{{ formatDateTime(r.created_at) }}</p>
          </div>
          <div class="flex items-center gap-0.5 text-amber-500">
            <Icon v-for="i in (r.rating || 0)" :key="i" icon="mdi:star" class="h-4 w-4" />
          </div>
        </div>
        <p v-if="r.comment" class="mt-2 text-sm text-slate-600">{{ r.comment }}</p>
      </li>
    </ul>
    <p v-if="!loading && (!reviews || reviews.length === 0)" class="text-sm text-slate-500">
      Chưa có đánh giá.
    </p>
  </div>
</template>
