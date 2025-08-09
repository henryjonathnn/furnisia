<template>
  <div class="bg-background text-foreground font-sans min-h-screen">
    <!-- Header Navigation -->
    <header class="bg-background border-b border-border sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center space-x-2">
            <Link href="/" class="flex items-center space-x-2">
              <div class="h-8 w-8 rounded-md bg-primary flex items-center justify-center">
                <Icon name="store" class="text-primary-foreground text-sm" />
              </div>
              <span class="text-xl font-semibold">Furnisia</span>
            </Link>
          </div>

          <!-- Navigation Menu -->
          <nav class="hidden md:flex items-center space-x-6">
            <Link href="/" :class="isActive('/') ? 'text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'">
              Beranda
            </Link>
            <Link href="/products" :class="isActive('/products') ? 'text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'">
              Produk
            </Link>
            <Link href="/categories" :class="isActive('/categories') ? 'text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'">
              Kategori
            </Link>
            <Link href="/about" :class="isActive('/about') ? 'text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'">
              Tentang
            </Link>
            <Link href="/contact" :class="isActive('/contact') ? 'text-foreground font-medium' : 'text-muted-foreground hover:text-foreground'">
              Kontak
            </Link>
          </nav>

          <!-- Right Side Actions -->
          <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="relative hidden md:block">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Icon name="search" class="text-muted-foreground text-sm" />
              </div>
              <input
                type="text"
                placeholder="Cari produk..."
                class="block w-64 pl-9 pr-3 py-2 text-sm border border-border rounded-md bg-input placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent"
                v-model="searchQuery"
                @keyup.enter="handleSearch"
              />
            </div>

            <!-- Cart -->
            <button class="relative p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-md transition-colors">
              <Icon name="shopping-cart" class="text-lg" />
              <span v-if="cartCount > 0" 
                    class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full flex items-center justify-center">
                <span class="text-xs font-medium text-white">{{ cartCount }}</span>
              </span>
            </button>

            <!-- Auth Buttons -->
            <div v-if="!$page.props.auth.user" class="flex items-center space-x-2">
              <Link href="/login" class="px-3 py-2 text-sm text-muted-foreground hover:text-foreground">
                Masuk
              </Link>
              <Link href="/register" class="px-4 py-2 text-sm bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors">
                Daftar
              </Link>
            </div>

            <!-- User Menu -->
            <DropdownMenu v-else>
              <DropdownMenuTrigger>
                <div class="flex items-center space-x-2 p-2 rounded-md hover:bg-accent transition-colors cursor-pointer">
                  <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center">
                    <span class="text-sm font-medium text-primary-foreground">
                      {{ userInitials }}
                    </span>
                  </div>
                  <span class="text-sm font-medium hidden md:block">{{ $page.props.auth.user.name }}</span>
                  <Icon name="chevron-down" class="text-muted-foreground text-xs" />
                </div>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" class="w-48">
                <DropdownMenuItem>
                  <Link href="/orders" class="flex items-center w-full">
                    <Icon name="package" class="mr-2 h-4 w-4" />
                    Pesanan Saya
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Link href="/settings/profile" class="flex items-center w-full">
                    <Icon name="user" class="mr-2 h-4 w-4" />
                    Profile
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

            <!-- Mobile Menu Toggle -->
            <button
              @click="toggleMobileMenu"
              class="md:hidden p-2 text-muted-foreground hover:text-foreground hover:bg-accent rounded-md transition-colors"
            >
              <Icon name="menu" class="h-5 w-5" />
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
import { computed, ref } from 'vue'
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
    // Implement search functionality
    console.log('Searching for:', searchQuery.value)
  }
}
</script>
