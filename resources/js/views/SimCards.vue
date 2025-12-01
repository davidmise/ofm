<template>
    <div class="simcards-management">
        <div class="mb-2">
            <h2>SIM Cards Management</h2>
        </div>
        <div class="table-page-header">
            <div class="table-page-search">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    v-model="searchQuery"
                    @input="handleSearch"
                    placeholder="Search SIM cards by ICCID or phone..."
                >
            </div>
            <div class="table-page-actions">
                <button class="btn btn-primary" @click="showCreateModal">
                    <i class="bi bi-plus-lg"></i> Add SIM Card
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
                            <th>ICCID</th>
                            <th>Phone Number</th>
                            <th>Network</th>
                            <th>Expiry Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sim, index) in simCards" :key="sim.id">
                            <td><strong>{{ getSerialNumber(index) }}</strong></td>
                            <td>{{ sim.iccid }}</td>
                            <td>{{ sim.phone_number }}</td>
                            <td>{{ sim.network_provider }}</td>
                            <td>{{ sim.expiry_date }}</td>
                            <td>{{ sim.sim_type }}</td>
                            <td>
                                <span :class="getStatusBadgeClass(sim.status)">
                                    {{ sim.status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info me-2" @click="editSim(sim)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteSim(sim.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="simCards.length === 0">
                            <td colspan="8" class="text-center">No SIM cards found.</td>
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
                            <button class="page-link" @click="fetchSimCards(1)"><i class="bi bi-chevron-double-left"></i></button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <button class="page-link" @click="fetchSimCards(pagination.current_page - 1)">Previous</button>
                        </li>
                        <li v-for="page in getPageNumbers()" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                            <button class="page-link" @click="fetchSimCards(page)">{{ page }}</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="fetchSimCards(pagination.current_page + 1)">Next</button>
                        </li>
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <button class="page-link" @click="fetchSimCards(pagination.last_page)"><i class="bi bi-chevron-double-right"></i></button>
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
                    <h5 class="modal-title">{{ isEditing ? 'Edit SIM Card' : 'Add SIM Card' }}</h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveSim">
                        <div class="mb-3">
                            <label class="form-label">ICCID</label>
                            <input v-model="form.iccid" type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input v-model="form.phone_number" type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Network Provider</label>
                            <input v-model="form.network_provider" type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input v-model="form.expiry_date" type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SIM Type</label>
                            <select v-model="form.sim_type" class="form-select">
                                <option value="data">Data</option>
                                <option value="voice">Voice</option>
                                <option value="mixed">Mixed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select v-model="form.status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="expiring">Expiring</option>
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

const simCards = ref([]);
const pagination = ref({});
const showModal = ref(false);
const loading = ref(false);
const searchQuery = ref('');
const perPage = ref('10');
let searchTimeout = null;
const isEditing = ref(false);
const form = reactive({
    id: null,
    iccid: '',
    phone_number: '',
    network_provider: '',
    expiry_date: '',
    sim_type: 'data',
    status: 'active'
});

const fetchSimCards = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: perPage.value };
        if (searchQuery.value) {
            params.search = searchQuery.value;
        }
        const response = await axios.get('/api/sim-cards', { params });
        simCards.value = response.data.data;
        pagination.value = response.data;
    } catch (error) {
        console.error('Error fetching SIM cards:', error);
    } finally {
        loading.value = false;
    }
};

const handleSearch = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        fetchSimCards(1);
    }, 300);
};

const changePerPage = () => {
    fetchSimCards(1);
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
        case 'expiring': return 'badge bg-warning text-dark';
        case 'inactive': return 'badge bg-secondary';
        default: return 'badge bg-secondary';
    }
};

const showCreateModal = () => {
    isEditing.value = false;
    resetForm();
    showModal.value = true;
};

const editSim = (sim) => {
    isEditing.value = true;
    Object.assign(form, sim);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const resetForm = () => {
    Object.assign(form, {
        id: null,
        iccid: '',
        phone_number: '',
        network_provider: '',
        expiry_date: '',
        sim_type: 'data',
        status: 'active'
    });
};

const saveSim = async () => {
    try {
        if (isEditing.value) {
            await axios.put(`/api/sim-cards/${form.id}`, form);
        } else {
            await axios.post('/api/sim-cards', form);
        }
        closeModal();
        fetchSimCards(pagination.value.current_page);
    } catch (error) {
        console.error('Error saving SIM card:', error);
        alert('Failed to save SIM card. Please check inputs.');
    }
};

const deleteSim = async (id) => {
    if (!confirm('Are you sure you want to delete this SIM card?')) return;

    try {
        await axios.delete(`/api/sim-cards/${id}`);
        fetchSimCards(pagination.value.current_page);
    } catch (error) {
        console.error('Error deleting SIM card:', error);
    }
};

onMounted(() => {
    fetchSimCards();
});
</script>
