<script setup>
import Modal from '@/Components/Modal.vue';

const props = defineProps({
  show: { type: Boolean, default: false },
  url: { type: String, default: null },
  alt: { type: String, default: '' },
  title: { type: String, default: 'Bildvorschau' },
  openInNewTab: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

function close(){
  emit('close');
}
</script>
<template>
  <Modal :show="show" @close="close" max-width="4xl">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">{{ title }}</h2>
        <button @click="close" class="text-gray-400 hover:text-gray-600" aria-label="Schließen">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="flex justify-center" v-if="url">
        <img :src="url" :alt="alt" class="max-w-full max-h-[70vh] object-contain rounded-lg shadow" />
      </div>
      <div v-else class="text-center text-sm text-gray-500 py-12">Kein Bild verfügbar</div>
      <div class="flex justify-center mt-4 gap-3" v-if="url">
        <a v-if="openInNewTab" :href="url" target="_blank" rel="noopener" class="px-3 py-2 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-500">In neuem Tab</a>
        <button @click="close" class="px-3 py-2 rounded bg-gray-600 text-white text-sm hover:bg-gray-500">Schließen</button>
      </div>
      <div v-else class="flex justify-center mt-4">
        <button @click="close" class="px-3 py-2 rounded bg-gray-600 text-white text-sm hover:bg-gray-500">Schließen</button>
      </div>
    </div>
  </Modal>
</template>
