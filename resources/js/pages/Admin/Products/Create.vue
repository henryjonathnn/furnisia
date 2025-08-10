<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { ref } from 'vue';

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    name: '',
    description: '',
    price: 0,
    stock: 0,
    category_id: '',
    is_active: true,
    specs: {} as Record<string, string>,
    image: null as File | null
});

const imagePreview = ref<string | null>(null);
const specKey = ref('');
const specValue = ref('');

// Methods
const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        form.image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const addSpec = () => {
    if (specKey.value && specValue.value) {
        form.specs[specKey.value] = specValue.value;
        specKey.value = '';
        specValue.value = '';
    }
};

const removeSpec = (key: string) => {
    delete form.specs[key];
};

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Redirect handled by controller
        }
    });
};

const cancel = () => {
    router.visit('/admin/products');
};
</script>

<template>
    <AdminLayout>
        <Head title="Create Product - Admin" />

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-black">Create New Product</h1>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">Add a new product to your catalog</p>
                </div>
                <button
                    @click="cancel"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-gray-700 bg-white border-2 border-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Products
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-8">
                <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-black mb-4">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-black mb-2">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    required
                                    placeholder="Enter product name"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                    :class="{ 'border-red-300': form.errors.name }"
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-black mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.category_id"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black appearance-none"
                                    :class="{ 'border-red-300': form.errors.category_id }"
                                >
                                    <option value="">Select Category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.category_id }}
                                </div>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-black mb-2">Status</label>
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_active"
                                        id="is_active"
                                        class="h-4 w-4 text-black border-gray-300 rounded focus:ring-black"
                                    />
                                    <label for="is_active" class="text-sm text-gray-700">
                                        Product is active and visible to customers
                                    </label>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-black mb-2">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.description"
                                    required
                                    rows="4"
                                    placeholder="Describe your product in detail"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                    :class="{ 'border-red-300': form.errors.description }"
                                ></textarea>
                                <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div>
                        <h3 class="text-lg font-semibold text-black mb-4">Pricing & Inventory</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-black mb-2">
                                    Price (IDR) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input
                                        type="number"
                                        v-model="form.price"
                                        required
                                        min="0"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                        :class="{ 'border-red-300': form.errors.price }"
                                    />
                                </div>
                                <div v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.price }}
                                </div>
                            </div>

                            <!-- Stock -->
                            <div>
                                <label class="block text-sm font-medium text-black mb-2">
                                    Initial Stock <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    v-model="form.stock"
                                    required
                                    min="0"
                                    placeholder="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                    :class="{ 'border-red-300': form.errors.stock }"
                                />
                                <div v-if="form.errors.stock" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.stock }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Image -->
                    <div>
                        <h3 class="text-lg font-semibold text-black mb-4">Product Image</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-black mb-2">Upload Image</label>
                                <input
                                    type="file"
                                    @change="handleImageChange"
                                    accept="image/jpeg,image/png,image/jpg,image/gif"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800"
                                    :class="{ 'border-red-300': form.errors.image }"
                                />
                                <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                <div v-if="form.errors.image" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.image }}
                                </div>
                            </div>

                            <!-- Image Preview -->
                            <div v-if="imagePreview">
                                <label class="block text-sm font-medium text-black mb-2">Preview</label>
                                <img 
                                    :src="imagePreview" 
                                    alt="Product preview"
                                    class="w-32 h-32 object-cover rounded-lg border border-gray-200"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Product Specifications -->
                    <div>
                        <h3 class="text-lg font-semibold text-black mb-4">Product Specifications</h3>
                        
                        <!-- Add Spec Form -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <input
                                    type="text"
                                    v-model="specKey"
                                    placeholder="Specification name (e.g., Material)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                />
                            </div>
                            <div>
                                <input
                                    type="text"
                                    v-model="specValue"
                                    placeholder="Specification value (e.g., Wood)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                />
                            </div>
                            <div>
                                <button
                                    type="button"
                                    @click="addSpec"
                                    class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                                >
                                    Add Spec
                                </button>
                            </div>
                        </div>

                        <!-- Existing Specs -->
                        <div v-if="Object.keys(form.specs).length > 0" class="space-y-2">
                            <div 
                                v-for="(value, key) in form.specs" 
                                :key="key"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div>
                                    <span class="font-medium text-black">{{ key }}:</span>
                                    <span class="text-gray-600 ml-2">{{ value }}</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeSpec(key)"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="form.errors.specs" class="mt-2 text-sm text-red-600">
                            {{ form.errors.specs }}
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4">
                    <button
                        type="button"
                        @click="cancel"
                        class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? 'Creating...' : 'Create Product' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
