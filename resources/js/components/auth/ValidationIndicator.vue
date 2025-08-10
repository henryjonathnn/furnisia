<script setup lang="ts">
interface Props {
    validation: {
        valid: boolean | null;
        checking?: boolean;
    };
}

defineProps<Props>();
</script>

<template>
    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
        <!-- Loading spinner -->
        <div 
            v-if="validation.checking" 
            class="animate-spin h-4 w-4 border-2 border-gray-300 border-t-gray-600 rounded-full"
        />
        
        <!-- Success checkmark -->
        <svg 
            v-else-if="validation.valid === true" 
            class="h-4 w-4 text-green-500" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M5 13l4 4L19 7" 
            />
        </svg>
        
        <!-- Error X -->
        <svg 
            v-else-if="validation.valid === false" 
            class="h-4 w-4 text-red-500" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M6 18L18 6M6 6l12 12" 
            />
        </svg>
    </div>
</template>
