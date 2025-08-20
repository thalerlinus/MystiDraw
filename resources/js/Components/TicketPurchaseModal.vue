<template>
    <div 
        v-if="isOpen" 
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click="closeModal"
    >
        <div 
            class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden relative"
            @click.stop
        >
            <!-- Header -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 relative">
                <button 
                    @click="closeModal"
                    class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors p-2"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-full">
                        <font-awesome-icon :icon="['fas', 'ticket']" class="text-2xl" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-black">Lose kaufen</h2>
                        <p class="text-white/90">{{ raffle.name }}</p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
                <!-- Prozess Overlay während finaler Bestätigung -->
                <div v-if="finalizing" class="absolute inset-0 z-40 flex items-center justify-center bg-white/80">
                    <div class="bg-white rounded-2xl p-8 shadow-xl w-full max-w-sm text-center space-y-4 border border-yellow-200">
                        <div class="w-16 h-16 rounded-full border-4 border-yellow-200 border-t-yellow-500 animate-spin mx-auto"></div>
                        <div class="space-y-1">
                            <h3 class="text-xl font-black text-gray-800" v-if="!finalizingSlow">Zahlung wird verarbeitet…</h3>
                            <h3 class="text-xl font-black text-gray-800" v-else>Fast geschafft…</h3>
                            <p class="text-sm text-gray-600" v-if="!finalizingSlow">Bitte nicht schließen.</p>
                            <p class="text-sm text-gray-600" v-else>Webhook-Bestätigung steht noch aus. Deine Lose werden gleich erstellt.</p>
                        </div>
                        <button type="button" @click="forceClose" class="mt-2 text-xs text-gray-500 hover:text-gray-700 underline">Fenster schließen</button>
                        <div class="text-[10px] text-gray-400" v-if="finalizingSlow">Du kannst dieses Fenster schließen; die Buchung läuft weiter.</div>
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="mb-8" v-if="!finalizing">
                    <label class="block text-lg font-bold text-gray-900 mb-4">
                        Anzahl Lose wählen
                    </label>
                    
                    <div class="flex items-center space-x-4 mb-6">
                        <button
                            @click="decreaseQuantity"
                            :disabled="quantity <= 1"
                            class="w-12 h-12 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 disabled:text-gray-300 rounded-full flex items-center justify-center font-bold text-xl transition-all duration-200 disabled:cursor-not-allowed"
                        >
                            −
                        </button>
                        
                        <div class="flex-1 relative">
                            <input
                                v-model.number="quantity"
                                @input="validateQuantity"
                                type="number"
                                min="1"
                                :max="raffle.tickets_available"
                                class="w-full text-center text-2xl font-bold py-4 px-6 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition-all duration-200"
                            />
                            <div class="absolute -bottom-6 left-0 right-0 text-center text-sm text-gray-500">
                                Max. {{ raffle.tickets_available }} verfügbar
                            </div>
                        </div>
                        
                        <button
                            @click="increaseQuantity"
                            :disabled="quantity >= raffle.tickets_available"
                            class="w-12 h-12 bg-gray-100 hover:bg-gray-200 disabled:bg-gray-50 disabled:text-gray-300 rounded-full flex items-center justify-center font-bold text-xl transition-all duration-200 disabled:cursor-not-allowed"
                        >
                            +
                        </button>
                    </div>

                    <!-- Quick Select Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="quickAmount in quickSelectAmounts"
                            :key="quickAmount"
                            @click="quantity = quickAmount"
                            :disabled="quickAmount > raffle.tickets_available"
                            class="px-4 py-2 bg-gray-100 hover:bg-yellow-100 hover:border-yellow-300 disabled:bg-gray-50 disabled:text-gray-300 border border-gray-200 rounded-lg font-semibold transition-all duration-200 disabled:cursor-not-allowed"
                            :class="quantity === quickAmount ? 'bg-yellow-500 text-white border-yellow-500' : ''"
                        >
                            {{ quickAmount }}
                        </button>
                    </div>
                </div>

                <!-- Pricing Breakdown -->
                <div class="mb-8" v-if="!finalizing">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Preisübersicht</h3>
                    
                    <div class="bg-gray-50 rounded-xl p-6 space-y-4">
                        <!-- Current Tier Info -->
                        <div v-if="currentTier" class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-yellow-500 rounded-full">
                                    <font-awesome-icon :icon="['fas', 'tags']" class="text-white" />
                                </div>
                                <div>
                                    <div class="font-bold text-yellow-800">
                                        Staffelpreis aktiv!
                                    </div>
                                    <div class="text-sm text-yellow-600">
                                        Ab {{ currentTier.min_qty }} Losen: {{ formatPrice(currentTier.unit_price) }} pro Los
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-yellow-600">Ersparnis pro Los:</div>
                                <div class="font-bold text-green-600">
                                    {{ formatPrice(raffle.base_ticket_price - currentTier.unit_price) }}
                                </div>
                            </div>
                        </div>

                        <!-- Price Calculation -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">{{ quantity }} × Lose à {{ formatPrice(unitPrice) }}</span>
                                <span class="font-semibold">{{ formatPrice(subtotal) }}</span>
                            </div>
                            
                            <div v-if="totalSavings > 0" class="flex justify-between items-center text-green-600">
                                <span>Gesamtersparnis</span>
                                <span class="font-bold">-{{ formatPrice(totalSavings) }}</span>
                            </div>
                            
                            <hr class="border-gray-200">
                            
                            <div class="flex justify-between items-center text-xl font-black">
                                <span>Gesamtpreis</span>
                                <span class="text-yellow-600">{{ formatPrice(totalPrice) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Tier Preview -->
                <div v-if="nextTier && quantity < nextTier.min_qty && !finalizing" class="mb-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-center space-x-2 text-blue-700 mb-2">
                            <font-awesome-icon :icon="['fas', 'info-circle']" />
                            <span class="font-semibold">Tipp: Noch mehr sparen!</span>
                        </div>
                        <p class="text-blue-600 text-sm">
                            Bei {{ nextTier.min_qty }} Losen sparst du {{ formatPrice(raffle.base_ticket_price - nextTier.unit_price) }} pro Los.
                            <button 
                                @click="quantity = nextTier.min_qty"
                                class="ml-2 text-blue-700 font-semibold hover:underline"
                            >
                                {{ nextTier.min_qty }} Lose auswählen
                            </button>
                        </p>
                    </div>
                </div>

                <!-- Schritt 2: Zahlung erst nach Klick anzeigen -->
                <!-- WICHTIG: Element nicht via v-if verstecken wenn finalizing, sonst unmounted -> Stripe IntegrationError -->
                <div class="mb-6" v-if="paymentStep">
                    <label class="block text-lg font-bold text-gray-900 mb-3">Zahlung</label>
                    <div id="payment-element" class="p-4 border-2 border-gray-200 rounded-xl relative min-h-[80px]" :class="finalizing ? 'opacity-50 pointer-events-none' : ''">
                        <div v-show="!clientSecret" class="h-full w-full flex items-center justify-center text-sm text-gray-500 space-x-2">
                            <font-awesome-icon :icon="['fas','spinner']" class="animate-spin" />
                            <span>Lade Zahlungsdaten…</span>
                        </div>
                        <div v-show="clientSecret && updatingIntent" class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl pointer-events-none">
                            <div class="flex items-center space-x-2 text-sm font-semibold text-gray-600">
                                <font-awesome-icon :icon="['fas','spinner']" class="animate-spin" />
                                <span>Aktualisiere Preis…</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="errorMessage && !finalizing" class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm font-semibold border border-red-200">{{ errorMessage }}</div>
                <div v-if="successMessage && !finalizing" class="mb-4 p-3 rounded bg-green-50 text-green-700 text-sm font-semibold border border-green-200">{{ successMessage }}</div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4" v-if="!finalizing">
                    <button @click="closeModal" class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all duration-200">
                        Abbrechen
                    </button>
                    <!-- Schritt 1: Weiter zur Zahlung -->
                    <button
                        v-if="!paymentStep"
                        @click="proceedToPayment"
                        :disabled="quantity <= 0 || quantity > raffle.tickets_available || loading"
                        class="flex-1 px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 disabled:from-gray-300 disabled:to-gray-300 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 disabled:transform-none disabled:cursor-not-allowed shadow-lg"
                    >
                        <div class="flex flex-col items-center justify-center leading-tight">
                            <span class="flex items-center space-x-2">
                                <font-awesome-icon :icon="['fas', loading ? 'spinner' : 'arrow-right']" :class="loading ? 'animate-spin' : ''" />
                                <span>Zur Zahlung</span>
                            </span>
                            <span class="text-xs font-semibold mt-1">Gesamt: {{ formatPrice(totalPrice) }}</span>
                        </div>
                    </button>
                    <!-- Schritt 2: Kaufen -->
                    <button
                        v-else
                        @click="purchaseTickets"
                        :disabled="loading || !clientSecret"
                        class="flex-1 px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 disabled:from-gray-300 disabled:to-gray-300 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 disabled:transform-none disabled:cursor-not-allowed shadow-lg"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <font-awesome-icon :icon="['fas', loading ? 'spinner' : 'shopping-cart']" :class="loading ? 'animate-spin' : ''" />
                            <span v-if="!loading">Jetzt kaufen</span>
                            <span v-else>Verarbeite...</span>
                            <span v-if="!loading" class="font-black">{{ formatPrice(totalPrice) }}</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
    raffle: { type: Object, required: true },
    isOpen: { type: Boolean, default: false },
    initialQuantity: { type: Number, default: 1 }
});

