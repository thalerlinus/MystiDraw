<template>
    <div 
        v-if="isOpen" 
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click="closeModal"
    >
        <div 
            class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
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
                <!-- Quantity Selector -->
                <div class="mb-8">
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
                <div class="mb-8">
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
                <div v-if="nextTier && quantity < nextTier.min_qty" class="mb-8">
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

                <!-- Stripe Payment Element (stable container) -->
                <div class="mb-6">
                    <label class="block text-lg font-bold text-gray-900 mb-3">Zahlungsmethode</label>
                    <div id="payment-element" class="p-4 border-2 border-gray-200 rounded-xl relative min-h-[80px]">
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

                <div v-if="errorMessage" class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm font-semibold border border-red-200">{{ errorMessage }}</div>
                <div v-if="successMessage" class="mb-4 p-3 rounded bg-green-50 text-green-700 text-sm font-semibold border border-green-200">{{ successMessage }}</div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button
                        @click="closeModal"
                        class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all duration-200"
                    >
                        Abbrechen
                    </button>
                    
                    <button
                        @click="purchaseTickets"
                        :disabled="loading || quantity <= 0 || quantity > raffle.tickets_available"
                        class="flex-1 px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 disabled:from-gray-300 disabled:to-gray-300 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 disabled:transform-none disabled:cursor-not-allowed shadow-lg"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <font-awesome-icon :icon="['fas', loading ? 'spinner' : 'shopping-cart']" :class="loading ? 'animate-spin' : ''" />
                            <span v-if="!loading">{{ quantity }} Lose kaufen</span>
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
import { ref, computed, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { loadStripe } from '@stripe/stripe-js';

const props = defineProps({
    raffle: { type: Object, required: true },
    isOpen: { type: Boolean, default: false }
});

const emit = defineEmits(['close', 'purchase']);

// Reactive data
const quantity = ref(1);

// Quick select amounts
const quickSelectAmounts = computed(() => {
    const amounts = [1, 5, 10];
    
    // Add tier minimum quantities
    if (props.raffle.pricing_tiers) {
        props.raffle.pricing_tiers.forEach(tier => {
            if (!amounts.includes(tier.min_qty) && tier.min_qty <= props.raffle.tickets_available) {
                amounts.push(tier.min_qty);
            }
        });
    }
    
    // Add max available if reasonable
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
        .pop(); // Get the highest tier that applies
});

const nextTier = computed(() => {
    return sortedTiers.value.find(tier => quantity.value < tier.min_qty);
});

const unitPrice = computed(() => {
    return currentTier.value ? currentTier.value.unit_price : props.raffle.base_ticket_price;
});

const subtotal = computed(() => {
    return quantity.value * unitPrice.value;
});

const totalSavings = computed(() => {
    if (!currentTier.value) return 0;
    return quantity.value * (props.raffle.base_ticket_price - currentTier.value.unit_price);
});

const totalPrice = computed(() => {
    return subtotal.value;
});

// Methods
const formatPrice = (price) => {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: props.raffle.currency || 'EUR'
    }).format(price);
};

const validateQuantity = () => {
    if (quantity.value < 1) {
        quantity.value = 1;
    } else if (quantity.value > props.raffle.tickets_available) {
        quantity.value = props.raffle.tickets_available;
    }
};

const increaseQuantity = () => {
    if (quantity.value < props.raffle.tickets_available) {
        quantity.value++;
    }
};

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
    }
};

const closeModal = () => {
    emit('close');
};

const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const clientSecret = ref(null);
const stripe = ref(null);
const elements = ref(null);
const paymentElement = ref(null);
const lastIntentQuantity = ref(null);
// Track auto update state
const updatingIntent = ref(false);
const page = usePage();

