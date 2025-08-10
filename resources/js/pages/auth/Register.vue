<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import AuthInputField from '@/components/auth/AuthInputField.vue';
import AuthFormWrapper from '@/components/auth/AuthFormWrapper.vue';
import AuthLinkSection from '@/components/auth/AuthLinkSection.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

// Validation states
const emailValidation = ref<{ valid: boolean | null; message: string; checking: boolean }>({ valid: null, message: '', checking: false });
const passwordValidation = ref<{ valid: boolean | null; message: string; checking: boolean }>({ valid: null, message: '', checking: false });
const passwordConfirmValidation = ref<{ valid: boolean | null; message: string }>({ valid: null, message: '' });

// Debounce timers
let emailTimer: number | null = null;
let passwordTimer: number | null = null;

// Email validation with debounce
const validateEmail = async (email: string) => {
    if (!email || email.length < 3) {
        emailValidation.value = { valid: null, message: '', checking: false };
        return;
    }
    
    emailValidation.value.checking = true;
    
    try {
        const response = await window.fetch('/api/validation/check-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ email })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        emailValidation.value = {
            valid: data.available,
            message: data.message,
            checking: false
        };
    } catch (error) {
        console.error('Email validation error:', error);
        emailValidation.value = {
            valid: false,
            message: 'Gagal memeriksa email',
            checking: false
        };
    }
};

// Password validation with debounce
const validatePassword = async (password: string) => {
    if (!password) {
        passwordValidation.value = { valid: null, message: '', checking: false };
        return;
    }
    
    passwordValidation.value.checking = true;
    
    try {
        const response = await window.fetch('/api/validation/validate-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ password })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        passwordValidation.value = {
            valid: data.valid,
            message: data.message,
            checking: false
        };
    } catch (error) {
        console.error('Password validation error:', error);
        passwordValidation.value = {
            valid: false,
            message: 'Gagal memvalidasi password',
            checking: false
        };
    }
};

// Password confirmation validation
const validatePasswordConfirmation = () => {
    if (!form.password_confirmation) {
        passwordConfirmValidation.value = { valid: null, message: '' };
        return;
    }
    
    const isMatching = form.password === form.password_confirmation;
    passwordConfirmValidation.value = {
        valid: isMatching,
        message: isMatching ? 'Password cocok' : 'Password tidak cocok'
    };
};

// Debounced watchers
watch(() => form.email, (newEmail) => {
    if (emailTimer) clearTimeout(emailTimer);
    emailTimer = setTimeout(() => validateEmail(newEmail), 500);
});

watch(() => form.password, (newPassword) => {
    if (passwordTimer) clearTimeout(passwordTimer);
    passwordTimer = setTimeout(() => validatePassword(newPassword), 500);
    
    // Re-validate confirmation when password changes
    if (form.password_confirmation) {
        setTimeout(() => validatePasswordConfirmation(), 100);
    }
});

watch(() => form.password_confirmation, () => {
    setTimeout(() => validatePasswordConfirmation(), 300);
});

// Form validity check
const isFormValid = computed(() => {
    return form.name &&
           form.email &&
           form.password &&
           form.password_confirmation &&
           emailValidation.value.valid === true &&
           passwordValidation.value.valid === true &&
           passwordConfirmValidation.value.valid === true;
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Daftar Akun Baru" description="Buat akun Furnisia untuk mengakses dashboard dan mengelola pesanan Anda">
        <Head title="Daftar - Furnisia" />

        <AuthFormWrapper
            :processing="form.processing"
            :disabled="!isFormValid"
            submit-text="Daftar Sekarang"
            processing-text="Mendaftar..."
            @submit="submit"
        >
            <template #fields>
                <!-- Name Field -->
                <AuthInputField
                    id="name"
                    label="Nama Lengkap"
                    type="text"
                    icon-type="user"
                    placeholder="Masukkan nama lengkap"
                    v-model="form.name"
                    :error="form.errors.name"
                    :required="true"
                    :autofocus="true"
                    :tabindex="1"
                    autocomplete="name"
                />

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
                    :tabindex="2"
                    autocomplete="email"
                    :validation="emailValidation"
                />

                <!-- Password Field -->
                <AuthInputField
                    id="password"
                    label="Password"
                    type="password"
                    icon-type="password"
                    placeholder="Minimal 8 karakter"
                    v-model="form.password"
                    :error="form.errors.password"
                    :required="true"
                    :tabindex="3"
                    autocomplete="new-password"
                    :validation="passwordValidation"
                />

                <!-- Password Confirmation Field -->
                <AuthInputField
                    id="password_confirmation"
                    label="Konfirmasi Password"
                    type="password"
                    icon-type="shield"
                    placeholder="Ulangi password"
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    :required="true"
                    :tabindex="4"
                    autocomplete="new-password"
                    :validation="passwordConfirmValidation"
                />
            </template>

            <template #footer>
                <!-- Login Link -->
                <AuthLinkSection
                    text="Sudah punya akun?"
                    link-text="Masuk di Sini"
                    :link-href="route('login')"
                    :tabindex="6"
                />
            </template>
        </AuthFormWrapper>
    </AuthBase>
</template>
