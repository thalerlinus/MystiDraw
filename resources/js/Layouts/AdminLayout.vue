<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({ title: String });
const navOpen = ref(false);
const page = usePage();
const current = computed(()=> page.url);

const links = [
  { href: '/admin', label: 'Dashboard', icon: ['fas','gauge'] },
  { href: '/admin/raffles', label: 'Raffles', icon: ['fas','ticket'] },
  { href: '/admin/products', label: 'Produkte', icon: ['fas','box-open'] },
  { href: '/admin/categories', label: 'Kategorien', icon: ['fas','folder-tree'] },
  { href: '/admin/orders', label: 'Bestellungen', icon: ['fas','shopping-cart'] },
  { href: '/admin/shipments', label: 'Versand', icon: ['fas','truck'] },
  { href: '/admin/inventory', label: 'Inventar', icon: ['fas','warehouse'] },
  { href: '/admin/recoveries', label: 'Recoveries', icon: ['fas','recycle'] },
  { href: '/admin/invoices', label: 'Rechnungen', icon: ['fas','file-invoice-dollar'] },
  { href: '/admin/credit-notes', label: 'Gutschriften', icon: ['fas','file-invoice'] },
  { href: '/admin/newsletter', label: 'Newsletter', icon: ['fas','envelope-open-text'] },
];

function isActive(path){
  if(path === '/admin') return current.value === '/admin';
  return current.value.startsWith(path);
}

function closeSidebar() {
  navOpen.value = false;
}

function handleEscapeKey(e) {
  if (e.key === 'Escape') {
    closeSidebar();
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey);
});
</script>
<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Main content (Sidebar removed – navigation moved to top bar) -->
    <div>
      <!-- Top navigation bar -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-sm border-b border-gray-200/70 shadow-sm">
        <div class="flex h-16 items-center gap-6 px-4 md:px-8">
          <!-- Branding / Title -->
          <div class="flex items-center gap-3 pr-4 border-r border-gray-200/60">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-inner">
              <font-awesome-icon :icon="['fas','circle-nodes']" class="text-white w-5 h-5" />
            </div>
            <div class="flex flex-col leading-tight">
              <span class="text-sm font-semibold tracking-wide text-indigo-600">Mysti Admin</span>
              <span class="text-xs text-gray-500">{{ title }}</span>
            </div>
          </div>

          <!-- Desktop navigation links -->
          <nav class="hidden md:flex items-center gap-2">
            <template v-for="l in links" :key="l.href">
              <Link 
                :href="l.href"
                :class="['group flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-all', isActive(l.href) ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700']"
              >
                <font-awesome-icon :icon="l.icon" class="w-4 h-4 opacity-80 group-hover:opacity-100" />
                <span>{{ l.label }}</span>
              </Link>
            </template>
          </nav>

          <!-- Spacer -->
          <div class="flex-1"></div>

          <!-- Toolbar slot -->
          <div class="hidden md:flex items-center gap-3">
            <slot name="toolbar" />
          </div>

          <!-- Mobile menu button -->
          <button 
            class="md:hidden flex items-center justify-center w-10 h-10 rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors"
            @click="navOpen = !navOpen"
            aria-label="Navigation umschalten"
          >
            <font-awesome-icon :icon="navOpen ? ['fas','times'] : ['fas','bars']" class="w-5 h-5" />
          </button>
        </div>

        <!-- Mobile dropdown navigation -->
        <transition name="fade-slide">
          <div 
            v-if="navOpen" 
            class="md:hidden border-t border-gray-200/70 bg-white/95 backdrop-blur p-4 space-y-4"
          >
            <nav class="space-y-1">
              <template v-for="l in links" :key="l.href">
                <Link 
                  :href="l.href" 
                  :class="['flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors', isActive(l.href) ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-700']"
                  @click="closeSidebar"
                >
                  <font-awesome-icon :icon="l.icon" class="w-4 h-4" />
                  <span>{{ l.label }}</span>
                </Link>
              </template>
            </nav>
            <div class="pt-2 border-t border-gray-100 flex flex-col gap-2">
              <Link 
                href="/dashboard" 
                class="flex items-center gap-2 px-3 py-2 text-xs rounded-md text-gray-600 hover:bg-indigo-50 hover:text-indigo-700"
                @click="closeSidebar"
              >
                <font-awesome-icon :icon="['fas','arrow-left']" class="w-4 h-4" />
                <span>Zur Website</span>
              </Link>
              <Link 
                href="/logout" 
                method="post" as="button"
                class="flex items-center gap-2 px-3 py-2 text-xs rounded-md text-gray-600 hover:bg-red-50 hover:text-red-600"
                @click="closeSidebar"
              >
                <font-awesome-icon :icon="['fas','right-from-bracket']" class="w-4 h-4" />
                <span>Abmelden</span>
              </Link>
            </div>
          </div>
        </transition>
      </header>
      
      <!-- Main content area -->
      <main class="min-h-screen bg-gray-50 pt-6 md:pt-10">
        <div class="px-4 md:px-8 pb-8">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-slide-enter-active,.fade-slide-leave-active{transition:all .25s ease}
.fade-slide-enter-from,.fade-slide-leave-to{opacity:0;transform:translateY(-6px)}
</style>
