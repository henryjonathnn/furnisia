<template>
  <CustomerLayout>
    <Head title="Furnisia - Premium Home Furniture Store" />

    <div class="min-h-screen bg-gray-50">
    <!-- Categories Section -->
      <section class="py-12 bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Belanja Berdasarkan Kategori</h2>
            <p class="text-gray-600">Temukan produk sesuai kebutuhan ruangan Anda</p>
        </div>

          <!-- Categories Horizontal Scroll -->
          <div class="flex justify-center">
            <div class="flex overflow-x-auto gap-6 pb-4 scrollbar-hide max-w-full">
              <Link 
            v-for="category in categories" 
            :key="category.id"
                :href="`/products?category=${category.slug}`"
                class="flex-shrink-0 group cursor-pointer"
          >
                <div class="w-36 bg-white rounded-2xl border border-gray-200 p-6 text-center hover:shadow-lg hover:border-black transition-all duration-300 hover:scale-105">
                  <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-2xl flex items-center justify-center group-hover:bg-black transition-colors duration-300 p-2">
              <img 
                :src="getCategoryIcon(category.name)" 
                :alt="category.name"
                      class="w-full h-full object-contain filter grayscale group-hover:grayscale-0 group-hover:brightness-0 group-hover:invert transition-all duration-300"
                loading="lazy"
              />
            </div>
                  <h3 class="font-semibold text-gray-900 text-sm mb-1 group-hover:text-black">{{ category.name }}</h3>
                  <p class="text-xs text-gray-500 group-hover:text-gray-600">{{ category.product_count }} item</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </section>

      <!-- Best Sellers Section -->
      <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Section Header -->
            <div class="bg-gradient-to-r from-orange-50 to-red-50 p-8 border-b border-gray-200">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
                <div>
                  <h2 class="text-3xl font-bold text-gray-900 mb-2">Produk Terlaris</h2>
                  <p class="text-gray-600">Pilihan favorit pelanggan dengan penjualan terbaik</p>
                </div>
                <div class="ml-auto">
                  <span class="px-4 py-2 bg-orange-100 text-orange-700 text-sm font-semibold rounded-full">
                    🔥 Hot Items
                  </span>
          </div>
          </div>
        </div>

            <!-- Products Grid -->
            <div class="p-8">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                  v-for="(product, index) in bestSellers" 
                  :key="product.id"
                  :href="`/products/${product.slug}`"
                  class="group relative bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl hover:border-gray-300 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                >
                  <!-- Bestseller Badge -->
                  <div class="absolute top-3 left-3 z-10">
                    <div class="flex items-center space-x-1 px-2 py-1 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
                      <span>#{{ index + 1 }}</span>
        </div>
      </div>

                  <!-- Stock Badge -->
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
                      <Icon name="package" class="h-16 w-16 text-gray-400" />
              </div>
            </div>

            <!-- Product Info -->
                  <div class="p-4 bg-white">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-xs font-medium text-orange-600 bg-orange-100 px-2 py-1 rounded-full">
                        {{ product.sold }} terjual
                      </span>
                      <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                        {{ product.category }}
                      </span>
                    </div>
                    
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-1 text-sm">{{ product.name }}</h3>
                    
              <div class="flex items-center justify-between mb-3">
                      <div class="text-lg font-bold text-gray-900">{{ product.price }}</div>
              </div>
              

                  </div>
                </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

      <!-- Limited Stock Section -->
      <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-white rounded-3xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Section Header -->
            <div class="bg-gradient-to-r from-red-50 to-pink-50 p-8 border-b border-gray-200">
              <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-3xl font-bold text-gray-900 mb-2">Produk Terbatas</h2>
                  <p class="text-gray-600">Stok terbatas! Segera dapatkan sebelum kehabisan</p>
                </div>
                <div class="ml-auto">
                  <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-full">
                    ⚡ Limited Stock
                  </span>
                </div>
              </div>
        </div>

            <!-- Products Grid -->
            <div class="p-8">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                  v-for="(product, index) in limitedProducts" 
            :key="product.id"
                  :href="`/products/${product.slug}`"
                  class="group relative bg-gray-50 rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl hover:border-gray-300 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                >
                  <!-- Limited Badge -->
                  <div class="absolute top-3 left-3 z-10">
                    <div class="flex items-center space-x-1 px-2 py-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full shadow-lg">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                      </svg>
                      <span>{{ product.stock }} left</span>
              </div>
            </div>

                  <!-- Stock Badge -->
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
                      <Icon name="package" class="h-16 w-16 text-gray-400" />
              </div>
              
                    <!-- Urgency Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-red-900/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Product Info -->
                  <div class="p-4 bg-white">
              <div class="flex items-center justify-between mb-2">
                      <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full">
                        Stok: {{ product.stock }}
                      </span>
                      <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                        {{ product.category }}
                      </span>
              </div>
              
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-1 text-sm">{{ product.name }}</h3>
              
                    <div class="flex items-center justify-between mb-3">
                      <div class="text-lg font-bold text-gray-900">{{ product.price }}</div>
              </div>


          </div>
                </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import Icon from '@/components/ui/Icon.vue'

interface Product {
  id: number
  name: string
  price: string
  image?: string
  category?: string
  stock: number
  stock_status: {
    label: string
    color: string
    badge_class: string
  }
  slug: string
}

interface BestSeller extends Product {
  sold: number
}

interface Category {
  id: number
  name: string
  slug: string
  description: string
  product_count: number
}

interface Stats {
  total_products: number
  total_categories: number
  happy_customers: number
  years_experience: number
}

defineProps<{
  categories: Category[]
  bestSellers: BestSeller[]
  limitedProducts: Product[]
  stats: Stats
}>()

// Methods
const addToCart = (product: Product) => {
  if (product.stock === 0) return
  
  // TODO: Implement add to cart functionality
  console.log('Adding to cart:', product.name)
  // This will be implemented when we have cart functionality
}

// Generate icon path from category name
const getCategoryIcon = (categoryName: string): string => {
  // Convert category name to kebab-case filename with all lowercase and hyphens
  const filename = categoryName
    .toLowerCase()
    .replace(/\s+/g, '-')
    .replace(/[^\w\-]+/g, '')
  
  // Return path to PNG icon in public/images/category-icons/
  return `/images/category-icons/${filename}.png`
}
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
}
</style>
