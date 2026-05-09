import http from './http'

export const adminUserService = {
  list: (params) => http.get('/admin/users', { params }).then((r) => r.data),
  detail: (id) => http.get(`/admin/users/${id}`).then((r) => r.data),
  create: (payload) => http.post('/admin/users', payload).then((r) => r.data),
  update: (id, payload) => http.put(`/admin/users/${id}`, payload).then((r) => r.data),
  delete: (id) => http.delete(`/admin/users/${id}`).then((r) => r.data),
  updateStatus: (id, payload) =>
    http.patch(`/admin/users/${id}/status`, payload).then((r) => r.data),
}
