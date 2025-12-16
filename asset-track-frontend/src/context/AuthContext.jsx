/**
 * Authentication Context - Global State for User Authentication
 * =============================================================
 *
 * WHAT IS REACT CONTEXT?
 * Context is like a "global variable" that any component can access without
 * passing props down through every level of the component tree.
 *
 * WHY DO WE NEED THIS?
 * - Many components need to know if user is logged in (Navbar, PrivateRoute, etc.)
 * - Without context, we'd have to pass user data as props through EVERY component
 * - Context lets any component access auth state directly
 *
 * WHAT THIS FILE PROVIDES:
 * 1. user - The currently logged in user object (or null if not logged in)
 * 2. token - The authentication token
 * 3. login() - Function to log in a user
 * 4. logout() - Function to log out a user
 * 5. loading - Boolean to show if we're checking authentication status
 */

import { createContext, useContext, useState, useEffect } from 'react';
import { authAPI } from '../services/api';

// =============================================================================
// STEP 1: Create the Context
// =============================================================================
// This creates an empty context that will hold our auth state
// Think of it as creating an empty container

const AuthContext = createContext(null);


// =============================================================================
// STEP 2: Create the Provider Component
// =============================================================================
// The Provider is what "fills" the context with actual data
// Any component wrapped inside this Provider can access the auth state

// Helper function to safely get user from localStorage
const getStoredUser = () => {
    try {
        const savedUser = localStorage.getItem('user');
        if (savedUser) {
            return JSON.parse(savedUser);
        }
    } catch (e) {
        console.log('Failed to parse saved user');
    }
    return null;
};

