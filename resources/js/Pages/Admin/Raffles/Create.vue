<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const form = useForm({
  name: '',
  slug: '',
  starts_at: '',
  ends_at: '',
  base_ticket_price: '',
  currency: 'EUR'
});

function submit(){
  form.post('/admin/raffles');
}
</script>
<template>
  <Head title="Raffle erstellen" />
  <AdminLayout title="Raffle erstellen">
    <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input v-model="form.name" type="text" class="mt-1 w-full rounded border-gray-300" />
        <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Slug</label>
        <input v-model="form.slug" type="text" class="mt-1 w-full rounded border-gray-300" />
        <div v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</div>
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
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Ticket Grundpreis</label>
          <input v-model="form.base_ticket_price" type="number" step="0.01" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Währung</label>
          <input v-model="form.currency" type="text" class="mt-1 w-full rounded border-gray-300" />
        </div>
      </div>
      <div>
        <button :disabled="form.processing" class="rounded bg-indigo-600 px-4 py-2 text-white disabled:opacity-50">Speichern</button>
      </div>
    </form>
  </AdminLayout>
</template>