const initStripeElements = async (opId) => {
    if (!props.isOpen) return; // modal must be open
    if (!stripe.value) {
        stripe.value = await loadStripe(page.props.stripe.key);
    }
    if (clientSecret.value && stripe.value) {
        // Wait for DOM to reflect clientSecret v-if
        await nextTick();
        const container = document.getElementById('payment-element');
        if (!container) return; // container not rendered (maybe modal closing)
    // Skip if a newer intent op started
    if (opId !== currentIntentOpId) return;
        // Unmount previous if exists
        if (paymentElement.value) {
            try { paymentElement.value.unmount(); } catch (_) {}
            paymentElement.value = null;
        }
    // Clear container to avoid residual nodes Stripe might append twice
    container.innerHTML = '';
        elements.value = stripe.value.elements({ clientSecret: clientSecret.value });
        const paymentEl = elements.value.create('payment');
        paymentEl.mount(container);
        paymentElement.value = paymentEl;
    }
};

let currentIntentOpId = 0;
const createIntent = async () => {
    if (!props.isOpen) return; // don't create when modal closed
    if (loading.value) return; // prevent overlap
    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';
    try {
    const opId = ++currentIntentOpId;
    const { data } = await axios.post(`/raffles/${props.raffle.id}/purchase/intent`, { quantity: quantity.value });
    window.__lastOrderId = data.order_id;
    // If modal closed or obsolete op, abort quietly
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

const refreshIntent = async () => {
    if (!props.isOpen) return;
    updatingIntent.value = true;
    try {
        await createIntent();
    } finally {
        updatingIntent.value = false;
    }
};

// No auto intent recreation on quantity change; user picks final quantity then presses purchase.

const purchaseTickets = async () => {
    // (Re)create intent only if none exists yet OR quantity changed since last intent
    if (!clientSecret.value || lastIntentQuantity.value !== quantity.value) {
        await createIntent();
    }
    if (!stripe.value || !elements.value) return;
    loading.value = true;
    errorMessage.value = '';
    try {
        const raffleId = props.raffle.id;
        const raffleSlug = props.raffle.slug;
        const params = new URLSearchParams();
        if (window.__lastOrderId) params.append('order_id', window.__lastOrderId);
        params.append('raffle_id', raffleId);
        if (raffleSlug) params.append('raffle_slug', raffleSlug);
        const { error } = await stripe.value.confirmPayment({
            elements: elements.value,
            confirmParams: {
                return_url: `${window.location.origin}/checkout/success?${params.toString()}`,
            }
        });
        if (error) {
            errorMessage.value = error.message || 'Zahlung fehlgeschlagen';
        } else {
            successMessage.value = 'Zahlung erfolgreich! Lose werden erstellt...';
            emit('purchase', { quantity: quantity.value });
        }
    } catch (e) {
        errorMessage.value = 'Unbekannter Fehler';
    } finally {
        loading.value = false;
    }
};

watch(() => props.isOpen, async (open) => {
    if (!open) {
        clientSecret.value = null;
        elements.value = null;
        lastIntentQuantity.value = null;
        if (paymentElement.value) {
            try { paymentElement.value.unmount(); } catch (_) {}
            paymentElement.value = null;
        }
    if (qtyTimer) { clearTimeout(qtyTimer); qtyTimer = null; }
    currentIntentOpId++; // invalidate pending ops
    } else {
        // reset state and immediately create an intent for initial quantity
        quantity.value = 1;
        errorMessage.value = '';
        successMessage.value = '';
        await createIntent();
    }
});

// Debounced automatic intent refresh when quantity changes (after initial intent exists)
let qtyTimer = null;
watch(quantity, () => {
    if (!props.isOpen) return;
    if (!clientSecret.value) return; // no intent yet
    if (loading.value || updatingIntent.value) return; // avoid stacking
    if (qtyTimer) clearTimeout(qtyTimer);
    qtyTimer = setTimeout(() => {
        if (!props.isOpen) return; // modal closed during debounce
        if (lastIntentQuantity.value !== quantity.value) {
            refreshIntent();
        }
    }, 600); // 600ms debounce
});
</script>

<style scoped>
/* Custom number input styling */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}

/* Smooth animations */
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(8px)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(8px);
    }
}
</style>
