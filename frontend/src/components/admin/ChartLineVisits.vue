<script setup>
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler)

const props = defineProps({
  data: { type: Object, default: null },
})

const chartData = computed(() => {
  const visits = props.data?.visits_30d || props.data?.visits || props.data?.line_visits || []
  const labels = visits.map((v) => v.date || v.label || v.day).slice(-30)
  const values = visits.map((v) => v.count ?? v.views ?? v.value ?? 0).slice(-30)
  return {
    labels,
    datasets: [
      {
        label: 'Lượt truy cập',
        data: values,
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37, 99, 235, 0.1)',
        tension: 0.35,
        fill: true,
      },
    ],
  }
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { display: false } },
    y: { beginAtZero: true },
  },
}
</script>

<template>
  <div
    class="group relative overflow-hidden rounded-2xl border border-slate-200/70 bg-white/95 p-5 shadow-soft backdrop-blur-sm motion-safe:animate-fade-up motion-safe:[animation-delay:120ms] motion-reduce:animate-none transition duration-300 hover:border-slate-200 hover:shadow-lift"
  >
    <div
      class="pointer-events-none absolute -right-6 top-0 h-24 w-24 rounded-full bg-brand-400/10 blur-2xl transition group-hover:bg-brand-400/15"
      aria-hidden="true"
    />
    <h3 class="relative mb-4 font-display text-base font-semibold text-slate-900">Lượt truy cập 30 ngày</h3>
    <div class="relative h-72">
      <Line v-if="chartData.labels?.length" :data="chartData" :options="options" />
      <p v-else class="py-12 text-center text-sm text-slate-500">Chưa có dữ liệu biểu đồ.</p>
    </div>
  </div>
</template>
