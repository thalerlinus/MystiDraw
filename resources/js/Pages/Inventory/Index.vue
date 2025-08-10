<template>
  <MainLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Mein Inventar
      </h2>
    </template>

    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Statistics -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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
              <i class="fa fa-eur text-2xl text-green-500"></i>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">Gesamtwert</p>
              <p class="text-2xl font-bold text-gray-900">{{ formatPrice(stats.total_value) }}</p>
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
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
              v-for="item in inventory.assigned" 
              :key="item.id"
              class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
            >
              <div class="aspect-square bg-gray-100 flex items-center justify-center">
                <img 
                  v-if="item.product.image_url" 
                  :src="item.product.image_url" 
                  :alt="item.product.name" 
                  class="w-full h-full object-cover"
                >
                <div v-else class="text-gray-400 text-3xl">
                  <i class="fa fa-gift"></i>
                </div>
              </div>
              
              <div class="p-4 space-y-3">
                <div>
                  <h4 class="font-medium text-gray-900 line-clamp-2">{{ item.product.name }}</h4>
                  <p class="text-sm text-gray-500 mt-1">{{ item.raffle_name }}</p>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-500 flex items-center gap-1">
                    <i class="fa fa-ticket"></i>
                    #{{ item.ticket_serial }}
                  </span>
                  <span class="font-semibold text-green-600">
                    {{ formatPrice(item.product.value) }}
                  </span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="px-2 py-1 rounded-full text-xs font-bold text-white" 
                        :class="`tier-${item.tier.toLowerCase()}`">
                    Tier {{ item.tier.toUpperCase() }}
                  </span>
                  <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">
                    Wartend
                  </span>
                </div>
                
                <p class="text-xs text-gray-500">
                  Gewonnen am {{ formatDate(item.won_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipped Items -->
        <div v-if="inventory.shipped.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fa fa-shipping-fast text-blue-500"></i>
            Versendet ({{ inventory.shipped.length }})
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
              v-for="item in inventory.shipped" 
              :key="item.id"
              class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
            >
              <div class="aspect-square bg-gray-100 flex items-center justify-center">
                <img 
                  v-if="item.product.image_url" 
                  :src="item.product.image_url" 
                  :alt="item.product.name" 
                  class="w-full h-full object-cover"
                >
                <div v-else class="text-gray-400 text-3xl">
                  <i class="fa fa-gift"></i>
                </div>
              </div>
              
              <div class="p-4 space-y-3">
                <div>
                  <h4 class="font-medium text-gray-900 line-clamp-2">{{ item.product.name }}</h4>
                  <p class="text-sm text-gray-500 mt-1">{{ item.raffle_name }}</p>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-500 flex items-center gap-1">
                    <i class="fa fa-ticket"></i>
                    #{{ item.ticket_serial }}
                  </span>
                  <span class="font-semibold text-green-600">
                    {{ formatPrice(item.product.value) }}
                  </span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="px-2 py-1 rounded-full text-xs font-bold text-white" 
                        :class="`tier-${item.tier.toLowerCase()}`">
                    Tier {{ item.tier.toUpperCase() }}
                  </span>
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                    Versendet
                  </span>
                </div>
                
                <p class="text-xs text-gray-500">
                  Gewonnen am {{ formatDate(item.won_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Delivered Items -->
        <div v-if="inventory.delivered.length > 0">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fa fa-check-circle text-green-500"></i>
            Zugestellt ({{ inventory.delivered.length }})
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
              v-for="item in inventory.delivered" 
              :key="item.id"
              class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow opacity-75"
            >
              <div class="aspect-square bg-gray-100 flex items-center justify-center">
                <img 
                  v-if="item.product.image_url" 
                  :src="item.product.image_url" 
                  :alt="item.product.name" 
                  class="w-full h-full object-cover"
                >
                <div v-else class="text-gray-400 text-3xl">
                  <i class="fa fa-gift"></i>
                </div>
              </div>
              
              <div class="p-4 space-y-3">
                <div>
                  <h4 class="font-medium text-gray-900 line-clamp-2">{{ item.product.name }}</h4>
                  <p class="text-sm text-gray-500 mt-1">{{ item.raffle_name }}</p>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-500 flex items-center gap-1">
                    <i class="fa fa-ticket"></i>
                    #{{ item.ticket_serial }}
                  </span>
                  <span class="font-semibold text-green-600">
                    {{ formatPrice(item.product.value) }}
                  </span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="px-2 py-1 rounded-full text-xs font-bold text-white" 
                        :class="`tier-${item.tier.toLowerCase()}`">
                    Tier {{ item.tier.toUpperCase() }}
                  </span>
                  <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                    Zugestellt
                  </span>
                </div>
                
                <p class="text-xs text-gray-500">
                  Gewonnen am {{ formatDate(item.won_at) }}
                </p>
              </div>
            </div>
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
import { defineProps } from 'vue'

const props = defineProps({
  inventory: Object,
  stats: Object,
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
