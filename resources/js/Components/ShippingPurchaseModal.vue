<template>
  <div 
    v-if="isOpen" 
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    @click="closeModal"
  >
    <div 
      class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
      @click.stop
    >
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 relative">
        <button 
          @click="closeModal"
          class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors p-2"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        
        <div class="flex items-center space-x-4">
          <div class="p-3 bg-white/20 rounded-full">
            <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-2xl" />
          </div>
          <div>
            <h2 class="text-2xl font-black">Versand bezahlen</h2>
            <p class="text-white/90">{{ totalItemCount }} Items zu dir nach Hause</p>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
        
        <!-- Selected Items Summary -->
        <div class="mb-8">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Ausgewählte Items</h3>
          
          <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
              <PrizeGroupCard 
                v-for="(prizeGroup, index) in selectedItems"
                :key="index"
                :prize-group="prizeGroup"
                :pull-zone="bunny?.pull_zone || ''"
                :show-selection="false"
                class="pointer-events-none transform scale-75"
              />
            </div>
          </div>

          <!-- Shipping Details -->
          <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-center space-x-3 mb-4">
              <div class="p-2 bg-blue-500 rounded-full">
                <font-awesome-icon :icon="['fas', 'truck']" class="text-white" />
              </div>
              <div>
                <div class="font-bold text-blue-800">Versand nach Deutschland</div>
                <div class="text-sm text-blue-600">Lieferung in 1-3 Werktagen</div>
              </div>
            </div>
            
            <!-- Price Breakdown -->
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-gray-600">{{ totalItemCount }} Items</span>
                <span class="font-semibold">Kostenlos</span>
              </div>
              
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Versandkosten</span>
                <span class="font-semibold">{{ formatPrice(shippingCost) }}</span>
              </div>
              
              <hr class="border-blue-200">
              
              <div class="flex justify-between items-center text-xl font-black">
                <span>Gesamtpreis</span>
                <span class="text-blue-600">{{ formatPrice(shippingCost) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Stripe Payment Element (stable container) -->
        <div class="mb-6">
          <label class="block text-lg font-bold text-gray-900 mb-3">Zahlungsmethode</label>
          <div id="shipping-payment-element" class="p-4 border-2 border-gray-200 rounded-xl relative min-h-[80px]">
            <div v-show="!clientSecret" class="h-full w-full flex items-center justify-center text-sm text-gray-500 space-x-2">
              <font-awesome-icon :icon="['fas','spinner']" class="animate-spin" />
              <span>Lade Zahlungsdaten…</span>
            </div>
            <div v-show="clientSecret && updatingIntent" class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl pointer-events-none">
              <div class="flex items-center space-x-2 text-sm font-semibold text-gray-600">
                <font-awesome-icon :icon="['fas','spinner']" class="animate-spin" />
                <span>Aktualisiere Preis…</span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="errorMessage" class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm font-semibold border border-red-200">{{ errorMessage }}</div>
        <div v-if="successMessage" class="mb-4 p-3 rounded bg-green-50 text-green-700 text-sm font-semibold border border-green-200">{{ successMessage }}</div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4">
          <button
            @click="closeModal"
            class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all duration-200"
          >
            Abbrechen
          </button>
          
          <button
            @click="purchaseShipping"
            :disabled="loading"
            class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 disabled:from-gray-300 disabled:to-gray-300 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 disabled:transform-none disabled:cursor-not-allowed shadow-lg"
          >
            <div class="flex items-center justify-center space-x-2">
              <font-awesome-icon :icon="['fas', loading ? 'spinner' : 'credit-card']" :class="loading ? 'animate-spin' : ''" />
              <span v-if="!loading">Versand bezahlen</span>
              <span v-else>Verarbeite...</span>
              <span v-if="!loading" class="font-black">{{ formatPrice(shippingCost) }}</span>
            </div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import PrizeGroupCard from '@/Components/PrizeGroupCard.vue'
import { ref, computed, watch, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { loadStripe } from '@stripe/stripe-js'

const props = defineProps({
  selectedItems: { type: Array, required: true },
  selectedTicketIds: { type: Array, required: true },
  addressData: { type: Object, required: true },
  shippingCost: { type: Number, default: 7.00 },
  bunny: { type: Object, default: () => ({}) },
  isOpen: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'success'])

// Reactive data
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const clientSecret = ref(null)
const stripe = ref(null)
const elements = ref(null)
const paymentElement = ref(null)
const updatingIntent = ref(false)
const page = usePage()

// Computed properties
const totalItemCount = computed(() => {
  return props.selectedItems.reduce((total, prizeGroup) => {
    return total + prizeGroup.count
  }, 0)
})

// Methods
const formatPrice = (price) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR'
  }).format(price || 0)
}

