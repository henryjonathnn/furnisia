<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

interface Props {
    modelValue: boolean;
    tabindex?: number;
}

withDefaults(defineProps<Props>(), {
    tabindex: 5
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
}>();
</script>

<template>
    <div class="flex items-center space-x-3">
        <Checkbox 
            id="remember" 
            :model-value="modelValue"
            @update:model-value="emit('update:modelValue', $event)"
            :tabindex="tabindex"
            class="h-5 w-5 text-black border-gray-300 rounded focus:ring-black focus:ring-2"
        />
        <Label for="remember" class="text-sm text-gray-700 cursor-pointer">
            Ingat saya
        </Label>
    </div>
</template>
