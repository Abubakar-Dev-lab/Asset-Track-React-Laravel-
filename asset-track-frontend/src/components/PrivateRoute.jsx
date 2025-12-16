/**
 * PrivateRoute Component - Protects Routes from Unauthenticated Users
 * ====================================================================
 *
 * This component is a "guard" that checks if user is logged in before
 * showing protected pages.
 *
 * HOW IT WORKS:
 * 1. It checks if user is authenticated using AuthContext
 * 2. If authenticated: Shows the protected page (children)
 * 3. If NOT authenticated: Redirects to login page
 *
 * KEY CONCEPTS:
 * - Navigate: Component that redirects to another page
 * - Conditional rendering: Different output based on auth state
 * - Loading state: Shows spinner while checking authentication
 */

import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function PrivateRoute({ children }) {
    // Get auth state from AuthContext
    const { isAuthenticated, loading } = useAuth();

    // =========================================================================
    // LOADING STATE
    // =========================================================================
    // While we're checking if the user is authenticated (checking token validity),
    // show a loading spinner. This prevents a flash of the login page.

    if (loading) {
        return (
            <div className="loading-container">
                <div className="loading-spinner"></div>
                <p>Loading...</p>
            </div>
        );
    }

    // =========================================================================
    // NOT AUTHENTICATED
    // =========================================================================
    // If user is not logged in, redirect them to the login page
    // The "replace" prop replaces the current entry in browser history
    // So when user clicks "back", they won't come back to this protected route

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    // =========================================================================
    // AUTHENTICATED
    // =========================================================================
    // User is logged in, show the protected content
    // "children" is whatever component is wrapped inside PrivateRoute

    return children;
}

export default PrivateRoute;


// =============================================================================
// HOW TO USE THIS COMPONENT:
// =============================================================================
//
// In your routes (App.jsx), wrap protected components:
//
// <Route
//   path="/dashboard"
//   element={
//     <PrivateRoute>
//       <Dashboard />
//     </PrivateRoute>
//   }
// />
//
// Now /dashboard will only be accessible if user is logged in.
// If not logged in, they'll be redirected to /login automatically.