const closeModal = () => {
  emit('close')
}

const initStripeElements = async (opId) => {
  if (!props.isOpen) return // modal must be open
  if (!stripe.value) {
    stripe.value = await loadStripe(page.props.stripe.key)
  }
  if (clientSecret.value && stripe.value) {
    // Wait for DOM to reflect clientSecret v-if
    await nextTick()
    const container = document.getElementById('shipping-payment-element')
    if (!container) return // container not rendered (maybe modal closing)
    // Skip if a newer intent op started
    if (opId !== currentIntentOpId) return
    // Unmount previous if exists
    if (paymentElement.value) {
      try { paymentElement.value.unmount() } catch (_) {}
      paymentElement.value = null
    }
    // Clear container to avoid residual nodes Stripe might append twice
    container.innerHTML = ''
    elements.value = stripe.value.elements({ clientSecret: clientSecret.value })
    const paymentEl = elements.value.create('payment')
    paymentEl.mount(container)
    paymentElement.value = paymentEl
  }
}

let currentIntentOpId = 0
const createIntent = async () => {
  if (!props.isOpen) return // don't create when modal closed
  if (loading.value) return // prevent overlap
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  try {
    const opId = ++currentIntentOpId
    const { data } = await axios.post('/shipping/purchase/intent', { 
      selected_ticket_ids: props.selectedTicketIds,
      address_data: props.addressData
    })
    window.__lastShippingOrderId = data.order_id
    // If modal closed or obsolete op, abort quietly
    if (!props.isOpen || opId !== currentIntentOpId) return
    clientSecret.value = data.client_secret
    await initStripeElements(opId)
  } catch (e) {
    errorMessage.value = e.response?.data?.message || 'Fehler beim Anlegen der Zahlung'
  } finally {
    loading.value = false
  }
}

const purchaseShipping = async () => {
  // (Re)create intent if none exists yet
  if (!clientSecret.value) {
    await createIntent()
  }
  if (!stripe.value || !elements.value) return
  loading.value = true
  errorMessage.value = ''
  try {
    const params = new URLSearchParams()
    if (window.__lastShippingOrderId) params.append('order_id', window.__lastShippingOrderId)
    const { error } = await stripe.value.confirmPayment({
      elements: elements.value,
      confirmParams: {
        return_url: `${window.location.origin}/shipping/success?${params.toString()}`,
      }
    })
    if (error) {
      errorMessage.value = error.message || 'Zahlung fehlgeschlagen'
    } else {
      successMessage.value = 'Zahlung erfolgreich! Versand wird erstellt...'
      emit('success')
    }
  } catch (e) {
    errorMessage.value = 'Unbekannter Fehler'
  } finally {
    loading.value = false
  }
}

watch(() => props.isOpen, async (open) => {
  if (!open) {
    clientSecret.value = null
    elements.value = null
    if (paymentElement.value) {
      try { paymentElement.value.unmount() } catch (_) {}
      paymentElement.value = null
    }
    currentIntentOpId++ // invalidate pending ops
    errorMessage.value = ''
    successMessage.value = ''
  } else {
    // reset state and immediately create an intent
    await createIntent()
  }
})
</script>

<style scoped>
/* Backdrop blur support */
@supports (backdrop-filter: blur(8px)) {
  .backdrop-blur-sm {
    backdrop-filter: blur(8px);
  }
}
</style>
