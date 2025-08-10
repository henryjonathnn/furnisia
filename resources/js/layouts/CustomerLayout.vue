<template>
  <div class="bg-background text-foreground font-sans min-h-screen">
    <!-- Header Navigation -->
    <header class="bg-background border-b border-border sticky top-0 z-50 shadow-sm">
      <!-- Top Bar -->
      <div class="bg-black text-white text-sm py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
              <div class="flex items-center space-x-2">
                <Icon name="truck" class="h-4 w-4" />
                <span>Gratis ongkir untuk pembelian di atas Rp 5.000.000</span>
              </div>
              <div class="hidden md:flex items-center space-x-2">
                <Icon name="phone" class="h-4 w-4" />
                <span>+62 21 1234 5678</span>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <a href="#" class="hover:text-gray-300 transition-colors">
                <Icon name="instagram" class="h-4 w-4" />
              </a>
              <a href="#" class="hover:text-gray-300 transition-colors">
                <Icon name="facebook" class="h-4 w-4" />
              </a>
              <a href="#" class="hover:text-gray-300 transition-colors">
                <Icon name="twitter" class="h-4 w-4" />
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Header -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">
          <!-- Logo -->
          <div class="flex items-center">
            <Link href="/" class="flex items-center space-x-3">
              <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center shadow-lg">
                <Icon name="store" class="text-primary-foreground text-lg" />
              </div>
              <div class="hidden sm:block">
                <span class="text-2xl font-bold bg-gradient-to-r from-primary to-primary/70 bg-clip-text text-transparent">Furnisia</span>
                <div class="text-xs text-gray-600 -mt-1">Premium Furniture</div>
              </div>
            </Link>
          </div>

          <!-- Search Bar - Now Prominent -->
          <div class="flex-1 max-w-2xl mx-8 hidden lg:block">
            <div class="relative">
              <input
                type="text"
                placeholder="Cari furniture, dekorasi rumah, dan lainnya..."
                class="block w-full pl-4 pr-12 py-3 text-sm border border-gray-300 rounded-xl bg-gray-50 placeholder:text-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-400 transition-all hover:shadow-md"
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                @input="handleSearchInput"
                @focus="showSuggestions = true"
                @blur="hideSuggestions"
              />
              <button 
                @click="handleSearch"
                class="absolute inset-y-0 right-0 pr-3 flex items-center"
              >
                <Icon name="search" class="text-gray-400 h-5 w-5 hover:text-gray-600 transition-colors" />
              </button>

              <!-- Search Suggestions -->
              <div 
                v-if="showSuggestions && searchSuggestions.length > 0" 
                class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-80 overflow-y-auto"
              >
                <div class="p-2">
                  <button
                    v-for="suggestion in searchSuggestions"
                    :key="suggestion.id"
                    @mousedown.prevent="selectSuggestion(suggestion)"
                    class="w-full flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition-colors text-left"
                  >
                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                      <img 
                        v-if="suggestion.image" 
                        :src="`/storage/${suggestion.image}`" 
                        :alt="suggestion.name"
                        class="w-full h-full object-cover"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center">
                        <Icon name="package" class="w-6 h-6 text-gray-400" />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="font-medium text-gray-900 truncate">{{ suggestion.name }}</h4>
                      <p class="text-sm text-gray-500">{{ suggestion.category }} • {{ suggestion.price }}</p>
                    </div>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Side Actions -->
          <div class="flex items-center space-x-2 lg:space-x-4">
            <!-- Cart -->
            <Link href="/cart" class="relative p-3 text-black hover:text-black hover:bg-gray-100 rounded-xl transition-colors">
              <Icon name="shopping-cart" class="h-6 w-6" />
              <span v-if="cartCount > 0" 
                    class="absolute -top-1 -right-1 h-6 w-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-lg animate-pulse">
                {{ cartCount }}
              </span>
            </Link>

            <!-- Auth Button -->
            <div v-if="!$page.props.auth.user">
              <Link href="/login" class="inline-flex items-center justify-center px-6 py-2 text-sm !bg-black !text-white rounded-lg hover:!bg-gray-800 transition-colors font-medium shadow-md border-2 border-black">
                Masuk
              </Link>
            </div>

            <!-- User Menu -->
            <DropdownMenu v-else>
              <DropdownMenuTrigger>
                <div class="flex items-center space-x-2 p-2 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer">
                  <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary to-primary/80 flex items-center justify-center shadow-md">
                    <span class="text-sm font-bold text-primary-foreground">
                      {{ userInitials }}
                    </span>
                  </div>
                  <div class="hidden lg:block text-left">
                    <div class="text-sm font-medium text-black">{{ $page.props.auth.user.name }}</div>
                    <div class="text-xs text-gray-600">Akun Saya</div>
                  </div>
                  <Icon name="chevron-down" class="text-gray-600 text-xs hidden lg:block" />
                </div>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" class="w-56">
                <DropdownMenuItem>
                  <Link href="/my-orders" class="flex items-center w-full">
                    <Icon name="package" class="mr-3 h-4 w-4" />
                    My Orders
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Link href="/settings/profile" class="flex items-center w-full">
                    <Icon name="user" class="mr-3 h-4 w-4" />
                    Profile Settings
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem>
                  <Link href="/logout" method="post" as="button" class="flex items-center w-full">
                    <Icon name="log-out" class="mr-3 h-4 w-4" />
                    Logout
                  </Link>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>

            <!-- Mobile Menu Toggle -->
            <button
              @click="toggleMobileMenu"
              class="lg:hidden p-2 text-muted-foreground hover:text-foreground hover:bg-muted rounded-lg transition-colors"
            >
              <Icon name="menu" class="h-6 w-6" />
            </button>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div v-show="showMobileMenu" class="md:hidden border-t border-border py-4">
          <nav class="flex flex-col space-y-3">
            <Link href="/" class="text-muted-foreground hover:text-foreground px-3 py-2">Beranda</Link>
            <Link href="/products" class="text-muted-foreground hover:text-foreground px-3 py-2">Produk</Link>
            <Link href="/categories" class="text-muted-foreground hover:text-foreground px-3 py-2">Kategori</Link>
            <Link href="/about" class="text-muted-foreground hover:text-foreground px-3 py-2">Tentang</Link>
            <Link href="/contact" class="text-muted-foreground hover:text-foreground px-3 py-2">Kontak</Link>
            
            <!-- Mobile Search -->
            <div class="px-3 py-2">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Icon name="search" class="text-muted-foreground text-sm" />
                </div>
                <input
                  type="text"
                  placeholder="Cari produk..."
                  class="block w-full pl-9 pr-3 py-2 text-sm border border-border rounded-md bg-input placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                  v-model="searchQuery"
                  @keyup.enter="handleSearch"
                />
              </div>
            </div>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-muted border-t border-border mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Company Info -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center space-x-2 mb-4">
              <div class="h-8 w-8 rounded-md bg-primary flex items-center justify-center">
                <Icon name="store" class="text-primary-foreground text-sm" />
              </div>
              <span class="text-xl font-semibold">Furnisia</span>
            </div>
            <p class="text-muted-foreground mb-4 max-w-md">
              Toko online terpercaya untuk kebutuhan rumah tangga Anda. 
              Kualitas terbaik dengan harga terjangkau.
            </p>
            <div class="flex space-x-4">
              <a href="#" class="text-muted-foreground hover:text-foreground">
                <Icon name="facebook" class="h-5 w-5" />
              </a>
              <a href="#" class="text-muted-foreground hover:text-foreground">
                <Icon name="twitter" class="h-5 w-5" />
              </a>
              <a href="#" class="text-muted-foreground hover:text-foreground">
                <Icon name="instagram" class="h-5 w-5" />
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="font-semibold mb-4">Menu Cepat</h3>
            <nav class="space-y-2">
              <Link href="/products" class="block text-muted-foreground hover:text-foreground">Semua Produk</Link>
              <Link href="/categories" class="block text-muted-foreground hover:text-foreground">Kategori</Link>
              <Link href="/about" class="block text-muted-foreground hover:text-foreground">Tentang Kami</Link>
              <Link href="/contact" class="block text-muted-foreground hover:text-foreground">Kontak</Link>
            </nav>
          </div>

          <!-- Customer Service -->
          <div>
            <h3 class="font-semibold mb-4">Layanan</h3>
            <nav class="space-y-2">
              <a href="#" class="block text-muted-foreground hover:text-foreground">Bantuan</a>
              <a href="#" class="block text-muted-foreground hover:text-foreground">Pengiriman</a>
              <a href="#" class="block text-muted-foreground hover:text-foreground">Pengembalian</a>
              <a href="#" class="block text-muted-foreground hover:text-foreground">FAQ</a>
            </nav>
          </div>
        </div>

        <div class="border-t border-border mt-8 pt-8 text-center text-muted-foreground">
          <p>&copy; 2024 Furnisia. All rights reserved.</p>
        </div>
      </div>
    </footer>
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
const searchQuery = ref('')
const showMobileMenu = ref(false)
const cartCount = ref(0) // Will be replaced with real cart data
const showSuggestions = ref(false)
const searchSuggestions = ref([])
const searchTimeout = ref(null)

