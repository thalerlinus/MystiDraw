<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed, ref } from 'vue';
import { getImageUrl } from '@/utils/cdn';
import ImagePreviewModal from '@/Components/ImagePreviewModal.vue';

const page = usePage();
const products = computed(()=> page.props.products);
const filters = computed(()=> page.props.filters || {});
// Bunny CDN base (pull zone) if provided from backend config
const bunnyPullZone = page.props.bunny?.pull_zone || null;
const buildThumb = (p) => getImageUrl(p.thumbnail_path, bunnyPullZone);

const showImagePreview = ref(false);
const previewImageUrl = ref(null);
const openImage = (url) => { if(!url) return; previewImageUrl.value = url; showImagePreview.value = true; };
</script>
<template>
  <Head title="Produkte" />
  <AdminLayout title="Produkte">
    <div class="mb-4 flex items-center gap-3">
      <form method="get" class="flex items-center gap-2">
        <input type="text" name="q" placeholder="Suche" class="rounded border-gray-300" :value="filters.q" />
      </form>
      <Link href="/admin/products/create" class="rounded bg-indigo-600 px-3 py-2 text-sm font-medium text-white">Neu</Link>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
            <tr>
              <th class="px-4 py-3 text-left w-20">Bild</th>
              <th class="px-4 py-3 text-left min-w-0">Name</th>
              <th class="px-4 py-3 text-left w-24">SKU</th>
              <th class="px-4 py-3 text-left w-20">Preis</th>
              <th class="px-4 py-3 text-left w-16">Stufe</th>
              <th class="px-4 py-3 text-left w-16">Status</th>
              <th class="px-4 py-3 text-center w-24">Aktionen</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white text-sm">
            <tr v-for="p in products.data" :key="p.id" class="hover:bg-gray-50 transition-colors">
              <!-- Product Image -->
              <td class="px-4 py-3">
                <div class="h-12 w-12 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100 border">
                  <img 
                    v-if="p.thumbnail_path" 
                    :src="buildThumb(p)" 
                    :alt="p.name"
                    class="h-full w-full object-cover cursor-pointer hover:opacity-80"
                    @click="openImage(buildThumb(p))"
                  />
                  <div v-else class="h-full w-full flex items-center justify-center">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
              </td>
              
              <!-- Product Name with Description Preview -->
              <td class="px-4 py-3">
                <div class="min-w-0 flex-1">
                  <p class="font-medium text-gray-900 truncate">{{ p.name }}</p>
                  <p v-if="p.description" class="text-xs text-gray-500 truncate mt-1" :title="p.description">
                    {{ p.description }}
                  </p>
                </div>
              </td>
              
              <!-- SKU -->
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                  {{ p.sku }}
                </span>
              </td>
              
              <!-- Price -->
              <td class="px-4 py-3 font-medium text-gray-900">
                €{{ parseFloat(p.base_cost).toFixed(2) }}
              </td>
              
              <!-- Default Tier -->
              <td class="px-4 py-3">
                <span v-if="p.default_tier" class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                      :class="{
                        'bg-green-50 text-green-700 ring-green-600/20': p.default_tier === 'A',
                        'bg-blue-50 text-blue-700 ring-blue-600/20': p.default_tier === 'B',
                        'bg-yellow-50 text-yellow-800 ring-yellow-600/20': p.default_tier === 'C',
                        'bg-orange-50 text-orange-700 ring-orange-600/20': p.default_tier === 'D',
                        'bg-red-50 text-red-700 ring-red-600/20': p.default_tier === 'E',
                      }">
                  {{ p.default_tier }}
                </span>
                <span v-else class="text-gray-400 text-xs">-</span>
              </td>
              
              <!-- Active Status -->
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium"
                      :class="p.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ p.active ? 'Aktiv' : 'Inaktiv' }}
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center space-x-2">
                  <Link :href="`/admin/products/${p.id}/edit`" 
                        class="inline-flex items-center rounded-md bg-white px-2 py-1 text-xs font-medium text-indigo-600 shadow-sm ring-1 ring-inset ring-indigo-300 hover:bg-indigo-50 transition-colors">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Bearbeiten
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <ImagePreviewModal :show="showImagePreview" :url="previewImageUrl" :alt="''" @close="showImagePreview=false" />
  </AdminLayout>
</template>
