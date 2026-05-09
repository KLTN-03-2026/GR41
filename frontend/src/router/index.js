import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/',
    component: () => import('@/layouts/DefaultLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('@/views/public/HomeView.vue') },
      { path: 'search', name: 'search', component: () => import('@/views/public/SearchView.vue') },
      {
        path: 'documents/:slug',
        name: 'document.detail',
        component: () => import('@/views/public/DocumentDetailView.vue'),
      },
      {
        path: 'categories/:slug',
        name: 'category',
        component: () => import('@/views/public/CategoryView.vue'),
      },
      {
        path: 'profile',
        name: 'profile',
        component: () => import('@/views/user/ProfileView.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'profile/favorites',
        name: 'favorites',
        component: () => import('@/views/user/FavoritesView.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'profile/history',
        name: 'history',
        component: () => import('@/views/user/HistoryView.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'profile/change-password',
        name: 'change-password',
        component: () => import('@/views/user/ChangePasswordView.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'notifications',
        name: 'notifications',
        component: () => import('@/views/user/NotificationsView.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },

  {
    path: '/login',
    component: () => import('@/layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'login',
        component: () => import('@/views/public/LoginView.vue'),
      },
    ],
  },
  {
    path: '/register',
    component: () => import('@/layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'register',
        component: () => import('@/views/public/RegisterView.vue'),
      },
    ],
  },
  {
    path: '/forgot-password',
    component: () => import('@/layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'forgot-password',
        component: () => import('@/views/public/ForgotPasswordView.vue'),
      },
    ],
  },
  {
    path: '/reset-password',
    component: () => import('@/layouts/AuthLayout.vue'),
    children: [
      {
        path: '',
        name: 'reset-password',
        component: () => import('@/views/public/ResetPasswordView.vue'),
      },
    ],
  },

  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: '',
        name: 'admin.dashboard',
        component: () => import('@/views/admin/AdminDashboardView.vue'),
      },
      {
        path: 'documents',
        name: 'admin.documents',
        component: () => import('@/views/admin/AdminDocumentsView.vue'),
      },
      {
        path: 'documents/new',
        name: 'admin.documents.new',
        component: () => import('@/views/admin/AdminDocumentFormView.vue'),
      },
      {
        path: 'documents/:id/edit',
        name: 'admin.documents.edit',
        component: () => import('@/views/admin/AdminDocumentFormView.vue'),
      },
      {
        path: 'users',
        name: 'admin.users',
        component: () => import('@/views/admin/AdminUsersView.vue'),
      },
      {
        path: 'categories',
        name: 'admin.categories',
        component: () => import('@/views/admin/AdminCategoriesView.vue'),
      },
      {
        path: 'tags',
        name: 'admin.tags',
        component: () => import('@/views/admin/AdminTagsView.vue'),
      },
      {
        path: 'chatbot/intents',
        name: 'admin.chatbot.intents',
        component: () => import('@/views/admin/AdminChatbotIntentsView.vue'),
      },
      {
        path: 'chatbot/logs',
        name: 'admin.chatbot.logs',
        component: () => import('@/views/admin/AdminChatbotLogsView.vue'),
      },
      {
        path: 'broadcast',
        name: 'admin.broadcast',
        component: () => import('@/views/admin/AdminBroadcastView.vue'),
      },
      {
        path: 'proposals',
        name: 'admin.proposals',
        component: () => import('@/views/admin/AdminProposalsView.vue'),
      },
    ],
  },

  {
    path: '/teacher',
    component: () => import('@/layouts/DefaultLayout.vue'),
    meta: { requiresAuth: true, role: 'teacher' },
    children: [
      {
        path: 'proposals',
        name: 'teacher.proposals',
        component: () => import('@/views/teacher/TeacherProposalsView.vue'),
      },
    ],
  },

  { path: '/forbidden', name: 'forbidden', component: () => import('@/views/error/ForbiddenView.vue') },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('@/views/error/NotFoundView.vue') },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0, behavior: 'smooth' }
  },
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
  if (to.meta.role === 'admin' && !auth.isAdmin) {
    return { name: 'forbidden' }
  }
  if (to.meta.role === 'teacher' && !auth.isTeacher) {
    return { name: 'forbidden' }
  }
})

export default router
