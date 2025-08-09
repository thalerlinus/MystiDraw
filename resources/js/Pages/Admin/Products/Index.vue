<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';

const page = usePage();
const products = computed(()=> page.props.products);
const filters = computed(()=> page.props.filters || {});
</script>
<template>
  <Head title="Produkte" />
  <AdminLayout title="Produkte">
    <div class="mb-4 flex items-center gap-3">
      <form method="get" class="flex items-center gap-2">
        <input type="text" name="q" placeholder="Suche" class="rounded border-gray-300" :value="filters.q" />
      </form>
      <Link href="/admin/products/create" class="rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white">Neu</Link>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">SKU</th>
            <th class="px-4 py-2 text-left">Preis</th>
            <th class="px-4 py-2 text-left">Aktiv</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="p in products.data" :key="p.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium">{{ p.name }}</td>
            <td class="px-4 py-2">{{ p.sku }}</td>
            <td class="px-4 py-2">{{ p.base_cost }}</td>
            <td class="px-4 py-2">{{ p.active ? 'Ja':'Nein' }}</td>
            <td class="px-4 py-2 text-right"><Link :href="`/admin/products/${p.id}/edit`" class="text-indigo-600 hover:underline">Bearbeiten</Link></td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
