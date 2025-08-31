<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { getImageUrl as buildImageUrl } from '@/utils/cdn';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';
import ImagePreviewModal from '@/Components/ImagePreviewModal.vue';

const page = usePage();
const categories = computed(()=> page.props.categories);
const all = computed(()=> page.props.allCategories || []);
const filters = computed(()=> page.props.filters || {});
const bunny = computed(()=> page.props.bunny || {});
const bunnyPullZone = computed(()=> bunny.value?.pull_zone || null);

// Helper function to create full CDN URLs (verhindert doppeltes https://)
const getImageUrl = (path) => buildImageUrl(path, bunnyPullZone.value);

const createForm = useForm({ name: '', parent_id: '', hero_image: null, hero_image_alt: '' });
const editForm = useForm({ id: null, name: '', parent_id: '', hero_image: null, hero_image_alt: '' });
// Quick product form (minimal)
const productForm = useForm({ sku: '', name: '', description: '', base_cost: '', default_tier: '' });

const showCreate = ref(false);
const showEdit = ref(false);
const showProduct = ref(false);
const currentEditCategory = ref(null);
const showImagePreview = ref(false);
const previewImageUrl = ref('');
const showDelete = ref(false);
const deleteForm = useForm({ id: null });

function openImagePreview(imageUrl) {
  if (!imageUrl) return;
  previewImageUrl.value = imageUrl;
  showImagePreview.value = true;
}

function openCreate(){
  createForm.reset();
  showCreate.value = true;
}
function openEdit(cat){
  currentEditCategory.value = cat;
  editForm.id = cat.id;
  editForm.name = cat.name;
  editForm.parent_id = cat.parent_id || '';
  editForm.hero_image = null;
  editForm.hero_image_alt = cat.hero_image_alt || '';
  showEdit.value = true;
}
function openDelete(cat){
  deleteForm.id = cat.id;
  currentEditCategory.value = cat;
  showDelete.value = true;
}
function openProduct(){
  productForm.reset();
  showProduct.value = true;
}
function submitCreate(){
  createForm.post('/admin/categories', {
    forceFormData: true,
    onSuccess: ()=> showCreate.value = false,
    preserveScroll: true,
    // ensure file is appended
    onBefore: () => {
      if (createForm.hero_image === null) {
        // nothing
      }
    },
  });
}
function submitEdit(){
  const hasFile = !!editForm.hero_image;
  // Transform: füge _method hinzu, entferne leeres File Feld wenn nicht nötig
  editForm.transform(data => {
    const payload = { ...data, _method: 'put' };
    if(!hasFile) delete payload.hero_image; // vermeidet leeres File Feld
    return payload;
  }).post('/admin/categories/' + editForm.id, {
    forceFormData: hasFile, // nur Multipart wenn wirklich Datei vorhanden
    preserveScroll: true,
    onSuccess: ()=> {
      showEdit.value = false;
      editForm.hero_image = null; // cleanup
    },
  });
}
function submitDelete(){
  if(!deleteForm.id) return;
  deleteForm.delete('/admin/categories/' + deleteForm.id, {
    preserveScroll: true,
    onSuccess: () => { showDelete.value = false; },
  });
}
function submitProduct(){
  productForm.post('/admin/products', {
    onSuccess: () => {
      // Weiterleitung erfolgt serverseitig zum Produkt-Edit; Modal schließen
      showProduct.value = false;
    },
  });
}

