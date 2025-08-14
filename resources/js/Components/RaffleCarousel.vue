<template>
  <div class="relative bg-white rounded-xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 border border-slate-200">
    <!-- Header mit Kategorie -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-xl font-bold">{{ raffle.name }}</h3>
          <p class="text-slate-300 text-sm">{{ raffle.category?.name || 'Allgemein' }}</p>
        </div>
        <div class="text-right">
          <div class="text-2xl font-bold text-yellow-400">{{ formatPrice(raffle.base_ticket_price) }}</div>
          <div class="text-slate-300 text-sm">pro Los</div>
        </div>
      </div>
    </div>

    <!-- Bild Carousel -->
    <div class="relative h-64 overflow-hidden">
      <!-- Sold Out Overlay -->
      <div 
        v-if="isSoldOut"
        class="absolute inset-0 bg-black/60 flex items-center justify-center z-20 backdrop-blur-sm"
      >
        <div class="text-center text-white">
          <div class="text-4xl font-bold mb-2 animate-pulse">AUSVERKAUFT</div>
          <div class="text-lg opacity-90">Alle Lose sind vergriffen</div>
        </div>
      </div>
      
      <div 
        v-if="raffle.items && raffle.items.length > 0"
        class="flex transition-transform duration-500 ease-in-out h-full"
        :style="{ transform: `translateX(-${currentImageIndex * 100}%)` }"
        :class="{ 'filter grayscale': isSoldOut }"
      >
        <div 
          v-for="item in raffle.items" 
          :key="item.id"
          class="min-w-full h-full relative bg-gray-100"
        >
          <img 
            v-if="item.product && item.product.images && item.product.images[0]"
            :src="resolveImage(item.product.images[0])" 
            :alt="item.product.name"
            class="w-full h-full object-contain bg-white"
            loading="lazy"
          />
          <div 
            v-else
            class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center"
          >
            <div class="text-gray-500 text-center">
              <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-sm">Kein Bild verfügbar</p>
            </div>
          </div>
          
          <!-- Overlay mit Produktinfo -->
          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 text-white">
            <div class="font-semibold">{{ item.product?.name || 'Unbekanntes Produkt' }}</div>
            <div class="text-sm opacity-90">Tier {{ item.tier }}</div>
          </div>
        </div>
      </div>

      <!-- Fallback wenn keine Items -->
      <div v-else class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center" :class="{ 'filter grayscale': isSoldOut }">
        <!-- Sold Out Overlay für Fallback -->
        <div 
          v-if="isSoldOut"
          class="absolute inset-0 bg-black/60 flex items-center justify-center z-20 backdrop-blur-sm"
        >
          <div class="text-center text-white">
            <div class="text-4xl font-bold mb-2 animate-pulse">AUSVERKAUFT</div>
            <div class="text-lg opacity-90">Alle Lose sind vergriffen</div>
          </div>
        </div>
        
        <div class="text-slate-600 text-center">
          <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
          </svg>
          <p class="text-lg font-semibold">{{ raffle.name }}</p>
          <p class="text-sm">Lade bald neue Überraschungen auf!</p>
        </div>
      </div>

      <!-- Navigation Dots -->
      <div 
        v-if="raffle.items && raffle.items.length > 1"
        class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2"
      >
        <button
          v-for="(item, index) in raffle.items"
          :key="index"
          @click="currentImageIndex = index"
          class="w-3 h-3 rounded-full transition-all duration-300"
          :class="index === currentImageIndex 
            ? 'bg-white scale-125' 
            : 'bg-white/50 hover:bg-white/75'"
        ></button>
      </div>

      <!-- Navigation Arrows -->
      <button
        v-if="raffle.items && raffle.items.length > 1"
        @click="previousImage"
        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>
      
      <button
        v-if="raffle.items && raffle.items.length > 1"
        @click="nextImage"
        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full transition-all duration-300 opacity-0 group-hover:opacity-100"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>

    <!-- Footer mit Status und Action -->
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
          <div 
            class="w-3 h-3 rounded-full"
            :class="getStatusClass()"
          ></div>
          <span class="text-sm font-medium" :class="getStatusTextClass()">
            {{ getStatusText() }}
          </span>
        </div>
        <div class="flex items-center gap-1 text-slate-600">
          <font-awesome-icon :icon="['fas', 'ticket']" class="text-xs" />
          <span class="text-sm font-medium">{{ getTicketDisplayText() }}</span>
        </div>
      </div>

      <!-- Ende Datum -->
      <div v-if="raffle.ends_at && !isSoldOut" class="text-sm text-gray-600 mb-4">
        <span class="font-medium">Läuft bis:</span> {{ formatDate(raffle.ends_at) }}
      </div>

      <!-- Sold Out Banner -->
      <div v-if="isSoldOut" class="text-center mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
        <div class="text-red-800 font-bold text-lg">AUSVERKAUFT</div>
        <div class="text-red-600 text-sm">Alle Lose wurden bereits verkauft</div>
      </div>

      <!-- Action Button -->
      <button
        @click="$emit('selectRaffle', raffle)"
        :disabled="!canPurchase"
        :class="getButtonClass()"
      >
        <span v-if="isSoldOut" class="flex items-center justify-center gap-2">
          <font-awesome-icon :icon="['fas', 'ban']" />
          Ausverkauft
        </span>
        <span v-else-if="normalizedStatus === 'active'" class="flex items-center justify-center gap-2">
          <font-awesome-icon :icon="['fas', 'ticket']" />
          Lose kaufen
        </span>
        <span v-else>Nicht verfügbar</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { getThumbUrl, getImageUrl } from '@/utils/cdn';

