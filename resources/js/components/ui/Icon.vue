<template>
  <i :class="iconClass" />
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  name: string
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  class: ''
})

const iconClass = computed(() => {
  // Map icon names to FontAwesome classes
  const iconMap: Record<string, string> = {
    'store': 'fas fa-store',
    'chart-pie': 'fas fa-chart-pie',
    'shopping-bag': 'fas fa-shopping-bag',
    'box': 'fas fa-box',
    'warehouse': 'fas fa-warehouse',
    'users': 'fas fa-users',
    'chart-line': 'fas fa-chart-line',
    'cog': 'fas fa-cog',
    'settings': 'fas fa-cog',
    'chevron-right': 'fas fa-chevron-right',
    'user': 'fas fa-user',
    'log-out': 'fas fa-sign-out-alt',
    'menu': 'fas fa-bars',
    'search': 'fas fa-search',
    'bell': 'fas fa-bell',
    'dollar-sign': 'fas fa-dollar-sign',
    'shopping-cart': 'fas fa-shopping-cart',
    'exclamation-triangle': 'fas fa-exclamation-triangle',
    'star': 'fas fa-star',
    'arrow-up': 'fas fa-arrow-up',
    'arrow-down': 'fas fa-arrow-down',
    'chart-area': 'fas fa-chart-area',
    'plus': 'fas fa-plus',
    'tags': 'fas fa-tags',
    'download': 'fas fa-download',
    'bullhorn': 'fas fa-bullhorn',
    'package': 'fas fa-box',
    'clock': 'fas fa-clock',
    'chevron-down': 'fas fa-chevron-down',
    'facebook': 'fab fa-facebook-f',
    'twitter': 'fab fa-twitter',
    'instagram': 'fab fa-instagram',
    
    // New icons for home page
    'play': 'fas fa-play',
    'cube': 'fas fa-cube',
    'home': 'fas fa-home',
    'sofa': 'fas fa-couch',
    'bed': 'fas fa-bed',
    'chair': 'fas fa-chair',
    'utensils': 'fas fa-utensils',
    'palette': 'fas fa-palette',
    'zap': 'fas fa-bolt',
    'droplets': 'fas fa-droplet',
    'flower': 'fas fa-leaf',
    'filter': 'fas fa-filter',
    'heart': 'fas fa-heart',
    'share': 'fas fa-share',
    'trending-up': 'fas fa-arrow-trend-up',
    'credit-card': 'fas fa-credit-card',
    'truck': 'fas fa-truck',
    'arrow-right': 'fas fa-arrow-right',
    'eye': 'fas fa-eye',
    'shield-check': 'fas fa-shield-alt'
  }

  const iconName = iconMap[props.name] || `fas fa-${props.name}`
  
  return `${iconName} ${props.class}`.trim()
})
</script>
