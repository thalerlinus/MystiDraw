<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
const page = usePage();
import axios from 'axios';

const props = defineProps({
  raffleId: { type: Number, required: true }
});

// User search dropdown state
const open = ref(false);
const query = ref('');
const highlighted = ref(-1);
const users = ref([]);
const loadingUsers = ref(false);
const selectedUser = ref(null);
const searchRef = ref(null);
const listRef = ref(null);
const buttonRef = ref(null);
const uidPanel = ref('user-select-panel-'+Math.random().toString(36).slice(2));
let searchCancelToken = null;
let debounceTimer = null;

const filtered = computed(()=> {
  if(!query.value) return users.value;
  const q = query.value.toLowerCase();
  return users.value.filter(u => (u.name?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q)));
});

function toggle(){
  open.value = !open.value;
  if(open.value){
    highlighted.value = -1;
    nextTick(()=> searchRef.value && searchRef.value.focus());
    if(users.value.length === 0) fetchUsers();
  }
}
function close(){ open.value = false; highlighted.value = -1; }
function selectUser(u){ selectedUser.value = u; close(); nextTick(()=> buttonRef.value && buttonRef.value.focus()); }
function ensureVisible(){
  nextTick(()=> {
    if(!listRef.value) return;
    const items = listRef.value.querySelectorAll('[data-option]');
    if(highlighted.value < 0 || highlighted.value >= items.length) return;
    const el = items[highlighted.value];
    const parent = listRef.value;
    if(el.offsetTop < parent.scrollTop){ parent.scrollTop = el.offsetTop; }
    else if(el.offsetTop + el.offsetHeight > parent.scrollTop + parent.clientHeight){ parent.scrollTop = el.offsetTop - parent.clientHeight + el.offsetHeight; }
  });
}
function onKey(e){
  if(!open.value && ['ArrowDown','ArrowUp','Enter',' '].includes(e.key)){ e.preventDefault(); toggle(); return; }
  if(!open.value) return;
  if(e.key === 'Escape'){ e.preventDefault(); close(); return; }
  if(e.key === 'ArrowDown'){ e.preventDefault(); highlighted.value = Math.min(highlighted.value + 1, filtered.value.length - 1); ensureVisible(); }
  else if(e.key === 'ArrowUp'){ e.preventDefault(); highlighted.value = Math.max(highlighted.value - 1, 0); ensureVisible(); }
  else if(e.key === 'Enter'){ e.preventDefault(); if(highlighted.value >=0 && filtered.value[highlighted.value]) selectUser(filtered.value[highlighted.value]); }
}

async function fetchUsers(){
  if(loadingUsers.value) return;
  loadingUsers.value = true;
  const q = query.value.trim();
  try {
    const { data } = await axios.get('/admin/users/search',{ params:{ q } });
    users.value = data;
  } catch(e){
    // swallow
  } finally { loadingUsers.value = false; }
}

