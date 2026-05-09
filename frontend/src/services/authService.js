import http from './http'

export const authService = {
  login: (payload) => http.post('/auth/login', payload).then((r) => r.data),
  register: (payload) => http.post('/auth/register', payload).then((r) => r.data),
  forgotPassword: (email) => http.post('/auth/forgot-password', { email }).then((r) => r.data),
  resetPassword: (payload) => http.post('/auth/reset-password', payload).then((r) => r.data),
  me: () => http.get('/auth/me').then((r) => r.data),
  logout: () => http.post('/auth/logout').then((r) => r.data),
}
