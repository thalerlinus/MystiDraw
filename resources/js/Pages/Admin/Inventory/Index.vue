<script setup>
import { Head, usePage, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';
const page = usePage();
const items = computed(()=> page.props.items);
const filters = computed(()=> page.props.filters || {});
const statusOptions = [
  { value: '', label: 'Alle Status'},
  { value: 'owned', label: 'Owned'},
  { value: 'reserved_for_shipping', label: 'Reserviert Versand'},
  { value: 'shipped', label: 'Shipped'},
];
const deliveredOptions = [
  { value: '', label: 'Alle'},
  { value: '1', label: 'Nur Delivered'},
  { value: '0', label: 'Nicht Delivered'},
];
</script>
<template>
  <Head title="Inventar" />
  <AdminLayout title="Inventar">
    <form method="get" class="mb-4 flex gap-2 items-end">
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
        <select name="status" class="rounded border-gray-300" :value="filters.status">
          <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">User ID</label>
        <input type="number" min="1" name="user_id" class="rounded border-gray-300" :value="filters.user_id || ''" placeholder="z.B. 5" />
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Delivered</label>
        <select name="delivered" class="rounded border-gray-300" :value="filters.delivered ?? ''">
          <option v-for="opt in deliveredOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
        </select>
      </div>
      <button class="px-3 py-2 rounded bg-indigo-600 text-white text-sm">Filtern</button>
      <Link href="/admin/inventory" class="text-sm text-gray-500 hover:underline">Reset</Link>
    </form>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">User</th>
            <th class="px-4 py-2 text-left">Produkt</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Outcome</th>
            <th class="px-4 py-2 text-left">Owned</th>
            <th class="px-4 py-2 text-left">Shipped</th>
            <th class="px-4 py-2 text-left">Carrier</th>
            <th class="px-4 py-2 text-left">Tracking</th>
            <th class="px-4 py-2 text-left">Delivered</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="i in items.data" :key="i.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium">#{{ i.id }}</td>
            <td class="px-4 py-2">#{{ i.user_id }}</td>
            <td class="px-4 py-2">{{ i.product?.name || ('#'+i.product_id) }}</td>
            <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-gray-100">{{ i.status }}</span></td>
            <td class="px-4 py-2">{{ i.ticket_outcome_id ? ('#'+i.ticket_outcome_id) : '-' }}</td>
            <td class="px-4 py-2">{{ i.owned_at || '-' }}</td>
            <td class="px-4 py-2">{{ i.shipped_at || '-' }}</td>
            <td class="px-4 py-2">
              {{ (i.shipment_items && i.shipment_items.length && i.shipment_items[0].shipment?.carrier) || '-' }}
            </td>
            <td class="px-4 py-2">
              <template v-if="i.shipment_items && i.shipment_items.length && i.shipment_items[0].shipment?.tracking_number">
                <a :href="i.shipment_items[0].shipment.tracking_url || '#'" target="_blank" class="text-indigo-600 hover:underline">
                  {{ i.shipment_items[0].shipment.tracking_number }}
                </a>
              </template>
              <template v-else>-</template>
            </td>
            <td class="px-4 py-2">{{ (i.shipment_items && i.shipment_items.length && i.shipment_items[0].shipment?.delivered_at) || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
