import axios from 'axios';

// Creăm o instanță de Axios cu o configurație de bază
const apiClient = axios.create({
    baseURL: 'https://localhost:8000', // Setați URL-ul de bază al API-ului
    headers: {
        'Content-Type': 'application/json',
    },
});

// Interceptor pentru a adăuga automat token-ul de autentificare la fiecare cerere
apiClient.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('authToken');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default apiClient;
