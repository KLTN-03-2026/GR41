/** Escape HTML và render markdown links đơn giản + xuống dòng */
export function escapeHtml(s) {
  return String(s)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
}

export function renderChatMessage(text) {
  if (!text) return ''
  let s = escapeHtml(text)
  s = s.replace(/\r\n/g, '\n')
  s = s.replace(/\n/g, '<br>')
  s = s.replace(/\[([^\]]+)\]\((https?:[^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-blue-600 underline break-all">$1</a>')
  return s
}
