<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';

interface Props {
    show: boolean;
    title: string;
    description?: string;
    icon?: string;
    iconColor?: string;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
    closeable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    maxWidth: 'lg',
    closeable: true,
    iconColor: 'blue'
});

const emit = defineEmits<{
    close: [];
}>();

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
});

const getMaxWidthClass = () => {
    const classes = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-2xl',
        xl: 'max-w-4xl',
        '2xl': 'max-w-6xl'
    };
    return classes[props.maxWidth];
};

const getIconClass = () => {
    const classes = {
        blue: 'from-blue-500 to-blue-600 text-white',
        green: 'from-green-500 to-green-600 text-white',
        orange: 'from-orange-500 to-orange-600 text-white',
        red: 'from-red-500 to-red-600 text-white',
        purple: 'from-purple-500 to-purple-600 text-white',
        gray: 'from-gray-100 to-gray-200 text-gray-700'
    };
    return classes[props.iconColor as keyof typeof classes] || classes.blue;
};

const getIconPath = (iconName: string) => {
    const icons = {
        create: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        edit: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        delete: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        view: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    return icons[iconName as keyof typeof icons] || icons.info;
};
</script>

<template>
    <!-- Modal Overlay -->
    <Teleport to="body">
        <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="show"
                class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4"
                @click="close"
            >
                <!-- Modal Container -->
                <Transition
                    enter-active-class="duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div 
                        v-if="show"
                        class="bg-white rounded-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto"
                        :class="getMaxWidthClass()"
                        @click.stop
                    >
                        <!-- Modal Header -->
                        <div class="relative border-b border-gray-100 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div 
                                        v-if="icon"
                                        class="h-12 w-12 rounded-xl bg-gradient-to-br flex items-center justify-center shadow-lg"
                                        :class="getIconClass()"
                                    >
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(icon)" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-black">{{ title }}</h3>
                                        <p v-if="description" class="text-sm text-gray-500">{{ description }}</p>
                                    </div>
                                </div>
                                <button 
                                    v-if="closeable"
                                    @click="close" 
                                    class="p-2 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer"
                                >
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-6">
                            <slot />
                        </div>

                        <!-- Modal Footer -->
                        <div v-if="$slots.footer" class="border-t border-gray-100 p-6">
                            <slot name="footer" />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
