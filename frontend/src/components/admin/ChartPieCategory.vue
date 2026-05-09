<script setup>
import { computed } from 'vue'
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
  data: { type: Object, default: null },
})

const palette = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#ec4899']

const chartData = computed(() => {
  const cats = props.data?.category_distribution || props.data?.pie_categories || []
  const labels = cats.map((c) => c.name || c.label || 'Khác')
  const values = cats.map((c) => c.count ?? c.documents_count ?? c.value ?? 0)
  return {
    labels,
    datasets: [
      {
        data: values,
        backgroundColor: palette.slice(0, Math.max(labels.length, 1)),
      },
    ],
  }
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { position: 'bottom' } },
}
</script>

<template>
  <div
    class="group relative overflow-hidden rounded-2xl border border-slate-200/70 bg-white/95 p-5 shadow-soft backdrop-blur-sm motion-safe:animate-fade-up motion-safe:[animation-delay:180ms] motion-reduce:animate-none transition duration-300 hover:border-slate-200 hover:shadow-lift"
  >
    <div
      class="pointer-events-none absolute -left-4 -bottom-6 h-28 w-28 rounded-full bg-purple-400/10 blur-2xl"
      aria-hidden="true"
    />
    <h3 class="relative mb-4 font-display text-base font-semibold text-slate-900">Phân bố theo danh mục</h3>
    <div class="relative h-72">
      <Pie v-if="chartData.labels?.length" :data="chartData" :options="options" />
      <p v-else class="py-12 text-center text-sm text-slate-500">Chưa có dữ liệu.</p>
    </div>
  </div>
</template>
