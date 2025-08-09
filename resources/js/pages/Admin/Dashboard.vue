<template>
  <AdminLayout>
    <Head title="Admin Dashboard - Furnisia" />

    <!-- Dashboard Content -->
    <div class="p-5">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Sales -->
        <div class="bg-background rounded-lg border border-border p-4 hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Total Penjualan</p>
              <p class="text-2xl font-semibold mt-1">{{ stats.totalSales.formatted }}</p>
              <p class="text-sm flex items-center mt-1" :class="stats.totalSales.growth >= 0 ? 'text-green-600' : 'text-red-600'">
                <Icon :name="stats.totalSales.growth >= 0 ? 'arrow-up' : 'arrow-down'" class="mr-1 text-xs" />
                {{ stats.totalSales.growthFormatted }} dari kemarin
              </p>
            </div>
            <div class="h-10 w-10 bg-green-100 rounded-md flex items-center justify-center">
              <Icon name="dollar-sign" class="text-green-600" />
            </div>
          </div>
        </div>

        <!-- New Orders -->
        <div class="bg-background rounded-lg border border-border p-4 hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Pesanan Baru</p>
              <p class="text-2xl font-semibold mt-1">{{ stats.newOrders.value }}</p>
              <p class="text-sm text-blue-600 flex items-center mt-1">
                <Icon name="arrow-up" class="mr-1 text-xs" />
                {{ stats.newOrders.growthFormatted }}
              </p>
            </div>
            <div class="h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
              <Icon name="shopping-cart" class="text-blue-600" />
            </div>
          </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-background rounded-lg border border-border p-4 hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Stok Menipis</p>
              <p class="text-2xl font-semibold mt-1">{{ stats.lowStock.value }}</p>
              <p class="text-sm text-red-600 flex items-center mt-1">
                <Icon name="exclamation-triangle" class="mr-1 text-xs" />
                Perlu restok
              </p>
            </div>
            <div class="h-10 w-10 bg-red-100 rounded-md flex items-center justify-center">
              <Icon name="warehouse" class="text-red-600" />
            </div>
          </div>
        </div>

        <!-- Store Rating -->
        <div class="bg-background rounded-lg border border-border p-4 hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-muted-foreground">Rating Toko</p>
              <p class="text-2xl font-semibold mt-1">{{ stats.storeRating.value }}</p>
              <p class="text-sm text-yellow-600 flex items-center mt-1">
                <Icon name="star" class="mr-1 text-xs" />
                Excellent
              </p>
            </div>
            <div class="h-10 w-10 bg-yellow-100 rounded-md flex items-center justify-center">
              <Icon name="star" class="text-yellow-600" />
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-background rounded-lg border border-border p-5">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="font-semibold">Grafik Penjualan</h3>
              <p class="text-sm text-muted-foreground">7 hari terakhir</p>
            </div>
            <div class="flex items-center space-x-2">
              <button class="px-3 py-1 text-sm bg-gray-900 text-white rounded-md">
                7 Hari
              </button>
              <button class="px-3 py-1 text-sm text-muted-foreground hover:bg-accent rounded-md">
                30 Hari
              </button>
            </div>
          </div>
          <div class="h-64 bg-muted rounded-md flex items-center justify-center">
            <div class="text-center text-muted-foreground">
              <Icon name="chart-area" class="text-3xl mb-3" />
              <p class="font-medium">Chart Integration Area</p>
              <p class="text-sm">Connect with Chart.js or similar library</p>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-background rounded-lg border border-border p-5">
          <h3 class="font-semibold mb-4">Aksi Cepat</h3>
          <div class="space-y-3">
            <Link href="/admin/products/create" 
                  class="w-full flex items-center justify-between p-3 hover:bg-accent rounded-md transition-colors group">
              <div class="flex items-center space-x-3">
                <div class="h-8 w-8 bg-blue-600 rounded-md flex items-center justify-center">
                  <Icon name="plus" class="text-white text-sm" />
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium">Tambah Produk</p>
                  <p class="text-xs text-muted-foreground">Produk baru ke katalog</p>
                </div>
              </div>
              <Icon name="chevron-right" class="text-muted-foreground text-xs" />
            </Link>

            <button class="w-full flex items-center justify-between p-3 hover:bg-accent rounded-md transition-colors group">
              <div class="flex items-center space-x-3">
                <div class="h-8 w-8 bg-green-600 rounded-md flex items-center justify-center">
                  <Icon name="tags" class="text-white text-sm" />
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium">Buat Promo</p>
                  <p class="text-xs text-muted-foreground">Diskon dan penawaran</p>
                </div>
              </div>
              <Icon name="chevron-right" class="text-muted-foreground text-xs" />
            </button>

            <button class="w-full flex items-center justify-between p-3 hover:bg-accent rounded-md transition-colors group">
              <div class="flex items-center space-x-3">
                <div class="h-8 w-8 bg-purple-600 rounded-md flex items-center justify-center">
                  <Icon name="download" class="text-white text-sm" />
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium">Export Laporan</p>
                  <p class="text-xs text-muted-foreground">Download data penjualan</p>
                </div>
              </div>
              <Icon name="chevron-right" class="text-muted-foreground text-xs" />
            </button>

            <button class="w-full flex items-center justify-between p-3 hover:bg-accent rounded-md transition-colors group">
              <div class="flex items-center space-x-3">
                <div class="h-8 w-8 bg-orange-600 rounded-md flex items-center justify-center">
                  <Icon name="bullhorn" class="text-white text-sm" />
                </div>
                <div class="text-left">
                  <p class="text-sm font-medium">Kampanye Iklan</p>
                  <p class="text-xs text-muted-foreground">Promosikan produk</p>
                </div>
              </div>
              <Icon name="chevron-right" class="text-muted-foreground text-xs" />
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-background rounded-lg border border-border">
          <div class="px-5 py-3 border-b border-border">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold">Pesanan Terbaru</h3>
              <Link href="/admin/orders" class="text-sm text-muted-foreground hover:text-foreground">
                Lihat Semua
              </Link>
            </div>
          </div>
          <div class="p-5">
            <div class="space-y-3">
              <div v-for="order in recentOrders" :key="order.id" 
                   class="flex items-center justify-between p-3 bg-muted/50 rounded-md">
                <div class="flex items-center space-x-3">
                  <div class="h-9 w-9 bg-muted rounded-md flex items-center justify-center">
                    <img v-if="order.product_image" :src="order.product_image" :alt="order.product_name" class="w-full h-full object-cover rounded-md" />
                    <Icon v-else name="package" class="text-muted-foreground" />
                  </div>
                  <div>
                    <p class="text-sm font-medium">{{ order.product_name }} - {{ order.customer_name }}</p>
                    <p class="text-xs text-muted-foreground">{{ order.order_number }} • {{ order.created_at }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold">{{ order.total }}</p>
                  <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full"
                        :class="getStatusBadgeClass(order.status_color)">
                    {{ order.status_label }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Products & Alerts -->
        <div class="space-y-6">
          <!-- Top Products -->
          <div class="bg-background rounded-lg border border-border p-5">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold">Produk Terlaris</h3>
              <button class="text-sm text-muted-foreground hover:text-foreground">
                Detail
              </button>
            </div>
            <div class="space-y-3">
              <div v-for="product in topProducts" :key="product.rank" class="flex items-center space-x-3">
                <div class="text-sm font-bold text-muted-foreground">{{ product.rank }}</div>
                <div class="h-8 w-8 bg-muted rounded-md flex items-center justify-center">
                  <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover rounded-md" />
                  <Icon v-else name="package" class="text-muted-foreground" />
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium">{{ product.name }}</p>
                  <p class="text-xs text-muted-foreground">{{ product.total_sold }} terjual</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold">{{ product.price }}</p>
                  <p class="text-xs text-green-600">+{{ product.growth }}%</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Alerts -->
          <div v-if="alerts.length > 0" class="bg-background rounded-lg border border-border p-5">
            <h3 class="font-semibold mb-4">Peringatan</h3>
            <div class="space-y-3">
              <div v-for="alert in alerts" :key="alert.title" 
                   class="p-3 rounded-md"
                   :class="getAlertClass(alert.color)">
                <div class="flex items-center space-x-2.5">
                  <Icon :name="alert.icon.split(' ')[1]" :class="getAlertIconClass(alert.color)" />
                  <div>
                    <p class="text-sm font-medium" :class="getAlertTitleClass(alert.color)">{{ alert.title }}</p>
                    <p class="text-xs" :class="getAlertMessageClass(alert.color)">{{ alert.message }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Icon from '@/components/ui/Icon.vue'

interface Stats {
  totalSales: {
    value: number
    formatted: string
    growth: number
    growthFormatted: string
  }
  newOrders: {
    value: number
    growth: number
    growthFormatted: string
  }
  lowStock: {
    value: number
    status: string
  }
  storeRating: {
    value: number
    status: string
  }
}

interface Order {
  id: number
  order_number: string
  customer_name: string
  product_name: string
  total: string
  status: string
  status_label: string
  status_color: string
  created_at: string
  product_image?: string
}

interface Product {
  rank: number
  name: string
  total_sold: number
  price: string
  image?: string
  growth: number
}

interface Alert {
  type: string
  icon: string
  title: string
  message: string
  color: string
}

interface User {
  id: number
  name: string
  email: string
  role: {
    id: number
    name: string
  }
}

defineProps<{
  stats: Stats
  recentOrders: Order[]
  topProducts: Product[]
  lowStockProducts: Product[]
  alerts: Alert[]
  user: User
}>()

const getStatusBadgeClass = (color: string) => {
  const classes = {
    green: 'bg-green-100 text-green-700',
    blue: 'bg-blue-100 text-blue-700',
    yellow: 'bg-yellow-100 text-yellow-700',
    red: 'bg-red-100 text-red-700',
    gray: 'bg-gray-100 text-gray-700'
  }
  return classes[color as keyof typeof classes] || classes.gray
}

const getAlertClass = (color: string) => {
  const classes = {
    red: 'bg-red-50 border border-red-200',
    orange: 'bg-orange-50 border border-orange-200',
    yellow: 'bg-yellow-50 border border-yellow-200',
    blue: 'bg-blue-50 border border-blue-200'
  }
  return classes[color as keyof typeof classes] || classes.blue
}

const getAlertIconClass = (color: string) => {
  const classes = {
    red: 'text-red-500',
    orange: 'text-orange-500',
    yellow: 'text-yellow-500',
    blue: 'text-blue-500'
  }
  return classes[color as keyof typeof classes] || classes.blue
}

const getAlertTitleClass = (color: string) => {
  const classes = {
    red: 'text-red-800',
    orange: 'text-orange-800',
    yellow: 'text-yellow-800',
    blue: 'text-blue-800'
  }
  return classes[color as keyof typeof classes] || classes.blue
}

const getAlertMessageClass = (color: string) => {
  const classes = {
    red: 'text-red-600',
    orange: 'text-orange-600',
    yellow: 'text-yellow-600',
    blue: 'text-blue-600'
  }
  return classes[color as keyof typeof classes] || classes.blue
}
</script>
