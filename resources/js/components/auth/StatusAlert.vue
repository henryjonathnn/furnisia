<script setup lang="ts">
interface Props {
    message: string;
    type?: 'success' | 'error' | 'info';
}

withDefaults(defineProps<Props>(), {
    type: 'success'
});

const getAlertClasses = (type: string) => {
    const classes = {
        success: 'text-green-600 bg-green-50 border-green-200',
        error: 'text-red-600 bg-red-50 border-red-200',
        info: 'text-blue-600 bg-blue-50 border-blue-200'
    };
    return classes[type as keyof typeof classes] || classes.success;
};
</script>

<template>
    <div 
        class="mb-6 text-center text-sm font-medium border rounded-lg p-3"
        :class="getAlertClasses(type)"
    >
        {{ message }}
    </div>
</template>
