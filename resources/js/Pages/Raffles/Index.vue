<script setup>
import { Head } from '@inertiajs/vue3';
import JsonLd from '@/Components/JsonLd.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import RaffleCarousel from '@/Components/RaffleCarousel.vue';
import CategoryNode from '@/Components/CategoryNode.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const route = window.route;

const props = defineProps({
  categoriesTree: { type: Array, default: () => [] },
  raffles: { type: Object, required: true },
  selectedCategory: { type: String, default: null },
  bunny: { type: Object, default: () => ({}) },
});

const expanded = ref(new Set());
// Auto-expand if only one root to directly show its children
if (props.categoriesTree.length === 1) {
  expanded.value.add(props.categoriesTree[0].slug);
}
const toggle = (slug) => { expanded.value.has(slug) ? expanded.value.delete(slug) : expanded.value.add(slug); };
const isExpanded = (slug) => expanded.value.has(slug);

const navigateCategory = (slug) => {
  router.get('/raffles', slug ? { category: slug } : {}, { preserveScroll: true, preserveState: true });
};
const clearFilter = () => navigateCategory(null);

const pages = computed(() => {
  const { current_page, last_page } = props.raffles;
  const arr = [];
  for (let i = 1; i <= last_page; i++) arr.push(i);
  return arr;
});
const gotoPage = (page) => {
  const params = { page };
  if (props.selectedCategory) params.category = props.selectedCategory;
  router.get('/raffles', params, { preserveScroll: true, preserveState: true });
};

const handleRaffleSelect = (raffle) => {
  // Navigation zur Raffle Detail-Seite
  router.get(route('raffles.show', raffle.slug));
};

// SEO helpers
const base = (typeof window !== 'undefined' && window.APP_URL) ? window.APP_URL.replace(/\/$/, '') : '';
const canonical = base + '/raffles';
// Provide a default OG image (adjust path to a real asset if available)
const defaultOgImage = base + '/images/og-default.jpg';
</script>

