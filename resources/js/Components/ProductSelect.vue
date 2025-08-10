<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { getImageUrl } from '@/utils/cdn';

const props = defineProps({
  products: { type: Array, required: true },
  modelValue: { type: [String, Number, null], default: '' },
  placeholder: { type: String, default: '-- wählen --' },
  pullZone: { type: String, default: null },
  disabled: { type: Boolean, default: false },
  showDescription: { type: Boolean, default: false }
});
const emit = defineEmits(['update:modelValue','change']);

const open = ref(false);
const query = ref('');
const highlighted = ref(-1);
const buttonRef = ref(null);
const listRef = ref(null);
const searchRef = ref(null);

const filtered = computed(()=> {
  if(!query.value) return props.products;
  const q = query.value.toLowerCase();
  return props.products.filter(p => p.name.toLowerCase().includes(q));
});

const selected = computed(()=> props.products.find(p=> p.id === props.modelValue) || null);

function thumb(p){
  if(!p || !p.thumbnail_path) return null;
  return getImageUrl(p.thumbnail_path, props.pullZone);
}

function toggle(){
  if(props.disabled) return;
  open.value = !open.value;
  if(open.value){
    nextTick(()=> searchRef.value && searchRef.value.focus());
  }
}
function close(){
  open.value = false;
  highlighted.value = -1;
  query.value = '';
}
function select(p){
  emit('update:modelValue', p ? p.id : '');
  emit('change', p || null);
  close();
  nextTick(()=> buttonRef.value && buttonRef.value.focus());
}

function onKey(e){
  if(!open.value && ['ArrowDown','ArrowUp','Enter',' '].includes(e.key)){
    e.preventDefault();
    toggle();
    return;
  }
  if(!open.value) return;
  if(e.key === 'Escape'){ e.preventDefault(); close(); return; }
  if(e.key === 'ArrowDown'){
    e.preventDefault();
    highlighted.value = Math.min(highlighted.value + 1, filtered.value.length - 1);
    ensureVisible();
  } else if(e.key === 'ArrowUp'){
    e.preventDefault();
    highlighted.value = Math.max(highlighted.value - 1, 0);
    ensureVisible();
  } else if(e.key === 'Enter'){
    e.preventDefault();
    if(highlighted.value >=0 && filtered.value[highlighted.value]) select(filtered.value[highlighted.value]);
  }
}
function ensureVisible(){
  nextTick(()=> {
    if(!listRef.value) return;
    const items = listRef.value.querySelectorAll('[data-option]');
    if(highlighted.value < 0 || highlighted.value >= items.length) return;
    const el = items[highlighted.value];
    const parent = listRef.value;
    if(el.offsetTop < parent.scrollTop){
      parent.scrollTop = el.offsetTop;
    } else if(el.offsetTop + el.offsetHeight > parent.scrollTop + parent.clientHeight){
      parent.scrollTop = el.offsetTop - parent.clientHeight + el.offsetHeight;
    }
  });
}

watch(() => props.modelValue, (v)=> {
  if(!v) return; // keep
});

onMounted(()=> {
  document.addEventListener('click', outsideClick);
});
function outsideClick(e){
  if(!open.value) return;
  if(buttonRef.value && buttonRef.value.contains(e.target)) return;
  const panel = dropdownPanel();
  if(panel && panel.contains(e.target)) return;
  close();
}
function dropdownPanel(){
  return document.getElementById(uidPanel.value);
}

const uid = Math.random().toString(36).slice(2);
const uidPanel = ref('product-select-panel-'+uid);

</script>
<template>
  <div class="relative" @keydown.stop.prevent="onKey">
    <button ref="buttonRef" type="button" class="w-full relative text-left rounded border bg-white px-2 py-2 text-sm flex items-center gap-2 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
      :aria-expanded="open" :aria-controls="uidPanel" @click="toggle" :disabled="disabled">
      <div v-if="selected" class="flex items-center gap-2 min-w-0">
        <img v-if="thumb(selected)" :src="thumb(selected)" :alt="selected.name" class="h-8 w-8 rounded object-cover border flex-shrink-0" />
        <div class="truncate">
          <div class="truncate font-medium">{{ selected.name }}</div>
          <div v-if="showDescription && selected.description" class="truncate text-[10px] text-gray-500">{{ selected.description }}</div>
        </div>
      </div>
      <span v-else class="text-gray-400">{{ placeholder }}</span>
      <span class="ml-auto text-xs text-gray-500">▾</span>
    </button>
    <div v-if="open" :id="uidPanel" class="absolute z-50 mt-1 w-full rounded border bg-white shadow-lg p-2 space-y-2" @click.stop>
      <input ref="searchRef" v-model="query" type="text" placeholder="Suchen..." class="w-full rounded border-gray-300 text-sm" @keydown.stop />
      <div ref="listRef" class="max-h-64 overflow-auto text-sm divide-y divide-gray-100">
        <div v-if="filtered.length === 0" class="py-4 text-center text-xs text-gray-500">Keine Treffer</div>
        <template v-else>
          <div v-for="(p,idx) in filtered" :key="p.id" data-option @mouseenter="highlighted=idx" @mouseleave="highlighted=-1" @click="select(p)"
               class="flex items-center gap-2 p-2 cursor-pointer select-none"
               :class="[ idx===highlighted ? 'bg-indigo-50' : '', p.id===modelValue ? 'bg-indigo-100' : '' ]">
            <img v-if="thumb(p)" :src="thumb(p)" :alt="p.name" class="h-10 w-10 rounded object-cover border" />
            <div class="min-w-0 flex-1">
              <div class="truncate font-medium">{{ p.name }}</div>
              <div v-if="showDescription && p.description" class="truncate text-[10px] text-gray-500">{{ p.description }}</div>
              <div v-if="p.default_tier" class="mt-0.5 inline-block rounded px-1.5 py-0.5 text-[10px] font-semibold ring-1 ring-inset"
                :class="{
                  'bg-green-50 text-green-700 ring-green-600/20': p.default_tier==='A',
                  'bg-blue-50 text-blue-700 ring-blue-600/20': p.default_tier==='B',
                  'bg-yellow-50 text-yellow-800 ring-yellow-600/20': p.default_tier==='C',
                  'bg-orange-50 text-orange-700 ring-orange-600/20': p.default_tier==='D',
                  'bg-red-50 text-red-700 ring-red-600/20': p.default_tier==='E',
                }">
                {{ p.default_tier }}
              </div>
            </div>
            <svg v-if="p.id===modelValue" class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          </div>
        </template>
      </div>
      <div class="flex justify-between gap-2 pt-1">
        <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="select(null)">Reset</button>
        <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="close">Schließen</button>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* Scrollbar subtle styling (optional) */
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
