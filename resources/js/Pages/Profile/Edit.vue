<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
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
                </div>
            </div>
        </section>
    </MainLayout>
</template>