</script>
<template>
  <Head title="Kategorien" />
  <AdminLayout title="Kategorien">
    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-2">
        <form method="get" class="flex items-center gap-2">
          <input type="text" name="q" placeholder="Suche" class="rounded border-gray-300 h-9" :value="filters.q" />
          <button type="submit" class="inline-flex items-center gap-1 h-9 px-3 rounded-md bg-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            <span class="hidden sm:inline">Suchen</span>
          </button>
          <button v-if="filters.q" type="button" @click="$inertia.get('/admin/categories')" class="text-xs text-gray-500 hover:text-gray-700">Reset</button>
        </form>
        <div class="flex items-center gap-2">
          <button @click="openCreate" class="inline-flex items-center gap-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Kategorie
          </button>
          <button @click="openProduct" class="inline-flex items-center gap-1 rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Produkt
          </button>
        </div>
      </div>
      <div class="flex items-center gap-3 text-xs text-gray-500">
        <span>⌨ Schnell hinzufügen von Kategorien & Produkten</span>
      </div>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs font-medium uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-4 py-3 text-left">Bild</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Slug</th>
            <th class="px-4 py-3 text-left">Parent</th>
            <th class="px-4 py-3 text-center w-32">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-sm">
          <tr v-for="c in categories.data" :key="c.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 w-16">
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden">
                <img 
                  v-if="c.thumbnail_path" 
                  :src="getImageUrl(c.thumbnail_path)" 
                  :alt="c.hero_image_alt || c.name" 
                  class="w-full h-full object-cover cursor-pointer hover:opacity-80 transition-opacity" 
                  @click="openImagePreview(getImageUrl(c.hero_image_path || c.thumbnail_path))"
                />
                <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </td>
            <td class="px-4 py-2 font-medium text-gray-900">{{ c.name }}</td>
            <td class="px-4 py-2 text-xs text-gray-500">{{ c.slug }}</td>
            <td class="px-4 py-2 text-xs text-gray-600">{{ c.parent ? c.parent.name : '-' }}</td>
            <td class="px-4 py-2 text-center">
              <div class="flex justify-center gap-1">
                <Link :href="`/admin/categories/${c.id}/overview`" class="inline-flex items-center rounded bg-white px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-300 hover:bg-emerald-50">Übersicht</Link>
                <button @click="openEdit(c)" class="inline-flex items-center rounded bg-white px-2 py-1 text-xs font-medium text-indigo-600 ring-1 ring-indigo-300 hover:bg-indigo-50">Bearbeiten</button>
                <button @click="openDelete(c)" class="inline-flex items-center rounded bg-white px-2 py-1 text-xs font-medium text-red-600 ring-1 ring-red-300 hover:bg-red-50">Löschen</button>
              </div>
            </td>
          </tr>
          <tr v-if="!categories.data.length">
            <td class="px-4 py-6 text-center text-gray-500 text-sm" colspan="5">Keine Kategorien gefunden</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Modal -->
    <Modal :show="showCreate" @close="showCreate=false">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">Neue Kategorie</h2>
        <form @submit.prevent="submitCreate" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input v-model="createForm.name" type="text" class="w-full rounded border-gray-300" />
            <div v-if="createForm.errors.name" class="text-xs text-red-600 mt-1">{{ createForm.errors.name }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Parent</label>
            <select v-model="createForm.parent_id" class="w-full rounded border-gray-300 text-sm">
              <option value="">(Keine)</option>
              <option v-for="c in all" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <div v-if="createForm.errors.parent_id" class="text-xs text-red-600 mt-1">{{ createForm.errors.parent_id }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Hero Bild</label>
            <input type="file" @change="e=> createForm.hero_image = e.target.files[0]" accept="image/*" class="w-full text-sm" />
            <div v-if="createForm.errors.hero_image" class="text-xs text-red-600 mt-1">{{ createForm.errors.hero_image }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Hero Alt-Text</label>
            <input v-model="createForm.hero_image_alt" type="text" maxlength="255" class="w-full rounded border-gray-300" placeholder="Beschreibung für SEO/Barrierefreiheit" />
            <div v-if="createForm.errors.hero_image_alt" class="text-xs text-red-600 mt-1">{{ createForm.errors.hero_image_alt }}</div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showCreate=false" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-100">Abbrechen</button>
            <button type="submit" :disabled="createForm.processing" class="px-3 py-2 rounded text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50">Speichern</button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Modal -->
    <Modal :show="showEdit" @close="showEdit=false">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">Kategorie bearbeiten</h2>
        <form @submit.prevent="submitEdit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input v-model="editForm.name" type="text" class="w-full rounded border-gray-300" />
            <div v-if="editForm.errors.name" class="text-xs text-red-600 mt-1">{{ editForm.errors.name }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Parent</label>
            <select v-model="editForm.parent_id" class="w-full rounded border-gray-300 text-sm">
              <option value="">(Keine)</option>
              <option v-for="c in all.filter(x=> x.id !== editForm.id)" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <div v-if="editForm.errors.parent_id" class="text-xs text-red-600 mt-1">{{ editForm.errors.parent_id }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Hero Bild (ersetzen)</label>
            <!-- Aktuelles Bild anzeigen -->
            <div v-if="currentEditCategory?.hero_image_path" class="mb-3">
              <p class="text-xs text-gray-600 mb-2">Aktuelles Hero-Bild:</p>
              <div class="flex items-start gap-3">
                <img :src="getImageUrl(currentEditCategory.hero_image_path)" :alt="currentEditCategory.hero_image_alt || currentEditCategory.name" class="w-24 h-24 object-cover rounded-lg border" />
                <div class="flex-1 text-xs text-gray-500">
                  <p><strong>Alt-Text:</strong> {{ currentEditCategory.hero_image_alt || 'Kein Alt-Text' }}</p>
                  <p class="mt-1 break-all">{{ currentEditCategory.hero_image_path }}</p>
                </div>
              </div>
            </div>
            <input type="file" @change="e=> editForm.hero_image = e.target.files[0]" accept="image/*" class="w-full text-sm" />
            <p class="text-[11px] text-gray-500 mt-1">Leer lassen, um aktuelles Bild zu behalten.</p>
            <div v-if="editForm.errors.hero_image" class="text-xs text-red-600 mt-1">{{ editForm.errors.hero_image }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Hero Alt-Text</label>
            <input v-model="editForm.hero_image_alt" type="text" maxlength="255" class="w-full rounded border-gray-300" />
            <div v-if="editForm.errors.hero_image_alt" class="text-xs text-red-600 mt-1">{{ editForm.errors.hero_image_alt }}</div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showEdit=false" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-100">Abbrechen</button>
            <button type="submit" :disabled="editForm.processing" class="px-3 py-2 rounded text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50">Aktualisieren</button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Quick Product Modal -->
    <Modal :show="showProduct" @close="showProduct=false">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">Schnell: Produkt anlegen</h2>
        <form @submit.prevent="submitProduct" class="space-y-4">
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">SKU<span class="text-red-500">*</span></label>
              <input v-model="productForm.sku" type="text" required maxlength="64" class="w-full rounded border-gray-300" />
              <div v-if="productForm.errors.sku" class="text-xs text-red-600 mt-1">{{ productForm.errors.sku }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Preis (€)<span class="text-red-500">*</span></label>
              <input v-model="productForm.base_cost" type="number" step="0.01" min="0" required class="w-full rounded border-gray-300" />
              <div v-if="productForm.errors.base_cost" class="text-xs text-red-600 mt-1">{{ productForm.errors.base_cost }}</div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Name<span class="text-red-500">*</span></label>
            <input v-model="productForm.name" type="text" required maxlength="255" class="w-full rounded border-gray-300" />
            <div v-if="productForm.errors.name" class="text-xs text-red-600 mt-1">{{ productForm.errors.name }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Standard-Stufe</label>
            <select v-model="productForm.default_tier" class="w-full rounded border-gray-300 text-sm">
              <option value="">--</option>
              <option v-for="t in ['A','B','C','D','E']" :key="t" :value="t">{{ t }}</option>
            </select>
            <div v-if="productForm.errors.default_tier" class="text-xs text-red-600 mt-1">{{ productForm.errors.default_tier }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Beschreibung (optional)</label>
            <textarea v-model="productForm.description" rows="3" class="w-full rounded border-gray-300 text-sm"></textarea>
            <div v-if="productForm.errors.description" class="text-xs text-red-600 mt-1">{{ productForm.errors.description }}</div>
          </div>
          <p class="text-[11px] text-gray-500">Nach dem Speichern wirst du zum detaillierten Bearbeitungsformular weitergeleitet um Bilder hinzuzufügen.</p>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="showProduct=false" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-100">Abbrechen</button>
            <button type="submit" :disabled="productForm.processing" class="px-3 py-2 rounded text-sm font-medium text-white bg-blue-600 hover:bg-blue-500 disabled:opacity-50 flex items-center gap-2">
              <svg v-if="productForm.processing" class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
              <span>{{ productForm.processing ? 'Speichert...' : 'Produkt speichern' }}</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Image Preview Modal -->
    <ImagePreviewModal 
      :show="showImagePreview" 
      :url="previewImageUrl" 
      :alt="currentEditCategory?.hero_image_alt || currentEditCategory?.name || ''" 
      @close="showImagePreview=false" />

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDelete" @close="showDelete=false" max-width="md">
      <div class="p-6">
        <h2 class="text-lg font-semibold mb-4">Kategorie löschen</h2>
        <p class="text-sm text-gray-600 mb-4">
          Bist du sicher, dass du die Kategorie
          <span class="font-medium text-gray-900">{{ currentEditCategory?.name }}</span>
          löschen möchtest? Diese Aktion kann nicht rückgängig gemacht werden.
        </p>
        <div v-if="currentEditCategory?.hero_image_path" class="mb-4">
          <p class="text-xs text-gray-500 mb-2">Das zugehörige Bild (inkl. Thumbnail) wird ebenfalls vom CDN entfernt:</p>
          <img :src="getImageUrl(currentEditCategory.hero_image_path)" class="w-32 h-32 object-cover rounded border" />
        </div>
        <div class="flex justify-end gap-2">
          <button @click="showDelete=false" type="button" class="px-3 py-2 rounded text-sm text-gray-600 hover:bg-gray-100">Abbrechen</button>
          <button @click="submitDelete" :disabled="deleteForm.processing" class="px-3 py-2 rounded text-sm font-medium text-white bg-red-600 hover:bg-red-500 disabled:opacity-50 flex items-center gap-2">
            <svg v-if="deleteForm.processing" class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
            <span>{{ deleteForm.processing ? 'Löscht...' : 'Endgültig löschen' }}</span>
          </button>
        </div>
      </div>
    </Modal>

  </AdminLayout>
</template>
