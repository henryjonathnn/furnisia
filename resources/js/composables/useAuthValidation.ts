import { ref, watch } from 'vue';

interface ValidationState {
  valid: boolean | null;
  message: string;
  checking: boolean;
}

export function useAuthValidation() {
  const emailValidation = ref<ValidationState>({ valid: null, message: '', checking: false });
  const passwordValidation = ref<ValidationState>({ valid: null, message: '', checking: false });
  const passwordConfirmValidation = ref<ValidationState>({ valid: null, message: '' });

  // Debounce timers
  let emailTimer: number | null = null;
  let passwordTimer: number | null = null;

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
        message: 'Gagal memeriksa password',
        checking: false
      };
    }
  };

  const validatePasswordConfirm = (password: string, confirmPassword: string) => {
    if (!confirmPassword) {
      passwordConfirmValidation.value = { valid: null, message: '' };
      return;
    }
    
    const isValid = password === confirmPassword;
    passwordConfirmValidation.value = {
      valid: isValid,
      message: isValid ? 'Password cocok' : 'Password tidak cocok'
    };
  };

  const debouncedEmailValidation = (email: string, delay = 500) => {
    if (emailTimer) clearTimeout(emailTimer);
    emailTimer = window.setTimeout(() => validateEmail(email), delay);
  };

  const debouncedPasswordValidation = (password: string, delay = 500) => {
    if (passwordTimer) clearTimeout(passwordTimer);
    passwordTimer = window.setTimeout(() => validatePassword(password), delay);
  };

  const setupEmailWatch = (emailRef: any) => {
    watch(emailRef, (newEmail) => {
      debouncedEmailValidation(newEmail);
    });
  };

  const setupPasswordWatch = (passwordRef: any) => {
    watch(passwordRef, (newPassword) => {
      debouncedPasswordValidation(newPassword);
    });
  };

  const setupPasswordConfirmWatch = (passwordRef: any, confirmRef: any) => {
    watch([passwordRef, confirmRef], ([password, confirm]) => {
      validatePasswordConfirm(password, confirm);
    });
  };

  return {
    emailValidation,
    passwordValidation,
    passwordConfirmValidation,
    validateEmail,
    validatePassword,
    validatePasswordConfirm,
    debouncedEmailValidation,
    debouncedPasswordValidation,
    setupEmailWatch,
    setupPasswordWatch,
    setupPasswordConfirmWatch
  };
}
