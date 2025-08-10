<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { DataTable, SearchFilter, AdminModal, Pagination } from '@/components/admin';
import { ref, computed } from 'vue';

// Types
interface Order {
    id: string;
    order_number: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    status: string;
    status_label: string;
    status_color: string;
    total: number;
    formatted_total: string;
    items_count: number;
    payment_status: string;
    payment_method: string | null;
    created_at: string;
    formatted_created_at: string;
    source: string;
    can_update_status: boolean;
    can_complete: boolean;
    is_paid: boolean;
}

interface Props {
    orders: {
        data: Order[];
        total: number;
        from: number;
        to: number;
        current_page: number;
        last_page: number;
        per_page: number;
        prev_page_url: string | null;
        next_page_url: string | null;
        links: any[];
    };
    filters: Record<string, any>;
    stats: {
        total_orders: number;
        pending_orders: number;
        progress_orders: number;
        completed_orders: number;
        today_orders: number;
        today_revenue: number;
    };
}

const props = defineProps<Props>();

// Modal states
const showViewModal = ref(false);
const selectedOrder = ref<Order | null>(null);
const orderDetails = ref<any>(null);
const loading = ref(false);

// Table configuration
const columns = [
    {
        key: 'order_info',
        label: 'Order',
        class: 'min-w-0'
    },
    {
        key: 'customer',
        label: 'Customer'
    },
    {
        key: 'total',
        label: 'Total'
    },
    {
        key: 'status',
        label: 'Status'
    },
    {
        key: 'payment',
        label: 'Payment'
    },
    {
        key: 'created_at',
        label: 'Date'
    }
];

const actions = [
    {
        label: 'View Details',
        icon: 'view',
        onClick: (order: Order) => viewOrder(order)
    },
    {
        label: 'Selesai',
        icon: 'check',
        variant: 'success' as const,
        onClick: (order: Order) => markAsCompleted(order),
        show: (order: Order) => order.status === 'progress' && order.is_paid
    }
];

// Filter configuration
const filterFields = [
    {
        type: 'text' as const,
        key: 'search',
        label: 'Search Orders',
        placeholder: 'Order ID or customer...',
        icon: 'search'
    },
    {
        type: 'select' as const,
        key: 'status_filter',
        label: 'Order Status',
        placeholder: 'All Status',
        options: [
            { value: 'pending', label: 'Menunggu Pembayaran' },
            { value: 'progress', label: 'Sedang Diproses' },
            { value: 'completed', label: 'Selesai' },
            { value: 'cancelled', label: 'Dibatalkan' }
        ]
    },
    {
        type: 'select' as const,
        key: 'payment_filter',
        label: 'Payment Status',
        placeholder: 'All Payments',
        options: [
            { value: 'pending', label: 'Pending' },
            { value: 'success', label: 'Success' },
            { value: 'failed', label: 'Failed' }
        ]
    }
];

// Computed
const filters = ref(props.filters);

