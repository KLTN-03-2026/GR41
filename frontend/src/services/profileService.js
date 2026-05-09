import http from './http'

export const profileService = {
  getProfile: () => http.get('/profile').then((r) => r.data),
  updateProfile: (payload) => http.put('/profile', payload).then((r) => r.data),
  updateAvatar: (payload) => http.post('/profile/avatar', payload).then((r) => r.data),
  changePassword: (payload) => http.post('/profile/change-password', payload).then((r) => r.data),
  favorites: (params) => http.get('/profile/favorites', { params }).then((r) => r.data),
  history: (params) => http.get('/profile/history', { params }).then((r) => r.data),
  removeFavorite: (id) => http.delete(`/profile/favorites/${id}`).then((r) => r.data),
}
