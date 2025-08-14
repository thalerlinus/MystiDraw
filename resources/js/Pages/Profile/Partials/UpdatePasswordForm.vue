<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <form @submit.prevent="updatePassword" class="space-y-6">
            <div>
                <InputLabel for="current_password" value="Aktuelles Passwort" class="text-navy-900 font-semibold" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-2 block w-full rounded-xl border-navy-300 focus:border-gold-400 focus:ring-gold-400/20 bg-white/80"
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div>
                <InputLabel for="password" value="Neues Passwort" class="text-navy-900 font-semibold" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-2 block w-full rounded-xl border-navy-300 focus:border-gold-400 focus:ring-gold-400/20 bg-white/80"
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Passwort bestätigen"
                    class="text-navy-900 font-semibold"
                />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-2 block w-full rounded-xl border-navy-300 focus:border-gold-400 focus:ring-gold-400/20 bg-white/80"
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4 pt-4">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 px-8 py-3 rounded-xl font-semibold"
                >
                    <span v-if="form.processing" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Passwort aktualisieren...
                    </span>
                    <span v-else class="flex items-center">
                        <font-awesome-icon :icon="['fas', 'key']" class="mr-2" />
                        Passwort aktualisieren
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
                        Passwort aktualisiert!
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