const emit = defineEmits(['close', 'purchase']);

// Reactive data
const quantity = ref(props.initialQuantity);

// Quick select amounts
const quickSelectAmounts = computed(() => {
    const amounts = [1, 5, 10];
    
    if (props.raffle.pricing_tiers) {
        props.raffle.pricing_tiers.forEach(tier => {
            if (!amounts.includes(tier.min_qty) && tier.min_qty <= props.raffle.tickets_available) {
                amounts.push(tier.min_qty);
            }
        });
    }
    
    if (props.raffle.tickets_available <= 50 && !amounts.includes(props.raffle.tickets_available)) {
        amounts.push(props.raffle.tickets_available);
    }
    
    return amounts.sort((a, b) => a - b).filter(amount => amount <= props.raffle.tickets_available);
});

// Computed properties for pricing
const sortedTiers = computed(() => {
    if (!props.raffle.pricing_tiers) return [];
    return [...props.raffle.pricing_tiers].sort((a, b) => a.min_qty - b.min_qty);
});

const currentTier = computed(() => {
    return sortedTiers.value
        .filter(tier => quantity.value >= tier.min_qty)
        .pop();
});

const nextTier = computed(() => {
    return sortedTiers.value.find(tier => quantity.value < tier.min_qty);
});

const unitPrice = computed(() => currentTier.value ? currentTier.value.unit_price : props.raffle.base_ticket_price);
const subtotal = computed(() => quantity.value * unitPrice.value);
const totalSavings = computed(() => !currentTier.value ? 0 : quantity.value * (props.raffle.base_ticket_price - currentTier.value.unit_price));
const totalPrice = computed(() => subtotal.value);

