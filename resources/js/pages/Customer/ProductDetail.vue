<template>
  <CustomerLayout>
    <Head :title="`${product.name} - Furnisia`" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
          <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
              <Link href="/" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-black transition-colors">
                <Icon name="home" class="w-4 h-4 mr-2" />
                Home
              </Link>
            </li>
            <li>
              <div class="flex items-center">
                <Icon name="chevron-right" class="w-4 h-4 text-gray-400" />
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Products</span>
              </div>
            </li>
            <li aria-current="page">
              <div class="flex items-center">
                <Icon name="chevron-right" class="w-4 h-4 text-gray-400" />
                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 truncate">{{ product.name }}</span>
              </div>
            </li>
          </ol>
        </nav>

        <!-- Main Product Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Product Images -->
            <div class="p-6">
              <div class="aspect-square rounded-2xl overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 shadow-inner">
                <img 
                  v-if="product.image_path" 
                  :src="`/storage/${product.image_path}`" 
                  :alt="product.name"
                  class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <Icon name="package" class="w-24 h-24 text-gray-400" />
                </div>
              </div>
            </div>

            <!-- Product Information -->
            <div class="p-6 space-y-6">
              <!-- Header -->
              <div class="space-y-4">
                <!-- Category and Status Badges -->
                <div class="flex items-center gap-3 flex-wrap">
                  <span v-if="product.category" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 text-sm font-medium rounded-full border border-blue-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ product.category.name }}
                  </span>
                  <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full border" :class="product.stock_status.badge_class">
                    <div class="w-2 h-2 rounded-full mr-2" :class="product.stock_status.color === 'green' ? 'bg-green-500' : product.stock_status.color === 'yellow' ? 'bg-yellow-500' : 'bg-red-500'"></div>
                    {{ product.stock_status.label }}
                  </span>
                </div>
                
                <!-- Product Title -->
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ product.name }}</h1>
                
                <!-- Description -->
                <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
              </div>

              <!-- Price Section -->
              <div class="bg-gradient-to-r from-gray-50 to-white rounded-xl p-4 border border-gray-200">
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <div class="flex items-center space-x-2 mb-2">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                      </svg>
                      <span class="text-sm font-medium text-gray-500">Harga</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ product.formatted_price }}</p>
                  </div>
                  <div>
                    <div class="flex items-center space-x-2 mb-2">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                      </svg>
                      <span class="text-sm font-medium text-gray-500">Stok Tersedia</span>
                    </div>
                    <p class="text-2xl font-bold" :class="product.stock > 10 ? 'text-green-600' : product.stock > 0 ? 'text-yellow-600' : 'text-red-600'">
                      {{ product.stock }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Quantity Selector -->
              <div v-if="product.stock > 0" class="space-y-4">
                <label class="block text-sm font-medium text-gray-700">Jumlah Pembelian</label>
                <div class="flex items-center space-x-4">
                  <button 
                    @click="decreaseQuantity"
                    :disabled="quantity <= 1"
                    class="w-12 h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center hover:border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                  >
                    <Icon name="minus" class="w-5 h-5 text-gray-600" />
                  </button>
                  <div class="flex-1 max-w-[120px]">
                    <input 
                      v-model="quantity" 
                      type="number" 
                      min="1" 
                      :max="product.stock"
                      class="w-full text-center text-lg font-semibold border-2 border-gray-200 rounded-xl py-3 focus:ring-2 focus:ring-black focus:border-black transition-all"
                    />
                  </div>
                  <button 
                    @click="increaseQuantity"
                    :disabled="quantity >= product.stock"
                    class="w-12 h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center hover:border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                  >
                    <Icon name="plus" class="w-5 h-5 text-gray-600" />
                  </button>
                </div>
                
                <!-- Total Price Display -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-700 font-medium">Total Harga</span>
                    <span class="text-xl font-bold text-gray-900">{{ totalPrice }}</span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="space-y-3 bg-gray-50 -m-6 mt-0 p-6 rounded-b-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <button 
                    @click="addToCart"
                    :disabled="product.stock === 0 || isAddingToCart"
                    class="w-full bg-black text-white py-3 px-6 rounded-xl font-semibold hover:bg-gray-800 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 shadow-md"
                    style="background-color: #000000 !important;"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 8l.4 2M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6.18" />
                    </svg>
                    <span>{{ isAddingToCart ? 'Menambahkan...' : (product.stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis') }}</span>
                  </button>
                  
                  <button 
                    @click="buyNow"
                    :disabled="product.stock === 0"
                    class="w-full bg-gray-800 text-white py-3 px-6 rounded-xl font-semibold hover:bg-gray-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 shadow-md"
                    style="background-color: #374151 !important;"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Beli Sekarang</span>
                  </button>
                </div>
                
                <!-- Security Features -->
                <div class="flex items-center justify-center space-x-6 text-sm text-gray-500 pt-4 border-t border-gray-200">
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Garansi Kualitas</span>
                  </div>
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                    </svg>
                    <span>Pengiriman Aman</span>
                  </div>
                  <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Pembayaran Aman</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Information Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
          <!-- Product Specifications -->
          <div v-if="product.specs && Object.keys(product.specs).length > 0" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white p-4 border-b border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Spesifikasi Produk</h3>
              </div>
            </div>
            <div class="p-4">
              <div class="space-y-3">
                <div v-for="(value, key) in product.specs" :key="key" class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                  <span class="font-medium text-gray-700">{{ key }}</span>
                  <span class="text-gray-900 font-semibold">{{ value }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Product Description -->
          <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Deskripsi Produk</h3>
              </div>
            </div>
            <div class="p-6">
              <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
            </div>
          </div>
        </div>

        <!-- Related Products -->
        <div v-if="relatedProducts.length > 0" class="mt-16">
          <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-200">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Produk Serupa</h3>
                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">
                  {{ relatedProducts.length }} produk
                </span>
              </div>
            </div>
            
            <div class="p-6">
              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                  v-for="relatedProduct in relatedProducts" 
                  :key="relatedProduct.id"
                  :href="`/products/${relatedProduct.slug}`"
                  class="group relative bg-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-300 hover:scale-[1.02] cursor-pointer"
                >
                  <!-- Product Image -->
                  <div class="aspect-square overflow-hidden bg-white relative">
                    <img 
                      v-if="relatedProduct.image_path" 
                      :src="`/storage/${relatedProduct.image_path}`" 
                      :alt="relatedProduct.name" 
                      class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                    />
                    <div v-else class="flex h-full items-center justify-center">
                      <Icon name="package" class="h-16 w-16 text-gray-400" />
                    </div>
                    
                    <!-- Stock Badge -->
                    <div class="absolute top-2 left-2">
                      <span class="px-2 py-1 text-xs font-medium rounded-lg border backdrop-blur-sm" :class="relatedProduct.stock_status.badge_class">
                        {{ relatedProduct.stock_status.label }}
                      </span>
                    </div>
                  </div>

                  <!-- Product Info -->
                  <div class="p-4 bg-white">
                    <h4 class="font-semibold text-gray-900 mb-2 line-clamp-1 text-sm">{{ relatedProduct.name }}</h4>
                    <div class="flex items-center justify-between">
                      <div class="text-base font-bold text-gray-900">{{ relatedProduct.formatted_price }}</div>
                      <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ relatedProduct.category?.name }}</div>
                    </div>
                  </div>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Buy Now Confirmation Modal -->
    <div v-if="showBuyNowModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm transition-all duration-300" @click="showBuyNowModal = false"></div>
        
        <div class="relative w-full max-w-md bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-white/20">
          <!-- Header -->
          <div class="p-6 border-b border-gray-200/50">
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Konfirmasi Pembelian</h3>
            <p class="text-gray-600">Pastikan detail pembelian Anda sudah benar</p>
          </div>

          <!-- Content -->
          <div class="p-6 space-y-4">
            <!-- Product Info -->
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
              <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                <img 
                  v-if="product.image_path" 
                  :src="`/storage/${product.image_path}`" 
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                  <Icon name="package" class="w-8 h-8 text-gray-400" />
                </div>
              </div>
              <div class="flex-1">
                <h4 class="font-semibold text-gray-900 line-clamp-2">{{ product.name }}</h4>
                <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ product.formatted_price }}</p>
              </div>
            </div>

            <!-- Order Details -->
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-gray-600">Jumlah</span>
                <span class="font-semibold">{{ quantity }} item</span>
              </div>
              <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                <span class="text-lg font-semibold text-gray-900">Total</span>
                <span class="text-xl font-bold text-gray-900">{{ totalPrice }}</span>
              </div>
            </div>

            <!-- Warning -->
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
              <p class="text-sm text-yellow-800">
                <span class="font-medium">Perhatian:</span> Pesanan yang dibuat akan langsung masuk ke daftar pesanan Anda dan menunggu pembayaran.
              </p>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200/50 bg-gray-50/80 backdrop-blur-sm rounded-b-2xl">
            <button 
              @click="showBuyNowModal = false"
              :disabled="isBuyingNow"
              class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors shadow-sm disabled:opacity-50"
            >
              Batal
            </button>
            <button 
              @click="confirmBuyNow"
              :disabled="isBuyingNow"
              class="px-6 py-2.5 text-sm font-medium text-white rounded-xl hover:bg-gray-800 transition-all duration-200 shadow-lg disabled:opacity-50"
              style="background-color: #000000 !important;"
            >
              <span v-if="isBuyingNow">Memproses...</span>
              <span v-else>Ya, Beli Sekarang</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import Icon from '@/components/ui/Icon.vue'

interface Product {
  id: number
  name: string
  description: string
  price: number
  formatted_price: string
  stock: number
  image_path?: string
  specs?: Record<string, string>
  category?: {
    id: number
    name: string
  }
  stock_status: {
    label: string
    color: string
    badge_class: string
  }
  slug: string
}

interface RelatedProduct {
  id: number
  name: string
  formatted_price: string
  image_path?: string
  category?: {
    id: number
    name: string
  }
  stock_status: {
    label: string
    color: string
    badge_class: string
  }
  slug: string
}

const props = defineProps<{
  product: Product
  relatedProducts: RelatedProduct[]
}>()

// State
const quantity = ref(1)

// Computed
const totalPrice = computed(() => {
  const total = props.product.price * quantity.value
  return 'Rp ' + total.toLocaleString('id-ID')
})

// Methods
const increaseQuantity = () => {
  if (quantity.value < props.product.stock) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const isAddingToCart = ref(false)

const addToCart = async () => {
  if (props.product.stock === 0 || isAddingToCart.value) return
  
  isAddingToCart.value = true
  
  try {
    const response = await fetch('/api/cart', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        product_id: props.product.id,
        quantity: quantity.value
      })
    })

    if (response.ok) {
      const data = await response.json()
      
      // Update navbar cart count
      window.dispatchEvent(new CustomEvent('cart-updated', { 
        detail: { count: data.cart_count } 
      }))
      
      // Show success message
      alert(data.message || 'Produk berhasil ditambahkan ke keranjang!')
      
      // Reset quantity
      quantity.value = 1
    } else if (response.status === 419) {
      alert('Session expired. Silakan refresh halaman dan login kembali.')
      window.location.reload()
    } else if (response.status === 401) {
      alert('Anda harus login untuk menambahkan produk ke keranjang.')
      window.location.href = '/login'
    } else {
      const data = await response.json().catch(() => ({ message: 'Unknown error' }))
      alert(data.message || 'Gagal menambahkan produk ke keranjang')
    }
  } catch (error) {
    console.error('Error adding to cart:', error)
    alert('Terjadi kesalahan. Silakan coba lagi.')
  } finally {
    isAddingToCart.value = false
  }
}

const showBuyNowModal = ref(false)
const isBuyingNow = ref(false)

const buyNow = () => {
  if (props.product.stock === 0) return
  showBuyNowModal.value = true
}

const confirmBuyNow = async () => {
  if (isBuyingNow.value) return
  
  isBuyingNow.value = true
  
  try {
    // Use Inertia router post method which handles CSRF automatically
    router.post('/api/orders/buy-now', {
      product_id: props.product.id,
      quantity: quantity.value
    }, {
      onSuccess: (page) => {
        // Close modal and redirect to orders page
        showBuyNowModal.value = false
        alert('Pesanan berhasil dibuat!')
        router.visit('/my-orders')
      },
      onError: (errors) => {
        console.error('Order creation failed:', errors)
        alert(errors.message || 'Gagal membuat pesanan')
      },
      onFinish: () => {
        isBuyingNow.value = false
      }
    })
  } catch (error) {
    console.error('Error creating order:', error)
    alert('Terjadi kesalahan. Silakan coba lagi.')
    isBuyingNow.value = false
    showBuyNowModal.value = false
  }
}
</script>
