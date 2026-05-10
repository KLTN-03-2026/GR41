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

_End of PROJECT_CONTEXT.md_
