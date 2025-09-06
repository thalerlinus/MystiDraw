<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { getImageUrl } from '@/utils/cdn';

const props = defineProps({
  products: { type: Array, required: true },
  modelValue: { type: [String, Number, null], default: '' },
  placeholder: { type: String, default: '-- wählen --' },
  pullZone: { type: String, default: null },
  disabled: { type: Boolean, default: false },
  showDescription: { type: Boolean, default: false },
  excludeIds: { type: Array, default: () => [] }, // Ausgeschlossene Produkt-IDs
  currentSelectedIds: { type: Array, default: () => [] }, // Aktuell in der Raffle bereits ausgewählte IDs
  groupByCategory: { type: Boolean, default: false }, // Optional nach Kategorie gruppieren
  showKeyboardHints: { type: Boolean, default: true } // Tastatur-Hinweise anzeigen
});
const emit = defineEmits(['update:modelValue','change']);

const open = ref(false);
const query = ref('');
const highlighted = ref(-1);
const buttonRef = ref(null);
const listRef = ref(null);
const searchRef = ref(null);

const filtered = computed(()=> {
  let results = props.products;
  
  // Filter nach Suchtext
  if(query.value) {
    const q = query.value.toLowerCase();
    results = results.filter(p => p.name.toLowerCase().includes(q));
  }
  
  // Bereits in anderen Raffles verwendete Produkte ausschließen (außer das aktuell ausgewählte)
  if(props.excludeIds.length > 0) {
    results = results.filter(p => 
      !props.excludeIds.includes(p.id) || 
      p.id === props.modelValue
    );
  }
  
  // Bereits in der aktuellen Raffle ausgewählte Produkte ausschließen (außer das aktuell ausgewählte)
  if(props.currentSelectedIds.length > 0) {
    results = results.filter(p => 
      !props.currentSelectedIds.includes(p.id) || 
      p.id === props.modelValue
    );
  }
  
  return results;
});

