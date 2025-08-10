<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import AuthInputField from '@/components/auth/AuthInputField.vue';
import AuthFormWrapper from '@/components/auth/AuthFormWrapper.vue';
import AuthLinkSection from '@/components/auth/AuthLinkSection.vue';
import RememberMeCheckbox from '@/components/auth/RememberMeCheckbox.vue';
import StatusAlert from '@/components/auth/StatusAlert.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            // Force refresh setelah login berhasil untuk update session state
            window.location.reload();
        }
    });
};
</script>

<template>
    <AuthBase title="Masuk ke Akun Anda" description="Masukkan email dan password untuk mengakses dashboard Furnisia">
        <Head title="Masuk - Furnisia" />

        <StatusAlert 
            v-if="status" 
            :message="status" 
            type="success" 
        />

        <AuthFormWrapper
            :processing="form.processing"
            submit-text="Masuk ke Dashboard"
            processing-text="Masuk..."
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
                        autocomplete="email"
                />

            <!-- Password Field -->
                <AuthInputField
                        id="password"
                    label="Password"
                        type="password"
                    icon-type="password"
                    placeholder="Masukkan password"
                    v-model="form.password"
                    :error="form.errors.password"
                    :required="true"
                        :tabindex="2"
                        autocomplete="current-password"
                    :show-forgot-password="canResetPassword"
                    :forgot-password-link="route('password.request')"
                />

            <!-- Remember Me -->
                <RememberMeCheckbox
                    v-model="form.remember" 
                    :tabindex="3"
                />
            </template>

            <template #footer>
            <!-- Register Link -->
                <AuthLinkSection
                    text="Belum punya akun?"
                    link-text="Daftar Sekarang"
                    :link-href="route('register')"
                    :tabindex="5"
                />
            </template>
        </AuthFormWrapper>
    </AuthBase>
</template>
