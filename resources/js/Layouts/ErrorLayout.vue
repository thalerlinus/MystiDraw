<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Fehler',
    },
});
</script>

<template>
    <div>
        <Head :title="title" />
        <main>
            <slot />
        </main>
    </div>
</template>
