<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { AdminCard } from '@/components/admin';

// Types
interface Stats {
    totalSales: {
        value: number;
        formatted: string;
        growth: number;
        growthFormatted: string;
    };
    newOrders: {
        value: number;
        growth: number;
        growthFormatted: string;
    };
    lowStock: {
        value: number;
        status: string;
    };
    storeRating: {
        value: number;
        status: string;
    };
}

interface Order {
    id: number;
    order_number: string;
    customer_name: string;
    product_name: string;
    total: string;
    status: string;
    status_label: string;
    status_color: string;
    created_at: string;
    product_image?: string;
}

interface Product {
    rank: number;
    name: string;
    total_sold: number;
    price: string;
    image?: string;
    growth: number;
}

interface Alert {
    type: string;
    icon: string;
    title: string;
    message: string;
    color: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    role: { id: number; name: string };
}

const props = defineProps<{
    stats: Stats;
    recentOrders: Order[];
    topProducts: Product[];
    lowStockProducts: Product[];
    alerts: Alert[];
    user: User;
}>();

// Quick actions configuration
const quickActions = [
    {
        title: 'Tambah Produk',
        description: 'Produk baru ke katalog',
        icon: 'plus',
        iconColor: 'text-white',
        iconBg: 'bg-blue-600',
        href: '/admin/products/create'
    },
    {
        title: 'Buat Promo',
        description: 'Diskon dan penawaran',
        icon: 'tags',
        iconColor: 'text-white',
        iconBg: 'bg-green-600'
    },
    {
        title: 'Export Laporan',
        description: 'Download data penjualan',
        icon: 'download',
        iconColor: 'text-white',
        iconBg: 'bg-purple-600'
    },
    {
        title: 'Kampanye Iklan',
        description: 'Promosikan produk',
        icon: 'bullhorn',
        iconColor: 'text-white',
        iconBg: 'bg-orange-600'
    }
];

// Helper functions
const getStatusBadgeClass = (color: string) => {
    const classes = {
        green: 'bg-green-100 text-green-700',
        blue: 'bg-blue-100 text-blue-700',
        yellow: 'bg-yellow-100 text-yellow-700',
        red: 'bg-red-100 text-red-700',
        gray: 'bg-gray-100 text-gray-700'
    };
    return classes[color as keyof typeof classes] || classes.gray;
};

const getAlertClass = (color: string) => {
    const classes = {
        red: 'bg-red-50 border border-red-200',
        orange: 'bg-orange-50 border border-orange-200',
        yellow: 'bg-yellow-50 border border-yellow-200',
        blue: 'bg-blue-50 border border-blue-200'
    };
    return classes[color as keyof typeof classes] || classes.blue;
};

const getAlertIconClass = (color: string) => {
    const classes = {
        red: 'text-red-500',
        orange: 'text-orange-500',
        yellow: 'text-yellow-500',
        blue: 'text-blue-500'
    };
    return classes[color as keyof typeof classes] || classes.blue;
};

const getAlertTitleClass = (color: string) => {
    const classes = {
        red: 'text-red-800',
        orange: 'text-orange-800',
        yellow: 'text-yellow-800',
        blue: 'text-blue-800'
    };
    return classes[color as keyof typeof classes] || classes.blue;
};

const getAlertMessageClass = (color: string) => {
    const classes = {
        red: 'text-red-600',
        orange: 'text-orange-600',
        yellow: 'text-yellow-600',
        blue: 'text-blue-600'
    };
    return classes[color as keyof typeof classes] || classes.blue;
};

