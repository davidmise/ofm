<template>
    <div class="settings-page">
        <div class="settings-header">
            <h1 class="page-title">Settings</h1>
            <p class="page-subtitle">Manage system settings and user accounts</p>
        </div>

        <!-- Tab Navigation -->
        <div class="tabs-nav">
            <button 
                :class="['tab-btn', { active: activeTab === 'users' }]"
                @click="activeTab = 'users'"
            >
                <i class="bi bi-people"></i>
                User Management
            </button>
            <button 
                :class="['tab-btn', { active: activeTab === 'system' }]"
                @click="activeTab = 'system'"
            >
                <i class="bi bi-gear"></i>
                System Settings
            </button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <UserManagement v-if="activeTab === 'users'" />
            <SystemSettings v-if="activeTab === 'system'" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import UserManagement from '../components/settings/UserManagement.vue';
import SystemSettings from '../components/settings/SystemSettings.vue';

const activeTab = ref('users');
</script>

<style scoped>
.settings-page {
    max-width: 1400px;
    margin: 0 auto;
}

.settings-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1rem;
    color: var(--text-secondary);
    margin: 0;
}

.tabs-nav {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 2rem;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: var(--text-secondary);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
    position: relative;
    bottom: -2px;
}

.tab-btn i {
    font-size: 1.125rem;
}

.tab-btn:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
    background: var(--bg-tertiary);
}

.tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
