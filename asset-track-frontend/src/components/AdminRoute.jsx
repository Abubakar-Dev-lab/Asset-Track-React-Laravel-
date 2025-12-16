/**
 * AdminRoute Component - Protects Admin-Only Routes
 * ==================================================
 *
 * This component checks if the logged-in user is an admin.
 * If not, it redirects them to their appropriate home page.
 *
 * HOW IT WORKS:
 * 1. Checks if user has admin role
 * 2. If admin: Shows the protected page (children)
 * 3. If NOT admin: Redirects to /my-assets
 */

import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function AdminRoute({ children }) {
    const { user, loading, isAuthenticated } = useAuth();

    // Show loading while checking auth OR while user data is still being fetched
    // This prevents the race condition where loading is false but user is still null
    if (loading || (isAuthenticated && !user)) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading...</p>
            </div>
        );
    }

    // If user is not authenticated, let PrivateRoute handle the redirect to login
    // If user is authenticated but not admin, redirect to my-assets
    if (!user || user.role !== 'admin') {
        return <Navigate to="/my-assets" replace />;
    }

    // User is admin, show the protected content
    return children;
}

export default AdminRoute;
