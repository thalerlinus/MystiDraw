<template>
  <MainLayout>
    <template #header>
      <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-navy-900 px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
          <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4">
              Mein Inventar
            </h1>
            <p class="text-lg sm:text-xl text-navy-200 font-medium">
              Deine gewonnenen Preise und Versandstatus
            </p>
          </div>
        </div>
      </div>
    </template>

    <div class="bg-gradient-to-b from-navy-50 via-white to-gold-50 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <!-- Success/Info Message -->
        <div v-if="$page.props.flash.message" class="bg-gold-50/80 backdrop-blur-sm border border-gold-300 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8 shadow-lg">
          <div class="flex items-center">
            <div class="p-2 bg-gold-gradient rounded-lg mr-3 sm:mr-4">
              <font-awesome-icon :icon="['fas', 'info-circle']" class="text-navy-900" />
            </div>
            <p class="text-gold-800 font-semibold text-sm sm:text-base">{{ $page.props.flash.message }}</p>
          </div>
        </div>

        <!-- Inventory Policy Notice -->
        <div class="bg-indigo-50/80 backdrop-blur-sm border border-indigo-200 rounded-xl p-4 sm:p-5 mb-6 sm:mb-8 shadow">
          <div class="flex items-start gap-3">
            <font-awesome-icon :icon="['fas','info-circle']" class="text-indigo-600 mt-0.5" />
            <p class="text-navy-800 text-sm sm:text-base">
              Hinweis: Gezogene Items verbleiben höchstens <strong>2 Monate</strong> in deinem Inventar. Ohne rechtzeitige Versandanforderung verfallen sie und können nicht mehr versendet werden. Mehr Infos in den
              <Link :href="route('agb')" class="font-semibold text-indigo-800 underline underline-offset-2 hover:text-indigo-900">AGB</Link>.
            </p>
          </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8 sm:mb-12">
          <!-- Total Prizes -->
          <div class="bg-white/95 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-4 sm:p-6 border border-gold-100 hover:border-gold-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gold-gradient rounded-xl flex items-center justify-center animate-pulse">
                  <font-awesome-icon :icon="['fas', 'trophy']" class="text-xl sm:text-2xl text-navy-900" />
                </div>
              </div>
              <div class="ml-4 sm:ml-6 min-w-0">
                <p class="text-sm sm:text-base font-bold text-gold-700">Gesamte Gewinne</p>
                <p class="text-2xl sm:text-3xl font-black text-gold-800">{{ stats.total_prizes }}</p>
              </div>
            </div>
          </div>

          <!-- Pending Shipment -->
          <div class="bg-white/95 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-4 sm:p-6 border border-amber-100 hover:border-amber-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl flex items-center justify-center animate-bounce">
                  <font-awesome-icon :icon="['fas', 'clock']" class="text-xl sm:text-2xl text-white" />
                </div>
              </div>
              <div class="ml-4 sm:ml-6 min-w-0">
                <p class="text-sm sm:text-base font-bold text-amber-700">Bereit zum Versand</p>
                <p class="text-2xl sm:text-3xl font-black text-amber-800">{{ stats.pending_shipment }}</p>
              </div>
            </div>
          </div>

          <!-- Shipped Items -->
          <div class="bg-white/95 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 p-4 sm:p-6 border border-navy-100 hover:border-navy-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-navy-gradient rounded-xl flex items-center justify-center">
                  <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-xl sm:text-2xl text-white" />
                </div>
              </div>
              <div class="ml-4 sm:ml-6 min-w-0">
                <p class="text-sm sm:text-base font-bold text-navy-700">Versendet</p>
                <p class="text-2xl sm:text-3xl font-black text-navy-800">{{ stats.shipped_items }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Info Banner -->
        <div class="bg-gradient-to-r from-navy-800 via-navy-700 to-navy-800 rounded-xl sm:rounded-2xl shadow-2xl p-6 sm:p-8 mb-8 sm:mb-12 border border-navy-600">
          <div class="flex flex-col lg:flex-row items-start lg:items-center space-y-4 lg:space-y-0 lg:space-x-6">
            <div class="flex-shrink-0">
              <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gold-gradient rounded-2xl flex items-center justify-center animate-bounce">
                <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-2xl sm:text-3xl text-navy-900" />
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-xl sm:text-2xl font-black text-white mb-3 sm:mb-4">Versand zu dir nach Hause</h3>
              <p class="text-base sm:text-lg text-navy-200 mb-4 sm:mb-6 leading-relaxed">
                Wähle deine gewonnenen Items aus und lasse sie dir bequem nach Hause senden.
              </p>
              
              <!-- Pricing Info -->
              <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-6 sm:mb-8">
                <div class="flex items-center space-x-2 bg-gold-500/20 px-3 sm:px-4 py-2 rounded-lg border border-gold-400/30">
                  <font-awesome-icon :icon="['fas', 'euro-sign']" class="text-gold-300 text-sm" />
                  <span class="font-bold text-gold-200 text-sm sm:text-base">Versandkosten: 7,00 €</span>
                </div>
                <div class="flex items-center space-x-2 bg-emerald-500/20 px-3 sm:px-4 py-2 rounded-lg border border-emerald-400/30">
                  <font-awesome-icon :icon="['fas', 'truck']" class="text-emerald-300 text-sm" />
                  <span class="text-emerald-200 text-sm sm:text-base">1-3 Werktage aus Deutschland</span>
                </div>
              </div>
              
              <!-- Selection Mode Toggle -->
              <div v-if="stats.pending_shipment > 0" class="space-y-4 sm:space-y-6">
                <!-- Instruction when not in shipping mode -->
                <div v-if="!isShippingMode" class="space-y-4">
                  <button 
                    @click="toggleShippingMode"
                    class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 text-navy-900 font-black rounded-xl transition-all duration-300 inline-flex items-center justify-center gap-3 transform hover:scale-105 text-sm sm:text-base"
                  >
                    <font-awesome-icon :icon="['fas', 'hand-pointer']" class="text-lg" />
                    <span>Items für Versand auswählen</span>
                  </button>
                  <div class="flex items-start gap-3 text-navy-200 bg-navy-900/20 px-4 sm:px-6 py-3 sm:py-4 rounded-xl border border-navy-600/50">
                    <font-awesome-icon :icon="['fas', 'info-circle']" class="flex-shrink-0 mt-1 text-gold-400" />
                    <span class="text-sm sm:text-base font-medium">Du hast {{ stats.pending_shipment }} Items, die versendet werden können</span>
                  </div>
                </div>

                <!-- Selection Mode Active -->
                <div v-else class="space-y-4 sm:space-y-6">
                  <div class="flex items-center gap-3 sm:gap-4 p-4 sm:p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
                    <div class="flex items-center gap-3 text-gold-300 min-w-0">
                      <font-awesome-icon :icon="['fas', 'mouse-pointer']" class="animate-pulse flex-shrink-0 text-lg" />
                      <span class="font-bold text-base sm:text-lg">Auswahlmodus aktiv</span>
                    </div>
                    <div class="text-sm sm:text-base text-navy-200 hidden sm:block">
                      Klicke auf die Items unten, um sie auszuwählen
                    </div>
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button 
                      @click="toggleShippingMode"
                      class="w-full sm:w-auto px-4 sm:px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-bold rounded-xl border border-white/30 hover:border-white/40 transition-all duration-300 inline-flex items-center justify-center gap-2 text-sm sm:text-base"
                    >
                      <font-awesome-icon :icon="['fas', 'times']" />
                      <span>Abbrechen</span>
                    </button>
                    
                    <button 
                      v-if="selectedItems.length > 0"
                      @click="proceedToShipping"
                      class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl transition-all duration-300 inline-flex items-center justify-center gap-3 shadow-lg hover:shadow-emerald-500/25 transform hover:scale-105 text-sm sm:text-base"
                    >
                      <font-awesome-icon :icon="['fas', 'arrow-right']" />
                      <span class="sm:hidden">Weiter ({{ selectedItems.length }})</span>
                      <span class="hidden sm:inline">Weiter mit {{ selectedItems.length }} {{ selectedItems.length === 1 ? 'Item' : 'Items' }}</span>
                    </button>
                    
                    <button 
                      v-else
                      disabled
                      class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-white/10 text-white/50 font-bold rounded-xl border border-white/20 inline-flex items-center justify-center gap-3 cursor-not-allowed text-sm sm:text-base"
                    >
                      <font-awesome-icon :icon="['fas', 'exclamation-triangle']" />
                      <span class="sm:hidden">Mindestens 1 Item wählen</span>
                      <span class="hidden sm:inline">Bitte wähle mindestens ein Item aus</span>
                    </button>
                  </div>

                  <!-- Selected Items Summary -->
                  <div v-if="selectedItems.length > 0" class="p-4 sm:p-6 bg-emerald-500/20 border border-emerald-400/30 rounded-xl backdrop-blur-sm">
                    <div class="flex items-center gap-3 mb-2 sm:mb-3">
                      <font-awesome-icon :icon="['fas', 'check-circle']" class="text-emerald-300 flex-shrink-0 text-lg" />
                      <span class="font-black text-emerald-200 text-base sm:text-lg">{{ selectedItems.length }} {{ selectedItems.length === 1 ? 'Item ausgewählt' : 'Items ausgewählt' }}</span>
                    </div>
                    <div class="text-sm sm:text-base text-emerald-200 font-semibold">
                      Versandkosten: <span class="text-emerald-100">7,00 €</span> • Lieferung in <span class="text-emerald-100">1-3 Werktagen</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- No items available -->
              <div v-else class="flex items-start gap-3 text-amber-200 bg-amber-500/20 px-4 sm:px-6 py-3 sm:py-4 rounded-xl border border-amber-400/30">
                <font-awesome-icon :icon="['fas', 'info-circle']" class="flex-shrink-0 mt-1 text-amber-300" />
                <span class="text-sm sm:text-base font-semibold">Du hast derzeit keine Items, die versendet werden können.</span>
              </div>

              <!-- Reserved items info -->
              <div v-if="stats.reserved_count > 0" class="mt-4 sm:mt-6 flex items-start gap-3 text-purple-200 bg-purple-500/20 px-4 sm:px-6 py-3 sm:py-4 rounded-xl border border-purple-400/30">
                <font-awesome-icon :icon="['fas', 'boxes-packing']" class="flex-shrink-0 mt-1 text-purple-300" />
                <span class="text-sm sm:text-base font-semibold">{{ stats.reserved_count }} Item(s) werden gerade für den Versand vorbereitet.</span>
              </div>
            </div>
          </div>
        </div>


        <!-- Inventory Sections -->
        <div class="space-y-8 sm:space-y-12">
          <!-- Assigned Items (Pending Shipment) -->
          <div v-if="inventory.assigned.length > 0">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3">
              <h3 class="text-xl sm:text-2xl font-black text-navy-900 flex items-center gap-3">
                <div class="p-2 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl">
                  <font-awesome-icon :icon="['fas', 'clock']" class="text-white text-lg" />
                </div>
                <span>Bereit für Versand ({{ inventory.assigned.length }})</span>
              </h3>
              <div v-if="isShippingMode" class="flex items-center gap-2 sm:gap-3 text-navy-700 bg-navy-100 px-3 sm:px-4 py-2 sm:py-3 rounded-xl border border-navy-200 animate-pulse">
                <font-awesome-icon :icon="['fas', 'hand-pointer']" class="animate-bounce text-sm flex-shrink-0" />
                <span class="text-sm sm:text-base font-bold">Klicke Items zum Auswählen</span>
              </div>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
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
            <h3 class="text-xl sm:text-2xl font-black text-navy-900 mb-4 sm:mb-6 flex items-center gap-3">
              <div class="p-2 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl">
                <font-awesome-icon :icon="['fas', 'boxes-packing']" class="text-white text-lg" />
              </div>
              <span>Versand in Vorbereitung ({{ inventory.reserved.length }})</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
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
            <h3 class="text-xl sm:text-2xl font-black text-navy-900 mb-4 sm:mb-6 flex items-center gap-3">
              <div class="p-2 bg-navy-gradient rounded-xl">
                <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-white text-lg" />
              </div>
              <span>Versendet ({{ inventory.shipped.length }})</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
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
            <h3 class="text-xl sm:text-2xl font-black text-navy-900 mb-4 sm:mb-6 flex items-center gap-3">
              <div class="p-2 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl">
                <font-awesome-icon :icon="['fas', 'check-circle']" class="text-white text-lg" />
              </div>
              <span>Zugestellt ({{ inventory.delivered.length }})</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
              <PrizeGroupCard 
                v-for="(prizeGroup, index) in inventory.delivered"
                :key="index"
                :prize-group="prizeGroup" 
                :pull-zone="bunny.pull_zone || ''"
              />
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="stats.total_prizes === 0" class="text-center py-16 sm:py-20">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl sm:rounded-3xl shadow-xl p-8 sm:p-12 border border-navy-100 max-w-md mx-auto">
              <div class="text-6xl sm:text-7xl text-navy-300 mb-6 animate-bounce">
                <font-awesome-icon :icon="['fas', 'box-open']" />
              </div>
              <h3 class="text-xl sm:text-2xl font-black text-navy-900 mb-4">Dein Inventar ist leer</h3>
              <p class="text-navy-600 mb-8 text-sm sm:text-base leading-relaxed px-4">Du hast noch keine Preise gewonnen. Nimm an Raffles teil, um deine Gewinnchancen zu erhöhen!</p>
              <a href="/raffles" class="inline-flex items-center px-6 sm:px-8 py-3 sm:py-4 border border-transparent text-sm sm:text-base font-bold rounded-xl text-navy-900 bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 transition-all duration-300 transform hover:scale-105">
                <font-awesome-icon :icon="['fas', 'dice']" class="mr-2 sm:mr-3" />
                <span>Zu den Raffles</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import PrizeGroupCard from '@/Components/PrizeGroupCard.vue'
import { defineProps, ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

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
