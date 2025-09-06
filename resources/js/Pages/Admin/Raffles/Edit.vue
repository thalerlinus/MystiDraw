<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { getImageUrl } from '@/utils/cdn';
import ProductSelect from '@/Components/ProductSelect.vue';
import GiftTicketsForm from '@/Components/GiftTicketsForm.vue';

const page = usePage();
const raffle = page.props.raffle;
const categories = page.props.categories || [];
const products = page.props.products || [];
const bunnyPullZone = page.props.bunny?.pull_zone || null;
const productMap = computed(()=> Object.fromEntries(products.map(p=>[p.id,p])));

// Bereits in dieser Raffle ausgewählte Produkt-IDs
const selectedProductIds = computed(() => {
  return form.items.map(item => item.product_id).filter(id => id);
});

// Detail-Liste der aktuell ausgewählten Produkte
const selectedItemsDetailed = computed(()=> {
  return form.items
    .filter(it => it.product_id)
    .map(it => ({
      ...it,
      product: productMap.value[it.product_id] || null
    }));
});

// Gesamtanzahl (aufsummierte Stückzahlen) der ausgewählten Gewinne
const totalSelectedQuantity = computed(() => {
  return form.items
    .filter(it => it.product_id)
    .reduce((sum, it) => sum + (Number(it.quantity_total) || 0), 0);
});

function productThumb(p){
  if(!p || !p.thumbnail_path) return null;
  return getImageUrl(p.thumbnail_path, bunnyPullZone);
}
const form = useForm({
  name: raffle.name || '',
  slug: raffle.slug || '',
  category_id: raffle.category_id || '',
  starts_at: raffle.starts_at ? raffle.starts_at.substring(0,16) : '',
  ends_at: raffle.ends_at ? raffle.ends_at.substring(0,16) : '',
  base_ticket_price: raffle.base_ticket_price,
  currency: raffle.currency,
  status: raffle.status,
  pricing_tiers: (raffle.pricing_tiers || raffle.pricingTiers || []).map(t => ({ min_qty: t.min_qty, unit_price: t.unit_price })),
  items: (raffle.items || []).map(it => ({ product_id: it.product_id, tier: it.tier, quantity_total: it.quantity_total, weight: it.weight, is_last_one: !!it.is_last_one }))
});

function addTier(){
  form.pricing_tiers.push({ min_qty: '', unit_price: '' });
}
function removeTier(i){
  form.pricing_tiers.splice(i,1);
}
function addItem(){
  form.items.push({ product_id: '', tier: '', quantity_total: 1, weight: 1, is_last_one: false });
}
function removeItem(i){
  form.items.splice(i,1);
}

function onProductChange(index, product){
  const it = form.items[index];
  if(!it) return;
  // Beim Produktwechsel Tier immer auf das Default-Tier des Produkts setzen
  it.tier = (product && product.default_tier) ? product.default_tier : '';
}

