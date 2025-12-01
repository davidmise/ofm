<template>
    <div class="dashboard-view">
        <LoadingSpinner :fullscreen="true" :show="loading" />
        <!-- Hero Section -->
        <div class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Fleet Management Dashboard</h1>
            <p class="hero-subtitle">Monitor and manage your fleet operations in real-time</p>
        </div>
        <div class="hero-stats-grid">
            <div class="hero-stat">
                <div class="stat-icon bg-primary-gradient">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ stats.totalTrucks }}</div>
                    <div class="stat-label">Total Trucks</div>
                </div>
            </div>
            <div class="hero-stat">
                <div class="stat-icon bg-primary-gradient">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ stats.activeDrivers }}</div>
                    <div class="stat-label">Active Drivers</div>
                </div>
            </div>
            <div class="hero-stat">
                <div class="stat-icon bg-primary-gradient">
                    <i class="bi bi-sim"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ stats.simCards }}</div>
                    <div class="stat-label">SIM Cards</div>
                </div>
            </div>
            <div class="hero-stat">
                <div class="stat-icon bg-primary-gradient">
                    <i class="bi bi-broadcast"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">{{ stats.gpsDevices }}</div>
                    <div class="stat-label">GPS Devices</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fleet Overview Section -->
    <div class="section-header mt-4">
        <h2 class="section-title">Fleet Overview</h2>
        <p class="section-subtitle">Real-time insights into your fleet operations</p>
    </div>

    <div class="row g-4">
        <!-- Operations Status -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-value">{{ stats.trucksInOperation }}</div>
                <div class="stat-label">In Operation</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up"></i> 12% from last week
                </div>
            </div>
        </div>

        <!-- Maintenance -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-value">{{ stats.trucksInMaintenance }}</div>
                <div class="stat-label">In Maintenance</div>
                <div class="stat-trend text-warning">
                    <i class="bi bi-arrow-down"></i> 5% from last week
                </div>
            </div>
        </div>

        <!-- Commands Sent -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="bi bi-send-fill"></i>
                </div>
                <div class="stat-value">{{ stats.commandsSent }}</div>
                <div class="stat-label">Commands (Today)</div>
                <div class="stat-trend">
                    <i class="bi bi-arrow-up"></i> Active monitoring
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stat-value">{{ stats.alerts }}</div>
                <div class="stat-label">Active Alerts</div>
                <div class="stat-trend text-danger">
                    <i class="bi bi-arrow-up"></i> Needs attention
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
        <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Fleet Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 pb-0">
                        <div class="table-page-header">
                            <div class="table-page-search">
                                <i class="bi bi-search"></i>
                                <input
                                    type="text"
                                    v-model="activitySearch"
                                    placeholder="Search trucks, drivers, or destinations..."
                                >
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Truck</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Client</th>
                                    <th>Destination</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="truck in filteredRecentTrucks" :key="truck.id">
                                    <td><strong>{{ truck.plate_number }}</strong></td>
                                    <td>{{ truck.driver ? truck.driver.name : 'Unassigned' }}</td>
                                    <td>
                                        <span :class="['badge', getStatusClass(truck.status)]">
                                            {{ truck.status }}
                                        </span>
                                    </td>
                                    <td>{{ truck.client || 'N/A' }}</td>
                                    <td>{{ truck.destination || 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-star-fill me-2"></i>Top Performing Drivers
                    </h5>
                </div>
                <div class="card-body">
                    <div v-for="driver in topDrivers" :key="driver.id" class="driver-item">
                        <div class="driver-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="driver-info">
                            <div class="driver-name">{{ driver.name }}</div>
                            <div class="driver-score">
                                <div class="score-bar">
                                    <div class="score-fill" :style="{ width: driver.performance_score + '%' }"></div>
                                </div>
                            </div>
                        </div>
                        <div class="driver-badge">
                            {{ driver.performance_score }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const loading = ref(false);
const activitySearch = ref('');

const stats = ref({
    totalTrucks: 0,
    activeDrivers: 0,
    simCards: 0,
    gpsDevices: 0,
    trucksInOperation: 0,
    trucksInMaintenance: 0,
    commandsSent: 0,
    alerts: 0
});

const recentTrucks = ref([]);
const topDrivers = ref([]);

const filteredRecentTrucks = computed(() => {
    const term = activitySearch.value.trim().toLowerCase();
    if (!term) return recentTrucks.value;

    return recentTrucks.value.filter((truck) => {
        const values = [
            truck.plate_number,
            truck.driver?.name,
            truck.status,
            truck.client,
            truck.destination
        ];
        return values.some((value) => value?.toString().toLowerCase().includes(term));
    });
});

const fetchDashboardData = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/dashboard/summary');
        const summaryStats = data.stats || {};

        stats.value.totalTrucks = summaryStats.totalTrucks ?? 0;
        stats.value.activeDrivers = summaryStats.activeDrivers ?? 0;
        stats.value.simCards = summaryStats.simCards ?? 0;
        stats.value.gpsDevices = summaryStats.gpsDevices ?? 0;
        stats.value.trucksInOperation = summaryStats.trucksInOperation ?? 0;
        stats.value.trucksInMaintenance = summaryStats.trucksInMaintenance ?? 0;
        stats.value.commandsSent = summaryStats.commandsSent ?? 0;
        stats.value.alerts = summaryStats.alerts ?? 0;

        recentTrucks.value = data.recentTrucks || [];
        topDrivers.value = data.topDrivers || [];
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    } finally {
        loading.value = false;
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'active': return 'bg-success';
        case 'maintenance': return 'bg-warning';
        case 'inactive': return 'bg-secondary';
        default: return 'bg-secondary';
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
.hero-section {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 16px;
    padding: 3rem 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="white" opacity="0.1"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    margin-bottom: 2.5rem;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
}

.hero-subtitle {
    font-size: 1.125rem;
    opacity: 0.95;
    font-weight: 400;
}

.hero-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    position: relative;
    z-index: 1;
}

.hero-stat {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all var(--transition-base);
}

.hero-stat:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.hero-stat .stat-icon {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.25) !important;
    backdrop-filter: blur(10px);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stat-details {
    flex: 1;
}

.hero-stat .stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: white;
}

.hero-stat .stat-label {
    font-size: 0.875rem;
    opacity: 0.9;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-header {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.section-subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
}

.stat-trend {
    margin-top: 0.75rem;
    font-size: 0.8125rem;
    color: var(--success);
    font-weight: 500;
}

.stat-trend i {
    font-size: 0.75rem;
}

.driver-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    background: var(--bg-tertiary);
    margin-bottom: 0.75rem;
    transition: all var(--transition-fast);
}

.driver-item:hover {
    background: var(--primary);
    color: white;
    transform: translateX(4px);
}

.driver-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.driver-item:hover .driver-avatar {
    background: white;
    color: var(--primary);
}

.driver-info {
    flex: 1;
}

.driver-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 0.9375rem;
}

.score-bar {
    height: 4px;
    background: var(--border-color);
    border-radius: 2px;
    overflow: hidden;
}

.score-fill {
    height: 100%;
    background: var(--primary);
    border-radius: 2px;
    transition: width var(--transition-slow);
}

.driver-item:hover .score-fill {
    background: white;
}

.driver-badge {
    font-weight: 700;
    font-size: 0.875rem;
    color: var(--primary);
}

.driver-item:hover .driver-badge {
    color: white;
}
</style>
