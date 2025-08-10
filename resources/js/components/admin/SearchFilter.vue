<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface FilterOption {
    value: string | number;
    label: string;
}

interface FilterField {
    type: 'text' | 'select';
    key: string;
    label: string;
    placeholder?: string;
    options?: FilterOption[];
    icon?: string;
}

interface Props {
    fields: FilterField[];
    modelValue: Record<string, any>;
    loading?: boolean;
    showClearButton?: boolean;
    statsLabel?: string;
    statsValue?: string | number;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    showClearButton: true
});

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>];
    'search': [filters: Record<string, any>];
    'clear': [];
}>();

const localFilters = ref({ ...props.modelValue });

// Debouncing for text inputs
let searchTimeouts: Record<string, NodeJS.Timeout> = {};

// Watch for prop changes
watch(() => props.modelValue, (newVal) => {
    localFilters.value = { ...newVal };
}, { deep: true });

// Emit changes with debouncing for text inputs
const updateFilter = (key: string, value: any, fieldType: string = 'select') => {
    localFilters.value[key] = value;
    emit('update:modelValue', { ...localFilters.value });
    
    // Clear existing timeout for this field
    if (searchTimeouts[key]) {
        clearTimeout(searchTimeouts[key]);
    }
    
    // For text inputs, use debouncing
    if (fieldType === 'text') {
        searchTimeouts[key] = setTimeout(() => {
            emit('search', { ...localFilters.value });
        }, 500); // 500ms debounce
    } else {
        // For selects, emit immediately
        emit('search', { ...localFilters.value });
    }
};

const clearFilters = () => {
    localFilters.value = {};
    emit('update:modelValue', {});
    emit('clear');
};

const hasActiveFilters = computed(() => {
    return Object.values(localFilters.value).some(value => 
        value !== '' && value !== null && value !== undefined
    );
});

const getIconPath = (iconName: string) => {
    const icons = {
        search: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
        filter: 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z',
        user: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        status: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        chevron: 'M19 9l-7 7-7-7'
    };
    return icons[iconName as keyof typeof icons] || icons.search;
};
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Dynamic Filter Fields -->
            <template v-for="field in fields" :key="field.key">
                <!-- Text Input -->
                <div v-if="field.type === 'text'">
                    <label class="block text-sm font-medium text-black mb-2">{{ field.label }}</label>
                    <div class="relative">
                        <input
                            type="text"
                            :value="localFilters[field.key] || ''"
                            @input="updateFilter(field.key, ($event.target as HTMLInputElement).value, 'text')"
                            :placeholder="field.placeholder"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-all"
                        />
                        <svg 
                            class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                :d="getIconPath(field.icon || 'search')"
                            />
                        </svg>
                    </div>
                </div>

                <!-- Select Dropdown -->
                <div v-else-if="field.type === 'select'">
                    <label class="block text-sm font-medium text-black mb-2">{{ field.label }}</label>
                    <div class="relative">
                        <select
                            :value="localFilters[field.key] || ''"
                            @change="updateFilter(field.key, ($event.target as HTMLSelectElement).value, 'select')"
                            class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition-all bg-white cursor-pointer appearance-none"
                        >
                            <option value="">{{ field.placeholder || `Semua ${field.label}` }}</option>
                            <option 
                                v-for="option in field.options" 
                                :key="option.value" 
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('chevron')" />
                            </svg>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Stats Display -->
            <div v-if="statsLabel && statsValue !== undefined" class="flex items-end sm:col-span-2 lg:col-span-1">
                <div class="w-full p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-xs text-gray-500 mb-1">{{ statsLabel }}</div>
                    <div class="text-lg font-semibold text-black">{{ statsValue }}</div>
                </div>
            </div>
        </div>

        <!-- Clear Filters -->
        <div v-if="showClearButton && hasActiveFilters" class="mt-4">
            <button
                @click="clearFilters"
                class="text-sm text-gray-500 hover:text-gray-700 underline cursor-pointer transition-colors"
            >
                Clear all filters
            </button>
        </div>

        <!-- Loading Indicator -->
        <div v-if="loading" class="mt-4 flex items-center justify-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-black mr-2"></div>
            <span class="text-sm text-gray-500">Memuat data...</span>
        </div>
    </div>
</template>
