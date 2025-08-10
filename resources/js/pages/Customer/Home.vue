<template>
  <CustomerLayout>
    <Head title="Furnisia - Premium Home Furniture Store" />

    <!-- Categories Section -->
    <section class="py-24 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-black mb-4">Belanja Berdasarkan Kategori</h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Jelajahi koleksi furniture kami yang dirancang khusus untuk setiap ruangan di rumah Anda
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
          <div 
            v-for="category in categories" 
            :key="category.id"
            class="group relative overflow-hidden rounded-2xl bg-white border-2 border-gray-200 hover:border-black p-8 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-2 cursor-pointer"
          >
            <div 
              class="h-20 w-20 rounded-2xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-all"
            >
              <img 
                :src="getCategoryIcon(category.name)" 
                :alt="category.name"
                class="h-12 w-12 object-contain filter grayscale group-hover:grayscale-0 transition-all"
                loading="lazy"
              />
            </div>
            <h3 class="font-bold text-black mb-2 group-hover:text-gray-700 transition-colors">
              {{ category.name }}
            </h3>
            <p class="text-sm text-gray-600">{{ category.product_count }} produk</p>
            
            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
              <div class="h-8 w-8 rounded-full bg-black text-white flex items-center justify-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
          </div>
          </div>
        </div>

        <!-- View All Categories Button -->
        <div class="text-center mt-12">
          <Link href="/categories" class="inline-flex items-center px-8 py-4 bg-black text-white rounded-xl font-semibold hover:bg-gray-800 transition-all hover:shadow-lg transform hover:-translate-y-0.5">
            <span>Lihat Semua Kategori</span>
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Products Section -->
    <section class="py-24 bg-muted/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-12">
          <div>
            <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-2">Produk Unggulan</h2>
            <p class="text-muted-foreground">Pilihan terbaik yang mendefinisikan gaya hidup modern</p>
          </div>
          <button class="flex items-center space-x-2 text-sm text-primary hover:text-primary/80 transition-colors">
            <span>Lihat Semua</span>
            <Icon name="arrow-right" class="h-4 w-4" />
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div 
            v-for="product in featuredProducts.slice(0, 8)" 
            :key="product.id"
            class="group relative bg-background rounded-2xl border border-border/50 overflow-hidden hover:shadow-lg transition-all duration-300 hover:scale-[1.02]"
          >
            <!-- Product Image -->
            <div class="aspect-square overflow-hidden bg-muted/50 relative">
              <img 
                v-if="product.image" 
                :src="product.image" 
                :alt="product.name" 
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
              />
              <div v-else class="flex h-full items-center justify-center">
                <Icon name="package" class="h-16 w-16 text-muted-foreground/50" />
              </div>
              
              <!-- Quick Actions -->
              <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="h-8 w-8 rounded-lg bg-background/80 backdrop-blur-sm border border-border/50 flex items-center justify-center hover:bg-background transition-colors">
                  <Icon name="heart" class="h-4 w-4 text-foreground" />
                </button>
                <button class="h-8 w-8 rounded-lg bg-background/80 backdrop-blur-sm border border-border/50 flex items-center justify-center hover:bg-background transition-colors">
                  <Icon name="eye" class="h-4 w-4 text-foreground" />
                </button>
              </div>
              
              <!-- Stock Badge -->
              <div v-if="product.stock > 0" class="absolute top-3 left-3">
                <span class="px-2 py-1 bg-green-500/20 text-green-600 text-xs font-medium rounded-lg border border-green-500/30">
                  Tersedia
                </span>
              </div>
              <div v-else class="absolute top-3 left-3">
                <span class="px-2 py-1 bg-red-500/20 text-red-600 text-xs font-medium rounded-lg border border-red-500/30">
                  Habis
                </span>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-4">
              <h3 class="font-semibold text-foreground mb-2 line-clamp-1">{{ product.name }}</h3>
              <div class="flex items-center justify-between mb-3">
                <div class="text-lg font-bold text-foreground">{{ product.price }}</div>
                <div class="flex items-center space-x-1">
                  <Icon name="star" class="h-4 w-4 text-yellow-500 fill-current" />
                  <span class="text-sm text-muted-foreground">4.8</span>
                </div>
              </div>
              
              <button 
                :disabled="product.stock === 0"
                class="w-full h-9 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ product.stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Best Sellers Section -->
    <section class="py-24 bg-background">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">Produk Terlaris</h2>
          <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
            Produk paling disukai pelanggan kami dengan kualitas dan desain yang telah terbukti
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div 
            v-for="(product, index) in bestSellers.slice(0, 3)" 
            :key="product.id"
            class="group relative bg-gradient-to-br from-background to-muted/50 rounded-2xl border border-border/50 overflow-hidden hover:shadow-xl transition-all duration-500 hover:scale-[1.02]"
          >
            <!-- Rank Badge -->
            <div class="absolute top-4 left-4 z-10">
              <div class="h-8 w-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-bold">
                {{ index + 1 }}
              </div>
            </div>

            <!-- Product Image -->
            <div class="aspect-[4/3] overflow-hidden bg-muted/50 relative">
              <img 
                v-if="product.image" 
                :src="product.image" 
                :alt="product.name" 
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
              />
              <div v-else class="flex h-full items-center justify-center">
                <Icon name="sofa" class="h-20 w-20 text-muted-foreground/50" />
              </div>
              
              <!-- Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </div>

            <!-- Product Info -->
            <div class="p-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-primary bg-primary/10 px-2 py-1 rounded-lg">Terlaris</span>
                <div class="flex items-center space-x-1">
                  <Icon name="star" class="h-4 w-4 text-yellow-500 fill-current" />
                  <span class="text-sm font-medium text-foreground">{{ product.rating || '4.9' }}</span>
                </div>
              </div>
              
              <h3 class="font-bold text-foreground mb-2 text-lg">{{ product.name }}</h3>
              
              <div class="flex items-center justify-between mb-4">
                <div class="text-xl font-bold text-foreground">{{ product.price }}</div>
                <span class="text-sm text-muted-foreground">{{ product.sold || 0 }} terjual</span>
              </div>

              <button class="w-full h-10 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors">
                Tambah ke Keranjang
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Customer Testimonials Section -->
    <section class="py-24 bg-muted/30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">Apa Kata Pelanggan Kami</h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
            Pengalaman nyata dari ribuan pelanggan yang puas telah mentransformasi rumah mereka dengan Furnisia
            </p>
          </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-background rounded-2xl border border-border/50 p-8 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-1 mb-4">
              <Icon v-for="i in 5" :key="i" name="star" class="h-4 w-4 text-yellow-500 fill-current" />
            </div>
            <p class="text-muted-foreground mb-6 leading-relaxed">
              "Kualitas furniture dari Furnisia benar-benar luar biasa! Sofa yang saya beli sangat nyaman dan desainnya perfect untuk ruang tamu modern saya. Pelayanannya juga sangat memuaskan."
            </p>
            <div class="flex items-center space-x-4">
              <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                <span class="text-white font-semibold">SR</span>
              </div>
              <div>
                <h4 class="font-semibold text-foreground">Sarah Ramadhani</h4>
                <p class="text-sm text-muted-foreground">Interior Designer, Jakarta</p>
              </div>
            </div>
          </div>

          <!-- Testimonial 2 -->
          <div class="bg-background rounded-2xl border border-border/50 p-8 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-1 mb-4">
              <Icon v-for="i in 5" :key="i" name="star" class="h-4 w-4 text-yellow-500 fill-current" />
            </div>
            <p class="text-muted-foreground mb-6 leading-relaxed">
              "Sudah 2 tahun menggunakan produk dari Furnisia dan semuanya masih dalam kondisi prima. Material berkualitas tinggi dengan harga yang reasonable. Highly recommended!"
            </p>
            <div class="flex items-center space-x-4">
              <div class="h-12 w-12 rounded-full bg-gradient-to-r from-green-500 to-blue-500 flex items-center justify-center">
                <span class="text-white font-semibold">BP</span>
              </div>
              <div>
                <h4 class="font-semibold text-foreground">Budi Prasetyo</h4>
                <p class="text-sm text-muted-foreground">Business Owner, Surabaya</p>
              </div>
            </div>
          </div>

          <!-- Testimonial 3 -->
          <div class="bg-background rounded-2xl border border-border/50 p-8 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center space-x-1 mb-4">
              <Icon v-for="i in 5" :key="i" name="star" class="h-4 w-4 text-yellow-500 fill-current" />
            </div>
            <p class="text-muted-foreground mb-6 leading-relaxed">
              "Pengalaman berbelanja di Furnisia sangat menyenangkan. Tim customer service-nya responsif, pengiriman cepat, dan produknya sesuai ekspektasi. Pasti akan order lagi!"
            </p>
            <div class="flex items-center space-x-4">
              <div class="h-12 w-12 rounded-full bg-gradient-to-r from-orange-500 to-red-500 flex items-center justify-center">
                <span class="text-white font-semibold">AF</span>
              </div>
              <div>
                <h4 class="font-semibold text-foreground">Anita Fitriani</h4>
                <p class="text-sm text-muted-foreground">Teacher, Bandung</p>
              </div>
            </div>
          </div>
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

// Generate icon path from category name
const getCategoryIcon = (categoryName: string): string => {
  // Convert category name to kebab-case filename
  const filename = categoryName
    .toLowerCase()
    .replace(/\s+/g, '-') // replace spaces with hyphens
    .replace(/[^a-z0-9-]/g, '') // remove special characters except hyphens
  
  return `/images/category-icons/${filename}.png`
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
