/**
 * HomeRedirect Component - Role-based Home Redirect
 * ==================================================
 *
 * This component redirects users to the appropriate home page based on their role:
 * - Admin users -> /dashboard
 * - Employee users -> /my-assets
 *
 * This matches Laravel's behavior where different roles see different home pages.
 */

import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function HomeRedirect() {
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

    // If not logged in, redirect to login
    if (!user) {
        return <Navigate to="/login" replace />;
    }

    // Redirect based on user role
    if (user.role === 'admin') {
        return <Navigate to="/dashboard" replace />;
    } else {
        return <Navigate to="/my-assets" replace />;
    }
}

export default HomeRedirect;
