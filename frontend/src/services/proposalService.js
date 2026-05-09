import http from './http'

export const proposalService = {
  // Teacher
  list: (params) => http.get('/teacher/proposals', { params }),
  create: (payload) => http.post('/teacher/proposals', payload),
  delete: (id) => http.delete(`/teacher/proposals/${id}`),

  // Admin
  adminList: (params) => http.get('/admin/proposals', { params }),
  adminPendingCount: () => http.get('/admin/proposals/pending-count'),
  adminShow: (id) => http.get(`/admin/proposals/${id}`),
  adminApprove: (id) => http.post(`/admin/proposals/${id}/approve`),
  adminReject: (id, payload) => http.post(`/admin/proposals/${id}/reject`, payload),
}