watch(query, ()=>{
  if(debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(()=> fetchUsers(), 300);
});

onMounted(()=> { document.addEventListener('click', outsideClick); });
function outsideClick(e){
  if(!open.value) return;
  if(buttonRef.value && buttonRef.value.contains(e.target)) return;
  const panel = document.getElementById(uidPanel.value);
  if(panel && panel.contains(e.target)) return;
  close();
}

// Gift form state
const quantity = ref(1);
const processing = ref(false);
const message = ref(null);
const error = ref(null);

function submit(){
  error.value = null; message.value = null;
  if(!selectedUser.value){ error.value = 'Bitte zuerst einen Benutzer auswählen.'; return; }
  processing.value = true;
  router.post(`/admin/raffles/${props.raffleId}/gift`, { user_id: selectedUser.value.id, quantity: quantity.value }, {
    preserveScroll: true,
    onSuccess: () => { 
      message.value = 'Tickets verschenkt.'; 
      // Wenn an aktuellen User verschenkt -> Navbar Counter per Event/Reload aktualisieren
      if(page.props.auth?.user && selectedUser.value?.id === page.props.auth.user.id){
        // Trigger kleines Inertia Reload für Ticket Stats Endpoint consumer
        // Leichtgewichtig: einfach Axios Hit auf unopened endpoint (Navbar holt selbst nicht automatisch hier)
        fetch('/api/tickets/unopened-count').then(()=>{
          const ev = new CustomEvent('tickets-gifted-self');
          window.dispatchEvent(ev);
        });
      }
    },
    onError: (errs) => { error.value = Object.values(errs).join('\n'); },
    onFinish: () => { processing.value = false; }
  });
}
</script>
<template>
  <div class="rounded bg-white p-4 shadow space-y-4" @keydown.stop.prevent="onKey">
    <div class="text-sm text-gray-600">Erstelle kostenlose Tickets für einen Benutzer. Suche nach Name oder Email. Status wird als <code>gifted</code> markiert.</div>
    <div class="grid md:grid-cols-3 gap-4">
      <!-- User Select -->
      <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Benutzer</label>
        <div class="relative">
          <button ref="buttonRef" type="button" class="w-full relative text-left rounded border bg-white px-2 py-2 text-sm flex items-center gap-2 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                  :aria-expanded="open" :aria-controls="uidPanel" @click="toggle">
            <div v-if="selectedUser" class="flex flex-col min-w-0">
              <span class="font-medium truncate">{{ selectedUser.name || selectedUser.email }}</span>
              <span class="text-[10px] text-gray-500 truncate" v-if="selectedUser.email">{{ selectedUser.email }}</span>
            </div>
            <span v-else class="text-gray-400">Benutzer wählen...</span>
            <span class="ml-auto text-xs text-gray-500">▾</span>
          </button>
          <div v-if="open" :id="uidPanel" class="absolute z-50 mt-1 w-full rounded border bg-white shadow-lg p-2 space-y-2" @click.stop>
            <input ref="searchRef" v-model="query" type="text" placeholder="Suchen (Name oder Email)..." class="w-full rounded border-gray-300 text-sm" @keydown.stop />
            <div ref="listRef" class="max-h-64 overflow-auto text-sm divide-y divide-gray-100">
              <div v-if="loadingUsers" class="py-4 text-center text-xs text-gray-500">Lade...</div>
              <div v-else-if="filtered.length === 0" class="py-4 text-center text-xs text-gray-500">Keine Treffer</div>
              <template v-else>
                <div v-for="(u,idx) in filtered" :key="u.id" data-option @mouseenter="highlighted=idx" @mouseleave="highlighted=-1" @click="selectUser(u)"
                     class="p-2 cursor-pointer select-none space-y-0.5"
                     :class="[ idx===highlighted ? 'bg-emerald-50' : '', selectedUser?.id===u.id ? 'bg-emerald-100' : '' ]">
                  <div class="flex items-center justify-between gap-2">
                    <span class="font-medium truncate">{{ u.name || '(kein Name)' }}</span>
                    <svg v-if="selectedUser?.id===u.id" class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </div>
                  <div class="text-[11px] text-gray-500 truncate" v-if="u.email">{{ u.email }}</div>
                </div>
              </template>
            </div>
            <div class="flex justify-between gap-2 pt-1">
              <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="selectUser(null)">Reset</button>
              <button type="button" class="text-xs text-gray-500 hover:text-gray-700" @click="close">Schließen</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Quantity -->
      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Anzahl</label>
        <input v-model.number="quantity" type="number" min="1" max="1000" class="mt-1 w-full rounded border-gray-300 text-sm" />
      </div>
    </div>
    <div class="flex justify-end">
      <button :disabled="processing || !selectedUser || quantity < 1" @click="submit" class="rounded bg-emerald-600 px-4 py-2 text-white disabled:opacity-50 text-sm flex items-center gap-2">
        <span v-if="!processing">Verschenken</span>
        <span v-else class="flex items-center gap-1"><svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" stroke-width="4" d="M4 12a8 8 0 018-8"/></svg> Lädt...</span>
      </button>
    </div>
    <div v-if="message" class="text-sm text-green-600">{{ message }}</div>
    <div v-if="error" class="text-sm text-red-600 whitespace-pre-line">{{ error }}</div>
  </div>
</template>
<style scoped>
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
