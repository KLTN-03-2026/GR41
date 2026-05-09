# PROJECT_CONTEXT.md — Tri Thức Số (TTS-2026)

> Digital Library System — Graduation Thesis Project
> Generated: 2026-04-30

---

## 1. PROJECT OVERVIEW

**Project Name:** Tri Thức Số (TTS-2026)
**Type:** Digital Library System (Hệ thống thư viện điện tử)
**Purpose:** A full-stack web application serving as a digital library for students and teachers at a university. Users can browse, search, view, download, and rate documents (eBooks, papers, textbooks). Teachers can propose documents for admin review. Admins manage all content, users, categories, tags, and operate a configurable rule-based chatbot.

---

## 2. TECHNOLOGY STACK

### Backend

| Item           | Value                                           |
| -------------- | ----------------------------------------------- |
| Language       | PHP 8.2                                         |
| Framework      | Laravel 11                                      |
| Authentication | Laravel Sanctum 4.3 (Bearer token, SPA)         |
| Database       | MySQL (with FULLTEXT index on documents)        |
| Cache          | File-based (Laravel Cache facade)               |
| Queue          | Sync (no async queue in current config)         |
| Mail           | Log driver (email sent client-side via EmailJS) |
| API Style      | RESTful JSON, prefix `/api/v1`                  |
| ORM            | Eloquent with SoftDeletes on Document           |

### Frontend

| Item                         | Value                                            |
| ---------------------------- | ------------------------------------------------ |
| Language                     | JavaScript (ES Modules)                          |
| Framework                    | Vue 3.5 (Composition API)                        |
| Build Tool                   | Vite 8                                           |
| State Management             | Pinia 3                                          |
| Routing                      | Vue Router 4                                     |
| HTTP Client                  | Axios 1.15                                       |
| UI Component Library         | PrimeVue 4.5 + Iconify/Vue                       |
| CSS Framework                | Tailwind CSS 3.4                                 |
| Form Validation              | VeeValidate 4 + Yup 1                            |
| Server State / Data Fetching | TanStack Vue Query 5                             |
| Charts                       | Chart.js 4 + vue-chartjs 5                       |
| Notifications (toast)        | vue-sonner 2                                     |
| File/Image Upload            | Cloudinary (via `useUploadImage` composable)     |
| Email (forgot-password)      | EmailJS (browser-side, VITE*EMAILJS*\* env vars) |

---

## 3. REPOSITORY STRUCTURE

```
TriThucSo/
├── backend/          # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/
│   │   │   │   ├── Admin/
│   │   │   │   ├── Teacher/
│   │   │   │   └── (public/user controllers)
│   │   │   ├── Middleware/
│   │   │   ├── Requests/
│   │   │   └── Resources/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/api.php
└── frontend/         # Vue 3 SPA
    └── src/
        ├── composables/
        ├── router/
        ├── services/
        ├── stores/
        └── views/
            ├── admin/
            ├── error/
            ├── public/
            ├── teacher/
            └── user/
```

---

## 4. BACKEND — COMPLETE FILE CONTENTS

### 4.1 composer.json

```json
{
  "name": "laravel/laravel",
  "type": "project",
  "description": "The skeleton application for the Laravel framework.",
  "keywords": ["laravel", "framework"],
  "license": "MIT",
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.3",
    "laravel/tinker": "^2.9"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pint": "^1.13",
    "laravel/sail": "^1.26",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.0",
    "phpunit/phpunit": "^10.5",
    "spatie/laravel-ignition": "^2.4"
  },
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/"
    }
  }
}
```

### 4.2 .env.example

```env
APP_NAME="Tri Thuc So"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=vi
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tri_thuc_so
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@tts.local"
MAIL_FROM_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost

FRONTEND_URL=http://localhost:5173
```

### 4.3 routes/api.php

All routes are prefixed with `/api/v1` (via `RouteServiceProvider`).

**Public routes (no auth):**

- `POST   auth/register`
- `POST   auth/login`
- `POST   auth/forgot-password`
- `POST   auth/reset-password`
- `GET    roles`
- `GET    documents`
- `GET    documents/featured`
- `GET    documents/popular`
- `GET    documents/recent`
- `GET    documents/{slug}`
- `GET    documents/{id}/related`
- `GET    search`
- `GET    search/suggestions`
- `GET    search/trending`
- `GET    categories`
- `GET    tags`
- `GET    stats`
- `POST   chatbot/ask`
- `GET    chatbot/suggestions`

**Authenticated routes (`auth:sanctum`):**

- `POST   auth/logout`
- `GET    auth/me`
- `GET    documents/recommended`
- `GET    documents/{id}/download`
- `POST   documents/{id}/favorite`
- `POST   documents/{id}/rate`
- `GET    profile`
- `PUT    profile`
- `POST   profile/avatar`
- `POST   profile/change-password`
- `GET    profile/favorites`
- `DELETE profile/favorites/{documentId}`
- `GET    profile/history`
- `GET    notifications`
- `PATCH  notifications/{id}/read`
- `POST   notifications/read-all`

**Admin routes (`auth:sanctum` + `role:admin`, prefix `admin/`):**

- Documents CRUD: `GET/POST/PUT/DELETE admin/documents`
- Categories CRUD: `GET/POST/PUT/DELETE admin/categories`
- Tags CRUD: `GET/POST/PUT/DELETE admin/tags`
- Users CRUD + status: `GET/POST/PUT/DELETE/PATCH admin/users`
- Chatbot intents CRUD: `GET/POST/PUT/DELETE admin/chatbot/intents`
- Chatbot logs: `GET admin/chatbot/logs`
- Broadcast notification: `POST admin/notifications/broadcast`
- Stats: `GET admin/stats/overview`, `GET admin/stats/charts`, `GET admin/stats/top-keywords`
- Proposals: `GET/POST admin/proposals`, `GET admin/proposals/pending-count`, `GET/POST admin/proposals/{id}/approve`, `POST admin/proposals/{id}/reject`

**Teacher routes (`auth:sanctum` + `role:teacher`, prefix `teacher/`):**

- `GET    teacher/proposals`
- `POST   teacher/proposals`
- `DELETE teacher/proposals/{id}`

---

## 5. BACKEND MODELS

### 5.1 User.php

```
Fillable: role_id, name, email, password, phone, avatar, student_code, status, email_verified_at
Hidden:   password, remember_token
Casts:    email_verified_at (datetime), password (hashed)
Traits:   CanResetPassword, HasApiTokens, HasFactory, Notifiable
Relations:
  - role()              → BelongsTo(Role)
  - favorites()         → HasMany(Favorite)
  - ratings()           → HasMany(Rating)
  - searchHistory()     → HasMany(SearchHistory)
  - documentViews()     → HasMany(DocumentView)
  - chatbotLogs()       → HasMany(ChatbotLog)
  - libraryNotifications() → HasMany(Notification)
```

### 5.2 Document.php

```
Fillable: category_id, uploaded_by, proposed_by, reviewed_by, reviewed_at, rejection_reason,
          status, title, slug, description, author, publisher, published_year, isbn,
          language, pages, file_url, cover_image, view_count, download_count, is_featured
Casts:    published_year (integer), is_featured (boolean), reviewed_at (datetime)
Traits:   HasFactory, SoftDeletes
Scopes:   scopePublished()
Relations:
  - category()   → BelongsTo(Category)
  - uploader()   → BelongsTo(User, 'uploaded_by')
  - proposer()   → BelongsTo(User, 'proposed_by')
  - reviewer()   → BelongsTo(User, 'reviewed_by')
  - tags()       → BelongsToMany(Tag, 'document_tag')
  - favorites()  → HasMany(Favorite)
  - ratings()    → HasMany(Rating)
  - views()      → HasMany(DocumentView)
```

### 5.3 Category.php

```
Fillable: parent_id, name, slug, icon, description, sort_order
Relations:
  - parent()    → BelongsTo(Category, 'parent_id')
  - children()  → HasMany(Category, 'parent_id') [ordered by sort_order]
  - documents() → HasMany(Document)
```

### 5.4 Tag.php

```
Fillable: name, slug
Relations:
  - documents() → BelongsToMany(Document, 'document_tag')
```

### 5.5 Role.php

```
Fillable: slug, name, description
Roles seeded: admin, teacher, student
Relations:
  - users() → HasMany(User)
```

### 5.6 Rating.php

```
Fillable: user_id, document_id, score, comment
Relations:
  - user()     → BelongsTo(User)
  - document() → BelongsTo(Document)
```

### 5.7 SearchHistory.php

```
Table:     search_history
Timestamps: false (manual searched_at)
Fillable:  user_id, keyword, result_count, searched_at
Casts:     searched_at (datetime)
Relations:
  - user() → BelongsTo(User)
```

