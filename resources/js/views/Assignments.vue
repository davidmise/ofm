<template>
    <div class="assignments-page">
        <div class="mb-2">
            <h2>SIM Assignments</h2>
        </div>
        <div class="table-page-header">
            <div class="table-page-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search assignments by SIM, truck, or status..."
                >
            </div>
            <div class="table-page-actions">
                <button class="btn btn-primary" @click="showCreateModal">
                    <i class="bi bi-plus-lg"></i> New Assignment
                </button>
            </div>
        </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SIM Card</th>
                            <th>GPS Device</th>
                            <th>Truck</th>
                            <th>Assigned At</th>
                            <th>Ended At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="assignment in filteredAssignments" :key="assignment.id">
                            <td>{{ assignment.sim ? assignment.sim.iccid : 'N/A' }}</td>
                            <td>{{ assignment.device ? assignment.device.imei : 'N/A' }}</td>
                            <td>{{ assignment.truck ? assignment.truck.plate_number : 'N/A' }}</td>
                            <td>{{ formatDate(assignment.assigned_at) }}</td>
                            <td>{{ assignment.ended_at ? formatDate(assignment.ended_at) : '-' }}</td>
                            <td>
                                <span :class="getStatusBadgeClass(assignment.status)">
                                    {{ assignment.status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info me-2" @click="editAssignment(assignment)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteAssignment(assignment.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="assignments.length === 0">
                            <td colspan="7" class="text-center">No assignments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav v-if="pagination.last_page > 1" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                        <button class="page-link" @click="fetchAssignments(pagination.current_page - 1)">Previous</button>
                    </li>
                    <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                        <button class="page-link" @click="fetchAssignments(pagination.current_page + 1)">Next</button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ isEditing ? 'Edit Assignment' : 'New Assignment' }}</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveAssignment">
                        <div class="mb-3">
                            <label class="form-label">SIM Card</label>
                            <select v-model="form.sim_id" class="form-select" required>
                                <option v-for="sim in availableSims" :key="sim.id" :value="sim.id">
                                    {{ sim.iccid }} ({{ sim.phone_number }})
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">GPS Device</label>
                            <select v-model="form.device_id" class="form-select">
                                <option :value="null">None</option>
                                <option v-for="device in availableDevices" :key="device.id" :value="device.id">
                                    {{ device.imei }} ({{ device.model }})
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Truck</label>
                            <select v-model="form.truck_id" class="form-select">
                                <option :value="null">None</option>
                                <option v-for="truck in availableTrucks" :key="truck.id" :value="truck.id">
                                    {{ truck.plate_number }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assigned Date</label>
                            <input v-model="form.assigned_at" type="datetime-local" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ended Date</label>
                            <input v-model="form.ended_at" type="datetime-local" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-select">
                                <option value="active">Active</option>
                                <option value="history">History</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" @click="closeModal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';

const assignments = ref([]);
const availableSims = ref([]);
const availableDevices = ref([]);
const availableTrucks = ref([]);
const pagination = ref({});
const showModal = ref(false);
const isEditing = ref(false);
const searchQuery = ref('');
const form = reactive({
    id: null,
    sim_id: null,
    device_id: null,
    truck_id: null,
    assigned_at: '',
    ended_at: '',
    status: 'active'
});

const fetchAssignments = async (page = 1) => {
    try {
        const response = await axios.get(`/api/sim-assignments?page=${page}`);
        assignments.value = response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching assignments:', error);
    }
};

const filteredAssignments = computed(() => {
    const term = searchQuery.value.trim().toLowerCase();
    if (!term) return assignments.value;

    return assignments.value.filter((assignment) => {
        const values = [
            assignment.sim?.iccid,
            assignment.sim?.phone_number,
            assignment.device?.imei,
            assignment.device?.model,
            assignment.truck?.plate_number,
            assignment.status
        ];

        return values.some((value) => value?.toString().toLowerCase().includes(term));
    });
});

const fetchOptions = async () => {
    try {
        // In a real app, these should be separate endpoints returning available (unassigned) items
        // For MVP, we'll just fetch all
        const [simsRes, devicesRes, trucksRes] = await Promise.all([
            axios.get('/api/sim-cards?per_page=100'),
            axios.get('/api/gps-devices?per_page=100'),
            axios.get('/api/trucks?per_page=100')
        ]);
        availableSims.value = simsRes.data.data;
        availableDevices.value = devicesRes.data.data;
        availableTrucks.value = trucksRes.data.data;
    } catch (error) {
        console.error('Error fetching options:', error);
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString();
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'active': return 'badge bg-success';
        case 'history': return 'badge bg-secondary';
        default: return 'badge bg-secondary';
    }
};

const showCreateModal = () => {
    isEditing.value = false;
    resetForm();
    // Set default assigned_at to now
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    form.assigned_at = now.toISOString().slice(0, 16);

    showModal.value = true;
    fetchOptions();
};

const editAssignment = (assignment) => {
    isEditing.value = true;
    Object.assign(form, assignment);
    // Format dates for input type="datetime-local"
    if (form.assigned_at) form.assigned_at = new Date(form.assigned_at).toISOString().slice(0, 16);
    if (form.ended_at) form.ended_at = new Date(form.ended_at).toISOString().slice(0, 16);

    showModal.value = true;
    fetchOptions();
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    Object.assign(form, {
        id: null,
        sim_id: null,
        device_id: null,
        truck_id: null,
        assigned_at: '',
        ended_at: '',
        status: 'active'
    });
};

const saveAssignment = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/sim-assignments/${form.id}`, form);
        } else {
            await axios.post('/api/sim-assignments', form);
        }
        closeModal();
        fetchAssignments(pagination.value.current_page);
    } catch (error) {
        console.error('Error saving assignment:', error);
        alert('Failed to save assignment. Please check inputs.');
    }
};

const deleteAssignment = async (id) => {
    if (!confirm('Are you sure you want to delete this assignment?')) return;

    try {
        await axios.delete(`/api/sim-assignments/${id}`);
        fetchAssignments(pagination.value.current_page);
    } catch (error) {
        console.error('Error deleting assignment:', error);
    }
};

onMounted(() => {
    fetchAssignments();
});
</script>
