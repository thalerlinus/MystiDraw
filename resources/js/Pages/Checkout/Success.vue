<template>
  <MainLayout :title="pending ? 'Zahlung wird bestätigt' : (order ? 'Zahlung erfolgreich' : 'Bestellung ausstehend')">
  <div class="container mx-auto px-4 py-12 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-xl p-8 border border-green-100">
      <div v-if="pending" class="flex flex-col items-center space-y-4">
        <div class="w-16 h-16 rounded-full border-4 border-green-200 border-t-green-500 animate-spin"></div>
        <h1 class="text-2xl font-black text-green-700">Zahlung wird bestätigt...</h1>
        <p class="text-green-600 text-sm">Bitte warte einen Moment, wir verarbeiten deine Bestellung.</p>
      </div>
  <div v-else-if="order" class="space-y-10">
        <div class="flex items-center space-x-4">
          <div class="p-3 bg-green-500 text-white rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
          </div>
          <h1 class="text-3xl font-black text-green-700">Erfolg! Bestellung bezahlt</h1>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
          <p class="text-green-700">Bestellnummer: <span class="font-mono">#{{ order.id }}</span></p>
          <p class="text-green-700">Gesamt: <strong>{{ formatPrice(order.total, order.currency) }}</strong></p>
        </div>
        <div>
          <h2 class="font-bold mb-2 text-green-800">Bestellte Lose</h2>
          <ul class="divide-y divide-green-200 bg-green-50 rounded-xl">
            <li v-for="item in order.items" :key="item.raffle_id" class="flex justify-between px-4 py-3">
              <span>Raffle #{{ item.raffle_id }} × {{ item.quantity }}</span>
              <span>{{ formatPrice(item.subtotal, order.currency) }}</span>
            </li>
          </ul>
        </div>
        <div v-if="ticketsGenerating" class="bg-white/50 border border-yellow-200 rounded-xl p-4 flex flex-col items-center text-center space-y-3">
          <div class="w-10 h-10 rounded-full border-4 border-yellow-200 border-t-yellow-500 animate-spin"></div>
          <div class="text-sm text-yellow-700 font-semibold">Tickets werden erstellt...</div>
          <div class="text-xs text-yellow-600">Das kann ein paar Sekunden dauern.</div>
        </div>
        <div v-else-if="tickets.length" class="bg-white/50 border border-green-100 rounded-xl p-4">
          <div class="text-center">
            <p class="text-green-800 font-semibold mb-4">{{ tickets.length }} Ticket(s) erfolgreich erstellt!</p>
            <button 
              @click="showTicketOpening = true"
              class="px-6 py-3 text-lg font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors inline-flex items-center gap-2"
            >
              <i class="fa fa-gift"></i>
              Tickets öffnen
            </button>
          </div>
        </div>
        <div class="flex flex-col gap-4 pt-2">
          <div v-if="raffleUrl" class="flex">
            <a :href="raffleUrl" class="flex-1 text-center px-6 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl">Zurück zum Raffle</a>
          </div>
          <div class="flex gap-4">
            <a :href="route('raffles.index')" class="flex-1 text-center px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-bold rounded-xl hover:from-yellow-400 hover:to-yellow-500">Weitere Raffles</a>
            <a :href="route('home')" class="flex-1 text-center px-6 py-4 border-2 border-green-400 text-green-700 font-bold rounded-xl hover:bg-green-50">Startseite</a>
          </div>
        </div>
      </div>
      <div v-else class="text-center space-y-4">
        <h1 class="text-2xl font-black text-yellow-700">Noch keine Bestelldaten</h1>
        <p class="text-yellow-600 text-sm">Wenn die Zahlung abgeschlossen ist, aktualisiere bitte diese Seite.</p>
        <button @click="reload" class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl">Aktualisieren</button>
      </div>
    </div>
  </div>
  
  <!-- Ticket Opening Modal -->
  <TicketOpeningModal 
    :show="showTicketOpening" 
    :tickets="tickets" 
    @close="showTicketOpening = false"
  />
  </MainLayout>
</template>
<script setup>
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import TicketOpeningModal from '@/Components/TicketOpeningModal.vue';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
const page = usePage();
const order = ref(page.props.order);
const pending = ref(page.props.pending);
const tickets = ref(page.props.tickets || []);
const showTicketOpening = ref(false);
const ticketsGenerating = computed(()=> !pending.value && order.value && tickets.value.length === 0);
const formatPrice = (amount, currency) => new Intl.NumberFormat('de-DE',{style:'currency',currency:currency||'EUR'}).format(amount);
const reload = () => window.location.reload();
const raffleId = computed(()=> (order.value?.items?.[0]?.raffle_id) || page.props.raffle_id);
const raffleSlug = page.props.raffle_slug;
const raffleParam = computed(()=> raffleSlug || raffleId.value);
const raffleUrl = computed(()=> raffleParam.value ? route('raffles.show', { raffle: raffleParam.value }) : null);
// Provide a URL that signals the raffle page to open ticket view (query param your show component can read)
const ticketsViewUrl = computed(()=> raffleUrl.value ? `${raffleUrl.value}?view=tickets` : null);

// Polling for pending status
import axios from 'axios';
let pollTimer = null;
let ticketPollTimer = null;
let ticketPollAttempts = 0;
const paymentIntent = page.props.payment_intent;
const poll = async () => {
  if (!pending.value || !paymentIntent) return;
  try {
    const { data } = await axios.get(route('checkout.status', { payment_intent: paymentIntent }));
    if (data.status === 'succeeded' && data.order) {
      order.value = data.order;
      tickets.value = data.tickets || [];
      pending.value = false;
    }
  } catch(_) {}
};
const pollTickets = async () => {
  if (!ticketsGenerating.value || ticketPollAttempts > 20 || !paymentIntent) return;
  ticketPollAttempts++;
  try {
    const { data } = await axios.get(route('checkout.status', { payment_intent: paymentIntent }));
    if (data.status === 'succeeded' && data.tickets && data.tickets.length) {
      tickets.value = data.tickets;
      if (ticketPollTimer) { clearInterval(ticketPollTimer); ticketPollTimer = null; }
    }
  } catch(_) {}
};

onMounted(() => {
  if (pending.value) {
    pollTimer = setInterval(poll, 2500);
    poll();
  } else if (ticketsGenerating.value) {
    ticketPollTimer = setInterval(pollTickets, 1500);
    pollTickets();
  }
});
onBeforeUnmount(() => { if (pollTimer) clearInterval(pollTimer); if (ticketPollTimer) clearInterval(ticketPollTimer); });
</script>
