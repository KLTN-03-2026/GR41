<script setup>
import { computed } from 'vue'
import { splitByKeyword } from '@/utils/formatters'

const props = defineProps({
  text: { type: String, default: '' },
  keyword: { type: String, default: '' },
})

const parts = computed(() => splitByKeyword(props.text, props.keyword))
</script>

<template>
  <span>
    <template v-for="(p, i) in parts" :key="i">
      <mark v-if="p.highlight" class="rounded bg-yellow-200 px-0.5">{{ p.text }}</mark>
      <template v-else>{{ p.text }}</template>
    </template>
  </span>
</template>
