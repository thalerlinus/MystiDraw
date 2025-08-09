<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';
const page = usePage();
const items = computed(()=> page.props.items);
</script>
<template>
  <Head title="Inventar" />
  <AdminLayout title="Inventar">
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">User</th>
            <th class="px-4 py-2 text-left">Produkt</th>
            <th class="px-4 py-2 text-left">Menge</th>
            <th class="px-4 py-2 text-left">Letztes Ticket</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="i in items.data" :key="i.id" class="hover:bg-gray-50">
            <td class="px-4 py-2">#{{ i.user_id }}</td>
            <td class="px-4 py-2">{{ i.product?.name || i.product_id }}</td>
            <td class="px-4 py-2">{{ i.quantity }}</td>
            <td class="px-4 py-2">{{ i.last_ticket_id || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
