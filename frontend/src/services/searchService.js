import http from './http'

export const searchService = {
  search: (params) => http.get('/search', { params }).then((r) => r.data),
  suggestions: (q) =>
    http.get('/search/suggestions', { params: { q } }).then((r) => r.data),
  trending: () => http.get('/search/trending').then((r) => r.data),
}
