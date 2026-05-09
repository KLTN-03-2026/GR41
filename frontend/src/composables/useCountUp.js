import { ref, onUnmounted } from 'vue'

/**
 * Đếm tăng dần từ 0 đến endValue trong duration ms (easing cubic-out).
 * Gọi start() khi vào viewport hoặc khi cần.
 */
export function useCountUp(endValue, duration = 1500) {
  const current = ref(0)
  let raf = 0
  let ran = false

  function start() {
    if (ran) return
    ran = true
    const end = Math.max(0, Number(endValue) || 0)
    const t0 = performance.now()

    function frame(now) {
      const t = Math.min(1, (now - t0) / duration)
      const eased = 1 - (1 - t) ** 3
      current.value = Math.round(end * eased)
      if (t < 1) raf = requestAnimationFrame(frame)
      else current.value = end
    }
    raf = requestAnimationFrame(frame)
  }

  onUnmounted(() => cancelAnimationFrame(raf))
  return { current, start }
}
