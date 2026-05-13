<script setup>
import { Icon } from '@iconify/vue'
import Dropdown from 'primevue/dropdown'
import MultiSelect from 'primevue/multiselect'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  tags: { type: Array, default: () => [] },
  modelValue: { type: Object, required: true },
})

const emit = defineEmits(['update:modelValue', 'reset'])

function patch(partial) {
  emit('update:modelValue', { ...props.modelValue, ...partial })
}
</script>

<template>
  <aside
    class="overflow-hidden rounded-2xl border border-ink-900/[0.07] bg-white/95 shadow-soft ring-1 ring-ink-900/[0.04] backdrop-blur-sm transition-shadow duration-300 hover:shadow-md motion-reduce:transition-none"
  >
    <div
      class="flex items-center gap-3 border-b border-ink-900/[0.06] bg-gradient-to-r from-brand-50/90 to-white px-5 py-4"
    >
      <span
        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white shadow-soft"
      >
        <Icon icon="mdi:tune-variant" class="h-5 w-5" />
      </span>
      <div>
        <h3 class="font-display text-base font-bold text-ink-900">Bộ lọc</h3>
        <p class="text-xs text-ink-500">Áp dụng ngay khi chọn</p>
      </div>
    </div>

    <div class="space-y-5 p-5">
      <div class="group">
        <label class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-ink-500">
          <Icon icon="mdi:folder-outline" class="h-4 w-4 text-brand-600" />
          Danh mục
        </label>
        <Dropdown
          :model-value="modelValue.category_id"
          :options="categories"
          option-label="name"
          option-value="id"
          placeholder="Tất cả danh mục"
          class="filter-dd w-full"
          panel-class="document-filter-overlay-panel"
          show-clear
          @update:model-value="(v) => patch({ category_id: v })"
        />
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-ink-500">
            <Icon icon="mdi:calendar-start" class="h-4 w-4 text-brand-600" />
            Từ năm
          </label>
          <input
            type="number"
            min="1900"
            max="2100"
            class="w-full rounded-xl border border-ink-900/[0.08] bg-surface-soft/80 px-3 py-2.5 text-sm text-ink-900 outline-none ring-brand-500/0 transition focus:border-brand-400 focus:bg-white focus:ring-2 focus:ring-brand-500/25"
            placeholder="—"
            :value="modelValue.year_from ?? ''"
            @input="
              patch({
                year_from: $event.target.value ? Number($event.target.value) : null,
              })
            "
          />
        </div>
        <div>
          <label class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-ink-500">
            <Icon icon="mdi:calendar-end" class="h-4 w-4 text-brand-600" />
            Đến năm
          </label>
          <input
            type="number"
            min="1900"
            max="2100"
            class="w-full rounded-xl border border-ink-900/[0.08] bg-surface-soft/80 px-3 py-2.5 text-sm text-ink-900 outline-none ring-brand-500/0 transition focus:border-brand-400 focus:bg-white focus:ring-2 focus:ring-brand-500/25"
            placeholder="—"
            :value="modelValue.year_to ?? ''"
            @input="
              patch({ year_to: $event.target.value ? Number($event.target.value) : null })
            "
          />
        </div>
      </div>

      <div>
        <label class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-ink-500">
          <Icon icon="mdi:translate" class="h-4 w-4 text-brand-600" />
          Ngôn ngữ
        </label>
        <Dropdown
          :model-value="modelValue.language"
          :options="[
            { label: 'Tất cả', value: '' },
            { label: 'Tiếng Việt', value: 'vi' },
            { label: 'English', value: 'en' },
          ]"
          option-label="label"
          option-value="value"
          placeholder="Chọn ngôn ngữ"
          class="filter-dd w-full"
          panel-class="document-filter-overlay-panel"
          @update:model-value="(v) => patch({ language: v ?? '' })"
        />
      </div>

      <div>
        <label class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-ink-500">
          <Icon icon="mdi:tag-multiple-outline" class="h-4 w-4 text-brand-600" />
          Tags
        </label>
        <MultiSelect
          :model-value="modelValue.tag_ids"
          :options="tags"
          option-label="name"
          option-value="id"
          display="chip"
          placeholder="Chọn một hoặc nhiều tag"
          class="filter-ms w-full"
          panel-class="document-filter-overlay-panel"
          filter
          @update:model-value="(v) => patch({ tag_ids: Array.isArray(v) ? v : [] })"
        />
      </div>

      <button
        type="button"
        class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:border-brand-300 hover:bg-brand-100 focus:outline-none focus:ring-2 focus:ring-brand-500/25"
        @click="emit('reset')"
      >
        <Icon icon="mdi:filter-remove-outline" class="h-4 w-4" />
        Đặt lại bộ lọc
      </button>
    </div>
  </aside>
</template>

<style scoped>
:deep(.filter-dd.p-dropdown),
:deep(.filter-ms.p-multiselect) {
  border-radius: 0.75rem;
  border-color: rgba(15, 23, 42, 0.08);
}
:deep(.filter-dd.p-dropdown:not(.p-disabled):hover),
:deep(.filter-ms.p-multiselect:not(.p-disabled):hover) {
  border-color: rgba(37, 99, 235, 0.3);
}
:deep(.filter-dd.p-dropdown.p-focus),
:deep(.filter-ms.p-multiselect.p-focus) {
  border-color: rgb(96 165 250);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
</style>

<style>
.document-filter-overlay-panel .p-select-list-container,
.document-filter-overlay-panel .p-multiselect-list-container {
  scrollbar-width: thin;
  scrollbar-color: rgba(148, 163, 184, 0.55) transparent;
}

.document-filter-overlay-panel .p-select-list-container::-webkit-scrollbar,
.document-filter-overlay-panel .p-multiselect-list-container::-webkit-scrollbar {
  width: 6px;
}

.document-filter-overlay-panel .p-select-list-container::-webkit-scrollbar-track,
.document-filter-overlay-panel .p-multiselect-list-container::-webkit-scrollbar-track {
  background: transparent;
}

.document-filter-overlay-panel .p-select-list-container::-webkit-scrollbar-thumb,
.document-filter-overlay-panel .p-multiselect-list-container::-webkit-scrollbar-thumb {
  background-color: rgba(148, 163, 184, 0.5);
  border-radius: 9999px;
}

.document-filter-overlay-panel .p-select-list-container::-webkit-scrollbar-thumb:hover,
.document-filter-overlay-panel .p-multiselect-list-container::-webkit-scrollbar-thumb:hover {
  background-color: rgba(100, 116, 139, 0.65);
}

.document-filter-overlay-panel .p-select-list-container::-webkit-scrollbar-button,
.document-filter-overlay-panel .p-multiselect-list-container::-webkit-scrollbar-button {
  display: none;
  width: 0;
  height: 0;
}
</style>
