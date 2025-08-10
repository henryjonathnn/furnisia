<template>
  <CustomerLayout>
    <Head title="Pesanan Saya - Furnisia" />

    <div class="min-h-screen bg-gray-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Pesanan Saya</h1>
              <p class="text-gray-600">Kelola dan pantau status pesanan Anda</p>
            </div>
          </div>
          
          <div class="flex items-center gap-6 text-sm text-gray-600">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
              <span>Menunggu Pembayaran</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span>Sedang Diproses</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-green-500 rounded-full"></div>
              <span>Selesai</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 bg-red-500 rounded-full"></div>
              <span>Dibatalkan</span>
            </div>
          </div>
        </div>

        <!-- Orders List -->
        <div v-if="orders.length > 0" class="space-y-6">
          <div 
            v-for="order in orders" 
            :key="order.id"
            class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300"
          >
            <!-- Order Header -->
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
              <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                  <div class="text-center">
                    <div class="text-sm text-gray-500">Order ID</div>
                    <div class="font-bold text-gray-900">{{ order.order_number }}</div>
                  </div>
                  <div class="w-px h-12 bg-gray-200"></div>
                  <div class="text-center">
                    <div class="text-sm text-gray-500">Tanggal</div>
                    <div class="font-medium text-gray-900">{{ order.created_at }}</div>
                  </div>
                  <div class="w-px h-12 bg-gray-200"></div>
                  <div class="text-center">
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="font-bold text-xl text-gray-900">{{ order.formatted_total }}</div>
                  </div>
                </div>
                
                <div class="flex items-center gap-3">
                  <!-- Status Badge -->
                  <span 
                    class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-full border"
                    :class="{
                      'bg-yellow-50 text-yellow-700 border-yellow-200': order.status_color === 'yellow',
                      'bg-blue-50 text-blue-700 border-blue-200': order.status_color === 'blue',
                      'bg-green-50 text-green-700 border-green-200': order.status_color === 'green',
                      'bg-red-50 text-red-700 border-red-200': order.status_color === 'red'
                    }"
                  >
                    <div 
                      class="w-2 h-2 rounded-full mr-2"
                      :class="{
                        'bg-yellow-500': order.status_color === 'yellow',
                        'bg-blue-500': order.status_color === 'blue',
                        'bg-green-500': order.status_color === 'green',
                        'bg-red-500': order.status_color === 'red'
                      }"
                    ></div>
                    {{ order.status_label }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Order Content -->
            <div class="p-6">
              <!-- Product Preview -->
              <div v-if="order.first_product" class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                  <img 
                    v-if="order.first_product.image" 
                    :src="`/storage/${order.first_product.image}`" 
                    :alt="order.first_product.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center">
                    <Icon name="package" class="w-8 h-8 text-gray-400" />
                  </div>
                </div>
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-900 line-clamp-2">{{ order.first_product.name }}</h3>
                  <p class="text-sm text-gray-500 mt-1">{{ order.first_product.quantity }} item</p>
                  <p v-if="order.items_count > 1" class="text-sm text-blue-600 font-medium mt-1">
                    +{{ order.items_count - 1 }} produk lainnya
                  </p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex flex-col sm:flex-row gap-3">
                <Link 
                  :href="`/my-orders/${order.id}`"
                  class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 text-center rounded-xl hover:bg-gray-200 transition-colors font-medium"
                >
                  Lihat Detail
                </Link>
                
                <Link 
                  v-if="order.can_pay"
                  :href="`/my-orders/${order.id}/payment`"
                  class="flex-1 px-4 py-2.5 text-white text-center rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl"
                  style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;"
                >
                  💳 Bayar Sekarang
                </Link>
                
                <div v-else-if="order.status === 'progress'" class="flex gap-2 flex-1">
                  <button 
                    disabled
                    class="flex-1 px-4 py-2.5 bg-blue-100 text-blue-700 text-center rounded-xl font-medium cursor-not-allowed"
                  >
                    ⏳ Sedang Diproses
                  </button>
                  <a 
                    v-if="order.payment_status === 'success'"
                    :href="`/my-orders/${order.id}/invoice`"
                    target="_blank"
                    class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors flex items-center gap-2"
                    title="Cetak Invoice"
                  >
                    📄 Invoice
                  </a>
                </div>
                
                <div v-else-if="order.status === 'completed'" class="flex gap-2 flex-1">
                  <button 
                    disabled
                    class="flex-1 px-4 py-2.5 bg-green-100 text-green-700 text-center rounded-xl font-medium cursor-not-allowed"
                  >
                    ✅ Selesai
                  </button>
                  <a 
                    v-if="order.payment_status === 'success'"
                    :href="`/my-orders/${order.id}/invoice`"
                    target="_blank"
                    class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-colors flex items-center gap-2"
                    title="Cetak Invoice"
                  >
                    📄 Invoice
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Pesanan</h3>
          <p class="text-gray-600 mb-8">Anda belum memiliki pesanan. Yuk, mulai berbelanja untuk membuat pesanan pertama!</p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link 
              href="/products" 
              class="inline-flex items-center px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 shadow-lg hover:shadow-xl"
              style="background: linear-gradient(135deg, #000000 0%, #374151 100%) !important;"
            >
              🛍️ Mulai Belanja
            </Link>
            <Link 
              href="/cart" 
              class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors"
            >
              🛒 Lihat Keranjang
            </Link>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/layouts/CustomerLayout.vue'
import Icon from '@/components/ui/Icon.vue'

interface OrderItem {
  name: string
  image: string | null
  quantity: number
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
  items_count: number
  first_product: OrderItem | null
  payment_status: string | null
  can_pay: boolean
}

interface Props {
  orders: Order[]
}

defineProps<Props>()
</script>
