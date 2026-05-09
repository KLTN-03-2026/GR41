import http from './http'

export const documentService = {
  list: (params) => http.get('/documents', { params }).then((r) => r.data),
  featured: () => http.get('/documents/featured').then((r) => r.data),
  popular: () => http.get('/documents/popular').then((r) => r.data),
  recent: () => http.get('/documents/recent').then((r) => r.data),
  recommended: () => http.get('/documents/recommended').then((r) => r.data),
  detail: (slug) => http.get(`/documents/${slug}`).then((r) => r.data),
  related: (id) => http.get(`/documents/${id}/related`).then((r) => r.data),
  download: (id) => http.get(`/documents/${id}/download`).then((r) => r.data),
  toggleFavorite: (id) => http.post(`/documents/${id}/favorite`).then((r) => r.data),
  rate: (id, payload) => http.post(`/documents/${id}/rate`, payload).then((r) => r.data),

  adminList: (params) => http.get('/admin/documents', { params }).then((r) => r.data),
  adminDetail: (id) => http.get(`/admin/documents/${id}`).then((r) => r.data),
  adminCreate: (payload) => http.post('/admin/documents', payload).then((r) => r.data),
  adminUpdate: (id, payload) => http.put(`/admin/documents/${id}`, payload).then((r) => r.data),
  adminDelete: (id) => http.delete(`/admin/documents/${id}`).then((r) => r.data),
}
