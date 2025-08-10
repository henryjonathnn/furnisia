<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/20 backdrop-blur-sm transition-all duration-300" @click="close"></div>
      
      <div class="relative w-full max-w-md bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20">
        <!-- Header -->
        <div class="flex items-center justify-between p-5 border-b border-gray-200/50">
          <h3 class="text-lg font-semibold text-gray-900">Filter & Urutkan</h3>
          <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-5 space-y-5 max-h-80 overflow-y-auto">
          
          <!-- Category Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <div class="space-y-1.5">
              <label class="flex items-center">
                <input 
                  type="radio" 
                  v-model="localFilters.category" 
                  :value="null"
                  class="h-4 w-4 text-black border-gray-300 focus:ring-black"
                />
                <span class="ml-3 text-sm text-gray-700">Semua Kategori</span>
              </label>
              <label v-for="category in categories" :key="category.id" class="flex items-center">
                <input 
                  type="radio" 
                  v-model="localFilters.category" 
                  :value="category.slug"
                  class="h-4 w-4 text-black border-gray-300 focus:ring-black"
                />
                <span class="ml-3 text-sm text-gray-700">
                  {{ category.name }} 
                  <span class="text-gray-500">({{ category.product_count }})</span>
                </span>
              </label>
            </div>
          </div>

          <!-- Price Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Harga</label>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs text-gray-500 mb-1">Minimum</label>
                <input 
                  type="number" 
                  v-model="localFilters.min_price"
                  :min="priceRange.min"
                  :max="priceRange.max"
                  :placeholder="`${formatPrice(priceRange.min)}`"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-black focus:border-black"
                />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Maksimum</label>
                <input 
                  type="number" 
                  v-model="localFilters.max_price"
                  :min="priceRange.min"
                  :max="priceRange.max"
                  :placeholder="`${formatPrice(priceRange.max)}`"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-black focus:border-black"
                />
              </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
              Range: {{ formatPrice(priceRange.min) }} - {{ formatPrice(priceRange.max) }}
            </div>
          </div>

          <!-- Sort Options -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
            <div class="space-y-1.5">
              <label v-for="option in sortOptions" :key="option.value" class="flex items-center">
                <input 
                  type="radio" 
                  v-model="localFilters.sort" 
                  :value="option.value"
                  class="h-4 w-4 text-black border-gray-300 focus:ring-black"
                />
                <span class="ml-3 text-sm text-gray-700">{{ option.label }}</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between p-5 border-t border-gray-200/50 bg-gray-50/80 backdrop-blur-sm rounded-b-2xl">
          <button 
            @click="resetFilters"
            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm"
          >
            Reset
          </button>
          <div class="flex space-x-3">
            <button 
              @click="close"
              class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm"
            >
              Batal
            </button>
            <button 
              @click="applyFilters"
              class="px-6 py-2.5 text-sm font-medium text-white rounded-xl hover:bg-gray-800 transition-all duration-200 shadow-lg"
              style="background-color: #000000 !important;"
            >
              Terapkan Filter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'

interface Category {
  id: number
  name: string
  slug: string
  product_count: number
}

interface PriceRange {
  min: number
  max: number
}

interface CurrentFilters {
  category?: string
  search?: string
  min_price?: number
  max_price?: number
  sort: string
}

interface Props {
  show: boolean
  categories: Category[]
  priceRange: PriceRange
  currentFilters: CurrentFilters
}

interface Emits {
  (e: 'update:show', value: boolean): void
  (e: 'apply-filters', filters: any): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const localFilters = ref({
  category: props.currentFilters.category || null,
  min_price: props.currentFilters.min_price || null,
  max_price: props.currentFilters.max_price || null,
  sort: props.currentFilters.sort || 'latest'
})

const sortOptions = [
  { value: 'latest', label: 'Terbaru' },
  { value: 'price_low', label: 'Harga Terendah' },
  { value: 'price_high', label: 'Harga Tertinggi' },
  { value: 'name_asc', label: 'Nama A-Z' },
  { value: 'name_desc', label: 'Nama Z-A' },
  { value: 'sold_high', label: 'Terlaris' },
  { value: 'sold_low', label: 'Kurang Laris' },
  { value: 'stock_high', label: 'Stok Terbanyak' },
  { value: 'stock_low', label: 'Stok Terbatas' }
]

const formatPrice = (price: number) => {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(price)
}

const close = () => {
  emit('update:show', false)
}

const resetFilters = () => {
  localFilters.value = {
    category: null,
    min_price: null,
    max_price: null,
    sort: 'latest'
  }
}

const applyFilters = () => {
  emit('apply-filters', {
    category: localFilters.value.category,
    min_price: localFilters.value.min_price,
    max_price: localFilters.value.max_price,
    sort: localFilters.value.sort
  })
}

// Reset local filters when modal opens
watch(() => props.show, (newValue) => {
  if (newValue) {
    localFilters.value = {
      category: props.currentFilters.category || null,
      min_price: props.currentFilters.min_price || null,
      max_price: props.currentFilters.max_price || null,
      sort: props.currentFilters.sort || 'latest'
    }
  }
})
</script>
