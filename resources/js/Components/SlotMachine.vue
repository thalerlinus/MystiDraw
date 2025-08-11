<template>
  <div class="slot-machine-container">
    <!-- Slot Machine Frame -->
    <div class="slot-frame">
      <!-- Top Indicator/Pointer -->
      <div class="slot-pointer">
        <div class="pointer-triangle"></div>
      </div>
      
      <!-- Scrolling Products Strip -->
      <div class="slot-reel" ref="slotReel">
        <div class="slot-track" :style="{ transform: `translateX(${translateX}px)` }">
          <!-- Repeat products multiple times for seamless scroll -->
          <div 
            v-for="(product, index) in repeatedProducts" 
            :key="`${product.id}-${Math.floor(index / allProducts.length)}`"
            class="slot-item"
            :class="{ 'winning-item': isWinningItem(product, index) }"
          >
            <div class="product-card">
              <img 
                v-if="product.image_url" 
                :src="product.image_url" 
                :alt="product.name"
                class="product-image"
              >
              <div v-else class="product-placeholder">
                <i class="fa fa-gift"></i>
              </div>
              <div class="product-name">{{ product.name }}</div>
              <div class="product-tier" :class="`tier-${product.tier.toLowerCase()}`">
                {{ product.tier }}
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Bottom Indicator Line -->
      <div class="slot-indicator-line"></div>
    </div>

    <!-- Controls -->
    <div class="slot-controls" v-if="!isAnimating && !hasResult">
      <button 
        @click="startSlotAnimation"
        class="slot-spin-btn"
      >
        <i class="fa fa-play"></i>
        Drehen!
      </button>
    </div>

    <!-- Loading during animation -->
    <div v-if="isAnimating" class="slot-loading">
      <div class="animate-spin w-6 h-6 border-2 border-yellow-500 border-t-transparent rounded-full mx-auto mb-2"></div>
      <div class="text-sm text-slate-600">Ermittlung...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, defineProps, defineEmits } from 'vue'

const props = defineProps({
  allProducts: Array,
  winningProduct: Object,
  isVisible: Boolean
})

const emit = defineEmits(['animation-complete'])

// Reactive state
const translateX = ref(0)
const isAnimating = ref(false)
const hasResult = ref(false)
const slotReel = ref(null)

// Constants
const ITEM_WIDTH = 120 // Width of each slot item in pixels
const ANIMATION_DURATION = 5000 // 5 seconds (verlängert von 3 auf 5)
const SLOW_DOWN_DURATION = 2000 // Last 2 seconds for slow down

// Computed
const repeatedProducts = computed(() => {
  if (!props.allProducts.length) return []
  
  // Repeat products 5 times for smooth animation
  const repeated = []
  for (let i = 0; i < 5; i++) {
    repeated.push(...props.allProducts)
  }
  return repeated
})

const isWinningItem = (product, index) => {
  if (!hasResult.value || !props.winningProduct) return false
  
  // Check if this is the final position item
  const finalPosition = Math.abs(translateX.value) / ITEM_WIDTH
  const itemPosition = index
  
  return Math.abs(itemPosition - finalPosition) < 0.5 && 
         product.id === props.winningProduct.id
}

// Methods
const startSlotAnimation = () => {
  if (isAnimating.value || !props.allProducts.length) return
  
  isAnimating.value = true
  
  // Calculate target position (where winning item should stop)
  let targetItemIndex = 0
  if (props.winningProduct) {
    targetItemIndex = props.allProducts.findIndex(p => p.id === props.winningProduct.id)
    if (targetItemIndex === -1) targetItemIndex = 0
    
    // Add offset to land in middle of visible area
    targetItemIndex += props.allProducts.length * 2 // Use middle repetition
  }
  
  const targetPosition = -(targetItemIndex * ITEM_WIDTH) + (slotReel.value?.offsetWidth / 2) - (ITEM_WIDTH / 2)
  
  // Start with fast spinning
  const startTime = Date.now()
  const startPosition = translateX.value
  
  const animate = () => {
    const elapsed = Date.now() - startTime
    const progress = elapsed / ANIMATION_DURATION
    
    if (progress >= 1) {
      // Animation complete
      translateX.value = targetPosition
      isAnimating.value = false
      hasResult.value = true
      emit('animation-complete')
      return
    }
    
    // Easing function: exponential ease-out für smootheres Ende
    let easeOut;
    if (progress < 0.7) {
      // Erste 70% - konstante schnelle Geschwindigkeit
      easeOut = progress / 0.7 * 0.8;
    } else {
      // Letzte 30% - exponentielles Abbremsen
      const slowProgress = (progress - 0.7) / 0.3;
      easeOut = 0.8 + (1 - 0.8) * (1 - Math.pow(1 - slowProgress, 4));
    }
    
    // Add more extra spinning for excitement (mehr Drehungen)
    const extraSpins = -props.allProducts.length * ITEM_WIDTH * 5 // Erhöht von 3 auf 5
    const currentPosition = startPosition + extraSpins + (targetPosition - startPosition - extraSpins) * easeOut
    
    translateX.value = currentPosition
    
    requestAnimationFrame(animate)
  }
  
  requestAnimationFrame(animate)
}

