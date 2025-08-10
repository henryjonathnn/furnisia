<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { DataTable, SearchFilter, AdminModal, Pagination } from '@/components/admin';
import { ref, computed } from 'vue';

// Form states
const createForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    is_active: true
});

const editForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    is_active: true
});

// Loading states
const createLoading = ref(false);
const editLoading = ref(false);

// Validation states
const validationErrors = ref({
    password: '',
    password_confirmation: ''
});

const isPasswordValid = ref(false);
const isPasswordMatching = ref(false);

// Selected user for operations
const userToDelete = ref<User | null>(null);
const selectedUser = ref<User | null>(null);
const userDetails = ref<any>(null);
const userDetailsLoading = ref(false);

// CRUD operations
const createUser = () => {
    createLoading.value = true;

    router.post('/admin/users', createForm.value, {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.value = {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role_id: '',
                is_active: true
            };
            createLoading.value = false;
        },
        onError: () => {
            createLoading.value = false;
        }
    });
};

const updateUser = () => {
    if (!selectedUser.value) return;

    editLoading.value = true;

    router.put(`/admin/users/${selectedUser.value.id}`, editForm.value, {
        onSuccess: () => {
            showEditModal.value = false;
            editLoading.value = false;
        },
        onError: () => {
            editLoading.value = false;
        }
    });
};

