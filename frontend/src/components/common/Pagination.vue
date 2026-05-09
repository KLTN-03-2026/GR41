<script setup>
const props = defineProps({
  page: { type: Number, required: true },
  totalPages: { type: Number, required: true },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['update:page'])

function go(p) {
  if (p < 1 || p > props.totalPages || props.loading) return
  emit('update:page', p)
}
</script>

<template>
  <div class="flex flex-wrap items-center justify-center gap-2 py-6">
    <button
      type="button"
      class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm disabled:opacity-40"
      :disabled="page <= 1 || loading"
      @click="go(page - 1)"
    >
      Trước
    </button>
    <span class="text-sm text-slate-600">
      Trang <strong>{{ page }}</strong> / {{ totalPages }}
    </span>
    <button
      type="button"
      class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm disabled:opacity-40"
      :disabled="page >= totalPages || loading"
      @click="go(page + 1)"
    >
      Sau
    </button>
  </div>
</template>
