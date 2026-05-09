/** Chuẩn hóa response list từ Laravel (array hoặc { data: [] }) */
export function unwrapList(payload) {
  if (!payload) return []
  if (Array.isArray(payload)) return payload
  if (Array.isArray(payload.data)) return payload.data
  if (Array.isArray(payload.items)) return payload.items
  return []
}

export function unwrapMeta(payload) {
  if (!payload || typeof payload !== 'object') return {}
  return payload.meta || payload.pagination || {}
}
