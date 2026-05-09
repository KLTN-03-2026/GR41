import http from './http'

export const metaService = {
  roles: () => http.get('/roles').then((r) => r.data),
}
