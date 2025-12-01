<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Real-time Fleet Tracking</h2>
        <div class="btn-group">
            <button class="btn btn-outline-secondary" @click="refreshLocations">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div id="map" style="height: 600px; width: 100%;"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix Leaflet icon issue
import icon from 'leaflet/dist/images/marker-icon.png';
import iconShadow from 'leaflet/dist/images/marker-shadow.png';

let DefaultIcon = L.icon({
    iconUrl: icon,
    shadowUrl: iconShadow,
    iconSize: [25, 41],
    iconAnchor: [12, 41]
});

L.Marker.prototype.options.icon = DefaultIcon;

const map = ref(null);
const markers = ref({});
const trucks = ref([]);

const initMap = () => {
    // Default to Tanzania center (approx)
    map.value = L.map('map').setView([-6.369028, 34.888822], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map.value);
};

const fetchLocations = async () => {
    try {
        const response = await axios.get('/api/trucks?per_page=100');
        trucks.value = response.data.data;
        updateMarkers();
    } catch (error) {
        console.error('Error fetching truck locations:', error);
    }
};

const updateMarkers = () => {
    trucks.value.forEach(truck => {
        // Mock coordinates for now since we don't have real GPS data yet
        // In a real app, these would come from the truck/device relationship
        const lat = -6.369028 + (Math.random() - 0.5) * 5;
        const lng = 34.888822 + (Math.random() - 0.5) * 5;

        if (markers.value[truck.id]) {
            markers.value[truck.id].setLatLng([lat, lng]);
            markers.value[truck.id].setPopupContent(getPopupContent(truck));
        } else {
            const marker = L.marker([lat, lng])
                .addTo(map.value)
                .bindPopup(getPopupContent(truck));
            markers.value[truck.id] = marker;
        }
    });
};

const getPopupContent = (truck) => {
    return `
        <div>
            <h6>${truck.plate_number}</h6>
            <p class="mb-1"><strong>Driver:</strong> ${truck.driver ? truck.driver.name : 'Unassigned'}</p>
            <p class="mb-1"><strong>Status:</strong> ${truck.status}</p>
            <p class="mb-0"><strong>Speed:</strong> ${Math.floor(Math.random() * 80)} km/h</p>
        </div>
    `;
};

const refreshLocations = () => {
    fetchLocations();
};

let intervalId;

onMounted(() => {
    initMap();
    fetchLocations();
    // Auto-refresh every 30 seconds
    intervalId = setInterval(fetchLocations, 30000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
    if (map.value) map.value.remove();
});
</script>

<style scoped>
/* Ensure map container has height */
#map {
    z-index: 1;
}
</style>
