<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';

interface Props {
    text: string;
    linkText: string;
    linkHref: string;
    tabindex?: number;
}

withDefaults(defineProps<Props>(), {
    tabindex: 20
});
</script>

<template>
    <div class="text-center pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
            {{ text }}
            <TextLink 
                :href="linkHref" 
                class="text-black font-semibold hover:underline ml-1" 
                :tabindex="tabindex"
            >
                {{ linkText }}
            </TextLink>
        </p>
    </div>
</template>
