<template>
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
        <div class="relative">
            <!-- Image Carousel für diese Preisgruppe -->
            <div class="h-32 sm:h-40 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center relative overflow-hidden cursor-pointer" @click="openModal">
                <div 
                    v-if="prizeGroup.product && prizeGroup.product.images && prizeGroup.product.images.length > 0"
                    class="flex transition-transform duration-300 ease-in-out w-full h-full"
                    :style="{ transform: `translateX(-${currentImageIndex * 100}%)` }"
                >
                    <div 
                        v-for="(image, index) in prizeGroup.product.images" 
                        :key="index"
                        class="min-w-full h-full flex items-center justify-center"
                    >
                        <img 
                            :src="getImageUrl(image.path, pullZone)" 
                            :alt="image.alt_text || prizeGroup.product.name" 
                            class="max-w-full max-h-full object-contain"
                        />
                    </div>
                </div>
                <div v-else-if="prizeGroup.product && prizeGroup.product.image_url" 
                     class="w-full h-full flex items-center justify-center">
                    <img 
                        :src="prizeGroup.product.image_url" 
                        :alt="prizeGroup.product.name" 
                        class="max-w-full max-h-full object-contain"
                    />
                </div>
                <div v-else class="text-gray-400 text-center">
                    <svg class="w-8 h-8 sm:w-12 sm:h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-xs">Kein Bild</p>
                </div>

                <!-- Navigation Arrows (nur wenn mehrere Bilder) -->
                <button
                    v-if="hasMultipleImages"
                    @click.stop="previousImage"
                    class="absolute left-1 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-1 rounded-full transition-colors"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <button
                    v-if="hasMultipleImages"
                    @click.stop="nextImage"
                    class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-1 rounded-full transition-colors"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Dots Indicator (nur wenn mehrere Bilder) -->
                <div 
                    v-if="hasMultipleImages"
                    class="absolute bottom-1 left-1/2 transform -translate-x-1/2 flex space-x-1"
                >
                    <button
                        v-for="(image, index) in prizeGroup.product.images"
                        :key="index"
                        @click.stop="setCurrentImageIndex(index)"
                        class="w-1.5 h-1.5 rounded-full transition-all duration-200"
                        :class="index === currentImageIndex 
                            ? 'bg-white scale-110' 
                            : 'bg-white/60 hover:bg-white/80'"
                    ></button>
                </div>

                <!-- Tier Badge -->
                <div class="absolute top-1 right-1">
                    <span 
                        class="px-1.5 py-0.5 rounded-full text-white text-xs font-bold"
                        :class="getTierColor(prizeGroup.tier)"
                    >
                        {{ prizeGroup.tier }}
                    </span>
                </div>

                <!-- Count Badge -->
                <div v-if="prizeGroup.count > 1" class="absolute top-1 left-1 z-10">
                    <span class="px-1.5 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full border border-white">
                        {{ prizeGroup.count }}x
                    </span>
                </div>

                <!-- Last One Badge -->
                <div v-if="prizeGroup.is_last_one" class="absolute bottom-1 right-1 z-10">
                    <span class="px-1.5 py-0.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full animate-pulse border border-white">
                        <font-awesome-icon :icon="['fas', 'star']" class="mr-1" />
                        Last!
                    </span>
                </div>
            </div>

            <!-- Product Info -->
            <div class="p-3">
                <h4 class="font-medium text-sm text-gray-900 mb-1 line-clamp-2">
                    {{ prizeGroup.product?.name || 'Unbekanntes Produkt' }}
                </h4>
                
                <!-- Ticket Numbers -->
                <div class="flex items-center justify-center text-xs text-slate-600 mb-2">
                    <span v-if="prizeGroup.count === 1" class="flex items-center gap-1">
                        <font-awesome-icon :icon="['fas', 'ticket-alt']" />
                        <span>#{{ prizeGroup.tickets[0].serial }}</span>
                    </span>
                    <span v-else class="flex items-center gap-1" :title="prizeGroup.tickets.map(t => `#${t.serial}`).join(', ')">
                        <font-awesome-icon :icon="['fas', 'ticket-alt']" />
                        <span>{{ prizeGroup.count }}x Tickets</span>
                    </span>
                </div>
                
                <!-- Last One Bonus Info -->
                <div v-if="prizeGroup.is_last_one" class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-center">
                    <div class="flex items-center justify-center text-red-700 text-xs">
                        <font-awesome-icon :icon="['fas', 'gift']" class="mr-1 text-red-500" />
                        <span class="font-medium">Last One Bonus!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <teleport to="body" v-if="isModalOpen">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" @click="closeModal">
            <!-- Close Button -->
            <button @click="closeModal" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Modal Content -->
            <div class="relative max-w-6xl max-h-[90vh] mx-4" @click.stop>
                <!-- Image Display -->
                <div class="relative bg-white rounded-lg overflow-hidden shadow-2xl">
                    <div class="relative h-96 md:h-[500px] bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                        <img 
                            v-if="prizeGroup.product && prizeGroup.product.images && prizeGroup.product.images[modalImageIndex]"
                            :src="getImageUrl(prizeGroup.product.images[modalImageIndex].path, pullZone)"
                            :alt="`${prizeGroup.product.name} - Bild ${modalImageIndex + 1}`"
                            class="max-w-full max-h-full object-contain"
                        />
                        
                        <!-- Navigation Arrows for Modal -->
                        <button
                            v-if="hasMultipleImages"
                            @click="previousModalImage"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-3 rounded-full transition-all duration-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <button
                            v-if="hasMultipleImages"
                            @click="nextModalImage"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-3 rounded-full transition-all duration-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Product Info in Modal -->
                    <div class="p-6 bg-white">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            {{ prizeGroup.product?.name || 'Unbekanntes Produkt' }}
                        </h3>
                        <p v-if="prizeGroup.product?.description" class="text-gray-600 mb-4">
                            {{ prizeGroup.product.description }}
                        </p>
                        
                        <!-- Badges in Modal -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span 
                                class="px-3 py-1 rounded-full text-white text-sm font-bold"
                                :class="getTierColor(prizeGroup.tier)"
                            >
                                Tier {{ prizeGroup.tier }}
                            </span>
                            <span v-if="prizeGroup.count > 1" class="px-3 py-1 bg-red-500 text-white text-sm font-bold rounded-full">
                                {{ prizeGroup.count }}x gewonnen
                            </span>
                            <span v-if="prizeGroup.is_last_one" class="px-3 py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-bold rounded-full">
                                <font-awesome-icon :icon="['fas', 'star']" class="mr-1" />
                                Last One Bonus!
                            </span>
                        </div>

                        <!-- Image Navigation Thumbnails -->
                        <div v-if="hasMultipleImages" class="flex space-x-2 overflow-x-auto">
                            <button
                                v-for="(image, index) in prizeGroup.product.images"
                                :key="index"
                                @click="modalImageIndex = index"
                                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden transition-all duration-200 border-2"
                                :class="index === modalImageIndex 
                                    ? 'border-blue-500 ring-2 ring-blue-200' 
                                    : 'border-gray-300 hover:border-gray-400'"
                            >
                                <img 
                                    :src="getImageUrl(image.path, pullZone)"
                                    :alt="`Thumbnail ${index + 1}`"
                                    class="w-full h-full object-cover"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script setup>
