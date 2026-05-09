/**
 * EmailJS chỉ từ trình duyệt (VITE_EMAILJS_*).
 * Quên mật khẩu: API trả `clientMail: { email, token }` — app ghép URL `/reset-password` rồi gửi mail.
 * Template EmailJS: to_email, title, from_name, name, subject, message, src
 */

import { EMAILJS_CONFIG } from '@/constants'

const EMAILJS_ENDPOINT = 'https://api.emailjs.com/api/v1.0/email/send'
const BRAND = 'Tri Thức Số'

function getConfig() {
  return {
    serviceId: EMAILJS_CONFIG.serviceId,
    templateId: EMAILJS_CONFIG.templateId,
    publicKey: EMAILJS_CONFIG.publicKey,
    fromName: EMAILJS_CONFIG.fromName,
    logoUrl: EMAILJS_CONFIG.logoUrl,
  }
}

export function isClientEmailConfigured() {
  const c = getConfig()
  return Boolean(c.serviceId && c.templateId && c.publicKey)
}

/**
 * @param {{ email: string, token: string } | string} payloadOrLegacy — đối tượng từ API hoặc chuỗi legacy (URL đầy đủ)
 */
export function buildPasswordResetPublicLink(payloadOrLegacy) {
  const base = String(import.meta.env.VITE_APP_URL || '').replace(/\/$/, '')
  if (!base) return ''

  if (
    payloadOrLegacy &&
    typeof payloadOrLegacy === 'object' &&
    'email' in payloadOrLegacy &&
    'token' in payloadOrLegacy
  ) {
    const email = String(payloadOrLegacy.email || '').trim()
    const token = String(payloadOrLegacy.token || '').trim()
    if (!email || !token) return ''
    const q = new URLSearchParams({
      email,
      token,
    })
    return `${base}/reset-password?${q.toString()}`
  }

  const raw = String(payloadOrLegacy || '').trim()
  if (!raw) return ''
  if (raw.startsWith('http')) return raw
  return ''
}

/**
 * @param {string} toEmail
 * @param {string} htmlContent — HTML an toàn (đã escape phía gọi nếu cần)
 * @param {object} [options]
 */
export async function sendEmailHelper(toEmail, htmlContent, options = {}) {
  if (!toEmail || !htmlContent) {
    throw new Error('Email address and content are required')
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(toEmail)) {
    throw new Error('Invalid email address format')
  }

  const { serviceId, templateId, publicKey, fromName, logoUrl } = getConfig()
  if (!serviceId || !templateId || !publicKey) {
    console.warn('[sendMailService] Thiếu VITE_EMAILJS_* — không gửi được email.')
    return false
  }

  const defaults = {
    title: `${BRAND} — Thông báo`,
    subject: `Thông báo từ ${BRAND}`,
    name: 'bạn',
  }
  const merged = { ...defaults, ...options }

  const res = await fetch(EMAILJS_ENDPOINT, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      service_id: serviceId,
      template_id: templateId,
      user_id: publicKey,
      template_params: {
        to_email: toEmail,
        title: merged.title,
        from_name: fromName,
        name: merged.name,
        subject: merged.subject,
        message: htmlContent,
        src: logoUrl,
      },
    }),
  })

  if (!res.ok) {
    const errText = await res.text()
    console.error('EmailJS error:', errText)
    return false
  }
  return true
}

function escapeHtml(s) {
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
}

/** Mã OTP đăng ký (gửi từ client khi cần). */
export async function sendRegistrationOtpEmail(
  toEmail,
  verificationCode,
  expiryMinutes = 15,
  displayName = '',
) {
  const greeting = displayName
    ? `<p>Xin chào <strong>${escapeHtml(displayName)}</strong>,</p>`
    : '<p>Xin chào,</p>'
  const content = `
    ${greeting}
    <p>Mã xác minh đăng ký ${BRAND} của bạn là:</p>
    <p style="font-size:22px;font-weight:bold;letter-spacing:4px;color:#00e5a0;">${escapeHtml(String(verificationCode))}</p>
    <p>Mã có hiệu lực trong <strong>${expiryMinutes} phút</strong>.</p>
    <p>Trân trọng,<br/>${BRAND}</p>
  `
  return sendEmailHelper(toEmail, content, {
    title: `${BRAND} — Mã xác minh đăng ký`,
    subject: `${BRAND} — Mã xác minh đăng ký`,
  })
}

/**
 * Gửi OTP từ trình duyệt khi API trả `otpForClientEmail` (chỉ khi server không gửi được + env backend cho phép).
 */
export async function deliverRegistrationOtpFromClient(toEmail, otp, fullName, expiryMinutes = 15) {
  if (!otp || !isClientEmailConfigured()) return false
  return sendRegistrationOtpEmail(toEmail, otp, expiryMinutes, fullName || '')
}

/**
 * @param {string} toEmail
 * @param {string | { email: string, token: string }} resetLinkOrPayload — URL đầy đủ hoặc payload từ API
 */
export async function sendPasswordResetEmail(toEmail, resetLinkOrPayload) {
  let resetLink = ''
  if (
    resetLinkOrPayload &&
    typeof resetLinkOrPayload === 'object' &&
    resetLinkOrPayload.token &&
    resetLinkOrPayload.email
  ) {
    resetLink = buildPasswordResetPublicLink(resetLinkOrPayload)
  } else {
    const raw = String(resetLinkOrPayload || '').trim()
    resetLink = raw.startsWith('http') ? raw : buildPasswordResetPublicLink(raw)
  }
  if (!resetLink) {
    console.warn('[sendMailService] Thiếu VITE_APP_URL hoặc token/email — không ghép được link.')
    return false
  }
  const esc = escapeHtml(resetLink)
  const content = `
    <p>Bạn đã yêu cầu đặt lại mật khẩu ${BRAND}.</p>
    <p><a href="${esc}" style="display:inline-block;padding:12px 20px;background:#00e5a0;color:#0a1628;text-decoration:none;border-radius:8px;font-weight:600;">Đặt lại mật khẩu</a></p>
    <p style="word-break:break-all;font-size:12px;color:#666;">${esc}</p>
    <p>Liên kết hết hạn sau khoảng 1 giờ.</p>
    <p>Trân trọng,<br/>${BRAND}</p>
  `
  return sendEmailHelper(toEmail, content, {
    title: `${BRAND} — Đặt lại mật khẩu`,
    subject: `${BRAND} — Đặt lại mật khẩu`,
  })
}

export default sendEmailHelper
