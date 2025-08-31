<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { getImageUrl as buildImageUrl } from '@/utils/cdn';

const page = usePage();
const products = computed(()=> page.props.products || []);
const bunny = computed(()=> page.props.bunny || {});
const bunnyPullZone = computed(()=> bunny.value?.pull_zone || null);

const getImageUrl = (p) => buildImageUrl(p, bunnyPullZone.value);

const sort = ref('name');
const dir = ref('asc');

const visible = computed(()=> {
  const arr = [...products.value];
  arr.sort((a,b)=>{
    const mult = dir.value === 'asc' ? 1 : -1;
    if (sort.value === 'name') return a.name.localeCompare(b.name) * mult;
    return ((a[sort.value]||0) - (b[sort.value]||0)) * mult;
  });
  return arr;
});
</script>

<template>
  <Head title="Produktübersicht" />
  <AdminLayout title="Produktübersicht">
    <template #toolbar>
      <Link href="/admin/products" class="text-sm text-gray-600 hover:text-gray-800">← Zurück</Link>
    </template>

    <div class="bg-white rounded-xl shadow border border-gray-100">
      <div class="px-4 py-3 border-b flex items-center justify-between">
        <div>
          <h3 class="text-base font-semibold">Gesamtübersicht Produkte</h3>
          <p class="text-xs text-gray-500">Summe in Raffles, vergeben an Nutzer und verbleibend über alle Kategorien.</p>
        </div>
        <div class="flex items-center gap-2">
          <label class="text-xs text-gray-500">Sortieren:</label>
          <select v-model="sort" class="h-8 rounded border-gray-300 text-sm">
            <option value="name">Name</option>
            <option value="total_in_raffles">In Raffles (Total)</option>
            <option value="awarded_in_raffles">Vergeben</option>
            <option value="remaining_in_raffles">Verbleibend</option>
            <option value="owned_by_users">Im Besitz (User)</option>
            <option value="reserved_for_shipping">Reserviert</option>
            <option value="shipped_to_users">Versendet</option>
            <option value="recovered_total">Wieder frei</option>
          </select>
          <button @click="dir = dir==='asc'?'desc':'asc'" class="h-8 px-2 rounded border text-xs">
            {{ dir==='asc' ? '↑' : '↓' }}
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead class="bg-gray-50 text-[11px] uppercase text-gray-500">
            <tr>
              <th class="px-3 py-2 text-left">Bild</th>
              <th class="px-3 py-2 text-left">Produkt</th>
              <th class="px-3 py-2 text-right">In Raffles</th>
              <th class="px-3 py-2 text-right">Vergeben</th>
              <th class="px-3 py-2 text-right">Verbleibend</th>
              <th class="px-3 py-2 text-right">Im Besitz</th>
              <th class="px-3 py-2 text-right">Reserviert</th>
              <th class="px-3 py-2 text-right">Versendet</th>
              <th class="px-3 py-2 text-right">Wieder frei</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="p in visible" :key="p.id" class="hover:bg-gray-50">
              <td class="px-3 py-2">
                <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden flex items-center justify-center">
                  <img v-if="p.thumbnail_path" :src="getImageUrl(p.thumbnail_path)" :alt="p.name" class="w-full h-full object-cover" />
                  <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              </td>
              <td class="px-3 py-2">
                <div class="text-sm font-medium text-gray-900">{{ p.name }}</div>
                <div class="text-[11px] text-gray-500">SKU: {{ p.sku }}</div>
              </td>
              <td class="px-3 py-2 text-right text-sm">{{ p.total_in_raffles }}</td>
              <td class="px-3 py-2 text-right text-sm">{{ p.awarded_in_raffles }}</td>
              <td class="px-3 py-2 text-right text-sm font-medium" :class="p.remaining_in_raffles>0 ? 'text-emerald-600' : 'text-gray-700'">{{ p.remaining_in_raffles }}</td>
              <td class="px-3 py-2 text-right text-sm">{{ p.owned_by_users }}</td>
              <td class="px-3 py-2 text-right text-sm">{{ p.reserved_for_shipping }}</td>
              <td class="px-3 py-2 text-right text-sm">{{ p.shipped_to_users }}</td>
              <td class="px-3 py-2 text-right text-sm font-medium text-emerald-700">{{ p.recovered_total }}</td>
            </tr>
            <tr v-if="!visible.length">
              <td colspan="9" class="px-3 py-8 text-center text-sm text-gray-500">Keine Produkte gefunden</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
