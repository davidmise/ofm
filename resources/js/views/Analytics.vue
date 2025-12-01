<template>
    <div class="analytics-page">
        <h2 class="mb-4">Fleet Analytics</h2>

        <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Distance (Week)</h6>
                    <h3>{{ stats.total_distance }} km</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Avg Delivery Time</h6>
                    <h3>{{ stats.avg_delivery_time }} hrs</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-dark">
                <div class="card-body">
                    <h6 class="card-title">Fuel Consumption</h6>
                    <h3>{{ stats.fuel_consumption }} L</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Active Alerts</h6>
                    <h3>{{ stats.active_alerts }}</h3>
                </div>
            </div>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Driver Performance</div>
                <div class="card-body">
                    <div class="table-page-header">
                        <div class="table-page-search">
                            <i class="bi bi-search"></i>
                            <input
                                type="text"
                                v-model="driverSearch"
                                placeholder="Search drivers..."
                            >
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Driver</th>
                                    <th>Score</th>
                                    <th>Trips</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="driver in filteredDrivers" :key="driver.id">
                                    <td>{{ driver.name }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" :style="{ width: driver.performance_score + '%' }">
                                                {{ driver.performance_score }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Math.floor(Math.random() * 50) + 10 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">Fleet Status Distribution</div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <!-- Placeholder for Chart.js or similar -->
                    <div class="text-center text-muted">
                        <i class="bi bi-pie-chart-fill" style="font-size: 5rem;"></i>
                        <p>Chart Visualization Placeholder</p>
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

const stats = ref({
    total_distance: 0,
    avg_delivery_time: 0,
    fuel_consumption: 0,
    active_alerts: 0
});

const topDrivers = ref([]);
const driverSearch = ref('');

const filteredDrivers = computed(() => {
    const term = driverSearch.value.trim().toLowerCase();
    if (!term) return topDrivers.value;

    return topDrivers.value.filter((driver) =>
        driver.name?.toLowerCase().includes(term)
    );
});

const fetchAnalytics = async () => {
    try {
        const response = await axios.get('/api/analytics/dashboard');
        stats.value = response.data.stats;
        topDrivers.value = response.data.top_drivers;
    } catch (error) {
        console.error('Error fetching analytics:', error);
    }
};

onMounted(() => {
    fetchAnalytics();
});
</script>
