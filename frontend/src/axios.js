// src/axios.js
import axios from 'axios';
import { getActivePinia } from 'pinia';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true,
});

api.interceptors.request.use((config) => {
  const pinia = getActivePinia();
  if (pinia) {
    const userStore = pinia._s.get('user');
    if (userStore?.token) {
      config.headers.Authorization = `Bearer ${userStore.token}`;
    }
  }
  return config;
});

export default api;
