<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Mein Inventar
      </h2>
    </template>

    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
      <!-- Success/Info Message -->
      <div v-if="$page.props.flash.message" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
          <font-awesome-icon :icon="['fas', 'info-circle']" class="text-blue-500 mr-3" />
          <p class="text-blue-700">{{ $page.props.flash.message }}</p>
        </div>
      </div>

  <!-- Shipping Info Banner -->
      <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex items-start space-x-3 sm:space-x-4">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-lg sm:text-xl text-blue-600" />
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="text-base sm:text-lg font-semibold text-blue-900 mb-2">Versand zu dir nach Hause</h3>
            <p class="text-sm sm:text-base text-blue-700 mb-3 sm:mb-4">
              Wähle deine gewonnenen Items aus und lasse sie dir bequem nach Hause senden.
              <strong class="font-semibold block sm:inline">Versandkosten: 7,00 €</strong> 
              <span class="text-xs sm:text-sm block sm:inline">(1-3 Werktage Lieferzeit aus Deutschland)</span>
            </p>
            
            <!-- Selection Mode Toggle -->
            <div v-if="stats.pending_shipment > 0" class="space-y-3 sm:space-y-4">
              <!-- Instruction when not in shipping mode -->
              <div v-if="!isShippingMode" class="space-y-3">
                <button 
                  @click="toggleShippingMode"
                  class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg border border-blue-600 transition-all duration-200 inline-flex items-center justify-center gap-2 sm:gap-3 shadow-sm hover:shadow-md transform hover:scale-105 text-sm sm:text-base"
                >
                  <font-awesome-icon :icon="['fas', 'hand-pointer']" class="text-base sm:text-lg" />
                  <span>Items für Versand auswählen</span>
                </button>
                <div class="text-xs sm:text-sm text-blue-600 flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-3 bg-blue-100 rounded-lg">
                  <font-awesome-icon :icon="['fas', 'info-circle']" class="flex-shrink-0" />
                  <span>Du hast {{ stats.pending_shipment }} Items, die versendet werden können</span>
                </div>
              </div>

              <!-- Selection Mode Active -->
              <div v-else class="space-y-3 sm:space-y-4">
                <div class="flex items-center gap-2 sm:gap-4 p-3 sm:p-4 bg-white rounded-lg border border-blue-200 shadow-sm">
                  <div class="flex items-center gap-2 text-blue-700 min-w-0">
                    <font-awesome-icon :icon="['fas', 'mouse-pointer']" class="animate-pulse flex-shrink-0" />
                    <span class="font-medium text-sm sm:text-base">Auswahlmodus aktiv</span>
                  </div>
                  <div class="text-xs sm:text-sm text-gray-600 hidden sm:block">
                    Klicke auf die Items unten, um sie auszuwählen
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                  <button 
                    @click="toggleShippingMode"
                    class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg border border-gray-500 transition-all duration-200 inline-flex items-center justify-center gap-2 text-sm sm:text-base"
                  >
                    <font-awesome-icon :icon="['fas', 'times']" />
                    <span>Abbrechen</span>
                  </button>
                  
                  <button 
                    v-if="selectedItems.length > 0"
                    @click="proceedToShipping"
                    class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg border border-green-600 transition-all duration-200 inline-flex items-center justify-center gap-2 sm:gap-3 shadow-sm hover:shadow-md transform hover:scale-105 text-sm sm:text-base"
                  >
                    <font-awesome-icon :icon="['fas', 'arrow-right']" />
                    <span class="sm:hidden">Weiter ({{ selectedItems.length }})</span>
                    <span class="hidden sm:inline">Weiter mit {{ selectedItems.length }} {{ selectedItems.length === 1 ? 'Item' : 'Items' }}</span>
                  </button>
                  
                  <button 
                    v-else
                    disabled
                    class="w-full sm:w-auto px-4 sm:px-6 py-2 sm:py-3 bg-gray-300 text-gray-500 font-medium rounded-lg border border-gray-300 inline-flex items-center justify-center gap-2 sm:gap-3 cursor-not-allowed text-sm sm:text-base"
                  >
                    <font-awesome-icon :icon="['fas', 'exclamation-triangle']" />
                    <span class="sm:hidden">Mindestens 1 Item wählen</span>
                    <span class="hidden sm:inline">Bitte wähle mindestens ein Item aus</span>
                  </button>
                </div>

                <!-- Selected Items Summary -->
                <div v-if="selectedItems.length > 0" class="p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
                  <div class="flex items-center gap-2 mb-1 sm:mb-2">
                    <font-awesome-icon :icon="['fas', 'check-circle']" class="text-green-600 flex-shrink-0" />
                    <span class="font-medium text-green-800 text-sm sm:text-base">{{ selectedItems.length }} {{ selectedItems.length === 1 ? 'Item ausgewählt' : 'Items ausgewählt' }}</span>
                  </div>
                  <div class="text-xs sm:text-sm text-green-700">
                    Versandkosten: <strong>7,00 €</strong> • Lieferung in 1-3 Werktagen
                  </div>
                </div>
              </div>
            </div>
            
            <div v-else class="flex items-start gap-2 text-amber-700 bg-amber-50 px-3 sm:px-4 py-2 sm:py-3 rounded-lg border border-amber-200">
              <font-awesome-icon :icon="['fas', 'info-circle']" class="flex-shrink-0 mt-0.5" />
              <span class="text-xs sm:text-sm">Du hast derzeit keine Items, die versendet werden können.</span>
            </div>

            <div v-if="stats.reserved_count > 0" class="mt-4 flex items-start gap-2 text-purple-700 bg-purple-50 px-3 sm:px-4 py-2 sm:py-3 rounded-lg border border-purple-200">
              <font-awesome-icon :icon="['fas', 'boxes-packing']" class="flex-shrink-0 mt-0.5" />
              <span class="text-xs sm:text-sm">{{ stats.reserved_count }} Item(s) werden gerade für den Versand vorbereitet.</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <font-awesome-icon :icon="['fas', 'trophy']" class="text-lg sm:text-xl text-yellow-600" />
              </div>
            </div>
            <div class="ml-3 sm:ml-4 min-w-0">
              <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Gesamte Gewinne</p>
              <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.total_prizes }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <font-awesome-icon :icon="['fas', 'clock']" class="text-lg sm:text-xl text-orange-600" />
              </div>
            </div>
            <div class="ml-3 sm:ml-4 min-w-0">
              <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Bereit zum Versand</p>
              <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.pending_shipment }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-lg sm:text-xl text-blue-600" />
              </div>
            </div>
            <div class="ml-3 sm:ml-4 min-w-0">
              <p class="text-xs sm:text-sm font-medium text-gray-500 truncate">Versendet</p>
              <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ stats.shipped_items }}</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Inventory Sections -->
      <div class="space-y-6 sm:space-y-8">
        <!-- Assigned Items (Pending Shipment) -->
        <div v-if="inventory.assigned.length > 0">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3 sm:mb-4 gap-2">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
              <font-awesome-icon :icon="['fas', 'clock']" class="text-orange-500" />
              <span>Bereit für Versand ({{ inventory.assigned.length }})</span>
            </h3>
            <div v-if="isShippingMode" class="flex items-center gap-2 text-blue-600 bg-blue-50 px-2 sm:px-3 py-1 sm:py-2 rounded-lg">
              <font-awesome-icon :icon="['fas', 'hand-pointer']" class="animate-bounce text-sm" />
              <span class="text-xs sm:text-sm font-medium">Klicke Items zum Auswählen</span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-3">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in inventory.assigned"
              :key="index"
              :prize-group="prizeGroup" 
              :pull-zone="bunny.pull_zone || ''"
              :is-shipping-mode="isShippingMode"
              :is-selected="isItemSelected(prizeGroup)"
              @toggle-selection="toggleItemSelection(prizeGroup)"
            />
          </div>
        </div>

        <!-- Reserved (Versand in Vorbereitung) -->
        <div v-if="inventory.reserved && inventory.reserved.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'boxes-packing']" class="text-purple-500" />
            <span>Versand in Vorbereitung ({{ inventory.reserved.length }})</span>
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-3">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in inventory.reserved"
              :key="index"
              :prize-group="prizeGroup" 
              :pull-zone="bunny.pull_zone || ''"
            />
          </div>
        </div>

        <!-- Shipped Items -->
        <div v-if="inventory.shipped.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-blue-500" />
            <span>Versendet ({{ inventory.shipped.length }})</span>
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-3">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in inventory.shipped"
              :key="index"
              :prize-group="prizeGroup" 
              :pull-zone="bunny.pull_zone || ''"
            />
          </div>
        </div>

        <!-- Delivered Items -->
        <div v-if="inventory.delivered.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'check-circle']" class="text-green-500" />
            <span>Zugestellt ({{ inventory.delivered.length }})</span>
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-3">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in inventory.delivered"
              :key="index"
              :prize-group="prizeGroup" 
              :pull-zone="bunny.pull_zone || ''"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="stats.total_prizes === 0" class="text-center py-8 sm:py-12">
          <div class="text-5xl sm:text-6xl text-gray-400 mb-3 sm:mb-4">
            <font-awesome-icon :icon="['fas', 'box-open']" />
          </div>
          <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">Dein Inventar ist leer</h3>
          <p class="text-gray-500 mb-4 sm:mb-6 text-sm sm:text-base px-4">Du hast noch keine Preise gewonnen. Nimm an Raffles teil, um deine Gewinnchancen zu erhöhen!</p>
          <a href="/" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-sm sm:text-base font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
            <font-awesome-icon :icon="['fas', 'dice']" class="mr-2" />
            Zu den Raffles
          </a>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import PrizeGroupCard from '@/Components/PrizeGroupCard.vue'
