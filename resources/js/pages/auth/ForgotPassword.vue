<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import AuthInputField from '@/components/auth/AuthInputField.vue';
import AuthFormWrapper from '@/components/auth/AuthFormWrapper.vue';
import AuthLinkSection from '@/components/auth/AuthLinkSection.vue';
import StatusAlert from '@/components/auth/StatusAlert.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout title="Lupa Password" description="Masukkan email Anda untuk menerima link reset password">
        <Head title="Lupa Password - Furnisia" />

        <StatusAlert 
            v-if="status" 
            :message="status" 
            type="success" 
        />

        <AuthFormWrapper
            :processing="form.processing"
            submit-text="Kirim Link Reset Password"
            processing-text="Mengirim..."
            @submit="submit"
        >
            <template #fields>
                <!-- Email Field -->
                <AuthInputField
                    id="email"
                    label="Alamat Email"
                    type="email"
                    icon-type="email"
                    placeholder="nama@email.com"
                    v-model="form.email"
                    :error="form.errors.email"
                    :required="true"
                    :autofocus="true"
                    :tabindex="1"
                    autocomplete="off"
                />
            </template>

            <template #footer>
                <!-- Back to Login -->
                <AuthLinkSection
                    text="Ingat password Anda?"
                    link-text="Kembali ke Login"
                    :link-href="route('login')"
                    :tabindex="2"
                />
            </template>
        </AuthFormWrapper>
    </AuthLayout>
</template>
