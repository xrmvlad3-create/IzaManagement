// assets/functions/axios.tsx
import axios, { AxiosRequestConfig, Method } from 'axios';

const apiClient = axios.create({
    baseURL: 'https://localhost:8000', // URL-ul complet al backend-ului
    withCredentials: true,
});

// Interceptor pentru a adăuga token-ul de autorizare la fiecare cerere
apiClient.interceptors.request.use(config => {
    const token = localStorage.getItem('authToken');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export const request = (
    method: Method,
    url: string,
    data?: unknown,
    config?: AxiosRequestConfig
) => {
    return apiClient({
        method,
        url,
        data,
        ...config,
    });
};

export default apiClient;
