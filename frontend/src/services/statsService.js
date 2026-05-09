import http from './http'

export const statsService = {
  overview: () => http.get('/admin/stats/overview').then((r) => r.data),
  charts: () => http.get('/admin/stats/charts').then((r) => r.data),
  trendingKeywords: () => http.get('/admin/stats/top-keywords').then((r) => r.data),
}
