<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { Icon } from '@iconify/vue'
import ChatMessage from '@/components/chatbot/ChatMessage.vue'
import ChatTyping from '@/components/chatbot/ChatTyping.vue'
import { chatbotService } from '@/services/chatbotService'
import { renderChatMessage, escapeHtml } from '@/utils/chatMarkdown'
import { CHAT_STORAGE_KEY } from '@/constants'
import { useAuthStore } from '@/stores/auth'

function userHtml(t) {
  return escapeHtml(t || '').replace(/\n/g, '<br>')
}

const auth = useAuthStore()

const open = ref(false)
const loadingBoot = ref(false)
const typing = ref(false)
const input = ref('')
const inputError = ref('')
const suggestions = ref([])
const messages = ref([])
const messagesEl = ref(null)

async function scrollToBottom() {
  await nextTick()
  if (messagesEl.value) messagesEl.value.scrollTop = messagesEl.value.scrollHeight
}

watch(messages, scrollToBottom, { deep: true })
watch(typing, scrollToBottom)

function loadStorage() {
  try {
    const raw = localStorage.getItem(CHAT_STORAGE_KEY)
    messages.value = raw ? JSON.parse(raw) : []
  } catch {
    messages.value = []
  }
}

function saveStorage() {
  localStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(messages.value.slice(-50)))
}

watch(messages, saveStorage, { deep: true })

watch(
  () => auth.isLoggedIn,
  (loggedIn) => {
    if (!loggedIn) {
      messages.value = []
      localStorage.removeItem(CHAT_STORAGE_KEY)
    }
  },
)

async function bootSuggestions() {
  loadingBoot.value = true
  try {
    const raw = await chatbotService.suggestions()
    suggestions.value = Array.isArray(raw) ? raw : raw?.questions || raw?.data || raw?.items || []
    if (!Array.isArray(suggestions.value)) suggestions.value = []
    suggestions.value = suggestions.value.slice(0, 5)
  } catch {
    suggestions.value = [
      'Tài liệu phổ biến',
      'Hướng dẫn mượn sách',
      'Liên hệ thư viện',
      'Giờ mở cửa',
      'Cách tìm kiếm',
    ]
  } finally {
    if (!suggestions.value?.length) {
      suggestions.value = [
        'Tài liệu phổ biến',
        'Hướng dẫn mượn sách',
        'Liên hệ thư viện',
        'Giờ mở cửa',
        'Cách tìm kiếm',
      ]
    }
    loadingBoot.value = false
  }
}

function toggle() {
  open.value = !open.value
  if (open.value) {
    if (suggestions.value.length === 0) bootSuggestions()
    scrollToBottom()
  }
}

function openFromEvent() {
  open.value = true
  if (suggestions.value.length === 0) bootSuggestions()
  scrollToBottom()
}

onMounted(() => {
  loadStorage()
  window.addEventListener('tri-thuc-open-chat', openFromEvent)
})

onUnmounted(() => {
  window.removeEventListener('tri-thuc-open-chat', openFromEvent)
})

async function sendText(text) {
  const q = (text ?? input.value).trim()
  if (!q) return
  if (q.length > 500) {
    inputError.value = 'Câu hỏi không quá 500 ký tự.'
    return
  }
  inputError.value = ''
  input.value = ''
  messages.value.push({ role: 'user', text: q })
  typing.value = true
  await new Promise((r) => setTimeout(r, 800))
  try {
    const res = await chatbotService.ask({ question: q })
    const answer =
      typeof res === 'string'
        ? res
        : res?.answer || res?.message || res?.data?.answer || 'Không có phản hồi.'
    messages.value.push({ role: 'bot', text: answer })
  } catch (e) {
    messages.value.push({
      role: 'bot',
      text: e?.message || 'Không kết nối được máy chủ chat.',
    })
  } finally {
    typing.value = false
  }
}
</script>

<template>
  <div class="fixed bottom-4 right-4 z-[60] flex flex-col items-end gap-3">
    <Transition name="fade">
      <div
        v-if="open"
        class="flex w-[360px] max-w-[calc(100vw-2rem)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
        style="height: 500px"
      >
        <div class="flex items-center justify-between bg-gradient-to-r from-primary-blue to-blue-700 px-4 py-3 text-white">
          <div class="flex items-center gap-2">
            <Icon icon="mdi:robot-happy-outline" class="h-6 w-6" />
            <span class="font-semibold">Trợ lý Tri Thức Số</span>
          </div>
          <button type="button" class="rounded-lg p-1 hover:bg-white/10" aria-label="Thu nhỏ" @click="open = false">
            <Icon icon="mdi:minus" class="h-5 w-5" />
          </button>
        </div>
        <div class="flex flex-1 flex-col overflow-hidden bg-slate-50">
          <div ref="messagesEl" class="flex-1 space-y-3 overflow-y-auto px-3 py-3 scrollbar-dialog">
            <div v-if="messages.length === 0 && !loadingBoot" class="flex flex-wrap gap-2">
              <ChatMessage
                class="basis-full"
                role="bot"
                :html="renderChatMessage('Xin chào! Tôi là trợ lý ảo của Tri Thức Số. Tôi có thể giúp gì cho bạn?')"
              />
              <p class="basis-full text-xs text-slate-500">Gợi ý nhanh:</p>
              <button
                v-for="(s, i) in suggestions"
                :key="i"
                type="button"
                class="inline-flex rounded-full border border-primary-blue/15 bg-primary-blue/10 px-3 py-2 text-left text-xs font-medium text-primary-blue transition hover:border-primary-blue/40 hover:bg-primary-blue/15"
                @click="sendText(typeof s === 'string' ? s : s.text || s.question)"
              >
                {{ typeof s === 'string' ? s : s.text || s.question }}
              </button>
            </div>
            <template v-for="(m, idx) in messages" :key="idx">
              <ChatMessage
                :role="m.role"
                :html="m.role === 'user' ? userHtml(m.text) : renderChatMessage(m.text)"
              />
            </template>
            <ChatTyping v-if="typing" />
          </div>
          <form class="border-t border-slate-200 bg-white p-3" @submit.prevent="sendText()">
            <div class="flex gap-2">
              <input
                v-model="input"
                type="text"
                class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-blue focus:ring-1 focus:ring-primary-blue"
                placeholder="Nhập câu hỏi..."
                @input="inputError = ''"
              />
              <button
                type="submit"
                class="rounded-xl bg-primary-blue px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                :disabled="typing"
              >
                Gửi
              </button>
            </div>
            <p v-if="inputError" class="mt-2 text-xs font-medium text-red-600">{{ inputError }}</p>
          </form>
        </div>
      </div>
    </Transition>

    <button
      type="button"
      class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-blue text-white shadow-lg ring-4 ring-white hover:bg-blue-700"
      aria-label="Mở chat"
      @click="toggle"
    >
      <Icon icon="mdi:chat-outline" class="h-7 w-7" />
    </button>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