const deleteUser = () => {
    if (userToDelete.value) {
        router.delete(`/admin/users/${userToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                userToDelete.value = null;
            }
        });
    }
};

// Computed properties
const isCreateFormValid = computed(() => {
    return createForm.value.name &&
        createForm.value.email &&
        createForm.value.role_id &&
        isPasswordValid.value &&
        isPasswordMatching.value &&
        !createLoading.value;
});

// Validation functions
let passwordValidationTimeout: ReturnType<typeof setTimeout> | null = null;
let confirmPasswordValidationTimeout: ReturnType<typeof setTimeout> | null = null;

const validatePassword = (password: string) => {
    const errors = [];

    if (!password) {
        errors.push('Password wajib diisi');
    } else {
        if (password.length < 8) {
            errors.push('Minimal 8 karakter');
        }
        if (!/[A-Z]/.test(password)) {
            errors.push('Harus ada huruf besar');
        }
        if (!/[a-z]/.test(password)) {
            errors.push('Harus ada huruf kecil');
        }
        if (!/[0-9]/.test(password)) {
            errors.push('Harus ada angka');
        }
    }

    return errors;
};

const validatePasswordConfirmation = (password: string, confirmation: string) => {
    if (!confirmation) {
        return 'Konfirmasi password wajib diisi';
    }

    if (password !== confirmation) {
        return 'Password tidak cocok';
    }

    return '';
};

const debouncedPasswordValidation = (password: string) => {
    if (passwordValidationTimeout) {
        clearTimeout(passwordValidationTimeout);
    }

    passwordValidationTimeout = setTimeout(() => {
        const errors = validatePassword(password);
        validationErrors.value.password = errors.length > 0 ? errors[0] : '';
        isPasswordValid.value = errors.length === 0 && password.length > 0;

        // Also revalidate confirmation if it exists
        if (createForm.value.password_confirmation) {
            debouncedConfirmPasswordValidation(createForm.value.password_confirmation);
        }
    }, 500);
};

const debouncedConfirmPasswordValidation = (confirmation: string) => {
    if (confirmPasswordValidationTimeout) {
        clearTimeout(confirmPasswordValidationTimeout);
    }

    confirmPasswordValidationTimeout = setTimeout(() => {
        const error = validatePasswordConfirmation(createForm.value.password, confirmation);
        validationErrors.value.password_confirmation = error;
        isPasswordMatching.value = !error && confirmation.length > 0;
    }, 300);
};

// Additional helper functions
const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount || 0);
};

// Modal close functions
const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
        is_active: true
    };

    // Reset validation states
    validationErrors.value = {
        password: '',
        password_confirmation: ''
    };
    isPasswordValid.value = false;
    isPasswordMatching.value = false;

    // Clear timeouts
    if (passwordValidationTimeout) {
        clearTimeout(passwordValidationTimeout);
    }
    if (confirmPasswordValidationTimeout) {
        clearTimeout(confirmPasswordValidationTimeout);
    }
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
        is_active: true
    };
    selectedUser.value = null;
};

// Types
interface User {
    id: number;
    name: string;
    email: string;
    role?: { id: number; name: string };
    is_active: boolean;
    created_at: string;
}

interface Role {
    id: number;
    name: string;
}

const props = defineProps<{
    users: {
        data: User[];
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
    roles: Role[];
    filters: Record<string, any>;
}>();

// Helper functions (defined first before usage)
const capitalizeRole = (roleName: string) => {
    if (!roleName) return 'N/A';
    return roleName.charAt(0).toUpperCase() + roleName.slice(1).toLowerCase();
};

// Modal states
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);

// Table configuration
const columns = [
    {
        key: 'user_info',
        label: 'User',
        class: 'min-w-0'
    },
    {
        key: 'role',
        label: 'Role'
    },
    {
        key: 'status',
        label: 'Status'
    },
    {
        key: 'created_at',
        label: 'Dibuat'
    }
];

const actions = [
    {
        label: 'Lihat Detail',
        icon: 'view',
        onClick: (user: User) => viewUser(user)
    },
    {
        label: 'Edit User',
        icon: 'edit',
        onClick: (user: User) => editUser(user)
    },
    {
        label: 'Hapus User',
        icon: 'delete',
        variant: 'danger' as const,
        onClick: (user: User) => confirmDelete(user),
        show: (user: User) => user.id !== 1 // Prevent deleting admin
    }
];

// Filter configuration
const filterFields = [
    {
        type: 'text' as const,
        key: 'search',
        label: 'Cari User',
        placeholder: 'Nama atau email...',
        icon: 'search'
    },
    {
        type: 'select' as const,
        key: 'role_filter',
        label: 'Filter Role',
        placeholder: 'Semua Role',
        options: props.roles.map(role => ({
            value: role.id,
            label: capitalizeRole(role.name)
        }))
    },
    {
        type: 'select' as const,
        key: 'status_filter',
        label: 'Filter Status',
        placeholder: 'Semua Status',
        options: [
            { value: 1, label: 'Aktif' },
            { value: 0, label: 'Nonaktif' }
        ]
    }
];

// Computed
const filters = ref(props.filters);

// Methods
const getRoleBadgeClass = (roleName: string) => {
    const classes = {
        'admin': 'bg-black text-white',
        'staff': 'bg-gray-800 text-white',
        'user': 'bg-gray-100 text-gray-800'
    };
    return classes[roleName?.toLowerCase() as keyof typeof classes] || 'bg-gray-100 text-gray-800';
};

const getInitials = (name: string) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Event handlers
const viewUser = async (user: User) => {
    selectedUser.value = user;
    showViewModal.value = true;
    userDetailsLoading.value = true;
    userDetails.value = null;

    try {
        console.log('Fetching user details for user ID:', user.id);
        const response = await fetch(`/admin/users/${user.id}/details`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Response error:', errorText);
            throw new Error(`HTTP ${response.status}: ${errorText}`);
        }

        const data = await response.json();
        console.log('User details loaded:', data);
        userDetails.value = data;
    } catch (error) {
        console.error('Error loading user details:', error);
        // Show error message to user
        alert('Gagal memuat detail user. Silakan coba lagi.');
    } finally {
        userDetailsLoading.value = false;
    }
};

const editUser = (user: User) => {
    selectedUser.value = user;
    editForm.value = {
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        role_id: user.role?.id?.toString() || '',
        is_active: user.is_active
    };
    showEditModal.value = true;
};

const confirmDelete = (user: User) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const handleFiltersUpdate = (newFilters: Record<string, any>) => {
    filters.value = newFilters;

    // Trigger filter request ke server dengan Inertia
    router.get('/admin/users', newFilters, {
        preserveState: true,
        replace: true,
        only: ['users', 'filters'] // Hanya reload data users dan filters
    });
};
</script>

<template>
    <AdminLayout>

        <Head title="User Management - Admin" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-black">User Management</h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola pengguna sistem</p>
                </div>
                <button @click="showCreateModal = true"
                    class="inline-flex items-center justify-center px-4 py-2.5 !bg-black !text-white text-sm font-medium rounded-lg hover:!bg-gray-800 transition-colors shadow-sm border-2 border-black cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah User
                </button>
            </div>

            <!-- Search & Filters -->
            <SearchFilter :fields="filterFields" v-model="filters" @update:model-value="handleFiltersUpdate"
                :stats-label="'Total User'" :stats-value="users.total" />

            <!-- Users Table -->
            <DataTable :columns="columns" :data="users.data" :actions="actions" empty-message="Tidak ada user"
                empty-icon="users">
                <!-- Custom cell templates -->
                <template #cell-user_info="{ row }">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ getInitials(row.name) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-black">{{ row.name }}</div>
                            <div class="text-sm text-gray-500">{{ row.email }}</div>
                        </div>
                    </div>
                </template>

                <template #cell-role="{ row }">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getRoleBadgeClass(row.role?.name)">
                        {{ capitalizeRole(row.role?.name) }}
                    </span>
                </template>

                <template #cell-status="{ row }">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="row.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'">
                        {{ row.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </template>

                <template #cell-created_at="{ row }">
                    <span class="text-sm text-gray-500">
                        {{ formatDate(row.created_at) }}
                    </span>
                </template>

                <template #empty-state>
                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan user baru.</p>
                </template>
            </DataTable>

            <!-- Pagination -->
            <Pagination :data="users" />
        </div>

        <!-- Create User Modal -->
        <AdminModal :show="showCreateModal" @close="closeCreateModal" title="Tambah User Baru"
            description="Buat akun pengguna baru untuk sistem" icon="create" icon-color="blue" max-width="lg">
            <form @submit.prevent="createUser" class="space-y-6">
                <!-- User Information Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Personal
                    </h4>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" v-model="createForm.name" placeholder="Masukkan nama lengkap"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                                    required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" v-model="createForm.email" placeholder="contoh@email.com"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                                    required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Keamanan Akun
                    </h4>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" v-model="createForm.password"
                                    @input="debouncedPasswordValidation(createForm.password)"
                                    placeholder="Minimal 8 karakter, huruf besar, kecil, dan angka" :class="[
                                        'w-full pl-10 pr-10 py-3 border rounded-xl bg-gray-50 focus:outline-none focus:bg-white transition-all',
                                        validationErrors.password
                                            ? 'border-red-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-red-50'
                                            : isPasswordValid
                                                ? 'border-green-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-green-50'
                                                : 'border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500'
                                    ]" required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5"
                                        :class="validationErrors.password ? 'text-red-400' : isPasswordValid ? 'text-green-400' : 'text-gray-400'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg v-if="validationErrors.password" class="h-5 w-5 text-red-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <svg v-else-if="isPasswordValid" class="h-5 w-5 text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div v-if="validationErrors.password" class="mt-2 flex items-center text-sm text-red-600">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ validationErrors.password }}
                            </div>
                            <div v-else-if="isPasswordValid" class="mt-2 flex items-center text-sm text-green-600">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Password memenuhi syarat
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" v-model="createForm.password_confirmation"
                                    @input="debouncedConfirmPasswordValidation(createForm.password_confirmation)"
                                    placeholder="Ulangi password yang sama" :class="[
                                        'w-full pl-10 pr-10 py-3 border rounded-xl bg-gray-50 focus:outline-none focus:bg-white transition-all',
                                        validationErrors.password_confirmation
                                            ? 'border-red-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-red-50'
                                            : isPasswordMatching
                                                ? 'border-green-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-green-50'
                                                : 'border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500'
                                    ]" required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5"
                                        :class="validationErrors.password_confirmation ? 'text-red-400' : isPasswordMatching ? 'text-green-400' : 'text-gray-400'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg v-if="validationErrors.password_confirmation" class="h-5 w-5 text-red-400"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <svg v-else-if="isPasswordMatching" class="h-5 w-5 text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div v-if="validationErrors.password_confirmation"
                                class="mt-2 flex items-center text-sm text-red-600">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ validationErrors.password_confirmation }}
                            </div>
                            <div v-else-if="isPasswordMatching" class="mt-2 flex items-center text-sm text-green-600">
                                <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Password cocok
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Settings Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Role & Pengaturan
                    </h4>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Role Pengguna <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select v-model="createForm.role_id"
                                    class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-black focus:border-black focus:bg-white transition-all appearance-none cursor-pointer"
                                    required>
                                    <option value="">Pilih Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ capitalizeRole(role.name) }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="bg-white border border-gray-200 rounded-xl p-4 w-full">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input type="checkbox" v-model="createForm.is_active" id="create_is_active"
                                            class="h-5 w-5 text-black border-gray-300 rounded focus:ring-black focus:ring-2" />
                                        <label for="create_is_active" class="ml-3 block text-sm font-medium text-black">
                                            Status Aktif
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2"
                                            :class="createForm.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                                        <span class="text-xs font-medium"
                                            :class="createForm.is_active ? 'text-green-600' : 'text-gray-500'">
                                            {{ createForm.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">User dapat login dan mengakses sistem</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="closeCreateModal"
                        class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium border border-gray-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit" :class="[
                        'px-6 py-3 rounded-xl transition-colors text-sm font-medium shadow-lg border-2',
                        isCreateFormValid
                            ? '!bg-black !text-white hover:!bg-gray-800 border-black'
                            : 'bg-gray-300 text-gray-500 cursor-not-allowed border-gray-300'
                    ]" :disabled="!isCreateFormValid">
                        <svg v-if="!createLoading" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <svg v-else class="w-4 h-4 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ createLoading ? 'Menyimpan...' : 'Buat User' }}
                    </button>
                </div>
            </form>
        </AdminModal>

        <!-- Edit User Modal -->
        <AdminModal :show="showEditModal" @close="closeEditModal" title="Edit User"
            description="Perbarui informasi pengguna sistem" icon="edit" icon-color="orange" max-width="lg">
            <form @submit.prevent="updateUser" class="space-y-6">
                <!-- User Information Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Personal
                    </h4>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" v-model="editForm.name" placeholder="Masukkan nama lengkap"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                                    required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" v-model="editForm.email" placeholder="contoh@email.com"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                                    required />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Update Password
                        <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Opsional</span>
                    </h4>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Password Baru
                            </label>
                            <div class="relative">
                                <input type="password" v-model="editForm.password"
                                    placeholder="Kosongkan jika tidak ingin mengubah"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-all" />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter jika ingin mengubah</p>
                        </div>

                        <div v-if="editForm.password">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" v-model="editForm.password_confirmation"
                                    placeholder="Ulangi password baru"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-all" />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div v-else class="flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm">Password tidak akan berubah</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role & Settings Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
                    <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Role & Pengaturan
                    </h4>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Role Pengguna <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select v-model="editForm.role_id"
                                    class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-black focus:border-black focus:bg-white transition-all appearance-none cursor-pointer"
                                    required>
                                    <option value="">Pilih Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ capitalizeRole(role.name) }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="bg-white border border-gray-200 rounded-xl p-4 w-full">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input type="checkbox" v-model="editForm.is_active" id="edit_is_active"
                                            class="h-5 w-5 text-black border-gray-300 rounded focus:ring-black focus:ring-2" />
                                        <label for="edit_is_active" class="ml-3 block text-sm font-medium text-black">
                                            Status Aktif
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2"
                                            :class="editForm.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                                        <span class="text-xs font-medium"
                                            :class="editForm.is_active ? 'text-green-600' : 'text-gray-500'">
                                            {{ editForm.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">User dapat login dan mengakses sistem</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="closeEditModal"
                        class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium border border-gray-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 !bg-black !text-white rounded-xl hover:!bg-gray-800 transition-colors text-sm font-medium shadow-lg border-2 border-black"
                        :disabled="editLoading">
                        <svg v-if="!editLoading" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <svg v-else class="w-4 h-4 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ editLoading ? 'Menyimpan...' : 'Update User' }}
                    </button>
                </div>
            </form>
        </AdminModal>

        <!-- View User Modal -->
        <AdminModal :show="showViewModal" @close="showViewModal = false" title="Detail User"
            description="Informasi lengkap pengguna sistem" icon="view" icon-color="gray" max-width="xl">

            <!-- Loading State -->
            <div v-if="userDetailsLoading" class="p-8 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black mx-auto mb-4"></div>
                <p class="text-gray-500">Memuat detail user...</p>
            </div>

            <!-- User Details -->
            <div v-else-if="userDetails" class="p-6">
                <!-- User Profile Section -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 mb-6 border border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-6">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div
                                class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center shadow-lg">
                                <span class="text-2xl font-bold text-gray-700">
                                    {{ getInitials(userDetails.user.name) }}
                                </span>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h4 class="text-2xl font-bold text-black mb-1">{{ userDetails.user.name }}</h4>
                                    <p class="text-gray-600 mb-3">{{ userDetails.user.email }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-medium"
                                            :class="getRoleBadgeClass(userDetails.user.role?.name)">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            {{ capitalizeRole(userDetails.user.role?.name) }}
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-medium"
                                            :class="userDetails.user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                            <div class="w-2 h-2 rounded-full mr-1.5"
                                                :class="userDetails.user.is_active ? 'bg-green-500' : 'bg-red-500'">
                                            </div>
                                            {{ userDetails.user.is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="flex space-x-2 mt-4 sm:mt-0">
                                    <button @click="editUser(userDetails.user)"
                                        class="p-2 bg-black text-white rounded-xl hover:bg-gray-800 transition-colors cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-black">{{ userDetails.stats.total_orders }}</p>
                                <p class="text-sm text-gray-600">Total Pesanan</p>
                            </div>
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-green-600">{{ userDetails.stats.completed_orders }}
                                </p>
                                <p class="text-sm text-gray-600">Selesai</p>
                            </div>
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-black">{{
                                    formatCurrency(userDetails.stats.total_spent) }}</p>
                                <p class="text-sm text-gray-600">Total Belanja</p>
                            </div>
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-purple-600">{{ userDetails.stats.cart_items }}</p>
                                <p class="text-sm text-gray-600">Item Keranjang</p>
                            </div>
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5H20" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Information -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Account Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h5 class="text-lg font-semibold text-black mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Akun
                        </h5>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">ID User</span>
                                <span class="font-medium text-black">#{{ userDetails.user.id }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Terdaftar</span>
                                <span class="font-medium text-black">{{ formatDate(userDetails.user.created_at)
                                    }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Umur Akun</span>
                                <span class="font-medium text-black">{{ userDetails.stats.account_age_formatted
                                    }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Email Verified</span>
                                <span v-if="userDetails.user.email_verified_at"
                                    class="inline-flex items-center text-green-600 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Ya
                                </span>
                                <span v-else class="inline-flex items-center text-red-600 font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Belum
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Aktivitas Terakhir</span>
                                <span class="font-medium text-black">{{ formatDate(userDetails.stats.last_activity)
                                    }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Order Statistics -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h5 class="text-lg font-semibold text-black mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Statistik Pesanan
                        </h5>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Total Pesanan</span>
                                <span class="font-bold text-blue-600">{{ userDetails.stats.total_orders }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Selesai</span>
                                <span class="font-bold text-green-600">{{ userDetails.stats.completed_orders }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Pending</span>
                                <span class="font-bold text-yellow-600">{{ userDetails.stats.pending_orders }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Dibatalkan</span>
                                <span class="font-bold text-red-600">{{ userDetails.stats.cancelled_orders }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Success Rate</span>
                                <span class="font-bold text-black">
                                    {{ userDetails.stats.total_orders > 0 ?
                                        Math.round((userDetails.stats.completed_orders /
                                    userDetails.stats.total_orders) * 100) : 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminModal>

        <!-- Delete Confirmation Modal -->
        <AdminModal :show="showDeleteModal" @close="showDeleteModal = false" title="Konfirmasi Hapus"
            description="Tindakan ini tidak dapat dibatalkan" icon="delete" icon-color="red" max-width="md">
            <!-- User Info -->
            <div v-if="userToDelete"
                class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 mb-6 border border-gray-100">
                <div class="flex items-center space-x-3">
                    <div
                        class="h-10 w-10 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">
                            {{ getInitials(userToDelete.name || '') }}
                        </span>
                    </div>
                    <div>
                        <h4 class="font-semibold text-black">{{ userToDelete.name }}</h4>
                        <p class="text-sm text-gray-600">{{ userToDelete.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Confirmation Message -->
            <div class="text-center mb-6">
                <p class="text-gray-700">
                    Apakah Anda yakin ingin menghapus user ini?
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Data user akan dihapus secara permanen dari sistem.
                </p>
            </div>

            <template #footer>
                <div class="flex justify-end space-x-3">
                    <button @click="showDeleteModal = false"
                        class="px-6 py-2.5 !text-gray-700 !bg-gray-100 rounded-lg hover:!bg-gray-200 transition-colors text-sm font-medium border border-gray-300">
                        Batal
                    </button>
                    <button @click="deleteUser"
                        class="px-6 py-2.5 !bg-red-600 !text-white rounded-lg hover:!bg-red-700 transition-colors text-sm font-medium border border-red-600">
                        Hapus
                    </button>
                </div>
            </template>
        </AdminModal>
    </AdminLayout>
</template>
