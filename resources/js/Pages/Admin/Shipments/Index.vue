<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';
const page = usePage();
const shipments = computed(()=> page.props.shipments);
const filters = computed(()=> page.props.filters || {});
</script>
<template>
  <Head title="Versand" />
  <AdminLayout title="Versand">
    <form method="get" class="mb-4">
      <select name="status" class="rounded border-gray-300" :value="filters.status" @change="$event.target.form.submit()">
        <option value="">Alle Status</option>
        <option value="pending">Pending</option>
        <option value="processing">Processing</option>
        <option value="shipped">Shipped</option>
        <option value="delivered">Delivered</option>
      </select>
    </form>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Order</th>
            <th class="px-4 py-2 text-left">Carrier</th>
            <th class="px-4 py-2 text-left">Tracking</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="s in shipments.data" :key="s.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium">#{{ s.id }}</td>
            <td class="px-4 py-2"><span class="rounded bg-gray-100 px-2 py-0.5 text-xs">{{ s.status }}</span></td>
            <td class="px-4 py-2">#{{ s.order_id }}</td>
            <td class="px-4 py-2">{{ s.carrier || '-' }}</td>
            <td class="px-4 py-2">{{ s.tracking_number || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
