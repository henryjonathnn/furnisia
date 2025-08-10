<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { DataTable, SearchFilter, AdminModal, Pagination, ProductModal } from '@/components/admin';
import { ref, computed } from 'vue';

// Types
interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    formatted_price: string;
    stock: number;
    category?: { id: number; name: string };
    is_active: boolean;
    image_path?: string;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
    stock_status: { label: string; color: string };
}

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    products: {
        data: Product[];
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
    categories: Category[];
    filters: Record<string, any>;
    stats: {
        total_products: number;
        active_products: number;
        low_stock_products: number;
        out_of_stock_products: number;
    };
}>();

// Modal states
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const showStockModal = ref(false);
const showProductModal = ref(false);
const productModalMode = ref<'create' | 'edit'>('create');
const selectedProduct = ref<Product | null>(null);
const productDetails = ref<any>(null);
const loading = ref(false);

// Stock update form
const stockForm = ref({
    stock: 0,
    reason: ''
});

// Table configuration - simplified columns
const columns = [
    {
        key: 'product_info',
        label: 'Product',
        class: 'min-w-0'
    },
    {
        key: 'category',
        label: 'Category'
    },
    {
        key: 'price',
        label: 'Price'
    },
    {
        key: 'stock',
        label: 'Stock'
    },
    {
        key: 'status',
        label: 'Status'
    }
];

const actions = [
    {
        label: 'View Details',
        icon: 'view',
        onClick: (product: Product) => viewProduct(product)
    },
    {
        label: 'Update Stock', 
        icon: 'warehouse',
        onClick: (product: Product) => openStockModal(product)
    },
    {
        label: 'Edit Product',
        icon: 'edit',
        onClick: (product: Product) => editProduct(product)
    },
    {
        label: 'Delete Product',
        icon: 'delete',
        variant: 'danger' as const,
        onClick: (product: Product) => confirmDelete(product)
    }
];

// Filter configuration
const filterFields = [
    {
        type: 'text' as const,
        key: 'search',
        label: 'Search Products',
        placeholder: 'Product name or description...',
        icon: 'search'
    },
    {
        type: 'select' as const,
        key: 'category_filter',
        label: 'Category',
        placeholder: 'All Categories',
        options: props.categories.map(category => ({
            value: category.id,
            label: category.name
        }))
    },
    {
        type: 'select' as const,
        key: 'status_filter',
        label: 'Status',
        placeholder: 'All Status',
        options: [
            { value: 1, label: 'Active' },
            { value: 0, label: 'Inactive' }
        ]
    },
    {
        type: 'select' as const,
        key: 'stock_filter',
        label: 'Stock Status',
        placeholder: 'All Stock',
        options: [
            { value: 'available', label: 'In Stock' },
            { value: 'low', label: 'Low Stock (≤10)' },
            { value: 'out', label: 'Out of Stock' }
        ]
    }
];

// Computed
const filters = ref(props.filters);

