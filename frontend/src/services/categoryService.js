import http from './http'

export const categoryService = {
  tree: () => http.get('/categories').then((r) => r.data),
  list: (params) => http.get('/categories', { params }).then((r) => r.data),
  detail: (slug) => http.get(`/categories/${slug}`).then((r) => r.data),

  adminList: (params) => http.get('/admin/categories', { params }).then((r) => r.data),
  adminCreate: (payload) => http.post('/admin/categories', payload).then((r) => r.data),
  adminUpdate: (id, payload) => http.put(`/admin/categories/${id}`, payload).then((r) => r.data),
  adminDelete: (id) => http.delete(`/admin/categories/${id}`).then((r) => r.data),
}
