import http from './http'

export const chatbotService = {
  suggestions: () => http.get('/chatbot/suggestions').then((r) => r.data),
  ask: (payload) => http.post('/chatbot/ask', payload).then((r) => r.data),
}
