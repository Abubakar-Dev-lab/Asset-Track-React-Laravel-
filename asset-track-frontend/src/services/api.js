/**
 * API Service - The Bridge Between React and Laravel
 * ==================================================
 *
 * This file is the MOST IMPORTANT file for understanding how React connects to Laravel.
 *
 * WHAT THIS FILE DOES:
 * 1. Creates an axios instance with a base URL pointing to Laravel
 * 2. Automatically attaches the authentication token to every request
 * 3. Handles token expiration (401 errors) by logging out the user
 *
 * HOW IT WORKS:
 * - When you login, Laravel gives you a token (like a VIP pass)
 * - We store this token in localStorage (browser storage)
 * - Every time we make an API request, we attach this token
 * - Laravel checks this token to know who you are
 */

import axios from 'axios';

// =============================================================================
// STEP 1: Create axios instance with base URL
// =============================================================================
// This is like setting the address of your Laravel server
// All requests will automatically go to this URL + the endpoint
// Example: api.get('/users') will actually call http://127.0.0.1:8000/api/users

const api = axios.create({
    // The base URL of your Laravel API
    // Change this to your production URL when deploying
    baseURL: 'http://127.0.0.1:8000/api',

    // Headers that will be sent with every request
    headers: {
        'Content-Type': 'application/json',  // We're sending JSON data
        'Accept': 'application/json',         // We expect JSON responses
    },
});


// =============================================================================
// STEP 2: Request Interceptor - Runs BEFORE every request is sent
// =============================================================================
// This is like a security guard that checks your VIP pass before letting you in
// It automatically adds the token to every request so you don't have to do it manually

api.interceptors.request.use(
    (config) => {
        // Get the token from localStorage (where we saved it after login)
        const token = localStorage.getItem('token');

        // If we have a token, add it to the request headers
        // This is how Laravel knows who is making the request
        if (token) {
            // "Bearer" is a standard prefix for token-based authentication
            // Laravel Sanctum expects this format: "Bearer your_token_here"
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;  // Continue with the request
    },
    (error) => {
        // If something goes wrong before the request is sent
        return Promise.reject(error);
    }
);


// =============================================================================
// STEP 3: Response Interceptor - Runs AFTER every response is received
// =============================================================================
// This handles errors globally, especially 401 (Unauthorized) errors
// If the token is expired or invalid, it will automatically log out the user

api.interceptors.response.use(
    (response) => {
        // If the request was successful, just return the response
        return response;
    },
    (error) => {
        // If we get a 401 error, it means the token is invalid or expired
        if (error.response && error.response.status === 401) {
            // Clear the invalid token from localStorage
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            // Redirect to login page
            // window.location.href will force a full page reload
            window.location.href = '/login';
        }

        // For other errors, just pass them through so components can handle them
        return Promise.reject(error);
    }
);


// =============================================================================
// STEP 4: Export the configured axios instance
// =============================================================================
// Now other files can import this and use it like:
// import api from './services/api';
// api.get('/users')  - GET request to /api/users
// api.post('/login', data)  - POST request to /api/login with data

export default api;


// =============================================================================
// BONUS: Helper functions for common API calls
// =============================================================================
// These make it even easier to call the API from your components

/**
 * AUTH ENDPOINTS
 * These handle login, logout, and getting current user info
 */
export const authAPI = {
    // Login - sends email and password, returns token
    login: (email, password) => api.post('/auth/login', { email, password }),

    // Register - creates new user account
    register: (data) => api.post('/auth/register', data),

    // Logout - invalidates the token on the server
    logout: () => api.post('/auth/logout'),

    // Get current user info
    getUser: () => api.get('/auth/user'),
};

/**
 * DASHBOARD ENDPOINT
 * Gets statistics for the dashboard
 */
export const dashboardAPI = {
    getStats: () => api.get('/dashboard'),
};

/**
 * ASSETS ENDPOINTS
 * CRUD operations for assets
 */
export const assetsAPI = {
    // Get all assets (with optional filters)
    getAll: (params = {}) => api.get('/assets', { params }),

    // Get single asset by ID
    getOne: (id) => api.get(`/assets/${id}`),

    // Create new asset
    create: (data) => api.post('/assets', data),

    // Update existing asset
    update: (id, data) => api.put(`/assets/${id}`, data),

    // Delete asset
    delete: (id) => api.delete(`/assets/${id}`),

    // Get only available assets (for assignment dropdown)
    getAvailable: () => api.get('/assets-available'),

    // Assign asset to user
    assign: (assetId, userId, notes = '') => api.post(`/assets/${assetId}/assign`, {
        user_id: userId,
        notes
    }),

    // Return asset (unassign)
    return: (assetId, notes = '') => api.post(`/assets/${assetId}/return`, { notes }),
};

/**
 * CATEGORIES ENDPOINTS
 */
export const categoriesAPI = {
    getAll: (params = {}) => api.get('/categories', { params }),
    getOne: (id) => api.get(`/categories/${id}`),
    create: (data) => api.post('/categories', data),
    update: (id, data) => api.put(`/categories/${id}`, data),
    delete: (id) => api.delete(`/categories/${id}`),
};

/**
 * USERS ENDPOINTS
 */
export const usersAPI = {
    getAll: (params = {}) => api.get('/users', { params }),
    getOne: (id) => api.get(`/users/${id}`),
    create: (data) => api.post('/users', data),
    update: (id, data) => api.put(`/users/${id}`, data),
    delete: (id) => api.delete(`/users/${id}`),
    getEmployees: () => api.get('/employees'),  // For dropdowns
};

/**
 * ASSIGNMENTS ENDPOINTS
 */
export const assignmentsAPI = {
    getAll: (params = {}) => api.get('/assignments', { params }),
    getOne: (id) => api.get(`/assignments/${id}`),
    getRecent: () => api.get('/assignments/recent'),
};
