<template>
    <div class="mb-4 sm:mb-6 p-3 sm:p-5 bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-400 rounded-xl shadow-sm" v-if="showTimer && hasReservedTickets">
        <!-- Header mit Icon und Titel -->
        <div class="flex items-start space-x-2 sm:space-x-3 mb-3">
            <div class="flex-shrink-0 p-1.5 sm:p-2 bg-amber-100 rounded-full">
                <font-awesome-icon :icon="['fas', 'hourglass-half']" class="text-amber-600 text-base sm:text-lg animate-pulse" />
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-amber-800 text-base sm:text-lg leading-tight">Lose werden bald wieder verfügbar</h3>
                <p class="text-xs sm:text-sm text-amber-700/80 mt-1 leading-relaxed">
                    Andere Nutzer haben aktuell Lose reserviert, die noch nicht bezahlt wurden
                </p>
            </div>
        </div>

        <!-- Countdown und Anzahl -->
        <div class="bg-white/60 rounded-lg p-3 sm:p-4 border border-amber-200/50">
            <!-- Mobile: Gestapeltes Layout -->
            <div class="block sm:hidden space-y-3">
                <div class="text-center">
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-semibold text-amber-800">Nächste Freigabe in:</span>
                    </div>
                    <div class="font-mono font-black text-xl text-amber-700 bg-amber-100 px-4 py-2 rounded-lg">
                        {{ formatTime(timeRemaining) }}
                    </div>
                </div>
                
                <div class="flex items-center justify-center space-x-2 text-sm">
                    <span class="text-amber-700">Reservierte Lose:</span>
                    <div class="bg-amber-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                        {{ reservedTickets }}
                    </div>
                    <span class="text-amber-600 font-medium">
                        {{ reservedTickets === 1 ? 'Los' : 'Lose' }}
                    </span>
                </div>
            </div>

            <!-- Desktop: Nebeneinander Layout -->
            <div class="hidden sm:block">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></div>
                        <span class="font-semibold text-amber-800">Nächste Freigabe in:</span>
                    </div>
                    <div class="font-mono font-black text-2xl text-amber-700 bg-amber-100 px-3 py-1 rounded-lg">
                        {{ formatTime(timeRemaining) }}
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-sm">
                    <span class="text-amber-700">Reservierte Lose:</span>
                    <div class="flex items-center space-x-2">
                        <div class="bg-amber-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                            {{ reservedTickets }}
                        </div>
                        <span class="text-amber-600 font-medium">
                            {{ reservedTickets === 1 ? 'Los' : 'Lose' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Erklärungstext -->
        <div class="mt-3 text-xs sm:text-xs text-amber-600/90 bg-amber-50/50 rounded-lg p-2 sm:p-3 border border-amber-200/30">
            <div class="flex items-start space-x-2">
                <font-awesome-icon :icon="['fas', 'info-circle']" class="text-amber-500 mt-0.5 flex-shrink-0 text-xs sm:text-sm" />
                <div class="leading-relaxed">
                    <strong>Was bedeutet das?</strong> Wenn jemand Lose in den Warenkorb legt, werden sie für 
                    <span class="font-semibold">5 Minuten reserviert</span>. Falls die Zahlung nicht abgeschlossen wird, 
                    stehen die Lose automatisch wieder zum Kauf zur Verfügung.
                </div>
            </div>
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
    const total = Math.max(0, Math.floor(seconds));
    const minutes = Math.floor(total / 60);
    const remainingSeconds = total % 60;
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
            // API kann Float liefern -> aufrunden, damit Nutzer volle Sekunde sieht
            timeRemaining.value = Math.ceil(data.time_until_next_expiry || 0);
            
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
        timeRemaining.value = Math.max(0, timeRemaining.value - 1);
        if (timeRemaining.value <= 0) {
            stopCountdown();
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
