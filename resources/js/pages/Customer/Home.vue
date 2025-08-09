<template>
  <CustomerLayout>
    <Head title="Furnisia - Toko Online Keperluan Rumah Tangga" />

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-background to-muted/20 py-20 md:py-32">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-8">
          <!-- Main Heading -->
          <div class="space-y-6">
            <h1 class="text-5xl font-bold tracking-tight text-foreground md:text-7xl lg:text-8xl">
              Find Your Style,
              <br />
              <span class="text-primary">Build Your Home.</span>
            </h1>
            <p class="max-w-2xl mx-auto text-xl text-muted-foreground md:text-2xl">
              Transform rumah Anda dengan furniture berkualitas tinggi dan peralatan rumah tangga modern untuk kehidupan yang lebih nyaman.
            </p>
          </div>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button class="inline-flex h-14 items-center justify-center rounded-full bg-primary px-8 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90">
              <Icon name="shopping-cart" class="mr-2 h-5 w-5" />
              Shop for Free
            </button>
            <button class="inline-flex h-14 items-center justify-center rounded-full border border-border bg-background px-6 text-sm font-medium text-foreground transition-colors hover:bg-muted/50">
              <div class="flex items-center space-x-2">
                <div class="flex -space-x-1">
                  <div class="h-6 w-6 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 border border-background"></div>
                  <div class="h-6 w-6 rounded-full bg-gradient-to-r from-green-500 to-blue-500 border border-background"></div>
                  <div class="h-6 w-6 rounded-full bg-gradient-to-r from-orange-500 to-red-500 border border-background"></div>
                </div>
                <span>Explore Collections</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Top Collections Section -->
    <section class="py-20 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-foreground mb-4">Top Collections Over Last 7 Days</h2>
        </div>

        <!-- Collection List -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <div 
            v-for="(category, index) in categories.slice(0, 9)" 
            :key="category.id"
            class="flex items-center space-x-4 p-4 rounded-lg hover:bg-muted/50 transition-colors"
          >
            <div class="text-2xl font-bold text-foreground w-8">{{ index + 1 }}</div>
            <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
              <Icon :name="getCategoryIcon(category.name)" class="text-primary" />
            </div>
            <div class="flex-1">
              <h3 class="font-semibold text-foreground">{{ category.name }}</h3>
              <p class="text-sm text-muted-foreground">{{ category.product_count }} items</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Discover Section -->
    <section class="py-20 bg-muted/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12">
          <h2 class="text-4xl font-bold text-foreground">Discover</h2>
          <button class="flex items-center space-x-2 text-sm text-muted-foreground hover:text-foreground transition-colors">
            <Icon name="filter" class="h-4 w-4" />
            <span>All Filters</span>
          </button>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-4 mb-12">
          <button 
            v-for="(filterCategory, index) in filterCategories" 
            :key="index"
            :class="[
              'px-6 py-2 rounded-full text-sm font-medium transition-colors',
              index === 0 
                ? 'bg-primary text-primary-foreground' 
                : 'bg-background text-foreground hover:bg-muted'
            ]"
          >
            {{ filterCategory }}
          </button>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="product in featuredProducts.slice(0, 8)" 
            :key="product.id"
            class="group relative overflow-hidden rounded-xl bg-background"
          >
            <!-- Product Image -->
            <div class="aspect-square overflow-hidden rounded-xl bg-muted">
              <img 
                v-if="product.image" 
                :src="product.image" 
                :alt="product.name" 
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
              />
              <div v-else class="flex h-full items-center justify-center">
                <Icon name="package" class="h-16 w-16 text-muted-foreground" />
              </div>
              
              <!-- Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
              
              <!-- Favorite Button -->
              <button class="absolute right-3 top-3 h-8 w-8 rounded-full bg-background/80 backdrop-blur-sm flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                <Icon name="heart" class="h-4 w-4 text-foreground" />
              </button>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <h3 class="font-semibold text-foreground mb-2 line-clamp-1">{{ product.name }}</h3>
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <span class="text-lg font-bold text-foreground">{{ product.price }}</span>
                  <span class="text-sm text-muted-foreground">ETH</span>
                </div>
                <button class="px-4 py-1 rounded-full bg-primary text-primary-foreground text-xs font-medium hover:bg-primary/90 transition-colors">
                  Place bid
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Product Section -->
    <section class="py-20 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <!-- Left - Featured Product -->
          <div class="relative">
            <div class="aspect-square rounded-2xl bg-gradient-to-br from-purple-100 to-purple-50 p-12 relative overflow-hidden">
              <!-- Decorative Elements -->
              <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-primary/10 rounded-2xl"></div>
              
              <!-- Main Product -->
              <div class="relative h-full flex items-center justify-center">
                <div class="text-center">
                  <Icon name="sofa" class="h-32 w-32 text-primary mb-6 mx-auto" />
                  <div class="absolute -top-4 -left-4 h-16 w-16 rounded-full bg-background shadow-lg flex items-center justify-center">
                    <Icon name="heart" class="h-6 w-6 text-red-500" />
                    <span class="absolute -top-2 -right-2 h-6 w-6 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">12</span>
                  </div>
                </div>
              </div>
              
              <!-- Creator Badge -->
              <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-background/80 backdrop-blur-sm rounded-xl p-4">
                  <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                      <span class="text-white text-sm font-bold">F</span>
                    </div>
                    <div class="flex-1">
                      <p class="text-sm text-muted-foreground">Creator</p>
                      <p class="font-semibold text-foreground">FURNISIA</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right - Product Details -->
          <div class="space-y-8">
            <div class="space-y-4">
              <h2 class="text-4xl font-bold text-foreground">
                Modern Living Room
                <br />
                Premium #001
              </h2>
              <p class="text-lg text-muted-foreground leading-relaxed">
                Koleksi furniture modern yang dirancang khusus untuk memberikan kenyamanan maksimal dan estetika contemporary pada ruang tamu Anda.
              </p>
            </div>

            <!-- Price Info -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Highest Price</span>
                <div class="flex items-center space-x-2">
                  <Icon name="trending-up" class="h-4 w-4 text-green-500" />
                  <span class="text-sm text-green-500">Current bid</span>
                </div>
              </div>
              
              <div class="flex items-center space-x-4">
                <div class="text-3xl font-bold text-foreground">Rp 15.250.000</div>
                <span class="text-sm text-muted-foreground">~ $995.45</span>
              </div>

              <!-- Bidders -->
              <div class="flex items-center space-x-4">
                <div class="flex -space-x-2">
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 border-2 border-background"></div>
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-green-500 to-blue-500 border-2 border-background"></div>
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-orange-500 to-red-500 border-2 border-background"></div>
                </div>
                <div class="text-sm text-muted-foreground">
                  <span class="font-semibold text-foreground">8 people</span> are bidding
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-4">
              <button class="flex-1 h-12 px-6 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors">
                Add to Cart
              </button>
              <button class="h-12 w-12 border border-border rounded-lg flex items-center justify-center hover:bg-muted transition-colors">
                <Icon name="heart" class="h-5 w-5 text-foreground" />
              </button>
              <button class="h-12 w-12 border border-border rounded-lg flex items-center justify-center hover:bg-muted transition-colors">
                <Icon name="share" class="h-5 w-5 text-foreground" />
              </button>
            </div>

            <!-- Timeline -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-sm">
                <span class="text-muted-foreground">Listing</span>
                <span class="text-foreground">Other Bids</span>
              </div>
              <div class="text-xs text-muted-foreground">
                <div class="flex items-center space-x-4">
                  <span>⭐ 4.8 (45 reviews)</span>
                  <span>•</span>
                  <span>🚛 Free shipping</span>
                  <span>•</span>
                  <span>🔒 Bergaransi</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-primary-foreground mb-6">
          Create, Explore & Collect
          <br />
          Quality Furniture
        </h2>
        <p class="text-xl text-primary-foreground/80 mb-8">
          Bergabunglah dengan ribuan pelanggan yang sudah merasakan kenyamanan berbelanja di Furnisia
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
          <Link href="/register" class="px-8 py-4 bg-primary-foreground text-primary rounded-lg font-semibold hover:bg-primary-foreground/90 transition-colors">
            Get Started
          </Link>
          <Link href="/products" class="px-8 py-4 border border-primary-foreground text-primary-foreground rounded-lg hover:bg-primary-foreground/10 transition-colors">
            Learn More
          </Link>
        </div>

        <!-- Footer Links -->
        <div class="mt-16 pt-8 border-t border-primary-foreground/20">
          <div class="flex flex-wrap items-center justify-center gap-8 text-sm text-primary-foreground/60">
            <a href="#" class="hover:text-primary-foreground transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-primary-foreground transition-colors">Terms & Conditions</a>
            <a href="#" class="hover:text-primary-foreground transition-colors">About Us</a>
            <a href="#" class="hover:text-primary-foreground transition-colors">FAQ</a>
          </div>
          <div class="mt-6 flex items-center justify-center space-x-4">
            <Icon name="facebook" class="h-5 w-5 text-primary-foreground/60 hover:text-primary-foreground transition-colors cursor-pointer" />
            <Icon name="twitter" class="h-5 w-5 text-primary-foreground/60 hover:text-primary-foreground transition-colors cursor-pointer" />
            <Icon name="instagram" class="h-5 w-5 text-primary-foreground/60 hover:text-primary-foreground transition-colors cursor-pointer" />
          </div>
          <p class="mt-6 text-xs text-primary-foreground/40">© 2025 FURNISIA. All Rights Reserved.</p>
        </div>
      </div>
    </section>
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
  in_stock: boolean
  stock: number
}

interface BestSeller extends Product {
  rating: number
  sold: number
}

interface Category {
  id: number
  name: string
  slug: string
  description?: string
  product_count: number
}

interface Stats {
  total_products: number
  total_categories: number
  happy_customers: number
  years_experience: number
}

defineProps<{
  featuredProducts: Product[]
  categories: Category[]
  bestSellers: BestSeller[]
  stats: Stats
}>()

// Filter categories for discover section
const filterCategories = ['All Categories', 'Living Room', 'Kitchen', 'Bedroom', 'Bathroom', 'Outdoor']

// Icon mapping for categories
const getCategoryIcon = (categoryName: string): string => {
  const iconMap: Record<string, string> = {
    'Peralatan Dapur': 'utensils',
    'Furniture': 'sofa',
    'Dekorasi': 'palette',
    'Elektronik': 'zap',
    'Kamar Mandi': 'droplets',
    'Kamar Tidur': 'bed',
    'Ruang Tamu': 'home',
    'Taman': 'flower',
    'default': 'box'
  }
  
  return iconMap[categoryName] || iconMap.default
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