const formatPrice = (price) => new Intl.NumberFormat('de-DE', { style: 'currency', currency: props.raffle.currency || 'EUR' }).format(price);

const validateQuantity = () => { if (quantity.value < 1) quantity.value = 1; else if (quantity.value > props.raffle.tickets_available) quantity.value = props.raffle.tickets_available; };
const increaseQuantity = () => { if (quantity.value < props.raffle.tickets_available) quantity.value++; };
const decreaseQuantity = () => { if (quantity.value > 1) quantity.value--; };
const closeModal = async () => { emit('close'); };
const forceClose = () => { finalizing.value = false; finalizingSlow.value = false; emit('close'); };

const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const clientSecret = ref(null);
const stripe = ref(null);
const elements = ref(null);
const paymentElement = ref(null);
const lastIntentQuantity = ref(null);
const updatingIntent = ref(false);
const page = usePage();
const paymentStep = ref(false);

// Finalisierungs UI States
const finalizing = ref(false); // sofort nach confirmPayment
const finalizingSlow = ref(false); // nach 2-3s ohne Redirect Hinweis anzeigen
let finalizingTimer = null;

const initStripeElements = async (opId) => {
    if (!props.isOpen) return;
    if (!stripe.value) {
        stripe.value = await loadStripe(page.props.stripe.key);
    }
    if (clientSecret.value && stripe.value) {
        await nextTick();
        const container = document.getElementById('payment-element');
        if (!container) return;
        if (opId !== currentIntentOpId) return;
        if (paymentElement.value) { try { paymentElement.value.unmount(); } catch (_) {} paymentElement.value = null; }
        container.innerHTML = '';
        elements.value = stripe.value.elements({ clientSecret: clientSecret.value });
        const paymentEl = elements.value.create('payment');
        paymentEl.mount(container);
        paymentElement.value = paymentEl;
    }
};

let currentIntentOpId = 0;
const createIntent = async () => {
    if (!props.isOpen || !paymentStep.value) return;
    if (loading.value) return;
    if (!page.props.auth?.user) { errorMessage.value = 'Sie müssen angemeldet sein, um Lose zu kaufen.'; return; }
    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';
    try {
        const opId = ++currentIntentOpId;
        const { data } = await axios.post(`/raffles/${props.raffle.id}/purchase/intent`, { quantity: quantity.value });
        if (!props.isOpen || opId !== currentIntentOpId) return;
        clientSecret.value = data.client_secret;
        lastIntentQuantity.value = quantity.value;
        await initStripeElements(opId);
    } catch (e) {
        errorMessage.value = e.response?.data?.message || 'Fehler beim Anlegen der Zahlung';
    } finally {
        loading.value = false;
    }
};