import { defineProps, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  inventory: Object,
  stats: Object,
  bunny: { type: Object, default: () => ({}) }
})

// Shipping mode state
const isShippingMode = ref(false)
const selectedItems = ref([])

const formatPrice = (amount) => {
  return new Intl.NumberFormat('de-DE', { 
    style: 'currency', 
    currency: 'EUR' 
  }).format(amount || 0)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('de-DE', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Shipping functions
const toggleShippingMode = () => {
  isShippingMode.value = !isShippingMode.value
  if (!isShippingMode.value) {
    selectedItems.value = []
  }
}

const isItemSelected = (prizeGroup) => {
  return selectedItems.value.some(item => 
    item.product.id === prizeGroup.product.id && 
    item.tier === prizeGroup.tier &&
    item.status === prizeGroup.status
  )
}

const toggleItemSelection = (prizeGroup) => {
  if (!isShippingMode.value) return
  
  const itemIndex = selectedItems.value.findIndex(item => 
    item.product.id === prizeGroup.product.id && 
    item.tier === prizeGroup.tier &&
    item.status === prizeGroup.status
  )
  
  if (itemIndex > -1) {
    selectedItems.value.splice(itemIndex, 1)
  } else {
    selectedItems.value.push(prizeGroup)
  }
}

const proceedToShipping = () => {
  if (selectedItems.value.length === 0) return
  
  // Store selected items in session or pass as data
  const itemIds = selectedItems.value.flatMap(prizeGroup => 
    prizeGroup.tickets.map(ticket => ticket.ticket_id)
  )
  
  router.post('/shipping/create', {
    selected_ticket_ids: itemIds
  })
}
</script>

<style scoped>
/* Tier badges */
.tier-a { background-color: #ef4444; }
.tier-b { background-color: #f97316; }
.tier-c { background-color: #eab308; }
.tier-d { background-color: #22c55e; }
.tier-e { background-color: #3b82f6; }

/* Text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
