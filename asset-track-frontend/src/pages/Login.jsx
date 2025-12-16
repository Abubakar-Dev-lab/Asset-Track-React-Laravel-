/**
 * Login Page Component (Matches Laravel login.blade.php)
 * =======================================================
 *
 * This is where users enter their credentials to log in.
 * Responsive design matching Laravel's Tailwind styles.
 *
 * HOW LOGIN WORKS:
 * 1. User enters email and password
 * 2. Form submits and calls the login() function from AuthContext
 * 3. AuthContext sends credentials to Laravel API (/api/auth/login)
 * 4. Laravel verifies credentials and returns a token
 * 5. Token is saved to localStorage and user is redirected to dashboard
 *
 * KEY CONCEPTS:
 * - useState: Stores form data (email, password) and UI state (loading, error)
 * - useAuth: Gets the login function from AuthContext
 * - useNavigate: Redirects user after successful login
 * - Link: React Router component for navigation to register page
 */

import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Login() {
    // =========================================================================
    // STATE VARIABLES
    // =========================================================================

    // Form inputs - controlled components (React manages the input values)
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    // UI state
    const [error, setError] = useState('');      // Error message to display
    const [loading, setLoading] = useState(false); // True while login request is in progress

    // =========================================================================
    // HOOKS
    // =========================================================================

    // Get the login function and user from AuthContext
    const { login, user } = useAuth();

    // useNavigate lets us redirect the user programmatically
    const navigate = useNavigate();


    // =========================================================================
    // FORM SUBMIT HANDLER
    // =========================================================================

    const handleSubmit = async (e) => {
        // Prevent default form submission (which would reload the page)
        e.preventDefault();

        // Clear any previous error
        setError('');

        // Set loading to true (shows spinner or disables button)
        setLoading(true);

        // Call the login function from AuthContext
        // This sends credentials to Laravel and handles the response
        const result = await login(email, password);

        if (result.success) {
            // Login successful! Redirect based on user role
            // Admin goes to dashboard, employee goes to my-assets (like Laravel)
            if (result.user?.role === 'admin') {
                navigate('/dashboard');
            } else {
                navigate('/my-assets');
            }
        } else {
            // Login failed, show error message
            setError(result.message);
        }

        // Done loading
        setLoading(false);
    };


    // =========================================================================
    // RENDER THE LOGIN FORM
    // =========================================================================

    return (
        <div className="auth-page">
            <div className="auth-card">
                {/* Header */}
                <div className="auth-header">
                    <h1>Asset Track</h1>
                    <p>Sign in to your account</p>
                </div>

                {/* Show error message if login failed */}
                {error && (
                    <div className="auth-error">
                        {error}
                    </div>
                )}

                {/* Login Form */}
                <form onSubmit={handleSubmit} className="auth-form">
                    {/* Email Input */}
                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="Enter your email"
                            required
                            disabled={loading}
                        />
                    </div>

                    {/* Password Input */}
                    <div className="form-group">
                        <label htmlFor="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="Enter your password"
                            required
                            disabled={loading}
                        />
                    </div>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        className="btn btn-primary auth-btn"
                        disabled={loading}
                    >
                        {loading ? 'Signing in...' : 'Sign In'}
                    </button>
                </form>

                {/* Register Link - Matches Laravel login.blade.php */}
                <div className="auth-footer">
                    <p>
                        Don't have an account?{' '}
                        <Link to="/register" className="auth-link">
                            Register here
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Login;
