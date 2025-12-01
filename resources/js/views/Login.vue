<template>
    <div class="login-container">
        <button
            class="auth-theme-toggle"
            type="button"
            @click="toggleTheme"
            :title="currentTheme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme'"
        >
            <i :class="currentTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill'"></i>
        </button>
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <i class="bi bi-truck"></i>
                </div>
                <h1 class="login-title">Fleet Manager</h1>
                <p class="login-subtitle">Sign in to access your dashboard</p>
            </div>

            <div v-if="error" class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ error }}
            </div>

            <form @submit.prevent="handleLogin" class="login-form">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-2"></i>Email Address
                    </label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        v-model="credentials.email"
                        placeholder="admin@overland.co.tz"
                        required
                        :disabled="loading"
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        v-model="credentials.password"
                        placeholder="Enter your password"
                        required
                        :disabled="loading"
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-login"
                    :disabled="loading"
                >
                    <span v-if="loading">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Signing in...
                    </span>
                    <span v-else>
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Sign In
                    </span>
                </button>
            </form>

            <div class="login-footer">
                <p class="text-muted mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Default credentials: admin@overland.co.tz / 123456
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import ApiService from '../services/ApiService';
import ThemeService from '../services/ThemeService';

const router = useRouter();

const credentials = ref({
    email: '',
    password: ''
});

const loading = ref(false);
const error = ref('');
const currentTheme = ref(ThemeService.getCurrentTheme());

const handleLogin = async () => {
    loading.value = true;
    error.value = '';

    try {
        const result = await ApiService.login(
            credentials.value.email,
            credentials.value.password
        );

        if (result.success) {
            // Redirect to dashboard
            router.push('/');
        } else {
            error.value = result.message || 'Invalid email or password';
        }
    } catch (err) {
        error.value = 'An error occurred. Please try again.';
        console.error('Login error:', err);
    } finally {
        loading.value = false;
    }
};

const toggleTheme = () => {
    currentTheme.value = ThemeService.toggleTheme();
};
</script>

<style scoped>
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--auth-bg-start) 0%, var(--auth-bg-end) 100%);
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.login-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="white" opacity="0.08"/></svg>');
    opacity: 1;
    mix-blend-mode: screen;
}

.login-card {
    background: var(--auth-card-bg);
    border: 1px solid var(--auth-card-border);
    border-radius: 16px;
    padding: 3rem;
    width: 100%;
    max-width: 450px;
    box-shadow: var(--auth-card-shadow);
    position: relative;
    z-index: 1;
}

.login-header {
    text-align: center;
    margin-bottom: 2rem;
}

.logo-container {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
}

.login-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.login-subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
    margin: 0;
}

.login-form {
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.9375rem;
}

.form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all var(--transition-base);
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.form-control:disabled {
    background-color: var(--bg-tertiary);
    cursor: not-allowed;
}

.btn-login {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    border: none;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    cursor: pointer;
    transition: all var(--transition-base);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-login:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
}

.btn-login:active:not(:disabled) {
    transform: translateY(0);
}

.btn-login:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.login-footer {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
}

.alert-danger {
    background-color: rgba(239, 68, 68, 0.12);
    color: var(--danger);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.text-muted {
    color: var(--text-secondary) !important;
}

.auth-theme-toggle {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    border: none;
    background: rgba(255, 255, 255, 0.3);
    color: var(--text-primary);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-base);
    backdrop-filter: blur(6px);
    z-index: 2;
}

[data-theme="dark"] .auth-theme-toggle {
    background: rgba(15, 23, 42, 0.65);
}

.auth-theme-toggle:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}
</style>
