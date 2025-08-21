<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const processing = ref(false);
const error = ref('');
const success = ref('');

const toggleNewsletter = async (subscribe) => {
    if (processing.value) return;
    processing.value = true;
    error.value = '';
    success.value = '';
    try {
        if (subscribe) {
            const res = await axios.post('/api/newsletter/subscribe');
            // Preserve existing token if relation already existed (after re-subscribe)
            const existingToken = page.props.auth.user.newsletter_subscription?.unsubscribe_token;
            page.props.auth.user.newsletter_subscription = {
                ...(page.props.auth.user.newsletter_subscription || {}),
                subscribed_at: new Date().toISOString(),
                unsubscribed_at: null,
                unsubscribe_token: existingToken || page.props.auth.user.newsletter_subscription?.unsubscribe_token
            };
            success.value = 'Newsletter abonniert.';
        } else {
            const token = page.props.auth.user?.newsletter_subscription?.unsubscribe_token;
            if (token) {
                await axios.get(`/newsletter/unsubscribe/${token}`);
                page.props.auth.user.newsletter_subscription.unsubscribed_at = new Date().toISOString();
            } else if (page.props.auth.user.newsletter_subscription) {
                page.props.auth.user.newsletter_subscription.unsubscribed_at = new Date().toISOString();
            }
            success.value = 'Newsletter abbestellt.';
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'Aktion fehlgeschlagen.';
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <Head title="Profil" />

    <MainLayout title="Profil">
        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-navy-50 via-white to-gold-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-gold-gradient rounded-3xl flex items-center justify-center shadow-lg">
                            <font-awesome-icon :icon="['fas', 'user-cog']" class="text-3xl text-navy-900" />
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-navy-900 mb-4">Profil-Einstellungen</h1>
                    <p class="text-xl text-navy-600 max-w-2xl mx-auto">
                        Verwalte deine Kontodaten, Sicherheitseinstellungen und Präferenzen
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-12 bg-white">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="space-y-8">
                    <!-- Profile Information -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-navy-gradient rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'user']" class="text-gold-400 text-xl" />
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-navy-900">Profil-Informationen</h3>
                                <p class="text-navy-600">Aktualisiere deine Kontodaten und E-Mail-Adresse</p>
                            </div>
                        </div>
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>

                    <!-- Password Update -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-navy-100 p-8 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'lock']" class="text-white text-xl" />
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-navy-900">Passwort ändern</h3>
                                <p class="text-navy-600">Stelle sicher, dass dein Konto sicher ist</p>
                            </div>
                        </div>
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <!-- Delete Account -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-rose-200 p-8 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'user-times']" class="text-white text-xl" />
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-navy-900">Konto löschen</h3>
                                <p class="text-navy-600">Dein Konto und alle Daten dauerhaft entfernen</p>
                            </div>
                        </div>
                        <DeleteUserForm class="max-w-xl" />
                    </div>

                    <!-- Newsletter Subscription -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-amber-200 p-8 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center mr-4">
                                <font-awesome-icon :icon="['fas', 'envelope-open-text']" class="text-white text-xl" />
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-navy-900">Newsletter</h3>
                                <p class="text-navy-600">Erhalte Updates zu neuen Raffles & Gewinnspielen</p>
                            </div>
                        </div>
                        <div v-if="$page.props.auth?.user" class="space-y-4 max-w-xl">
                            <div v-if="$page.props.auth.user.newsletter_subscription && !$page.props.auth.user.newsletter_subscription.unsubscribed_at" class="p-4 rounded-xl border border-emerald-300 bg-emerald-50 flex items-start gap-3">
                                <font-awesome-icon :icon="['fas','check-circle']" class="text-emerald-600 mt-1" />
                                <div class="text-emerald-700 text-sm">
                                    Du hast den Newsletter abonniert und erhältst exklusive Infos. Danke! 📬
                                </div>
                            </div>
                            <div v-else class="p-4 rounded-xl border border-amber-300 bg-amber-50 flex items-start gap-3">
                                <font-awesome-icon :icon="['fas','bell']" class="text-amber-600 mt-1" />
                                <div class="text-amber-700 text-sm">
                                    Du verpasst aktuell neue Raffles & Aktionen. Abonniere jetzt, um auf dem Laufenden zu bleiben.
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <button
                                    v-if="!($page.props.auth.user.newsletter_subscription && !$page.props.auth.user.newsletter_subscription.unsubscribed_at)"
                                    @click="toggleNewsletter(true)"
                                    :disabled="processing"
                                    class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold shadow-md hover:shadow-lg transition disabled:opacity-60"
                                >
                                    <span v-if="!processing">Jetzt abonnieren</span>
                                    <span v-else class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>Speichere...</span>
                                </button>
                                <button
                                    v-else
                                    @click="toggleNewsletter(false)"
                                    :disabled="processing"
                                    class="px-6 py-3 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold shadow-md hover:shadow-lg transition disabled:opacity-60"
                                >
                                    <span v-if="!processing">Abbestellen</span>
                                    <span v-else class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>Speichere...</span>
                                </button>
                            </div>
                            <p class="text-xs text-navy-500">Du kannst dich jederzeit wieder abmelden. Kein Spam.</p>
                            <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
                            <p v-if="success" class="text-sm text-emerald-600">{{ success }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<style scoped>
/* ...existing code... */
</style>
