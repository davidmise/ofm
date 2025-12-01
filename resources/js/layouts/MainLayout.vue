<template>
    <div class="layout-wrapper">
        <!-- Sidebar -->
        <aside :class="['sidebar', { 'sidebar-collapsed': sidebarCollapsed }]">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="bi bi-truck-front-fill"></i>
                    <span v-if="!sidebarCollapsed" class="logo-text">Fleet Manager</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <router-link to="/" class="nav-item" active-class="router-link-active-custom" exact-active-class="router-link-active">
                    <i class="bi bi-speedometer2"></i>
                    <span v-if="!sidebarCollapsed">Dashboard</span>
                </router-link>
                <router-link to="/trucks" class="nav-item">
                    <i class="bi bi-truck"></i>
                    <span v-if="!sidebarCollapsed">Trucks</span>
                </router-link>
                <router-link to="/drivers" class="nav-item">
                    <i class="bi bi-person-badge"></i>
                    <span v-if="!sidebarCollapsed">Drivers</span>
                </router-link>
                <router-link to="/sim-cards" class="nav-item">
                    <i class="bi bi-sim"></i>
                    <span v-if="!sidebarCollapsed">SIM Cards</span>
                </router-link>
                <router-link to="/gps-devices" class="nav-item">
                    <i class="bi bi-broadcast"></i>
                    <span v-if="!sidebarCollapsed">GPS Devices</span>
                </router-link>
                <router-link to="/assignments" class="nav-item">
                    <i class="bi bi-link-45deg"></i>
                    <span v-if="!sidebarCollapsed">Assignments</span>
                </router-link>
                <router-link to="/command-center" class="nav-item">
                    <i class="bi bi-terminal"></i>
                    <span v-if="!sidebarCollapsed">Command Center</span>
                </router-link>
                <router-link to="/tracking" class="nav-item">
                    <i class="bi bi-geo-alt"></i>
                    <span v-if="!sidebarCollapsed">Tracking</span>
                </router-link>
                <router-link to="/analytics" class="nav-item">
                    <i class="bi bi-graph-up"></i>
                    <span v-if="!sidebarCollapsed">Analytics</span>
                </router-link>
                <router-link to="/settings" class="nav-item">
                    <i class="bi bi-gear"></i>
                    <span v-if="!sidebarCollapsed">Settings</span>
                </router-link>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navbar -->
            <header class="navbar-main">
                <button @click="toggleSidebar" class="sidebar-toggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="navbar-spacer"></div>
                
                <div class="navbar-actions">
                    <button @click="toggleTheme" class="theme-toggle" :title="currentTheme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                        <i :class="currentTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill'"></i>
                    </button>
                    
                    <div class="user-menu">
                        <i class="bi bi-person-circle"></i>
                        <span class="user-name">{{ userName }}</span>
                    </div>

                    <button @click="handleLogout" class="logout-btn" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import ThemeService from '../services/ThemeService';
import ApiService from '../services/ApiService';

const router = useRouter();
const sidebarCollapsed = ref(false);
const currentTheme = ref('light');

const userName = computed(() => {
    const user = ApiService.getUser();
    return user?.name || 'Admin';
});

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const toggleTheme = () => {
    currentTheme.value = ThemeService.toggleTheme();
};

const handleLogout = async () => {
    await ApiService.logout();
    router.push('/login');
};

onMounted(() => {
    currentTheme.value = ThemeService.getCurrentTheme();
});
</script>

<style scoped>
.layout-wrapper {
    display: flex;
    min-height: 100vh;
    background-color: var(--bg-secondary);
}

/* ===== SIDEBAR ===== */
.sidebar {
    width: var(--sidebar-width);
    background-color: var(--bg-primary);
    border-right: 1px solid var(--border-color);
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    transition: width var(--transition-base);
    z-index: 1000;
    box-shadow: var(--shadow);
}

.sidebar-collapsed {
    width: 80px;
}

.sidebar-header {
    padding: 1.25rem;
    border-bottom: 1px solid var(--border-color);
    min-height: var(--navbar-height);
    display: flex;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    color: var(--primary);
    font-weight: 700;
}

.logo-text {
    font-size: 1.125rem;
    white-space: nowrap;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.875rem 1.25rem;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all var(--transition-fast);
    font-size: 0.9375rem;
    font-weight: 500;
    margin: 0.25rem 0.75rem;
    border-radius: 8px;
}

.nav-item i {
    font-size: 1.25rem;
    min-width: 24px;
}

.nav-item:hover {
    background-color: var(--bg-tertiary);
    color: var(--text-primary);
}

.nav-item.router-link-active {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    box-shadow: var(--shadow-md);
}

.sidebar-collapsed .nav-item {
    justify-content: center;
    padding: 0.875rem;
}

.sidebar-collapsed .nav-item span {
    display: none;
}

/* ===== MAIN CONTENT ===== */
.main-content {
    flex: 1;
    margin-left: var(--sidebar-width);
    display: flex;
    flex-direction: column;
    transition: margin-left var(--transition-base);
}

.sidebar-collapsed + .main-content {
    margin-left: 80px;
}

/* ===== NAVBAR ===== */
.navbar-main {
    height: var(--navbar-height);
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    gap: 1rem;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: var(--shadow-sm);
}

.sidebar-toggle {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--bg-primary);
    color: var(--text-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    font-size: 1.25rem;
}

.sidebar-toggle:hover {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.navbar-spacer {
    flex: 1;
}

.navbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    background: var(--bg-tertiary);
    color: var(--text-primary);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.user-menu:hover {
    background: var(--primary);
    color: white;
}

.user-menu i {
    font-size: 1.5rem;
}

.user-name {
    font-weight: 500;
    font-size: 0.875rem;
}

.logout-btn {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: var(--bg-primary);
    color: var(--text-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    font-size: 1.125rem;
}

.logout-btn:hover {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
}


/* ===== PAGE CONTENT ===== */
.page-content {
    flex: 1;
    padding: 2rem;
    max-width: 1600px;
    margin: 0 auto;
    width: 100%;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar:not(.sidebar-collapsed) {
        transform: translateX(0);
    }
    
    .main-content {
        margin-left: 0;
    }
    
    .user-name {
        display: none;
    }
}
</style>
