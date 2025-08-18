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
    <form @submit.prevent="submit" class="space-y-6 max-w-3xl">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input v-model="form.name" type="text" class="mt-1 w-full rounded border-gray-300" />
        <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Slug</label>
        <input v-model="form.slug" type="text" class="mt-1 w-full rounded border-gray-300" />
        <div v-if="form.errors.slug" class="text-sm text-red-600">{{ form.errors.slug }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Kategorie</label>
        <select v-model="form.category_id" class="mt-1 w-full rounded border-gray-300">
          <option value="">-- auswählen --</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <div v-if="form.errors.category_id" class="text-sm text-red-600">{{ form.errors.category_id }}</div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Start</label>
          <input v-model="form.starts_at" type="datetime-local" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Ende</label>
          <input v-model="form.ends_at" type="datetime-local" class="mt-1 w-full rounded border-gray-300" />
        </div>
      </div>
      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Ticket Grundpreis</label>
          <input v-model="form.base_ticket_price" type="number" step="0.01" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Währung</label>
          <input v-model="form.currency" type="text" class="mt-1 w-full rounded border-gray-300" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select v-model="form.status" class="mt-1 w-full rounded border-gray-300">
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

      <div class="pt-4 border-t">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold text-sm">Staffelpreise (optional)</h3>
          <button type="button" @click="addTier" class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">+ Tier</button>
        </div>
        <div v-if="form.pricing_tiers.length === 0" class="text-xs text-gray-500">Keine Staffel angelegt.</div>
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

      <div class="pt-4 border-t">
        <div class="flex items-center justify-between mb-2">
          <h3 class="font-semibold text-sm">Produkte / Gewinne</h3>
          <button type="button" @click="addItem" class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">+ Produkt</button>
        </div>
        <div v-if="form.items.length === 0" class="text-xs text-gray-500">Noch keine Produkte hinzugefügt.</div>
        <div v-for="(it,i) in form.items" :key="i" class="grid md:grid-cols-12 gap-2 items-start mb-3 p-2 border rounded bg-white">
          <div class="md:col-span-5 space-y-1">
            <label class="block text-[11px] font-medium">Produkt</label>
            <ProductSelect v-model="it.product_id" :products="products" :pull-zone="bunnyPullZone" :show-description="true" />
          </div>
          <div class="md:col-span-1">
            <label class="block text-[11px] font-medium">Tier</label>
            <select v-model="it.tier" class="w-full rounded border-gray-300 text-sm">
              <option value="">-</option>
              <option v-for="t in ['A','B','C','D','E']" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-[11px] font-medium">Anzahl</label>
            <input v-model.number="it.quantity_total" type="number" min="1" class="w-full rounded border-gray-300 text-sm" />
          </div>
          <div class="md:col-span-2">
            <label class="block text-[11px] font-medium">Gewicht</label>
            <input v-model.number="it.weight" type="number" min="1" class="w-full rounded border-gray-300 text-sm" />
          </div>
          <div class="md:col-span-1 flex items-center gap-1 pt-5">
            <input v-model="it.is_last_one" type="checkbox" class="rounded border-gray-300" />
            <span class="text-[11px]">Last One?</span>
          </div>
          <div class="md:col-span-2 flex justify-end pt-5">
            <button type="button" @click="removeItem(i)" class="text-xs text-red-600 hover:underline">Entfernen</button>
          </div>
        </div>
        <div v-if="form.errors['items']" class="text-xs text-red-600">{{ form.errors['items'] }}</div>
      </div>

      <div>
        <button :disabled="form.processing" class="rounded bg-indigo-600 px-4 py-2 text-white disabled:opacity-50">Speichern</button>
      </div>
    </form>

    <!-- Gift Tickets -->
    <div class="mt-12 max-w-3xl border-t pt-8">
      <h2 class="text-lg font-semibold mb-4">Tickets verschenken</h2>
      <GiftTicketsForm :raffle-id="raffle.id" />
    </div>
  </AdminLayout>
</template>
