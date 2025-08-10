<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import AdminModal from './AdminModal.vue';

// Types
interface Category {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    stock: number;
    category_id: number;
    is_active: boolean;
    image_path?: string;
    specs?: Record<string, string>;
}

interface Props {
    show: boolean;
    mode: 'create' | 'edit';
    product?: Product | null;
    categories: Category[];
}

const props = withDefaults(defineProps<Props>(), {
    product: null
});

const emit = defineEmits<{
    close: [];
    success: [];
}>();

// Form setup
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

// Local state
const imagePreview = ref<string | null>(null);
const specKey = ref('');
const specValue = ref('');

// Computed properties
const isEditMode = computed(() => props.mode === 'edit');
const modalTitle = computed(() => isEditMode.value ? 'Edit Product' : 'Create Product');
const modalDescription = computed(() => isEditMode.value ? 'Update product information' : 'Add new product to catalog');
const modalIcon = computed(() => isEditMode.value ? 'edit' : 'create');
const modalIconColor = computed(() => isEditMode.value ? 'orange' : 'blue');

// Watch for prop changes
watch(() => props.show, (show) => {
    if (show) {
        resetForm();
    }
});

watch(() => props.product, (product) => {
    if (product && props.mode === 'edit') {
        populateForm(product);
    }
}, { immediate: true });

// Methods
const resetForm = () => {
    if (props.mode === 'create') {
        form.reset();
        form.clearErrors();
        imagePreview.value = null;
        specKey.value = '';
        specValue.value = '';
    } else if (props.product) {
        populateForm(props.product);
    }
};

const populateForm = (product: Product) => {
    form.name = product.name;
    form.description = product.description;
    form.price = product.price;
    form.stock = product.stock;
    form.category_id = product.category_id;
    form.is_active = product.is_active;
    form.specs = product.specs || {};
    form.image = null;
    imagePreview.value = product.image_path ? `/storage/${product.image_path}` : null;
    specKey.value = '';
    specValue.value = '';
};

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

const close = () => {
    emit('close');
};

const submit = () => {
    if (props.mode === 'create') {
        form.post(route('admin.products.store'), {
            forceFormData: true,
            onSuccess: () => {
                emit('success');
                close();
            }
        });
    } else if (props.product) {
        form.put(route('admin.products.update', props.product.id), {
            forceFormData: true,
            onSuccess: () => {
                emit('success');
                close();
            }
        });
    }
};
</script>

<template>
    <AdminModal
        :show="show"
        @close="close"
        :title="modalTitle"
        :description="modalDescription"
        :icon="modalIcon"
        :icon-color="modalIconColor"
        max-width="xl"
    >
        <form id="product-form" @submit.prevent="submit" class="space-y-8">
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
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

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-black mb-2">
                                Price <span class="text-red-500">*</span>
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
                                Stock <span class="text-red-500">*</span>
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

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-black mb-2">Status</label>
                            <div class="flex items-center space-x-3">
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.is_active"
                                        :value="true"
                                        class="text-black focus:ring-black"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.is_active"
                                        :value="false"
                                        class="text-black focus:ring-black"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
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
                                placeholder="Enter product description"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                :class="{ 'border-red-300': form.errors.description }"
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Image -->
                <div>
                    <h3 class="text-lg font-semibold text-black mb-4">Product Image</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-black mb-2">Upload Image</label>
                            <input
                                type="file"
                                @change="handleImageChange"
                                accept="image/jpeg,image/png,image/jpg,image/gif"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                                :class="{ 'border-red-300': form.errors.image }"
                            />
                            <p class="mt-1 text-sm text-gray-500">Supported formats: JPEG, PNG, JPG, GIF (max 2MB)</p>
                            <div v-if="form.errors.image" class="mt-1 text-sm text-red-600">
                                {{ form.errors.image }}
                            </div>
                        </div>
                        
                        <!-- Image Preview -->
                        <div v-if="imagePreview" class="mt-4">
                            <label class="block text-sm font-medium text-black mb-2">Preview</label>
                            <div class="w-32 h-32 border border-gray-300 rounded-lg overflow-hidden">
                                <img :src="imagePreview" alt="Preview" class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specifications -->
                <div>
                    <h3 class="text-lg font-semibold text-black mb-4">Specifications</h3>
                    
                    <div class="space-y-4">
                        <!-- Add Spec Form -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input
                                type="text"
                                v-model="specKey"
                                placeholder="Specification name (e.g., Material)"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                            />
                            <input
                                type="text"
                                v-model="specValue"
                                placeholder="Value (e.g., Solid Wood)"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black"
                            />
                            <button
                                type="button"
                                @click="addSpec"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium"
                            >
                                Add Spec
                            </button>
                        </div>
                        
                        <!-- Specs List -->
                        <div v-if="Object.keys(form.specs).length > 0" class="space-y-2">
                            <div
                                v-for="(value, key) in form.specs"
                                :key="key"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div>
                                    <span class="font-medium text-black">{{ key }}:</span>
                                    <span class="text-gray-700 ml-2">{{ value }}</span>
                                </div>
                                <button
                                    type="button"
                                    @click="removeSpec(key)"
                                    class="text-red-500 hover:text-red-700 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <template #footer>
            <div class="flex justify-end space-x-3 bg-gray-50 -m-6 p-6 rounded-b-2xl">
                <button
                    type="button"
                    @click="close"
                    class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium shadow-sm"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    form="product-form"
                    :disabled="form.processing"
                    class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors text-sm font-medium disabled:opacity-50 shadow-sm"
                    style="background-color: #000000 !important;"
                >
                    <svg v-if="!form.processing" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? 'Saving...' : (isEditMode ? 'Update Product' : 'Create Product') }}
                </button>
            </div>
        </template>
    </AdminModal>
</template>
