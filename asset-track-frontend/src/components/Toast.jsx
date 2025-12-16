/**
 * Toast Component - Displays Toast Notifications
 * ================================================
 *
 * Renders all active toast notifications from ToastContext.
 * Toasts auto-dismiss and can also be manually closed.
 */

import { useToast } from '../context/ToastContext';

function Toast() {
    const { toasts, removeToast } = useToast();

    // Icon based on toast type
    const getIcon = (type) => {
        switch (type) {
            case 'success': return '✓';
            case 'error': return '✕';
            case 'warning': return '⚠';
            case 'info': return 'ℹ';
            default: return 'ℹ';
        }
    };

    if (toasts.length === 0) return null;

    return (
        <div className="toast-container">
            {toasts.map((toast) => (
                <div key={toast.id} className={`toast toast-${toast.type}`}>
                    <span className="toast-icon">{getIcon(toast.type)}</span>
                    <span className="toast-message">{toast.message}</span>
                    <button
                        className="toast-close"
                        onClick={() => removeToast(toast.id)}
                        aria-label="Close"
                    >
                        ×
                    </button>
                </div>
            ))}
        </div>
    );
}

export default Toast;
