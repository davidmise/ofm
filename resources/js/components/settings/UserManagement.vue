<template>
    <div class="user-management">
        <!-- Header with Search and Add Button -->
        <div class="management-header">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input 
                    type="text" 
                    v-model="searchQuery" 
                    @input="handleSearch"
                    placeholder="Search users by name, email, or role..."
                    class="search-input"
                >
            </div>
            <button @click="showAddModal" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add User
            </button>
        </div>

        <!-- Loading State -->
        <LoadingSpinner v-if="loading" :fullscreen="false" :show="true" />

        <!-- Users Table -->
        <div v-else class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td><strong>{{ user.name }}</strong></td>
                            <td>{{ user.email }}</td>
                            <td>
                                <span :class="['badge', getRoleBadge(user.role)]">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td>
                                <span :class="['badge', user.status === 'active' ? 'bg-success' : 'bg-secondary']">
                                    {{ user.status }}
                                </span>
                            </td>
                            <td>{{ formatDate(user.created_at) }}</td>
                            <td>
                                <button @click="showEditModal(user)" class="btn btn-sm btn-info me-2">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button @click="confirmDelete(user)" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="6" class="text-center">No users found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="pagination-container">
                <button 
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="btn btn-sm btn-outline-primary"
                >
                    Previous
                </button>
                <span class="pagination-info">
                    Page {{ pagination.current_page }} of {{ pagination.last_page }}
                </span>
                <button 
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="btn btn-sm btn-outline-primary"
                >
                    Next
                </button>
            </div>
        </div>

        <!-- Add/Edit User Modal -->
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>{{ isEdit ? 'Edit User' : 'Add New User' }}</h5>
                        <button @click="closeModal" class="btn-close">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveUser">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    v-model="formData.name" 
                                    class="form-control"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input 
                                    type="email" 
                                    v-model="formData.email" 
                                    class="form-control"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label>Password {{ isEdit ? '' : '*' }}</label>
                                <input 
                                    type="password" 
                                    v-model="formData.password" 
                                    class="form-control"
                                    :required="!isEdit"
                                >
                                <small v-if="isEdit" class="text-muted">Leave blank to keep current password</small>
                            </div>

                            <div class="form-group">
                                <label>Role <span class="text-danger">*</span></label>
                                <select v-model="formData.role" class="form-control" required>
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="tracking">Tracking</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select v-model="formData.status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div v-if="error" class="alert alert-danger">
                                {{ error }}
                            </div>

                            <div class="modal-footer">
                                <button type="button" @click="closeModal" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="saving">
                                    <span v-if="saving">Saving...</span>
                                    <span v-else>{{ isEdit ? 'Update' : 'Create' }} User</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Confirm Delete</h5>
                        <button @click="showDeleteModal = false" class="btn-close">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete user <strong>{{ userToDelete?.name }}</strong>?</p>
                        <p class="text-danger">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button @click="showDeleteModal = false" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button @click="deleteUser" class="btn btn-danger" :disabled="deleting">
                            <span v-if="deleting">Deleting...</span>
                            <span v-else>Delete User</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingSpinner from '../LoadingSpinner.vue';

const users = ref([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const searchQuery = ref('');
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEdit = ref(false);
const error = ref('');
const userToDelete = ref(null);

const formData = ref({
    name: '',
    email: '',
    password: '',
    role: 'manager',
    status: 'active'
});

const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0
});

let searchTimeout = null;

const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page };
        if (searchQuery.value) {
            params.search = searchQuery.value;
        }

        const response = await axios.get('/api/users', { params });
        users.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total
        };
    } catch (err) {
        console.error('Error fetching users:', err);
    } finally {
        loading.value = false;
    }
};

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchUsers(1);
    }, 300);
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchUsers(page);
    }
};

const showAddModal = () => {
    isEdit.value = false;
    formData.value = {
        name: '',
        email: '',
        password: '',
        role: 'manager',
        status: 'active'
    };
    error.value = '';
    showModal.value = true;
};

const showEditModal = (user) => {
    isEdit.value = true;
    formData.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: '',
        role: user.role,
        status: user.status
    };
    error.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    formData.value = {
        name: '',
        email: '',
        password: '',
        role: 'manager',
        status: 'active'
    };
    error.value = '';
};

const saveUser = async () => {
    saving.value = true;
    error.value = '';

    try {
        if (isEdit.value) {
            await axios.put(`/api/users/${formData.value.id}`, formData.value);
        } else {
            await axios.post('/api/users', formData.value);
        }
        
        closeModal();
        fetchUsers(pagination.value.current_page);
    } catch (err) {
        error.value = err.response?.data?.message || 'An error occurred';
    } finally {
        saving.value = false;
    }
};

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = async () => {
    deleting.value = true;
    try {
        await axios.delete(`/api/users/${userToDelete.value.id}`);
        showDeleteModal.value = false;
        fetchUsers(pagination.value.current_page);
    } catch (err) {
        alert(err.response?.data?.message || 'Error deleting user');
    } finally {
        deleting.value = false;
    }
};

const getRoleBadge = (role) => {
    const badges = {
        admin: 'bg-danger',
        manager: 'bg-primary',
        tracking: 'bg-info'
    };
    return badges[role] || 'bg-secondary';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

onMounted(() => {
    fetchUsers();
});
</script>

<style scoped>
.user-management {
    padding: 1rem 0;
}

.management-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

.search-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 0.9375rem;
    transition: all var(--transition-base);
}

.search-input:focus {
    outline: none;
    border-color: var(--primary);
}

.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-top: 1px solid var(--border-color);
}

.pagination-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--text-primary);
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    animation: fadeIn 0.2s;
}

.modal-dialog {
    width: 90%;
    max-width: 500px;
    animation: slideUp 0.3s;
}

.modal-content {
    background: var(--bg-primary);
    border-radius: 12px;
    box-shadow: var(--shadow-xl);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h5 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--text-secondary);
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    transition: all var(--transition-fast);
}

.btn-close:hover {
    background: var(--bg-tertiary);
    color: var(--text-primary);
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