<template>
  <MainLayout title="Raffles" :user="$page.props.auth?.user">
    <Head>
      <title>Raffles – MystiDraw</title>
      <meta name="description" content="Mystery Box Raffles mit 100% Gewinnchance – entdecke jetzt aktive Raffles in verschiedenen Kategorien und sichere dir Überraschungen!" />
      <link rel="canonical" :href="canonical" />
      <meta property="og:type" content="website" />
      <meta property="og:site_name" content="MystiDraw" />
      <meta property="og:title" content="Raffles – MystiDraw" />
      <meta property="og:description" content="Mystery Box Raffles mit 100% Gewinnchance – entdecke jetzt aktive Raffles in verschiedenen Kategorien und sichere dir Überraschungen!" />
      <meta property="og:url" :content="canonical" />
      <meta property="og:image" :content="defaultOgImage" />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content="Raffles – MystiDraw" />
      <meta name="twitter:description" content="Mystery Box Raffles mit 100% Gewinnchance – entdecke jetzt aktive Raffles in verschiedenen Kategorien und sichere dir Überraschungen!" />
      <meta name="twitter:image" :content="defaultOgImage" />
    </Head>
  <JsonLd v-if="$page.props.jsonLd" :json="$page.props.jsonLd" key="raffles-list" />
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-gradient-to-r from-yellow-400/10 to-transparent"></div>
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-10 left-10 w-20 h-20 bg-yellow-400/10 rounded-full"></div>
        <div class="absolute top-32 right-20 w-16 h-16 bg-yellow-400/5 rounded-full"></div>
        <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-yellow-400/8 rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-12 h-12 bg-yellow-400/6 rounded-full"></div>
      </div>
      
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
        <div class="text-center mb-8">
          <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
            <font-awesome-icon :icon="['fas', 'dice']" class="mr-4 text-yellow-400 drop-shadow-lg" />
            Mystery-Box 
            <span class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 bg-clip-text text-transparent animate-pulse">
              Raffles
            </span>
          </h1>
          <p class="text-xl md:text-2xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
            Entdecke spannende Mystery-Boxen mit <span class="text-yellow-400 font-semibold">100% Gewinngarantie</span>. 
            Filtere nach Kategorien und finde dein nächstes Abenteuer!
          </p>
        </div>
        
        <!-- Enhanced Stats with Icons -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all duration-300 group">
            <div class="flex items-center justify-center mb-3">
              <div class="w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                <font-awesome-icon :icon="['fas', 'trophy']" class="text-2xl text-yellow-400" />
              </div>
            </div>
            <div class="text-3xl font-bold text-yellow-400 mb-1">100%</div>
            <div class="text-slate-300 font-medium">Gewinnchance</div>
            <div class="text-sm text-slate-400 mt-1">Jedes Los gewinnt</div>
          </div>
          
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all duration-300 group">
            <div class="flex items-center justify-center mb-3">
              <div class="w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                <font-awesome-icon :icon="['fas', 'boxes-stacked']" class="text-2xl text-yellow-400" />
              </div>
            </div>
            <div class="text-3xl font-bold text-yellow-400 mb-1">{{ raffles.total }}</div>
            <div class="text-slate-300 font-medium">Aktive Raffles</div>
            <div class="text-sm text-slate-400 mt-1">Wähle dein Lieblings-Raffle</div>
          </div>
          
          <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center border border-white/20 hover:bg-white/15 transition-all duration-300 group">
            <div class="flex items-center justify-center mb-3">
              <div class="w-12 h-12 bg-yellow-400/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                <font-awesome-icon :icon="['fas', 'star']" class="text-2xl text-yellow-400" />
              </div>
            </div>
            <div class="text-3xl font-bold text-yellow-400 mb-1">A-E</div>
            <div class="text-slate-300 font-medium">Gewinn-Kategorien</div>
            <div class="text-sm text-slate-400 mt-1">Von Premium bis Bonus</div>
          </div>
        </div>
        
        <!-- Call to Action -->
        <div class="mt-12 text-center">
          <div class="inline-flex items-center gap-2 bg-yellow-400/20 backdrop-blur-sm border border-yellow-400/30 rounded-full px-6 py-3 text-yellow-300">
            <font-awesome-icon :icon="['fas', 'star']" class="animate-pulse" />
            <span class="font-medium">Beginne dein Mystery-Abenteuer!</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="bg-slate-50 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
          
          <!-- Sidebar -->
          <aside class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
              
                            <!-- Category Filter Header -->
              <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                  <font-awesome-icon :icon="['fas', 'folder-tree']" class="text-slate-700" />
                  Kategorien
                </h2>
                <button 
                  v-if="selectedCategory" 
                  @click="clearFilter" 
                  class="text-sm bg-yellow-400 hover:bg-yellow-500 text-slate-900 px-3 py-1 rounded-full font-medium transition-all duration-200 hover:shadow-md flex items-center gap-1"
                >
                  <font-awesome-icon :icon="['fas', 'times']" class="w-3 h-3" />
                  Reset
                </button>
              </div>
              
              <!-- Category Tree -->
              <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
                <!-- All Raffles Button -->
                <div class="p-4 border-b border-slate-100">
                  <button 
                    @click="navigateCategory(null)" 
                    :class="[
                      'w-full text-left px-4 py-3 rounded-xl font-semibold transition-all duration-200',
                      !selectedCategory 
                        ? 'bg-gradient-to-r from-slate-800 to-slate-900 text-white shadow-md' 
                        : 'text-slate-700 hover:bg-slate-50 hover:shadow-sm'
                    ]"
                  >
                    <span class="flex items-center gap-2">
                      <font-awesome-icon :icon="['fas', 'bullseye']" class="text-yellow-400" />
                      Alle Raffles
                      <span class="text-sm opacity-75">({{ raffles.total }})</span>
                    </span>
                  </button>
                </div>
                
                <!-- Categories -->
                <div class="p-4" v-if="categoriesTree && categoriesTree.length">
                  <CategoryNode
                    v-for="node in categoriesTree"
                    :key="node.slug || node.id"
                    :node="node"
                    :selected="selectedCategory"
                    :depth="0"
                    :expanded="isExpanded(node.slug)"
                    @toggle="toggle"
                    @select="navigateCategory"
                    :is-expanded="isExpanded"
                  />
                </div>
                
                <!-- No Categories -->
                <div v-else class="p-6 text-center text-slate-500">
                  <div class="text-4xl mb-2">
                    <font-awesome-icon :icon="['fas', 'folder-open']" class="text-slate-400" />
                  </div>
                  <div class="text-sm">Keine Kategorien verfügbar</div>
                </div>
              </div>
              
              <!-- Filter Info -->
              <div v-if="selectedCategory" class="bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-2xl p-4 text-slate-900">
                <div class="font-semibold mb-1 flex items-center gap-2">
                  <font-awesome-icon :icon="['fas', 'filter']" />
                  Aktiver Filter
                </div>
                <div class="text-sm opacity-90">
                  Zeige Raffles aus: <span class="font-medium">{{ selectedCategory }}</span>
                </div>
              </div>
            </div>
          </aside>

          <!-- Raffles Grid -->
          <div class="lg:col-span-3 space-y-8">
            
            <!-- Current Filter Display -->
            <div v-if="selectedCategory" class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-xl font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <font-awesome-icon :icon="['fas', 'dice']" class="text-yellow-500" />
                    Raffles: {{ selectedCategory }}
                  </h3>
                  <p class="text-slate-600">
                    {{ raffles.total }} {{ raffles.total === 1 ? 'Raffle' : 'Raffles' }} gefunden
                  </p>
                </div>
                <button 
                  @click="clearFilter"
                  class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl transition-colors font-medium"
                >
                  Alle anzeigen
                </button>
              </div>
            </div>
            
            <!-- Raffles -->
            <div v-if="raffles.data.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              <RaffleCarousel 
                v-for="raffle in raffles.data" 
                :key="raffle.id" 
                :raffle="raffle" 
                :pull-zone="bunny.pull_zone" 
                @selectRaffle="handleRaffleSelect"
                class="transform hover:scale-105 transition-transform duration-300"
              />
            </div>
            
            <!-- No Raffles Found -->
            <div v-else class="bg-white rounded-2xl p-12 text-center shadow-lg border border-slate-200">
              <div class="text-6xl mb-4">
                <font-awesome-icon :icon="['fas', 'theater-masks']" class="text-slate-400" />
              </div>
              <h3 class="text-2xl font-bold text-slate-900 mb-4">Keine Raffles gefunden</h3>
              <p class="text-slate-600 mb-6 max-w-md mx-auto">
                {{ selectedCategory 
                  ? `Keine Raffles in der Kategorie "${selectedCategory}" verfügbar.` 
                  : 'Momentan sind keine Raffles verfügbar.'
                }}
              </p>
              <div class="space-y-3">
                <button 
                  v-if="selectedCategory"
                  @click="clearFilter" 
                  class="bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-slate-900 px-6 py-3 rounded-xl font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105 flex items-center gap-2 mx-auto"
                >
                  <font-awesome-icon :icon="['fas', 'bullseye']" />
                  Alle Raffles anzeigen
                </button>
                <div class="text-sm text-slate-500">
                  Oder wähle eine andere Kategorie aus der Seitenleiste
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div v-if="raffles.last_page > 1" class="bg-white rounded-2xl p-6 shadow-lg border border-slate-200">
              <div class="flex flex-wrap items-center justify-center gap-2">
                <!-- Previous Button -->
                <button 
                  :disabled="raffles.current_page === 1" 
                  @click="gotoPage(raffles.current_page - 1)"
                  class="px-4 py-2 text-sm font-medium rounded-xl border transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed bg-white hover:bg-slate-50 text-slate-700 border-slate-300 flex items-center gap-2"
                >
                  <font-awesome-icon :icon="['fas', 'chevron-left']" />
                  Zurück
                </button>
                
                <!-- Page Numbers -->
                <button 
                  v-for="p in pages" 
                  :key="p" 
                  @click="gotoPage(p)" 
                  :class="[
                    'w-10 h-10 flex items-center justify-center rounded-xl text-sm font-medium transition-all duration-200',
                    p === raffles.current_page 
                      ? 'bg-gradient-to-r from-slate-800 to-slate-900 text-white shadow-md' 
                      : 'bg-white border border-slate-300 hover:bg-slate-50 text-slate-700'
                  ]"
                >
                  {{ p }}
                </button>
                
                <!-- Next Button -->
                <button 
                  :disabled="raffles.current_page === raffles.last_page" 
                  @click="gotoPage(raffles.current_page + 1)"
                  class="px-4 py-2 text-sm font-medium rounded-xl border transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed bg-white hover:bg-slate-50 text-slate-700 border-slate-300 flex items-center gap-2"
                >
                  Weiter
                  <font-awesome-icon :icon="['fas', 'chevron-right']" />
                </button>
              </div>
              
              <!-- Page Info -->
              <div class="text-center text-sm text-slate-500 mt-4">
                Seite {{ raffles.current_page }} von {{ raffles.last_page }} 
                ({{ raffles.total }} {{ raffles.total === 1 ? 'Raffle' : 'Raffles' }} gesamt)
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>



<style scoped>
.fade-enter-active, .fade-leave-active { transition: all 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-2px); }
</style>
