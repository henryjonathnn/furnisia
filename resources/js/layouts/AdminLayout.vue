<template>
  <div class="bg-background text-foreground font-sans min-h-screen">
    <!-- Sidebar -->
    <aside
      id="admin-sidebar"
      class="fixed left-0 top-0 z-40 h-screen w-56 bg-background border-r border-border transition-transform duration-300 ease-in-out"
      :class="{ '-translate-x-full': !sidebarOpen }"
    >
      <div class="flex h-full flex-col">
        <!-- Logo -->
        <div class="flex h-14 items-center justify-center border-b border-border px-4">
          <div class="flex items-center space-x-2">
            <div class="h-7 w-7 rounded-md bg-primary flex items-center justify-center">
              <Icon name="store" class="text-primary-foreground text-sm" />
            </div>
            <span class="text-lg font-semibold">Furnisia</span>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1 px-3 py-4">
          <Link
            href="/admin/dashboard"
            :class="[
              'group flex items-center rounded-md px-2.5 py-2 text-sm font-medium transition-all duration-200',
              isActive('/admin/dashboard') 
                ? 'bg-primary text-primary-foreground shadow-md' 
                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
            ]"
          >
            <Icon name="chart-pie" class="mr-2.5 text-sm" />
            Dashboard
          </Link>

          
          <Link
            href="/admin/users"
            :class="[
              'group flex items-center rounded-md px-2.5 py-2.5 text-sm font-medium transition-colors',
              isActive('/admin/users')
                ? 'bg-primary text-primary-foreground shadow-md'
                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
            ]"
          >
            <Icon name="users" class="mr-2.5 text-sm" />
            User Management
          </Link>

          <Link
            href="/admin/orders"
            :class="[
              'group flex items-center rounded-md px-2.5 py-2.5 text-sm font-medium transition-colors',
              isActive('/admin/orders')
                ? 'bg-primary text-primary-foreground shadow-md'
                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
            ]"
          >
            <Icon name="shopping-bag" class="mr-2.5 text-sm" />
            Pesanan
            <span v-if="pendingOrdersCount > 0" 
                  class="ml-auto bg-red-500 text-white text-xs font-medium px-1.5 py-0.5 rounded-full">
              {{ pendingOrdersCount }}
            </span>
          </Link>

          <Link
            href="/admin/products"
            :class="[
              'group flex items-center rounded-md px-2.5 py-2.5 text-sm font-medium transition-colors',
              isActive('/admin/products')
                ? 'bg-primary text-primary-foreground shadow-md'
                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
            ]"
          >
            <Icon name="box" class="mr-2.5 text-sm" />
            Produk
          </Link>

          <Link
            href="/admin/categories"
            :class="[
              'group flex items-center rounded-md px-2.5 py-2.5 text-sm font-medium transition-colors',
              isActive('/admin/categories')
                ? 'bg-primary text-primary-foreground shadow-md'
                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
            ]"
          >
            <Icon name="warehouse" class="mr-2.5 text-sm" />
            Kategori
            <span v-if="lowStockCount > 0" 
                  class="ml-auto bg-orange-100 text-orange-600 text-xs font-medium px-1.5 py-0.5 rounded-full">
              {{ lowStockCount }}
            </span>
          </Link>

          <div class="border-t border-border my-3"></div>

          <Link
            href="/admin/settings"
            :class="[
              'group flex items-center rounded-md px-2.5 py-2.5 text-sm font-medium transition-colors',
              isActive('/admin/settings')
                ? 'bg-primary text-primary-foreground shadow-md'
                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
            ]"
          >
            <Icon name="cog" class="mr-2.5 text-sm" />
            Pengaturan
          </Link>
        </nav>

        <!-- User Profile -->
        <div class="border-t border-border p-3">
          <DropdownMenu>
            <DropdownMenuTrigger>
              <div class="flex items-center space-x-2.5 p-2 rounded-md hover:bg-accent transition-colors cursor-pointer w-full">
                <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center">
                  <span class="text-sm font-medium text-primary-foreground">
                    {{ userInitials }}
                  </span>
                </div>
                <div class="flex-1 min-w-0 text-left">
                  <p class="text-sm font-medium truncate">{{ $page.props.auth.user.name }}</p>
                  <p class="text-xs text-muted-foreground truncate">{{ $page.props.auth.user.email }}</p>
                </div>
                <Icon name="chevron-right" class="text-muted-foreground text-xs" />
              </div>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-56">
              <DropdownMenuItem>
                <Link href="/settings/profile" class="flex items-center w-full">
                  <Icon name="user" class="mr-2 h-4 w-4" />
                  Profile
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem>
                <Link href="/settings" class="flex items-center w-full">
                  <Icon name="settings" class="mr-2 h-4 w-4" />
                  Settings
                </Link>
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem>
                <Link href="/logout" method="post" as="button" class="flex items-center w-full">
                  <Icon name="log-out" class="mr-2 h-4 w-4" />
                  Logout
                </Link>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div :class="{ 'ml-56': sidebarOpen, 'ml-0': !sidebarOpen }" class="transition-all duration-300">
      <!-- Header -->
      <header class="bg-background border-b border-border px-5 py-3 sticky top-0 z-30">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <!-- Mobile Menu Toggle -->
            <button
              @click="toggleSidebar"
              class="lg:hidden p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-md transition-colors"
            >
              <Icon name="menu" class="h-5 w-5" />
            </button>
            
            <div>
              <h1 class="text-xl font-semibold">{{ pageTitle }}</h1>
              <p class="text-sm text-muted-foreground mt-0.5">
                Selamat datang kembali, {{ $page.props.auth.user.name }}!
              </p>
            </div>
          </div>
          
          <div class="flex items-center space-x-3">
            <!-- Search -->
            <div class="relative hidden md:block">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Icon name="search" class="text-muted-foreground text-sm" />
              </div>
              <input
                type="text"
                placeholder="Cari produk, pesanan..."
                class="block w-72 pl-9 pr-3 py-2 text-sm border border-border rounded-md bg-input placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                v-model="searchQuery"
                @keyup.enter="handleSearch"
              />
            </div>

            <!-- Wallet Balance -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg px-4 py-2 flex items-center space-x-2">
              <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                <Icon name="wallet" class="text-white text-sm" />
              </div>
              <div>
                <p class="text-xs text-green-600 font-medium">Wallet Balance</p>
                <p class="text-sm font-bold text-green-700">{{ formatCurrency(walletBalance) }}</p>
              </div>
            </div>

            <!-- Notifications -->
            <DropdownMenu>
              <DropdownMenuTrigger>
                <div class="relative p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-md transition-colors cursor-pointer">
                  <Icon name="bell" class="h-5 w-5" />
                  <span v-if="notificationCount > 0" 
                        class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs font-medium rounded-full flex items-center justify-center">
                    {{ notificationCount > 9 ? '9+' : notificationCount }}
                  </span>
                </div>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" class="w-80">
                <div class="p-4 border-b border-border">
                  <h3 class="font-semibold">Notifikasi</h3>
                  <p class="text-sm text-muted-foreground">{{ notificationCount }} notifikasi baru</p>
                </div>
                <div class="max-h-64 overflow-y-auto">
                  <div v-for="notification in notifications" 
                       :key="notification.id" 
                       class="p-4 border-b border-border last:border-b-0 hover:bg-accent transition-colors cursor-pointer">
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-sm font-medium">{{ notification.title }}</p>
                        <p class="text-xs text-muted-foreground mt-1">{{ notification.message }}</p>
                      </div>
                      <span class="text-xs text-muted-foreground">{{ notification.time }}</span>
                    </div>
                  </div>
                </div>
              </DropdownMenuContent>
            </DropdownMenu>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>

    <!-- Mobile Overlay -->
    <div
      v-if="!sidebarOpen"
      @click="toggleSidebar"
      class="fixed inset-0 bg-background/80 z-30 lg:hidden"
      v-show="showMobileOverlay"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { 
  DropdownMenu, 
  DropdownMenuContent, 
  DropdownMenuItem, 
  DropdownMenuSeparator, 
  DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu'
import Icon from '@/components/ui/Icon.vue'

// Reactive state
const sidebarOpen = ref(true)
const showMobileOverlay = ref(false)
const searchQuery = ref('')
const walletBalance = ref(0)

// Mock data - akan diganti dengan real data
const pendingOrdersCount = ref(23)
const lowStockCount = ref(12)
const notificationCount = ref(3)
const notifications = ref([
  {
    id: 1,
    title: 'Pesanan Baru',
    message: 'Order #12580 dari Sarah M.',
    time: '2 menit lalu'
  },
  {
    id: 2,
    title: 'Stok Menipis',
    message: 'iPhone 15 Pro tersisa 2 unit',
    time: '15 menit lalu'
  },
  {
    id: 3,
    title: 'Pembayaran Diterima',
    message: 'Order #12579 - Rp 22.5M',
    time: '1 jam lalu'
  }
])

// Computed properties
const userInitials = computed(() => {
  const user = usePage().props.auth.user
  return user.name.split(' ').map((n: string) => n[0]).join('').toUpperCase()
})

const pageTitle = computed(() => {
  const currentRoute = usePage().url
  const titleMap: Record<string, string> = {
    '/admin/dashboard': 'Dashboard',
    '/admin/orders': 'Pesanan',
    '/admin/products': 'Produk',
    '/admin/categories': 'Kategori',
    '/admin/users': 'Pengguna',
    '/admin/analytics': 'Analytics',
    '/admin/settings': 'Pengaturan'
  }
  
  return titleMap[currentRoute] || 'Admin Panel'
})

// Methods
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount || 0)
}

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
  if (window.innerWidth < 1024) {
    showMobileOverlay.value = !sidebarOpen.value
  }
}

const isActive = (path: string) => {
  return usePage().url.startsWith(path)
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Implement search functionality
    console.log('Searching for:', searchQuery.value)
  }
}

const loadWalletBalance = async () => {
  try {
    const response = await fetch('/admin/wallet/balance', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      walletBalance.value = data.balance || 0
    }
  } catch (error) {
    console.error('Error loading wallet balance:', error)
    walletBalance.value = 0
  }
}

const handleResize = () => {
  if (window.innerWidth >= 1024) {
    sidebarOpen.value = true
    showMobileOverlay.value = false
  } else {
    sidebarOpen.value = false
  }
}

const fetchWalletBalance = async () => {
  try {
    const response = await fetch('/admin/wallet/balance', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (response.ok) {
      const data = await response.json()
      walletBalance.value = data.balance
    }
  } catch (error) {
    console.error('Error fetching wallet balance:', error)
  }
}

// Lifecycle
onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
  loadWalletBalance() // Load wallet balance on mount
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>
