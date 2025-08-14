<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-6"
        >
            <div>
                <InputLabel for="name" value="Name" class="text-navy-900 font-semibold" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-2 block w-full rounded-xl border-navy-300 focus:border-gold-400 focus:ring-gold-400/20 bg-white/80"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Dein Name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-Mail-Adresse" class="text-navy-900 font-semibold" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-2 block w-full rounded-xl border-navy-300 focus:border-gold-400 focus:ring-gold-400/20 bg-white/80"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="deine@email.de"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-amber-500 text-lg" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                Deine E-Mail-Adresse ist noch nicht bestätigt.
                                <Link
                                    :href="route('verification.send')"
                                    method="post"
                                    as="button"
                                    class="font-semibold text-gold-600 hover:text-gold-500 underline transition-colors"
                                >
                                    Bestätigungs-E-Mail erneut senden.
                                </Link>
                            </p>
                            <div
                                v-show="status === 'verification-link-sent'"
                                class="mt-2 text-sm font-medium text-emerald-600 flex items-center"
                            >
                                <font-awesome-icon :icon="['fas', 'check-circle']" class="mr-2" />
                                Ein neuer Bestätigungslink wurde an deine E-Mail-Adresse gesendet.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="bg-gold-gradient hover:shadow-lg hover:shadow-gold-500/25 transform hover:scale-[1.02] transition-all duration-300 px-8 py-3 rounded-xl font-semibold"
                >
                    <span v-if="form.processing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-navy-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Speichern...
                    </span>
                    <span v-else class="flex items-center">
                        <font-awesome-icon :icon="['fas', 'save']" class="mr-2" />
                        Speichern
                    </span>
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-if="form.recentlySuccessful"
                        class="flex items-center text-sm text-emerald-600 font-medium"
                    >
                        <font-awesome-icon :icon="['fas', 'check']" class="mr-2" />
                        Gespeichert!
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