// Computed properties
const userInitials = computed(() => {
  const user = usePage().props.auth?.user
  if (!user) return ''
  return user.name.split(' ').map((n: string) => n[0]).join('').toUpperCase()
})

// Methods
const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

const isActive = (path: string) => {
  return usePage().url === path
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    window.location.href = `/products?search=${encodeURIComponent(searchQuery.value.trim())}`
  }
}

const handleSearchInput = async () => {
  const query = searchQuery.value.trim()
  
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
  
  if (query.length < 2) {
    searchSuggestions.value = []
    return
  }
  
  searchTimeout.value = setTimeout(async () => {
    try {
      const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`)
      if (response.ok) {
        searchSuggestions.value = await response.json()
      }
    } catch (error) {
      console.error('Search suggestions error:', error)
    }
  }, 300)
}

const selectSuggestion = (suggestion) => {
  window.location.href = `/products?search=${encodeURIComponent(suggestion.name)}`
}

const hideSuggestions = () => {
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}

// Load cart count on mount
const loadCartCount = async () => {
  try {
    const response = await fetch('/api/cart/count')
    if (response.ok) {
      const data = await response.json()
      cartCount.value = data.count
    }
  } catch (error) {
    console.error('Error loading cart count:', error)
  }
}

// Listen for cart updates
const handleCartUpdate = (event: CustomEvent) => {
  cartCount.value = event.detail.count
}

onMounted(() => {
  const user = usePage().props.auth?.user
  if (user) {
    loadCartCount()
  }
  
  window.addEventListener('cart-updated', handleCartUpdate as EventListener)
})

onUnmounted(() => {
  window.removeEventListener('cart-updated', handleCartUpdate as EventListener)
})
</script>
