import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../views/Dashboard.vue';
import Trucks from '../views/Trucks.vue';
import Drivers from '../views/Drivers.vue';
import SimCards from '../views/SimCards.vue';
import GpsDevices from '../views/GpsDevices.vue';
import Assignments from '../views/Assignments.vue';
import CommandCenter from '../views/CommandCenter.vue';
import Tracking from '../views/Tracking.vue';
import Analytics from '../views/Analytics.vue';
import Login from '../views/Login.vue';
import MainLayout from '../layouts/MainLayout.vue';

const routes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { guest: true }
    },
    {
        path: '/',
        component: MainLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: Dashboard
            },
            {
                path: 'trucks',
                name: 'Trucks',
                component: Trucks
            },
            {
                path: 'drivers',
                name: 'Drivers',
                component: Drivers
            },
            {
                path: 'sim-cards',
                name: 'SimCards',
                component: SimCards
            },
            {
                path: 'gps-devices',
                name: 'GpsDevices',
                component: GpsDevices
            },
            {
                path: 'assignments',
                name: 'Assignments',
                component: Assignments
            },
            {
                path: 'command-center',
                name: 'CommandCenter',
                component: CommandCenter
            },
            {
                path: 'tracking',
                name: 'Tracking',
                component: Tracking
            },
            {
                path: 'analytics',
                name: 'Analytics',
                component: Analytics
            },
            {
                path: 'settings',
                name: 'Settings',
                component: () => import('../views/Settings.vue')
            },
            // Add more routes here
        ]
    }
];

import ApiService from '../services/ApiService';

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation Guard
router.beforeEach((to, from, next) => {
    const isAuthenticated = ApiService.isAuthenticated();

    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else if (to.path === '/login' && isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

export default router;
