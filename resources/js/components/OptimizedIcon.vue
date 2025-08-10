<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';

interface Props {
  name: string;
  class?: string;
  size?: 'sm' | 'md' | 'lg' | 'xl';
  lazy?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  class: '',
  size: 'md',
  lazy: false
});

const isLoaded = ref(!props.lazy);

// Icon cache to prevent re-imports
const iconCache = new Map();

// Size classes
const sizeClasses = {
  sm: 'h-4 w-4',
  md: 'h-5 w-5',
  lg: 'h-6 w-6',
  xl: 'h-8 w-8'
};

const iconClass = computed(() => {
  return `${sizeClasses[props.size]} ${props.class}`;
});

// Dynamic icon import with caching
const loadIcon = async () => {
  if (iconCache.has(props.name)) {
    return iconCache.get(props.name);
  }

  try {
    let IconComponent;
    
    // Import from lucide-vue-next with caching
    const module = await import('lucide-vue-next');
    IconComponent = module[props.name];
    
    if (!IconComponent) {
      console.warn(`Icon "${props.name}" not found in lucide-vue-next`);
      return null;
    }
    
    iconCache.set(props.name, IconComponent);
    return IconComponent;
  } catch (error) {
    console.error(`Failed to load icon "${props.name}":`, error);
    return null;
  }
};

const IconComponent = ref(null);

onMounted(async () => {
  if (props.lazy) {
    // Use Intersection Observer for lazy loading
    const observer = new IntersectionObserver(async (entries) => {
      if (entries[0].isIntersecting) {
        IconComponent.value = await loadIcon();
        isLoaded.value = true;
        observer.disconnect();
      }
    });
    
    const element = document.querySelector(`[data-icon="${props.name}"]`);
    if (element) observer.observe(element);
  } else {
    IconComponent.value = await loadIcon();
  }
});
</script>

<template>
  <div
    v-if="lazy"
    :data-icon="name"
    :class="iconClass"
  >
    <component
      v-if="isLoaded && IconComponent"
      :is="IconComponent"
      :class="iconClass"
    />
    <!-- Loading placeholder -->
    <div
      v-else
      :class="iconClass"
      class="animate-pulse bg-gray-200 rounded"
    />
  </div>
  
  <component
    v-else-if="IconComponent"
    :is="IconComponent"
    :class="iconClass"
  />
</template>
