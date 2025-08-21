<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed, ref } from 'vue';
const page = usePage();
const payments = computed(()=> page.props.payments);
const filters = computed(()=> page.props.filters || {});
const q = ref(filters.value.q || '');
</script>
<template>
  <Head title="Gutschriften" />
  <AdminLayout title="Gutschriften">
    <form method="get" class="mb-4 flex gap-2">
      <input name="q" v-model="q" placeholder="Suche Nummer / Txn / Kunde" class="flex-1 rounded border-gray-300 text-sm" />
      <button class="rounded bg-indigo-600 text-white px-3 py-2 text-sm">Filtern</button>
      <Link href="/admin/credit-notes" class="rounded px-3 py-2 text-sm border border-gray-300 text-gray-600">Reset</Link>
    </form>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">Nr.</th>
            <th class="px-4 py-2 text-left">Txn</th>
            <th class="px-4 py-2 text-left">Kunde</th>
            <th class="px-4 py-2 text-left">Betrag</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Erstattet</th>
            <th class="px-4 py-2 text-right">PDF</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          <tr v-for="p in payments.data" :key="p.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium">{{ p.credit_note_number }}</td>
            <td class="px-4 py-2 text-xs">{{ p.provider_txn_id }}</td>
            <td class="px-4 py-2">{{ p.order?.user?.email || p.user?.email || '-' }}</td>
            <td class="px-4 py-2">{{ Number(p.amount).toFixed(2) }} {{ p.currency?.toUpperCase() }}</td>
            <td class="px-4 py-2"><span class="rounded bg-rose-50 text-rose-700 px-2 py-0.5 text-xs">{{ p.status }}</span></td>
            <td class="px-4 py-2 text-xs">{{ p.refund_email_sent_at || '-' }}</td>
            <td class="px-4 py-2 text-right">
              <a :href="`/admin/credit-notes/${p.id}/download`" target="_blank" rel="noopener" class="text-indigo-600 hover:underline text-xs">Download</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
