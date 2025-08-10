<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import AuthInputField from '@/components/auth/AuthInputField.vue';
import AuthFormWrapper from '@/components/auth/AuthFormWrapper.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AuthLayout title="Confirm your password" description="This is a secure area of the application. Please confirm your password before continuing.">
        <Head title="Confirm password" />

        <AuthFormWrapper
            :processing="form.processing"
            submit-text="Konfirmasi Password"
            processing-text="Memproses..."
            @submit="submit"
        >
            <template #fields>
                <AuthInputField
                    id="password"
                    label="Password"
                    type="password"
                    icon-type="password"
                    placeholder="Masukkan password Anda"
                    v-model="form.password"
                    :error="form.errors.password"
                    :required="true"
                    :autofocus="true"
                    :tabindex="1"
                    autocomplete="current-password"
                />
            </template>
        </AuthFormWrapper>
    </AuthLayout>
</template>
