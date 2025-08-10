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
              <i class="fa fa-ticket text-2xl text-blue-500"></i>
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
              <i class="fa fa-envelope text-2xl text-orange-500"></i>
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
              <i class="fa fa-envelope-open text-2xl text-green-500"></i>
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
              <i class="fa fa-trophy text-2xl text-yellow-500"></i>
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
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                    <img 
                      v-if="raffleGroup.raffle.image_url" 
                      :src="raffleGroup.raffle.image_url" 
                      :alt="raffleGroup.raffle.name" 
                      class="w-full h-full object-cover"
                    >
                    <i v-else class="fa fa-gift text-gray-400"></i>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ raffleGroup.raffle.name }}</h3>
                    <p class="text-sm text-gray-500">{{ raffleGroup.total_count }} Ticket(s)</p>
                  </div>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="text-sm text-gray-500">
                    <span class="text-orange-600 font-medium">{{ raffleGroup.unopened_count }} ungeöffnet</span>
                    <span class="mx-2">•</span>
                    <span class="text-green-600 font-medium">{{ raffleGroup.opened_count }} geöffnet</span>
                  </div>
                  <button 
                    v-if="raffleGroup.unopened_count > 0"
                    @click="openTicketsForRaffle(raffleGroup)"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    Tickets öffnen
                  </button>
                </div>
              </div>
            </div>

            <!-- Tickets Grid -->
            <div class="p-6">
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
import { ref, defineProps } from 'vue'

const props = defineProps({
  ticketsByRaffle: Array,
  stats: Object,
})

const showOpeningModal = ref(false)
const selectedRaffleTickets = ref([])

const openTicketsForRaffle = (raffleGroup) => {
  const unopenedTickets = raffleGroup.tickets.filter(ticket => !ticket.is_opened)
  if (unopenedTickets.length > 0) {
    selectedRaffleTickets.value = unopenedTickets
    showOpeningModal.value = true
  }
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
