<script setup>
import { computed } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
  data: { type: Object, default: null },
})

const chartData = computed(() => {
  const docs = props.data?.top_documents || props.data?.bar_top_docs || []
  const labels = docs.map((d) => d.title || d.name || '—').slice(0, 10)
  const values = docs.map((d) => d.views ?? d.view_count ?? 0).slice(0, 10)
  return {
    labels,
    datasets: [
      {
        label: 'Lượt xem',
        data: values,
        backgroundColor: 'rgba(16, 185, 129, 0.65)',
        borderRadius: 6,
      },
    ],
  }
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y',
  plugins: { legend: { display: false } },
  scales: {
    x: { beginAtZero: true },
  },
}
</script>

<template>
  <div
    class="group relative overflow-hidden rounded-2xl border border-slate-200/70 bg-white/95 p-5 shadow-soft backdrop-blur-sm lg:col-span-2 motion-safe:animate-fade-up motion-safe:[animation-delay:240ms] motion-reduce:animate-none transition duration-300 hover:border-slate-200 hover:shadow-lift"
  >
    <div
      class="pointer-events-none absolute right-1/4 -top-8 h-20 w-40 rounded-full bg-emerald-400/10 blur-2xl"
      aria-hidden="true"
    />
    <h3 class="relative mb-4 font-display text-base font-semibold text-slate-900">Top tài liệu xem nhiều</h3>
    <div class="relative h-96">
      <Bar v-if="chartData.labels?.length" :data="chartData" :options="options" />
      <p v-else class="py-12 text-center text-sm text-slate-500">Chưa có dữ liệu.</p>
    </div>
  </div>
</template>
