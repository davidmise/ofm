import axios from 'axios';
import ApiService from './services/ApiService';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
