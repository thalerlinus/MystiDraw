<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';
const page = usePage();
const orders = computed(()=> page.props.orders);
const filters = computed(()=> page.props.filters || {});
</script>
<template>
  <Head title="Bestellungen" />
  <AdminLayout title="Bestellungen">
    <form method="get" class="mb-4">
      <select name="status" class="rounded border-gray-300" :value="filters.status" @change="$event.target.form.submit()">
        <option value="">Status: Alle</option>
        <option value="pending">Pending</option>
        <option value="paid">Paid</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </form>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Typ</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Items</th>
            <th class="px-4 py-2 text-left">Total</th>
            <th class="px-4 py-2 text-left">Bezahlt</th>
            <th class="px-4 py-2 text-left">Rechnung</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="o in orders.data" :key="o.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium">#{{ o.id }}</td>
            <td class="px-4 py-2"><span class="rounded bg-slate-100 px-2 py-0.5 text-xs">{{ o.type || '-' }}</span></td>
            <td class="px-4 py-2"><span class="rounded bg-gray-100 px-2 py-0.5 text-xs">{{ o.status }}</span></td>
            <td class="px-4 py-2">{{ o.items_count }}</td>
            <td class="px-4 py-2">{{ Number(o.total).toFixed(2) }}</td>
            <td class="px-4 py-2">{{ o.paid_at || '-' }}</td>
            <td class="px-4 py-2">{{ (o.payments?.[0]?.invoice_number) || '-' }}</td>
            <td class="px-4 py-2 text-right"><Link :href="`/admin/orders/${o.id}`" class="text-indigo-600 hover:underline">Details</Link></td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