### 5.8 DocumentView.php

```
Timestamps: false (manual viewed_at)
Fillable:   user_id, document_id, ip_address, viewed_at
Casts:      viewed_at (datetime)
Relations:
  - user()     → BelongsTo(User)
  - document() → BelongsTo(Document)
```

### 5.9 ChatbotIntent.php

```
Fillable: intent_key, name, keywords, response_template, data_source, is_active
Casts:    keywords (array), is_active (boolean)
Relations:
  - logs() → HasMany(ChatbotLog, 'matched_intent_id')
```

### 5.10 ChatbotLog.php

```
Timestamps: false (manual created_at)
Fillable:   user_id, matched_intent_id, question, answer, created_at
Casts:      created_at (datetime)
Relations:
  - user()   → BelongsTo(User)
  - intent() → BelongsTo(ChatbotIntent, 'matched_intent_id')
```

### 5.11 Synonym.php

```
Fillable: keyword, synonyms
Casts:    synonyms (array)
Purpose:  Used by SearchService to expand query keywords
```

### 5.12 Notification.php

```
Fillable: user_id, title, content, type, is_read, data
Casts:    is_read (boolean), data (array)
Types:    broadcast, proposal_approved, proposal_rejected
Relations:
  - user() → BelongsTo(User)
```

### 5.13 Favorite.php

```
Timestamps: false (manual created_at)
Fillable:   user_id, document_id, created_at
Unique:     [user_id, document_id]
Relations:
  - user()     → BelongsTo(User)
  - document() → BelongsTo(Document)
```

---

## 6. DATABASE SCHEMA (Migrations)

### roles

| Column      | Type           | Notes    |
| ----------- | -------------- | -------- |
| id          | tinyIncrements | PK       |
| slug        | string(20)     | UNIQUE   |
| name        | string(50)     |          |
| description | text           | nullable |
| timestamps  |                |          |

### users

| Column            | Type                    | Notes             |
| ----------------- | ----------------------- | ----------------- |
| id                | bigIncrements           | PK                |
| role_id           | unsignedTinyInteger     | FK → roles.id     |
| name              | string(100)             |                   |
| email             | string(150)             | UNIQUE            |
| email_verified_at | timestamp               | nullable          |
| password          | string                  |                   |
| phone             | string(20)              | nullable          |
| avatar            | string(500)             | nullable          |
| student_code      | string(20)              | nullable, indexed |
| status            | enum('active','banned') | default: active   |
| remember_token    | string                  |                   |
| timestamps        |                         |                   |

### categories

| Column      | Type            | Notes                                       |
| ----------- | --------------- | ------------------------------------------- |
| id          | unsignedInteger | PK                                          |
| parent_id   | unsignedInteger | nullable, FK → categories.id (nullOnDelete) |
| name        | string(100)     |                                             |
| slug        | string(120)     | UNIQUE                                      |
| icon        | string(50)      | nullable                                    |
| description | text            | nullable                                    |
| sort_order  | integer         | default: 0                                  |
| timestamps  |                 |                                             |

### documents

| Column           | Type                                   | Notes                                                       |
| ---------------- | -------------------------------------- | ----------------------------------------------------------- |
| id               | bigIncrements                          | PK                                                          |
| category_id      | unsignedInteger                        | FK → categories.id (restrict)                               |
| uploaded_by      | foreignId                              | nullable → users.id (nullOnDelete)                          |
| proposed_by      | foreignId                              | nullable → users.id (nullOnDelete) — added by migration 018 |
| reviewed_by      | foreignId                              | nullable → users.id (nullOnDelete) — added by migration 018 |
| reviewed_at      | timestamp                              | nullable — added by migration 018                           |
| rejection_reason | text                                   | nullable — added by migration 018                           |
| status           | enum('pending','published','rejected') | default: published — added by migration 018                 |
| title            | string(255)                            | indexed                                                     |
| slug             | string(280)                            | UNIQUE                                                      |
| description      | text                                   | nullable                                                    |
| author           | string(150)                            | nullable                                                    |
| publisher        | string(150)                            | nullable                                                    |
| published_year   | year                                   | nullable                                                    |
| isbn             | string(20)                             | nullable                                                    |
| language         | string(10)                             | default: 'vi'                                               |
| pages            | integer                                | nullable                                                    |
| file_url         | string(500)                            |                                                             |
| cover_image      | string(500)                            | nullable                                                    |
| view_count       | unsignedInteger                        | default: 0                                                  |
| download_count   | unsignedInteger                        | default: 0                                                  |
| is_featured      | boolean                                | default: false                                              |
| timestamps       |                                        |                                                             |
| deleted_at       | timestamp                              | nullable (SoftDeletes)                                      |

**MySQL-specific indexes on documents:**

- FULLTEXT INDEX ft_search on (title, description, author)
- INDEX idx_doc_views on (view_count DESC)
- INDEX idx_doc_category on (category_id, is_featured, created_at)

### tags

| Column     | Type            | Notes  |
| ---------- | --------------- | ------ |
| id         | unsignedInteger | PK     |
| name       | string(50)      |        |
| slug       | string(60)      | UNIQUE |
| timestamps |                 |        |

### document_tag (pivot)

| Column      | Type                  |
| ----------- | --------------------- |
| document_id | unsignedBigInteger    |
| tag_id      | unsignedInteger       |
| PRIMARY KEY | (document_id, tag_id) |

### favorites

| Column      | Type                   | Notes                            |
| ----------- | ---------------------- | -------------------------------- |
| id          | bigIncrements          | PK                               |
| user_id     | foreignId              | → users.id (cascadeOnDelete)     |
| document_id | foreignId              | → documents.id (cascadeOnDelete) |
| created_at  | timestamp              | useCurrent                       |
| UNIQUE      | (user_id, document_id) |                                  |

### ratings

| Column      | Type                   | Notes                            |
| ----------- | ---------------------- | -------------------------------- |
| id          | bigIncrements          | PK                               |
| user_id     | foreignId              | → users.id (cascadeOnDelete)     |
| document_id | foreignId              | → documents.id (cascadeOnDelete) |
| score       | tinyInteger            | 1–5 stars                        |
| comment     | text                   | nullable                         |
| timestamps  |                        |                                  |
| UNIQUE      | (user_id, document_id) |                                  |

### search_history

| Column       | Type          | Notes                              |
| ------------ | ------------- | ---------------------------------- |
| id           | bigIncrements | PK                                 |
| user_id      | foreignId     | nullable → users.id (nullOnDelete) |
| keyword      | string(255)   | indexed                            |
| result_count | integer       |                                    |
| searched_at  | timestamp     | useCurrent                         |

### document_views

| Column      | Type          | Notes                              |
| ----------- | ------------- | ---------------------------------- |
| id          | bigIncrements | PK                                 |
| user_id     | foreignId     | nullable → users.id (nullOnDelete) |
| document_id | foreignId     | → documents.id (cascadeOnDelete)   |
| ip_address  | string(45)    |                                    |
| viewed_at   | timestamp     | useCurrent                         |

### chatbot_intents

| Column            | Type            | Notes                                                                                                                |
| ----------------- | --------------- | -------------------------------------------------------------------------------------------------------------------- |
| id                | unsignedInteger | PK                                                                                                                   |
| intent_key        | string(50)      | UNIQUE                                                                                                               |
| name              | string(100)     |                                                                                                                      |
| keywords          | json            | Array of trigger keywords                                                                                            |
| response_template | text            | Supports placeholders: {{popular_documents}}, {{categories_list}}, {{new_documents}}, {{count}}, {{topic}}, {{list}} |
| data_source       | string(50)      | nullable                                                                                                             |
| is_active         | boolean         | default: true                                                                                                        |
| timestamps        |                 |                                                                                                                      |

### chatbot_logs

| Column            | Type            | Notes                                        |
| ----------------- | --------------- | -------------------------------------------- |
| id                | bigIncrements   | PK                                           |
| user_id           | foreignId       | nullable → users.id (nullOnDelete)           |
| matched_intent_id | unsignedInteger | nullable → chatbot_intents.id (nullOnDelete) |
| question          | text            |                                              |
| answer            | text            |                                              |
| created_at        | timestamp       | useCurrent                                   |

### synonyms

| Column     | Type          | Notes                    |
| ---------- | ------------- | ------------------------ |
| id         | bigIncrements | PK                       |
| keyword    | string(100)   | indexed                  |
| synonyms   | json          | Array of synonym strings |
| timestamps |               |                          |

### notifications

