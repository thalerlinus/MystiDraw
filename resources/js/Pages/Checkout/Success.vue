<template>
  <MainLayout :title="pageTitle">
  <div class="container mx-auto px-4 py-12 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-xl p-8 border" :class="pending ? 'border-green-100' : (oversellRefund ? 'border-red-200' : 'border-green-100')">
      <!-- Pending (Webhook Delay) -->
      <div v-if="pending && !oversellRefund" class="flex flex-col items-center space-y-4 text-center">
        <div class="w-16 h-16 rounded-full border-4 border-green-200 border-t-green-500 animate-spin"></div>
        <h1 class="text-2xl font-black text-green-700">Zahlung wird bestätigt...</h1>
        <p class="text-green-600 text-sm">Wir warten auf die Bestätigung vom Zahlungsanbieter.</p>
        <div class="text-xs text-green-500" v-if="countdown > 0">Automatische Aktualisierung in <span class="font-semibold">{{ countdown }}</span> s…</div>
        <button @click="reload" class="mt-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Jetzt aktualisieren</button>
      </div>

      <!-- Oversell / Refund -->
      <div v-else-if="oversellRefund" class="space-y-6">
        <div class="flex items-center space-x-4">
          <div class="p-3 bg-red-500 text-white rounded-full">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </div>
          <h1 class="text-3xl font-black text-red-600">Bestellung nicht möglich</h1>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-2">
          <p class="text-red-700 font-semibold">Deine Zahlung wurde automatisch erstattet.</p>
          <p class="text-red-600 text-sm">Während deiner Zahlung waren leider nicht mehr genügend Lose verfügbar (Oversell Schutz). Der Betrag wird dir automatisch zurückerstattet. Je nach Bank kann die Anzeige 1–3 Werktage dauern.</p>
          <p v-if="paymentStatus" class="text-xs text-red-500">Status: {{ paymentStatus }}</p>
        </div>
        <div class="flex gap-4 pt-2">
          <a :href="route('raffles.show',{ raffle: raffleParam })" v-if="raffleParam" class="flex-1 text-center px-6 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-xl">Raffle ansehen</a>
          <a :href="route('raffles.index')" class="flex-1 text-center px-6 py-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl">Weitere Raffles</a>
        </div>
      </div>

      <!-- Erfolg -->
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
            <div class="flex flex-wrap gap-3 justify-center">
              <button 
                @click="showTicketOpening = true"
                class="px-6 py-3 text-lg font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors inline-flex items-center gap-2">
                <i class="fa fa-gift"></i>
                Tickets öffnen
              </button>
              <a :href="route('inventory.index')" class="px-6 py-3 text-lg font-semibold rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white inline-flex items-center gap-2">
                <i class="fa fa-box"></i>
                Inventar ansehen
              </a>
            </div>
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

      <!-- Fallback -->
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
import { ref, computed, onMounted } from 'vue';
const page = usePage();
const order = ref(page.props.order);
const pending = ref(page.props.pending);
const tickets = ref(page.props.tickets || []);
const oversellRefund = ref(!!page.props.oversell_refund);
const paymentStatus = ref(page.props.payment_status || null);
const showTicketOpening = ref(false);
const ticketsGenerating = computed(()=> !pending.value && order.value && tickets.value.length === 0);
const formatPrice = (amount, currency) => new Intl.NumberFormat('de-DE',{style:'currency',currency:currency||'EUR'}).format(amount);
const reload = () => window.location.reload();
const raffleSlug = page.props.raffle_slug;
const raffleUrl = computed(()=> raffleSlug ? route('raffles.show', { raffle: raffleSlug }) : null);

// Auto-Reload Countdown bei pending
const countdown = ref(4);
let interval = null;
const startCountdown = () => {
  if (!pending.value) return;
  interval = setInterval(()=>{
    countdown.value--;
    if (countdown.value <= 0) {
      clearInterval(interval);
      reload();
    }
  }, 1000);
};

const pageTitle = computed(()=> {
  if (oversellRefund.value) return 'Erstattung';
  if (pending.value) return 'Zahlung wird bestätigt';
  if (order.value) return 'Zahlung erfolgreich';
  return 'Bestellung';
});

onMounted(() => { if (pending.value && !oversellRefund.value) { startCountdown(); } });
</script>
