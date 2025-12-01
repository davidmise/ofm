<template>
    <div class="system-settings">
        <div class="settings-grid">
            <!-- Theme Settings -->
            <div class="settings-card">
                <div class="card-icon">
                    <i class="bi bi-palette"></i>
                </div>
                <h3>Theme Settings</h3>
                <p>Configure application theme and appearance settings.</p>
                <button class="btn btn-outline-primary" disabled>
                    Coming Soon
                </button>
            </div>

            <!-- Backup & Restore -->
            <div class="settings-card">
                <div class="card-icon">
                    <i class="bi bi-cloud-upload"></i>
                </div>
                <h3>Backup & Restore</h3>
                <p>Manage database backups and restore points.</p>
                <button class="btn btn-outline-primary" disabled>
                    Coming Soon
                </button>
            </div>

            <!-- Email Settings -->
            <div class="settings-card">
                <div class="card-icon">
                    <i class="bi bi-envelope"></i>
                </div>
                <h3>Email Settings</h3>
                <p>Configure SMTP settings and email notifications.</p>
                <button class="btn btn-outline-primary" disabled>
                    Coming Soon
                </button>
            </div>

            <!-- System Logs -->
            <div class="settings-card">
                <div class="card-icon">
                    <i class="bi bi-file-text"></i>
                </div>
                <h3>System Logs</h3>
                <p>View and manage application logs and error reports.</p>
                <button class="btn btn-outline-primary" disabled>
                    Coming Soon
                </button>
            </div>

            <!-- API Settings -->
            <div class="settings-card">
                <div class="card-icon">
                    <i class="bi bi-code-square"></i>
                </div>
                <h3>API Settings</h3>
                <p>Configure API keys and integration settings.</p>
                <button class="btn btn-outline-primary" disabled>
                    Coming Soon
                </button>
            </div>

            <!-- Security Settings -->
            <div class="settings-card">
                <div class="card-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3>Security Settings</h3>
                <p>Manage security policies and access controls.</p>
                <button class="btn btn-outline-primary" disabled>
                    Coming Soon
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
// Placeholder for system settings
</script>

<style scoped>
.system-settings {
    padding: 1rem 0;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.settings-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    transition: all var(--transition-base);
}

.settings-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.card-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 1rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.settings-card h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.settings-card p {
    font-size: 0.9375rem;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}
</style>
