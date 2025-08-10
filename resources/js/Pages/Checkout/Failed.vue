<template>
  <MainLayout title="Zahlung fehlgeschlagen">
  <div class="container mx-auto px-4 py-12 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-red-100 space-y-6">
      <div class="flex items-center space-x-4">
        <div class="p-3 bg-red-500 text-white rounded-full">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </div>
        <h1 class="text-3xl font-black text-red-700">Zahlung fehlgeschlagen</h1>
      </div>
      <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <p class="text-red-700 mb-2">Leider konnte deine Zahlung nicht abgeschlossen werden.</p>
        <p v-if="status" class="text-red-600 text-sm">Status: {{ status }}</p>
        <p v-if="error" class="text-red-600 text-sm">Fehler: {{ error }}</p>
      </div>
      <div class="flex flex-col gap-4">
        <div v-if="raffleUrl" class="flex">
          <a :href="raffleUrl" class="flex-1 text-center px-6 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl">Zurück zum Raffle</a>
        </div>
        <div class="flex gap-4">
          <a :href="route('raffles.index')" class="flex-1 text-center px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-bold rounded-xl hover:from-yellow-400 hover:to-yellow-500">Alle Raffles</a>
          <a :href="route('home')" class="flex-1 text-center px-6 py-4 border-2 border-red-400 text-red-700 font-bold rounded-xl hover:bg-red-50">Startseite</a>
        </div>
      </div>
    </div>
  </div>
  </MainLayout>
</template>
<script setup>
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { computed } from 'vue';
const page = usePage();
const error = page.props.error;
const status = page.props.status;
// Attempt to reconstruct raffle link if order meta passed via props in future
const raffleId = page.props.order?.items?.[0]?.raffle_id || page.props.raffle_id;
const raffleSlug = page.props.raffle_slug;
const raffleParam = raffleSlug || raffleId;
const raffleUrl = computed(()=> raffleParam ? route('raffles.show', { raffle: raffleParam }) : null);
</script>
