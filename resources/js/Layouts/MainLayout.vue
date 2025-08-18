<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { defineAsyncComponent, ref, onMounted } from 'vue';

// Lazy load heavier below-the-fold layout pieces
const Footer = defineAsyncComponent(() => import('@/Components/Footer.vue'));
const CookieConsent = defineAsyncComponent(() => import('@/Components/CookieConsent.vue'));
const showDeferred = ref(false);
onMounted(() => {
    requestAnimationFrame(() => {
        // Use requestIdleCallback when available with proper options; fallback to setTimeout otherwise
        if (typeof window.requestIdleCallback === 'function') {
            window.requestIdleCallback(() => { showDeferred.value = true; }, { timeout: 1200 });
        } else {
            setTimeout(() => { showDeferred.value = true; }, 800);
        }
    });
});

defineProps({
    title: {
        type: String,
        default: 'Willkommen',
    },
    user: {
        type: Object,
        default: null,
    },
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
    showFooter: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col overflow-x-hidden">
        <Head :title="title" />
        
        <!-- Navigation -->
        <Navbar 
            :user="user" 
            :can-login="canLogin" 
            :can-register="canRegister" 
        />

        <!-- Main Content with top padding for fixed navbar -->
        <main class="flex-grow pt-20 w-full overflow-x-hidden">
            <slot />
        </main>

        <!-- Footer -->
    <Footer v-if="showFooter && showDeferred" />
    <!-- Cookie Consent -->
    <CookieConsent v-if="showDeferred" />
    </div>
</template>