export function AuthProvider({ children }) {
    // State to store the current user - initialize from localStorage immediately
    // This prevents the flash/redirect issue on page reload
    const [user, setUser] = useState(() => getStoredUser());

    // State to store the authentication token
    const [token, setToken] = useState(localStorage.getItem('token'));

    // Loading state - true while we're checking if user is already logged in
    const [loading, setLoading] = useState(true);


    // =========================================================================
    // STEP 3: Check if user is already logged in when app starts
    // =========================================================================
    // useEffect runs when the component mounts (when app loads)
    // We check if there's a token in localStorage and verify it's still valid

    useEffect(() => {
        const checkAuth = async () => {
            // Get token from localStorage
            const savedToken = localStorage.getItem('token');
            const savedUser = localStorage.getItem('user');

            if (savedToken) {
                // First, try to set user from localStorage for immediate display
                if (savedUser) {
                    try {
                        const parsedUser = JSON.parse(savedUser);
                        setUser(parsedUser);
                    } catch (e) {
                        console.log('Failed to parse saved user');
                    }
                }

                try {
                    // Try to get fresh user info from the API
                    // If token is valid, this will return user data
                    // If token is expired/invalid, this will throw a 401 error
                    const response = await authAPI.getUser();

                    // Token is valid, set the user
                    // Handle both response.data.data and response.data formats
                    const userData = response.data.data || response.data;
                    setUser(userData);
                    setToken(savedToken);

                    // Update localStorage with fresh user data
                    localStorage.setItem('user', JSON.stringify(userData));
                } catch (error) {
                    // Token is invalid or expired
                    // Clear everything and user will need to login again
                    console.log('Token invalid or expired, clearing auth state');
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    setUser(null);
                    setToken(null);
                }
            }

            // Done checking, set loading to false
            setLoading(false);
        };

        checkAuth();
    }, []);  // Empty array means this runs only once when component mounts


    // =========================================================================
    // STEP 4: Login Function
    // =========================================================================
    // This is called when user submits the login form
    // It sends credentials to Laravel and stores the token if successful

    const login = async (email, password) => {
        try {
            // Send login request to Laravel API
            const response = await authAPI.login(email, password);

            // Extract token and user from response
            // Laravel returns: { token: '...', user: {...} }
            // Handle both wrapped (user.data) and unwrapped (user) formats
            const { token: newToken, user: rawUser } = response.data;
            const userData = rawUser?.data || rawUser;

            // Save token to localStorage so it persists after page refresh
            localStorage.setItem('token', newToken);
            localStorage.setItem('user', JSON.stringify(userData));

            // Update state
            setToken(newToken);
            setUser(userData);

            // Return success with user data for role-based redirect
            return { success: true, user: userData };
        } catch (error) {
            // Login failed - wrong credentials or server error
            console.error('Login error:', error);

            // Return error message to display to user
            return {
                success: false,
                message: error.response?.data?.message || 'Login failed. Please try again.'
            };
        }
    };


    // =========================================================================
    // STEP 5: Register Function
    // =========================================================================
    // This is called when user submits the registration form
    // It sends user data to Laravel and stores the token if successful

    const register = async (name, email, password, password_confirmation) => {
        try {
            // Send registration request to Laravel API
            const response = await authAPI.register({
                name,
                email,
                password,
                password_confirmation
            });

            // Extract token and user from response
            // Laravel returns: { token: '...', user: {...} }
            // Handle both wrapped (user.data) and unwrapped (user) formats
            const { token: newToken, user: rawUser } = response.data;
            const userData = rawUser?.data || rawUser;

            // Save token to localStorage so it persists after page refresh
            localStorage.setItem('token', newToken);
            localStorage.setItem('user', JSON.stringify(userData));

            // Update state
            setToken(newToken);
            setUser(userData);

            // Return success with user data for role-based redirect
            return { success: true, user: userData };
        } catch (error) {
            // Registration failed - validation error or server error
            console.error('Register error:', error);

            // Return error message to display to user
            return {
                success: false,
                message: error.response?.data?.message || 'Registration failed. Please try again.'
            };
        }
    };


    // =========================================================================
    // STEP 6: Logout Function
    // =========================================================================
    // This is called when user clicks logout
    // It tells Laravel to invalidate the token and clears local storage

    const logout = async () => {
        try {
            // Tell Laravel to invalidate this token
            // This is important for security - the token won't work anymore
            await authAPI.logout();
        } catch (error) {
            // Even if the API call fails, we still want to clear local data
            console.error('Logout API error:', error);
        }

        // Clear local storage
        localStorage.removeItem('token');
        localStorage.removeItem('user');

        // Clear state
        setToken(null);
        setUser(null);
    };


    // =========================================================================
    // STEP 6: Create the value object that will be shared
    // =========================================================================
    // This is what components will receive when they use useAuth()

    const value = {
        user,           // Current user object or null
        token,          // Current token or null
        login,          // Function to log in
        register,       // Function to register
        logout,         // Function to log out
        loading,        // True while checking auth status
        isAuthenticated: !!token,  // Boolean: true if logged in, false if not
    };


    // =========================================================================
    // STEP 7: Render the Provider with the value
    // =========================================================================
    // All children components can now access the auth state

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
}


// =============================================================================
// STEP 8: Create a custom hook for easy access
// =============================================================================
// This is a shortcut so components can use:
//   const { user, login, logout } = useAuth();
// Instead of:
//   const { user, login, logout } = useContext(AuthContext);

export function useAuth() {
    const context = useContext(AuthContext);

    // Make sure this hook is used inside an AuthProvider
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }

    return context;
}


// =============================================================================
// HOW TO USE THIS IN OTHER COMPONENTS:
// =============================================================================
//
// 1. In your main App.jsx, wrap everything with AuthProvider:
//    <AuthProvider>
//      <App />
//    </AuthProvider>
//
// 2. In any component that needs auth info:
//    import { useAuth } from '../context/AuthContext';
//
//    function MyComponent() {
//      const { user, login, logout, isAuthenticated } = useAuth();
//
//      if (!isAuthenticated) {
//        return <p>Please login</p>;
//      }
//
//      return <p>Hello, {user.name}!</p>;
//    }
