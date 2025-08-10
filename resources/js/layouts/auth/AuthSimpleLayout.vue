<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-3 font-medium">
                        <!-- Furnisia Logo from Navbar -->
                        <div class="flex items-center space-x-3">
                            <div class="h-12 w-12 rounded-xl bg-black flex items-center justify-center shadow-lg">
                                <Icon name="store" class="text-white text-xl" />
                            </div>
                            <div class="text-center">
                                <span class="text-2xl font-bold text-black">Furnisia</span>
                                <div class="text-xs text-gray-600 -mt-1">Premium Furniture</div>
                            </div>
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium text-black">{{ title }}</h1>
                        <p class="text-center text-sm text-gray-600">{{ description }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
