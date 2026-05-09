export function formatDate(input, opts = {}) {
  if (!input) return '—'
  const d = typeof input === 'string' || typeof input === 'number' ? new Date(input) : input
  if (Number.isNaN(d.getTime())) return '—'
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    ...opts,
  }).format(d)
}

export function formatDateTime(input) {
  if (!input) return '—'
  const d = new Date(input)
  if (Number.isNaN(d.getTime())) return '—'
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(d)
}

export function formatNumber(n) {
  if (n == null || Number.isNaN(Number(n))) return '0'
  return new Intl.NumberFormat('vi-VN').format(Number(n))
}

/** Trả về các đoạn text để highlight keyword (an toàn — không dùng innerHTML trực tiếp với user HTML) */
export function splitByKeyword(text, keyword) {
  if (!text) return [{ text: '', highlight: false }]
  const k = (keyword || '').trim()
  if (!k) return [{ text: String(text), highlight: false }]
  const lower = String(text).toLowerCase()
  const kl = k.toLowerCase()
  const parts = []
  let start = 0
  let idx = lower.indexOf(kl, start)
  while (idx !== -1) {
    if (idx > start) parts.push({ text: String(text).slice(start, idx), highlight: false })
    parts.push({ text: String(text).slice(idx, idx + k.length), highlight: true })
    start = idx + k.length
    idx = lower.indexOf(kl, start)
  }
  if (start < text.length) parts.push({ text: String(text).slice(start), highlight: false })
  return parts.length ? parts : [{ text: String(text), highlight: false }]
}
