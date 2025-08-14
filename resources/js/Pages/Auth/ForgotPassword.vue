<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <MainLayout title="Passwort vergessen" :show-footer="false">
        <Head title="Passwort vergessen" />

        <!-- Hero Background -->
        <div class="min-h-screen bg-gradient-to-br from-navy-900 via-navy-800 to-navy-700 relative overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-400/5 rounded-full animate-float"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-gold-400/3 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-gold-400/5 rounded-full animate-float" style="animation-delay: 1s;"></div>
            </div>

            <div class="relative flex min-h-screen flex-col items-center justify-center px-6 py-12">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="w-24 h-24 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center shadow-2xl animate-glow p-3">
                        <img src="/images/logo.webp" alt="MystiDraw Logo" class="w-full h-full object-contain filter drop-shadow-lg">
                    </div>
                </div>

                <!-- Main Card -->
                <div class="w-full max-w-md">
                    <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 border border-navy-200/20">
                        <!-- Header -->
                        <div class="text-center mb-8">
                            <div class="w-16 h-16 bg-gold-gradient rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <font-awesome-icon :icon="['fas', 'key']" class="text-2xl text-navy-900" />
                            </div>
                            <h1 class="text-3xl font-bold text-navy-900 mb-2">Passwort vergessen?</h1>
                            <p class="text-navy-600">Kein Problem! Wir senden dir einen Link zum Zurücksetzen.</p>
                        </div>

                        <!-- Description -->
                        <div class="mb-6 p-4 bg-navy-50 rounded-2xl border border-navy-100">
                            <p class="text-sm text-navy-600 text-center">
                                Gib deine E-Mail-Adresse ein und wir senden dir einen sicheren Link zum Zurücksetzen deines Passworts.
                            </p>
                        </div>

                        <!-- Status Message -->
                        <div v-if="status" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
                            <p class="text-sm font-medium text-emerald-600 flex items-center">
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="mr-2 text-emerald-500" />
                                {{ status }}
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="email" value="E-Mail-Adresse" class="text-navy-900 font-semibold" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-2 block w-full rounded-xl border-navy-300 focus:border-gold-400 focus:ring-gold-400/20 bg-white/80"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="deine@email.de"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <PrimaryButton
                                class="w-full justify-center bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 transform hover:scale-[1.02] transition-all duration-300 py-3 rounded-xl font-semibold"
                                :class="{ 'opacity-75': form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-navy-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Link wird gesendet...
                                </span>
                                <span v-else class="flex items-center">
                                    <font-awesome-icon :icon="['fas', 'paper-plane']" class="mr-2" />
                                    Link zum Zurücksetzen senden
                                </span>
                            </PrimaryButton>
                        </form>

                        <!-- Back to Login -->
                        <div class="mt-8 text-center">
                            <p class="text-navy-600">
                                Passwort wieder eingefallen?
                                <Link
                                    :href="route('login')"
                                    class="font-semibold text-gold-600 hover:text-gold-500 transition-colors ml-1"
                                >
                                    Zur Anmeldung
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="mt-8">
                    <Link
                        :href="route('home')"
                        class="flex items-center text-navy-200 hover:text-white transition-colors group"
                    >
                        <font-awesome-icon :icon="['fas', 'arrow-left']" class="mr-2 group-hover:-translate-x-1 transition-transform" />
                        Zurück zur Startseite
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
