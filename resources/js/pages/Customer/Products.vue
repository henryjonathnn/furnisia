<template>
  <CustomerLayout>
    <Head :title="`${filterInfo.type === 'semua produk' ? 'Semua Produk' : `${filterInfo.value}`} - Furnisia`" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            
            <!-- Left Side - Filter Info -->
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 capitalize">
                  {{ filterInfo.type === 'pencarian' ? `Hasil pencarian "${filterInfo.value}"` : 
                      filterInfo.type === 'kategori' ? `Kategori ${filterInfo.value}` : 
                      'Semua Produk' }}
                </h1>
              </div>
              <p class="text-gray-600">{{ filterInfo.description }}</p>
              <p class="text-sm text-gray-500 mt-1">
                Menampilkan <span class="font-semibold text-gray-700">{{ products.total }}</span> produk
                <span v-if="products.current_page > 1">
                  (Halaman {{ products.current_page }} dari {{ products.last_page }})
                </span>
              </p>
            </div>

            <!-- Right Side - Filter Button -->
            <div class="flex items-center gap-3">
              <button 
                @click="showFilterModal = true"
                class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                Filter & Urutkan
              </button>
            </div>
          </div>

          <!-- Active Filters Display -->
          <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-sm font-medium text-gray-700">Filter aktif:</span>
              
              <span v-if="currentFilters.category" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                Kategori: {{ getCategoryName(currentFilters.category) }}
                <button @click="removeFilter('category')" class="ml-2 text-blue-600 hover:text-blue-800">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span v-if="currentFilters.min_price || currentFilters.max_price" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                Harga: {{ formatPrice(currentFilters.min_price || 0) }} - {{ formatPrice(currentFilters.max_price || 999999999) }}
                <button @click="removeFilter('price')" class="ml-2 text-green-600 hover:text-green-800">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <span v-if="currentFilters.sort !== 'latest'" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-sm font-medium rounded-full">
                Urutan: {{ getSortLabel(currentFilters.sort) }}
                <button @click="removeFilter('sort')" class="ml-2 text-purple-600 hover:text-purple-800">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>

              <button @click="clearAllFilters" class="text-sm text-red-600 hover:text-red-800 font-medium">
                Hapus semua filter
              </button>
            </div>
          </div>
        </div>

        <!-- Products Grid -->
        <div v-if="products.data.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
          <Link
            v-for="product in products.data" 
            :key="product.id"
            :href="`/products/${product.slug}`"
            class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-gray-300 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
          >
            <!-- Stock Status Badge -->
            <div class="absolute top-3 right-3 z-10">
              <span class="px-2 py-1 text-xs font-medium rounded-lg border backdrop-blur-sm" :class="product.stock_status.badge_class">
                {{ product.stock_status.label }}
              </span>
            </div>

            <!-- Product Image -->
            <div class="aspect-square overflow-hidden bg-white relative">
              <img 
                v-if="product.image" 
                :src="`/storage/${product.image}`" 
                :alt="product.name" 
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div v-else class="flex h-full items-center justify-center">
                <Icon name="package" class="h-12 w-12 text-gray-400" />
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                  {{ product.category }}
                </span>
                <span class="text-xs font-medium text-gray-600">
                  {{ product.sold }} terjual
                </span>
              </div>
              
              <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 text-sm lg:text-base">{{ product.name }}</h3>
              
              <div class="flex items-center justify-between">
                <div class="text-base lg:text-lg font-bold text-gray-900">{{ product.price }}</div>
                <div class="text-xs text-gray-600">Stok: {{ product.stock }}</div>
              </div>
            </div>
          </Link>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
          <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada produk ditemukan</h3>
          <p class="text-gray-600 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
          <button @click="clearAllFilters" class="inline-flex items-center px-6 py-3 bg-black text-white rounded-xl hover:bg-gray-800 transition-colors">
            Hapus semua filter
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="products.data.length > 0 && products.last_page > 1" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Menampilkan {{ products.from }} - {{ products.to }} dari {{ products.total }} produk
            </div>
            
            <div class="flex items-center space-x-2">
              <Link
                v-if="products.prev_page_url"
                :href="products.prev_page_url"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Sebelumnya
              </Link>

              <div class="flex items-center space-x-1">
                <template v-for="page in paginationPages" :key="page">
                  <Link
                    v-if="page.url"
                    :href="page.url"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors rounded-lg"
                    :class="page.active 
                      ? 'bg-black text-white' 
                      : 'text-gray-700 border border-gray-300 bg-white hover:bg-gray-50'"
                  >
                    {{ page.label }}
                  </Link>
                  <span v-else class="inline-flex items-center px-3 py-2 text-sm text-gray-500">
                    {{ page.label }}
                  </span>
                </template>
              </div>

              <Link
                v-if="products.next_page_url"
                :href="products.next_page_url"
                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
              >
                Selanjutnya
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Modal -->
    <FilterModal 
      v-model:show="showFilterModal"
      :categories="categories"
      :price-range="priceRange"
      :current-filters="currentFilters"
      @apply-filters="applyFilters"
    />
  </CustomerLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import FilterModal from '@/components/customer/FilterModal.vue'
