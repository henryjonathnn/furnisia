<template>
  <CustomerLayout>
    <Head title="Keranjang Belanja - Furnisia" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Belanja</h1>
          <p class="text-gray-600">{{ summary.items_count }} produk dalam keranjang</p>
        </div>

        <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Cart Items -->
          <div class="lg:col-span-2 space-y-4">
            
            <!-- Select All -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
              <label class="flex items-center">
                <input 
                  type="checkbox" 
                  :checked="isAllSelected"
                  @change="toggleSelectAll"
                  class="h-4 w-4 text-black border-gray-300 rounded focus:ring-black focus:ring-2"
                />
                <span class="ml-3 text-sm font-medium text-gray-900">
                  Pilih Semua ({{ summary.items_count }} produk)
                </span>
              </label>
            </div>

            <!-- Cart Items List -->
            <div class="space-y-4">
              <div 
                v-for="item in cartItems" 
                :key="item.id"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
              >
                <div class="p-6">
                  <div class="flex items-start gap-4">
                    
                    <!-- Checkbox -->
                    <div class="flex-shrink-0 pt-1">
                      <input 
                        type="checkbox" 
                        :checked="item.is_selected"
                        @change="toggleItemSelection(item.id)"
                        class="h-4 w-4 text-black border-gray-300 rounded focus:ring-black focus:ring-2"
                      />
                    </div>

                    <!-- Product Image -->
                    <div class="flex-shrink-0">
                      <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden">
                        <img 
                          v-if="item.image" 
                          :src="`/storage/${item.image}`" 
                          :alt="item.name"
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                          <Icon name="package" class="w-8 h-8 text-gray-400" />
                        </div>
                      </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex-1 min-w-0">
                      <div class="flex items-start justify-between">
                        <div class="flex-1">
                          <Link 
                            :href="`/products/${item.slug}`"
                            class="text-lg font-semibold text-gray-900 hover:text-black transition-colors line-clamp-2"
                          >
                            {{ item.name }}
                          </Link>
                          <p class="text-sm text-gray-500 mt-1">{{ item.category }}</p>
                          
                          <!-- Stock Status -->
                          <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full" :class="item.stock_status.badge_class">
                              {{ item.stock_status.label }}
                            </span>
                          </div>
                        </div>

                        <!-- Remove Button -->
                        <button 
                          @click="removeItem(item.id)"
                          class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>

                      <!-- Price and Quantity -->
                      <div class="mt-4 flex items-center justify-between">
                        <div class="text-xl font-bold text-gray-900">
                          {{ item.formatted_price }}
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-3">
                          <button 
                            @click="updateQuantity(item.id, item.quantity - 1)"
                            :disabled="item.quantity <= 1"
                            class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                          </button>
                          
                          <div class="w-12 text-center">
                            <input 
                              type="number" 
                              :value="item.quantity"
                              @input="updateQuantity(item.id, $event.target.value)"
                              :min="1"
                              :max="item.max_quantity"
                              class="w-full text-center text-sm font-medium bg-transparent border-none focus:outline-none"
                            />
                          </div>
                          
                          <button 
                            @click="updateQuantity(item.id, item.quantity + 1)"
                            :disabled="item.quantity >= item.max_quantity"
                            class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                          </button>
                        </div>
                      </div>

                      <!-- Subtotal -->
                      <div class="mt-3 pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                          <span class="text-sm text-gray-600">Subtotal ({{ item.quantity }} item)</span>
                          <span class="text-lg font-bold text-gray-900">{{ item.formatted_subtotal }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Bulk Actions -->
            <div v-if="summary.selected_items_count > 0" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ summary.selected_items_count }} produk dipilih</span>
                <button 
                  @click="removeSelected"
                  class="text-sm text-red-600 hover:text-red-800 font-medium"
                >
                  Hapus Produk Terpilih
                </button>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 sticky top-6">
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Belanja</h3>
                
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Total ({{ summary.total_items }} item)</span>
                    <span class="font-semibold">{{ summary.formatted_total_price }}</span>
                  </div>
                  
                  <div v-if="summary.total_savings > 0" class="flex items-center justify-between text-green-600">
                    <span>Total Hemat</span>
                    <span class="font-semibold">-{{ summary.formatted_total_savings }}</span>
                  </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200">
                  <div class="flex items-center justify-between text-lg font-bold">
                    <span>Total Pembayaran</span>
                    <span class="text-xl">{{ summary.formatted_total_price }}</span>
                  </div>
                </div>

                <button 
                  :disabled="summary.selected_items_count === 0 || isCheckingOut"
                  @click="proceedToCheckout"
                  class="w-full mt-6 py-3 px-4 rounded-xl text-white font-semibold transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
                  style="background-color: #000000 !important;"
                >
                  <span v-if="isCheckingOut">Memproses...</span>
                  <span v-else>Beli ({{ summary.selected_items_count }})</span>
                </button>

                <div class="mt-4 text-center">
                  <Link 
                    href="/products" 
                    class="text-sm text-gray-600 hover:text-black transition-colors"
                  >
                    ← Lanjut Belanja
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty Cart State -->
        <div v-else class="text-center py-16">
          <div class="w-32 h-32 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m-2.4 8l.4 2M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6.18" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Keranjang Belanja Kosong</h3>
          <p class="text-gray-600 mb-6">Belum ada produk di keranjang Anda. Yuk, mulai berbelanja!</p>
          <Link 
            href="/products" 
            class="inline-flex items-center px-6 py-3 rounded-xl text-white font-semibold transition-colors shadow-lg"
            style="background-color: #000000 !important;"
          >
            Mulai Belanja
          </Link>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import Icon from '@/components/ui/Icon.vue'