| Column     | Type          | Notes                                                     |
| ---------- | ------------- | --------------------------------------------------------- |
| id         | bigIncrements | PK                                                        |
| user_id    | foreignId     | → users.id (cascadeOnDelete)                              |
| title      | string(200)   |                                                           |
| content    | text          |                                                           |
| type       | string(30)    | indexed (broadcast, proposal_approved, proposal_rejected) |
| is_read    | boolean       | default: false                                            |
| data       | json          | nullable                                                  |
| timestamps |               |                                                           |

### personal_access_tokens (Laravel Sanctum)

| Column         | Type               | Notes             |
| -------------- | ------------------ | ----------------- |
| id             | bigIncrements      | PK                |
| tokenable_type | string             | morphs            |
| tokenable_id   | unsignedBigInteger | morphs            |
| name           | text               |                   |
| token          | string(64)         | UNIQUE            |
| abilities      | text               | nullable          |
| last_used_at   | timestamp          | nullable          |
| expires_at     | timestamp          | nullable, indexed |
| timestamps     |                    |                   |

### password_reset_tokens

| Column     | Type               |
| ---------- | ------------------ |
| email      | string (PK)        |
| token      | string             |
| created_at | timestamp nullable |

### cache / cache_locks / sessions / jobs / job_batches / failed_jobs

Standard Laravel tables (file cache, sync queue, session driver in .env).

---

## 7. BACKEND CONTROLLERS

### 7.1 AuthController

- `register(RegisterRequest)` — creates user with 'student' role, returns UserResource + 201
- `login(LoginRequest)` — validates credentials, checks 'active' status, deletes old tokens, creates Sanctum token
- `logout(Request)` — deletes current access token
- `me(Request)` — returns authenticated user with role loaded
- `forgotPassword(ForgotPasswordRequest)` — generates reset token server-side, returns `clientMail: {email, token}` for EmailJS browser-side delivery
- `resetPassword(ResetPasswordRequest)` — resets password via Laravel Password broker

### 7.2 DocumentController (public + auth)

- `index(Request)` — paginated list of published documents; filters: category_slug, category, year, language, tag; sorts: newest, popular, rating
- `featured()` — up to 5 featured published documents
- `popular()` — top 8 by view_count via RecommendService
- `recent()` — latest 8 via RecommendService
- `recommended(Request)` — personalised 8 docs via RecommendService (requires auth)
- `show(Request, string $slug)` — document detail; increments view_count; logs DocumentView; invalidates recommend cache
- `related(int $id)` — 6 related documents via RecommendService
- `download(Request, int $id)` — increments download_count; increments daily cache key; returns file_url
- `toggleFavorite(Request, int $id)` — add/remove favorite
- `rate(RateDocumentRequest, int $id)` — updateOrCreate rating

### 7.3 SearchController

- `search(Request)` — full search via SearchService; supports q, category, year, year_from, year_to, language, tag, sort, per_page; returns items + meta + links + expanded_keywords + did_you_mean (if < 3 results)
- `suggestions(Request)` — autocomplete suggestions from history + document titles
- `trending()` — top 10 keywords from last 7 days

### 7.4 ChatbotController

- `ask(ChatbotAskRequest)` — processes question via ChatbotService; public route (user identified optionally via `user('sanctum')`)
- `suggestions()` — returns 5 sample questions

### 7.5 ProfileController

- `show(Request)` — returns user profile
- `update(UpdateProfileRequest)` — update name, phone, student_code
- `avatar(AvatarRequest)` — update avatar URL
- `changePassword(ChangePasswordRequest)` — verify current password then update
- `removeFavorite(Request, int $documentId)` — remove from favorites
- `favorites(Request)` — paginated favorite documents
- `history(Request)` — last 50 search history + last 50 document view history

### 7.6 NotificationController

- `index(Request)` — paginated notifications with unread_count
- `markRead(Request, int $id)` — mark one as read
- `markAllRead(Request)` — mark all as read

### 7.7 CategoryController

- `index()` — tree of parent categories with children

### 7.8 TagController

- `index()` — all tags ordered by name

### 7.9 RoleController

- `index()` — all roles (id, slug, name)

### 7.10 Admin — DocumentAdminController

- `index(Request)` — paginated all-status documents; filters: q (title/author), category_id, year, is_featured
- `show(int $id)` — document detail with category, tags, uploader
- `store(StoreDocumentRequest)` — create document with unique slug, sync tags
- `update(UpdateDocumentRequest, int $id)` — update document, re-slug if title changed, sync tags
- `destroy(int $id)` — soft delete

### 7.11 Admin — UserAdminController

- `index(Request)` — paginated users; filters: role (slug), status, q (name/email/student_code)
- `show(int $id)` — user detail with role, favorites, searchHistory, ratings
- `store(StoreUserAdminRequest)` — create user
- `update(UpdateUserAdminRequest, int $id)` — update user (password optional)
- `patchStatus(PatchUserStatusRequest, int $id)` — toggle active/banned
- `destroy(int $id)` — delete user

### 7.12 Admin — CategoryAdminController

- `index(Request)` — paginated categories with parent; filter: q
- `store(StoreCategoryRequest)` — create with unique slug
- `update(UpdateCategoryRequest, int $id)` — update with re-slug
- `destroy(int $id)` — delete (guards: no children, not in use)

### 7.13 Admin — TagAdminController

- `index(Request)` — paginated tags; filter: q
- `store(StoreTagRequest)` — create with unique slug
- `update(UpdateTagRequest, int $id)` — update
- `destroy(int $id)` — delete

### 7.14 Admin — StatsController

- `publicStats()` — total_documents, total_users, total_downloads (cached 10 min)
- `overview()` — total_documents, total_users, downloads_today, chatbot_questions_week
- `charts()` — visits_30d (daily), category_distribution (top 12), top_documents (top 10)
- `topKeywords()` — top 20 search keywords from last 7 days

### 7.15 Admin — ChatbotIntentController

- `index()` — all intents ordered by intent_key
- `store(StoreChatbotIntentRequest)` — create intent
- `update(UpdateChatbotIntentRequest, int $id)` — update intent
- `destroy(int $id)` — delete intent
- `logs(Request)` — paginated chatbot logs; filters: intent_id, date_from, date_to

### 7.16 Admin — NotificationAdminController

- `broadcast(BroadcastNotificationRequest)` — bulk create notifications for target: all | students | teachers (chunked 500)

### 7.17 Admin — ProposalAdminController

- `index(Request)` — paginated documents proposed by teachers; filters: status (default: pending), q; includes pending_count
- `pendingCount()` — count of pending proposals
- `show(int $id)` — proposal detail
- `approve(Request, int $id)` — set status=published, record reviewer, notify proposer
- `reject(RejectProposalRequest, int $id)` — set status=rejected, record reason, notify proposer

### 7.18 Teacher — ProposalController

- `index(Request)` — teacher's own proposals; filter: status
- `store(StoreProposalRequest)` — submit proposal (status=pending, proposed_by=current user)
- `destroy(Request, int $id)` — delete own pending proposal

---

## 8. BACKEND SERVICES

### 8.1 SearchService

Handles full-text search, synonym expansion, autocomplete suggestions, trending keywords, and fuzzy "did you mean" corrections.

**Key methods:**

- `search(string $query, array $filters, ?int $userId): LengthAwarePaginator`
  - Expands synonyms (Synonym table lookup)
  - LIKE-based multi-keyword OR matching on title, description, author
  - Filters: category (includes children), year, year_from, year_to, language, tag
  - Sorts: relevance (custom CASE-WHEN SQL score), newest, popular, rating
  - Logs to search_history after each search
- `expandedKeywords(string $rawQuery): array` — returns all synonym-expanded keywords
- `suggestions(string $prefix): array` — merges history keywords + document titles (top 8)
- `trending(): array` — top 10 keywords (last 7 days, grouped by count)
- `fuzzyMatch(string $query): array` — "did you mean" using Unicode-safe Levenshtein distance against history candidates then document titles; adaptive threshold: 1 char (≤3), 2 (≤6), 3 (≤10), 30% of length (longer)
- `expandSynonyms(string $query): array` — splits query into words, looks up Synonym table, returns unique array of original + synonyms
- `mbLevenshtein(string $s1, string $s2): int` — Unicode-safe implementation (preg_split + rolling DP)
- `applyRelevanceOrder(Builder, array $keywords)` — builds CASE WHEN LOWER(title) LIKE... SQL for scoring

### 8.2 ChatbotService

Rule-based chatbot using keyword matching against ChatbotIntent records.

**Key methods:**

- `ask(string $question, ?int $userId): array{answer, intent}`
  - Normalises question to lowercase
  - Iterates all active intents, counts keyword matches
  - Selects highest-scoring intent; falls back to 'fallback' intent
  - Renders template (see below), logs to chatbot_logs
