import http from './http'

export const tagService = {
  list: (params) => http.get('/tags', { params }).then((r) => r.data),

  adminList: (params) => http.get('/admin/tags', { params }).then((r) => r.data),
  adminCreate: (payload) => http.post('/admin/tags', payload).then((r) => r.data),
  adminUpdate: (id, payload) => http.put(`/admin/tags/${id}`, payload).then((r) => r.data),
  adminDelete: (id) => http.delete(`/admin/tags/${id}`).then((r) => r.data),
}
