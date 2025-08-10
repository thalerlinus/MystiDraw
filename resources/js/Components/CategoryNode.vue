<template>
  <div v-if="node" :style="{ marginLeft: depth * 12 + 'px' }" class="py-1">
    <div class="flex items-center group">
      <button 
        v-if="hasChildren" 
        @click="$emit('toggle', node.slug)" 
        class="w-6 h-6 flex items-center justify-center text-slate-500 hover:text-yellow-400 transition-colors"
      >
        <font-awesome-icon 
          :icon="['fas', expanded ? 'chevron-down' : 'chevron-right']" 
          class="w-3 h-3" 
        />
      </button>
      <div v-else class="w-6 h-6"></div>
      
      <button 
        @click="$emit('select', node.slug)" 
        :class="[
          'flex-1 text-left text-sm px-3 py-2 rounded-lg font-medium transition-all duration-200 group-hover:shadow-sm', 
          selected === node.slug 
            ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900 shadow-md transform scale-[1.02]' 
            : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900'
        ]"
      >
        {{ node.name }}
      </button>
    </div>
    <transition name="fade" mode="out-in">
      <div v-if="expanded" class="mt-2 space-y-1 border-l-2 border-slate-200 ml-3 pl-3">
        <CategoryNode
          v-for="child in node.children"
          :key="child.slug || child.id"
          :node="child"
          :selected="selected"
          :depth="depth + 1"
          :expanded="isExpanded(child.slug)"
          @toggle="$emit('toggle', $event)"
          @select="$emit('select', $event)"
          :is-expanded="isExpanded"
        />
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  node: { type: Object, required: true },
  selected: { type: String, default: null },
  depth: { type: Number, default: 0 },
  expanded: { type: Boolean, default: false },
  isExpanded: { type: Function, required: true }
});

defineEmits(['toggle', 'select']);

const hasChildren = computed(() => Array.isArray(props.node.children) && props.node.children.length > 0);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { 
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
}
.fade-enter-from, .fade-leave-to { 
  opacity: 0; 
  transform: translateY(-8px); 
}
</style>