const reset = () => {
  translateX.value = 0
  isAnimating.value = false
  hasResult.value = false
}

// Auto-start animation when component becomes visible
onMounted(() => {
  if (props.isVisible && props.winningProduct) {
    // Small delay for visual effect
    setTimeout(startSlotAnimation, 500)
  }
})

// Expose reset method
defineExpose({ reset })
</script>

<style scoped>
.slot-machine-container {
  width: 100%;
  max-width: 32rem;
  margin-left: auto;
  margin-right: auto;
  padding: 1rem;
}

.slot-frame {
  position: relative;
  background: linear-gradient(to bottom, #1e293b, #0f172a);
  border-radius: 0.75rem;
  padding: 1rem;
  border: 4px solid #eab308;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  background-image: 
    radial-gradient(circle at 20% 20%, rgba(255, 215, 0, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(255, 215, 0, 0.05) 0%, transparent 50%),
    radial-gradient(circle at 50% 50%, rgba(255, 215, 0, 0.02) 0%, transparent 70%);
}

.slot-pointer {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translateX(-50%) translateY(-50%);
  z-index: 10;
}

.pointer-triangle {
  width: 0;
  height: 0;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-bottom: 8px solid #eab308;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
}

.slot-reel {
  position: relative;
  height: 8rem;
  overflow: hidden;
  background-color: #334155;
  border-radius: 0.5rem;
  border: 2px solid #475569;
}

.slot-track {
  display: flex;
  height: 100%;
  transition: none;
}

.slot-item {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  width: 120px;
}

.winning-item {
  box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.75);
  border-radius: 0.5rem;
  animation: winningPulse 1s ease-in-out infinite;
  z-index: 5;
  position: relative;
}

@keyframes winningPulse {
  0%, 100% { 
    transform: scale(1); 
    box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.75);
  }
  50% { 
    transform: scale(1.05); 
    box-shadow: 0 0 0 6px rgba(251, 191, 36, 0.9), 0 0 20px rgba(251, 191, 36, 0.5);
  }
}

.product-card {
  background-color: white;
  border-radius: 0.5rem;
  padding: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  text-align: center;
  border: 1px solid #e2e8f0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.product-image {
  width: 3rem;
  height: 3rem;
  object-fit: cover;
  border-radius: 0.25rem;
  margin: 0 auto 0.25rem auto;
}

.product-placeholder {
  width: 3rem;
  height: 3rem;
  background-color: #e2e8f0;
  border-radius: 0.25rem;
  margin: 0 auto 0.25rem auto;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
}

.product-name {
  font-size: 0.75rem;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-tier {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 0.125rem 0.25rem;
  border-radius: 0.25rem;
  color: white;
}

.tier-a { background-color: #ef4444; }
.tier-b { background-color: #f97316; }
.tier-c { background-color: #eab308; }
.tier-d { background-color: #22c55e; }
.tier-e { background-color: #3b82f6; }

.slot-indicator-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%) translateY(50%);
  width: 2rem;
  height: 0.25rem;
  background-color: #eab308;
  border-radius: 0.25rem;
  z-index: 10;
  box-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
}

.slot-controls {
  margin-top: 1.5rem;
  text-align: center;
}

.slot-spin-btn {
  padding: 0.75rem 2rem;
  background: linear-gradient(to right, #eab308, #ca8a04);
  color: #1e293b;
  font-weight: 700;
  border-radius: 0.75rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  transition: all 0.2s;
  transform: scale(1);
  border: none;
  cursor: pointer;
}

.slot-spin-btn:hover {
  background: linear-gradient(to right, #fbbf24, #eab308);
  transform: scale(1.05);
}

.slot-loading {
  margin-top: 1.5rem;
  text-align: center;
}
</style>
