<template>
    <div class="command-center-page">
        <h2 class="mb-4">GPS Command Center</h2>

        <div class="row">
        <!-- Command Panel -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-terminal me-2"></i> Send Command
                </div>
                <div class="card-body">
                    <form @submit.prevent="sendCommand">
                        <div class="mb-3">
                            <label class="form-label">Select Truck</label>
                            <select v-model="form.truck_id" class="form-select" required @change="onTruckSelect">
                                <option v-for="truck in trucks" :key="truck.id" :value="truck.id">
                                    {{ truck.plate_number }}
                                </option>
                            </select>
                        </div>

                        <div v-if="selectedTruck" class="mb-3 p-2 bg-light rounded">
                            <small class="d-block"><strong>Driver:</strong> {{ selectedTruck.driver ? selectedTruck.driver.name : 'Unassigned' }}</small>
                            <small class="d-block"><strong>Status:</strong> {{ selectedTruck.status }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Command Type</label>
                            <select v-model="form.command_type" class="form-select" required>
                                <option value="locate">Locate Vehicle</option>
                                <option value="immobilize" class="text-danger">Immobilize Vehicle</option>
                                <option value="resume" class="text-success">Resume Vehicle</option>
                                <option value="voice_monitor">Voice Monitor</option>
                            </select>
                        </div>

                        <div class="mb-3" v-if="form.command_type === 'voice_monitor'">
                            <label class="form-label">Duration (seconds)</label>
                            <input v-model="form.payload.duration" type="number" class="form-control" placeholder="30">
                        </div>

                        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                            Send Command
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Command History -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clock-history me-2"></i> Command History
                </div>
                <div class="card-body">
                    <div class="table-page-header">
                        <div class="table-page-search">
                            <i class="bi bi-search"></i>
                            <input
                                type="text"
                                v-model="commandSearch"
                                placeholder="Search commands, trucks, or status..."
                            >
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Truck</th>
                                    <th>Command</th>
                                    <th>Status</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="command in filteredCommands" :key="command.id">
                                    <td>{{ formatDate(command.created_at) }}</td>
                                    <td>{{ command.truck ? command.truck.plate_number : 'Unknown' }}</td>
                                    <td>
                                        <span :class="getCommandBadgeClass(command.command_type)">
                                            {{ formatCommandType(command.command_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span :class="getStatusBadgeClass(command.status)">
                                            {{ command.status }}
                                        </span>
                                    </td>
                                    <td>{{ command.user ? command.user.name : 'System' }}</td>
                                </tr>
                                <tr v-if="commands.length === 0">
                                    <td colspan="5" class="text-center">No commands found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';

const trucks = ref([]);
const commands = ref([]);
const loading = ref(false);
const commandSearch = ref('');
const form = reactive({
    truck_id: null,
    command_type: 'locate',
    payload: {}
});

const selectedTruck = computed(() => {
    return trucks.value.find(t => t.id === form.truck_id);
});

const fetchTrucks = async () => {
    try {
        const response = await axios.get('/api/trucks?per_page=100');
        trucks.value = response.data.data;
    } catch (error) {
        console.error('Error fetching trucks:', error);
    }
};

const fetchCommands = async () => {
    try {
        const response = await axios.get('/api/commands');
        commands.value = response.data.data;
    } catch (error) {
        console.error('Error fetching commands:', error);
    }
};

const filteredCommands = computed(() => {
    const term = commandSearch.value.trim().toLowerCase();
    if (!term) return commands.value;

    return commands.value.filter((command) => {
        const values = [
            command.truck?.plate_number,
            command.command_type,
            formatCommandType(command.command_type),
            command.status,
            command.user?.name,
            formatDate(command.created_at)
        ];

        return values.some((value) => value?.toString().toLowerCase().includes(term));
    });
});

const sendCommand = async () => {
    if (!confirm(`Are you sure you want to send "${form.command_type}" to ${selectedTruck.value.plate_number}?`)) return;

    loading.value = true;
    try {
        await axios.post('/api/commands', form);
        alert('Command sent successfully!');
        fetchCommands();
        // Reset form but keep truck selected
        form.command_type = 'locate';
        form.payload = {};
    } catch (error) {
        console.error('Error sending command:', error);
        alert('Failed to send command.');
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};

const formatCommandType = (type) => {
    return type.replace('_', ' ').toUpperCase();
};

const getCommandBadgeClass = (type) => {
    switch (type) {
        case 'immobilize': return 'badge bg-danger';
        case 'resume': return 'badge bg-success';
        case 'locate': return 'badge bg-info text-dark';
        default: return 'badge bg-secondary';
    }
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'sent': return 'badge bg-primary';
        case 'delivered': return 'badge bg-info text-dark';
        case 'success': return 'badge bg-success';
        case 'failed': return 'badge bg-danger';
        default: return 'badge bg-secondary';
    }
};

onMounted(() => {
    fetchTrucks();
    fetchCommands();

    // Poll for updates every 10 seconds
    setInterval(fetchCommands, 10000);
});
</script>