- `renderTemplate(ChatbotIntent, string $q): string`
  - Replaces `{{popular_documents}}`, `{{categories_list}}`, `{{new_documents}}`
  - For `find_document` intent: extracts topic from question, queries documents, replaces `{{count}}`, `{{topic}}`, `{{list}}`
- `guessTopicFromQuestion(string $q): string`
  - Uses ordered markers: 'tài liệu về', 'sách về', 'tìm', 'về', 'sách', 'tài liệu'
  - Strips trailing noise words (Vietnamese: không, vậy, nhé, etc.)
  - Unicode NFC normalized

**Seeded intents (15 total):**
greeting, find_document, borrow_guide, opening_hours, forgot_password, popular, register_guide, account_type, contact, categories, new_documents, thank_you, goodbye, about, fallback

### 8.3 RecommendService

Content-based recommendation engine using view history.

**Key methods:**

- `forUser(int $userId)` — cached 5 min; uses last 10 viewed documents to get their categories; returns up to 8 docs from same categories not yet viewed; falls back to popular() if no history
- `related(int $documentId)` — same category + overlapping tags, top 6 by view_count
- `popular()` — top 8 by view_count (published)
- `newest()` — latest 8 by created_at (published)

### 8.4 StatsService

Dashboard statistics with caching.

**Key methods:**

- `publicStats()` — cached 10 min; total_documents, total_users, total_downloads
- `overview()` — downloads_today (from Cache key `stats.downloads.{date}`), chatbot_questions_week
- `charts()` — visits_30d (DocumentView grouped by date), category_distribution (LEFT JOIN documents COUNT), top_documents (top 10 by view_count)
- `topKeywords()` — search_history last 7 days grouped by keyword, top 20

---

## 9. BACKEND MIDDLEWARE

### RoleMiddleware

- Registered as `role` middleware alias
- Signature: `handle(Request, Closure, string $rolesCsv)`
- Accepts comma-separated role slugs (e.g. `role:admin` or `role:admin,teacher`)
- Loads user's role relation; returns 403 JSON if slug not in list

---

## 10. BACKEND REQUESTS (Validation)

| Request Class                | Route                              | Key Rules                                                                                                                                     |
| ---------------------------- | ---------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| RegisterRequest              | POST auth/register                 | name(required,max:100), email(required,email,unique), password(confirmed,min:8), phone(nullable), student_code(nullable)                      |
| LoginRequest                 | POST auth/login                    | email(required,email), password(required)                                                                                                     |
| ForgotPasswordRequest        | POST auth/forgot-password          | email(required,email,exists:users)                                                                                                            |
| ResetPasswordRequest         | POST auth/reset-password           | email, password(confirmed,min:8), token                                                                                                       |
| StoreDocumentRequest         | POST admin/documents               | title, category_id, file_url, cover_image, author, publisher, published_year, isbn, language, pages, description, tags[], is_featured, status |
| UpdateDocumentRequest        | PUT admin/documents/{id}           | Same as Store, all optional                                                                                                                   |
| RateDocumentRequest          | POST documents/{id}/rate           | score(required,integer,min:1,max:5), comment(nullable)                                                                                        |
| ChatbotAskRequest            | POST chatbot/ask                   | question(required,string,max:500)                                                                                                             |
| StoreUserAdminRequest        | POST admin/users                   | name, email(unique), password(confirmed,min:8), role_id, phone, student_code, status                                                          |
| UpdateUserAdminRequest       | PUT admin/users/{id}               | Same, password optional                                                                                                                       |
| PatchUserStatusRequest       | PATCH admin/users/{id}/status      | status(required,in:active,banned)                                                                                                             |
| BroadcastNotificationRequest | POST admin/notifications/broadcast | title, content, target(in:all,students,teachers)                                                                                              |
| StoreChatbotIntentRequest    | POST admin/chatbot/intents         | intent_key(unique), name, keywords(array), response_template, data_source, is_active                                                          |
| UpdateChatbotIntentRequest   | PUT admin/chatbot/intents/{id}     | Same as Store, all optional                                                                                                                   |
| UpdateProfileRequest         | PUT profile                        | name, phone, student_code                                                                                                                     |
| AvatarRequest                | POST profile/avatar                | avatar(required,string,url,max:500)                                                                                                           |
| ChangePasswordRequest        | POST profile/change-password       | current_password, password(confirmed,min:8)                                                                                                   |
| StoreTagRequest              | POST admin/tags                    | name(required,max:50)                                                                                                                         |
| UpdateTagRequest             | PUT admin/tags/{id}                | name(required,max:50)                                                                                                                         |
| StoreCategoryRequest         | POST admin/categories              | name, parent_id(nullable,exists:categories), icon, description, sort_order                                                                    |
| UpdateCategoryRequest        | PUT admin/categories/{id}          | Same as Store, all optional                                                                                                                   |
| StoreProposalRequest         | POST teacher/proposals             | title, category_id, file_url, description, author, publisher, published_year, language, pages, isbn, cover_image, tags[]                      |
| RejectProposalRequest        | POST admin/proposals/{id}/reject   | reason(required,string,max:1000)                                                                                                              |

---

## 11. BACKEND RESOURCES (API Response Shapes)

### DocumentResource

```
id, title, slug, description, author, publisher, published_year, isbn, language,
pages, file_url, cover_image, view_count, download_count, is_featured,
avg_rating (rounded 1dp, null if not counted),
rating_count (whenCounted),
is_favorited (boolean — checks Favorite table using Sanctum user),
category (CategoryResource, whenLoaded),
tags (TagResource[], whenLoaded),
reviews (RatingResource[], whenLoaded),
uploaded_by (UserResource, whenLoaded),
status, proposed_by, reviewed_by, reviewed_at (ISO8601), rejection_reason,
proposer (id, name, email — whenLoaded),
created_at (ISO8601), updated_at (ISO8601)
```

### UserResource

```
id, name, email, phone, avatar, student_code, status,
role (RoleResource, whenLoaded),
created_at (ISO8601)
```

### CategoryResource

```
id, parent_id, name, slug, icon, description, sort_order,
children (CategoryResource[], whenLoaded),
parent ({id, name, slug} — whenLoaded)
```

### TagResource

```
id, name, slug
```

### RoleResource

```
id, slug, name, description
```

### RatingResource

```
id, score, rating (same as score), comment,
user (UserResource, whenLoaded),
created_at (ISO8601)
```

### NotificationResource

```
id, title, content, type, is_read, data, created_at (ISO8601)
```

### ChatbotIntentResource

```
id, intent_key, name, keywords, response_template, data_source, is_active,
created_at (ISO8601), updated_at (ISO8601)
```

### ChatbotLogResource

```
id, question, answer,
matched_intent (intent_key or name),
user ({id, name} — whenLoaded),
created_at (ISO8601)
```

---

## 12. DATABASE SEEDERS

### Seeder Order (DatabaseSeeder)

1. RoleSeeder
2. CategorySeeder
3. TagSeeder
4. UserSeeder
5. SynonymSeeder
6. ChatbotIntentSeeder
7. DocumentSeeder

### RoleSeeder

Creates 3 roles: admin (Quản trị viên), teacher (Giảng viên), student (Sinh viên).

### UserSeeder

Creates 3 fixed users:

- admin@tts.com / Admin@123 — role: admin
- teacher@tts.com / Teacher@123 — role: teacher
- student@tts.com / Student@123 / student_code: SV20260001 — role: student
  Plus 7 random users via factory.

### CategorySeeder

Creates 8 parent categories, each with 2 children (16 children total):

- Công nghệ thông tin → Lập trình Web, Trí tuệ nhân tạo
- Kinh tế → Tài chính, Marketing
- Ngôn ngữ → Tiếng Anh, Tiếng Hàn
- Khoa học → Vật lý, Hóa học
- Văn học → Tiểu thuyết, Thơ
- Giáo dục → Phương pháp dạy học, Đánh giá học tập
- Y học → Nội khoa, Ngoại khoa
- Pháp luật → Luật dân sự, Luật kinh doanh

### TagSeeder

26 technology/topic tags: Java, Python, Vue, Laravel, AI, Machine Learning, Deep Learning, Database, Algorithm, Web, Mobile, Network, Security, Marketing, Finance, English, IELTS, Data Science, Docker, Kubernetes, Blockchain, Cloud, DevOps, Linux, PHP, JavaScript.

### SynonymSeeder

15 synonym groups for search expansion:

