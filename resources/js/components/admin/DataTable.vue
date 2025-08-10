<script setup lang="ts">
import { computed } from 'vue';

interface TableColumn {
    key: string;
    label: string;
    sortable?: boolean;
    class?: string;
    render?: (value: any, row: any) => string;
}

interface TableAction {
    label: string;
    icon: string;
    onClick: (row: any) => void;
    variant?: 'default' | 'danger';
    show?: (row: any) => boolean;
}

interface Props {
    columns: TableColumn[];
    data: any[];
    actions?: TableAction[];
    loading?: boolean;
    emptyMessage?: string;
    emptyIcon?: string;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    emptyMessage: 'Tidak ada data',
    emptyIcon: 'inbox'
});

const emit = defineEmits<{
    sort: [column: string, direction: 'asc' | 'desc'];
}>();

const getActionClass = (variant: string) => {
    const classes = {
        default: 'text-gray-600 hover:text-black hover:bg-gray-100',
        danger: 'text-gray-600 hover:text-red-600 hover:bg-red-50'
    };
    return classes[variant as keyof typeof classes] || classes.default;
};

const getCellValue = (row: any, column: TableColumn) => {
    if (column.render) {
        return column.render(row[column.key], row);
    }
    return row[column.key];
};

const getIconPath = (iconName: string) => {
    const icons: Record<string, string> = {
        view: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
        edit: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        delete: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        inbox: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0l-3.5-3.5M4 13l3.5-3.5',
        users: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        warehouse: 'M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4m-13 0h13m-13 0V10a4 4 0 014-4h5a4 4 0 014 4v11m-13 0V10',
        package: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10'
    };
    return icons[iconName] || icons.inbox;
};
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <!-- Loading State -->
        <div v-if="loading" class="p-8 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black mx-auto mb-4"></div>
            <p class="text-gray-500">Memuat data...</p>
        </div>

        <!-- Table -->
        <div v-else-if="data.length > 0" class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th 
                            v-for="column in columns" 
                            :key="column.key"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            :class="column.class"
                        >
                            <button
                                v-if="column.sortable"
                                @click="emit('sort', column.key, 'asc')"
                                class="flex items-center space-x-1 hover:text-gray-700 transition-colors"
                            >
                                <span>{{ column.label }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                </svg>
                            </button>
                            <span v-else>{{ column.label }}</span>
                        </th>
                        <th v-if="actions && actions.length > 0" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(row, index) in data" :key="index" class="hover:bg-gray-50 transition-colors">
                        <td 
                            v-for="column in columns" 
                            :key="column.key"
                            class="px-6 py-4 whitespace-nowrap"
                            :class="column.class"
                        >
                            <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                                <span v-html="getCellValue(row, column)"></span>
                            </slot>
                        </td>
                        <td v-if="actions && actions.length > 0" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <template v-for="action in actions" :key="action.label">
                                    <button
                                        v-if="!action.show || action.show(row)"
                                        @click="action.onClick(row)"
                                        class="p-2 rounded-md transition-colors cursor-pointer"
                                        :class="getActionClass(action.variant || 'default')"
                                        :title="action.label"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(action.icon)" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(emptyIcon)" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ emptyMessage }}</h3>
            <slot name="empty-state">
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan data baru.</p>
            </slot>
        </div>
    </div>
</template>