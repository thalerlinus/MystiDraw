<script setup>
import { Head, usePage, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const page = usePage();
const kpis = computed(()=> page.props.kpis || {});
const activeRaffles = computed(()=> page.props.activeRaffles || []);
const recentOrders = computed(()=> page.props.recentOrders || []);
const lowInventory = computed(()=> page.props.lowInventory || []);
const openShipments = computed(()=> page.props.openShipments || []);
</script>

<template>
  <Head title="Admin Dashboard" />
  <AdminLayout title="Dashboard">
    <template #toolbar>
      <!-- Live Status Indicator -->
      <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg border border-green-200">
        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        <span class="text-sm font-medium">Live</span>
      </div>
      <!-- Refresh Button -->
      <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" @click="$inertia.reload()">
        <font-awesome-icon :icon="['fas','refresh']" class="w-4 h-4" />
      </button>
    </template>

    <!-- Main Dashboard Content -->
    <div class="space-y-8">
      
      <!-- Welcome Section -->
      <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-indigo-700 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="relative z-10">
          <h2 class="text-3xl font-bold mb-2">Willkommen zurück! 👋</h2>
          <p class="text-indigo-100 text-lg">Hier ist eine Übersicht über Ihren Shop</p>
        </div>
        <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="absolute -left-8 -bottom-8 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
      </div>

      <!-- KPI Cards - Flex layout to ensure side by side -->
      <div class="flex flex-wrap gap-4">
        <!-- Revenue Card -->
        <div class="flex-1 min-w-0 group relative overflow-hidden rounded-xl bg-white p-3 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
          <div class="text-center">
            <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 inline-flex mb-2">
              <font-awesome-icon :icon="['fas','coins']" class="h-4 w-4" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Gesamtumsatz</p>
            <p class="text-sm font-bold tracking-tight text-gray-900">{{ new Intl.NumberFormat('de-DE', {style:'currency', currency:'EUR', minimumFractionDigits: 0}).format(kpis.totalRevenue || 0) }}</p>
            <p class="text-xs text-gray-500">Heute: {{ new Intl.NumberFormat('de-DE', {style:'currency', currency:'EUR', minimumFractionDigits: 0}).format(kpis.revenueToday || 0) }}</p>
          </div>
        </div>

        <!-- Tickets Card -->
        <div class="flex-1 min-w-0 group relative overflow-hidden rounded-xl bg-white p-3 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
          <div class="text-center">
            <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600 inline-flex mb-2">
              <font-awesome-icon :icon="['fas','ticket']" class="h-4 w-4" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Verkaufte Lose</p>
            <p class="text-sm font-bold tracking-tight text-gray-900">{{ kpis.totalTickets || 0 }}</p>
            <p class="text-xs text-gray-500">Heute: {{ kpis.ticketsToday || 0 }}</p>
          </div>
        </div>

        <!-- Active Raffles Card -->
        <div class="flex-1 min-w-0 group relative overflow-hidden rounded-xl bg-white p-3 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
          <div class="text-center">
            <div class="rounded-lg bg-purple-50 p-2 text-purple-600 inline-flex mb-2">
              <font-awesome-icon :icon="['fas','shuffle']" class="h-4 w-4" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Aktive Raffles</p>
            <p class="text-sm font-bold tracking-tight text-gray-900">{{ kpis.activeRaffles || 0 }}</p>
            <p class="text-xs text-gray-500">Laufend</p>
          </div>
        </div>

        <!-- Shipments Card -->
        <div class="flex-1 min-w-0 group relative overflow-hidden rounded-xl bg-white p-3 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
          <div class="text-center">
            <div class="rounded-lg bg-orange-50 p-2 text-orange-600 inline-flex mb-2">
              <font-awesome-icon :icon="['fas','truck']" class="h-4 w-4" />
            </div>
            <p class="text-xs font-medium text-gray-600 mb-1">Offene Sendungen</p>
            <p class="text-sm font-bold tracking-tight text-gray-900">{{ openShipments.length || 0 }}</p>
            <p class="text-xs text-gray-500">Ausstehend</p>
          </div>
        </div>
      </div>

      <!-- Active Raffles Section - Compact -->
      <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                <font-awesome-icon :icon="['fas','shuffle']" class="w-3 h-3 text-indigo-600" />
              </div>
              <h3 class="text-base font-semibold text-gray-900">Aktive Raffles</h3>
            </div>
            <Link href="/admin/raffles" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
              Alle →
            </Link>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50/50 border-b border-gray-100">
              <tr>
                <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Ende</th>
                <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Tickets</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="r in activeRaffles.slice(0,3)" :key="r.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-2 font-medium text-gray-900 text-sm">{{ r.name }}</td>
                <td class="px-4 py-2 text-gray-600 text-xs">{{ r.starts_at }}</td>
                <td class="px-4 py-2 text-gray-600 text-xs">{{ r.ends_at }}</td>
                <td class="px-4 py-2">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ r.tickets_count }}
                  </span>
                </td>
              </tr>
              <tr v-if="!activeRaffles.length">
                <td colspan="4" class="px-4 py-8 text-center">
                  <div class="flex flex-col items-center gap-2">
                    <font-awesome-icon :icon="['fas','shuffle']" class="w-6 h-6 text-gray-300" />
                    <p class="text-gray-500 text-sm">Keine aktiven Raffles</p>
                  </div>
                </td>
              </tr>
              <tr v-if="activeRaffles.length > 3">
                <td colspan="4" class="px-4 py-2 text-center border-t border-gray-100">
                  <Link href="/admin/raffles" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    +{{ activeRaffles.length - 3 }} weitere Raffles anzeigen
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Data Grid Section - Wider cards -->
      <div class="space-y-6">
        
        <!-- Recent Orders - Full width -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
          <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                  <font-awesome-icon :icon="['fas','shopping-cart']" class="w-3 h-3 text-blue-600" />
                </div>
                <h3 class="text-base font-semibold text-gray-900">Letzte Bestellungen</h3>
              </div>
              <Link href="/admin/orders" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                Alle →
              </Link>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                  <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                  <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  <th class="text-left px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Bezahlt</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="o in recentOrders.slice(0,4)" :key="o.id" class="hover:bg-gray-50/50 transition-colors">
                  <td class="px-4 py-2 font-medium text-gray-900 text-sm">#{{ o.id }}</td>
                  <td class="px-4 py-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ o.status }}
                    </span>
                  </td>
                  <td class="px-4 py-2 font-medium text-gray-900 text-sm">{{ new Intl.NumberFormat('de-DE',{style:'currency',currency:o.currency||'EUR'}).format(o.total) }}</td>
                  <td class="px-4 py-2 text-gray-600 text-xs">{{ o.paid_at || '-' }}</td>
                </tr>
                <tr v-if="!recentOrders.length">
                  <td colspan="4" class="px-4 py-8 text-center">
                    <div class="flex flex-col items-center gap-2">
                      <font-awesome-icon :icon="['fas','shopping-cart']" class="w-6 h-6 text-gray-300" />
                      <p class="text-gray-500 text-sm">Keine Bestellungen</p>
                    </div>
                  </td>
                </tr>
                <tr v-if="recentOrders.length > 4">
                  <td colspan="4" class="px-4 py-2 text-center border-t border-gray-100">
                    <Link href="/admin/orders" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                      +{{ recentOrders.length - 4 }} weitere Bestellungen anzeigen
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Two columns for better card width -->
        <div class="grid gap-6 lg:grid-cols-2">
          
          <!-- Left Column -->
          <div class="space-y-6">
            <!-- Low Inventory -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
              <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-yellow-100 rounded-lg flex items-center justify-center">
                      <font-awesome-icon :icon="['fas','exclamation-triangle']" class="w-4 h-4 text-yellow-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Niedriger Bestand</h3>
                  </div>
                  <Link href="/admin/inventory" class="text-sm text-yellow-600 hover:text-yellow-800 font-medium">
                    Alle →
                  </Link>
                </div>
              </div>
              <div class="p-5">
                <div class="space-y-3">
                  <div v-for="p in lowInventory.slice(0,4)" :key="p.id" class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="font-medium text-gray-900">{{ p.name }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                      {{ p.remaining ?? '—' }}
                    </span>
                  </div>
                  <div v-if="!lowInventory.length" class="text-center py-8">
                    <font-awesome-icon :icon="['fas','check-circle']" class="w-8 h-8 text-green-400 mb-2" />
                    <p class="text-gray-500">Alle Bestände sind ausreichend</p>
                  </div>
                  <div v-if="lowInventory.length > 4" class="text-center pt-3 border-t border-yellow-100">
                    <Link href="/admin/inventory" class="text-sm text-yellow-600 hover:text-yellow-800 font-medium">
                      +{{ lowInventory.length - 4 }} weitere Produkte anzeigen
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Open Shipments -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
              <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                      <font-awesome-icon :icon="['fas','truck']" class="w-4 h-4 text-orange-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Offene Sendungen</h3>
                  </div>
                  <Link href="/admin/shipments" class="text-sm text-orange-600 hover:text-orange-800 font-medium">
                    Alle →
                  </Link>
                </div>
              </div>
              <div class="p-5">
                <div class="space-y-3">
                  <div v-for="s in openShipments.slice(0,5)" :key="s.id" class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <div>
                      <p class="font-medium text-gray-900">#{{ s.id }}</p>
                      <p class="text-sm text-gray-600">Bestellung #{{ s.order_id }}</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                      {{ s.status }}
                    </span>
                  </div>
                  <div v-if="!openShipments.length" class="text-center py-8">
                    <font-awesome-icon :icon="['fas','check-circle']" class="w-8 h-8 text-green-400 mb-2" />
                    <p class="text-gray-500">Keine offenen Sendungen</p>
                  </div>
                  <div v-if="openShipments.length > 5" class="text-center pt-3 border-t border-orange-100">
                    <Link href="/admin/shipments" class="text-sm text-orange-600 hover:text-orange-800 font-medium">
                      +{{ openShipments.length - 5 }} weitere Sendungen anzeigen
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Quick Actions -->
          <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header aligned with other cards -->
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
              <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                <font-awesome-icon :icon="['fas','bolt']" class="w-4 h-4 text-indigo-600" />
              </div>
              <h3 class="text-lg font-semibold text-gray-900">Schnellaktionen</h3>
            </div>
            <!-- Body -->
            <div class="p-5 grid grid-cols-2 gap-4 auto-rows-fr">
              <!-- Primary: Raffle erstellen -->
              <Link href="/admin/raffles/create" class="group flex items-center gap-4 p-4 rounded-xl bg-indigo-50 border border-indigo-100 text-gray-900 hover:bg-white hover:border-indigo-200 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 h-full">
                <div class="w-11 h-11 bg-white border border-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <font-awesome-icon :icon="['fas','plus']" class="w-5 h-5 text-indigo-600" />
                </div>
                <div class="space-y-0.5">
                  <p class="font-semibold leading-tight text-gray-900">Raffle erstellen</p>
                  <p class="text-xs text-gray-600">Neue Verlosung starten</p>
                </div>
              </Link>
              <!-- Secondary: Produkt anlegen -->
              <Link href="/admin/products/create" class="group flex items-center gap-4 p-4 rounded-xl bg-gray-100 border border-gray-200 text-gray-900 hover:bg-white hover:border-gray-300 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 h-full">
                <div class="w-11 h-11 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                  <font-awesome-icon :icon="['fas','box-open']" class="w-5 h-5 text-gray-700" />
                </div>
                <div class="space-y-0.5">
                  <p class="font-semibold leading-tight text-gray-900">Produkt anlegen</p>
                  <p class="text-xs text-gray-600">Neues Item hinzufügen</p>
                </div>
              </Link>
              <!-- Neutral: Versandlabels -->
              <Link href="/admin/shipments" class="group flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 hover:bg-white hover:border-gray-300 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 h-full">
                <div class="w-11 h-11 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                  <font-awesome-icon :icon="['fas','truck']" class="w-5 h-5 text-gray-600" />
                </div>
                <div class="space-y-0.5">
                  <p class="font-semibold leading-tight">Versandlabels</p>
                  <p class="text-xs text-gray-500">Labels erstellen</p>
                </div>
              </Link>
              <!-- Neutral: Analytics -->
              <Link href="/admin/analytics" class="group flex items-center gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200 text-gray-900 hover:bg-white hover:border-gray-300 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 h-full">
                <div class="w-11 h-11 bg-white border border-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                  <font-awesome-icon :icon="['fas','chart-line']" class="w-5 h-5 text-gray-600" />
                </div>
                <div class="space-y-0.5">
                  <p class="font-semibold leading-tight">Analytics</p>
                  <p class="text-xs text-gray-500">Berichte anzeigen</p>
                </div>
              </Link>
            </div>
          </div>

        </div>
      </div>

    </div>
  </AdminLayout>
</template>
