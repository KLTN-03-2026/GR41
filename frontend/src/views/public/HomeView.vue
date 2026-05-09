<script setup>
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import HeroSection from '@/components/home/HeroSection.vue'
import FeaturedCarousel from '@/components/home/FeaturedCarousel.vue'
import StatsBanner from '@/components/home/StatsBanner.vue'
import PopularGrid from '@/components/home/PopularGrid.vue'
import RecentList from '@/components/home/RecentList.vue'
import CategoryGrid from '@/components/home/CategoryGrid.vue'
import CtaSection from '@/components/home/CtaSection.vue'
import ScrollReveal from '@/components/home/ScrollReveal.vue'
import DocumentList from '@/components/document/DocumentList.vue'
import { documentService } from '@/services/documentService'
import { categoryService } from '@/services/categoryService'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
import { unwrapList } from '@/utils/apiHelpers'

const { isLoggedIn } = storeToRefs(useAuthStore())

const { data: featured, isLoading: loadFeatured } = useQuery({
  queryKey: ['documents', 'featured'],
  queryFn: documentService.featured,
})

const { data: popular, isLoading: loadPopular } = useQuery({
  queryKey: ['documents', 'popular'],
  queryFn: documentService.popular,
})

const { data: recent, isLoading: loadRecent } = useQuery({
  queryKey: ['documents', 'recent'],
  queryFn: documentService.recent,
})

const { data: recommended, isLoading: loadRec } = useQuery({
  queryKey: ['documents', 'recommended'],
  queryFn: async () => {
    try {
      return await documentService.recommended()
    } catch {
      return []
    }
  },
  enabled: computed(() => isLoggedIn.value),
})

const { data: categoriesRaw } = useQuery({
  queryKey: ['categories', 'tree'],
  queryFn: categoryService.tree,
})

const categories = computed(() => unwrapList(categoriesRaw.value))
</script>

<template>
  <div class="bg-surface">
    <HeroSection />

    <ScrollReveal>
      <FeaturedCarousel class="relative z-10" :items="featured" :loading="loadFeatured" />
    </ScrollReveal>

    <ScrollReveal :delay="40">
      <StatsBanner />
    </ScrollReveal>
    <ScrollReveal :delay="60">
      <PopularGrid :items="popular" :loading="loadPopular" />
    </ScrollReveal>
    <ScrollReveal :delay="80">
      <RecentList :items="recent" :loading="loadRecent" />
    </ScrollReveal>
    <ScrollReveal v-if="isLoggedIn" :delay="100">
      <DocumentList title="Có thể bạn quan tâm" :items="recommended" :loading="loadRec" />
    </ScrollReveal>
    <ScrollReveal :delay="isLoggedIn ? 120 : 100">
      <CategoryGrid :categories="categories" />
    </ScrollReveal>
    <ScrollReveal :delay="140">
      <CtaSection />
    </ScrollReveal>
  </div>
</template>
