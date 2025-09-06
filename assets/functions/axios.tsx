import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://localhost:8000', // Remove https for development
    headers: {
        'Content-Type': 'application/json',
    },
    withCredentials: true, // Important for CORS
});

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

apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Token expired or invalid
            localStorage.removeItem('authToken');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default apiClient;
