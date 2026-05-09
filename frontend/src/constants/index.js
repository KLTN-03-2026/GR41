export const CLOUDINARY_CLOUD_NAME = import.meta.env.VITE_CLOUDINARY_CLOUD_NAME
export const CLOUDINARY_UPLOAD_PRESET = import.meta.env.VITE_CLOUDINARY_UPLOAD_PRESET
export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api/v1'

export const ROLES = {
  ADMIN: 'admin',
  TEACHER: 'teacher',
  STUDENT: 'student',
}

export const TOKEN_KEY = 'tts_token'
export const USER_KEY = 'tts_user'
export const CHAT_STORAGE_KEY = 'tts_chat_history'

export const ROUTES = {
  HOME: '/',
  LOGIN: '/login',
  ADMIN: '/admin',
}

/** EmailJS — chỉ dùng từ trình duyệt (khai báo trong `.env`). */
export const EMAILJS_CONFIG = {
  serviceId: 'oceanpham0102',
  templateId: 'template_oceanpham',
  publicKey: 'Dh-Ki1qa0gpbfrRF9',
  fromName: 'Tri Thức Số',
  logoUrl: 'https://res.cloudinary.com/oceanmind/image/upload/v1750514254/ChatGPT_Image_20_56_36_21_thg_6_2025_1_i9sns3.png',
}