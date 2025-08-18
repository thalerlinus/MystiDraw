<script setup>
import { onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  json: { type: [Object, Array, String], required: true },
  id: { type: String, default: null }
});

let el;
onMounted(() => {
  const data = typeof props.json === 'string' ? props.json : JSON.stringify(props.json);
  // Remove existing with same id to avoid duplicates on Inertia navigations
  if (props.id) {
    const existing = document.head.querySelector(`script[data-jsonld-id="${props.id}"]`);
    if (existing) existing.remove();
  }
  el = document.createElement('script');
  el.type = 'application/ld+json';
  if (props.id) el.setAttribute('data-jsonld-id', props.id);
  el.textContent = data;
  document.head.appendChild(el);
});

onBeforeUnmount(() => {
  // Optional: keep structured data after navigation? If we want to remove, uncomment below
  // if (el && el.parentNode) el.parentNode.removeChild(el);
});
</script>

<template>
  <!-- Renders nothing -->
</template>
