import { ref, computed } from 'vue'

export function usePagination(initialPage = 1, perPage = 12) {
  const page = ref(initialPage)
  const perPageRef = ref(perPage)
  const total = ref(0)

  const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPageRef.value)))

  const setTotal = (n) => {
    total.value = n
  }

  const next = () => {
    if (page.value < totalPages.value) page.value += 1
  }
  const prev = () => {
    if (page.value > 1) page.value -= 1
  }

  return {
    page,
    perPage: perPageRef,
    total,
    totalPages,
    setTotal,
    next,
    prev,
  }
}