function submit(){
  form.put(`/admin/raffles/${raffle.id}`);
}
</script>
<template>
  <Head :title="`Raffle bearbeiten - ${raffle.name}`" />
  <AdminLayout :title="`Raffle bearbeiten`">
    <div v-if="$page.props.flash?.success" class="mb-4 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-700">
      {{$page.props.flash.success}}
    </div>
    <div v-if="$page.props.flash?.error" class="mb-4 rounded border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">
      {{$page.props.flash.error}}
    </div>
    <form @submit.prevent="submit" class="grid gap-10 xl:grid-cols-2 items-start w-full">
      <!-- Linke Spalte -->
      <div class="space-y-8">
        <div class="bg-white rounded-lg border p-5 shadow-sm space-y-5">
          <div class="grid md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-600">*</span></label>
              <input v-model="form.name" type="text" required class="mt-1 w-full rounded border-gray-300" />
              <div v-if="form.errors.name" class="text-xs text-red-600 mt-1">{{ form.errors.name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Slug <span class="text-red-600">*</span></label>
              <input v-model="form.slug" type="text" required class="mt-1 w-full rounded border-gray-300" />
              <div v-if="form.errors.slug" class="text-xs text-red-600 mt-1">{{ form.errors.slug }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kategorie <span class="text-red-600">*</span></label>
              <select v-model="form.category_id" required class="mt-1 w-full rounded border-gray-300">
                <option value="">-- auswählen --</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <div v-if="form.errors.category_id" class="text-xs text-red-600 mt-1">{{ form.errors.category_id }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Start</label>
              <input v-model="form.starts_at" type="datetime-local" class="mt-1 w-full rounded border-gray-300" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Ende</label>
              <input v-model="form.ends_at" type="datetime-local" class="mt-1 w-full rounded border-gray-300" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Ticket Grundpreis <span class="text-red-600">*</span></label>
              <input v-model="form.base_ticket_price" type="number" step="0.01" required class="mt-1 w-full rounded border-gray-300" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Währung <span class="text-red-600">*</span></label>
              <input v-model="form.currency" type="text" required class="mt-1 w-full rounded border-gray-300" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Status <span class="text-red-600">*</span></label>
              <select v-model="form.status" required class="mt-1 w-full rounded border-gray-300">
                <option value="draft">Draft</option>
                <option value="scheduled">Geplant</option>
                <option value="live">Live</option>
                <option value="paused">Pausiert</option>
                <option value="sold_out">Ausverkauft</option>
                <option value="finished">Beendet</option>
                <option value="archived">Archiviert</option>
              </select>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg border p-5 shadow-sm">
          <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-sm">Staffelpreise (optional)</h3>
            <button type="button" @click="addTier" class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">+ Tier</button>
          </div>
          <div v-if="form.pricing_tiers.length === 0" class="text-xs text-gray-500 mb-2">Keine Staffel angelegt.</div>
          <div v-for="(t,i) in form.pricing_tiers" :key="i" class="grid grid-cols-5 gap-2 items-end mb-2">
            <div class="col-span-2">
              <label class="block text-[11px] font-medium">Mindestmenge</label>
              <input v-model.number="t.min_qty" type="number" min="1" class="w-full rounded border-gray-300 text-sm" />
            </div>
            <div class="col-span-2">
              <label class="block text-[11px] font-medium">Stückpreis</label>
              <input v-model="t.unit_price" type="number" step="0.01" min="0" class="w-full rounded border-gray-300 text-sm" />
            </div>
            <div class="flex items-center gap-2 pb-1">
              <button type="button" @click="removeTier(i)" class="text-red-600 text-xs hover:underline">Entfernen</button>
            </div>
          </div>
          <div v-if="form.errors['pricing_tiers']" class="text-xs text-red-600">{{ form.errors['pricing_tiers'] }}</div>
        </div>

        <div class="bg-white rounded-lg border p-5 shadow-sm">
          <h3 class="font-semibold text-sm mb-3">Aktionen</h3>
          <button :disabled="form.processing" class="rounded bg-indigo-600 px-4 py-2 text-white text-sm disabled:opacity-50">Speichern</button>
        </div>
      </div>

      <!-- Rechte Spalte -->
      <div class="space-y-8">
        <div class="bg-white rounded-lg border p-5 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-sm">Produkte / Gewinne</h3>
            <button type="button" @click="addItem" class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">+ Produkt</button>
          </div>

          <div v-if="selectedItemsDetailed.length" class="mb-5">
            <h4 class="text-[11px] uppercase tracking-wide text-gray-500 mb-2">Ausgewählt ({{ selectedItemsDetailed.length }} | Gesamt: {{ totalSelectedQuantity }})</h4>
            <div class="flex flex-wrap gap-3">
              <div v-for="si in selectedItemsDetailed" :key="si.product_id" class="flex items-center gap-2 pr-2 bg-gray-50 border rounded-md shadow-sm">
                <img v-if="si.product && si.product.thumbnail_path" :src="getImageUrl(si.product.thumbnail_path, bunnyPullZone)" class="h-10 w-10 object-cover rounded-l-md" />
                <div class="py-1 text-xs max-w-[120px] truncate">{{ si.product?.name || '—' }}</div>
                <button type="button" @click="removeItem(form.items.indexOf(si))" class="text-gray-400 hover:text-red-600 px-1">
                  <font-awesome-icon :icon="['fas','xmark']" class="w-3 h-3" />
                </button>
              </div>
            </div>
          </div>

          <div v-if="form.items.length === 0" class="text-xs text-gray-500">Noch keine Produkte hinzugefügt.</div>
          <div v-for="(it,i) in form.items" :key="i" class="mb-5 rounded-lg border p-3 bg-white shadow-sm relative">
            <div class="flex items-start gap-4">
              <div class="flex-1 space-y-2">
                <label class="block text-[11px] font-medium">Produkt <span class="text-red-600">*</span></label>
                <ProductSelect v-model="it.product_id" :products="products" :pull-zone="bunnyPullZone" :show-description="true" :current-selected-ids="selectedProductIds" :group-by-category="true" @change="onProductChange(i, $event)" />
              </div>
              <div class="w-40 space-y-2">
                <label class="block text-[11px] font-medium">Tier <span class="text-red-600">*</span></label>
                <select v-model="it.tier" required class="w-full rounded border-gray-300 text-sm">
                  <option value="">-</option>
                  <option v-for="t in ['A','B','C','D','E']" :key="t" :value="t">{{ t }}</option>
                </select>
                <label class="block text-[11px] font-medium">Anzahl <span class="text-red-600">*</span></label>
                <input v-model.number="it.quantity_total" type="number" min="1" required class="w-full rounded border-gray-300 text-sm" />
                <label class="block text-[11px] font-medium">Gewicht</label>
                <input v-model.number="it.weight" type="number" min="1" class="w-full rounded border-gray-300 text-sm" />
                <div class="flex items-center gap-2 pt-1">
                  <input v-model="it.is_last_one" type="checkbox" class="rounded border-gray-300" />
                  <span class="text-[11px]">Last One?</span>
                </div>
              </div>
            </div>
            <button type="button" @click="removeItem(i)" class="absolute top-2 right-2 text-xs text-red-600 hover:underline">Entfernen</button>
          </div>
          <div v-if="form.errors['items']" class="text-xs text-red-600">{{ form.errors['items'] }}</div>
        </div>
      </div>
    </form>

  <!-- Gift Tickets -->
  <div class="mt-12 border-t pt-8">
      <h2 class="text-lg font-semibold mb-4">Tickets verschenken</h2>
      <GiftTicketsForm :raffle-id="raffle.id" />
    </div>
  </AdminLayout>
</template>
