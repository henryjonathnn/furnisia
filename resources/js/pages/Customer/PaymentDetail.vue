<template>
  <CustomerLayout>
    <Head :title="`Pembayaran ${order.order_number} - Furnisia`" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        
        <!-- Back Button -->
        <div class="mb-4">
          <Link 
            href="/my-orders" 
            class="inline-flex items-center text-gray-600 hover:text-black transition-colors text-sm"
          >
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Pesanan Saya
          </Link>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
          
          <!-- Invoice Header -->
          <div class="bg-gradient-to-br from-black via-gray-800 to-gray-900 text-white p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div>
                <div class="flex items-center gap-2 mb-2">
                  <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div>
                    <h1 class="text-lg font-bold">Invoice Digital</h1>
                    <p class="text-white/80 text-sm">Furnisia Premium Furniture</p>
                  </div>
                </div>
                <div class="space-y-1">
                  <p class="text-white/60 text-xs">Order ID</p>
                  <p class="text-base font-mono font-bold">{{ order.order_number }}</p>
                </div>
              </div>
              
              <div class="text-right">
                <div class="space-y-1">
                  <p class="text-white/60 text-xs">Tanggal Pesanan</p>
                  <p class="text-sm font-semibold">{{ order.created_at }}</p>
                </div>
                <div class="mt-2">
                  <span 
                    class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full border"
                    :class="{
                      'bg-yellow-400/20 text-yellow-100 border-yellow-400/50': order.status_color === 'yellow',
                      'bg-blue-400/20 text-blue-100 border-blue-400/50': order.status_color === 'blue',
                      'bg-green-400/20 text-green-100 border-green-400/50': order.status_color === 'green',
                      'bg-red-400/20 text-red-100 border-red-400/50': order.status_color === 'red'
                    }"
                  >
                    {{ order.status_label }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Invoice Body -->
          <div class="p-4">
            
            <!-- Customer Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
              <div class="bg-gray-50 rounded-xl p-3">
                <h3 class="text-sm font-semibold text-gray-900 mb-2 flex items-center">
                  <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Pembeli
                </h3>
                <div class="space-y-1">
                  <p class="text-gray-900 font-medium text-sm">{{ $page.props.auth.user.name }}</p>
                  <p class="text-gray-600 text-xs">{{ $page.props.auth.user.email }}</p>
                </div>
              </div>
              
              <div class="bg-gray-50 rounded-xl p-3">
                <h3 class="text-sm font-semibold text-gray-900 mb-2 flex items-center">
                  <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Penjual
                </h3>
                <div class="space-y-1">
                  <p class="text-gray-900 font-medium text-sm">Furnisia</p>
                  <p class="text-gray-600 text-xs">Premium Furniture Store</p>
                </div>
              </div>
            </div>

            <!-- Order Items -->
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
                Detail Produk
              </h3>
              
              <div class="bg-gray-50 rounded-xl overflow-hidden">
                <!-- Table Header -->
                <div class="bg-gray-100 px-3 py-2 border-b border-gray-200">
                  <div class="grid grid-cols-12 gap-2 text-xs font-semibold text-gray-700">
                    <div class="col-span-6">Produk</div>
                    <div class="col-span-2 text-center">Harga</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-2 text-right">Subtotal</div>
                  </div>
                </div>
                
                <!-- Table Body -->
                <div class="divide-y divide-gray-200">
                  <div 
                    v-for="item in order.items" 
                    :key="item.id"
                    class="px-3 py-3"
                  >
                    <div class="grid grid-cols-12 gap-2 items-center">
                      <div class="col-span-6 flex items-center gap-2">
                        <div class="w-10 h-10 bg-white rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
                          <img 
                            v-if="item.product.image" 
                            :src="`/storage/${item.product.image}`" 
                            :alt="item.product.name"
                            class="w-full h-full object-cover"
                          />
                          <div v-else class="w-full h-full flex items-center justify-center">
                            <Icon name="package" class="w-4 h-4 text-gray-400" />
                          </div>
                        </div>
                        <div>
                          <h4 class="font-semibold text-gray-900 text-sm line-clamp-1">{{ item.product.name }}</h4>
                          <p class="text-xs text-gray-500">{{ item.product.category }}</p>
                        </div>
                      </div>
                      <div class="col-span-2 text-center font-medium text-gray-900 text-xs">
                        {{ item.product.formatted_price }}
                      </div>
                      <div class="col-span-2 text-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-white rounded border border-gray-200 font-semibold text-gray-900 text-xs">
                          {{ item.quantity }}
                        </span>
                      </div>
                      <div class="col-span-2 text-right font-bold text-gray-900 text-xs">
                        {{ item.formatted_subtotal }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 mb-4 border border-gray-200">
              <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Ringkasan Pembayaran
              </h3>
              
              <div class="space-y-2">
                <div class="flex justify-between items-center py-1">
                  <span class="text-gray-600 text-sm">Subtotal ({{ order.items.length }} item)</span>
                  <span class="font-semibold text-gray-900 text-sm">{{ order.formatted_total }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                  <span class="text-gray-600 text-sm">Ongkos Kirim</span>
                  <span class="font-semibold text-green-600 text-sm">GRATIS</span>
                </div>
                <div class="border-t border-gray-200 pt-2">
                  <div class="flex justify-between items-center">
                    <span class="text-base font-bold text-gray-900">Total Pembayaran</span>
                    <span class="text-lg font-bold text-gray-900">{{ order.formatted_total }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payment Method Selection -->
            <div v-if="order.can_pay" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
              <h3 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                <svg class="w-4 h-4 mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Pilih Metode Pembayaran
              </h3>
              
              <!-- Payment Methods Dropdown -->
              <div class="mb-3">
                <div class="relative">
                  <button 
                    @click="showPaymentDropdown = !showPaymentDropdown"
                    class="w-full p-3 bg-white border-2 border-gray-200 rounded-lg text-left flex items-center justify-between hover:border-blue-300 transition-colors"
                    :class="{ 'border-blue-500': showPaymentDropdown }"
                  >
                    <div v-if="selectedPaymentMethod" class="flex items-center gap-2">
                      <img 
                        :src="selectedPaymentMethod.logo" 
                        :alt="selectedPaymentMethod.name"
                        class="w-6 h-6 object-contain"
                      />
                      <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ selectedPaymentMethod.name }}</p>
                        <p class="text-xs text-gray-500">{{ selectedPaymentMethod.description }}</p>
                      </div>
                    </div>
                    <div v-else class="text-gray-500 text-sm">
                      Pilih metode pembayaran
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': showPaymentDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                  
                  <div v-if="showPaymentDropdown" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-10 max-h-60 overflow-y-auto">
                    <button
                      v-for="method in paymentMethods"
                      :key="method.code"
                      @click="selectPaymentMethod(method)"
                      class="w-full p-3 text-left hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0 flex items-center gap-2"
                    >
                      <img 
                        :src="method.logo" 
                        :alt="method.name"
                        class="w-6 h-6 object-contain flex-shrink-0"
                      />
                      <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ method.name }}</p>
                        <p class="text-xs text-gray-500">{{ method.description }}</p>
                      </div>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Pay Button -->
              <button 
                @click="processPayment"
                :disabled="!selectedPaymentMethod || isProcessingPayment"
                class="w-full py-3 px-4 rounded-lg text-white font-bold text-sm transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;"
              >
                <span v-if="isProcessingPayment">🔄 Memproses...</span>
                <span v-else-if="selectedPaymentMethod">💳 Bayar dengan {{ selectedPaymentMethod.name }}</span>
                <span v-else>Pilih Metode Pembayaran</span>
              </button>
            </div>

            <!-- Payment Info (if already paid) -->
            <div v-else-if="order.payment && order.payment.status === 'success'" class="bg-green-50 rounded-xl p-4 border border-green-200">
              <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-sm font-semibold text-green-800">Pembayaran Berhasil</h3>
                  <p class="text-green-600 text-xs">Pesanan Anda sedang diproses</p>
                </div>
              </div>
              
              <div class="bg-white rounded-lg p-3">
                <div class="grid grid-cols-2 gap-3 text-xs">
                  <div>
                    <p class="text-gray-500">Metode Pembayaran</p>
                    <p class="font-semibold text-gray-900 capitalize">{{ order.payment.method }}</p>
                  </div>
                  <div>
                    <p class="text-gray-500">Waktu Pembayaran</p>
                    <p class="font-semibold text-gray-900">{{ order.payment.paid_at }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import Icon from '@/components/ui/Icon.vue'

interface PaymentMethod {
  code: string
  name: string
  logo: string
  description: string
}

interface Product {
  id: string
  name: string
  image: string | null
  category: string
  price: number
  formatted_price: string
}

interface OrderItem {
  id: string
  product: Product
  quantity: number
  subtotal: number
  formatted_subtotal: string
}

interface Payment {
  id: string
  method: string | null
  status: string
  paid_at: string | null
}

interface Order {
  id: string
  order_number: string
  status: string
  status_label: string
  status_color: string
  total: number
  formatted_total: string
  created_at: string
  items: OrderItem[]
  payment: Payment | null
  can_pay: boolean
}

interface Props {
  order: Order
  paymentMethods: PaymentMethod[]
}

const props = defineProps<Props>()

const showPaymentDropdown = ref(false)
const selectedPaymentMethod = ref<PaymentMethod | null>(null)
const isProcessingPayment = ref(false)

const selectPaymentMethod = (method: PaymentMethod) => {
  selectedPaymentMethod.value = method
  showPaymentDropdown.value = false
}

const processPayment = async () => {
  if (!selectedPaymentMethod.value || isProcessingPayment.value) return
  
  isProcessingPayment.value = true
  
  try {
    const response = await fetch(`/api/orders/${props.order.id}/payment`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        payment_method: selectedPaymentMethod.value.code
      })
    })

    const data = await response.json()
    
    if (response.ok) {
      alert('Pembayaran berhasil! Pesanan Anda sedang diproses.')
      window.location.reload()
    } else {
      alert(data.message || 'Gagal memproses pembayaran')
    }
  } catch (error) {
    console.error('Error processing payment:', error)
    alert('Terjadi kesalahan. Silakan coba lagi.')
  } finally {
    isProcessingPayment.value = false
  }
}
</script>
