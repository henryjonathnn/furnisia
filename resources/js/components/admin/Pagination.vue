<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationData {
    current_page: number;
    from: number;
    to: number;
    total: number;
    per_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    links: PaginationLink[];
}

interface Props {
    data: PaginationData;
    showInfo?: boolean;
    showMobile?: boolean;
}

withDefaults(defineProps<Props>(), {
    showInfo: true,
    showMobile: true
});

const isNumeric = (str: string) => {
    return /^\d+$/.test(str);
};
</script>

<template>
    <div v-if="data.total > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
            <!-- Mobile Pagination -->
            <div v-if="showMobile" class="flex-1 flex justify-between sm:hidden">
                <Link
                    v-if="data.prev_page_url"
                    :href="data.prev_page_url"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </Link>
                <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </span>

                <Link
                    v-if="data.next_page_url"
                    :href="data.next_page_url"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                >
                    Next
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
                <span v-else class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                    Next
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </div>

            <!-- Desktop Pagination -->
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <!-- Info -->
                <div v-if="showInfo">
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ data.from }}</span> to <span class="font-medium">{{ data.to }}</span> 
                        of <span class="font-medium">{{ data.total }}</span> results
                    </p>
                </div>

                <!-- Page Links -->
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <template v-for="(link, index) in data.links" :key="index">
                            <!-- Active Page -->
                            <span
                                v-if="link.active"
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium bg-black text-white border-black"
                                :class="{
                                    'rounded-l-md': index === 0,
                                    'rounded-r-md': index === data.links.length - 1
                                }"
                                aria-current="page"
                                v-html="link.label"
                            />

                            <!-- Clickable Page -->
                            <Link
                                v-else-if="link.url"
                                :href="link.url"
                                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors bg-white text-gray-500 border-gray-300 hover:bg-gray-50 hover:text-gray-700"
                                :class="{
                                    'rounded-l-md': index === 0,
                                    'rounded-r-md': index === data.links.length - 1
                                }"
                                v-html="link.label"
                            />

                            <!-- Disabled Link -->
                            <span
                                v-else
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed"
                                :class="{
                                    'rounded-l-md': index === 0,
                                    'rounded-r-md': index === data.links.length - 1
                                }"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Page Size Info (optional) -->
        <div class="mt-3 text-center sm:text-left">
            <p class="text-xs text-gray-500">
                Page {{ data.current_page }} of {{ data.last_page }} 
                ({{ data.per_page }} items per page)
            </p>
        </div>
    </div>
</template>
