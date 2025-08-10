<script setup>
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { getImageUrl } from '@/utils/cdn';
import draggable from 'vuedraggable';

const page = usePage();
const product = page.props.product;
// Bunny CDN base (pull zone) if provided from backend config
const bunnyPullZone = page.props.bunny?.pull_zone || null;
const buildImg = (path) => getImageUrl(path, bunnyPullZone);
const form = useForm({ name: product.name, description: product.description, base_cost: product.base_cost, default_tier: product.default_tier, active: product.active });
function submit(){ form.put(`/admin/products/${product.id}`); }

// Image upload & management with new drag & drop
const uploading = ref(false);
const imageFiles = ref([]);

// Drag & drop configuration
const dragOptions = {
  animation: 200,
  group: 'images',
  disabled: false,
  ghostClass: 'ghost',
  chosenClass: 'sortable-chosen',
  dragClass: 'sortable-drag'
};

function onFilesSelected(e){
  const files = Array.from(e.target.files || []);
  if(!files.length) return;
  addNewFiles(files);
  e.target.value='';
}

function addNewFiles(files) {
  files.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      imageFiles.value.push({
        id: Date.now() + index,
        file: file,
        preview: e.target.result,
        name: file.name,
        isNew: true
      });
    };
    reader.readAsDataURL(file);
  });
}

function removeNewImage(index) {
  imageFiles.value.splice(index, 1);
}

function uploadNewImages() {
  if(imageFiles.value.length === 0) return;
  
  const data = new FormData();
  imageFiles.value.forEach(img => {
    if(img.isNew) {
      data.append('images[]', img.file);
    }
  });
  
  uploading.value = true;
  router.post(`/admin/products/${product.id}/images`, data, { 
    forceFormData: true, 
    onFinish: () => {
      uploading.value = false;
      imageFiles.value = [];
    }
  });
}

function onDrop(e) {
  e.preventDefault();
  const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
  if (files.length > 0) {
    addNewFiles(files);
  }
}

function onDragOver(e) {
  e.preventDefault();
}

function onDragLeave(e) {
  e.preventDefault();
}

// Existing image management
const sortedImages = ref([]);

// Initialize sorted images from product
function initializeSortedImages() {
  sortedImages.value = [...product.images].sort((a,b) => a.sort_order - b.sort_order);
}

// Call on component mount
initializeSortedImages();

function setPrimary(image){
  router.post(`/admin/products/${product.id}/images/${image.id}`, { _method:'PUT', is_primary: true }, { preserveScroll: true });
}
function updateAlt(image, alt){
  router.post(`/admin/products/${product.id}/images/${image.id}`, { _method:'PUT', alt }, { preserveScroll: true });
}
function removeImage(image){
  if(!confirm('Bild wirklich löschen?')) return;
  router.post(`/admin/products/${product.id}/images/${image.id}`, { _method:'DELETE' }, { preserveScroll: true });
}

