<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    processing: boolean;
    submitText?: string;
    processingText?: string;
    disabled?: boolean;
    tabindex?: number;
}

const props = withDefaults(defineProps<Props>(), {
    submitText: 'Submit',
    processingText: 'Processing...',
    disabled: false,
    tabindex: 10
});

const emit = defineEmits<{
    submit: [];
}>();

const buttonText = computed(() => {
    return props.processing ? props.processingText : props.submitText;
});

const isDisabled = computed(() => {
    return props.processing || props.disabled;
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <!-- Form fields slot -->
        <slot name="fields" />

        <!-- Submit button -->
        <Button 
            type="submit" 
            class="w-full !bg-black !text-white hover:!bg-gray-800 focus:!bg-gray-800 py-3 px-4 rounded-xl font-semibold transition-all duration-200 border-2 border-black shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed" 
            :tabindex="tabindex" 
            :disabled="isDisabled"
        >
            <LoaderCircle v-if="processing" class="h-5 w-5 animate-spin mr-2" />
            {{ buttonText }}
        </Button>

        <!-- Additional content slot (links, etc.) -->
        <slot name="footer" />
    </form>
</template>