import Icon from '@/components/ui/Icon.vue'

interface Product {
  id: string
  name: string
  description: string
  price: string
  raw_price: number
  image: string | null
  category: string
  stock: number
  sold: number
  stock_status: {
    label: string
    color: string
    badge_class: string
  }
  slug: string
  created_at: string
}

interface Category {
  id: number
  name: string
  slug: string
  product_count: number
}

interface FilterInfo {
  type: string
  value: string
  description: string
}

interface CurrentFilters {
  category?: string
  search?: string
  min_price?: number
  max_price?: number
  sort: string
}

interface PaginatedProducts {
  data: Product[]
  current_page: number
  last_page: number
  total: number
  from: number
  to: number
  prev_page_url: string | null
  next_page_url: string | null
  links: Array<{
    url: string | null
    label: string
    active: boolean
  }>
}

interface Props {
  products: PaginatedProducts
  filterInfo: FilterInfo
  categories: Category[]
  priceRange: {
    min: number
    max: number
  }
  currentFilters: CurrentFilters
}

const props = defineProps<Props>()

const showFilterModal = ref(false)

const hasActiveFilters = computed(() => {
  return props.currentFilters.category || 
         props.currentFilters.search ||
         props.currentFilters.min_price ||
         props.currentFilters.max_price ||
         (props.currentFilters.sort && props.currentFilters.sort !== 'latest')
})

const paginationPages = computed(() => {
  return props.products.links.filter(link => 
    link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'
  )
})

const getCategoryName = (slug: string) => {
  const category = props.categories.find(cat => cat.slug === slug)
  return category?.name || slug
}

const getSortLabel = (sort: string) => {
  const labels: Record<string, string> = {
    'latest': 'Terbaru',
    'price_low': 'Harga Terendah',
    'price_high': 'Harga Tertinggi',
    'name_asc': 'Nama A-Z',
    'name_desc': 'Nama Z-A',
    'sold_high': 'Terlaris',
    'sold_low': 'Kurang Laris',
    'stock_high': 'Stok Terbanyak',
    'stock_low': 'Stok Terbatas'
  }
  return labels[sort] || sort
}

const formatPrice = (price: number) => {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(price)
}

const removeFilter = (filterType: string) => {
  const currentUrl = new URL(window.location.href)
  const params = new URLSearchParams(currentUrl.search)
  
  if (filterType === 'category') {
    params.delete('category')
  } else if (filterType === 'price') {
    params.delete('min_price')
    params.delete('max_price')
  } else if (filterType === 'sort') {
    params.delete('sort')
  }
  
  router.get(`/products?${params.toString()}`)
}

const clearAllFilters = () => {
  router.get('/products')
}

const applyFilters = (filters: any) => {
  const params = new URLSearchParams()
  
  // Keep search if exists
  if (props.currentFilters.search) {
    params.append('search', props.currentFilters.search)
  }
  
  // Add new filters
  if (filters.category) {
    params.append('category', filters.category)
  }
  if (filters.min_price) {
    params.append('min_price', filters.min_price.toString())
  }
  if (filters.max_price) {
    params.append('max_price', filters.max_price.toString())
  }
  if (filters.sort && filters.sort !== 'latest') {
    params.append('sort', filters.sort)
  }
  
  router.get(`/products?${params.toString()}`)
  showFilterModal.value = false
}
</script>
