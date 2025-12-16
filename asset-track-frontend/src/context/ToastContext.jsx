/**
 * Toast Context - Global Toast Notifications
 * ===========================================
 *
 * Provides a way to show toast notifications from anywhere in the app.
 *
 * Usage:
 *   const { showToast } = useToast();
 *   showToast('Asset created successfully', 'success');
 *   showToast('Failed to delete', 'error');
 */

import { createContext, useContext, useState, useCallback } from 'react';

// Create context
const ToastContext = createContext(null);

// Toast Provider Component
export function ToastProvider({ children }) {
    // Array of toast messages
    const [toasts, setToasts] = useState([]);

    // Add a new toast
    const showToast = useCallback((message, type = 'info', duration = 4000) => {
        const id = Date.now();

        // Add toast to array
        setToasts(prev => [...prev, { id, message, type }]);

        // Auto-remove after duration
        setTimeout(() => {
            removeToast(id);
        }, duration);
    }, []);

    // Remove a toast by ID
    const removeToast = useCallback((id) => {
        setToasts(prev => prev.filter(toast => toast.id !== id));
    }, []);

    // Shortcut methods for different toast types
    const success = useCallback((message) => showToast(message, 'success'), [showToast]);
    const error = useCallback((message) => showToast(message, 'error'), [showToast]);
    const warning = useCallback((message) => showToast(message, 'warning'), [showToast]);
    const info = useCallback((message) => showToast(message, 'info'), [showToast]);

    const value = {
        toasts,
        showToast,
        removeToast,
        success,
        error,
        warning,
        info,
    };

    return (
        <ToastContext.Provider value={value}>
            {children}
        </ToastContext.Provider>
    );
}

// Custom hook to use toast
export function useToast() {
    const context = useContext(ToastContext);
    if (!context) {
        throw new Error('useToast must be used within a ToastProvider');
    }
    return context;
}
