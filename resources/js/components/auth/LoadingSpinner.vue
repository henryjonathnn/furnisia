<script setup lang="ts">
interface Props {
    size?: 'sm' | 'md' | 'lg';
    message?: string;
}

withDefaults(defineProps<Props>(), {
    size: 'md'
});

const getSizeClasses = (size: string) => {
    const sizes = {
        sm: 'h-4 w-4',
        md: 'h-6 w-6', 
        lg: 'h-8 w-8'
    };
    return sizes[size as keyof typeof sizes] || sizes.md;
};
</script>

<template>
    <div class="flex flex-col items-center justify-center gap-3">
        <div 
            class="animate-spin border-2 border-gray-300 border-t-black rounded-full"
            :class="getSizeClasses(size)"
        />
        <p v-if="message" class="text-sm text-gray-600">{{ message }}</p>
    </div>
</template>