const refreshIntent = async () => { if (!props.isOpen) return; updatingIntent.value = true; try { await createIntent(); } finally { updatingIntent.value = false; } };

const availability = ref({ available: null, total: null, status: null });
const availabilityError = ref('');

const fetchAvailability = async () => {
  try {
    availabilityError.value = '';
    const { data } = await axios.get(`/raffles/${props.raffle.id}/availability`);
    availability.value = data;
    if (data.status !== 'live' || data.available <= 0) {
      errorMessage.value = 'Raffle nicht mehr verfügbar.';
      return false;
    }
    if (quantity.value > data.available) {
      quantity.value = data.available;
      errorMessage.value = `Menge angepasst – nur noch ${data.available} verfügbar.`;
      return false; // erzwingt erneuten Intent erst nach Bestätigung
    }
    return true;
  } catch (e) {
    availabilityError.value = 'Verfügbarkeitsprüfung fehlgeschlagen';
    return true; // nicht blockieren, aber loggen könnte man später
  }
};

const purchaseTickets = async () => {
    if (!page.props.auth?.user) { errorMessage.value = 'Sie müssen angemeldet sein, um Lose zu kaufen.'; return; }
    // Zweiter Check direkt vor confirmPayment
    const ok2 = await fetchAvailability();
    if (!ok2) {
      // Falls Menge reduziert wurde, Intent neu holen
      if (clientSecret.value && lastIntentQuantity.value !== quantity.value) {
        await createIntent();
      }
      // Abbrechen damit Nutzer erneut bestätigt
      return;
    }
    if (!clientSecret.value || lastIntentQuantity.value !== quantity.value) { await createIntent(); }
    if (!stripe.value || !elements.value) return;
    loading.value = true;
    errorMessage.value = '';
    try {
        const raffleId = props.raffle.id;
        const raffleSlug = props.raffle.slug;
        const params = new URLSearchParams();
        params.append('raffle_id', raffleId);
        if (raffleSlug) params.append('raffle_slug', raffleSlug);
        finalizing.value = true;
        finalizingTimer = setTimeout(()=> { finalizingSlow.value = true; }, 2500);
        const { error } = await stripe.value.confirmPayment({
            elements: elements.value,
            confirmParams: { return_url: `${window.location.origin}/checkout/success?${params.toString()}` }
        });
        if (error) {
            errorMessage.value = error.message || 'Zahlung fehlgeschlagen';
            finalizing.value = false;
            clearTimeout(finalizingTimer); finalizingSlow.value = false;
        } else {
            successMessage.value = 'Zahlung erfolgreich! Lose werden erstellt...';
            emit('purchase', { quantity: quantity.value });
        }
    } catch (e) {
        errorMessage.value = 'Unbekannter Fehler';
        finalizing.value = false;
        clearTimeout(finalizingTimer); finalizingSlow.value = false;
    } finally {
        loading.value = false;
    }
};

watch(() => props.isOpen, async (open) => {
    if (!open) {
        clientSecret.value = null;
        elements.value = null;
        lastIntentQuantity.value = null;
        paymentStep.value = false;
        if (paymentElement.value) { try { paymentElement.value.unmount(); } catch (_) {} paymentElement.value = null; }
        if (qtyTimer) { clearTimeout(qtyTimer); qtyTimer = null; }
        currentIntentOpId++;
        finalizing.value = false; finalizingSlow.value = false; clearTimeout(finalizingTimer);
    } else {
        quantity.value = props.initialQuantity;
        errorMessage.value = '';
        successMessage.value = '';
    }
});

let qtyTimer = null;
watch(quantity, () => {
    if (!props.isOpen) return;
    if (!paymentStep.value) return;
    if (!clientSecret.value) return;
    if (loading.value || updatingIntent.value) return;
    if (qtyTimer) clearTimeout(qtyTimer);
    qtyTimer = setTimeout(() => { if (!props.isOpen) return; if (lastIntentQuantity.value !== quantity.value) { refreshIntent(); } }, 600);
});

const proceedToPayment = async () => { if (paymentStep.value) return; const ok = await fetchAvailability(); if (!ok) return; paymentStep.value = true; await createIntent(); };

onMounted(() => {});
onUnmounted(() => { if (qtyTimer) clearTimeout(qtyTimer); clearTimeout(finalizingTimer); });
</script>

<style scoped>
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
input[type="number"] { -moz-appearance: textfield; appearance: textfield; }
.modal-enter-active, .modal-leave-active { transition: opacity 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
@supports (backdrop-filter: blur(8px)) { .backdrop-blur-sm { backdrop-filter: blur(8px); } }
</style>
