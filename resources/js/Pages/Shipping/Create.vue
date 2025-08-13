<template>
  <MainLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Versand erstellen
        </h2>
        <a href="/inventory" class="text-blue-600 hover:text-blue-800 inline-flex items-center gap-2 transition-colors">
          <font-awesome-icon :icon="['fas', 'arrow-left']" />
          <span>Zurück zum Inventar</span>
        </a>
      </div>
    </template>

    <div class="max-w-4xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
      
      <!-- Selected Items Summary -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
        <!-- Header with Collapse Toggle -->
        <div class="flex items-center justify-between mb-3 sm:mb-4">
          <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center gap-2">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon :icon="['fas', 'box']" class="text-blue-600 text-sm sm:text-base" />
            </div>
            <span>Ausgewählte Items ({{ totalItemCount }})</span>
          </h3>
          
          <button 
            @click="showItemDetails = !showItemDetails"
            class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 transition-colors"
          >
            <span>{{ showItemDetails ? 'Zuklappen' : 'Details anzeigen' }}</span>
            <font-awesome-icon 
              :icon="['fas', 'chevron-down']" 
              class="transition-transform duration-200"
              :class="{ 'rotate-180': showItemDetails }"
            />
          </button>
        </div>

        <!-- Collapsible Items Grid -->
        <div 
          v-show="showItemDetails" 
          class="mb-4 sm:mb-6 transition-all duration-300 ease-in-out"
        >
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in selectedItems"
              :key="index"
              :prize-group="prizeGroup"
              :pull-zone="bunny?.pull_zone || ''"
              :show-selection="false"
              class="pointer-events-none"
            />
          </div>
        </div>

        <!-- Summary Info -->
        <div class="p-3 sm:p-4 bg-blue-50 rounded-lg">
          <div class="text-center">
            <p class="text-xs sm:text-sm text-blue-600 font-medium">Versandkosten</p>
            <p class="text-lg sm:text-xl font-bold text-blue-900">{{ formatPrice(shippingCost) }}</p>
            <p class="text-xs text-blue-600">Lieferung in 1-3 Werktagen</p>
          </div>
        </div>
      </div>

      <!-- Shipping Form -->
      <form @submit.prevent="submitShipping" class="space-y-6 sm:space-y-8">
        
        <!-- Address Selection -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
          <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-green-600 text-sm sm:text-base" />
            </div>
            <span>Versandadresse</span>
          </h3>

          <!-- Existing Addresses -->
          <div v-if="addresses.length > 0" class="mb-4 sm:mb-6">
            <h4 class="text-sm sm:text-base font-medium text-gray-700 mb-2 sm:mb-3">Gespeicherte Adressen</h4>
            <div class="space-y-2 sm:space-y-3">
              <label v-for="address in addresses" 
                     :key="address.id"
                     class="flex items-start space-x-2 sm:space-x-3 p-3 sm:p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                     :class="selectedAddressId === address.id ? 'border-blue-500 bg-blue-50' : ''">
                <input type="radio" 
                       :value="address.id" 
                       v-model="selectedAddressId"
                       name="address_selection"
                       class="mt-1 text-blue-600 flex-shrink-0">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-medium text-sm sm:text-base">{{ address.first_name }} {{ address.last_name }}</span>
                    <span v-if="address.is_default" class="px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs bg-green-100 text-green-800 rounded-full">Standard</span>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-600">
                    {{ address.street }} {{ address.house_number }}<br>
                    {{ address.postal_code }} {{ address.city }}, {{ address.country_code }}
                  </p>
                </div>
              </label>
            </div>
            
            <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200">
              <label class="flex items-center space-x-2 sm:space-x-3 cursor-pointer">
                <input type="radio" 
                       value="" 
                       v-model="selectedAddressId"
                       name="address_selection"
                       class="text-blue-600">
                <span class="font-medium text-gray-700 text-sm sm:text-base">Neue Adresse verwenden</span>
              </label>
            </div>
          </div>

          <!-- New Address Form -->
          <div v-show="!selectedAddressId || addresses.length === 0" class="space-y-3 sm:space-y-4">
            <h4 v-if="addresses.length > 0" class="text-sm sm:text-base font-medium text-gray-700 mb-2 sm:mb-3">Neue Adresse</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Vorname *</label>
                <input type="text" 
                       v-model="form.first_name"
                       required
                       :class="errors.first_name ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                       class="w-full rounded-md shadow-sm text-sm sm:text-base py-2 sm:py-2.5">
                <p v-if="errors.first_name" class="mt-1 text-xs sm:text-sm text-red-600">{{ errors.first_name }}</p>
              </div>
              
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nachname *</label>
                <input type="text" 
                       v-model="form.last_name"
                       required
                       :class="errors.last_name ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                       class="w-full rounded-md shadow-sm text-sm sm:text-base py-2 sm:py-2.5">
                <p v-if="errors.last_name" class="mt-1 text-xs sm:text-sm text-red-600">{{ errors.last_name }}</p>
              </div>
            </div>

            <div>
              <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Firma (optional)</label>
              <input type="text" 
                     v-model="form.company"
                     class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base py-2 sm:py-2.5">
            </div>

            <div class="grid grid-cols-3 gap-2 sm:gap-4">
              <div class="col-span-2">
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Straße *</label>
                <input type="text" 
                       v-model="form.street"
                       required
                       :class="errors.street ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                       class="w-full rounded-md shadow-sm text-sm sm:text-base py-2 sm:py-2.5">
                <p v-if="errors.street" class="mt-1 text-xs sm:text-sm text-red-600">{{ errors.street }}</p>
              </div>
              
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Hausnr.</label>
                <input type="text" 
                       v-model="form.house_number"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base py-2 sm:py-2.5">
              </div>
            </div>

            <div>
              <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Adresszusatz (optional)</label>
              <input type="text" 
                     v-model="form.address2"
                     placeholder="z.B. c/o, Apartment, etc."
                     class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base py-2 sm:py-2.5">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">PLZ *</label>
                <input type="text" 
                       v-model="form.postal_code"
                       required
                       :class="errors.postal_code ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                       class="w-full rounded-md shadow-sm text-sm sm:text-base py-2 sm:py-2.5">
                <p v-if="errors.postal_code" class="mt-1 text-xs sm:text-sm text-red-600">{{ errors.postal_code }}</p>
              </div>
              
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Stadt *</label>
                <input type="text" 
                       v-model="form.city"
                       required
                       :class="errors.city ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-blue-500 focus:border-blue-500'"
                       class="w-full rounded-md shadow-sm text-sm sm:text-base py-2 sm:py-2.5">
                <p v-if="errors.city" class="mt-1 text-xs sm:text-sm text-red-600">{{ errors.city }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Land *</label>
                <input type="text"
                       v-model="form.country"
                       required
                       placeholder="z.B. Deutschland"
                       class="w-full rounded-md shadow-sm text-sm sm:text-base py-2 sm:py-2.5 border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
              </div>
              
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Telefon (optional)</label>
                <input type="tel" 
                       v-model="form.phone"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm sm:text-base py-2 sm:py-2.5">
              </div>
            </div>

            <div class="flex items-start gap-2">
              <input type="checkbox" 
                     id="save_address" 
                     v-model="form.save_address"
                     class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
              <label for="save_address" class="text-xs sm:text-sm text-gray-700">
                Adresse für zukünftige Bestellungen speichern
              </label>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="text-center sm:text-left">
              <p class="text-xs sm:text-sm text-gray-600 mb-1">Gesamtkosten (nur Versand)</p>
              <p class="text-xl sm:text-2xl font-bold text-green-600">{{ formatPrice(shippingCost) }}</p>
              <p class="text-xs text-gray-500">Lieferung in 1-3 Werktagen</p>
            </div>
            <button type="button" 
                    @click="openPaymentModal"
                    :disabled="!isValidAddress"
                    class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-md transition-colors inline-flex items-center justify-center gap-2 text-sm sm:text-base">
              <font-awesome-icon :icon="['fas', 'credit-card']" />
              <span>{{ isValidAddress ? 'Weiter zur Zahlung' : 'Adresse ausfüllen' }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Shipping Purchase Modal -->
    <ShippingPurchaseModal
      :is-open="showPaymentModal"
      :selected-items="selectedItems"
      :selected-ticket-ids="getTotalTicketIds()"
      :address-data="getAddressData()"
      :shipping-cost="shippingCost"
      :bunny="bunny"
      @close="showPaymentModal = false"
      @success="handlePaymentSuccess"
    />
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import PrizeGroupCard from '@/Components/PrizeGroupCard.vue'
import ShippingPurchaseModal from '@/Components/ShippingPurchaseModal.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  selectedItems: Array,
  addresses: Array,
  shippingCost: Number,
  errors: { type: Object, default: () => ({}) },
  bunny: { type: Object, default: () => ({}) }
})

