<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import ValidationIndicator from '@/components/auth/ValidationIndicator.vue';
import { computed } from 'vue';

interface Props {
    id: string;
    label: string;
    type?: string;
    placeholder?: string;
    modelValue: string;
    error?: string;
    required?: boolean;
    autofocus?: boolean;
    readonly?: boolean;
    autocomplete?: string;
    tabindex?: number | string;
    iconType?: 'email' | 'password' | 'user' | 'shield';
    validation?: {
        valid: boolean | null;
        message: string;
        checking?: boolean;
    };
    showForgotPassword?: boolean;
    forgotPasswordLink?: string;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'text',
    required: false,
    autofocus: false,
    readonly: false,
    tabindex: 1,
    iconType: 'user'
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const inputClasses = computed(() => {
    const baseClasses = 'pl-10 pr-4 py-3 border-2 rounded-xl bg-gray-50 focus:outline-none focus:bg-white transition-all';
    
    if (props.readonly) {
        return `${baseClasses} border-gray-200 bg-gray-100 text-gray-600 cursor-not-allowed`;
    }
    
    if (props.validation?.valid === null) {
        return `${baseClasses} border-gray-200 focus:ring-2 focus:ring-black focus:border-black`;
    }
    
    if (props.validation?.valid === true) {
        return `${baseClasses} border-green-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-green-50 pr-10`;
    }
    
    if (props.validation?.valid === false) {
        return `${baseClasses} border-red-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-red-50 pr-10`;
    }
    
    return baseClasses;
});

const iconClasses = computed(() => {
    if (props.validation?.valid === null) return 'text-gray-400';
    return props.validation?.valid ? 'text-green-400' : 'text-red-400';
});

const getIcon = (type: string) => {
    const icons = {
        email: 'M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207',
        password: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        user: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        shield: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    return icons[type as keyof typeof icons] || icons.user;
};
</script>

<template>
    <div class="space-y-2">
        <!-- Label with optional forgot password link -->
        <div class="flex items-center justify-between">
            <Label :for="id" class="text-sm font-semibold text-black">{{ label }}</Label>
            <a 
                v-if="showForgotPassword && forgotPasswordLink" 
                :href="forgotPasswordLink" 
                class="text-sm text-gray-600 hover:text-black transition-colors"
                :tabindex="Number(tabindex) + 10"
            >
                Lupa password?
            </a>
        </div>

        <!-- Input field with icon -->
        <div class="relative">
            <Input
                :id="id"
                :type="type"
                :required="required"
                :autofocus="autofocus"
                :readonly="readonly"
                :autocomplete="autocomplete"
                :tabindex="Number(tabindex)"
                :model-value="modelValue"
                @update:model-value="(value: any) => emit('update:modelValue', String(value))"
                :placeholder="placeholder"
                :class="inputClasses"
            />

            <!-- Left icon -->
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg 
                    class="h-5 w-5" 
                    :class="iconClasses"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path 
                        stroke-linecap="round" 
                        stroke-linejoin="round" 
                        stroke-width="2" 
                        :d="getIcon(iconType)"
                    />
                </svg>
            </div>

            <!-- Right validation indicator -->
            <ValidationIndicator 
                v-if="validation && (validation.valid !== null || validation.checking)"
                :validation="validation"
            />
        </div>

        <!-- Validation message -->
        <div 
            v-if="validation?.message" 
            :class="['text-xs font-medium', validation.valid ? 'text-green-600' : 'text-red-600']"
        >
            {{ validation.message }}
        </div>

        <!-- Error message -->
        <InputError :message="error" />
    </div>
</template>