// Gruppierte Produkte (optional)
const grouped = computed(()=> {
  if(!props.groupByCategory) return {};
  const groups = {};
  for(const p of filtered.value){
    const key = (p.category && (p.category.name || p.category.title)) || p.category_name || p.category_id || 'Andere';
    if(!groups[key]) groups[key] = [];
    groups[key].push(p);
  }
  return Object.fromEntries(Object.keys(groups).sort().map(k => [k, groups[k]]));
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

function escapeHtml(str){
  return (str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function highlightName(name){
  if(!query.value) return escapeHtml(name);
  const q = query.value.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
  return escapeHtml(name).replace(new RegExp(`(${q})`, 'ig'), '<mark>$1</mark>');
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
    <button ref="buttonRef" type="button" class="w-full relative text-left rounded border bg-white px-4 py-4 text-sm flex items-center gap-4 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 min-h-[120px]"
      :aria-expanded="open" :aria-controls="uidPanel" @click="toggle" :disabled="disabled">
      <div v-if="selected" class="flex items-center gap-3 min-w-0 w-full">
        <img v-if="thumb(selected)" :src="thumb(selected)" :alt="selected.name" class="h-24 w-24 rounded-lg object-cover border shadow-sm flex-shrink-0" />
        <div class="flex-1 min-w-0 space-y-1">
          <div class="font-medium text-base leading-snug break-words">{{ selected.name }}</div>
          <div v-if="showDescription && selected.description" class="text-xs text-gray-500 line-clamp-3">{{ selected.description }}</div>
        </div>
      </div>
      <span v-else class="text-gray-400">{{ placeholder }}</span>
      <span class="ml-auto text-xs text-gray-500">▾</span>
    </button>
    <div v-if="open" :id="uidPanel" class="absolute z-50 mt-1 w-[1100px] max-w-[97vw] rounded border bg-white shadow-2xl p-5 space-y-4" @click.stop role="listbox" :aria-activedescendant="highlighted>=0 && filtered[highlighted] ? 'opt-'+filtered[highlighted].id : null">
      <div class="relative">
        <input ref="searchRef" v-model="query" type="text" placeholder="Suchen..." class="w-full rounded border-gray-300 text-sm py-2 pl-3 pr-9" @keydown.stop aria-label="Produkte suchen" />
        <button v-if="query" type="button" @click="query=''; highlighted=-1" class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 hover:text-gray-600" aria-label="Suche löschen">✕</button>
      </div>
      <div ref="listRef" class="max-h-[650px] overflow-auto text-sm pr-1 space-y-6" :class="groupByCategory ? '' : 'grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 space-y-0'">
        <div v-if="filtered.length === 0" class="py-8 text-center text-xs text-gray-500">Keine Treffer</div>
        <template v-else>
          <template v-if="groupByCategory">
            <div v-for="(items,cat) in grouped" :key="cat" class="space-y-3">
              <h4 class="text-[11px] uppercase tracking-wide font-semibold text-gray-500 pl-1">{{ cat }} <span class="text-gray-300">/ {{ items.length }}</span></h4>
              <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div v-for="p in items" :key="p.id" :id="'opt-'+p.id" data-option @mouseenter="highlighted=filtered.indexOf(p)" @mouseleave="highlighted=-1" @click="select(p)"
                     class="group relative flex flex-col gap-2 p-3 cursor-pointer select-none rounded-lg border hover:border-indigo-300 hover:shadow transition-all duration-150 bg-white"
                     :class="[
                       p.id===modelValue ? 'ring-2 ring-indigo-400 border-indigo-400' : 'border-gray-200',
                       (highlighted>=0 && filtered[highlighted] && filtered[highlighted].id===p.id) ? 'ring-2 ring-indigo-200' : ''
                     ]" role="option" :aria-selected="p.id===modelValue">
                  <div class="relative">
                    <img v-if="thumb(p)" :src="thumb(p)" :alt="p.name" class="h-40 w-full object-cover rounded-md border shadow-sm" />
                    <div v-if="p.default_tier" class="absolute top-2 left-2 rounded-md px-2 py-1 text-[11px] font-semibold ring-1 ring-inset backdrop-blur-sm bg-white/80"
                         :class="{
                           'text-green-700 ring-green-600/20': p.default_tier==='A',
                           'text-blue-700 ring-blue-600/20': p.default_tier==='B',
                           'text-yellow-800 ring-yellow-600/20': p.default_tier==='C',
                           'text-orange-700 ring-orange-600/20': p.default_tier==='D',
                           'text-red-700 ring-red-600/20': p.default_tier==='E',
                         }">Tier {{ p.default_tier }}</div>
                    <svg v-if="p.id===modelValue" class="absolute top-2 right-2 h-6 w-6 text-indigo-600 drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <div class="min-w-0 flex-1 flex flex-col">
                    <div class="font-medium text-sm leading-snug line-clamp-2" v-html="highlightName(p.name)"></div>
                    <div v-if="showDescription && p.description" class="text-xs text-gray-600 mt-1 line-clamp-2">{{ p.description }}</div>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <div v-for="(p,idx) in filtered" :key="p.id" :id="'opt-'+p.id" data-option @mouseenter="highlighted=idx" @mouseleave="highlighted=-1" @click="select(p)"
                 class="group relative flex flex-col gap-2 p-3 cursor-pointer select-none rounded-lg border hover:border-indigo-300 hover:shadow transition-all duration-150 bg-white"
                 :class="[
                   idx===highlighted ? 'ring-2 ring-indigo-200' : '',
                   p.id===modelValue ? 'ring-2 ring-indigo-400 border-indigo-400' : 'border-gray-200'
                 ]" role="option" :aria-selected="p.id===modelValue">
              <div class="relative">
                <img v-if="thumb(p)" :src="thumb(p)" :alt="p.name" class="h-40 w-full object-cover rounded-md border shadow-sm" />
                <div v-if="p.default_tier" class="absolute top-2 left-2 rounded-md px-2 py-1 text-[11px] font-semibold ring-1 ring-inset backdrop-blur-sm bg-white/80"
                     :class="{
                       'text-green-700 ring-green-600/20': p.default_tier==='A',
                       'text-blue-700 ring-blue-600/20': p.default_tier==='B',
                       'text-yellow-800 ring-yellow-600/20': p.default_tier==='C',
                       'text-orange-700 ring-orange-600/20': p.default_tier==='D',
                       'text-red-700 ring-red-600/20': p.default_tier==='E',
                     }">Tier {{ p.default_tier }}</div>
                <svg v-if="p.id===modelValue" class="absolute top-2 right-2 h-6 w-6 text-indigo-600 drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              </div>
              <div class="min-w-0 flex-1 flex flex-col">
                <div class="font-medium text-sm leading-snug line-clamp-2" v-html="highlightName(p.name)"></div>
                <div v-if="showDescription && p.description" class="text-xs text-gray-600 mt-1 line-clamp-2">{{ p.description }}</div>
              </div>
            </div>
          </template>
        </template>
      </div>
      <div class="space-y-2 pt-3 border-t">
        <div v-if="showKeyboardHints && filtered.length" class="flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-gray-400">
          <span><strong class="text-gray-500">↑/↓</strong> Navigieren</span>
          <span><strong class="text-gray-500">Enter</strong> Auswählen</span>
          <span><strong class="text-gray-500">Esc</strong> Schließen</span>
          <span><strong class="text-gray-500">Tab</strong> Fokus verlassen</span>
        </div>
        <div class="flex justify-between gap-2">
            <button type="button" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1 rounded hover:bg-gray-100" @click="select(null)">Reset</button>
            <button type="button" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1 rounded hover:bg-gray-100" @click="close">Schließen</button>
        </div>
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

/* Line clamp for description */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
mark { background: #fde68a; color: #92400e; padding: 0 2px; border-radius: 2px; }
</style>
