<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import draggable from 'vuedraggable';

const form = useForm({ sku:'', name:'', description:'', base_cost:'', default_tier:'', images: [] });
const imageFiles = ref([]);
const dragOptions = {
  animation: 150,
  group: 'images',
  disabled: false,
  ghostClass: 'ghost'
};

function onFiles(e){
  const files = Array.from(e.target.files || []);
  addFiles(files);
}

function addFiles(files) {
  files.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      imageFiles.value.push({
        id: Date.now() + index,
        file: file,
        preview: e.target.result,
        name: file.name
      });
      updateFormImages();
    };
    reader.readAsDataURL(file);
  });
}

function removeImage(index) {
  imageFiles.value.splice(index, 1);
  updateFormImages();
}

function updateFormImages() {
  form.images = imageFiles.value.map(img => img.file);
}

function onDrop(e) {
  e.preventDefault();
  const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
  if (files.length > 0) {
    addFiles(files);
  }
}

function onDragOver(e) {
  e.preventDefault();
}

function onDragLeave(e) {
  e.preventDefault();
}

function submit(){
  form.post('/admin/products', { forceFormData: true });
}
</script>
<template>
  <Head title="Produkt anlegen" />
  <AdminLayout title="Produkt anlegen">
  <form @submit.prevent="submit" class="space-y-6 max-w-2xl" enctype="multipart/form-data">
      <div>
        <label class="block text-sm font-medium">
          SKU <span class="text-red-500">*</span>
        </label>
        <input 
          v-model="form.sku" 
          class="mt-1 w-full rounded border-gray-300" 
          required
          maxlength="64"
        />
        <div v-if="form.errors.sku" class="mt-1 text-sm text-red-600">{{ form.errors.sku }}</div>
      </div>
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
      </div>
      <div>
        <label class="block text-sm font-medium mb-2">Bilder (optional)</label>
        
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
              <span class="font-medium text-blue-600 hover:text-blue-500">Klicken Sie hier</span> oder ziehen Sie Bilder hierher
            </p>
            <p class="text-xs text-gray-500">PNG, JPG, GIF bis zu 10MB</p>
          </div>
          
          <input 
            type="file" 
            multiple 
            accept="image/*" 
            @change="onFiles" 
            class="hidden" 
            ref="fileInput"
          />
        </div>
        
        <!-- Image Preview with Drag & Drop Sorting -->
        <div v-if="imageFiles.length > 0" class="space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900">Bilder ({{ imageFiles.length }})</h3>
            <p class="text-xs text-gray-500">Ziehen Sie die Bilder, um die Reihenfolge zu ändern</p>
          </div>
          
          <draggable 
            v-model="imageFiles" 
            v-bind="dragOptions"
            @end="updateFormImages"
            item-key="id"
            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
          >
            <template #item="{ element, index }">
              <div class="relative group cursor-move bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                <!-- Drag Handle -->
                <div class="absolute top-2 left-2 z-10 bg-white bg-opacity-90 rounded p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                  </svg>
                </div>
                
                <!-- Remove Button -->
                <button 
                  @click="removeImage(index)"
                  type="button"
                  class="absolute top-2 right-2 z-10 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="p-2">
                  <p class="text-xs text-gray-600 truncate" :title="element.name">
                    {{ element.name }}
                  </p>
                  <p class="text-xs text-gray-400">
                    Position: {{ index + 1 }}
                  </p>
                </div>
              </div>
            </template>
          </draggable>
        </div>
        
        <!-- Image Errors -->
        <div v-if="form.errors.images" class="mt-1 text-sm text-red-600">{{ form.errors.images }}</div>
        <div v-if="form.errors['images.0']" class="mt-1 text-sm text-red-600">{{ form.errors['images.0'] }}</div>
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
            {{ form.processing ? 'Speichert...' : 'Produkt speichern' }}
          </button>
          
          <p class="text-xs text-gray-500">
            <span class="text-red-500">*</span> Pflichtfelder
          </p>
        </div>
      </div>
    </form>
  </AdminLayout>
</template>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #f3f4f6;
  border: 2px dashed #9ca3af;
}

.drag-area {
  transition: all 0.2s ease;
}

.drag-area.drag-over {
  border-color: #3b82f6;
  background-color: #dbeafe;
}
</style>
