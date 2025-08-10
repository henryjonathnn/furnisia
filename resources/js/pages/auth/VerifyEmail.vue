<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import StatusAlert from '@/components/auth/StatusAlert.vue';
import AuthFormWrapper from '@/components/auth/AuthFormWrapper.vue';
import AuthLinkSection from '@/components/auth/AuthLinkSection.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
        <Head title="Email verification" />

        <StatusAlert 
            v-if="status === 'verification-link-sent'" 
            message="Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran."
            type="success" 
        />

        <AuthFormWrapper
            :processing="form.processing"
            submit-text="Kirim Ulang Email Verifikasi"
            processing-text="Mengirim..."
            @submit="submit"
        >
            <template #footer>
                <AuthLinkSection
                    text=""
                    link-text="Keluar"
                    :link-href="route('logout')"
                    :tabindex="2"
                />
            </template>
        </AuthFormWrapper>
    </AuthLayout>
</template>