// Methods
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount || 0);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusBadgeClass = (statusColor: string) => {
    const classes = {
        'yellow': 'bg-yellow-100 text-yellow-800',
        'blue': 'bg-blue-100 text-blue-800',
        'green': 'bg-green-100 text-green-800',
        'red': 'bg-red-100 text-red-800'
    };
    return classes[statusColor as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getPaymentBadgeClass = (paymentStatus: string) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'success': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800'
    };
    return classes[paymentStatus as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

// Event handlers
const viewOrder = async (order: Order) => {
    selectedOrder.value = order;
    showViewModal.value = true;
    loading.value = true;
    orderDetails.value = null;
    
    try {
        const response = await fetch(`/admin/orders/${order.id}/details`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            orderDetails.value = data;
        }
    } catch (error) {
        console.error('Error loading order details:', error);
        alert('Gagal memuat detail pesanan. Silakan coba lagi.');
    } finally {
        loading.value = false;
    }
};

const markAsCompleted = async (order: Order) => {
    if (!confirm('Apakah Anda yakin ingin menandai pesanan ini sebagai selesai?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/orders/${order.id}/complete`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            // Refresh the page to show updated data
            router.reload();
            alert('Pesanan berhasil diselesaikan!');
        } else {
            alert(data.message || 'Gagal menyelesaikan pesanan');
        }
    } catch (error) {
        console.error('Error completing order:', error);
        alert('Terjadi kesalahan saat menyelesaikan pesanan');
    }
};

const handleFiltersUpdate = (newFilters: Record<string, any>) => {
    filters.value = newFilters;
    router.get('/admin/orders', newFilters, {
        preserveState: true,
        replace: true,
        only: ['orders', 'filters']
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Order Management - Admin" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-black">Order Management</h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola pesanan dan pembayaran</p>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-semibold text-black">{{ stats.total_orders }}</p>
                        </div>
                        <div class="h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-semibold text-yellow-600">{{ stats.pending_orders }}</p>
                        </div>
                        <div class="h-10 w-10 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Processing</p>
                            <p class="text-2xl font-semibold text-blue-600">{{ stats.progress_orders }}</p>
                        </div>
                        <div class="h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl font-semibold text-green-600">{{ stats.completed_orders }}</p>
                        </div>
                        <div class="h-10 w-10 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today Orders</p>
                            <p class="text-2xl font-semibold text-purple-600">{{ stats.today_orders }}</p>
                        </div>
                        <div class="h-10 w-10 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today Revenue</p>
                            <p class="text-lg font-semibold text-green-600">{{ formatCurrency(stats.today_revenue) }}</p>
                        </div>
                        <div class="h-10 w-10 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filters -->
            <SearchFilter
                :fields="filterFields"
                v-model="filters"
                @update:model-value="handleFiltersUpdate"
                :stats-label="'Total Orders'"
                :stats-value="orders.total"
            />

            <!-- Orders Table -->
            <DataTable
                :columns="columns"
                :data="orders.data"
                :actions="actions"
                empty-message="Tidak ada pesanan"
                empty-icon="package"
            >
                <!-- Custom cell templates -->
                <template #cell-order_info="{ row }">
                    <div class="flex flex-col">
                        <div class="text-sm font-semibold text-black">{{ row.order_number }}</div>
                        <div class="text-xs text-gray-500">{{ row.items_count }} item</div>
                        <div class="text-xs text-gray-500">{{ row.source }}</div>
                    </div>
                </template>

                <template #cell-customer="{ row }">
                    <div class="flex items-center">
                        <div class="h-8 w-8 flex-shrink-0 mr-3">
                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-700">
                                    {{ getInitials(row.user.name) }}
                                </span>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-black truncate">{{ row.user.name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ row.user.email }}</div>
                        </div>
                    </div>
                </template>

                <template #cell-total="{ row }">
                    <span class="text-sm font-semibold text-black">{{ row.formatted_total }}</span>
                </template>

                <template #cell-status="{ row }">
                    <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusBadgeClass(row.status_color)"
                    >
                        {{ row.status_label }}
                    </span>
                </template>

                <template #cell-payment="{ row }">
                    <div class="flex flex-col space-y-1">
                        <span 
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="getPaymentBadgeClass(row.payment_status)"
                        >
                            {{ row.payment_status }}
                        </span>
                        <span v-if="row.payment_method" class="text-xs text-gray-500 capitalize">
                            {{ row.payment_method }}
                        </span>
                    </div>
                </template>

                <template #cell-created_at="{ row }">
                    <span class="text-sm text-gray-500">
                        {{ row.formatted_created_at }}
                    </span>
                </template>

                <template #empty-state>
                    <p class="mt-1 text-sm text-gray-500">Belum ada pesanan masuk.</p>
                </template>
            </DataTable>

            <!-- Pagination -->
            <Pagination :data="orders" />
        </div>

        <!-- View Order Modal -->
        <AdminModal
            :show="showViewModal"
            @close="showViewModal = false"
            title="Detail Pesanan"
            description="Informasi lengkap pesanan"
            icon="view"
            icon-color="gray"
            max-width="2xl"
        >
            <div v-if="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black mx-auto mb-4"></div>
                <p class="text-gray-500">Memuat detail pesanan...</p>
            </div>

            <div v-else-if="orderDetails" class="space-y-6">
                <!-- Order Header -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-black">{{ orderDetails.order.order_number }}</h3>
                            <p class="text-gray-600">{{ orderDetails.order.formatted_created_at }}</p>
                        </div>
                        <span 
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            :class="getStatusBadgeClass(orderDetails.order.status_color)"
                        >
                            {{ orderDetails.order.status_label }}
                        </span>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-black mb-4">Informasi Customer</h4>
                    <div class="flex items-center">
                        <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                            <span class="text-sm font-medium text-gray-700">
                                {{ getInitials(orderDetails.order.user.name) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-black">{{ orderDetails.order.user.name }}</p>
                            <p class="text-sm text-gray-600">{{ orderDetails.order.user.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-black mb-4">Produk yang Dipesan</h4>
                    <div class="space-y-4">
                        <div 
                            v-for="item in orderDetails.order.items" 
                            :key="item.id"
                            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
                        >
                            <div class="flex items-center">
                                <img 
                                    v-if="item.product.image" 
                                    :src="`/storage/${item.product.image}`" 
                                    :alt="item.product.name"
                                    class="w-12 h-12 rounded-lg object-cover mr-4"
                                />
                                <div v-else class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-black">{{ item.product.name }}</p>
                                    <p class="text-xs text-gray-500">{{ item.product.category }}</p>
                                    <p class="text-xs text-gray-600">{{ item.product.formatted_price }} × {{ item.quantity }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-black">{{ item.formatted_subtotal }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-black">Total</span>
                            <span class="text-xl font-bold text-black">{{ orderDetails.order.formatted_total }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div v-if="orderDetails.order.payment" class="bg-white border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-black mb-4">Informasi Pembayaran</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Metode</span>
                            <p class="text-sm font-medium text-gray-700 capitalize">{{ orderDetails.order.payment.method || 'Belum dipilih' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status</span>
                            <span 
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2"
                                :class="getPaymentBadgeClass(orderDetails.order.payment.status)"
                            >
                                {{ orderDetails.order.payment.status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </AdminModal>


    </AdminLayout>
</template>
