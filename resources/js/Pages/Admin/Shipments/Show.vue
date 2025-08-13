<script setup>
import { Head, usePage, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { reactive, computed } from 'vue';
const page = usePage();
const shipment = computed(()=> page.props.shipment);
const form = useForm({
  status: shipment.value.status,
  carrier: shipment.value.carrier || '',
  tracking_number: shipment.value.tracking_number || '',
  tracking_url: shipment.value.tracking_url || ''
});
const flash = computed(()=> page.props.flash || {});
const statuses = ['draft','queued','label_printed','shipped','delivered','returned'];
function submit(){
  form.patch(`/admin/shipments/${shipment.value.id}`);
}
</script>
<template>
  <Head :title="`Shipment #${shipment.id}`" />
  <AdminLayout :title="`Shipment #${shipment.id}`">
    <div v-if="flash.message" class="mb-4 rounded border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
      {{ flash.message }}
    </div>
    <div class="mb-4">
      <Link href="/admin/shipments" class="text-sm text-indigo-600 hover:underline">← Zur Übersicht</Link>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="md:col-span-2 space-y-6">
        <div class="bg-white shadow rounded-lg p-4 border border-gray-100">
          <h2 class="text-sm font-semibold mb-3">Versand-Details</h2>
          <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
            <dt class="font-medium text-gray-500">Order</dt><dd>#{{ shipment.order_id || '-' }}</dd>
            <dt class="font-medium text-gray-500">Status</dt><dd><span class="px-2 py-0.5 bg-gray-100 rounded">{{ shipment.status }}</span></dd>
            <dt class="font-medium text-gray-500">User</dt><dd>#{{ shipment.user_id }}</dd>
            <dt class="font-medium text-gray-500">Items</dt><dd>{{ shipment.items.length }}</dd>
            <dt class="font-medium text-gray-500">Created</dt><dd>{{ shipment.created_at }}</dd>
            <dt class="font-medium text-gray-500">Shipped</dt><dd>{{ shipment.shipped_at || '-' }}</dd>
            <dt class="font-medium text-gray-500">Delivered</dt><dd>{{ shipment.delivered_at || '-' }}</dd>
          </dl>
        </div>
        <div class="bg-white shadow rounded-lg p-4 border border-gray-100">
          <h2 class="text-sm font-semibold mb-3">Items</h2>
          <table class="min-w-full text-xs">
            <thead>
              <tr class="text-gray-500 uppercase tracking-wide">
                <th class="text-left py-1">#</th>
                <th class="text-left py-1">UserItem</th>
                <th class="text-left py-1">Product</th>
                <th class="text-left py-1">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="(it,i) in shipment.items" :key="it.id" class="hover:bg-gray-50">
                <td class="py-1 pr-2">{{ i+1 }}</td>
                <td class="py-1 pr-4">#{{ it.user_item_id }}</td>
                <td class="py-1 pr-4">{{ it.user_item?.product?.name || '-' }}</td>
                <td class="py-1"><span class="px-2 py-0.5 rounded bg-gray-100">{{ it.user_item?.status }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="space-y-6">
        <div class="bg-white shadow rounded-lg p-4 border border-gray-100" v-if="shipment.address">
          <h2 class="text-sm font-semibold mb-3">Versandadresse</h2>
          <div class="text-xs leading-relaxed text-gray-700">
            <p><strong>{{ shipment.address.first_name }} {{ shipment.address.last_name }}</strong></p>
            <p>{{ shipment.address.street }} <span v-if="shipment.address.house_number">{{ shipment.address.house_number }}</span></p>
            <p v-if="shipment.address.address2">{{ shipment.address.address2 }}</p>
            <p>{{ shipment.address.postal_code }} {{ shipment.address.city }}</p>
            <p v-if="shipment.address.state">{{ shipment.address.state }}</p>
            <p>{{ shipment.address.country_code }}</p>
            <p v-if="shipment.address.phone" class="mt-1">📞 {{ shipment.address.phone }}</p>
          </div>
        </div>
  <form @submit.prevent="submit" class="bg-white shadow rounded-lg p-4 border border-gray-100 space-y-4">
          <h2 class="text-sm font-semibold">Aktualisieren</h2>
          <div class="space-y-1">
            <label class="text-xs font-medium text-gray-600">Status</label>
            <select v-model="form.status" class="w-full rounded border-gray-300 text-sm">
              <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <div class="space-y-1">
            <label class="text-xs font-medium text-gray-600">Carrier</label>
            <input v-model="form.carrier" class="w-full rounded border-gray-300 text-sm" />
          </div>
          <div class="space-y-1">
            <label class="text-xs font-medium text-gray-600">Tracking Nummer</label>
            <input v-model="form.tracking_number" class="w-full rounded border-gray-300 text-sm" />
          </div>
          <div class="space-y-1">
            <label class="text-xs font-medium text-gray-600">Tracking URL</label>
            <input v-model="form.tracking_url" class="w-full rounded border-gray-300 text-sm" />
          </div>
          <div v-if="form.hasErrors" class="text-[10px] text-red-600">
            <div v-for="(v,k) in form.errors" :key="k">{{ v }}</div>
          </div>
          <button :disabled="form.processing" class="w-full py-2 rounded text-sm font-medium text-white"
            :class="form.processing ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'">
            <span v-if="form.processing">Speichern...</span>
            <span v-else>Speichern</span>
          </button>
          <p class="text-[10px] text-gray-500">Beim Setzen auf 'shipped' werden zugehörige Items als shipped markiert.</p>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