const processing = ref(false)
const showItemDetails = ref(false) // Start collapsed
const showPaymentModal = ref(false)
const selectedAddressId = ref(props.addresses.find(a => a.is_default)?.id || '')

const form = ref({
  first_name: '',
  last_name: '',
  company: '',
  street: '',
  house_number: '',
  address2: '',
  postal_code: '',
  city: '',
  country: 'Deutschland',
  phone: '',
  save_address: false
})

// Calculate total tickets for submission
const getTotalTicketIds = () => {
  const ticketIds = []
  props.selectedItems.forEach(prizeGroup => {
    prizeGroup.tickets.forEach(ticket => {
      ticketIds.push(ticket.ticket_id)
    })
  })
  return ticketIds
}

// Calculate total item count for display
const totalItemCount = computed(() => {
  return props.selectedItems.reduce((total, prizeGroup) => {
    return total + prizeGroup.count
  }, 0)
})

const formatPrice = (amount) => {
  return new Intl.NumberFormat('de-DE', { 
    style: 'currency', 
    currency: 'EUR' 
  }).format(amount || 0)
}

// Check if address is valid
const isValidAddress = computed(() => {
  if (selectedAddressId.value) {
    return true // Existing address selected
  }
  
  // Check if new address form is filled
  return form.value.first_name.trim() && 
         form.value.last_name.trim() && 
         form.value.street.trim() && 
         form.value.postal_code.trim() && 
         form.value.city.trim() && 
         form.value.country.trim()
})

// Get address data for payment processing
const getAddressData = () => {
  if (selectedAddressId.value) {
    return { address_id: selectedAddressId.value }
  }
  
  // Map long country name to code for backend fallback clarity
  const mapping = { 'Deutschland':'DE','Germany':'DE','Österreich':'AT','Austria':'AT','Schweiz':'CH','Switzerland':'CH' }
  const code = mapping[form.value.country] || (form.value.country.length === 2 ? form.value.country.toUpperCase() : null)
  return {
    ...form.value,
    country_code: code,
    isNewAddress: true
  }
}

const openPaymentModal = () => {
  if (!isValidAddress.value) return
  showPaymentModal.value = true
}

const handlePaymentSuccess = () => {
  showPaymentModal.value = false
  // The success will be handled by Stripe redirect
}
</script>
