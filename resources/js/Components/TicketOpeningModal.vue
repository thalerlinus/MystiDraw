<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-2 sm:px-4 pt-2 sm:pt-4 pb-12 sm:pb-20 text-center">
      <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="close"></div>
      
      <div class="relative inline-block w-full max-w-4xl p-3 sm:p-6 my-4 sm:my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl sm:rounded-2xl">
        <div class="absolute top-0 right-0 pt-2 sm:pt-4 pr-2 sm:pr-4">
          <button @click="close" class="p-1 sm:p-2 text-slate-400 hover:text-slate-600 transition-colors">
            <font-awesome-icon :icon="['fas', 'times']" class="w-4 h-4 sm:w-6 sm:h-6" />
          </button>
        </div>

        <div class="mb-4 sm:mb-6">
          <h3 class="text-lg sm:text-2xl font-bold text-slate-900 flex items-center gap-2 sm:gap-3">
            <font-awesome-icon :icon="['fas', 'gift']" class="text-yellow-500 text-sm sm:text-base" />
            Tickets öffnen
          </h3>
          <p class="text-xs sm:text-sm text-slate-600 mt-1 sm:mt-2">{{ tickets.length }} Ticket(s) bereit zum Öffnen</p>
        </div>

        <!-- Warning Banner for Already Opened Tickets -->
        <div v-if="warningMessage" class="mb-4 sm:mb-6 p-3 sm:p-4 bg-orange-100 border border-orange-400 rounded-lg">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-orange-500 text-sm sm:text-base" />
            </div>
            <div class="ml-2 sm:ml-3">
              <h3 class="text-xs sm:text-sm font-medium text-orange-800">
                Achtung
              </h3>
              <div class="mt-1 text-xs sm:text-sm text-orange-700">
                {{ warningMessage }}
              </div>
            </div>
            <div class="ml-auto pl-2 sm:pl-3">
              <button 
                @click="warningMessage = null"
                class="text-orange-400 hover:text-orange-600"
              >
                <font-awesome-icon :icon="['fas', 'times']" class="text-sm sm:text-base" />
              </button>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div v-if="!openingStarted" class="flex flex-col sm:flex-row gap-2 sm:gap-4 mb-4 sm:mb-6">
          <button 
            @click="startSingleMode"
            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
          >
            <font-awesome-icon :icon="['fas', 'eye']" class="text-sm sm:text-base" />
            <span class="hidden sm:inline">Einzeln öffnen (Karussell)</span>
            <span class="sm:hidden">Einzeln öffnen</span>
          </button>
          <button 
            @click="openAllAtOnce"
            class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-yellow-500 hover:bg-yellow-400 text-slate-900 font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
          >
            <font-awesome-icon :icon="['fas', 'boxes']" class="text-sm sm:text-base" />
            Alle auf einmal öffnen
          </button>
        </div>

        <!-- Single Mode Carousel -->
        <div v-if="mode === 'single'" class="text-center">
          <div v-if="currentTicketIndex < tickets.length" class="space-y-4 sm:space-y-6">
            <div class="text-sm sm:text-lg font-semibold text-slate-700">
              Ticket {{ currentTicketIndex + 1 }} von {{ tickets.length }}
            </div>
            
            <!-- Slot Machine Animation -->
            <div v-if="currentResult && currentResult.all_products && !opening" class="mb-4 sm:mb-6">
              <SlotMachine 
                :all-products="currentResult.all_products"
                :winning-product="currentResult.outcome?.product"
                :is-visible="true"
                @animation-complete="onSlotAnimationComplete"
              />
            </div>

            <!-- Loading State während API Call -->
            <div v-if="opening && !currentResult" class="mb-4 sm:mb-6 flex flex-col items-center justify-center py-8">
              <div class="animate-spin w-8 h-8 sm:w-12 sm:h-12 border-4 border-yellow-500 border-t-transparent rounded-full mb-3 sm:mb-4"></div>
              <div class="text-sm sm:text-lg font-semibold text-slate-700">
                Ticket wird geöffnet...
              </div>
            </div>

            <!-- Traditional Ticket Card (fallback or after slot animation) -->
            <div v-if="!opening && (!currentResult || !currentResult.all_products || slotAnimationComplete)" class="relative mx-auto w-56 h-40 sm:w-72 sm:h-48">
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
                  <div v-if="opening" class="animate-spin w-6 h-6 sm:w-8 sm:h-8 border-4 border-yellow-500 border-t-transparent rounded-full mb-3 sm:mb-4"></div>
                  <div v-else class="text-yellow-500 text-2xl sm:text-4xl mb-3 sm:mb-4">
                    <font-awesome-icon :icon="['fas', 'ticket']" />
                  </div>
                  <div class="text-lg sm:text-2xl font-bold">#{{ currentTicket.serial }}</div>
                  <div class="text-xs sm:text-sm opacity-90 mt-1 sm:mt-2 flex items-center gap-1 sm:gap-2">
                    <font-awesome-icon v-if="!opening" :icon="['far', 'hand-pointer']" class="text-xs sm:text-sm" />
                    {{ opening ? 'Wird geöffnet...' : 'Klicken zum Öffnen' }}
                  </div>
                </div>

                <!-- Back (Result) -->
                <div class="absolute inset-0 w-full h-full backface-hidden rotate-y-180 rounded-xl flex items-center justify-center"
                     :class="[
                       currentResult?.outcome?.type === 'prize' ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-slate-900 border-2 border-yellow-500 sm:p-4 sm:p-6' : 'bg-gradient-to-br from-slate-600 to-slate-700 text-white border-2 border-slate-500 sm:p-4 sm:p-6',
                       currentResult?.outcome?.is_last_one ? 'ring-4 ring-yellow-300 shadow-2xl' : ''
                     ]">
                  
                  <!-- Last One Crown Badge -->
                  <div v-if="currentResult?.outcome?.is_last_one" class="absolute -top-1 sm:-top-2 -right-1 sm:-right-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900 text-xs font-bold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full border-2 border-yellow-300">
                    <font-awesome-icon :icon="['fas', 'crown']" class="mr-1 text-xs" />
                    <span class="hidden sm:inline">LAST ONE!</span>
                    <span class="sm:hidden">LAST!</span>
                  </div>
                  
                  <div v-if="currentResult?.outcome">
                    <!-- Mobile Layout: Image left, text right -->
                    <div v-if="currentResult.outcome.type === 'prize'" class="flex items-center gap-3 sm:flex-col sm:text-center w-full p-3 sm:p-0">
                      <!-- Mobile: Large product image on the left -->
                      <div v-if="currentResult.outcome.product.image_url" class="flex-shrink-0 sm:mb-2">
                        <img :src="currentResult.outcome.product.image_url" :alt="currentResult.outcome.product.name" 
                             :class="[
                               'w-28 h-28 sm:w-20 sm:h-20 object-cover rounded-lg border-2 shadow-lg',
                               currentResult.outcome.is_last_one ? 'border-yellow-700 ring-2 ring-yellow-300' : 'border-slate-800'
                             ]">
                      </div>
                      
                      <!-- Mobile: Text content on the right, Desktop: Below image -->
                      <div class="flex-1 sm:flex-none">
                        <div class="text-lg sm:text-2xl sm:text-3xl mb-1 sm:mb-2">
                          <font-awesome-icon :icon="currentResult.outcome.is_last_one ? ['fas', 'crown'] : ['fas', 'trophy']" :class="currentResult.outcome.is_last_one ? 'text-yellow-700' : 'text-yellow-600'" />
                        </div>
                        <div class="text-sm sm:text-xs sm:text-sm font-bold mb-1 sm:mb-2 leading-tight">{{ currentResult.outcome.message }}</div>
                        <div class="text-sm sm:text-xs sm:text-sm font-semibold">{{ currentResult.outcome.product.name }}</div>
                      </div>
                    </div>
                    
                    <!-- Loss state -->
                    <div v-else class="flex items-center gap-3 sm:flex-col sm:text-center w-full p-3 sm:p-0">
                      <div class="text-2xl sm:text-3xl mb-0 sm:mb-2">
                        <font-awesome-icon :icon="['fas', 'times-circle']" />
                      </div>
                      <div class="text-sm sm:text-sm">{{ currentResult.outcome.message }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- After opening ticket - control buttons -->
            <div v-if="currentResult && slotAnimationComplete && currentTicketIndex < tickets.length - 1" class="space-y-2 sm:space-y-3">
              <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-center">
                <button 
                  @click="nextTicket"
                  class="px-4 sm:px-8 py-2 sm:py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                >
                  Nächstes Ticket
                  <font-awesome-icon :icon="['fas', 'arrow-right']" class="text-xs sm:text-sm" />
                </button>
                <button 
                  v-if="remainingTicketsCount > 0"
                  @click="openAllRemainingTickets"
                  class="px-4 sm:px-8 py-2 sm:py-3 bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
                >
                  <font-awesome-icon :icon="['fas', 'forward']" class="text-xs sm:text-sm" />
                  <span class="hidden sm:inline">Alle restlichen ({{ remainingTicketsCount }}) öffnen</span>
                  <span class="sm:hidden">Alle {{ remainingTicketsCount }} öffnen</span>
                </button>
              </div>
            </div>
            
            <!-- Final ticket summary button -->
            <div v-else-if="currentResult && slotAnimationComplete && currentTicketIndex === tickets.length - 1">
              <button 
                @click="showSummary"
                class="px-4 sm:px-8 py-2 sm:py-3 bg-yellow-500 hover:bg-yellow-400 text-slate-900 font-semibold rounded-lg transition-colors flex items-center gap-2 mx-auto text-sm sm:text-base"
              >
                <font-awesome-icon :icon="['fas', 'list']" class="text-xs sm:text-sm" />
                Zusammenfassung anzeigen
              </button>
            </div>
          </div>
        </div>

        <!-- All At Once Mode -->
        <div v-if="mode === 'all'" class="space-y-4 sm:space-y-6">
          <div v-if="openingAll" class="text-center">
            <div class="animate-spin w-8 h-8 sm:w-12 sm:h-12 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-3 sm:mb-4"></div>
            <div class="text-sm sm:text-lg font-semibold text-slate-700 flex items-center justify-center gap-2">
              <font-awesome-icon :icon="['fas', 'cog']" class="text-sm sm:text-base" />
              Alle Tickets werden geöffnet...
            </div>
          </div>
          
          <div v-if="allResults.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3 max-h-64 sm:max-h-96 overflow-y-auto">
            <div 
              v-for="result in allResults" 
              :key="result.ticket_id"
              class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow"
              :class="[
                result.outcome?.type === 'prize' ? 'border-yellow-200' : 'border-slate-200',
                result.outcome?.is_last_one ? 'border-2 border-yellow-400 ring-2 ring-yellow-200' : 'border'
              ]"
            >
              <!-- Last One Badge -->
              <div v-if="result.outcome?.is_last_one" class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900 text-xs font-bold text-center py-1 px-2">
                <font-awesome-icon :icon="['fas', 'crown']" class="mr-1" />
                LAST ONE!
              </div>
              
              <!-- Product Image -->
              <div class="aspect-square bg-slate-100 flex items-center justify-center">
                <div v-if="result.outcome?.type === 'prize' && result.outcome.product.image_url">
                  <img 
                    :src="result.outcome.product.image_url" 
                    :alt="result.outcome.product.name" 
                    :class="[
                      'w-full h-full object-cover',
                      result.outcome.is_last_one ? 'ring-2 ring-yellow-300' : ''
                    ]"
                  >
                </div>
                <div v-else-if="result.outcome?.type === 'prize'" :class="result.outcome.is_last_one ? 'text-yellow-600 text-2xl' : 'text-yellow-500 text-2xl'">
                  <font-awesome-icon :icon="result.outcome.is_last_one ? ['fas', 'crown'] : ['fas', 'gift']" />
                </div>
                <div v-else class="text-slate-400 text-2xl">
                  <font-awesome-icon :icon="['fas', 'times-circle']" />
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
                          :class="[
                            `tier-${result.outcome.tier.toLowerCase()}`,
                            result.outcome.is_last_one ? 'ring-2 ring-yellow-300' : ''
                          ]">
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
                    <font-awesome-icon :icon="['fas', 'ticket']" />
                    #{{ result.serial }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div v-if="showingSummary" class="space-y-4 sm:space-y-6">
          <h4 class="text-lg sm:text-xl font-bold text-slate-900 flex items-center gap-2">
            <font-awesome-icon :icon="['fas', 'chart-pie']" class="text-yellow-500 text-sm sm:text-base" />
            Zusammenfassung
          </h4>
          
          <!-- Won Products Grid -->
          <div v-if="groupedPrizes.length" class="space-y-3 sm:space-y-4">
            <h5 class="font-semibold text-slate-800 flex items-center gap-2 text-sm sm:text-base">
              <font-awesome-icon :icon="['fas', 'gift']" class="text-yellow-500 text-sm sm:text-base" />
              Deine Gewinne:
            </h5>
            
            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
              <div 
                v-for="prizeGroup in groupedPrizes" 
                :key="prizeGroup.product.id" 
                class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow relative"
                :class="prizeGroup.is_last_one ? 'border-2 border-yellow-400 ring-2 ring-yellow-200' : 'border border-yellow-200'"
              >
                <!-- Count Badge -->
                <div v-if="prizeGroup.count > 1" class="absolute top-1 sm:top-2 right-1 sm:right-2 z-10 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 sm:h-6 sm:w-6 flex items-center justify-center count-badge">
                  {{ prizeGroup.count }}
                </div>
                
                <!-- Last One Badge -->
                <div v-if="prizeGroup.is_last_one" class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900 text-xs font-bold text-center py-0.5 sm:py-1 px-1 sm:px-2">
                  <font-awesome-icon :icon="['fas', 'crown']" class="mr-1 text-xs" />
                  <span class="hidden sm:inline">LETZTES TICKET!</span>
                  <span class="sm:hidden">LAST!</span>
                </div>
                
                <!-- Product Image -->
                <div class="aspect-square bg-slate-100 flex items-center justify-center">
                  <img 
                    v-if="prizeGroup.product.image_url" 
                    :src="prizeGroup.product.image_url" 
                    :alt="prizeGroup.product.name" 
                    class="w-full h-full object-cover"
                  >
                  <div v-else class="text-slate-400 text-lg sm:text-2xl">
                    <font-awesome-icon :icon="['fas', 'gift']" />
                  </div>
                </div>
                
                <!-- Product Info -->
                <div class="p-1.5 sm:p-2 space-y-1 sm:space-y-2">
                  <h6 class="font-medium text-xs sm:text-sm text-slate-800 line-clamp-2">
                    {{ prizeGroup.product.name }}
                  </h6>
                  
                  <!-- Ticket Numbers -->
                  <div class="flex items-center justify-center text-xs text-slate-600">
                    <span v-if="prizeGroup.count === 1" class="flex items-center gap-1">
                      <font-awesome-icon :icon="['fas', 'ticket']" class="text-xs" />
                      <span class="text-xs">#{{ prizeGroup.tickets[0].serial }}</span>
                    </span>
                    <span v-else class="flex items-center gap-1" :title="prizeGroup.tickets.map(t => `#${t.serial}`).join(', ')">
                      <font-awesome-icon :icon="['fas', 'ticket']" class="text-xs" />
                      <span class="text-xs">{{ prizeGroup.count }}x Tickets</span>
                    </span>
                  </div>
                  
                  <!-- Tier Badge -->
                  <div class="flex justify-center">
                    <span class="px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-bold text-white" 
                          :class="[
                            `tier-${prizeGroup.tier.toLowerCase()}`,
                            prizeGroup.is_last_one ? 'ring-1 sm:ring-2 ring-yellow-300' : ''
                          ]">
                      <span class="hidden sm:inline">Tier </span>{{ prizeGroup.tier.toUpperCase() }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Prizes Message -->
          <div v-else class="text-center py-6 sm:py-8">
            <div class="text-2xl sm:text-4xl text-slate-400 mb-2">
              <font-awesome-icon :icon="['fas', 'sad-tear']" />
            </div>
            <p class="text-sm sm:text-base text-slate-600">Diesmal leider nichts gewonnen. Viel Glück beim nächsten Mal!</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <button 
              @click="goToInventory"
              class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-slate-900 font-semibold rounded-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm sm:text-base"
            >
              <font-awesome-icon :icon="['fas', 'box']" class="text-xs sm:text-sm" />
              <span class="hidden sm:inline">Zu meinem Inventar</span>
              <span class="sm:hidden">Inventar</span>
            </button>
            <button 
              @click="close"
              class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 text-sm sm:text-base"
            >
              <font-awesome-icon :icon="['fas', 'times']" class="text-xs sm:text-sm" />
              Schließen
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="!mode && openingStarted" class="text-center py-8 sm:py-12">
          <div class="animate-spin w-8 h-8 sm:w-12 sm:h-12 border-4 border-yellow-500 border-t-transparent rounded-full mx-auto mb-3 sm:mb-4"></div>
          <div class="text-sm sm:text-lg font-semibold text-slate-700 flex items-center justify-center gap-2">
            <font-awesome-icon :icon="['fas', 'cog']" class="text-sm sm:text-base" />
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

// Group identical prizes and count them
const groupedPrizes = computed(() => {
  const prizes = allResults.value.filter(r => r.outcome?.type === 'prize')
  const grouped = new Map()
  
  prizes.forEach(prize => {
    // Use product ID as key, but fallback to name if ID doesn't exist
    const productKey = prize.outcome.product?.id || prize.outcome.product?.name || 'unknown'
    
    if (grouped.has(productKey)) {
      const existing = grouped.get(productKey)
      existing.count += 1
      existing.tickets.push({
        serial: prize.serial,
        ticket_id: prize.ticket_id,
        is_last_one: prize.outcome.is_last_one
      })
      // Update is_last_one if any ticket has it
      if (prize.outcome.is_last_one) {
        existing.is_last_one = true
      }
    } else {
      grouped.set(productKey, {
        product: prize.outcome.product,
        tier: prize.outcome.tier,
        count: 1,
        is_last_one: prize.outcome.is_last_one || false,
        tickets: [{
          serial: prize.serial,
          ticket_id: prize.ticket_id,
          is_last_one: prize.outcome.is_last_one
        }]
      })
    }
  })
  
  return Array.from(grouped.values())
})

// Compute remaining tickets that haven't been opened yet
const remainingTicketsCount = computed(() => {
  if (mode.value === 'single') {
    if (currentResult.value) {
      // Current ticket is already opened, count remaining tickets after current
      return props.tickets.length - currentTicketIndex.value - 1
    } else {
      // Current ticket not opened yet, count all tickets from current position
      return props.tickets.length - currentTicketIndex.value
    }
  } else {
    // In other modes, use the full ticket count
    return props.tickets.length
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

const startSingleMode = async () => {
  mode.value = 'single'
  openingStarted.value = true
  
  // Automatisch das erste Ticket öffnen
  await openCurrentTicket()
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

const nextTicket = async () => {
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
  
  // Automatisch nächstes Ticket öffnen
  await openCurrentTicket()
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
    
    // Switch to summary mode after opening all tickets
    showingSummary.value = true
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

/* Count badge styling */
.count-badge {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: 2px solid white;
}

/* Text truncation */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