interface CartItem {
  id: string
  product_id: string
  name: string
  image: string | null
  price: number
  formatted_price: string
  quantity: number
  stock: number
  subtotal: number
  formatted_subtotal: string
  category: string
  stock_status: {
    label: string
    color: string
    badge_class: string
  }
  is_selected: boolean
  slug: string
  max_quantity: number
}

interface Summary {
  total_items: number
  total_price: number
  formatted_total_price: string
  total_savings: number
  formatted_total_savings: string
  items_count: number
  selected_items_count: number
}

interface Props {
  cartItems: CartItem[]
  summary: Summary
}

const props = defineProps<Props>()

const isUpdating = ref(false)

const isAllSelected = computed(() => {
  return props.cartItems.length > 0 && props.cartItems.every(item => item.is_selected)
})

const toggleSelectAll = async () => {
  if (isUpdating.value) return
  
  isUpdating.value = true
  
  try {
    const response = await fetch('/api/cart/toggle-select-all', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        select_all: !isAllSelected.value
      })
    })

    if (response.ok) {
      router.reload({ only: ['cartItems', 'summary'] })
    }
  } catch (error) {
    console.error('Error toggling select all:', error)
  } finally {
    isUpdating.value = false
  }
}

const toggleItemSelection = async (itemId: string) => {
  if (isUpdating.value) return
  
  isUpdating.value = true
  
  try {
    const response = await fetch(`/api/cart/${itemId}/toggle-selection`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })

    if (response.ok) {
      router.reload({ only: ['cartItems', 'summary'] })
    }
  } catch (error) {
    console.error('Error toggling item selection:', error)
  } finally {
    isUpdating.value = false
  }
}

const updateQuantity = async (itemId: string, quantity: number | string) => {
  const qty = parseInt(quantity.toString())
  
  if (isNaN(qty) || qty < 1 || qty > 99) return
  if (isUpdating.value) return
  
  isUpdating.value = true
  
  try {
    const response = await fetch(`/api/cart/${itemId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({ quantity: qty })
    })

    const data = await response.json()
    
    if (response.ok) {
      router.reload({ only: ['cartItems', 'summary'] })
    } else {
      alert(data.message || 'Gagal memperbarui keranjang')
    }
  } catch (error) {
    console.error('Error updating quantity:', error)
  } finally {
    isUpdating.value = false
  }
}

const removeItem = async (itemId: string) => {
  if (!confirm('Yakin ingin menghapus produk ini dari keranjang?')) return
  if (isUpdating.value) return
  
  isUpdating.value = true
  
  try {
    const response = await fetch(`/api/cart/${itemId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })

    const data = await response.json()
    
    if (response.ok) {
      router.reload({ only: ['cartItems', 'summary'] })
      
      // Update navbar cart count
      window.dispatchEvent(new CustomEvent('cart-updated', { 
        detail: { count: data.cart_count } 
      }))
    }
  } catch (error) {
    console.error('Error removing item:', error)
  } finally {
    isUpdating.value = false
  }
}

const removeSelected = async () => {
  if (!confirm('Yakin ingin menghapus semua produk yang dipilih?')) return
  if (isUpdating.value) return
  
  isUpdating.value = true
  
  try {
    const response = await fetch('/api/cart/remove-selected', {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })

    const data = await response.json()
    
    if (response.ok) {
      router.reload({ only: ['cartItems', 'summary'] })
      
      // Update navbar cart count
      window.dispatchEvent(new CustomEvent('cart-updated', { 
        detail: { count: data.cart_count } 
      }))
    }
  } catch (error) {
    console.error('Error removing selected items:', error)
  } finally {
    isUpdating.value = false
  }
}

const isCheckingOut = ref(false)

const proceedToCheckout = () => {
  if (isCheckingOut.value || props.summary.selected_items_count === 0) return
  
  isCheckingOut.value = true
  
  try {
    // Use Inertia router post method which handles CSRF automatically
    router.post('/api/cart/checkout', {}, {
      onSuccess: () => {
        // Redirect to orders page after successful checkout
        alert('Pesanan berhasil dibuat dari keranjang!')
        router.visit('/my-orders')
      },
      onError: (errors) => {
        console.error('Checkout failed:', errors)
        alert(errors.message || 'Gagal membuat pesanan dari keranjang')
      },
      onFinish: () => {
        isCheckingOut.value = false
      }
    })
  } catch (error) {
    console.error('Error during checkout:', error)
    alert('Terjadi kesalahan. Silakan coba lagi.')
    isCheckingOut.value = false
  }
}
</script>
