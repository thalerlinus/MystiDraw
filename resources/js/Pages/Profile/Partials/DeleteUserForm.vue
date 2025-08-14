<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <!-- Warning Info -->
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-rose-500 text-lg" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-rose-800 mb-2">Achtung: Unwiderrufliche Aktion</h3>
                    <p class="text-sm text-rose-700 leading-relaxed">
                        Sobald dein Konto gelöscht wird, werden alle deine Daten und Ressourcen dauerhaft gelöscht. 
                        Bitte lade alle Daten oder Informationen herunter, die du behalten möchtest, bevor du dein Konto löschst.
                    </p>
                </div>
            </div>
        </div>

        <DangerButton 
            @click="confirmUserDeletion"
            class="bg-rose-600 hover:bg-rose-700 hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 px-8 py-3 rounded-xl font-semibold"
        >
            <font-awesome-icon :icon="['fas', 'user-times']" class="mr-2" />
            Konto dauerhaft löschen
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal" max-width="2xl">
            <div class="p-8">
                <!-- Header with Icon -->
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center mr-4">
                        <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="text-rose-600 text-xl" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-navy-900">
                            Konto wirklich löschen?
                        </h2>
                        <p class="text-navy-600">Diese Aktion kann nicht rückgängig gemacht werden</p>
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl mb-6">
                    <p class="text-sm text-rose-700 leading-relaxed">
                        Sobald dein Konto gelöscht wird, werden alle deine Ressourcen und Daten dauerhaft gelöscht. 
                        Bitte gib dein Passwort ein, um zu bestätigen, dass du dein Konto dauerhaft löschen möchtest.
                    </p>
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <InputLabel
                        for="password"
                        value="Passwort zur Bestätigung"
                        class="text-navy-900 font-semibold"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-xl border-navy-300 focus:border-rose-400 focus:ring-rose-400/20 bg-white/80"
                        placeholder="••••••••"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <SecondaryButton 
                        @click="closeModal"
                        class="px-6 py-3 border-2 border-navy-300 text-navy-700 font-semibold rounded-xl hover:bg-navy-50 hover:border-navy-400 transition-all duration-300"
                    >
                        <font-awesome-icon :icon="['fas', 'times']" class="mr-2" />
                        Abbrechen
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-75': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="bg-rose-600 hover:bg-rose-700 hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 px-8 py-3 rounded-xl font-semibold"
                    >
                        <span v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Konto wird gelöscht...
                        </span>
                        <span v-else class="flex items-center">
                            <font-awesome-icon :icon="['fas', 'trash']" class="mr-2" />
                            Konto endgültig löschen
                        </span>
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
