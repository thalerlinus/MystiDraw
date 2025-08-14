<template>
    <!-- Debug: Timer immer anzeigen -->
    <div class="mb-4 p-3 bg-orange-50/20 backdrop-blur-sm border border-orange-300/30 rounded-xl">
        <div class="text-xs text-orange-200/60 mb-2">
            [DEBUG] showTimer: {{ showTimer }} | hasReservedTickets: {{ hasReservedTickets }} | reservedTickets: {{ reservedTickets }} | timeRemaining: {{ timeRemaining }}
        </div>
        <div class="text-xs text-orange-200/60 mb-2" v-if="lastApiResponse">
            [API] Reserved: {{ lastApiResponse.reserved_count }} | Time: {{ lastApiResponse.time_until_next_expiry }}s | Has: {{ lastApiResponse.has_reservations }}
        </div>
        <div class="text-xs text-orange-200/60 mb-2" v-if="lastApiResponse?.debug_info">
            [DEBUG] Order Age: {{ lastApiResponse.debug_info.oldest_order_age_seconds }}s | Calc Expiry: {{ lastApiResponse.debug_info.calculated_expiry_seconds }}s
        </div>
        <div class="text-xs text-orange-200/60 mb-2">
            [API] Raw response: {{ JSON.stringify(lastApiResponse) }}
        </div>
        
        <div v-if="showTimer && hasReservedTickets">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center space-x-2 text-orange-200">
                    <font-awesome-icon :icon="['fas', 'clock']" class="animate-pulse" />
                    <span class="font-semibold">Reservierte Lose werden frei in:</span>
                </div>
                <div class="font-mono font-bold text-orange-100 text-lg">
                    {{ formatTime(timeRemaining) }}
                </div>
            </div>
            <div class="mt-2 text-xs text-orange-200/80">
                <span class="font-semibold">{{ reservedTickets }}</span> Lose werden gerade reserviert - 
                diese könnten bald wieder verfügbar werden.
            </div>
        </div>
        
        <div v-else class="text-xs text-orange-200/60">
            Keine Reservierungen aktiv
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    raffleId: { type: [Number, String], required: true },
    pollInterval: { type: Number, default: 30000 }, // 30 seconds
});

const emit = defineEmits(['update:reserved', 'tickets-released']);

// Reactive data
const timeRemaining = ref(0);
const reservedTickets = ref(0);
const showTimer = ref(false);
const pollTimer = ref(null);
const countdownTimer = ref(null);
const lastApiResponse = ref(null);

// Computed properties
const hasReservedTickets = computed(() => reservedTickets.value > 0);

// Methods
const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const fetchReservedTicketsInfo = async () => {
    try {
        console.log(`[ReservationTimer] Fetching reservation status for raffle ${props.raffleId}`);
        const response = await axios.get(`/api/raffles/${props.raffleId}/reservation-status`);
        const data = response.data;
        
        // Speichere die rohe API-Antwort für Debug-Zwecke
        lastApiResponse.value = data;
        
        console.log('[ReservationTimer] API Response:', data);
        
        if (data.has_reservations) {
            reservedTickets.value = data.reserved_count;
            timeRemaining.value = data.time_until_next_expiry;
            
            console.log(`[ReservationTimer] Found ${data.reserved_count} reserved tickets, ${data.time_until_next_expiry}s remaining`);
            
            // Timer anzeigen wenn Reservierungen vorhanden sind, auch wenn timeRemaining 0 ist
            if (!showTimer.value) {
                showTimer.value = true;
                console.log('[ReservationTimer] Timer started');
            }
            
            // Countdown nur starten wenn noch Zeit übrig ist
            if (timeRemaining.value > 0) {
                startCountdown();
            }
            
            emit('update:reserved', reservedTickets.value);
        } else {
            console.log('[ReservationTimer] No reservations found');
            
            if (showTimer.value) {
                // Reservierungen sind abgelaufen - Event emittieren
                emit('tickets-released', reservedTickets.value);
                console.log('[ReservationTimer] Tickets released');
            }
            
            reservedTickets.value = 0;
            showTimer.value = false;
            stopCountdown();
        }
    } catch (error) {
        console.error('Fehler beim Abrufen der Reservierungsinformationen:', error);
        lastApiResponse.value = { error: error.message };
        // Bei Fehlern Timer ausblenden
        showTimer.value = false;
        reservedTickets.value = 0;
        stopCountdown();
    }
};

const startCountdown = () => {
    if (countdownTimer.value) {
        clearInterval(countdownTimer.value);
    }
    
    countdownTimer.value = setInterval(() => {
        timeRemaining.value--;
        
        if (timeRemaining.value <= 0) {
            stopCountdown();
            // Nach Ablauf erneut prüfen
            fetchReservedTicketsInfo();
        }
    }, 1000);
};

const stopCountdown = () => {
    if (countdownTimer.value) {
        clearInterval(countdownTimer.value);
        countdownTimer.value = null;
    }
};

const startPolling = () => {
    // Sofort einmal abrufen
    fetchReservedTicketsInfo();
    
    // Dann regelmäßig pollen
    pollTimer.value = setInterval(() => {
        fetchReservedTicketsInfo();
    }, props.pollInterval);
};

const stopPolling = () => {
    if (pollTimer.value) {
        clearInterval(pollTimer.value);
        pollTimer.value = null;
    }
};

// Lifecycle hooks
onMounted(() => {
    startPolling();
});

onUnmounted(() => {
    stopPolling();
    stopCountdown();
});

// Expose methods for manual refresh
defineExpose({
    refresh: fetchReservedTicketsInfo,
    startPolling,
    stopPolling
});
</script>

<style scoped>
/* Animation for the clock icon */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
