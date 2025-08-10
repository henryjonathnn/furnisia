<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
    value?: string | number;
    icon?: string;
    iconColor?: string;
    iconBg?: string;
    trend?: {
        value: number;
        label: string;
        direction: 'up' | 'down' | 'neutral';
    };
    loading?: boolean;
    clickable?: boolean;
    variant?: 'default' | 'compact' | 'detailed';
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    clickable: false,
    variant: 'default',
    iconColor: 'text-gray-600',
    iconBg: 'bg-gray-100'
});

const emit = defineEmits<{
    click: [];
}>();

const getTrendClass = (direction: string) => {
    const classes = {
        up: 'text-green-600',
        down: 'text-red-600',
        neutral: 'text-gray-600'
    };
    return classes[direction as keyof typeof classes] || classes.neutral;
};

const getTrendIcon = (direction: string) => {
    const icons = {
        up: 'M7 14l3-3 3 3',
        down: 'M17 10l-3 3-3-3',
        neutral: 'M20 12H4'
    };
    return icons[direction as keyof typeof icons] || icons.neutral;
};

const getIconPath = (iconName: string) => {
    const icons = {
        'dollar-sign': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1',
        'shopping-cart': 'M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5H20',
        'warehouse': 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4',
        'star': 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a4 4 0 11-8 0 4 4 0 018 0z',
        'chart-pie': 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z',
        'package': 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10'
    };
    return icons[iconName as keyof typeof icons] || icons.package;
};
</script>

<template>
    <div 
        class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-sm transition-shadow"
        :class="{ 'cursor-pointer hover:border-gray-300': clickable }"
        @click="clickable ? emit('click') : null"
    >
        <!-- Loading State -->
        <div v-if="loading" class="animate-pulse">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                    <div class="h-6 bg-gray-200 rounded w-16"></div>
                </div>
                <div class="h-10 w-10 bg-gray-200 rounded-md"></div>
            </div>
        </div>

        <!-- Default Variant -->
        <div v-else-if="variant === 'default'" class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">{{ title }}</p>
                <p v-if="value !== undefined" class="text-2xl font-semibold mt-1 text-black">{{ value }}</p>
                <div v-if="trend" class="flex items-center mt-1" :class="getTrendClass(trend.direction)">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTrendIcon(trend.direction)" />
                    </svg>
                    <span class="text-sm">{{ trend.label }}</span>
                </div>
            </div>
            <div v-if="icon" class="h-10 w-10 rounded-md flex items-center justify-center" :class="iconBg">
                <svg class="w-5 h-5" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(icon)" />
                </svg>
            </div>
        </div>

        <!-- Compact Variant -->
        <div v-else-if="variant === 'compact'" class="space-y-1">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-600">{{ title }}</h3>
                <div v-if="icon" class="h-6 w-6 rounded flex items-center justify-center" :class="iconBg">
                    <svg class="w-3 h-3" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(icon)" />
                    </svg>
                </div>
            </div>
            <p v-if="value !== undefined" class="text-lg font-semibold text-black">{{ value }}</p>
            <p v-if="description" class="text-xs text-gray-500">{{ description }}</p>
        </div>

        <!-- Detailed Variant -->
        <div v-else-if="variant === 'detailed'" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-black">{{ title }}</h3>
                <div v-if="icon" class="h-12 w-12 rounded-lg flex items-center justify-center" :class="iconBg">
                    <svg class="w-6 h-6" :class="iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(icon)" />
                    </svg>
                </div>
            </div>
            <div>
                <p v-if="value !== undefined" class="text-3xl font-bold text-black mb-2">{{ value }}</p>
                <p v-if="description" class="text-sm text-gray-600 mb-2">{{ description }}</p>
                <div v-if="trend" class="flex items-center" :class="getTrendClass(trend.direction)">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTrendIcon(trend.direction)" />
                    </svg>
                    <span class="text-sm font-medium">{{ trend.label }}</span>
                </div>
            </div>
        </div>

        <!-- Custom Slot -->
        <slot />
    </div>
</template>
