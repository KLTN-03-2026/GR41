/** Gán màu + icon theo tên/slug danh mục (8 nhóm phổ biến). */

const RULES = [
  { re: /cntt|công nghệ|tin học|phần mềm|máy tính|lập trình/i, tw: 'blue', icon: 'mdi:laptop' },
  { re: /kinh tế|kế toán|tài chính|marketing/i, tw: 'amber', icon: 'mdi:finance' },
  { re: /ngôn ngữ|tiếng anh|ngoại ngữ|ielts/i, tw: 'pink', icon: 'mdi:translate' },
  { re: /khoa học|vật lý|hóa học|sinh học/i, tw: 'cyan', icon: 'mdi:flask' },
  { re: /văn học|thơ|truyện|tác phẩm/i, tw: 'rose', icon: 'mdi:book-open-page-variant' },
  { re: /giáo dục|sư phạm|đại học/i, tw: 'violet', icon: 'mdi:school' },
  { re: /y học|y khoa|sức khỏe|điều dưỡng/i, tw: 'emerald', icon: 'mdi:medical-bag' },
  { re: /pháp luật|luật|pháp chế/i, tw: 'slate', icon: 'mdi:gavel' },
]

const FALLBACK_KEYS = ['indigo', 'teal', 'orange']

/** Class Tailwind tĩnh (tránh dynamic string để JIT nhận đủ). */
export const CATEGORY_SKINS = {
  blue: {
    card: 'bg-gradient-to-br from-blue-50 to-white ring-blue-100/80',
    iconBox: 'bg-blue-500/10 text-blue-600',
    blob: 'bg-blue-400/40',
    chip: 'bg-blue-100/80 text-blue-800',
  },
  amber: {
    card: 'bg-gradient-to-br from-amber-50 to-white ring-amber-100/80',
    iconBox: 'bg-amber-500/10 text-amber-700',
    blob: 'bg-amber-400/40',
    chip: 'bg-amber-100/80 text-amber-900',
  },
  pink: {
    card: 'bg-gradient-to-br from-pink-50 to-white ring-pink-100/80',
    iconBox: 'bg-pink-500/10 text-pink-600',
    blob: 'bg-pink-400/35',
    chip: 'bg-pink-100/80 text-pink-800',
  },
  cyan: {
    card: 'bg-gradient-to-br from-cyan-50 to-white ring-cyan-100/80',
    iconBox: 'bg-cyan-500/10 text-cyan-700',
    blob: 'bg-cyan-400/35',
    chip: 'bg-cyan-100/80 text-cyan-900',
  },
  rose: {
    card: 'bg-gradient-to-br from-rose-50 to-white ring-rose-100/80',
    iconBox: 'bg-rose-500/10 text-rose-600',
    blob: 'bg-rose-400/35',
    chip: 'bg-rose-100/80 text-rose-800',
  },
  violet: {
    card: 'bg-gradient-to-br from-violet-50 to-white ring-violet-100/80',
    iconBox: 'bg-violet-500/10 text-violet-700',
    blob: 'bg-violet-400/35',
    chip: 'bg-violet-100/80 text-violet-900',
  },
  emerald: {
    card: 'bg-gradient-to-br from-emerald-50 to-white ring-emerald-100/80',
    iconBox: 'bg-emerald-500/10 text-emerald-700',
    blob: 'bg-emerald-400/35',
    chip: 'bg-emerald-100/80 text-emerald-900',
  },
  slate: {
    card: 'bg-gradient-to-br from-slate-50 to-white ring-slate-200/80',
    iconBox: 'bg-slate-500/10 text-slate-700',
    blob: 'bg-slate-400/30',
    chip: 'bg-slate-100/90 text-slate-800',
  },
  indigo: {
    card: 'bg-gradient-to-br from-indigo-50 to-white ring-indigo-100/80',
    iconBox: 'bg-indigo-500/10 text-indigo-700',
    blob: 'bg-indigo-400/35',
    chip: 'bg-indigo-100/80 text-indigo-900',
  },
  teal: {
    card: 'bg-gradient-to-br from-teal-50 to-white ring-teal-100/80',
    iconBox: 'bg-teal-500/10 text-teal-700',
    blob: 'bg-teal-400/35',
    chip: 'bg-teal-100/80 text-teal-900',
  },
  orange: {
    card: 'bg-gradient-to-br from-orange-50 to-white ring-orange-100/80',
    iconBox: 'bg-orange-500/10 text-orange-700',
    blob: 'bg-orange-400/35',
    chip: 'bg-orange-100/80 text-orange-900',
  },
}

export function getCategoryTheme(cat, index = 0) {
  const label = [cat?.name, cat?.slug].filter(Boolean).join(' ')
  for (const r of RULES) {
    if (r.re.test(label)) return { key: r.tw, icon: r.icon, skin: CATEGORY_SKINS[r.tw] }
  }
  const key = FALLBACK_KEYS[index % FALLBACK_KEYS.length]
  return { key, icon: 'mdi:folder-outline', skin: CATEGORY_SKINS[key] }
}
