<script setup>
defineProps({
  role: { type: String, default: 'bot' },
  html: { type: String, default: '' },
})
</script>

<template>
  <div
    class="max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-relaxed shadow-sm"
    :class="role === 'user' ? 'ml-auto bg-primary-blue text-white' : 'mr-auto bg-white text-slate-800 border border-slate-100'"
  >
    <div
      class="prose prose-sm max-w-none break-words"
      :class="role === 'user' ? '[&_a]:text-blue-100 [&_a]:underline' : '[&_a]:text-blue-600 [&_ul]:my-1 [&_ul]:list-disc [&_ul]:pl-5'"
      v-html="html"
    />
  </div>
</template>
