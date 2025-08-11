<template>
    <div v-if="prizeGroups && prizeGroups.length > 0" class="relative">
        <div class="overflow-hidden rounded-xl shadow-lg">
            <div 
                class="flex transition-transform duration-500 ease-in-out"
                :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
            >
                <div 
                    v-for="(prizeGroup, index) in prizeGroups" 
                    :key="index"
                    class="min-w-full relative"
                >
                    <!-- Prize Image -->
                    <div class="h-48 sm:h-64 md:h-80 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center relative">
                        <!-- Main Product Image -->
                        <div v-if="prizeGroup.product && prizeGroup.product.images && prizeGroup.product.images.length > 0" 
                             class="w-full h-full flex items-center justify-center">
                            <img 
                                :src="getImageUrl(prizeGroup.product.images[currentImageIndex[index] || 0].file_path, pullZone)" 
                                :alt="prizeGroup.product.name" 
                                class="max-w-full max-h-full object-contain"
                            />
                        </div>
                        <div v-else-if="prizeGroup.product && prizeGroup.product.image_url" 
                             class="w-full h-full flex items-center justify-center">
                            <img 
                                :src="prizeGroup.product.image_url" 
                                :alt="prizeGroup.product.name" 
                                class="max-w-full max-h-full object-contain"
                            />
                        </div>
                        <div v-else class="text-gray-500 text-center">
                            <svg class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-lg sm:text-xl">Kein Bild verfügbar</p>
                        </div>

                        <!-- Tier Badge -->
                        <div class="absolute top-2 sm:top-4 right-2 sm:right-4">
                            <span 
                                class="px-2 py-1 sm:px-4 sm:py-2 rounded-full font-bold text-white text-sm sm:text-lg shadow-lg"
                                :class="getTierColor(prizeGroup.tier)"
                            >
                                Tier {{ prizeGroup.tier }}
                            </span>
                        </div>

                        <!-- Count Badge -->
                        <div v-if="prizeGroup.count > 1" class="absolute top-2 sm:top-4 left-2 sm:left-4 z-10">
                            <span class="px-2 py-1 sm:px-4 sm:py-2 bg-red-500 text-white text-sm sm:text-lg font-bold rounded-full shadow-lg border-2 border-white">
                                {{ prizeGroup.count }}x
                            </span>
                        </div>

                        <!-- Last One Badge -->
                        <div v-if="prizeGroup.is_last_one" class="absolute bottom-2 sm:bottom-4 left-1/2 transform -translate-x-1/2 z-10">
                            <span class="px-2 py-1 sm:px-4 sm:py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm sm:text-lg font-bold rounded-full animate-pulse shadow-lg border-2 border-white">
                                <font-awesome-icon :icon="['fas', 'star']" class="mr-1 sm:mr-2" />
                                <span class="hidden sm:inline">Last One!</span>
                                <span class="sm:hidden">Last!</span>
                            </span>
                        </div>

                        <!-- Image Navigation (if multiple images) -->
                        <div v-if="prizeGroup.product && prizeGroup.product.images && prizeGroup.product.images.length > 1" 
                             class="absolute bottom-2 right-2 flex space-x-1">
                            <button
                                v-for="(image, imgIndex) in prizeGroup.product.images"
                                :key="imgIndex"
                                @click="setCurrentImageIndex(index, imgIndex)"
                                class="w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-all duration-300"
                                :class="imgIndex === (currentImageIndex[index] || 0) 
                                    ? 'bg-yellow-500 scale-125' 
                                    : 'bg-white/60 hover:bg-white/80'"
                            ></button>
                        </div>
                    </div>

                    <!-- Prize Info -->
                    <div class="bg-white p-4 sm:p-6 text-center">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2">
                            {{ prizeGroup.product?.name || 'Unbekanntes Produkt' }}
                        </h3>
                        <p v-if="prizeGroup.product?.description" class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4 line-clamp-2">
                            {{ prizeGroup.product.description }}
                        </p>
                        
                        <!-- Ticket Numbers -->
                        <div class="flex items-center justify-center text-xs sm:text-sm text-slate-600 mb-3">
                            <span v-if="prizeGroup.count === 1" class="flex items-center gap-1">
                                <font-awesome-icon :icon="['fas', 'ticket-alt']" />
                                <span>Ticket #{{ prizeGroup.tickets[0].serial }}</span>
                            </span>
                            <span v-else class="flex items-center gap-1" :title="prizeGroup.tickets.map(t => `#${t.serial}`).join(', ')">
                                <font-awesome-icon :icon="['fas', 'ticket-alt']" />
                                <span>{{ prizeGroup.count }} Tickets: #{{ prizeGroup.tickets.map(t => t.serial).join(', #') }}</span>
                            </span>
                        </div>
                        
                        <!-- Last One Bonus Info -->
                        <div v-if="prizeGroup.is_last_one" class="mt-3 sm:mt-4 p-2 sm:p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center justify-center text-red-700 text-sm sm:text-base">
                                <font-awesome-icon :icon="['fas', 'gift']" class="mr-2 text-red-500" />
                                <span class="font-bold">Last One Bonus!</span>
                            </div>
                            <p class="text-xs sm:text-sm text-red-600 mt-1">Extra Belohnung beim letzten verfügbaren Los</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carousel Navigation -->
        <button
            v-if="prizeGroups.length > 1"
            @click="previousPrize"
            class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 sm:p-3 rounded-full shadow-lg transition-all duration-300 z-10"
        >
            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <button
            v-if="prizeGroups.length > 1"
            @click="nextPrize"
            class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 sm:p-3 rounded-full shadow-lg transition-all duration-300 z-10"
        >
            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Dots Indicator -->
        <div 
            v-if="prizeGroups.length > 1"
            class="flex justify-center space-x-1 sm:space-x-2 mt-4 sm:mt-6"
        >
            <button
                v-for="(prizeGroup, index) in prizeGroups"
                :key="index"
                @click="currentIndex = index"
                class="w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-all duration-300"
                :class="index === currentIndex 
                    ? (prizeGroup.is_last_one ? 'bg-red-500 scale-125 animate-pulse' : 'bg-yellow-500 scale-125')
                    : (prizeGroup.is_last_one ? 'bg-red-300 hover:bg-red-400' : 'bg-gray-300 hover:bg-gray-400')"
            ></button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { getImageUrl } from '@/utils/cdn';

const props = defineProps({
    prizeGroups: { type: Array, required: true },
    pullZone: { type: String, required: true },
    autoRotate: { type: Boolean, default: true },
    rotateInterval: { type: Number, default: 5000 }
});

// Reactive data
const currentIndex = ref(0);
const currentImageIndex = ref({});

// Auto-rotate carousel
let prizeInterval;

onMounted(() => {
    if (props.autoRotate && props.prizeGroups && props.prizeGroups.length > 1) {
        prizeInterval = setInterval(() => {
            currentIndex.value = (currentIndex.value + 1) % props.prizeGroups.length;
        }, props.rotateInterval);
    }
});

onUnmounted(() => {
    if (prizeInterval) {
        clearInterval(prizeInterval);
    }
});

// Methods
const nextPrize = () => {
    currentIndex.value = (currentIndex.value + 1) % props.prizeGroups.length;
};

const previousPrize = () => {
    currentIndex.value = currentIndex.value === 0 
        ? props.prizeGroups.length - 1 
        : currentIndex.value - 1;
};

const setCurrentImageIndex = (prizeIndex, imageIndex) => {
    currentImageIndex.value = {
        ...currentImageIndex.value,
        [prizeIndex]: imageIndex
    };
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
