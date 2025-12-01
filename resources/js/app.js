import './bootstrap';
import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import ThemeService from './services/ThemeService';

// Import custom CSS
import '../css/app.css';

// Initialize theme on app load
ThemeService.init();

createApp(App).use(router).mount('#app');
