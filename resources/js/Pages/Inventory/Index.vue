<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Mein Inventar
      </h2>
    </template>

    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="fa fa-trophy text-2xl text-yellow-500"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Gesamte Gewinne</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total_prizes }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="fa fa-clock text-2xl text-orange-500"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Wartend auf Versand</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.pending_shipment }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <i class="fa fa-shipping-fast text-2xl text-blue-500"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Versendet</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.shipped_items }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Inventory Sections -->
      <div class="space-y-8">
        <!-- Assigned Items (Pending Shipment) -->
        <div v-if="inventory.assigned.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fa fa-clock text-orange-500"></i>
            Wartend auf Versand ({{ inventory.assigned.length }})
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in inventory.assigned"
              :key="index"
              :prize-group="prizeGroup" 
              :pull-zone="bunny.pull_zone || ''"
            />
          </div>
        </div>

        <!-- Shipped Items -->
        <div v-if="inventory.shipped.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fa fa-shipping-fast text-blue-500"></i>
            Versendet ({{ inventory.shipped.length }})
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
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
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fa fa-check-circle text-green-500"></i>
            Zugestellt ({{ inventory.delivered.length }})
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <PrizeGroupCard 
              v-for="(prizeGroup, index) in inventory.delivered"
              :key="index"
              :prize-group="prizeGroup" 
              :pull-zone="bunny.pull_zone || ''"
            />
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="stats.total_prizes === 0" class="text-center py-12">
          <div class="text-6xl text-gray-400 mb-4">
            <i class="fa fa-box-open"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Dein Inventar ist leer</h3>
          <p class="text-gray-500 mb-6">Du hast noch keine Preise gewonnen. Nimm an Raffles teil, um deine Gewinnchancen zu erhöhen!</p>
          <a href="/" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 transition-colors">
            <i class="fa fa-dice mr-2"></i>
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
import { defineProps } from 'vue'

const props = defineProps({
  inventory: Object,
  stats: Object,
  bunny: { type: Object, default: () => ({}) }
})

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
