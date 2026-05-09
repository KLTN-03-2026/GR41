<script setup>
import { RouterView } from 'vue-router'
import AppHeader from '@/components/common/AppHeader.vue'
import AppFooter from '@/components/common/AppFooter.vue'
import ScrollToTop from '@/components/common/ScrollToTop.vue'
import ChatbotWidget from '@/components/chatbot/ChatbotWidget.vue'
</script>

<template>
  <div class="flex min-h-screen flex-col">
    <AppHeader />
    <main class="flex-1">
      <RouterView />
    </main>
    <AppFooter />
    <ScrollToTop />
    <ChatbotWidget />
  </div>
</template>