- ai → trí tuệ nhân tạo, machine learning, học máy, deep learning
- lập trình → coding, programming, code
- web → website, frontend, backend, fullstack
- sách → tài liệu, giáo trình, ebook
- database → cơ sở dữ liệu, sql, nosql
- mobile → ứng dụng di động, android, ios
- kinh tế → economics, tài chính, thị trường
- anh văn → english, tiếng anh, ielts
- toán → mathematics, đại số, giải tích
- mạng → network, tcp/ip, bảo mật mạng
- thiết kế → design, ui, ux
- cloud → đám mây, aws, azure
- docker → container, kubernetes
- python → pandas, numpy, django
- javascript → typescript, node, vue

### ChatbotIntentSeeder

15 intents seeded (see Section 8.2 for full list with intent keys and templates).

### DocumentSeeder

Seeds many real documents across categories. Each document has: title, description, author, publisher, published_year, isbn, language, pages, tags. Categories include Lập trình Web (7+ docs), Trí tuệ nhân tạo (7+ docs), Tài chính, and others. Cover image served from Cloudinary. Uploaded by admin@tts.com.

---

## 13. FRONTEND — COMPLETE FILE CONTENTS

### 13.1 package.json

```json
{
  "name": "frontend",
  "private": true,
  "version": "0.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "@iconify/vue": "^5.0.0",
    "@primevue/themes": "^4.5.4",
    "@tanstack/vue-query": "^5.100.6",
    "@vee-validate/yup": "^4.15.1",
    "@vueuse/core": "^11.3.0",
    "axios": "^1.15.2",
    "chart.js": "^4.5.1",
    "pinia": "^3.0.4",
    "primeicons": "^7.0.0",
    "primevue": "^4.5.5",
    "vee-validate": "^4.15.1",
    "vue": "^3.5.32",
    "vue-chartjs": "^5.3.3",
    "vue-router": "^4.6.4",
    "vue-sonner": "^2.0.9",
    "yup": "^1.7.1"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^6.0.6",
    "autoprefixer": "^10.5.0",
    "postcss": "^8.5.12",
    "tailwindcss": "^3.4.19",
    "vite": "^8.0.10"
  }
}
```

### 13.2 vite.config.js

```js
import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig(({ mode }) => ({
  plugins: [
    vue(),
    {
      name: "html-site-url",
      transformIndexHtml(html) {
        const env = loadEnv(mode, process.cwd(), "");
        const base = String(
          env.VITE_SITE_URL || "http://localhost:5173",
        ).replace(/\/$/, "");
        return html.replaceAll("%SITE_URL%", base);
      },
    },
  ],
  resolve: {
    alias: { "@": path.resolve(__dirname, "./src") },
  },
  server: {
    port: 5173,
    proxy: {
      "/api": { target: "http://localhost:8000", changeOrigin: true },
    },
  },
}));
```

### 13.3 tailwind.config.js (Custom Design Tokens)

Custom brand colors:

