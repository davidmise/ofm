<template>
    <div class="gps-devices-management">
        <div class="mb-2">
            <h2>GPS Devices Management</h2>
        </div>
        <div class="table-page-header">
            <div class="table-page-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    @input="handleSearch"
                    placeholder="Search devices by model, IMEI, or serial..."
                >
            </div>
            <div class="table-page-actions">
                <button class="btn btn-primary" @click="showCreateModal">
                    <i class="bi bi-plus-lg"></i> Add GPS Device
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <LoadingSpinner v-if="loading" :fullscreen="false" :show="true" />

        <div v-else class="card">
            <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S/N</th>
                            <th>Model</th>
                            <th>IMEI</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(device, index) in devices" :key="device.id">
                            <td><strong>{{ getSerialNumber(index) }}</strong></td>
                            <td>{{ device.model }}</td>
                            <td>{{ device.imei }}</td>
                            <td>{{ device.serial_number }}</td>
                            <td>
                                <span :class="getStatusBadgeClass(device.status)">
                                    {{ device.status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info me-2" @click="editDevice(device)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteDevice(device.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="devices.length === 0">
                            <td colspan="6" class="text-center">No GPS devices found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination with page numbers -->
            <div v-if="pagination.last_page > 1" class="mt-4 d-flex justify-content-between align-items-center">
                <div>
                    <label class="me-2">Items per page:</label>
                    <select v-model="perPage" @change="changePerPage" class="form-select form-select-sm d-inline-block" style="width: auto;">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <button class="page-link" @click="fetchDevices(1)"><i class="bi bi-chevron-double-left"></i></button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <button class="page-link" @click="fetchDevices(pagination.current_page - 1)">Previous</button>
                        </li>
                        <li v-for="page in getPageNumbers()" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                            <button class="page-link" @click="fetchDevices(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="fetchDevices(pagination.current_page + 1)">Next</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="fetchDevices(pagination.last_page)"><i class="bi bi-chevron-double-right"></i></button>
                        </li>
                    </ul>
                </nav>
                <div class="text-muted small">
                    Page {{ pagination.current_page }} of {{ pagination.last_page }} ({{ pagination.total }} total)
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ isEditing ? 'Edit GPS Device' : 'Add GPS Device' }}</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveDevice">
                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <input v-model="form.model" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">IMEI</label>
                            <input v-model="form.imei" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Serial Number</label>
                            <input v-model="form.serial_number" type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-select">
                                <option value="active">Active</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
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
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const devices = ref([]);
const pagination = ref({});
const showModal = ref(false);
const loading = ref(false);
const searchQuery = ref('');
const perPage = ref('10');
let searchTimeout = null;
const isEditing = ref(false);
const form = reactive({
    id: null,
    model: '',
    imei: '',
    serial_number: '',
    status: 'active'
});

const fetchDevices = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: perPage.value };
        if (searchQuery.value) {
            params.search = searchQuery.value;
        }
        const response = await axios.get('/api/gps-devices', { params });
        devices.value = response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching GPS devices:', error);
    } finally {
        loading.value = false;
    }
};

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        fetchDevices(1);
    }, 300);
};

const changePerPage = () => {
    fetchDevices(1);
};

const getPageNumbers = () => {
    const pages = [];
    const maxPages = 5;
    let startPage = Math.max(1, pagination.value.current_page - Math.floor(maxPages / 2));
    let endPage = Math.min(pagination.value.last_page, startPage + maxPages - 1);
    if (endPage - startPage < maxPages - 1) {
        startPage = Math.max(1, endPage - maxPages + 1);
    }
    for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
    }
    return pages;
};

const getSerialNumber = (index) => {
    return ((pagination.value.current_page - 1) * parseInt(perPage.value)) + (index + 1);
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'active': return 'badge bg-success';
        case 'maintenance': return 'badge bg-warning text-dark';
        case 'inactive': return 'badge bg-secondary';
        default: return 'badge bg-secondary';
    }
};

const showCreateModal = () => {
    isEditing.value = false;
    resetForm();
    showModal.value = true;
};

const editDevice = (device) => {
    isEditing.value = true;
    Object.assign(form, device);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    Object.assign(form, {
        id: null,
        model: '',
        imei: '',
        serial_number: '',
        status: 'active'
    });
};

const saveDevice = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/gps-devices/${form.id}`, form);
        } else {
            await axios.post('/api/gps-devices', form);
        }
        closeModal();
        fetchDevices(pagination.value.current_page);
    } catch (error) {
        console.error('Error saving GPS device:', error);
        alert('Failed to save GPS device. Please check inputs.');
    }
};

const deleteDevice = async (id) => {
    if (!confirm('Are you sure you want to delete this GPS device?')) return;

    try {
        await axios.delete(`/api/gps-devices/${id}`);
        fetchDevices(pagination.value.current_page);
    } catch (error) {
        console.error('Error deleting GPS device:', error);
    }
};

onMounted(() => {
    fetchDevices();
});
</script>