// Methods
const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStockBadgeClass = (stockStatus: { label: string; color: string }) => {
    const classes = {
        green: 'bg-green-100 text-green-800',
        yellow: 'bg-yellow-100 text-yellow-800',
        red: 'bg-red-100 text-red-800'
    };
    return classes[stockStatus.color as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

// Event handlers
const viewProduct = async (product: Product) => {
    selectedProduct.value = product;
    showViewModal.value = true;
    loading.value = true;
    
    try {
        const response = await fetch(`/admin/products/${product.id}/details`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            productDetails.value = data;
        }
    } catch (error) {
        console.error('Error loading product details:', error);
    } finally {
        loading.value = false;
    }
};

// Function moved below

const confirmDelete = (product: Product) => {
    selectedProduct.value = product;
    showDeleteModal.value = true;
};

const deleteProduct = () => {
    if (selectedProduct.value) {
        router.delete(`/admin/products/${selectedProduct.value.id}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                selectedProduct.value = null;
            }
        });
    }
};

const openStockModal = (product: Product) => {
    selectedProduct.value = product;
    stockForm.value.stock = product.stock;
    stockForm.value.reason = '';
    showStockModal.value = true;
};

const updateStock = async () => {
    if (!selectedProduct.value) return;
    
    try {
        const response = await fetch(`/admin/products/${selectedProduct.value.id}/update-stock`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(stockForm.value)
        });
        
        if (response.ok) {
            showStockModal.value = false;
            router.reload();
        }
    } catch (error) {
        console.error('Error updating stock:', error);
    }
};

const handleFiltersUpdate = (newFilters: Record<string, any>) => {
    filters.value = newFilters;
    router.get('/admin/products', newFilters, {
        preserveState: true,
        replace: true,
        only: ['products', 'filters'] // Hanya reload data products dan filters
    });
};

const createProduct = () => {
    selectedProduct.value = null;
    productModalMode.value = 'create';
    showProductModal.value = true;
};

const editProduct = (product: Product) => {
    selectedProduct.value = product;
    productModalMode.value = 'edit';
    showProductModal.value = true;
};

const handleProductModalSuccess = () => {
    showProductModal.value = false;
    selectedProduct.value = null;
    router.reload();
};
</script>

<template>
    <AdminLayout>
        <Head title="Product Management - Admin" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-black">Product Management</h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola produk dan inventory</p>
                </div>
                <button
                    @click="createProduct"
                    class="inline-flex items-center justify-center px-4 py-2.5 !bg-black !text-white text-sm font-medium rounded-lg hover:!bg-gray-800 transition-colors shadow-sm border-2 border-black cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Product
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-2xl font-semibold text-black">{{ stats.total_products }}</p>
                        </div>
                        <div class="h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Products</p>
                            <p class="text-2xl font-semibold text-green-600">{{ stats.active_products }}</p>
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
                            <p class="text-sm font-medium text-gray-600">Low Stock</p>
                            <p class="text-2xl font-semibold text-yellow-600">{{ stats.low_stock_products }}</p>
                        </div>
                        <div class="h-10 w-10 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                            <p class="text-2xl font-semibold text-red-600">{{ stats.out_of_stock_products }}</p>
                        </div>
                        <div class="h-10 w-10 bg-red-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
            />

            <!-- Products Table -->
            <DataTable
                :columns="columns"
                :data="products.data"
                :actions="actions"
                empty-message="No products found"
                empty-icon="package"
            >
                <!-- Custom cell templates -->
                <template #cell-product_info="{ row }">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex-shrink-0 mr-4">
                            <img 
                                v-if="row.image_path" 
                                :src="`/storage/${row.image_path}`" 
                                :alt="row.name"
                                class="h-12 w-12 rounded-lg object-cover"
                            />
                            <div 
                                v-else 
                                class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center"
                            >
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-black truncate">{{ row.name }}</div>
                        </div>
                    </div>
                </template>

                <template #cell-category="{ row }">
                    <span 
                        v-if="row.category"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                    >
                        {{ row.category.name }}
                    </span>
                    <span v-else class="text-gray-400 text-sm">No category</span>
                </template>

                <template #cell-price="{ row }">
                    <span class="text-sm font-semibold text-black">{{ row.formatted_price }}</span>
                </template>

                <template #cell-stock="{ row }">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-black">{{ row.stock }}</span>
                        <span 
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="getStockBadgeClass(row.stock_status)"
                        >
                            {{ row.stock_status.label }}
                        </span>
                    </div>
                </template>

                <template #cell-status="{ row }">
                    <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="row.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'"
                    >
                        {{ row.is_active ? 'Active' : 'Inactive' }}
                    </span>
                </template>


            </DataTable>

            <!-- Pagination -->
            <Pagination :data="products" />
        </div>

        <!-- View Product Modal -->
        <AdminModal
            :show="showViewModal"
            @close="showViewModal = false"
            title="Product Details"
            description="Complete product information"
            icon="view"
            icon-color="gray"
            max-width="xl"
        >
            <div v-if="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black mx-auto mb-4"></div>
                <p class="text-gray-500">Loading product details...</p>
            </div>

            <div v-else-if="productDetails" class="space-y-6">
                <!-- Product Info -->
                <div class="flex items-start space-x-4">
                    <img 
                        v-if="productDetails.product.image_path" 
                        :src="`/storage/${productDetails.product.image_path}`" 
                        :alt="productDetails.product.name"
                        class="w-32 h-32 rounded-lg object-cover border border-gray-200"
                    />
                    <div v-else class="w-32 h-32 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-black">{{ productDetails.product.name }}</h3>
                        <p class="text-gray-600 mt-1 text-sm">{{ productDetails.product.description }}</p>
                        <div class="mt-3 grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-500">Price</span>
                                <p class="text-lg font-semibold text-black">{{ productDetails.product.formatted_price }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Stock</span>
                                <p class="text-lg font-semibold text-black">{{ productDetails.product.stock }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Category</span>
                                <p class="text-sm font-medium text-gray-700">{{ productDetails.product.category?.name || 'No Category' }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Status</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                    :class="productDetails.product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ productDetails.product.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-lg font-semibold text-black mb-3">Additional Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-sm text-gray-500">Created Date</span>
                            <p class="text-sm font-medium text-gray-700 mt-1">{{ formatDate(productDetails.product.created_at) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <span class="text-sm text-gray-500">Last Updated</span>
                            <p class="text-sm font-medium text-gray-700 mt-1">{{ formatDate(productDetails.product.updated_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Product Specifications -->
                <div v-if="productDetails.product.specs && Object.keys(productDetails.product.specs).length > 0" class="border-t border-gray-200 pt-4">
                    <h4 class="text-lg font-semibold text-black mb-3">Specifications</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="(value, key) in productDetails.product.specs" :key="key" class="bg-gray-50 rounded-lg p-3">
                            <span class="text-sm text-gray-500">{{ key }}</span>
                            <p class="text-sm font-medium text-gray-700 mt-1">{{ value }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sales Stats -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-lg font-semibold text-black mb-3">Sales Statistics</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-blue-600 font-medium">Total Orders</p>
                            <p class="text-2xl font-bold text-blue-700 mt-1">{{ productDetails.stats.total_orders }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-green-600 font-medium">Total Sold</p>
                            <p class="text-2xl font-bold text-green-700 mt-1">{{ productDetails.stats.total_sold }}</p>
                    </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <p class="text-sm text-orange-600 font-medium">In Carts</p>
                            <p class="text-2xl font-bold text-orange-700 mt-1">{{ productDetails.stats.in_carts }}</p>
                    </div>
                    </div>
                </div>
            </div>
        </AdminModal>

        <!-- Update Stock Modal -->
        <AdminModal
            :show="showStockModal"
            @close="showStockModal = false"
            title="Update Stock"
            description="Adjust product inventory"
            icon="warehouse"
            icon-color="blue"
            max-width="md"
        >
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-black mb-2">New Stock</label>
                    <input
                        type="number"
                        v-model="stockForm.stock"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-black mb-2">Reason (optional)</label>
                    <textarea
                        v-model="stockForm.reason"
                        rows="3"
                        placeholder="Why are you updating the stock?"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                    ></textarea>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end space-x-3 bg-gray-50 -m-6 p-6 rounded-b-2xl">
                    <button
                        @click="showStockModal = false"
                        class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="updateStock"
                        class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors text-sm font-medium shadow-sm"
                        style="background-color: #000000 !important;"
                    >
                        Update Stock
                    </button>
                </div>
            </template>
        </AdminModal>

        <!-- Delete Confirmation Modal -->
        <AdminModal
            :show="showDeleteModal"
            @close="showDeleteModal = false"
            title="Delete Product"
            description="This action cannot be undone"
            icon="delete"
            icon-color="red"
            max-width="md"
        >
            <div class="space-y-4">
                <p class="text-gray-700">
                    Are you sure you want to delete <strong>{{ selectedProduct?.name }}</strong>? 
                    This will remove the product from your catalog.
                </p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-sm text-red-600">
                        <strong>Warning:</strong> This action will soft delete the product. 
                        You can restore it later if needed.
                    </p>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end space-x-3 bg-gray-50 -m-6 p-6 rounded-b-2xl">
                    <button
                        @click="showDeleteModal = false"
                        class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="deleteProduct"
                        class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 text-sm font-medium shadow-lg backdrop-blur-sm"
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4) !important;"
                    >
                        Delete Product
                    </button>
                </div>
            </template>
        </AdminModal>

        <!-- Product Modal (Create/Edit) -->
        <ProductModal
            :show="showProductModal"
            :mode="productModalMode"
            :product="selectedProduct"
            :categories="categories"
            @close="showProductModal = false"
            @success="handleProductModalSuccess"
        />
    </AdminLayout>
</template>
