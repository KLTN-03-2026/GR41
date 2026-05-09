# Instruction — Frontend Project: Tri Thức Số

> Tài liệu kỹ thuật nội bộ cho developer mới. Đọc từ đầu đến cuối trước khi bắt đầu code.

---

## Mục lục

1. [Giới thiệu Project](#1-giới-thiệu-project)
2. [Tech Stack](#2-tech-stack)
3. [Yêu cầu môi trường](#3-yêu-cầu-môi-trường)
4. [Cài đặt và chạy local](#4-cài-đặt-và-chạy-local)
5. [Các script trong package.json](#5-các-script-trong-packagejson)
6. [Cấu trúc thư mục](#6-cấu-trúc-thư-mục)
7. [Luồng khởi tạo ứng dụng](#7-luồng-khởi-tạo-ứng-dụng)
8. [Routing & Navigation Guards](#8-routing--navigation-guards)
9. [State Management (Pinia stores)](#9-state-management-pinia-stores)
10. [HTTP & API Layer](#10-http--api-layer)
11. [Form Validation](#11-form-validation)
12. [UI Components & Styling](#12-ui-components--styling)
13. [Tích hợp Backend API](#13-tích-hợp-backend-api)
14. [Bảng các thư viện đã cài](#14-bảng-các-thư-viện-đã-cài)
15. [Build & Deploy](#15-build--deploy)
16. [Convention & Code Style](#16-convention--code-style)
17. [Troubleshooting thường gặp](#17-troubleshooting-thường-gặp)
18. [Onboarding checklist cho dev mới](#18-onboarding-checklist-cho-dev-mới)
19. [⚠️ Phát hiện cần lưu ý](#️-phát-hiện-cần-lưu-ý)

---

## 1. Giới thiệu Project

**Tri Thức Số** là nền tảng thư viện tài liệu số dành cho sinh viên, giáo viên và người học tại Việt Nam. Người dùng có thể tra cứu, đọc online, tải xuống, đánh giá tài liệu, lưu yêu thích và nhận gợi ý cá nhân hóa. Hệ thống có chatbot hỗ trợ tìm kiếm, phân quyền 3 vai trò (admin / teacher / student), và trang quản trị riêng cho admin.

- **Live demo**: https://trithucso.oceanmind.id.vn
- **Backend API**: https://api.trithucso.oceanmind.id.vn/api/v1
- **Backend local (dev)**: http://localhost:8000/api/v1 (proxy qua Vite)

---

## 2. Tech Stack

| Công nghệ              | Version  | Mục đích sử dụng                                             |
| ---------------------- | -------- | ------------------------------------------------------------ |
| Vue                    | ^3.5.32  | Framework UI chính                                           |
| Vite                   | ^5.4.21  | Build tool, dev server                                       |
| JavaScript (ES Module) | ES2022+  | Ngôn ngữ (không dùng TypeScript)                             |
| Tailwind CSS           | ^3.4.19  | Utility-first CSS framework                                  |
| PrimeVue               | ^4.5.5   | UI component library (Aura theme)                            |
| @primevue/themes       | ^4.5.4   | Theme preset cho PrimeVue                                    |
| Pinia                  | ^3.0.4   | State management                                             |
| Vue Router             | ^4.6.4   | Client-side routing (history mode)                           |
| Axios                  | ^1.15.2  | HTTP client                                                  |
| @tanstack/vue-query    | ^5.100.6 | Server state & caching (useQuery, useMutation)               |
| vee-validate           | ^4.15.1  | Form validation                                              |
| @vee-validate/yup      | ^4.15.1  | Adapter kết nối vee-validate với Yup                         |
| yup                    | ^1.7.1   | Schema validation                                            |
| vue-sonner             | ^2.0.9   | Toast notification                                           |
| primeicons             | ^7.0.0   | Icon set cho PrimeVue components                             |
| @iconify/vue           | ^5.0.0   | Icon library tổng hợp (MDI, v.v.)                            |
| chart.js               | ^4.5.1   | Thư viện vẽ chart                                            |
| vue-chartjs            | ^5.3.3   | Wrapper Vue cho Chart.js                                     |
| @vueuse/core           | ^11.3.0  | Composables tiện ích (useWindowScroll, onClickOutside, v.v.) |
| autoprefixer           | ^10.5.0  | PostCSS plugin tự thêm vendor prefix                         |
| postcss                | ^8.5.12  | CSS transformer (dùng với Tailwind)                          |

---

## 3. Yêu cầu môi trường

- **Node.js**: >= 18.x (khuyến nghị 20.x LTS). Kiểm tra: `node -v`
- **Package manager**: npm (đi kèm Node). Không dùng yarn/pnpm.
- **Trình duyệt hỗ trợ**: Chrome 90+, Firefox 88+, Edge 90+, Safari 14+. Không hỗ trợ IE.
- **OS**: Windows / macOS / Linux — lệnh giống nhau, không có sự khác biệt.

---

## 4. Cài đặt và chạy local

```bash
# 1. Clone repository
git clone <repo-url>
cd TriThucSo/frontend

# 2. Cài dependencies
npm install

# 3. Tạo file .env từ mẫu dưới đây (chưa có .env.example, xem bảng bên dưới)
cp .env .env.local   # hoặc tạo tay

# 4. Chạy dev server (port 5173, proxy /api → localhost:8000)
npm run dev

# 5. Build production
npm run build

# 6. Preview build local (sau khi build)
npm run preview
```

### Biến môi trường `.env`

| Biến                            | Giá trị mẫu (local)            | Bắt buộc  | Mục đích                                                                                   |
| ------------------------------- | ------------------------------ | --------- | ------------------------------------------------------------------------------------------ |
| `VITE_SITE_URL`                 | `http://localhost:5173`        | Có        | URL frontend — dùng inject vào `<link rel="canonical">` trong `index.html` qua Vite plugin |
| `VITE_APP_URL`                  | `http://localhost:5173`        | Có        | Base URL app — dùng ghép link reset password khi gửi email từ client                       |
| `VITE_API_BASE_URL`             | `http://localhost:8000/api/v1` | Có        | Base URL gọi backend API, import bởi `src/services/http.js`                                |
| `VITE_CLOUDINARY_CLOUD_NAME`    | `your_cloud_name`              | Có        | Cloudinary cloud name — dùng upload media                                                  |
| `VITE_CLOUDINARY_UPLOAD_PRESET` | `your_upload_preset`           | Có        | Cloudinary unsigned upload preset                                                          |
| `VITE_CLOUDINARY_API_KEY`       | `your_api_key`                 | Không     | Cloudinary API key (chưa dùng trong code upload)                                           |
| `VITE_CLOUDINARY_API_SECRET`    | _(để trống)_                   | **Không** | ⚠️ KHÔNG đặt API secret trong frontend — xem [mục 19](#️-phát-hiện-cần-lưu-ý)               |

> **Lưu ý**: Tất cả biến `VITE_*` được Vite inject lúc build-time thành giá trị tĩnh. Sau khi build, không thể thay đổi mà không build lại.

**Cấu hình `.env` cho local development:**

```env
VITE_SITE_URL=http://localhost:5173
VITE_APP_URL=http://localhost:5173
VITE_API_BASE_URL=http://localhost:8000/api/v1
VITE_CLOUDINARY_CLOUD_NAME=your_cloud_name
VITE_CLOUDINARY_UPLOAD_PRESET=your_preset
```

---

## 5. Các script trong package.json

| Script    | Lệnh           | Khi nào dùng                | Output                                                                                       |
| --------- | -------------- | --------------------------- | -------------------------------------------------------------------------------------------- |
| `dev`     | `vite`         | Phát triển hàng ngày        | Dev server tại http://localhost:5173, HMR bật, sourcemap bật                                 |
| `build`   | `vite build`   | Deploy production           | Tạo thư mục `dist/` với các file tĩnh đã minify (hiện đang tắt minify tạm thời — xem mục 19) |
| `preview` | `vite preview` | Kiểm tra build trước deploy | Serve `dist/` tại http://localhost:4173                                                      |

---

## 6. Cấu trúc thư mục

```
frontend/
├── index.html                  # Entry HTML, chứa SEO meta, font, JSON-LD
├── vite.config.js              # Vite config: alias @/, proxy /api, custom plugin inject VITE_SITE_URL
├── tailwind.config.js          # Tailwind: custom colors (brand, ink, surface, accent), fonts, animations
├── postcss.config.js           # PostCSS: autoprefixer
├── package.json
├── .env                        # Biến môi trường (KHÔNG commit secret thực)
└── src/
    ├── main.js                 # Entry point: khởi tạo app, đăng ký plugins
    ├── App.vue                 # Root component: <RouterView> + <Toaster>
    ├── assets/
    │   ├── images/             # Logo (logo.png, logo-text.png, icon-logo.png, icon-logo-removebg.png)
    │   ├── hero.png            # Ảnh hero section
    │   └── styles/
    │       └── main.css        # Global CSS: Tailwind base/components/utilities + custom scrollbar
    ├── components/             # 43 reusable components (xem bảng dưới)
    │   ├── admin/              # 10 components: StatsCard, Charts, AdminSurface, Dialogs
    │   ├── chatbot/            # 3 components: ChatbotWidget, ChatMessage, ChatTyping
    │   ├── common/             # 10 components: AppHeader, AppFooter, AppSidebar, Pagination, ...
    │   ├── document/           # 6 components: DocumentCard, DocumentList, DocumentSlider, ...
    │   ├── home/               # 8 components: HeroSection, FeaturedCarousel, StatsBanner, ...
    │   ├── notification/       # 2 components: NotificationBell, NotificationItem
    │   ├── search/             # 3 components: SearchBar, SearchResults, TrendingKeywords
    │   └── teacher/            # 1 component: TeacherProposalFormDialog
    ├── composables/            # 6 Vue Composition API hooks tái sử dụng
    │   ├── useAuth.js          # Wrapper trên authStore, expose login/logout/user/role
    │   ├── useCountUp.js       # Đếm số animate từ 0 → n (dùng trong StatsBanner)
    │   ├── useDebounce.js      # useDebouncedRef() + debounce() utility
    │   ├── usePagination.js    # Quản lý state phân trang (page, perPage, total, totalPages)
    │   ├── useToast.js         # Wrapper vue-sonner (success, error, info)
    │   └── useUploadImage.js   # Upload file lên Cloudinary qua useMutation
    ├── constants/
    │   └── index.js            # API_BASE_URL, TOKEN_KEY, USER_KEY, CHAT_STORAGE_KEY, ROLES, ROUTES, EMAILJS_CONFIG
    ├── layouts/
    │   ├── DefaultLayout.vue   # Layout public: AppHeader + main + AppFooter + ScrollToTop + ChatbotWidget
    │   ├── AdminLayout.vue     # Layout admin: AppSidebar + breadcrumb bar + main
    │   └── AuthLayout.vue      # Layout auth: animated background + glassmorphism card
    ├── router/
    │   └── index.js            # 27 routes, createWebHistory, beforeEach guard (auth + role)
    ├── services/               # 14 service modules + http.js
    │   ├── http.js             # Axios instance: baseURL, Bearer token interceptor, 401/403/500 handler
    │   ├── authService.js      # login, register, forgotPassword, resetPassword, me, logout
    │   ├── documentService.js  # list, featured, popular, recent, recommended, detail, download, toggleFavorite, rate, admin*
    │   ├── categoryService.js  # tree, list, detail, admin*
    │   ├── tagService.js       # list, admin*
    │   ├── searchService.js    # search, suggestions, trending
    │   ├── profileService.js   # getProfile, updateProfile, updateAvatar, changePassword, favorites, history
    │   ├── notificationService.js # list, markRead, markAllRead
    │   ├── chatbotService.js   # suggestions, ask
    │   ├── adminUserService.js # list, detail, create, update, delete, updateStatus
    │   ├── adminChatbotService.js # intents CRUD, logs + adminBroadcastService
    │   ├── proposalService.js  # teacher: list/create/delete; admin: list/count/show/approve/reject
    │   ├── statsService.js     # overview, charts, trendingKeywords (admin dashboard)
    │   ├── metaService.js      # roles()
    │   └── sendMailService.js  # EmailJS client-side: sendPasswordResetEmail, sendRegistrationOtpEmail
    ├── stores/                 # 3 Pinia stores
    │   ├── auth.js             # user, token, isLoggedIn, isAdmin, isTeacher + login/logout/register/fetchMe
    │   ├── notification.js     # unreadCount, setUnreadCount
    │   └── ui.js               # sidebarCollapsed, theme, toggleSidebar
    ├── utils/
    │   ├── apiHelpers.js       # unwrapList(), unwrapMeta() — chuẩn hóa response Laravel
    │   ├── categoryTheme.js    # getCategoryTheme() — gán màu Tailwind + icon theo tên category
    │   ├── chatMarkdown.js     # renderChatMessage() — escape HTML + render link trong chat
    │   ├── formatters.js       # formatDate, formatDateTime, formatNumber, splitByKeyword
    │   └── validators.js       # Yup schemas: loginSchema, registerSchema, changePasswordSchema
    └── views/                  # 25 page-level components
        ├── public/             # 8 views: HomeView, SearchView, DocumentDetailView, CategoryView,
        │                       #          LoginView, RegisterView, ForgotPasswordView, ResetPasswordView
        ├── user/               # 5 views: ProfileView, FavoritesView, HistoryView,
        │                       #          ChangePasswordView, NotificationsView
        ├── admin/              # 10 views: Dashboard, Documents, DocumentForm, Users,
        │                       #           Categories, Tags, ChatbotIntents, ChatbotLogs,
        │                       #           Broadcast, Proposals
        ├── teacher/            # 1 view: TeacherProposalsView
        └── error/              # 2 views: ForbiddenView (403), NotFoundView (404)
```

---

## 7. Luồng khởi tạo ứng dụng

```
Browser load index.html
    │
    ├─ Load fonts (Google Fonts: Inter + Plus Jakarta Sans)
    ├─ Load /src/main.js (type="module")
    │
    ▼
main.js
    ├── createApp(App)
    ├── app.use(createPinia())           ← 1. Pinia (state management)
    ├── app.use(router)                  ← 2. Vue Router
    ├── app.use(VueQueryPlugin, {...})   ← 3. TanStack Vue Query (staleTime: 60s, no refetch on focus)
    ├── app.use(PrimeVue, {             ← 4. PrimeVue (ripple bật, theme: Aura, darkModeSelector: '.dark-mode')
    │       theme: { preset: Aura }
    │   })
    ├── app.use(ToastService)            ← 5. PrimeVue ToastService
    ├── app.directive('tooltip', Tooltip) ← 6. Tooltip directive
    └── app.mount('#app')
            │
            ▼
        App.vue
            ├── <RouterView />          ← Router render layout/view
            └── <Toaster />             ← vue-sonner global toast container
```

**Sơ đồ render route:**

```
URL thay đổi
    │
    ▼
router.beforeEach()
    ├── requiresAuth? → kiểm tra auth.isLoggedIn → nếu không → redirect /login?redirect=<path>
    ├── role === 'admin'? → kiểm tra auth.isAdmin → nếu không → redirect /forbidden
    └── role === 'teacher'? → kiểm tra auth.isTeacher → nếu không → redirect /forbidden
    │
    ▼
Resolve layout component (DefaultLayout / AdminLayout / AuthLayout)
    │
    ▼
Render child view component trong <RouterView> của layout
```

---

## 8. Routing & Navigation Guards

Vue Router 4.6.4, history mode (`createWebHistory`). Tổng cộng **27 routes**.

| Path                        | Name                    | Layout  | Auth  | Role      | Mô tả                                              |
| --------------------------- | ----------------------- | ------- | ----- | --------- | -------------------------------------------------- |
| `/`                         | `home`                  | Default | Không | —         | Trang chủ (featured, popular, recent, recommended) |
| `/search`                   | `search`                | Default | Không | —         | Tìm kiếm tài liệu                                  |
| `/documents/:slug`          | `document.detail`       | Default | Không | —         | Chi tiết tài liệu                                  |
| `/categories/:slug`         | `category`              | Default | Không | —         | Danh mục tài liệu                                  |
| `/profile`                  | `profile`               | Default | Có    | —         | Hồ sơ người dùng                                   |
| `/profile/favorites`        | `favorites`             | Default | Có    | —         | Tài liệu yêu thích                                 |
| `/profile/history`          | `history`               | Default | Có    | —         | Lịch sử xem                                        |
| `/profile/change-password`  | `change-password`       | Default | Có    | —         | Đổi mật khẩu                                       |
| `/notifications`            | `notifications`         | Default | Có    | —         | Thông báo                                          |
| `/login`                    | `login`                 | Auth    | Không | —         | Đăng nhập                                          |
| `/register`                 | `register`              | Auth    | Không | —         | Đăng ký                                            |
| `/forgot-password`          | `forgot-password`       | Auth    | Không | —         | Quên mật khẩu                                      |
| `/reset-password`           | `reset-password`        | Auth    | Không | —         | Đặt lại mật khẩu (qua link email)                  |
| `/admin`                    | `admin.dashboard`       | Admin   | Có    | `admin`   | Tổng quan admin                                    |
| `/admin/documents`          | `admin.documents`       | Admin   | Có    | `admin`   | Danh sách tài liệu                                 |
| `/admin/documents/new`      | `admin.documents.new`   | Admin   | Có    | `admin`   | Thêm tài liệu mới                                  |
| `/admin/documents/:id/edit` | `admin.documents.edit`  | Admin   | Có    | `admin`   | Sửa tài liệu                                       |
| `/admin/users`              | `admin.users`           | Admin   | Có    | `admin`   | Quản lý người dùng                                 |
| `/admin/categories`         | `admin.categories`      | Admin   | Có    | `admin`   | Quản lý danh mục                                   |
| `/admin/tags`               | `admin.tags`            | Admin   | Có    | `admin`   | Quản lý tags                                       |
| `/admin/chatbot/intents`    | `admin.chatbot.intents` | Admin   | Có    | `admin`   | Chatbot intents CRUD                               |
| `/admin/chatbot/logs`       | `admin.chatbot.logs`    | Admin   | Có    | `admin`   | Lịch sử chat                                       |
| `/admin/broadcast`          | `admin.broadcast`       | Admin   | Có    | `admin`   | Gửi thông báo hàng loạt                            |
| `/admin/proposals`          | `admin.proposals`       | Admin   | Có    | `admin`   | Duyệt đề xuất tài liệu                             |
| `/teacher/proposals`        | `teacher.proposals`     | Default | Có    | `teacher` | Đề xuất tài liệu (teacher)                         |
| `/forbidden`                | `forbidden`             | Không   | —     | —         | Trang 403                                          |
| `/:pathMatch(.*)`           | `not-found`             | Không   | —     | —         | Trang 404                                          |

### Guard Logic (`src/router/index.js:188`)

```js
router.beforeEach((to) => {
  const auth = useAuthStore();
  // Kiểm tra đăng nhập — redirect về login kèm ?redirect= để sau login quay lại đúng trang
  if (to.meta.requiresAuth && !auth.isLoggedIn)
    return { name: "login", query: { redirect: to.fullPath } };
  // Kiểm tra role admin
  if (to.meta.role === "admin" && !auth.isAdmin) return { name: "forbidden" };
  // Kiểm tra role teacher
  if (to.meta.role === "teacher" && !auth.isTeacher)
    return { name: "forbidden" };
});
```

**Scroll behavior**: Luôn scroll lên top khi navigate, trừ khi có `savedPosition` (back/forward của browser) — smooth scroll.

---

## 9. State Management (Pinia stores)

### `src/stores/auth.js` — Store quan trọng nhất

|                 | Chi tiết                                                                                                   |
| --------------- | ---------------------------------------------------------------------------------------------------------- |
| **State**       | `user` (ref, object hoặc null), `token` (ref, string)                                                      |
| **Getters**     | `isLoggedIn` (!!token), `isAdmin` (user.role.slug === 'admin'), `isTeacher` (user.role.slug === 'teacher') |
| **Actions**     | `login(email, password)`, `register(payload)`, `fetchMe()`, `logout(redirect?)`, `forceLogout()`           |
| **Persistence** | localStorage: token → key `tts_token`, user → key `tts_user`                                               |

**Luồng đăng nhập:**

```
user submit form
    │
    ▼
auth.login(email, password)
    ├── POST /auth/login
    ├── token.value = res.data.token
    ├── user.value = res.data.user
    ├── localStorage.setItem('tts_token', token)
    └── localStorage.setItem('tts_user', JSON.stringify(user))
    │
    ▼
router.push(redirect || '/')
```

**Luồng đăng xuất (có 2 loại):**

- `logout()` — user chủ động: gọi POST /auth/logout, xóa localStorage (token + user + chat history), redirect `/login`
- `forceLogout()` — auto trigger khi nhận HTTP 401: không gọi API, xóa localStorage, redirect về `/login?redirect=<current>` (trừ các trang auth)

**Khởi tạo trạng thái**: Khi app load, token và user được đọc trực tiếp từ localStorage — không có bước "verify token" lúc khởi động. Token chỉ được xác thực khi gọi API thực sự.

---

### `src/stores/notification.js`

|                 | Chi tiết                    |
| --------------- | --------------------------- |
| **State**       | `unreadCount` (ref, number) |
| **Actions**     | `setUnreadCount(n)`         |
| **Persistence** | Không persist               |

Dùng bởi `NotificationBell` component để hiển thị badge số thông báo chưa đọc.

---

### `src/stores/ui.js`

|                 | Chi tiết                                                                            |
| --------------- | ----------------------------------------------------------------------------------- |
| **State**       | `sidebarCollapsed` (ref, boolean, mặc định `true`), `theme` (ref, string `'light'`) |
| **Actions**     | `toggleSidebar()`                                                                   |
| **Persistence** | Không persist                                                                       |

`sidebarCollapsed` điều khiển sidebar trong AdminLayout trên mobile. `theme` hiện chưa được dùng cho dark mode toggle thực sự.

---

## 10. HTTP & API Layer

### `src/services/http.js` — Axios instance trung tâm

```js
const http = axios.create({
  baseURL: API_BASE_URL, // đọc từ import.meta.env.VITE_API_BASE_URL
  headers: { Accept: "application/json" },
  timeout: 20000, // 20 giây
});
```

**Request interceptor** — Tự động gắn Bearer token:

```js
config.headers.Authorization = `Bearer ${localStorage.getItem("tts_token")}`;
```

**Response interceptor** — Xử lý response và lỗi:

| Điều kiện                    | Hành động                                                    |
| ---------------------------- | ------------------------------------------------------------ |
| `res.data.success === true`  | Unwrap: trả về `{ ...res, data: res.data.data, _meta: ... }` |
| `res.data.success === false` | Reject ngay với `res.data`                                   |
| HTTP 401 + token tồn tại     | `auth.forceLogout()` — auto logout                           |
| HTTP 403                     | Toast "Bạn không có quyền truy cập"                          |
| HTTP >= 500                  | Toast "Lỗi máy chủ, thử lại sau"                             |

**Không có cơ chế refresh token.** Khi token hết hạn, server trả 401 → interceptor gọi `forceLogout()` → user phải đăng nhập lại.

### Pattern gọi API trong project

**Cách chuẩn** — dùng `useQuery` hoặc `useMutation` từ TanStack Vue Query trong view:

```js
// Trong view (HomeView.vue)
const { data, isLoading } = useQuery({
  queryKey: ["documents", "featured"],
  queryFn: documentService.featured,
});
```

**Service module** — mỗi module export một object với các method:

```js
// src/services/documentService.js
export const documentService = {
  list: (params) => http.get("/documents", { params }).then((r) => r.data),
  featured: () => http.get("/documents/featured").then((r) => r.data),
  // ...
};
```

Service không dùng `useQuery` — chỉ là wrapper thuần của axios. `useQuery` được dùng trong view/composable.

### Danh sách service modules

| File                     | Prefix endpoint                                      | Mô tả                                                                        |
| ------------------------ | ---------------------------------------------------- | ---------------------------------------------------------------------------- |
| `authService.js`         | `/auth/*`                                            | Login, register, forgot/reset password, me, logout                           |
| `documentService.js`     | `/documents/*`, `/admin/documents/*`                 | CRUD tài liệu, featured/popular/recent/recommended, download, favorite, rate |
| `categoryService.js`     | `/categories/*`, `/admin/categories/*`               | CRUD danh mục                                                                |
| `tagService.js`          | `/tags/*`, `/admin/tags/*`                           | CRUD tags                                                                    |
| `searchService.js`       | `/search/*`                                          | Tìm kiếm, gợi ý, trending keywords                                           |
| `profileService.js`      | `/profile/*`                                         | Hồ sơ, avatar, đổi mật khẩu, favorites, history                              |
| `notificationService.js` | `/notifications/*`                                   | Danh sách thông báo, đánh dấu đã đọc                                         |
| `chatbotService.js`      | `/chatbot/*`                                         | Gợi ý chatbot, gửi tin nhắn                                                  |
| `adminUserService.js`    | `/admin/users/*`                                     | CRUD users + cập nhật status                                                 |
| `adminChatbotService.js` | `/admin/chatbot/*`, `/admin/notifications/broadcast` | Quản lý intents, xem logs, broadcast                                         |
| `proposalService.js`     | `/teacher/proposals/*`, `/admin/proposals/*`         | Teacher gửi đề xuất, admin duyệt                                             |
| `statsService.js`        | `/admin/stats/*`                                     | Thống kê dashboard admin                                                     |
| `metaService.js`         | `/roles`                                             | Danh sách roles                                                              |
| `sendMailService.js`     | EmailJS API                                          | Gửi email từ browser (reset password, OTP đăng ký)                           |

---

## 11. Form Validation

Library: **vee-validate ^4.15.1** + **@vee-validate/yup ^4.15.1** + **yup ^1.7.1**

Pattern chuẩn trong project:

```js
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/yup";
import { loginSchema } from "@/utils/validators";

const { defineField, handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: toTypedSchema(loginSchema),
  initialValues: { email: "", password: "" },
});

const [email, emailAttrs] = defineField("email");
const [password, passwordAttrs] = defineField("password");

const onSubmit = handleSubmit(async (values) => {
  // gọi API
});
```

Các schema được định nghĩa tập trung tại `src/utils/validators.js`:

| Schema                 | Fields                                                              | Rules                                 |
| ---------------------- | ------------------------------------------------------------------- | ------------------------------------- |
| `loginSchema`          | email, password                                                     | email hợp lệ, required                |
| `registerSchema`       | name, email, password, password_confirmation, phone?, student_code? | min 2/8 ký tự, xác nhận mật khẩu khớp |
| `changePasswordSchema` | current_password, password, password_confirmation                   | min 8 ký tự, xác nhận khớp            |

---

## 12. UI Components & Styling

### PrimeVue 4.5.5 — Aura theme

- **Theme preset**: `@primevue/themes/aura` — theme mặc định hiện đại
- **Dark mode**: kích hoạt bằng cách thêm class `.dark-mode` vào thẻ `<html>`. Hiện tại chưa có UI toggle — `ui.theme` trong store chưa được connect.
- **Components dùng phổ biến**: `Button`, `InputText`, `Password`, `DataTable`, `Column`, `Dialog`, `Select`, `MultiSelect`, `Textarea`, `Tag`, `Badge`
- **Ripple**: bật toàn cục

### Tailwind CSS 3.4.19

**Custom colors:**

| Tên            | Mục đích                 | Mã màu chính            |
| -------------- | ------------------------ | ----------------------- |
| `brand-*`      | Màu chính của app (blue) | `brand-600`: `#2563eb`  |
| `accent-*`     | Màu nhấn (emerald/green) | `accent-500`: `#10b981` |
| `surface`      | Background trang         | `#fafbfc`               |
| `surface-soft` | Background nhẹ hơn       | `#f4f6fa`               |
| `ink-*`        | Text colors              | `ink-900`: `#0b1220`    |

**Custom fonts:**

- `font-sans`: Inter (body text)
- `font-display`: Plus Jakarta Sans (headings, tiêu đề nổi bật)

**Custom animations:** `fade-up`, `fade-in`, `scale-in`, `shimmer`, `float`, `gradient-pan`, `pulse-soft`, `caret`

**Custom shadows:** `shadow-soft`, `shadow-lift`, `shadow-glow`, `shadow-card`

### Icons

- **PrimeIcons** (`pi pi-*`): dùng trong PrimeVue components (Button, v.v.)
- **Iconify + MDI** (`@iconify/vue`, `icon="mdi:*"`): dùng trong toàn bộ custom UI

```vue
<Icon icon="mdi:book-open-variant-outline" class="h-5 w-5" />
```

### Toasts

**vue-sonner** — `Toaster` đặt trong `App.vue` (position: top-right, rich-colors). Dùng qua `useToast()` composable:

```js
const toast = useToast();
toast.success("Thành công");
toast.error("Lỗi");
toast.info("Thông tin");
```

---

## 13. Tích hợp Backend API

- **Base URL**: Đọc từ `VITE_API_BASE_URL` → mặc định fallback `http://localhost:8000/api/v1`
- **Dev proxy**: Vite proxy `/api` → `http://localhost:8000` (chỉ dùng khi gọi `/api/*` trực tiếp, không phải qua `http.js` dùng full URL)
- **Authentication**: Bearer token — `Authorization: Bearer <token>` trong header mỗi request
- **Token storage**: localStorage, key `tts_token`
- **CORS**: Backend phải allow origin `http://localhost:5173` (dev) và `https://trithucso.oceanmind.id.vn` (prod)
- **Response format từ Laravel**: `{ success: true, data: {...} }` — được interceptor của `http.js` unwrap tự động

**Media upload** — Không upload qua backend. File được upload thẳng lên Cloudinary từ browser (`useUploadImage` composable), sau đó gửi URL về backend.

**Email** — Một số email (OTP đăng ký, reset password) được gửi từ browser qua EmailJS API nếu backend không tự gửi được. Credentials EmailJS được hardcode trong `src/constants/index.js`.

---

## 14. Bảng các thư viện đã cài

### dependencies (runtime)

| Package               | Version  | Mô tả ngắn                   | Dùng ở đâu                                         |
| --------------------- | -------- | ---------------------------- | -------------------------------------------------- |
| `vue`                 | ^3.5.32  | Framework chính              | Tất cả `.vue` files, `main.js`                     |
| `vue-router`          | ^4.6.4   | Routing SPA                  | `src/router/index.js`, mọi view                    |
| `pinia`               | ^3.0.4   | State management             | `src/stores/`                                      |
| `axios`               | ^1.15.2  | HTTP client                  | `src/services/http.js`                             |
| `@tanstack/vue-query` | ^5.100.6 | Server state caching         | `main.js`, views (useQuery/useMutation)            |
| `primevue`            | ^4.5.5   | UI components                | AdminLayout, forms, DataTable                      |
| `@primevue/themes`    | ^4.5.4   | Theme preset Aura            | `main.js`                                          |
| `primeicons`          | ^7.0.0   | Icons cho PrimeVue           | Buttons, inputs                                    |
| `@iconify/vue`        | ^5.0.0   | Icon library (MDI, v.v.)     | Header, sidebar, layouts                           |
| `vee-validate`        | ^4.15.1  | Form validation              | Login, Register, ChangePassword forms              |
| `@vee-validate/yup`   | ^4.15.1  | Yup adapter cho vee-validate | Cùng với vee-validate                              |
| `yup`                 | ^1.7.1   | Schema validation            | `src/utils/validators.js`                          |
| `vue-sonner`          | ^2.0.9   | Toast notifications          | `App.vue`, `useToast.js`                           |
| `@vueuse/core`        | ^11.3.0  | Vue composable utilities     | `AppHeader.vue` (useWindowScroll, onClickOutside)  |
| `chart.js`            | ^4.5.1   | Vẽ charts                    | Admin dashboard                                    |
| `vue-chartjs`         | ^5.3.3   | Wrapper Vue cho Chart.js     | `ChartLine*.vue`, `ChartPie*.vue`, `ChartBar*.vue` |

### devDependencies (build tools)

| Package              | Version | Mô tả ngắn                     | Dùng ở đâu             |
| -------------------- | ------- | ------------------------------ | ---------------------- |
| `vite`               | ^5.4.21 | Build tool, dev server         | `package.json` scripts |
| `@vitejs/plugin-vue` | ^5.2.4  | Vite plugin xử lý `.vue` files | `vite.config.js`       |
| `tailwindcss`        | ^3.4.19 | Utility CSS framework          | Toàn bộ template       |
| `postcss`            | ^8.5.12 | CSS transformer                | `postcss.config.js`    |
| `autoprefixer`       | ^10.5.0 | Tự thêm vendor CSS prefix      | `postcss.config.js`    |

---

## 15. Build & Deploy

### Build local

```bash
npm run build
# Output: frontend/dist/
# - dist/index.html
# - dist/assets/*.js, *.css (chunked)
# - dist/assets/*.png (images)
```

> ⚠️ Hiện tại `vite.config.js` có `minify: false` — file JS không được nén. Xem [mục 19](#️-phát-hiện-cần-lưu-ý).

### Deploy lên VPS (static hosting)

```bash
# 1. Build với biến env production
VITE_API_BASE_URL=https://api.trithucso.oceanmind.id.vn/api/v1 \
VITE_SITE_URL=https://trithucso.oceanmind.id.vn \
VITE_APP_URL=https://trithucso.oceanmind.id.vn \
npm run build

# 2. Upload thư mục dist/ lên VPS
scp -r dist/ user@server:/var/www/trithucso/

# Hoặc dùng rsync
rsync -av dist/ user@server:/var/www/trithucso/
```

### Nginx config gợi ý (SPA fallback)

```nginx
server {
    listen 80;
    server_name trithucso.oceanmind.id.vn;
    root /var/www/trithucso;
    index index.html;

    # SPA fallback — mọi path không phải file tĩnh đều trả về index.html
    location / {
        try_files $uri $uri/ /index.html;
    }

    # Cache assets lâu (Vite hash content trong tên file)
    location /assets/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### Lưu ý quan trọng

- **Biến `VITE_*` được inject lúc build-time**, không phải runtime. Nếu cần thay đổi URL API, phải build lại.
- Chỉ các biến bắt đầu bằng `VITE_` mới được expose ra browser. Các biến khác trong `.env` bị Vite bỏ qua.
- File `index.html` dùng `__SITE_URL__` làm placeholder — Vite plugin trong `vite.config.js` replace bằng `VITE_SITE_URL` lúc build.

---

## 16. Convention & Code Style

### File naming

| Loại          | Convention                  | Ví dụ                                    |
| ------------- | --------------------------- | ---------------------------------------- |
| Vue component | PascalCase                  | `DocumentCard.vue`, `AppHeader.vue`      |
| Composable    | camelCase, prefix `use`     | `useAuth.js`, `usePagination.js`         |
| Store         | camelCase                   | `auth.js`, `notification.js`             |
| Service       | camelCase, suffix `Service` | `documentService.js`, `authService.js`   |
| Utils         | camelCase                   | `formatters.js`, `validators.js`         |
| View          | PascalCase, suffix `View`   | `HomeView.vue`, `AdminDashboardView.vue` |

### Folder organization

- **Components** chia theo domain, không theo atomic design: `admin/`, `chatbot/`, `common/`, `document/`, `home/`, `notification/`, `search/`, `teacher/`
- **Views** chia theo vai trò người dùng: `public/`, `user/`, `admin/`, `teacher/`, `error/`

### Import alias

`@/` trỏ đến `src/` — cấu hình trong `vite.config.js`:

```js
resolve: { alias: { '@': path.resolve(__dirname, './src') } }
```

Luôn dùng `@/` thay vì đường dẫn tương đối `../../`:

```js
import { useAuthStore } from "@/stores/auth";
import DocumentCard from "@/components/document/DocumentCard.vue";
```

### Vue style

- **Composition API** với `<script setup>` — toàn bộ project dùng cú pháp này
- **`storeToRefs()`** để destructure reactive refs từ Pinia store mà không mất reactivity
- **TanStack Vue Query** cho data fetching (không dùng `ref` + `onMounted` + `fetch` thủ công)

### ESLint / Prettier

Hiện tại **chưa có** ESLint hoặc Prettier config trong project. Xem [mục 19](#️-phát-hiện-cần-lưu-ý).

---

## 17. Troubleshooting thường gặp

### 1. CORS error khi gọi API local

**Triệu chứng**: `Access-Control-Allow-Origin` error trong console.

**Nguyên nhân**: Backend chưa allow origin `http://localhost:5173`.

**Fix**:

- Kiểm tra backend `config/cors.php` — thêm `http://localhost:5173` vào `allowed_origins`.
- Vite proxy chỉ hoạt động nếu gọi `/api/*` (không có domain). `http.js` dùng full URL từ `VITE_API_BASE_URL` nên proxy không apply — đây là design đúng cho production nhưng cần backend config CORS cho dev.

### 2. Trang trắng sau build production

**Nguyên nhân**: Server trả `404` cho các route như `/documents/abc` (không phải file tĩnh).

**Fix**: Thêm SPA fallback vào Nginx/Apache (xem mục 15).

### 3. Token hết hạn nhưng không tự logout

**Triệu chứng**: User vẫn thấy UI đăng nhập nhưng mọi API call bị lỗi.

**Nguyên nhân**: Token trong localStorage đã hết hạn nhưng chưa gọi API nào để trigger 401.

**Fix**: Chỉ khi thực hiện một request API thực sự thì interceptor mới phát hiện 401 và `forceLogout()`. Không có cơ chế kiểm tra token chủ động lúc khởi động.

### 4. Build fail vì memory

```bash
NODE_OPTIONS=--max-old-space-size=4096 npm run build
```

### 5. `unwrapList` trả về mảng rỗng

**Nguyên nhân**: Response structure từ backend thay đổi (không phải array, không có `.data`, không có `.items`).

**Fix**: Kiểm tra response thực từ network tab, update `unwrapList()` trong `src/utils/apiHelpers.js` nếu cần.

### 6. PrimeVue component không có style

**Nguyên nhân**: Quên import `primeicons/primeicons.css` hoặc thiếu `PrimeVue` trong `app.use()`.

**Fix**: Kiểm tra `src/main.js` — đảm bảo đủ 5 dòng: `createPinia`, `router`, `VueQueryPlugin`, `PrimeVue`, `ToastService`.

### 7. Upload Cloudinary thất bại

**Nguyên nhân**: `VITE_CLOUDINARY_CLOUD_NAME` hoặc `VITE_CLOUDINARY_UPLOAD_PRESET` chưa đặt trong `.env`.

**Fix**: Kiểm tra `.env`, restart dev server sau khi sửa (Vite không hot-reload `.env`).

### 8. Email reset password không gửi được

**Nguyên nhân**: EmailJS credentials trong `src/constants/index.js` sai hoặc template không tồn tại.

**Fix**: Kiểm tra `EMAILJS_CONFIG` trong `constants/index.js` — đây là credentials hardcode, không phải từ `.env`.

---

## 18. Onboarding checklist cho dev mới

- [ ] **Clone repo** và `cd frontend`
- [ ] **Kiểm tra Node version**: `node -v` phải >= 18.x
- [ ] **Cài dependencies**: `npm install`
- [ ] **Tạo file `.env`** với các biến cần thiết (xem mục 4)
- [ ] **Đảm bảo backend đang chạy** tại `http://localhost:8000` (Laravel) và đã config CORS cho `http://localhost:5173`
- [ ] **Chạy dev server**: `npm run dev`
- [ ] **Mở http://localhost:5173** — trang chủ phải load được (có thể chưa có data nếu backend chưa seed)
- [ ] **Đọc `src/router/index.js`** — nắm 27 routes, 3 layouts, guard logic
- [ ] **Đọc `src/stores/auth.js`** — hiểu luồng login/logout/forceLogout và cách token được lưu
- [ ] **Đọc `src/services/http.js`** — hiểu interceptor, unwrap response, xử lý 401
- [ ] **Thử tạo tài khoản hoặc login** bằng tài khoản test để xem flow hoạt động end-to-end
- [ ] **Thử sửa một component đơn giản** (vd: `src/components/common/LoadingSpinner.vue`) để xác nhận HMR hoạt động

---

## ⚠️ Phát hiện cần lưu ý

### 1. Không có file `.env.example`

File `.env` chứa credentials thật (Cloudinary, URL production). Dự án **thiếu file `.env.example`** — dev mới không biết cần set biến nào. Nên tạo ngay:

```bash
# Tạo .env.example (bỏ giá trị thật)
VITE_SITE_URL=http://localhost:5173
VITE_APP_URL=http://localhost:5173
VITE_API_BASE_URL=http://localhost:8000/api/v1
VITE_CLOUDINARY_CLOUD_NAME=
VITE_CLOUDINARY_UPLOAD_PRESET=
VITE_CLOUDINARY_API_KEY=
```

### 2. `VITE_CLOUDINARY_API_SECRET` trong `.env` — bảo mật nghiêm trọng

`VITE_*` variables được **bundle vào file JS và public hoàn toàn**. `VITE_CLOUDINARY_API_SECRET` đang được set trong `.env` nhưng **Cloudinary API Secret tuyệt đối không được để client biết** (dùng để ký request, có thể dùng upload bất hạn hoặc xóa asset). Cần:

- Xóa `VITE_CLOUDINARY_API_SECRET` khỏi `.env` ngay lập tức
- Mọi thao tác cần API Secret phải thực hiện từ backend

### 3. Build production tắt minify

Trong `vite.config.js`:

```js
build: {
  sourcemap: true,
  minify: false,   // ← "tạm thời" nhưng đang ở code chính
}
```

File JS production không được nén, kích thước lớn hơn nhiều lần, sourcemap expose code gốc. Cần bật lại `minify: 'esbuild'` (mặc định) và cân nhắc tắt `sourcemap` cho production.

### 4. Không có ESLint / Prettier

Dự án chưa cấu hình ESLint hoặc Prettier — không có lint khi commit, code style có thể không đồng nhất. Nên thêm:

- `@eslint/js` + `eslint-plugin-vue` để lint Vue files
- `prettier` + format on save trong VS Code

### 5. EmailJS credentials hardcode trong constants

`src/constants/index.js` chứa hardcode `serviceId`, `templateId`, `publicKey` của EmailJS — không dùng `.env`. Nếu cần thay đổi, phải sửa code thay vì chỉ sửa env. Nên chuyển sang `VITE_EMAILJS_SERVICE_ID`, v.v.

### 6. Dark mode chưa hoàn chỉnh

`ui.js` store có `theme` ref và PrimeVue config `darkModeSelector: '.dark-mode'`, nhưng không có component nào toggle class `.dark-mode` trên `<html>`. Dark mode chưa hoạt động thực sự.

### 7. Không có cơ chế refresh token

Khi token hết hạn, user bị force logout. Không có silent refresh hay rotating token. Với session dài (ví dụ làm việc qua đêm), user sẽ bị ngắt phiên đột ngột.
