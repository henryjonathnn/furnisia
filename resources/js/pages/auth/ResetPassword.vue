<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import AuthInputField from '@/components/auth/AuthInputField.vue';
import AuthFormWrapper from '@/components/auth/AuthFormWrapper.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <AuthLayout title="Reset Password" description="Masukkan password baru untuk akun Furnisia Anda">
        <Head title="Reset Password - Furnisia" />

        <AuthFormWrapper
            :processing="form.processing"
            submit-text="Perbarui Password"
            processing-text="Memperbarui..."
            @submit="submit"
        >
            <template #fields>
                <!-- Email Field (Read-only) -->
                <AuthInputField
                    id="email"
                    label="Email"
                    type="email"
                    icon-type="email"
                    placeholder=""
                    v-model="form.email"
                    :error="form.errors.email"
                    :readonly="true"
                    :tabindex="1"
                    autocomplete="email"
                />

                <!-- New Password Field -->
                <AuthInputField
                    id="password"
                    label="Password Baru"
                    type="password"
                    icon-type="password"
                    placeholder="Minimal 8 karakter"
                    v-model="form.password"
                    :error="form.errors.password"
                    :required="true"
                    :autofocus="true"
                    :tabindex="2"
                    autocomplete="new-password"
                />

                <!-- Password Confirmation Field -->
                <AuthInputField
                    id="password_confirmation"
                    label="Konfirmasi Password Baru"
                    type="password"
                    icon-type="shield"
                    placeholder="Ulangi password baru"
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    :required="true"
                    :tabindex="3"
                    autocomplete="new-password"
                />
            </template>
        </AuthFormWrapper>
    </AuthLayout>
</template>
