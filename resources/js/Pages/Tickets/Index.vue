<template>
  <MainLayout>
    <template #header>
      <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-navy-900 px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
          <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4">
              Meine Tickets
            </h1>
            <p class="text-lg sm:text-xl text-navy-200 font-medium">
              Verwalte deine Tickets und entdecke deine Gewinne
            </p>
          </div>
        </div>
      </div>
    </template>

    <div class="bg-gradient-to-b from-navy-50 via-white to-gold-50 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <!-- Statistics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8 sm:mb-12">
          <!-- Total Tickets -->
          <div class="bg-white/90 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 sm:p-6 border border-navy-100 hover:border-navy-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-3 bg-navy-gradient rounded-xl">
                <font-awesome-icon :icon="['fas', 'ticket']" class="text-xl sm:text-2xl text-white" />
              </div>
              <div class="ml-4 sm:ml-6">
                <p class="text-sm sm:text-base font-semibold text-navy-600">Gesamte Tickets</p>
                <p class="text-2xl sm:text-3xl font-black text-navy-900">{{ stats.total_tickets }}</p>
              </div>
            </div>
          </div>

          <!-- Unopened Tickets -->
          <div class="bg-white/90 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 sm:p-6 border border-gold-100 hover:border-gold-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-3 bg-gradient-to-r from-gold-500 to-gold-600 rounded-xl animate-pulse">
                <font-awesome-icon :icon="['fas', 'envelope']" class="text-xl sm:text-2xl text-white" />
              </div>
              <div class="ml-4 sm:ml-6">
                <p class="text-sm sm:text-base font-semibold text-gold-700">Ungeöffnet</p>
                <p class="text-2xl sm:text-3xl font-black text-gold-800">{{ stats.unopened_tickets }}</p>
              </div>
            </div>
          </div>

          <!-- Opened Tickets -->
          <div class="bg-white/90 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 sm:p-6 border border-emerald-100 hover:border-emerald-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-3 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl">
                <font-awesome-icon :icon="['fas', 'envelope-open']" class="text-xl sm:text-2xl text-white" />
              </div>
              <div class="ml-4 sm:ml-6">
                <p class="text-sm sm:text-base font-semibold text-emerald-600">Geöffnet</p>
                <p class="text-2xl sm:text-3xl font-black text-emerald-700">{{ stats.opened_tickets }}</p>
              </div>
            </div>
          </div>

          <!-- Prizes Won -->
          <div class="bg-white/90 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-4 sm:p-6 border border-amber-100 hover:border-amber-200 transform hover:-translate-y-1">
            <div class="flex items-center">
              <div class="flex-shrink-0 p-3 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl animate-bounce">
                <font-awesome-icon :icon="['fas', 'trophy']" class="text-xl sm:text-2xl text-white" />
              </div>
              <div class="ml-4 sm:ml-6">
                <p class="text-sm sm:text-base font-semibold text-amber-600">Preise gewonnen</p>
                <p class="text-2xl sm:text-3xl font-black text-amber-700">{{ stats.prizes_won }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tickets by Raffle -->
        <div class="space-y-6 sm:space-y-8">
          <div v-for="raffleGroup in ticketsByRaffle" :key="raffleGroup.raffle.id">
            <div class="bg-white/95 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-xl border border-navy-100 overflow-hidden hover:shadow-2xl transition-all duration-500">
              <!-- Raffle Header -->
              <div class="bg-gradient-to-r from-navy-800 via-navy-700 to-navy-800 px-4 sm:px-6 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                  <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Klickbares Raffle Bild -->
                    <Link 
                      :href="raffleGroup.raffle.slug ? route('raffles.show', raffleGroup.raffle.slug) : '#'"
                      class="w-12 h-12 sm:w-14 sm:h-14 bg-white/10 rounded-xl flex items-center justify-center overflow-hidden hover:ring-2 hover:ring-gold-400 transition-all cursor-pointer transform hover:scale-105 duration-300"
                    >
                      <img 
                        v-if="raffleGroup.raffle.image_url" 
                        :src="raffleGroup.raffle.image_url" 
                        :alt="raffleGroup.raffle.name" 
                        class="w-full h-full object-cover"
                      >
                      <font-awesome-icon v-else :icon="['fas', 'gift']" class="text-white/70 text-lg" />
                    </Link>
                    <div>
                      <Link 
                        :href="raffleGroup.raffle.slug ? route('raffles.show', raffleGroup.raffle.slug) : '#'"
                        class="group cursor-pointer"
                      >
                        <h3 class="text-lg sm:text-xl font-bold text-white group-hover:text-gold-300 transition-colors duration-300 flex items-center">
                          {{ raffleGroup.raffle.name }}
                          <font-awesome-icon :icon="['fas', 'external-link-alt']" class="ml-2 text-sm text-white/50 group-hover:text-gold-300" />
                        </h3>
                      </Link>
                      <p class="text-sm sm:text-base text-navy-200 font-medium">{{ raffleGroup.total_count }} Ticket(s)</p>
                    </div>
                  </div>
                  
                  <!-- Mobile: Status & Actions stacked -->
                  <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
                    <!-- Ticket Status -->
                    <div class="flex items-center justify-center sm:justify-start space-x-4 text-sm sm:text-base">
                      <div class="flex items-center space-x-2 bg-gold-500/20 px-3 py-1 rounded-full border border-gold-400/30">
                        <font-awesome-icon :icon="['fas', 'envelope']" class="text-xs text-gold-300" />
                        <span class="text-gold-200 font-semibold">{{ raffleGroup.unopened_count }}</span>
                      </div>
                      <div class="flex items-center space-x-2 bg-emerald-500/20 px-3 py-1 rounded-full border border-emerald-400/30">
                        <font-awesome-icon :icon="['fas', 'envelope-open']" class="text-xs text-emerald-300" />
                        <span class="text-emerald-200 font-semibold">{{ raffleGroup.opened_count }}</span>
                      </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2 sm:space-x-3">
                      <!-- Tickets öffnen Button -->
                      <button 
                        v-if="raffleGroup.unopened_count > 0"
                        @click="openTicketsForRaffle(raffleGroup)"
                        class="flex items-center space-x-2 px-3 sm:px-4 py-2 bg-gold-gradient text-navy-900 text-sm font-bold rounded-lg hover:shadow-lg hover:shadow-gold-500/25 transition-all duration-300 transform hover:scale-105"
                      >
                        <font-awesome-icon :icon="['fas', 'gift']" class="text-xs" />
                        <span class="hidden sm:inline">Tickets öffnen</span>
                        <span class="sm:hidden">Öffnen</span>
                      </button>
                      
                      <!-- Aufklapp Button -->
                      <button 
                        @click="toggleRaffle(raffleGroup.raffle.id)"
                        class="flex items-center gap-1 sm:gap-2 px-3 py-2 text-sm font-bold text-white bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-300 border border-white/20 hover:border-white/30"
                        :title="isRaffleExpanded(raffleGroup.raffle.id) ? 'Details ausblenden' : 'Details anzeigen'"
                      >
                        <span class="text-xs sm:text-sm">
                          {{ isRaffleExpanded(raffleGroup.raffle.id) ? 'Weniger' : 'Details' }}
                        </span>
                        <font-awesome-icon 
                          :icon="['fas', isRaffleExpanded(raffleGroup.raffle.id) ? 'chevron-up' : 'chevron-down']"
                          class="transition-transform duration-200 text-xs"
                        />
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Prize Groups Grid -->
              <div 
                v-if="raffleGroup.prize_groups && raffleGroup.prize_groups.length > 0 && isRaffleExpanded(raffleGroup.raffle.id)" 
                class="px-4 sm:px-6 py-4 sm:py-6 bg-gradient-to-r from-gold-50/80 to-amber-50/80 border-t border-gold-200/50"
              >
                <h4 class="text-base sm:text-lg font-bold text-gold-800 mb-4 flex items-center">
                  <div class="p-2 bg-gold-gradient rounded-lg mr-3">
                    <font-awesome-icon :icon="['fas', 'trophy']" class="text-navy-900 text-sm" />
                  </div>
                  <span>Gewonnene Preise ({{ raffleGroup.prize_groups.length }})</span>
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4">
                  <PrizeGroupCard 
                    v-for="(prizeGroup, index) in raffleGroup.prize_groups"
                    :key="index"
                    :prize-group="prizeGroup" 
                    :pull-zone="bunny.pull_zone || ''"
                  />
                </div>
              </div>

              <!-- Tickets Grid -->
              <div v-if="isRaffleExpanded(raffleGroup.raffle.id)" 
                   class="p-4 sm:p-6 bg-gradient-to-b from-navy-50/50 to-white border-t border-navy-100"
              >
                <h4 class="text-base sm:text-lg font-bold text-navy-800 mb-4 flex items-center">
                  <div class="p-2 bg-navy-gradient rounded-lg mr-3">
                    <font-awesome-icon :icon="['fas', 'ticket']" class="text-white text-sm" />
                  </div>
                  <span>Alle Tickets ({{ raffleGroup.tickets.length }})</span>
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 xl:grid-cols-10 gap-3 sm:gap-4">
                  <div 
                    v-for="ticket in raffleGroup.tickets" 
                    :key="ticket.id"
                    class="group aspect-square border-2 rounded-xl flex flex-col items-center justify-center p-2 sm:p-3 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1"
                    :class="ticket.is_opened 
                      ? (ticket.outcome?.type === 'prize' 
                          ? 'border-gold-300 bg-gradient-to-br from-gold-50 to-amber-50 text-gold-700 hover:shadow-gold-200' 
                          : 'border-navy-200 bg-gradient-to-br from-navy-50 to-slate-50 text-navy-600 hover:shadow-navy-200'
                        )
                      : 'border-gold-400 bg-gradient-to-br from-gold-100 to-amber-100 text-gold-800 hover:border-gold-500 cursor-pointer hover:shadow-gold-300 animate-pulse'"
                  >
                    <!-- Produktbild für gewonnene Tickets -->
                    <div v-if="ticket.is_opened && ticket.outcome?.type === 'prize' && ticket.outcome?.product?.image_url" 
                         class="w-full flex-1 mb-2 flex items-center justify-center rounded-lg overflow-hidden">
                      <img 
                        :src="ticket.outcome.product.image_url" 
                        :alt="ticket.outcome.product.name"
                        class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-300"
                      >
                    </div>
                    <!-- Icons für andere Tickets -->
                    <div v-else class="text-lg sm:text-xl mb-1 sm:mb-2 p-2 rounded-full bg-white/50 group-hover:bg-white/70 transition-colors">
                      <font-awesome-icon v-if="!ticket.is_opened" :icon="['fas', 'envelope']" class="text-gold-600 group-hover:animate-bounce" />
                      <font-awesome-icon v-else-if="ticket.outcome?.type === 'prize'" :icon="['fas', 'trophy']" class="text-gold-600" />
                      <font-awesome-icon v-else :icon="['fas', 'times-circle']" class="text-navy-400" />
                    </div>
                    
                    <div class="text-xs sm:text-sm font-bold text-center bg-white/70 rounded-full px-2 py-1">
                      #{{ ticket.serial }}
                    </div>
                    <div v-if="ticket.is_opened && ticket.outcome?.type === 'prize' && ticket.outcome?.product" 
                         class="text-xs text-center mt-1 sm:mt-2 line-clamp-2 font-semibold">
                      {{ ticket.outcome.product.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="ticketsByRaffle.length === 0" class="text-center py-16 sm:py-20">
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl sm:rounded-3xl shadow-xl p-8 sm:p-12 border border-navy-100 max-w-md mx-auto">
              <div class="text-6xl sm:text-7xl text-navy-300 mb-6 animate-bounce">
                <font-awesome-icon :icon="['fas', 'ticket']" />
              </div>
              <h3 class="text-xl sm:text-2xl font-black text-navy-900 mb-4">Keine Tickets vorhanden</h3>
              <p class="text-navy-600 mb-8 text-sm sm:text-base leading-relaxed">Du hast noch keine Tickets gekauft. Nimm an Raffles teil, um Tickets zu erhalten!</p>
              <a href="/raffles" class="inline-flex items-center px-6 sm:px-8 py-3 sm:py-4 border border-transparent text-sm sm:text-base font-bold rounded-xl text-navy-900 bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 transition-all duration-300 transform hover:scale-105">
                <font-awesome-icon :icon="['fas', 'dice']" class="mr-2 sm:mr-3" />
                <span>Zu den Raffles</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Ticket Opening Modal -->
    <TicketOpeningModal 
      :show="showOpeningModal" 
      :tickets="selectedRaffleTickets"
      @close="closeOpeningModal"
    />
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import TicketOpeningModal from '@/Components/TicketOpeningModal.vue'
import PrizeGroupCard from '@/Components/PrizeGroupCard.vue'
import { ref, defineProps, reactive } from 'vue'
import { Link } from '@inertiajs/vue3'

const route = window.route;

const props = defineProps({
  ticketsByRaffle: Array,
  stats: Object,
  bunny: { type: Object, default: () => ({}) }
})

const showOpeningModal = ref(false)
const selectedRaffleTickets = ref([])

// Reaktiver State für aufgeklappte Raffles
const expandedRaffles = reactive({})

const openTicketsForRaffle = (raffleGroup) => {
  const unopenedTickets = raffleGroup.tickets.filter(ticket => !ticket.is_opened)
  if (unopenedTickets.length > 0) {
    selectedRaffleTickets.value = unopenedTickets
    showOpeningModal.value = true
  }
}

const toggleRaffle = (raffleId) => {
  expandedRaffles[raffleId] = !expandedRaffles[raffleId]
}

const isRaffleExpanded = (raffleId) => {
  return expandedRaffles[raffleId] ?? false // Standardmäßig zugeklappt
}

const closeOpeningModal = () => {
  showOpeningModal.value = false
  selectedRaffleTickets.value = []
  // Reload page to update ticket states
  window.location.reload()
}
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