const getIconPath = (iconName: string) => {
    const icons = {
        plus: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
        tags: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        download: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
        bullhorn: 'M7 4V2a1 1 0 011-1h4a1 1 0 011 1v2M7 4H5a2 2 0 00-2 2v5a2 2 0 002 2h2M7 4h10a2 2 0 012 2v5a2 2 0 01-2 2H7M7 4v9M7 13v6a1 1 0 001 1h4a1 1 0 001-1v-6M7 13l4-4 4 4',
        'chevron-right': 'M9 5l7 7-7 7',
        package: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
        'chart-area': 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'
    };
    return icons[iconName as keyof typeof icons] || icons.package;
};
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard - Furnisia" />

        <!-- Dashboard Content -->
        <div class="p-5">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Sales -->
                <AdminCard
                    title="Total Penjualan"
                    :value="stats.totalSales.formatted"
                    icon="dollar-sign"
                    icon-color="text-green-600"
                    icon-bg="bg-green-100"
                    :trend="{
                        value: stats.totalSales.growth,
                        label: `${stats.totalSales.growthFormatted} dari kemarin`,
                        direction: stats.totalSales.growth >= 0 ? 'up' : 'down'
                    }"
                />

                <!-- New Orders -->
                <AdminCard
                    title="Pesanan Baru"
                    :value="stats.newOrders.value"
                    icon="shopping-cart"
                    icon-color="text-blue-600"
                    icon-bg="bg-blue-100"
                    :trend="{
                        value: stats.newOrders.growth,
                        label: stats.newOrders.growthFormatted,
                        direction: 'up'
                    }"
                />

                <!-- Low Stock -->
                <AdminCard
                    title="Stok Menipis"
                    :value="stats.lowStock.value"
                    icon="warehouse"
                    icon-color="text-red-600"
                    icon-bg="bg-red-100"
                    :trend="{
                        value: 0,
                        label: 'Perlu restok',
                        direction: 'neutral'
                    }"
                />

                <!-- Store Rating -->
                <AdminCard
                    title="Rating Toko"
                    :value="stats.storeRating.value"
                    icon="star"
                    icon-color="text-yellow-600"
                    icon-bg="bg-yellow-100"
                    :trend="{
                        value: 0,
                        label: 'Excellent',
                        direction: 'neutral'
                    }"
                />
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Sales Chart -->
                <AdminCard
                    title="Grafik Penjualan"
                    description="7 hari terakhir"
                    variant="detailed"
                    class="lg:col-span-2"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-1 text-sm bg-gray-900 text-white rounded-md">
                                7 Hari
                            </button>
                            <button class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100 rounded-md transition-colors">
                                30 Hari
                            </button>
                        </div>
                    </div>
                    <div class="h-64 bg-gray-50 rounded-md flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('chart-area')" />
                            </svg>
                            <p class="font-medium">Chart Integration Area</p>
                            <p class="text-sm">Connect with Chart.js or similar library</p>
                        </div>
                    </div>
                </AdminCard>

                <!-- Quick Actions -->
                <AdminCard
                    title="Aksi Cepat"
                    variant="detailed"
                >
                    <div class="space-y-3">
                        <template v-for="action in quickActions" :key="action.title">
                            <Link
                                v-if="action.href"
                                :href="action.href" 
                                class="w-full flex items-center justify-between p-3 hover:bg-gray-50 rounded-md transition-colors group"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-md flex items-center justify-center" :class="action.iconBg">
                                        <svg class="w-4 h-4" :class="action.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(action.icon)" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-medium">{{ action.title }}</p>
                                        <p class="text-xs text-gray-500">{{ action.description }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('chevron-right')" />
                                </svg>
                            </Link>
                            <button
                                v-else
                                class="w-full flex items-center justify-between p-3 hover:bg-gray-50 rounded-md transition-colors group"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-md flex items-center justify-center" :class="action.iconBg">
                                        <svg class="w-4 h-4" :class="action.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(action.icon)" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-sm font-medium">{{ action.title }}</p>
                                        <p class="text-xs text-gray-500">{{ action.description }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('chevron-right')" />
                                </svg>
                            </button>
                        </template>
                    </div>
                </AdminCard>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <AdminCard
                    title="Pesanan Terbaru"
                    variant="detailed"
                >
                    <template #default>
                        <div class="flex items-center justify-between mb-4">
                            <Link href="/admin/orders" class="text-sm text-gray-500 hover:text-black transition-colors">
                                Lihat Semua
                            </Link>
                        </div>
                        <div class="space-y-3">
                            <div v-for="order in recentOrders" :key="order.id" 
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                <div class="flex items-center space-x-3">
                                    <div class="h-9 w-9 bg-gray-200 rounded-md flex items-center justify-center">
                                        <img v-if="order.product_image" :src="order.product_image" :alt="order.product_name" class="w-full h-full object-cover rounded-md" />
                                        <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('package')" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ order.product_name }} - {{ order.customer_name }}</p>
                                        <p class="text-xs text-gray-500">{{ order.order_number }} • {{ order.created_at }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold">{{ order.total }}</p>
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                                          :class="getStatusBadgeClass(order.status_color)">
                                        {{ order.status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </AdminCard>

                <!-- Top Products & Alerts -->
                <div class="space-y-6">
                    <!-- Top Products -->
                    <AdminCard
                        title="Produk Terlaris"
                        variant="detailed"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <button class="text-sm text-gray-500 hover:text-black transition-colors">
                                Detail
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="product in topProducts" :key="product.rank" class="flex items-center space-x-3">
                                <div class="text-sm font-bold text-gray-500">{{ product.rank }}</div>
                                <div class="h-8 w-8 bg-gray-200 rounded-md flex items-center justify-center">
                                    <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover rounded-md" />
                                    <svg v-else class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath('package')" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ product.name }}</p>
                                    <p class="text-xs text-gray-500">{{ product.total_sold }} terjual</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold">{{ product.price }}</p>
                                    <p class="text-xs text-green-600">+{{ product.growth }}%</p>
                                </div>
                            </div>
                        </div>
                    </AdminCard>

                    <!-- Alerts -->
                    <AdminCard
                        v-if="alerts.length > 0"
                        title="Peringatan"
                        variant="detailed"
                    >
                        <div class="space-y-3">
                            <div v-for="alert in alerts" :key="alert.title" 
                                 class="p-3 rounded-md"
                                 :class="getAlertClass(alert.color)">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-5 h-5" :class="getAlertIconClass(alert.color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium" :class="getAlertTitleClass(alert.color)">{{ alert.title }}</p>
                                        <p class="text-xs" :class="getAlertMessageClass(alert.color)">{{ alert.message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </AdminCard>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>