- `brand.*` — blue scale (50→900, primary-blue: #2563eb)
- `accent.*` — emerald (500: #10b981, 600: #059669)
- `surface` — #fafbfc
- `ink.*` — dark text scale
- Fonts: Inter (sans), Plus Jakarta Sans (display)
- Shadows: soft, lift, glow, card
- Backgrounds: hero-mesh (radial gradient), shimmer
- Custom animations: fade-up, fade-in, scale-in, shimmer, float, gradient-pan, pulse-soft, caret

---

## 14. FRONTEND ROUTER (src/router/index.js)

### Route Groups

**Public layout (`/`):**
| Name | Path | Component |
|---|---|---|
| home | / | HomeView.vue |
| search | /search | SearchView.vue |
| document.detail | /documents/:slug | DocumentDetailView.vue |
| category | /categories/:slug | CategoryView.vue |
| profile | /profile | ProfileView.vue (requiresAuth) |
| favorites | /profile/favorites | FavoritesView.vue (requiresAuth) |
| history | /profile/history | HistoryView.vue (requiresAuth) |
| change-password | /profile/change-password | ChangePasswordView.vue (requiresAuth) |
| notifications | /notifications | NotificationsView.vue (requiresAuth) |

**Auth layout:**
| Name | Path | Component |
|---|---|---|
| login | /login | LoginView.vue |
| register | /register | RegisterView.vue |
| forgot-password | /forgot-password | ForgotPasswordView.vue |
| reset-password | /reset-password | ResetPasswordView.vue |

**Admin layout (`/admin`, requiresAuth + role:admin):**
| Name | Path | Component |
|---|---|---|
| admin.dashboard | /admin | AdminDashboardView.vue |
| admin.documents | /admin/documents | AdminDocumentsView.vue |
| admin.documents.new | /admin/documents/new | AdminDocumentFormView.vue |
| admin.documents.edit | /admin/documents/:id/edit | AdminDocumentFormView.vue |
| admin.users | /admin/users | AdminUsersView.vue |
| admin.categories | /admin/categories | AdminCategoriesView.vue |
| admin.tags | /admin/tags | AdminTagsView.vue |
| admin.chatbot.intents | /admin/chatbot/intents | AdminChatbotIntentsView.vue |
| admin.chatbot.logs | /admin/chatbot/logs | AdminChatbotLogsView.vue |
| admin.broadcast | /admin/broadcast | AdminBroadcastView.vue |
| admin.proposals | /admin/proposals | AdminProposalsView.vue |

**Teacher layout (`/teacher`, requiresAuth + role:teacher):**
| Name | Path | Component |
|---|---|---|
| teacher.proposals | /teacher/proposals | TeacherProposalsView.vue |

**Error routes:**
| Name | Path |
|---|---|
| forbidden | /forbidden |
| not-found | /:pathMatch(.\*) |

**Navigation Guards:**

- Redirect to login if `requiresAuth` and not logged in (stores redirect query)
- Redirect to forbidden if role:admin and `!auth.isAdmin`
- Redirect to forbidden if role:teacher and `!auth.isTeacher`

---

## 15. FRONTEND STORES (Pinia)

### 15.1 auth.js (useAuthStore)

```
State:    user (ref, localStorage), token (ref, localStorage)
Computed: isLoggedIn (!!token), isAdmin (role.slug === 'admin'), isTeacher (role.slug === 'teacher')
Actions:
  - login(email, password)    → POST /auth/login; stores token + user
  - register(payload)         → POST /auth/register; then login()
  - fetchMe()                 → GET /auth/me; refreshes user
  - logout(redirect?)         → POST /auth/logout; clears storage; redirects to /login
  - forceLogout()             → clears storage; redirects if not on auth page (called by 401 interceptor)
Storage keys: TOKEN_KEY, USER_KEY, CHAT_STORAGE_KEY (cleared on logout)
```

### 15.2 notification.js (useNotificationStore)

```
State:  unreadCount (ref, number)
Actions:
  - setUnreadCount(n) → sets unreadCount
```

### 15.3 ui.js (useUiStore)

```
State:  sidebarCollapsed (ref, default: true), theme (ref, 'light')
Actions:
  - toggleSidebar() → toggles sidebarCollapsed
```

---

## 16. FRONTEND SERVICES

All services use `src/services/http.js` (Axios instance).

### 16.1 http.js

```
baseURL: API_BASE_URL (from @/constants)
timeout: 20000ms
Request interceptor:  attaches Bearer token from localStorage
Response interceptor:
  - Unwraps {success: true, data: ...} envelope → returns data directly
  - 401 + token present → calls forceLogout()
  - 403 → toast.error('Bạn không có quyền truy cập')
  - 500+ → toast.error('Lỗi máy chủ, thử lại sau')
```

### 16.2 authService.js

```
login(payload), register(payload), forgotPassword(email),
resetPassword(payload), me(), logout()
```

### 16.3 documentService.js

```
Public:  list(params), featured(), popular(), recent(), recommended(), detail(slug), related(id), download(id), toggleFavorite(id), rate(id, payload)
Admin:   adminList(params), adminDetail(id), adminCreate(payload), adminUpdate(id, payload), adminDelete(id)
```

### 16.4 searchService.js

```
search(params), suggestions(q), trending()
```

### 16.5 chatbotService.js

```
suggestions(), ask(payload)
```

### 16.6 profileService.js

```
getProfile(), updateProfile(payload), updateAvatar(payload),
changePassword(payload), favorites(params), history(params), removeFavorite(id)
```

### 16.7 notificationService.js

```
list(params)     → unwraps items + unread_count from response
markRead(id)     → PATCH /notifications/{id}/read
markAllRead()    → POST /notifications/read-all
```

### 16.8 categoryService.js

```
Public: tree(), list(params), detail(slug)
Admin:  adminList(params), adminCreate(payload), adminUpdate(id, payload), adminDelete(id)
```

### 16.9 tagService.js

```
Public: list(params)
Admin:  adminList(params), adminCreate(payload), adminUpdate(id, payload), adminDelete(id)
```

### 16.10 statsService.js

```
overview(), charts(), trendingKeywords()
```

### 16.11 adminUserService.js

```
list(params), detail(id), create(payload), update(id, payload), delete(id), updateStatus(id, payload)
```

### 16.12 adminChatbotService.js

```
intents(params), intentDetail(id), createIntent(payload), updateIntent(id, payload), deleteIntent(id)
logs(params)
```

### 16.13 adminBroadcastService (in adminChatbotService.js)

```
broadcast(payload)  → POST /admin/notifications/broadcast
```

### 16.14 proposalService.js

```
Teacher: list(params), create(payload), delete(id)
Admin:   adminList(params), adminPendingCount(), adminShow(id), adminApprove(id), adminReject(id, payload)
```

### 16.15 metaService.js

```
roles()  → GET /roles
```

### 16.16 sendMailService.js

EmailJS browser-side email delivery:

- `isClientEmailConfigured()` — checks VITE*EMAILJS*\* env vars
- `buildPasswordResetPublicLink({email, token})` — builds `/reset-password?email=...&token=...` URL
- `sendEmailHelper(toEmail, htmlContent, options)` — sends via EmailJS REST API
- `sendRegistrationOtpEmail(toEmail, verificationCode, expiryMinutes, displayName)` — OTP email
- `sendPasswordResetEmail(toEmail, resetLinkOrPayload)` — password reset email
- `deliverRegistrationOtpFromClient(toEmail, otp, fullName, expiryMinutes)` — client-side OTP delivery

---

## 17. FRONTEND COMPOSABLES

### 17.1 useAuth.js

```
Wraps useAuthStore via storeToRefs.
Returns: user, token, isLoggedIn, isAdmin, isTeacher, roleSlug (computed), login, register, logout, fetchMe
```

### 17.2 useDebounce.js

```
useDebouncedRef(value, delay=300) — reactive debounced ref
debounce(fn, delay=300)           — function wrapper
```

### 17.3 usePagination.js

```
usePagination(initialPage=1, perPage=12)
Returns: page (ref), perPage (ref), total (ref), totalPages (computed), setTotal(n), next(), prev()
```

### 17.4 useToast.js

```
Wraps vue-sonner toast.
Returns: success(msg), error(msg), info(msg)
```

### 17.5 useUploadImage.js

```
Uses TanStack useMutation to upload file to Cloudinary.
Config from: CLOUDINARY_CLOUD_NAME, CLOUDINARY_UPLOAD_PRESET (@/constants)
Returns: { url, public_id, mime, size } on success
Folder: 'TriThucSo' in Cloudinary
```

### 17.6 useCountUp.js

```
useCountUp(endValue, duration=1500)
Animates count from 0 to endValue using cubic-out easing with requestAnimationFrame.
Returns: { current (ref), start() }
Used in: HomeView stats display
```

---

## 18. FRONTEND VIEWS

### Public Views (src/views/public/)

| File                   | Route             | Description                                                                                                   |
| ---------------------- | ----------------- | ------------------------------------------------------------------------------------------------------------- |
| HomeView.vue           | /                 | Landing page: hero, stats (useCountUp), featured docs, popular docs, recent docs, categories grid, search bar |
| SearchView.vue         | /search           | Advanced search with filters (category, year range, language, tag, sort), pagination, did_you_mean            |
| DocumentDetailView.vue | /documents/:slug  | Document detail: cover, metadata, tags, ratings, reviews, related documents, favorite toggle, download        |
| CategoryView.vue       | /categories/:slug | Documents filtered by category, sub-category navigation                                                       |
| LoginView.vue          | /login            | Email+password login form (vee-validate+yup)                                                                  |
| RegisterView.vue       | /register         | Registration form                                                                                             |
| ForgotPasswordView.vue | /forgot-password  | Forgot password → calls API → sends email via EmailJS                                                         |
| ResetPasswordView.vue  | /reset-password   | Reset password form (reads email+token from query params)                                                     |

### User Views (src/views/user/)

| File                   | Route                    | Description                                       |
| ---------------------- | ------------------------ | ------------------------------------------------- |
| ProfileView.vue        | /profile                 | View/edit profile, avatar upload (Cloudinary)     |
| FavoritesView.vue      | /profile/favorites       | Paginated favorite documents, remove favorite     |
| HistoryView.vue        | /profile/history         | Search history + document view history            |
| ChangePasswordView.vue | /profile/change-password | Change password form                              |
| NotificationsView.vue  | /notifications           | Paginated notifications, mark read, mark all read |

### Admin Views (src/views/admin/)

| File                        | Route                        | Description                                                                                        |
| --------------------------- | ---------------------------- | -------------------------------------------------------------------------------------------------- |
| AdminDashboardView.vue      | /admin                       | Stats overview cards, charts (visits 30d, category distribution, top documents), trending keywords |
| AdminDocumentsView.vue      | /admin/documents             | Document list with filters, pagination, delete, link to form                                       |
| AdminDocumentFormView.vue   | /admin/documents/new + /edit | Create/edit document form with Cloudinary upload for cover image and PDF                           |
| AdminUsersView.vue          | /admin/users                 | User management: list, create, edit, toggle status, delete                                         |
| AdminCategoriesView.vue     | /admin/categories            | Category tree management: create, edit, delete                                                     |
| AdminTagsView.vue           | /admin/tags                  | Tag management: create, edit, delete                                                               |
| AdminChatbotIntentsView.vue | /admin/chatbot/intents       | Intent CRUD: keyword arrays, response templates                                                    |
| AdminChatbotLogsView.vue    | /admin/chatbot/logs          | Chatbot conversation logs with date filters                                                        |
| AdminBroadcastView.vue      | /admin/broadcast             | Send broadcast notification to all/students/teachers                                               |
| AdminProposalsView.vue      | /admin/proposals             | Review teacher document proposals: approve, reject with reason                                     |

### Teacher Views (src/views/teacher/)

| File                     | Route              | Description                                                             |
| ------------------------ | ------------------ | ----------------------------------------------------------------------- |
| TeacherProposalsView.vue | /teacher/proposals | Teacher's submitted proposals list, submit new proposal, delete pending |

### Error Views (src/views/error/)

| File              | Route            | Description        |
| ----------------- | ---------------- | ------------------ |
| NotFoundView.vue  | /:pathMatch(.\*) | 404 Not Found page |
| ForbiddenView.vue | /forbidden       | 403 Forbidden page |

---

## 19. USER ROLES & PERMISSIONS SUMMARY

| Feature                  | Guest | Student | Teacher | Admin |
| ------------------------ | ----- | ------- | ------- | ----- |
| Browse documents         | YES   | YES     | YES     | YES   |
| Search documents         | YES   | YES     | YES     | YES   |
| View document detail     | YES   | YES     | YES     | YES   |
| Download document        | NO    | YES     | YES     | YES   |
| Rate/Review document     | NO    | YES     | YES     | YES   |
| Favorite document        | NO    | YES     | YES     | YES   |
| View profile/history     | NO    | YES     | YES     | YES   |
| Chatbot                  | YES   | YES     | YES     | YES   |
| Propose documents        | NO    | NO      | YES     | YES   |
| Admin dashboard          | NO    | NO      | NO      | YES   |
| Manage documents         | NO    | NO      | NO      | YES   |
| Manage users             | NO    | NO      | NO      | YES   |
| Manage categories/tags   | NO    | NO      | NO      | YES   |
| Manage chatbot intents   | NO    | NO      | NO      | YES   |
| View chatbot logs        | NO    | NO      | NO      | YES   |
| Broadcast notifications  | NO    | NO      | NO      | YES   |
| Approve/reject proposals | NO    | NO      | NO      | YES   |

---

## 20. KEY ARCHITECTURAL DECISIONS

1. **Stateless JWT-style auth via Sanctum** — Bearer tokens stored in localStorage; 401 triggers `forceLogout()` in Axios interceptor.
2. **SoftDeletes on Document** — deleted documents preserved for audit; `Document::withTrashed()` used in slug uniqueness check.
3. **Proposal workflow** — Teachers create documents with `status=pending`; admin can approve (→ published) or reject (→ rejected + reason); notifications sent to proposer automatically.
4. **Client-side email** — Password reset and OTP emails sent via EmailJS from the browser (avoids need for SMTP server); backend generates token and returns `clientMail: {email, token}`.
5. **Cloudinary for file storage** — Both cover images and PDF documents are uploaded to Cloudinary from the browser; backend stores only the URL.
6. **Synonym expansion** — SearchService expands queries using the Synonym table to support Vietnamese language aliases (e.g. "ai" → "trí tuệ nhân tạo", "machine learning").
7. **Fuzzy "did you mean"** — Unicode-safe Levenshtein (mb-aware) with adaptive thresholds based on query length.
8. **Rule-based chatbot** — No AI dependency; intent matching via keyword counting; 15 configurable intents with template placeholders; admin can add/edit via UI.
9. **Content-based recommendation** — Built from user's 10 most recent views → category affinity → recommend similar unpublished-not-yet-viewed docs; cached 5 min.
10. **Custom CASE-WHEN relevance scoring** — SQL-level ranking that weights title matches higher without MySQL FULLTEXT dependency for LIKE queries.
11. **API response envelope** — All responses use `ApiResponse::success()` / `ApiResponse::paginate()` helper: `{success: bool, data: any, message?: string}`.
12. **Role middleware** — Single `RoleMiddleware` accepts comma-separated roles for flexible guard composition.

---

## 21. EXTERNAL INTEGRATIONS

| Service    | Purpose                                                 | Config                                                                                    |
| ---------- | ------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| Cloudinary | File storage (documents PDFs, cover images, avatars)    | CLOUDINARY_CLOUD_NAME, CLOUDINARY_UPLOAD_PRESET (frontend env)                            |
| EmailJS    | Browser-side transactional email (forgot-password, OTP) | VITE_EMAILJS_SERVICE_ID, VITE_EMAILJS_TEMPLATE_ID, VITE_EMAILJS_PUBLIC_KEY (frontend env) |
| MySQL      | Primary database                                        | DB\_\* vars in backend .env                                                               |

---

## 22. DEVELOPMENT SETUP

**Backend:**

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve  # http://localhost:8000
```

**Frontend:**

```bash
cd frontend
npm install
# copy .env.example to .env, set VITE_API_BASE_URL, VITE_APP_URL, Cloudinary vars, EmailJS vars
npm run dev  # http://localhost:5173
```

**Default accounts (seeded):**

- Admin: admin@tts.com / Admin@123
- Teacher: teacher@tts.com / Teacher@123
- Student: student@tts.com / Student@123

## 23. LUỒNG NGHIỆP VỤ QUAN TRỌNG

### 23.1 Đăng ký tài khoản

1. Người dùng truy cập `/register` → `RegisterView.vue`
2. Điền form: name, email, password, confirm, phone, student_code (tùy chọn)
3. Frontend validate với vee-validate + yup (email format, password min:8, confirmed)
4. `authService.register(payload)` → `POST /api/v1/auth/register`
5. Backend: `RegisterRequest` validate → tạo User với `role_id` của 'student' → trả UserResource + 201
6. Frontend: `useAuthStore.register()` gọi tiếp `login()` → lưu token + user vào localStorage
7. Redirect về trang chủ `/`

### 23.2 Đăng nhập / Đăng xuất

**Đăng nhập:**

1. Người dùng truy cập `/login` → `LoginView.vue`
2. Điền email + password → `authService.login()` → `POST /api/v1/auth/login`
3. Backend: `LoginRequest` → kiểm tra credentials → kiểm tra `status = 'active'` → xoá tokens cũ → tạo Sanctum token → trả `{token, user: UserResource}`
4. Frontend: lưu token vào localStorage (`tts_token`) + user vào localStorage (`tts_user`)
5. Redirect về `/` hoặc `query.redirect` nếu có

**Đăng xuất:**

1. Click "Đăng xuất" trong `AppHeader.vue` dropdown
2. `authService.logout()` → `POST /api/v1/auth/logout` (xoá token server-side)
3. `useAuthStore.logout()`: xoá localStorage, reset state, redirect `/login`

### 23.3 Quên mật khẩu (EmailJS client-side)

1. Người dùng truy cập `/forgot-password` → `ForgotPasswordView.vue`
2. Nhập email → `POST /api/v1/auth/forgot-password`
3. Backend: validate email exists → tạo reset token qua `Password::createToken()` → **KHÔNG** gửi mail; trả `{clientMail: {email, token}}`
4. Frontend: nhận `clientMail` → gọi `sendMailService.sendPasswordResetEmail(email, {email, token})`
5. `sendMailService` dùng EmailJS REST API (không cần SMTP server) gửi email chứa link `{VITE_SITE_URL}/reset-password?email=...&token=...`
6. Người dùng click link → `ResetPasswordView.vue` đọc `email` + `token` từ query params
7. Điền password mới → `POST /api/v1/auth/reset-password` → reset qua Laravel Password broker

### 23.4 Tìm kiếm tài liệu (luồng đầy đủ)

1. Người dùng gõ query vào `SearchView.vue`, có thể chọn thêm: category, year from/to, language, tag, sort
2. `searchService.search(params)` → `GET /api/v1/search?q=...&category=...&...`
3. Backend `SearchController.search()` → `SearchService.search(query, filters, userId)`:
   a. **Mở rộng synonym**: `expandSynonyms(query)` → tra bảng `synonyms` → thêm các từ đồng nghĩa
   b. **LIKE matching**: với mỗi keyword đã mở rộng, OR WHERE title/description/author LIKE '%keyword%'
   c. **Lọc category**: nếu có category filter → lấy thêm child IDs → `whereIn(category_id, [parent, ...children])`
   d. **Lọc khác**: year, year_from, year_to, language, tag
   e. **Sắp xếp**: relevance (CASE WHEN scoring) / newest / popular / rating
   f. **Lọc status**: chỉ trả documents có `status = 'published'`
   g. Paginate → lưu `SearchHistory`
4. Nếu kết quả < 3: gọi thêm `fuzzyMatch(query)` → Unicode Levenshtein → trả `did_you_mean`
5. Response: `{items, meta, expanded_keywords, did_you_mean?}`
6. Frontend hiển thị kết quả, hiển thị "Ý bạn muốn tìm: ..." nếu có `did_you_mean`
7. Autocomplete (gõ ≥ 2 ký tự): `GET /api/v1/search/suggestions?q=...` → merge history + document titles

### 23.5 Xem chi tiết và tải tài liệu

1. Click document card → navigate `/documents/:slug` → `DocumentDetailView.vue`
2. `documentService.detail(slug)` → `GET /api/v1/documents/{slug}`
3. Backend: tìm doc theo slug (status=published) → `increment('view_count')` → tạo `DocumentView` → `Cache::forget('recommend.user.{userId}')` → trả DocumentResource với ratings + avg_rating
4. Trang hiển thị: cover, metadata, description, tags, rating trung bình, danh sách reviews, related documents
5. Nút "Tải xuống" (cần đăng nhập): `documentService.download(id)` → `GET /api/v1/documents/{id}/download` → `increment('download_count')` → trả `file_url` → mở tab mới

### 23.6 Đề xuất tài liệu (Teacher Proposal Flow)

1. Giảng viên đăng nhập → menu "Đề xuất tài liệu" → `/teacher/proposals` → `TeacherProposalsView.vue`
2. Click "Đề xuất mới" → `TeacherProposalFormDialog.vue` mở
3. Upload PDF + ảnh bìa lên Cloudinary trực tiếp từ browser → nhận URL
4. Điền metadata: title, description, author, publisher, year, category, tags, v.v.
5. Submit → `proposalService.create(payload)` → `POST /api/v1/teacher/proposals`
6. Backend `Teacher\ProposalController.store()`: validate `StoreProposalRequest` → tạo Document với `status='pending'`, `proposed_by=userId` → sync tags → trả 201
7. Giảng viên thấy đề xuất với badge "Chờ duyệt" trong danh sách
8. Có thể xoá nếu vẫn pending: `DELETE /api/v1/teacher/proposals/{id}` (chỉ xoá được của mình + status=pending)

### 23.7 Admin duyệt / từ chối đề xuất

1. Admin vào `/admin/proposals` → `AdminProposalsView.vue`
2. Mặc định tab "Chờ duyệt"; DataTable hiển thị proposals kèm proposer name
3. Click "Xem" → `Dialog` hiển thị chi tiết: cover, metadata, PDF link, thông tin giảng viên
4. **Duyệt**: Click "Duyệt tài liệu" → confirm → `proposalService.adminApprove(id)` → `POST /api/v1/admin/proposals/{id}/approve`
   - Backend: `status=published`, `reviewed_by=admin.id`, `reviewed_at=now()`
   - Tạo Notification cho giảng viên: "Đề xuất tài liệu được duyệt"
5. **Từ chối**: Click "Từ chối" → Dialog nhập lý do → `proposalService.adminReject(id, {reason})` → `POST /api/v1/admin/proposals/{id}/reject`
   - Backend: `status=rejected`, `rejection_reason=reason`, `reviewed_by=admin.id`, `reviewed_at=now()`
   - Tạo Notification cho giảng viên: "Đề xuất tài liệu bị từ chối" + lý do trong content
6. Giảng viên nhận thông báo trong `NotificationsView.vue`, click xem nội dung đầy đủ

### 23.8 Quản lý tài liệu (Admin CRUD)

1. Admin vào `/admin/documents` → `AdminDocumentsView.vue`
2. DataTable: filter theo q (title/author), category_id, year, is_featured
3. Tạo mới: `/admin/documents/new` → `AdminDocumentFormView.vue` → `AdminDocumentFormDialog.vue`
   - Upload file qua `ImageUploader.vue` → Cloudinary
   - Nhập metadata → slug tự sinh từ title
   - Submit → `documentService.adminCreate()` → `POST /admin/documents` → `status=published` (default)
4. Sửa: `/admin/documents/:id/edit` → load document → `documentService.adminUpdate()`
5. Xoá: click nút xoá → `ConfirmDialog` → `documentService.adminDelete()` → soft delete

### 23.9 Gửi thông báo broadcast

1. Admin vào `/admin/broadcast` → `AdminBroadcastView.vue`
2. Chọn đối tượng: Tất cả / Sinh viên / Giảng viên
3. Nhập tiêu đề + nội dung
4. Gửi → `POST /admin/notifications/broadcast`
5. Backend `NotificationAdminController.broadcast()`: truy vấn users theo role → tạo Notification cho mỗi user (chunked 500)
6. Người nhận thấy badge trên chuông thông báo → vào `/notifications` → click xem nội dung đầy đủ trong Dialog

### 23.10 Chatbot hỏi đáp

1. Người dùng click biểu tượng chatbot (góc phải màn hình) → `ChatbotWidget.vue` mở
2. Hiển thị 5 gợi ý câu hỏi từ `chatbotService.suggestions()`
3. Người dùng gõ câu hỏi → `chatbotService.ask({question})`
4. Backend `ChatbotController.ask()` → `ChatbotService.ask(question, userId)`:
   a. Lowercase normalise câu hỏi
   b. Lặp qua tất cả active intents → đếm keyword match
   c. Intent có score cao nhất được chọn; nếu score=0 → dùng 'fallback'
   d. `renderTemplate()`: thay thế {{popular_documents}}, {{categories_list}}, {{new_documents}}, v.v.
   e. Lưu `ChatbotLog`
5. Widget nhận response, tự scroll xuống dưới, hiển thị câu trả lời

---

## 24. ĐỘ HOÀN THIỆN & TODO

### ✅ Chức năng đã hoàn thành đầy đủ

- Xác thực người dùng: đăng ký, đăng nhập, đăng xuất, quên mật khẩu (EmailJS)
- Hệ thống phân quyền 3 vai trò: admin / teacher / student
- Duyệt, tìm kiếm, xem chi tiết, tải tài liệu (chỉ published)
- Tìm kiếm nâng cao: synonym expansion, relevance scoring, fuzzy "did you mean", category với subcategory
- Autocomplete + trending keywords
- Yêu thích, đánh giá, bình luận tài liệu
- Hệ thống thông báo: in-app, broadcast (admin), proposal notifications
- Lịch sử tìm kiếm + lịch sử xem tài liệu
- Gợi ý tài liệu (content-based recommendation)
- Chatbot hỏi đáp rule-based (15 intents, configurable)
- Đề xuất tài liệu (teacher) + duyệt/từ chối (admin) + thông báo
- Quản lý admin đầy đủ: documents, users, categories, tags, chatbot intents/logs, broadcast, proposals
- Upload file (PDF + ảnh) qua Cloudinary từ browser
- Trang chủ: carousel nổi bật, thống kê live, popular/recent/recommended sections
- Dashboard admin: stats, biểu đồ visits 30 ngày, phân bổ danh mục, top documents
- Responsive UI trên mobile/tablet/desktop

### 🟡 Chức năng dang dở / cần cải thiện

- Chatbot chỉ rule-based, chưa tích hợp LLM thực sự (không có AI/NLP ngoài keyword matching)
- Password reset email gửi client-side qua EmailJS → phụ thuộc CORS/quota của EmailJS; không có server-side fallback
- Download counter dùng `Cache::increment('stats.downloads.{date}')` nhưng không persist vào DB → mất khi cache clear
- Không có rate limiting trên API endpoints (có thể bị abuse)
- Không có email verification sau đăng ký (email_verified_at không được set)
- Không có hệ thống reset session / force logout toàn bộ thiết bị
- Không có kiểm tra trùng lặp tài liệu khi giảng viên đề xuất

### ⏳ Chưa implement / planned

- Không có AI/vector semantic search (hệ thống dùng LIKE + synonym expansion, không có embedding)
- Không có hệ thống tag tự động gợi ý dựa trên nội dung PDF
- Không có preview PDF inline trong trình duyệt
- Không có hệ thống comment threading / reply
- Không có export báo cáo (CSV/Excel/PDF)
- Không có hệ thống subscription/follow danh mục
- Không có full-text search qua MySQL FULLTEXT (index tồn tại nhưng SearchService dùng LIKE thay vì MATCH AGAINST)

---

## 25. ẢNH MÀN HÌNH & TÀI NGUYÊN MINH HOẠ

| Đường dẫn                                           | Mô tả                               | Dùng cho phần nào trong báo cáo   |
| --------------------------------------------------- | ----------------------------------- | --------------------------------- |
| `frontend/src/assets/images/logo.png`               | Logo chính Tri Thức Số              | Bìa báo cáo, giới thiệu hệ thống  |
| `frontend/src/assets/images/logo-text.png`          | Logo kèm text (dùng trong Header)   | Mô tả giao diện, header           |
| `frontend/src/assets/images/icon-logo.png`          | Icon logo vuông                     | Favicon, biểu tượng app           |
| `frontend/src/assets/images/icon-logo-removebg.png` | Icon logo nền trong suốt            | Slide presentation                |
| `frontend/src/assets/hero.png`                      | Ảnh hero section trang chủ          | Minh hoạ giao diện HomeView       |
| Ảnh màn hình thực tế                                | Chụp từ browser khi chạy dev server | Chương UI/UX, các luồng nghiệp vụ |

> **Lưu ý**: Để minh hoạ đầy đủ, nên chụp screenshot các màn hình chính khi chạy `npm run dev`:
>
> - Trang chủ (HomeView) — hero, stats banner, featured carousel
> - Trang tìm kiếm (SearchView) — bộ lọc, kết quả, did_you_mean
> - Chi tiết tài liệu (DocumentDetailView) — metadata, rating, reviews
> - Dashboard admin — charts, stats cards
> - Trang đề xuất giảng viên — danh sách proposals + form
> - Admin duyệt đề xuất — dialog chi tiết
> - Chatbot widget — đang mở, đang chat

---

## 26. LƯU Ý ĐẶC BIỆT CHO BÁO CÁO

### Điểm nổi bật kỹ thuật

1. **Tìm kiếm thông minh không cần AI ngoài**: Hệ thống đạt được tìm kiếm "ngữ nghĩa giới hạn" qua synonym expansion (15 nhóm từ đồng nghĩa tiếng Việt), relevance scoring bằng CASE-WHEN SQL, và fuzzy correction bằng Unicode Levenshtein — hoàn toàn trong MySQL + PHP, không cần external AI API.
2. **Kiến trúc đề xuất-phê duyệt hoàn chỉnh**: Document proposal workflow với 3 trạng thái (pending/published/rejected), phân quyền chặt chẽ, notification tự động, và giao diện riêng cho từng vai trò.
3. **Email client-side qua EmailJS**: Giải pháp sáng tạo để gửi email không cần SMTP server, phù hợp triển khai không có backend email service.

### Cảnh báo khi viết báo cáo

- **KHÔNG có vector embedding hay semantic AI search** — đề tài gốc trong prompt là "website bán hàng tích hợp AI tìm kiếm" nhưng thực tế đây là **hệ thống thư viện điện tử** với keyword search + synonym expansion. Phần "AI" trong báo cáo phải mô tả đúng là: synonym-based query expansion + fuzzy match + rule-based chatbot.
- **MySQL FULLTEXT index tồn tại** nhưng SearchService dùng LIKE thay vì `MATCH... AGAINST` — có thể là điểm cần giải thích trong báo cáo.
- **Không có Docker/CI/CD** — hệ thống chạy thuần local development.
- Tên biến môi trường frontend cần prefix `VITE_` để Vite expose ra browser.

---

_End of PROJECT_CONTEXT.md_
