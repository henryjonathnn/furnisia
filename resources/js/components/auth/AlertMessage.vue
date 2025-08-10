<script setup lang="ts">
import { CheckCircle, XCircle, Info, AlertTriangle, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    message: string;
    type?: 'success' | 'error' | 'warning' | 'info';
    dismissible?: boolean;
    autoClose?: boolean;
    autoCloseDelay?: number;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'info',
    dismissible: false,
    autoClose: false,
    autoCloseDelay: 5000
});

const emit = defineEmits<{
    dismiss: [];
}>();

const isVisible = ref(true);

const getIcon = () => {
    const icons = {
        success: CheckCircle,
        error: XCircle,
        warning: AlertTriangle,
        info: Info
    };
    return icons[props.type];
};

const getClasses = () => {
    const classes = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800'
    };
    return classes[props.type];
};

const getIconClasses = () => {
    const classes = {
        success: 'text-green-500',
        error: 'text-red-500',
        warning: 'text-yellow-500',
        info: 'text-blue-500'
    };
    return classes[props.type];
};

const dismiss = () => {
    isVisible.value = false;
    emit('dismiss');
};

if (props.autoClose) {
    setTimeout(() => {
        dismiss();
    }, props.autoCloseDelay);
}

const IconComponent = getIcon();
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0 transform scale-95"
        enter-to-class="opacity-100 transform scale-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100 transform scale-100"
        leave-to-class="opacity-0 transform scale-95"
    >
        <div 
            v-if="isVisible"
            class="relative rounded-lg border-2 p-4 shadow-sm"
            :class="getClasses()"
        >
            <div class="flex items-start gap-3">
                <component 
                    :is="IconComponent" 
                    class="h-5 w-5 flex-shrink-0 mt-0.5"
                    :class="getIconClasses()"
                />
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ message }}</p>
                </div>
                <button
                    v-if="dismissible"
                    @click="dismiss"
                    class="flex-shrink-0 ml-2 rounded-md p-1 hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                    :class="getIconClasses()"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </div>
    </Transition>
</template>
