<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed, ref } from 'vue';

const page = usePage();
const raffles = computed(()=> page.props.raffles);
const filters = computed(()=> page.props.filters || {});
</script>
<template>
  <Head title="Raffles" />
  <AdminLayout title="Raffles">
    <div v-if="$page.props.flash?.success" class="mb-4 rounded border border-green-300 bg-green-50 px-3 py-2 text-sm text-green-700">
      {{$page.props.flash.success}}
    </div>
    <div v-if="$page.props.flash?.error" class="mb-4 rounded border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700">
      {{$page.props.flash.error}}
    </div>
    <div class="mb-4 flex items-center gap-3">
      <form method="get" class="flex items-center gap-2">
        <select name="status" class="rounded border-gray-300" @change="$event.target.form.submit()" :value="filters.status">
          <option value="">Alle Status</option>
          <option value="draft">Draft</option>
          <option value="scheduled">Geplant</option>
          <option value="live">Live</option>
          <option value="paused">Pausiert</option>
          <option value="sold_out">Ausverkauft</option>
          <option value="finished">Beendet</option>
          <option value="archived">Archiviert</option>
        </select>
      </form>
      <Link href="/admin/raffles/create" class="rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white">Neu</Link>
    </div>
  <div class="rounded-lg bg-white shadow ring-1 ring-gray-100 overflow-visible">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Start</th>
            <th class="px-4 py-2 text-left">Ende</th>
            <th class="px-4 py-2 text-left">Tickets</th>
            <th class="px-4 py-2 text-left">Prizes</th>
            <th class="px-4 py-2 text-left">% Awarded</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="r in raffles.data" :key="r.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium">{{ r.name }}</td>
            <td class="px-4 py-2"><span class="rounded bg-gray-100 px-2 py-0.5 text-xs">{{ r.status }}</span></td>
            <td class="px-4 py-2">{{ r.starts_at || '-' }}</td>
            <td class="px-4 py-2">{{ r.ends_at || '-' }}</td>
            <td class="px-4 py-2">{{ r.admin_stats?.tickets_sold || 0 }}</td>
            <td class="px-4 py-2">
              <span class="inline-flex items-center gap-1">
                {{ r.admin_stats?.prizes_awarded || 0 }}/{{ r.admin_stats?.prizes_total || 0 }}
                <span v-if="r.admin_stats?.prizes_remaining > 0" class="text-xs text-amber-600">({{ r.admin_stats.prizes_remaining }} left)</span>
                <span v-else class="text-xs text-green-600">(done)</span>
                <span v-if="r.admin_stats?.tiers" class="relative group inline-block">
                  <span class="cursor-pointer text-indigo-600 text-xs border border-indigo-300 rounded px-1 select-none">Tiers ▾</span>
                  <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 transition ease-out duration-150 absolute left-0 top-full mt-1 z-50 bg-white border border-gray-200 rounded-md shadow-xl p-2 w-56 overflow-hidden">
                    <div class="font-semibold text-[11px] text-gray-500 mb-1 tracking-wide">PRIZE DISTRIBUTION</div>
                    <div class="divide-y divide-gray-100 max-h-56 overflow-auto pr-1">
                      <div v-for="(tier, key) in r.admin_stats.tiers" :key="key" class="flex items-center justify-between text-[12px] py-1">
                        <span class="font-medium">Tier {{ key }}</span>
                        <span class="tabular-nums">{{ tier.awarded }}/{{ tier.total }}</span>
                      </div>
                    </div>
                  </div>
                </span>
              </span>
            </td>
            <td class="px-4 py-2">{{ r.admin_stats?.prizes_awarded_percent ?? 0 }}%</td>
            <td class="px-4 py-2 text-right"><Link :href="`/admin/raffles/${r.id}/edit`" class="text-indigo-600 hover:underline">Bearbeiten</Link></td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