// Handle drag end for existing images with automatic primary logic
function onExistingImagesDragEnd() {
  const newOrder = sortedImages.value.map(img => img.id);
  
  // Auto-set first image as primary
  const firstImage = sortedImages.value[0];
  if (firstImage && !firstImage.is_primary) {
    // Update primary status locally for immediate UI feedback
    sortedImages.value.forEach((img, index) => {
      img.is_primary = index === 0;
    });
    
    // Send primary update to server
    router.post(`/admin/products/${product.id}/images/${firstImage.id}`, { 
      _method:'PUT', 
      is_primary: true 
    }, { preserveScroll: true });
  }
  
  // Send reorder request
  router.post(`/admin/products/${product.id}/images/reorder`, { 
    _method:'PUT', 
    order: newOrder 
  }, { preserveScroll: true });
}
</script>
<template>
  <Head :title="`Produkt bearbeiten - ${product.name}`" />
  <AdminLayout title="Produkt bearbeiten">
    <form @submit.prevent="submit" class="space-y-6 max-w-3xl">
      <div>
        <label class="block text-sm font-medium">
          Name <span class="text-red-500">*</span>
        </label>
        <input 
          v-model="form.name" 
          class="mt-1 w-full rounded border-gray-300" 
          required
          maxlength="255"
        />
        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
      </div>
      <div>
        <label class="block text-sm font-medium">Beschreibung</label>
        <textarea 
          v-model="form.description" 
          class="mt-1 w-full rounded border-gray-300" 
          rows="4" 
        />
        <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</div>
      </div>
      <div class="grid grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium">
            Preis <span class="text-red-500">*</span>
          </label>
          <input 
            v-model="form.base_cost" 
            type="number" 
            step="0.01" 
            class="mt-1 w-full rounded border-gray-300" 
            required
            min="0"
          />
          <div v-if="form.errors.base_cost" class="mt-1 text-sm text-red-600">{{ form.errors.base_cost }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Standard-Stufe</label>
          <select v-model="form.default_tier" class="mt-1 w-full rounded border-gray-300">
            <option value="">--</option>
            <option v-for="t in ['A','B','C','D','E']" :key="t" :value="t">{{ t }}</option>
          </select>
          <div v-if="form.errors.default_tier" class="mt-1 text-sm text-red-600">{{ form.errors.default_tier }}</div>
        </div>
        <div>
          <label class="block text-sm font-medium">Aktiv</label>
          <select v-model="form.active" class="mt-1 w-full rounded border-gray-300">
            <option :value="true">Ja</option>
            <option :value="false">Nein</option>
          </select>
          <div v-if="form.errors.active" class="mt-1 text-sm text-red-600">{{ form.errors.active }}</div>
        </div>
      </div>
      <div>
        <div class="flex items-center justify-between">
          <button 
            :disabled="form.processing" 
            type="submit"
            class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
          >
            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ form.processing ? 'Speichert...' : 'Änderungen speichern' }}
          </button>
          
          <p class="text-xs text-gray-500">
            <span class="text-red-500">*</span> Pflichtfelder
          </p>
        </div>
      </div>
    </form>

    <div class="mt-12">
      <h2 class="mb-6 text-lg font-semibold">Bilder verwalten</h2>
      
      <!-- New Image Upload Section -->
      <div class="mb-8 rounded-lg border border-gray-200 p-6">
        <h3 class="mb-4 text-md font-medium text-gray-900">Neue Bilder hinzufügen</h3>
        
        <!-- Drag & Drop Zone -->
        <div 
          @drop="onDrop"
          @dragover="onDragOver"
          @dragleave="onDragLeave"
          @click="$refs.fileInput.click()"
          class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors mb-4 cursor-pointer"
        >
          <div class="space-y-2">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="text-sm text-gray-600">
              <span class="font-medium text-blue-600 hover:text-blue-500">Klicken Sie hier</span> oder ziehen Sie neue Bilder hierher
            </p>
            <p class="text-xs text-gray-500">PNG, JPG, GIF bis zu 5MB</p>
          </div>
          
          <input 
            type="file" 
            multiple 
            accept="image/*" 
            @change="onFilesSelected" 
            class="hidden" 
            ref="fileInput"
          />
        </div>
        
        <!-- New Images Preview -->
        <div v-if="imageFiles.length > 0" class="space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium text-gray-900">Neue Bilder ({{ imageFiles.length }})</h4>
            <button 
              @click="uploadNewImages"
              :disabled="uploading"
              class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm disabled:opacity-50 flex items-center"
            >
              <svg v-if="uploading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ uploading ? 'Hochladen...' : 'Bilder hochladen' }}
            </button>
          </div>
          
          <draggable 
            v-model="imageFiles" 
            v-bind="dragOptions"
            item-key="id"
            class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3"
          >
            <template #item="{ element, index }">
              <div class="relative group cursor-move bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <!-- Remove Button -->
                <button 
                  @click="removeNewImage(index)"
                  type="button"
                  class="absolute top-1 right-1 z-10 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                >
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
                
                <!-- Image Preview -->
                <div class="aspect-square">
                  <img 
                    :src="element.preview" 
                    :alt="element.name"
                    class="w-full h-full object-cover rounded-lg"
                  />
                </div>
                
                <!-- Image Info -->
                <div class="p-1">
                  <p class="text-xs text-gray-600 truncate" :title="element.name">
                    {{ element.name }}
                  </p>
                  <p class="text-xs text-green-600">Neu</p>
                </div>
              </div>
            </template>
          </draggable>
        </div>
      </div>

      <!-- Existing Images Section -->
      <div class="rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-md font-medium text-gray-900">Vorhandene Bilder ({{ product.images.length }})</h3>
          <div class="text-right">
            <p class="text-xs text-gray-500">Drag & Drop zum Sortieren</p>
            <p class="text-xs text-indigo-600">Das erste Bild wird automatisch zum Hauptbild</p>
          </div>
        </div>
        
        <div v-if="product.images.length===0" class="text-sm text-gray-500 text-center py-8">
          Noch keine Bilder vorhanden.
        </div>
        <draggable 
          v-else
          v-model="sortedImages" 
          v-bind="dragOptions"
          item-key="id"
          @end="onExistingImagesDragEnd"
          class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3"
          tag="ul"
        >
          <template #item="{ element: img, index }">
            <li :class="[
              'group relative rounded-lg border bg-white p-1 shadow-sm hover:shadow-md transition-all cursor-move',
              index === 0 || img.is_primary ? 'primary-border' : ''
            ]">
              <!-- Primary Badge -->
              <div v-if="img.is_primary || index === 0" class="absolute top-0.5 left-0.5 z-10 bg-indigo-600 text-white rounded px-1 py-0.5 text-xs font-semibold">
                #1
              </div>
              <!-- Drag Handle -->
              <div class="absolute top-0.5 right-0.5 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="h-3 w-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                </svg>
              </div>
              <div class="aspect-square w-full overflow-hidden rounded bg-gray-100">
                <img :src="buildImg(img.path)" :alt="img.alt || ''" class="h-full w-full object-cover" />
              </div>
              <div class="mt-1 space-y-1">
                <input 
                  :value="img.alt" 
                  @change="e=>updateAlt(img,e.target.value)" 
                  placeholder="Alt-Text" 
                  class="w-full rounded border-gray-300 px-1 py-0.5 text-xs" 
                />
                <div class="flex items-center justify-between gap-1">
                  <button 
                    v-if="index !== 0 && !img.is_primary" 
                    @click.prevent="setPrimary(img)" 
                    class="rounded bg-gray-200 hover:bg-gray-300 px-1 py-0.5 text-xs transition-colors"
                  >
                    #1
                  </button>
                  <span v-else-if="index === 0" class="text-xs text-indigo-600 font-medium">#{{ index + 1 }}</span>
                  <span v-else class="text-xs text-gray-500">#{{ index + 1 }}</span>
                  <button 
                    @click.prevent="removeImage(img)" 
                    class="text-xs text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-1 py-0.5 rounded transition-colors"
                  >
                    ×
                  </button>
                </div>
              </div>
            </li>
          </template>
        </draggable>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #f3f4f6;
  border: 2px dashed #9ca3af;
  transform: rotate(2deg);
}

.drag-area {
  transition: all 0.2s ease;
}

.drag-area.drag-over {
  border-color: #3b82f6;
  background-color: #dbeafe;
}

/* Drag handle for existing images */
.cursor-move {
  cursor: grab;
}

.cursor-move:active {
  cursor: grabbing;
}

/* Primary image highlighting */
.primary-border {
  border-color: #4f46e5;
  border-width: 2px;
}

/* Smooth transitions for reordering */
.sortable-ghost {
  opacity: 0.4;
}

.sortable-chosen {
  transform: rotate(2deg);
}

.sortable-drag {
  transform: rotate(5deg);
  z-index: 9999;
}
</style>
