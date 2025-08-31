<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { getImageUrl as buildImageUrl } from '@/utils/cdn'

const props = defineProps({
  recoveries: Object,
  products: Array,
  filters: Object,
  bunny: Object,
})

const getImg = (path) => buildImageUrl(path, props.bunny?.pull_zone)

const form = ref({
  q: props.filters?.q || '',
  product_id: props.filters?.product_id || '',
  user_id: props.filters?.user_id || '',
})

function applyFilters() {
  router.get(route('admin.recoveries.index'), form.value, { preserveState: true, replace: true })
}

function updateQty(id, qty) {
  router.put(route('admin.recoveries.update', id), { quantity: qty }, { preserveScroll: true })
}

function deleteRecovery(id) {
  if (!confirm('Diesen Recovery-Eintrag wirklich löschen?')) return
  router.delete(route('admin.recoveries.destroy', id), { preserveScroll: true })
}
</script>

<template>
  <Head title="Recoveries" />
  <AdminLayout>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Inventory Recoveries</h1>
    </div>
    <div class="bg-white rounded shadow p-3 mb-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div>
          <label class="block text-xs text-gray-600">Suche</label>
          <input v-model="form.q" type="text" class="w-full border rounded h-9 px-2" @keyup.enter="applyFilters" placeholder="Name oder SKU" />
        </div>
        <div>
          <label class="block text-xs text-gray-600">Produkt</label>
          <select v-model="form.product_id" class="w-full border rounded h-9 px-2">
            <option value="">Alle</option>
            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} ({{ p.sku }})</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600">User ID</label>
          <input v-model="form.user_id" type="number" class="w-full border rounded h-9 px-2" />
        </div>
        <div>
          <button @click="applyFilters" class="h-9 px-4 bg-gray-900 text-white rounded">Filtern</button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="p-2 text-left">ID</th>
            <th class="p-2 text-left">Produkt</th>
            <th class="p-2 text-left">Menge</th>
            <th class="p-2 text-left">User</th>
            <th class="p-2 text-left">Raffle</th>
            <th class="p-2 text-left">Purge Datum</th>
            <th class="p-2 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in recoveries.data" :key="r.id" class="border-t">
            <td class="p-2">{{ r.id }}</td>
            <td class="p-2">
              <div class="flex items-center gap-2">
                <img v-if="r.product?.thumbnail_path" :src="getImg(r.product.thumbnail_path)" class="w-10 h-10 object-cover rounded" />
                <div>
                  <div class="font-medium">{{ r.product?.name }}</div>
                  <div class="text-xs text-gray-500">SKU: {{ r.product?.sku }}</div>
                </div>
              </div>
            </td>
            <td class="p-2">
              <input type="number" min="0" :value="r.quantity" class="w-24 border rounded h-8 px-2"
                     @change="e => updateQty(r.id, parseInt(e.target.value || 0))" />
            </td>
            <td class="p-2">
              <div v-if="r.user">{{ r.user.name }} <span class="text-xs text-gray-500">({{ r.user.email }})</span></div>
              <div v-else class="text-gray-400">—</div>
            </td>
            <td class="p-2">
              <div v-if="r.raffle_item">
                <div class="text-xs text-gray-600">Raffle Item #{{ r.raffle_item.id }}</div>
                <div class="text-xs text-gray-400">{{ r.raffle_item.raffle?.title }}</div>
              </div>
              <div v-else class="text-gray-400">—</div>
            </td>
            <td class="p-2">{{ new Date(r.purged_at).toLocaleString() }}</td>
            <td class="p-2 text-right">
              <button @click="deleteRecovery(r.id)" class="px-3 py-1 text-red-700 hover:bg-red-50 rounded">Löschen</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="p-2 border-t text-xs text-gray-500">
        <div v-if="recoveries.total">{{ recoveries.from }}–{{ recoveries.to }} von {{ recoveries.total }}</div>
      </div>
    </div>
  </AdminLayout>
</template>
