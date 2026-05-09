import { ref, watch } from 'vue'

export function useDebouncedRef(value, delay = 300) {
  const debounced = ref(value)
  let timer = null
  watch(
    () => value,
    (v) => {
      clearTimeout(timer)
      timer = setTimeout(() => {
        debounced.value = v
      }, delay)
    },
  )
  return debounced
}

export function debounce(fn, delay = 300) {
  let t = null
  return (...args) => {
    clearTimeout(t)
    t = setTimeout(() => fn(...args), delay)
  }
}