const props = defineProps({
  raffle: { type: Object, required: true },
  pullZone: { type: String, default: null }
});

defineEmits(['selectRaffle']);

const currentImageIndex = ref(0);
let imageInterval;

// Auto-rotate images
onMounted(() => {
  if (props.raffle.items && props.raffle.items.length > 1) {
    imageInterval = setInterval(() => {
      nextImage();
    }, 4000);
  }
});

onUnmounted(() => {
  if (imageInterval) {
    clearInterval(imageInterval);
  }
});

const nextImage = () => {
  if (props.raffle.items && props.raffle.items.length > 1) {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.raffle.items.length;
  }
};

const previousImage = () => {
  if (props.raffle.items && props.raffle.items.length > 1) {
    currentImageIndex.value = currentImageIndex.value === 0 
      ? props.raffle.items.length - 1 
      : currentImageIndex.value - 1;
  }
};

const formatPrice = (price) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: props.raffle.currency || 'EUR'
  }).format(price);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('de-DE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const normalizedStatus = computed(() => {
  if (props.raffle.status === 'live') return 'active';
  if (props.raffle.status === 'scheduled') return 'scheduled';
  return props.raffle.status;
});

// Computed für Sold Out Status
const isSoldOut = computed(() => {
  return props.raffle.is_sold_out || props.raffle.tickets_available === 0;
});

// Computed für Kauf-Berechtigung
const canPurchase = computed(() => {
  return normalizedStatus.value === 'active' && !isSoldOut.value;
});

const getStatusText = () => {
  if (isSoldOut.value) return 'Ausverkauft';
  
  const statusMap = {
    'active': 'Aktiv',
    'live': 'Aktiv',
    'draft': 'Entwurf',
    'ended': 'Beendet',
    'paused': 'Pausiert',
    'scheduled': 'Geplant'
  };
  return statusMap[normalizedStatus.value] || normalizedStatus.value;
};

const getStatusClass = () => {
  if (isSoldOut.value) return 'bg-red-500';
  if (normalizedStatus.value === 'active') return 'bg-green-500';
  return 'bg-gray-400';
};

const getStatusTextClass = () => {
  if (isSoldOut.value) return 'text-red-700 font-bold';
  if (normalizedStatus.value === 'active') return 'text-green-700';
  return 'text-gray-600';
};

const getButtonClass = () => {
  const baseClass = 'w-full py-3 px-4 font-bold rounded-lg transition-all duration-300 shadow-lg';
  
  if (isSoldOut.value) {
    return `${baseClass} bg-gradient-to-r from-red-400 to-red-500 text-white cursor-not-allowed opacity-75`;
  } else if (canPurchase.value) {
    return `${baseClass} bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-slate-900 transform hover:scale-[1.02] hover:shadow-xl`;
  } else {
    return `${baseClass} bg-gray-400 text-gray-700 cursor-not-allowed opacity-50`;
  }
};

const getTicketDisplayText = () => {
  if (typeof props.raffle.tickets_available === 'number') {
    if (props.raffle.tickets_available === 0) {
      return '0 verfügbar';
    }
    return `${props.raffle.tickets_available} verfügbar`;
  }
  
  // Fallback für alte Datenstruktur
  return `${getRemainingTickets()} verfügbar`;
};

const getRemainingTickets = () => {
  // Berechne verfügbare Lose basierend auf den Items (Fallback)
  const totalTickets = props.raffle.items?.reduce((sum, item) => sum + (item.quantity || 0), 0) || 0;
  const soldTickets = props.raffle.tickets?.length || 0;
  return Math.max(0, totalTickets - soldTickets);
};

function resolveImage(img){
  if(!img) return null;
  const base = img.image_path || img.path;
  if(!base) return null;
  const thumb = getThumbUrl(base, props.pullZone);
  return thumb || getImageUrl(base, props.pullZone);
}
</script>
