<template>
    <div
        v-if="show"
        :class="['spinner-wrapper', { 'spinner-wrapper--fullscreen': fullscreen }]"
    >
        <div class="spinner"></div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { loadingStore } from '../stores/loadingStore';

const props = defineProps({
    fullscreen: {
        type: Boolean,
        default: true
    },
    show: {
        type: Boolean,
        default: undefined
    }
});

const show = computed(() => {
    return props.show !== undefined ? props.show : loadingStore.isLoading;
});
</script>

<style scoped>
 .spinner-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    width: 100%;
}

.spinner-wrapper--fullscreen {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.35);
    backdrop-filter: blur(3px);
    z-index: 9999;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes truckMove {
    0% {
        transform: translateX(-40px);
    }
    50% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(40px);
    }
}

@keyframes lineMove {
    0% {
        transform: translateX(0);
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        transform: translateX(300px);
        opacity: 0;
    }
}
</style>
