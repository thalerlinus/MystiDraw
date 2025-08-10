<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
      <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="close"></div>
      
      <div class="relative inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button @click="close" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fa fa-times w-6 h-6"></i>
          </button>
        </div>

        <div class="mb-6">
          <h3 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
            <i class="fa fa-gift text-yellow-500"></i>
            Tickets öffnen
          </h3>
          <p class="text-sm text-slate-600 mt-2">{{ tickets.length }} Ticket(s) bereit zum Öffnen</p>
        </div>

        <!-- Warning Banner for Already Opened Tickets -->
        <div v-if="warningMessage" class="mb-6 p-4 bg-orange-100 border border-orange-400 rounded-lg">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="fa fa-exclamation-triangle text-orange-500"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-orange-800">
                Achtung
              </h3>
              <div class="mt-1 text-sm text-orange-700">
                {{ warningMessage }}
              </div>
            </div>
            <div class="ml-auto pl-3">
              <button 
                @click="warningMessage = null"
                class="text-orange-400 hover:text-orange-600"
              >
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="!openingStarted" class="flex gap-4 mb-6">
          <button 
            @click="startSingleMode"
            class="flex-1 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
          >
            <i class="fa fa-eye"></i>
            Einzeln öffnen (Karussell)
          </button>
          <button 
            @click="openAllAtOnce"
            class="flex-1 px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-slate-900 font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
          >
            <i class="fa fa-boxes"></i>
            Alle auf einmal öffnen
          </button>
        </div>

        <!-- Single Mode Carousel -->
        <div v-if="mode === 'single'" class="text-center">
          <div v-if="currentTicketIndex < tickets.length" class="space-y-6">
            <div class="text-lg font-semibold text-slate-700">
              Ticket {{ currentTicketIndex + 1 }} von {{ tickets.length }}
            </div>
            
            <!-- Slot Machine Animation -->
            <div v-if="currentResult && currentResult.all_products" class="mb-6">
              <SlotMachine 
                :all-products="currentResult.all_products"
                :winning-product="currentResult.outcome?.product"
                :is-visible="true"
                @animation-complete="onSlotAnimationComplete"
              />
            </div>

            <!-- Traditional Ticket Card (fallback or after slot animation) -->
            <div v-if="!currentResult || !currentResult.all_products || slotAnimationComplete" class="relative mx-auto w-80 h-64">
              <div 
                :class="[
                  'absolute inset-0 rounded-xl shadow-2xl cursor-pointer transition-all duration-700 transform-preserve-3d',
                  (currentResult && slotAnimationComplete) ? 'rotate-y-180' : 'hover:scale-105'
                ]"
                @click="!currentResult && !opening ? openCurrentTicket() : null"
              >
                  currentResult ? 'rotate-y-180' : 'hover:scale-105'
                ]"
                @click="!currentResult && !opening ? openCurrentTicket() : null"
              >
                <!-- Front (Closed Ticket) -->
                <div class="absolute inset-0 w-full h-full backface-hidden rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 flex flex-col items-center justify-center text-white border-2 border-yellow-500">
                  <div v-if="opening" class="animate-spin w-8 h-8 border-4 border-yellow-500 border-t-transparent rounded-full mb-4"></div>
                  <div v-else class="text-yellow-500 text-4xl mb-4"><i class="fa fa-ticket"></i></div>
                  <div class="text-2xl font-bold">#{{ currentTicket.serial }}</div>
                  <div class="text-sm opacity-90 mt-2 flex items-center gap-2">
                    <i v-if="!opening" class="fa fa-hand-pointer"></i>
                    {{ opening ? 'Wird geöffnet...' : 'Klicken zum Öffnen' }}
                  </div>
                </div>

                <!-- Back (Result) -->
                <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 rounded-xl flex flex-col items-center justify-center p-4"
                     :class="currentResult?.outcome?.type === 'prize' ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-slate-900 border-2 border-yellow-500' : 'bg-gradient-to-br from-slate-600 to-slate-700 text-white border-2 border-slate-500'">
                  <div v-if="currentResult?.outcome">
                    <div v-if="currentResult.outcome.type === 'prize'" class="text-center">
                      <div class="text-4xl mb-2"><i class="fa fa-trophy text-yellow-600"></i></div>
                      <div class="text-xl font-bold mb-2">{{ currentResult.outcome.message }}</div>
                      <div v-if="currentResult.outcome.product.image_url" class="mb-3">
                        <img :src="currentResult.outcome.product.image_url" :alt="currentResult.outcome.product.name" class="w-20 h-20 object-cover rounded-lg mx-auto border-2 border-slate-800">
                      </div>
                      <div class="text-sm font-semibold">{{ currentResult.outcome.product.name }}</div>
                      <div class="text-xs opacity-75 mt-1">Wert: {{ formatPrice(currentResult.outcome.product.value) }}</div>
                    </div>
                    <div v-else class="text-center">
                      <div class="text-4xl mb-2"><i class="fa fa-times-circle"></i></div>
                      <div class="text-lg">{{ currentResult.outcome.message }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Next Button -->
            <div v-if="currentResult && slotAnimationComplete && currentTicketIndex < tickets.length - 1" class="space-y-3">
              <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button 
                  @click="nextTicket"
                  class="px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                  Nächstes Ticket
                  <i class="fa fa-arrow-right"></i>
                </button>
                <button 
                  v-if="remainingTicketsCount > 1"
                  @click="openAllRemainingTickets"
                  class="px-8 py-3 bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                  <i class="fa fa-forward"></i>
                  Alle restlichen ({{ remainingTicketsCount - 1 }}) öffnen
                </button>
              </div>
            </div>
            
            <!-- Before opening any tickets - option to open all -->
            <div v-if="!currentResult && !opening" class="space-y-3">
              <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button 
                  @click="openCurrentTicket"
                  class="px-8 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                  <i class="fa fa-eye"></i>
                  Dieses Ticket öffnen
                </button>
                <button 
                  v-if="remainingTicketsCount > 1"
                  @click="openAllRemainingTickets"
                  class="px-8 py-3 bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                  <i class="fa fa-boxes"></i>
                  Alle {{ remainingTicketsCount }} Tickets öffnen
                </button>
              </div>
            </div>
            
            <!-- Final ticket summary button -->
            <div v-else-if="currentResult && slotAnimationComplete && currentTicketIndex === tickets.length - 1">
              <button 
                @click="showSummary"
                class="px-8 py-3 bg-yellow-500 hover:bg-yellow-400 text-slate-900 font-semibold rounded-lg transition-colors flex items-center gap-2 mx-auto"
              >
                <i class="fa fa-list"></i>
                Zusammenfassung anzeigen
              </button>
            </div>
          </div>
        </div>

        <!-- All At Once Mode -->
        <div v-if="mode === 'all'" class="space-y-6">
          <div v-if="openingAll" class="text-center">
            <div class="animate-spin w-12 h-12 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-4"></div>
            <div class="text-lg font-semibold text-slate-700 flex items-center justify-center gap-2">
              <i class="fa fa-cog"></i>
              Alle Tickets werden geöffnet...
            </div>
          </div>
          
          <div v-if="allResults.length" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-96 overflow-y-auto">
            <div 
              v-for="result in allResults" 
              :key="result.ticket_id"
              class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow"
              :class="result.outcome?.type === 'prize' ? 'border-yellow-200' : 'border-slate-200'"
            >
              <!-- Product Image -->
              <div class="aspect-square bg-slate-100 flex items-center justify-center">
                <div v-if="result.outcome?.type === 'prize' && result.outcome.product.image_url">
                  <img 
                    :src="result.outcome.product.image_url" 
                    :alt="result.outcome.product.name" 
                    class="w-full h-full object-cover"
                  >
                </div>
                <div v-else-if="result.outcome?.type === 'prize'" class="text-yellow-500 text-2xl">
                  <i class="fa fa-gift"></i>
                </div>
                <div v-else class="text-slate-400 text-2xl">
                  <i class="fa fa-times-circle"></i>
                </div>
              </div>
              
              <!-- Product Info -->
              <div class="p-2 space-y-1">
                <div v-if="result.outcome?.type === 'prize'">
                  <h6 class="font-medium text-xs text-slate-800 line-clamp-2 mb-1">
                    {{ result.outcome.product.name }}
                  </h6>
                  <!-- Tier Badge -->
                  <div class="flex justify-center mb-1">
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white" 
                          :class="`tier-${result.outcome.tier.toLowerCase()}`">
                      {{ result.outcome.tier.toUpperCase() }}
                    </span>
                  </div>
                </div>
                <div v-else class="text-center">
                  <span class="text-xs text-slate-600">Nichts gewonnen</span>
                </div>
                
                <!-- Ticket Number -->
                <div class="text-center">
                  <span class="text-xs text-slate-500 flex items-center justify-center gap-1">
                    <i class="fa fa-ticket"></i>
                    #{{ result.serial }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div v-if="showingSummary" class="space-y-6">
          <h4 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="fa fa-chart-pie text-yellow-500"></i>
            Zusammenfassung
          </h4>
          
          <!-- Won Products Grid -->
          <div v-if="wonPrizes.length" class="space-y-4">
            <h5 class="font-semibold text-slate-800 flex items-center gap-2">
              <i class="fa fa-gift text-yellow-500"></i>
              Deine Gewinne:
            </h5>
            
            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
              <div 
                v-for="prize in wonPrizes" 
                :key="prize.ticket_id" 
                class="bg-white border border-yellow-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow"
              >
                <!-- Product Image -->
                <div class="aspect-square bg-slate-100 flex items-center justify-center">
                  <img 
                    v-if="prize.outcome.product.image_url" 
                    :src="prize.outcome.product.image_url" 
                    :alt="prize.outcome.product.name" 
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="text-slate-400 text-2xl">
                    <i class="fa fa-gift"></i>
                  </div>
                </div>
                
                <!-- Product Info -->
                <div class="p-2 space-y-2">
                  <h6 class="font-medium text-xs text-slate-800 line-clamp-2">
                    {{ prize.outcome.product.name }}
                  </h6>
                  
                  <div class="flex items-center justify-center text-xs text-slate-600">
                    <span class="flex items-center gap-1">
                      <i class="fa fa-ticket"></i>
                      #{{ prize.serial }}
                    </span>
                  </div>
                  
                  <!-- Tier Badge -->
                  <div class="flex justify-center">
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold text-white" 
                          :class="`tier-${prize.outcome.tier.toLowerCase()}`">
                      Tier {{ prize.outcome.tier.toUpperCase() }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Prizes Message -->
          <div v-else class="text-center py-8">
            <div class="text-4xl text-slate-400 mb-2">
              <i class="fa fa-sad-tear"></i>
            </div>
            <p class="text-slate-600">Diesmal leider nichts gewonnen. Viel Glück beim nächsten Mal!</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <button 
              @click="goToInventory"
              class="flex-1 px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-slate-900 font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
            >
              <i class="fa fa-box"></i>
              Zu meinem Inventar
            </button>
            <button 
              @click="close"
              class="flex-1 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
            >
              <i class="fa fa-times"></i>
              Schließen
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="!mode && openingStarted" class="text-center py-12">
          <div class="animate-spin w-12 h-12 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-4"></div>
          <div class="text-lg font-semibold text-slate-700 flex items-center justify-center gap-2">
            <i class="fa fa-cog"></i>
            Bereite Tickets vor...
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue'
import axios from 'axios'
import SlotMachine from './SlotMachine.vue'

const props = defineProps({
  show: Boolean,
  tickets: Array
})

const emit = defineEmits(['close'])

// State
const mode = ref(null) // 'single' or 'all'
const openingStarted = ref(false)
const opening = ref(false)
const openingAll = ref(false)
const currentTicketIndex = ref(0)
const currentResult = ref(null)
const allResults = ref([])
const showingSummary = ref(false)
const slotAnimationComplete = ref(false)
const warningMessage = ref(null)

// Computed
const currentTicket = computed(() => props.tickets[currentTicketIndex.value])
const prizesWon = computed(() => allResults.value.filter(r => r.outcome?.type === 'prize').length)
const wonPrizes = computed(() => allResults.value.filter(r => r.outcome?.type === 'prize'))

// Compute remaining tickets that haven't been opened yet
const remainingTicketsCount = computed(() => {
  if (mode.value === 'single') {
    // In single mode, count tickets from current index onwards that haven't been processed
    const openedTicketIds = new Set(allResults.value.map(r => r.ticket_id))
    if (currentResult.value && !openedTicketIds.has(currentTicket.value?.id)) {
      openedTicketIds.add(currentTicket.value?.id)
    }
    return props.tickets.slice(currentTicketIndex.value).filter(ticket => !openedTicketIds.has(ticket.id)).length
  } else {
    // In other modes, check if we have results for all tickets
    const openedTicketIds = new Set(allResults.value.map(r => r.ticket_id))
    return props.tickets.filter(ticket => !openedTicketIds.has(ticket.id)).length
  }
})

// Methods
const close = () => {
  // Reset state
  mode.value = null
  openingStarted.value = false
  opening.value = false
  openingAll.value = false
  currentTicketIndex.value = 0
  currentResult.value = null
  allResults.value = []
  showingSummary.value = false
  slotAnimationComplete.value = false
  warningMessage.value = null
  emit('close')
}

const startSingleMode = () => {
  mode.value = 'single'
  openingStarted.value = true
}

const openCurrentTicket = async () => {
  if (opening.value || currentResult.value) return
  
  opening.value = true
  try {
    const response = await axios.post(`/tickets/${currentTicket.value.id}/open`)
    
    // Handle already opened ticket
    if (response.data.already_opened) {
      console.warn('Ticket already opened:', response.data.message)
      warningMessage.value = response.data.warning
      // Still display result but mark as already opened
      currentResult.value = response.data
    } else {
      // Speichere die komplette Response mit all_products
      currentResult.value = response.data
      console.log('Ticket opened:', response.data)
    }
  } catch (error) {
    console.error('Error opening ticket:', error)
    currentResult.value = { outcome: { type: 'error', message: 'Fehler beim Öffnen' } }
  }
  opening.value = false
}

const nextTicket = () => {
  // Store current result in allResults for summary
  if (currentResult.value && currentResult.value.outcome) {
    allResults.value.push({
      ticket_id: currentTicket.value.id,
      serial: currentTicket.value.serial,
      outcome: currentResult.value.outcome
    })
  }
  
  currentTicketIndex.value++
  currentResult.value = null
  slotAnimationComplete.value = false
}

const onSlotAnimationComplete = () => {
  slotAnimationComplete.value = true
}

const openAllRemainingTickets = async () => {
  // Get remaining tickets that haven't been opened yet
  const openedTicketIds = new Set(allResults.value.map(r => r.ticket_id))
  if (currentResult.value && currentTicket.value) {
    openedTicketIds.add(currentTicket.value.id)
  }
  
  const remainingTickets = props.tickets.filter(ticket => !openedTicketIds.has(ticket.id))
  
  if (remainingTickets.length === 0) {
    warningMessage.value = 'Alle Tickets wurden bereits geöffnet!'
    return
  }
  
  try {
    const ticketIds = remainingTickets.map(t => t.id)
    const response = await axios.post('/tickets/open-all', { ticket_ids: ticketIds })
    
    // Add results to allResults for summary
    response.data.results.forEach(result => {
      allResults.value.push(result)
    })
    
    // Show warning if some tickets were already opened
    if (response.data.warning) {
      warningMessage.value = response.data.warning
      console.warn('Some tickets already opened:', response.data.stats)
    }
    
    // Switch to summary mode
    showingSummary.value = true
    console.log('All remaining tickets processed:', response.data.stats)
  } catch (error) {
    console.error('Error opening remaining tickets:', error)
    warningMessage.value = 'Fehler beim Öffnen der restlichen Tickets'
  }
}

const openAllAtOnce = async () => {
  mode.value = 'all'
  openingStarted.value = true
  openingAll.value = true
  
  try {
    const ticketIds = props.tickets.map(t => t.id)
    const response = await axios.post('/tickets/open-all', { ticket_ids: ticketIds })
    allResults.value = response.data.results
    
    // Show warning if some tickets were already opened
    if (response.data.warning) {
      warningMessage.value = response.data.warning
      console.warn('Some tickets already opened:', response.data.stats)
    }
    
    console.log('All tickets processed:', response.data.stats)
  } catch (error) {
    console.error('Error opening all tickets:', error)
  }
  openingAll.value = false
}

const showSummary = () => {
  // Add the last ticket result to allResults if in single mode
  if (mode.value === 'single' && currentResult.value && currentResult.value.outcome) {
    allResults.value.push({
      ticket_id: currentTicket.value.id,
      serial: currentTicket.value.serial,
      outcome: currentResult.value.outcome
    })
  }
  showingSummary.value = true
}

const formatPrice = (amount) => {
  return new Intl.NumberFormat('de-DE', { 
    style: 'currency', 
    currency: 'EUR' 
  }).format(amount || 0)
}

const goToInventory = () => {
  window.location.href = '/inventory'
}
</script>

<style scoped>
.transform-preserve-3d {
  transform-style: preserve-3d;
}
.backface-hidden {
  backface-visibility: hidden;
}
.rotate-y-180 {
  transform: rotateY(180deg);
}

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
