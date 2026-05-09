<script setup>
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'

defineProps({
  visible: { type: Boolean, default: false },
  title: { type: String, default: 'Xác nhận' },
  message: { type: String, default: 'Bạn chắc chắn?' },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['update:visible', 'confirm', 'cancel'])
</script>

<template>
  <Dialog
    :visible="visible"
    modal
    :header="title"
    :style="{ width: 'min(420px, 95vw)' }"
    @update:visible="(v) => emit('update:visible', v)"
  >
    <p class="text-slate-600">{{ message }}</p>
    <template #footer>
      <Button label="Hủy" severity="secondary" text @click="emit('update:visible', false); emit('cancel')" />
      <Button label="Xác nhận" severity="danger" :loading="Boolean(loading)" @click="emit('confirm')" />
    </template>
  </Dialog>
</template>
