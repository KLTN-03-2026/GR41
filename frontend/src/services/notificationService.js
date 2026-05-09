import http from './http'
import { unwrapList } from '@/utils/apiHelpers'

export const notificationService = {
  async list(params) {
    const r = await http.get('/notifications', { params })
    const payload = r.data
    const meta = r._meta?.meta || r._meta || {}
    const items = unwrapList(payload)
    const unread =
      meta.unread_count ??
      meta.total_unread ??
      r._meta?.unread_count ??
      (Array.isArray(items) ? items.filter((x) => x && x.is_read === false).length : 0)
    return { items, unread_count: Number(unread) || 0, meta }
  },
  markRead: (id) => http.patch(`/notifications/${id}/read`).then((r) => r.data),
  markAllRead: () => http.post('/notifications/read-all').then((r) => r.data),
}
