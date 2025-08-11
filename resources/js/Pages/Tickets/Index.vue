<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Meine Tickets
      </h2>
    </template>

    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <font-awesome-icon :icon="['fas', 'ticket']" class="text-2xl text-blue-500" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Gesamte Tickets</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total_tickets }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <font-awesome-icon :icon="['fas', 'envelope']" class="text-2xl text-orange-500" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Ungeöffnet</p>
              <p class="text-2xl font-bold text-orange-600">{{ stats.unopened_tickets }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <font-awesome-icon :icon="['fas', 'envelope-open']" class="text-2xl text-green-500" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Geöffnet</p>
              <p class="text-2xl font-bold text-green-600">{{ stats.opened_tickets }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <font-awesome-icon :icon="['fas', 'trophy']" class="text-2xl text-yellow-500" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Preise gewonnen</p>
              <p class="text-2xl font-bold text-yellow-600">{{ stats.prizes_won }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tickets by Raffle -->
      <div class="space-y-8">
        <div v-for="raffleGroup in ticketsByRaffle" :key="raffleGroup.raffle.id">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Raffle Header -->
            <div class="bg-gray-50 px-6 py-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <!-- Klickbares Raffle Bild -->
                  <Link 
                    :href="raffleGroup.raffle.slug ? route('raffles.show', raffleGroup.raffle.slug) : '#'"
                    class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden hover:ring-2 hover:ring-blue-300 transition-all cursor-pointer"
                  >
                    <img 
                      v-if="raffleGroup.raffle.image_url" 
                      :src="raffleGroup.raffle.image_url" 
                      :alt="raffleGroup.raffle.name" 
                      class="w-full h-full object-cover"
                    >
                    <font-awesome-icon v-else :icon="['fas', 'gift']" class="text-gray-400" />
                  </Link>
                  <div>
                    <Link 
                      :href="raffleGroup.raffle.slug ? route('raffles.show', raffleGroup.raffle.slug) : '#'"
                      class="group cursor-pointer"
                    >
                      <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">
                        {{ raffleGroup.raffle.name }}
                        <font-awesome-icon :icon="['fas', 'external-link-alt']" class="ml-1 text-xs text-gray-400 group-hover:text-blue-400" />
                      </h3>
                    </Link>
                    <p class="text-sm text-gray-500">{{ raffleGroup.total_count }} Ticket(s)</p>
                  </div>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="text-sm text-gray-500">
                    <span class="text-orange-600 font-medium">{{ raffleGroup.unopened_count }} ungeöffnet</span>
                    <span class="mx-2">•</span>
                    <span class="text-green-600 font-medium">{{ raffleGroup.opened_count }} geöffnet</span>
                  </div>
                  <div class="flex items-center space-x-3">
                    <!-- Tickets öffnen Button -->
                    <button 
                      v-if="raffleGroup.unopened_count > 0"
                      @click="openTicketsForRaffle(raffleGroup)"
                      class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                    >
                      Tickets öffnen
                    </button>
                    <!-- Aufklapp Button - immer sichtbar -->
                    <button 
                      @click="toggleRaffle(raffleGroup.raffle.id)"
                      class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-yellow-800 bg-yellow-100 hover:bg-yellow-200 hover:text-yellow-900 rounded-lg transition-colors border border-yellow-300 shadow-sm"
                      :title="isRaffleExpanded(raffleGroup.raffle.id) ? 'Details ausblenden' : 'Details anzeigen'"
                    >
                      <span class="text-xs">
                        {{ isRaffleExpanded(raffleGroup.raffle.id) ? 'Ausblenden' : 'Details' }}
                      </span>
                      <font-awesome-icon 
                        :icon="['fas', isRaffleExpanded(raffleGroup.raffle.id) ? 'chevron-up' : 'chevron-down']"
                        class="transition-transform duration-200"
                      />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Prize Groups Grid -->
            <div 
              v-if="raffleGroup.prize_groups && raffleGroup.prize_groups.length > 0 && isRaffleExpanded(raffleGroup.raffle.id)" 
              class="px-6 py-4 border-t border-gray-200"
            >
              <h4 class="text-sm font-medium text-gray-700 mb-4 flex items-center">
                <i class="fa fa-trophy text-yellow-500 mr-2"></i>
                Gewonnene Preise ({{ raffleGroup.prize_groups.length }})
              </h4>
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
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
                 class="p-6 border-t border-gray-200"
            >
              <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4">
                <div 
                  v-for="ticket in raffleGroup.tickets" 
                  :key="ticket.id"
                  class="aspect-square border-2 rounded-lg flex flex-col items-center justify-center p-2 transition-all hover:shadow-md"
                  :class="ticket.is_opened 
                    ? (ticket.outcome?.type === 'prize' 
                        ? 'border-yellow-300 bg-yellow-50 text-yellow-700' 
                        : 'border-gray-300 bg-gray-50 text-gray-600'
                      )
                    : 'border-orange-300 bg-orange-50 text-orange-700 hover:border-orange-400 cursor-pointer'"
                >
                  <!-- Produktbild für gewonnene Tickets -->
                  <div v-if="ticket.is_opened && ticket.outcome?.type === 'prize' && ticket.outcome?.product?.image_url" 
                       class="w-full flex-1 mb-2 flex items-center justify-center">
                    <img 
                      :src="ticket.outcome.product.image_url" 
                      :alt="ticket.outcome.product.name"
                      class="max-w-full max-h-full object-contain rounded"
                    >
                  </div>
                  <!-- Icons für andere Tickets -->
                  <div v-else class="text-lg mb-1">
                    <i v-if="!ticket.is_opened" class="fa fa-envelope text-orange-500"></i>
                    <i v-else-if="ticket.outcome?.type === 'prize'" class="fa fa-trophy text-yellow-500"></i>
                    <i v-else class="fa fa-times-circle text-gray-400"></i>
                  </div>
                  
                  <div class="text-xs font-mono text-center">
                    #{{ ticket.serial }}
                  </div>
                  <div v-if="ticket.is_opened && ticket.outcome?.type === 'prize' && ticket.outcome?.product" 
                       class="text-xs text-center mt-1 line-clamp-2">
                    {{ ticket.outcome.product.name }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="ticketsByRaffle.length === 0" class="text-center py-12">
          <div class="text-6xl text-gray-400 mb-4">
            <i class="fa fa-ticket"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Keine Tickets vorhanden</h3>
          <p class="text-gray-500 mb-6">Du hast noch keine Tickets gekauft. Nimm an Raffles teil, um Tickets zu erhalten!</p>
          <a href="/raffles" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
            <i class="fa fa-dice mr-2"></i>
            Zu den Raffles
          </a>
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
