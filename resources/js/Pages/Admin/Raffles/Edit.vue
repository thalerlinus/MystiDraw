<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const raffle = usePage().props.raffle;
const form = useForm({
  name: raffle.name,
  starts_at: raffle.starts_at || '',
  ends_at: raffle.ends_at || '',
  base_ticket_price: raffle.base_ticket_price,
  currency: raffle.currency,
  status: raffle.status
});
function submit(){
  form.put(`/admin/raffles/${raffle.id}`);
}
</script>
<template>
  <Head :title="`Raffle bearbeiten - ${raffle.name}`" />
  <AdminLayout :title="`Raffle bearbeiten`">
    <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input v-model="form.name" type="text" class="mt-1 w-full rounded border-gray-300" />
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Start</label>
          <input v-model="form.starts_at" type="datetime-local" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Ende</label>
          <input v-model="form.ends_at" type="datetime-local" class="mt-1 w-full rounded border-gray-300" />
        </div>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Ticket Grundpreis</label>
          <input v-model="form.base_ticket_price" type="number" step="0.01" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Währung</label>
          <input v-model="form.currency" type="text" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="form.status" class="mt-1 w-full rounded border-gray-300">
            <option value="draft">Draft</option>
            <option value="active">Aktiv</option>
            <option value="finished">Beendet</option>
          </select>
        </div>
      </div>
      <div>
        <button :disabled="form.processing" class="rounded bg-indigo-600 px-4 py-2 text-white disabled:opacity-50">Speichern</button>
      </div>
    </form>
  </AdminLayout>
</template>
