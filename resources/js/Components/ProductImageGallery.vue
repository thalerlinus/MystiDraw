<template>
  <!-- Product Image Display -->
  <div class="relative cursor-pointer w-full h-full flex items-center justify-center" @click="openModal">
    <img 
      v-if="product.images && product.images[0]"
      :src="getThumbUrl(product.images[0].path, pullZone)" 
      :alt="product.name"
      class="max-w-full max-h-full object-contain hover:scale-105 transition-transform duration-300"
    />
    <div v-else class="text-gray-500 text-center">
      <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
      </svg>
      <p class="text-sm">Kein Bild verfügbar</p>
    </div>
    
    <!-- Multiple Images Indicator -->
    <div 
      v-if="product.images && product.images.length > 1" 
      class="absolute bottom-2 right-2 bg-black/70 text-white px-2 py-1 rounded-full text-sm font-medium"
    >
      <font-awesome-icon :icon="['fas', 'images']" class="mr-1" />
      {{ product.images.length }}
    </div>
  </div>

  <!-- Modal -->
  <teleport to="body">
    <div 
      v-if="isModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
      @click="closeModal"
    >
      <div class="relative max-w-6xl max-h-screen mx-4" @click.stop>
        <!-- Close Button -->
        <button 
          @click="closeModal"
          class="absolute -top-12 right-0 text-white hover:text-yellow-400 transition-colors z-10"
        >
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <!-- Image Gallery -->
        <div class="bg-white rounded-lg overflow-hidden shadow-2xl">
          <!-- Main Image Display -->
          <div class="relative h-96 md:h-[500px] bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
            <img 
              v-if="product.images && product.images[currentImageIndex]"
              :src="getImageUrl(product.images[currentImageIndex].path, pullZone)"
              :alt="`${product.name} - Bild ${currentImageIndex + 1}`"
              class="max-w-full max-h-full object-contain"
            />
            
            <!-- Navigation Arrows -->
            <button
              v-if="product.images && product.images.length > 1"
              @click="previousImage"
              class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-3 rounded-full transition-all duration-300"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            
            <button
              v-if="product.images && product.images.length > 1"
              @click="nextImage"
              class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-3 rounded-full transition-all duration-300"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>

            <!-- Image Counter -->
            <div 
              v-if="product.images && product.images.length > 1"
              class="absolute top-4 right-4 bg-black/70 text-white px-3 py-1 rounded-full text-sm font-medium"
            >
              {{ currentImageIndex + 1 }} / {{ product.images.length }}
            </div>
          </div>

          <!-- Thumbnail Strip -->
          <div 
            v-if="product.images && product.images.length > 1"
            class="p-4 bg-gray-50 border-t"
          >
            <div class="flex space-x-2 overflow-x-auto">
              <button
                v-for="(image, index) in product.images"
                :key="index"
                @click="currentImageIndex = index"
                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all duration-300"
                :class="index === currentImageIndex 
                  ? 'border-yellow-500 shadow-lg scale-105' 
                  : 'border-gray-300 hover:border-gray-400'"
              >
                <img 
                  :src="getThumbUrl(image.path, pullZone)"
                  :alt="`${product.name} - Thumbnail ${index + 1}`"
                  class="w-full h-full object-cover"
                />
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="p-6 border-t bg-white">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">
              {{ product.name }}
            </h3>
            <p v-if="product.description" class="text-gray-600">
              {{ product.description }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { ref } from 'vue';
import { getThumbUrl, getImageUrl } from '@/utils/cdn';

const props = defineProps({
  product: { type: Object, required: true },
  pullZone: { type: String, default: null }
});

const isModalOpen = ref(false);
const currentImageIndex = ref(0);

const openModal = () => {
  if (props.product.images && props.product.images.length > 0) {
    isModalOpen.value = true;
    currentImageIndex.value = 0;
    // Disable body scroll
    document.body.style.overflow = 'hidden';
  }
};

const closeModal = () => {
  isModalOpen.value = false;
  // Enable body scroll
  document.body.style.overflow = '';
};

const nextImage = () => {
  if (props.product.images && props.product.images.length > 1) {
    currentImageIndex.value = (currentImageIndex.value + 1) % props.product.images.length;
  }
};

const previousImage = () => {
  if (props.product.images && props.product.images.length > 1) {
    currentImageIndex.value = currentImageIndex.value === 0 
      ? props.product.images.length - 1 
      : currentImageIndex.value - 1;
  }
};

// Keyboard navigation
const handleKeydown = (event) => {
  if (!isModalOpen.value) return;
  
  if (event.key === 'Escape') {
    closeModal();
  } else if (event.key === 'ArrowLeft') {
    previousImage();
  } else if (event.key === 'ArrowRight') {
    nextImage();
  }
};

// Add keyboard event listeners when mounted
import { onMounted, onUnmounted } from 'vue';

onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
  // Ensure body scroll is restored
  document.body.style.overflow = '';
});
</script>

<style scoped>
/* Custom scrollbar for thumbnail strip */
.overflow-x-auto::-webkit-scrollbar {
  height: 4px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 2px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
