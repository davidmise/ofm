<template>
    <div class="drivers-management">
        <div class="mb-2">
            <h2>Drivers Management</h2>
        </div>
        <div class="table-page-header">
            <div class="table-page-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    @input="handleSearch"
                    placeholder="Search drivers by name, phone, or license..."
                >
            </div>
            <div class="table-page-actions">
                <button class="btn btn-primary" @click="showCreateModal">
                    <i class="bi bi-plus-lg"></i> Add Driver
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
                            <th>Name</th>
                            <th>License Number</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Performance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(driver, index) in drivers" :key="driver.id">
                            <td><strong>{{ getSerialNumber(index) }}</strong></td>
                            <td>{{ driver.name }}</td>
                            <td>{{ driver.license_number }}</td>
                            <td>{{ driver.phone }}</td>
                            <td>
                                <span :class="getStatusBadgeClass(driver.status)">
                                    {{ driver.status }}
                                </span>
                            </td>
                            <td>{{ driver.performance_score }}%</td>
                            <td>
                                <button class="btn btn-sm btn-info me-2" @click="editDriver(driver)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteDriver(driver.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="drivers.length === 0">
                            <td colspan="7" class="text-center">No drivers found.</td>
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
                            <button class="page-link" @click="fetchDrivers(1)"><i class="bi bi-chevron-double-left"></i></button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <button class="page-link" @click="fetchDrivers(pagination.current_page - 1)">Previous</button>
                        </li>
                        <li v-for="page in getPageNumbers()" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                            <button class="page-link" @click="fetchDrivers(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="fetchDrivers(pagination.current_page + 1)">Next</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="fetchDrivers(pagination.last_page)"><i class="bi bi-chevron-double-right"></i></button>
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
                    <h5 class="modal-title">{{ isEditing ? 'Edit Driver' : 'Add Driver' }}</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveDriver">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input v-model="form.name" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">License Number</label>
                            <input v-model="form.license_number" type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input v-model="form.phone" type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-select">
                                <option value="active">Active</option>
                                <option value="on_leave">On Leave</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Performance Score</label>
                            <input v-model="form.performance_score" type="number" step="0.01" min="0" max="100" class="form-control">
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

const drivers = ref([]);
const pagination = ref({});
const showModal = ref(false);
const loading = ref(false);
const searchQuery = ref('');
const perPage = ref('10');
let searchTimeout = null;
const isEditing = ref(false);
const form = reactive({
    id: null,
    name: '',
    license_number: '',
    phone: '',
    status: 'active',
    performance_score: 100.00
});

const fetchDrivers = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: perPage.value };
        if (searchQuery.value) {
            params.search = searchQuery.value;
        }
        const response = await axios.get('/api/drivers', { params });
        drivers.value = response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching drivers:', error);
    } finally {
        loading.value = false;
    }
};

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        fetchDrivers(1);
    }, 300);
};

const changePerPage = () => {
    fetchDrivers(1);
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
        case 'on_leave': return 'badge bg-warning text-dark';
        case 'inactive': return 'badge bg-secondary';
        default: return 'badge bg-secondary';
    }
};

const showCreateModal = () => {
    isEditing.value = false;
    resetForm();
    showModal.value = true;
};

const editDriver = (driver) => {
    isEditing.value = true;
    Object.assign(form, driver);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    Object.assign(form, {
        id: null,
        name: '',
        license_number: '',
        phone: '',
        status: 'active',
        performance_score: 100.00
    });
};

const saveDriver = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/drivers/${form.id}`, form);
        } else {
            await axios.post('/api/drivers', form);
        }
        closeModal();
        fetchDrivers(pagination.value.current_page);
    } catch (error) {
        console.error('Error saving driver:', error);
        alert('Failed to save driver. Please check inputs.');
    }
};

const deleteDriver = async (id) => {
    if (!confirm('Are you sure you want to delete this driver?')) return;

    try {
        await axios.delete(`/api/drivers/${id}`);
        fetchDrivers(pagination.value.current_page);
    } catch (error) {
        console.error('Error deleting driver:', error);
    }
};

onMounted(() => {
    fetchDrivers();
});
</script>