import { ref, computed } from 'vue';
import { getImageUrl } from '@/utils/cdn';

const props = defineProps({
    prizeGroup: { type: Object, required: true },
    pullZone: { type: String, required: true }
});

// Reactive data
const currentImageIndex = ref(0);
const isModalOpen = ref(false);
const modalImageIndex = ref(0);

// Computed properties
const hasMultipleImages = computed(() => {
    return props.prizeGroup.product?.images?.length > 1;
});

// Methods
const nextImage = () => {
    if (props.prizeGroup.product?.images?.length > 1) {
        currentImageIndex.value = (currentImageIndex.value + 1) % props.prizeGroup.product.images.length;
    }
};

const previousImage = () => {
    if (props.prizeGroup.product?.images?.length > 1) {
        currentImageIndex.value = currentImageIndex.value === 0 
            ? props.prizeGroup.product.images.length - 1 
            : currentImageIndex.value - 1;
    }
};

const setCurrentImageIndex = (index) => {
    currentImageIndex.value = index;
};

const openModal = () => {
    if (props.prizeGroup.product && (props.prizeGroup.product.images?.length > 0 || props.prizeGroup.product.image_url)) {
        modalImageIndex.value = currentImageIndex.value;
        isModalOpen.value = true;
        // Disable body scroll
        document.body.style.overflow = 'hidden';
    }
};

const closeModal = () => {
    isModalOpen.value = false;
    // Enable body scroll
    document.body.style.overflow = '';
};

const nextModalImage = () => {
    if (props.prizeGroup.product?.images?.length > 1) {
        modalImageIndex.value = (modalImageIndex.value + 1) % props.prizeGroup.product.images.length;
    }
};

const previousModalImage = () => {
    if (props.prizeGroup.product?.images?.length > 1) {
        modalImageIndex.value = modalImageIndex.value === 0 
            ? props.prizeGroup.product.images.length - 1 
            : modalImageIndex.value - 1;
    }
};

const getTierColor = (tier) => {
    const colors = {
        'A': 'bg-gradient-to-r from-red-500 to-red-600',
        'B': 'bg-gradient-to-r from-orange-500 to-orange-600', 
        'C': 'bg-gradient-to-r from-yellow-500 to-yellow-600',
        'D': 'bg-gradient-to-r from-green-500 to-green-600',
        'E': 'bg-gradient-to-r from-blue-500 to-blue-600'
    };
    return colors[tier] || 'bg-gradient-to-r from-gray-500 to-gray-600';
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
