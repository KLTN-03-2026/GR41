import http from './http'

export const adminChatbotService = {
  intents: (params) => http.get('/admin/chatbot/intents', { params }).then((r) => r.data),
  intentDetail: (id) => http.get(`/admin/chatbot/intents/${id}`).then((r) => r.data),
  createIntent: (payload) => http.post('/admin/chatbot/intents', payload).then((r) => r.data),
  updateIntent: (id, payload) => http.put(`/admin/chatbot/intents/${id}`, payload).then((r) => r.data),
  deleteIntent: (id) => http.delete(`/admin/chatbot/intents/${id}`).then((r) => r.data),

  logs: (params) => http.get('/admin/chatbot/logs', { params }).then((r) => r.data),
}

export const adminBroadcastService = {
  broadcast: (payload) => http.post('/admin/notifications/broadcast', payload).then((r) => r.data),
}
