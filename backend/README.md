# Backend — Tri Thức Số (Laravel 11)

API REST cho hệ thống thư viện số **Tri Thức Số — TTS-2026**. Auth Bearer **Laravel Sanctum**. File/PDF/ảnh do frontend upload lên **Cloudinary**; backend chỉ lưu URL (`string`).

## Yêu cầu hệ thống

- PHP **8.2+**
- Composer **2.x**
- MySQL **8** (InnoDB, `utf8mb4_unicode_ci`)

## Cài đặt nhanh

```bash
cd backend
composer install
copy .env.example .env
php artisan key:generate
```

Chỉnh `.env` (đặc biệt `DB_*`), sau đó:

```bash
php artisan migrate:fresh --seed
php artisan serve --port=8000
```

```bash
cd /home/trithucso/backend

# Update thường (sửa controller, view, route, migration mới)
./deploy.sh

# Khi composer.json đổi (thêm/đổi package)
./deploy.sh --composer

# Khi Dockerfile đổi (đổi PHP version, thêm extension)
./deploy.sh --rebuild
```

Base URL API: `http://localhost:8000/api/v1`

## Tài khoản thử nghiệm

| Email           | Mật khẩu    | Vai trò    |
| --------------- | ----------- | ---------- |
| admin@tts.com   | Admin@123   | Admin      |
| teacher@tts.com | Teacher@123 | Giảng viên |
| student@tts.com | Student@123 | Sinh viên  |

Luồng kiểm tra Postman: **Login** (`POST /auth/login`) → copy `token` → Header `Authorization: Bearer {token}` → **GET** `/auth/me` → kỳ vọng **200**.

## Postman

Import collection: [`docs/API.postman_collection.json`](docs/API.postman_collection.json)

Export đầy đủ danh sách route:

```bash
php artisan route:list --path=api/v1
```

Hiện có **52** endpoint trong nhóm `api/v1`.

## Ghi chú CORS

Frontend dev: `http://localhost:5173`. Cấu hình tại `config/cors.php`.
