/**
 * Register Page Component (Matches Laravel register.blade.php)
 * =============================================================
 *
 * This is where new users create their accounts.
 * Responsive design matching Laravel's Tailwind styles.
 *
 * HOW REGISTRATION WORKS:
 * 1. User enters name, email, password, and confirms password
 * 2. Form submits and calls the register() function from AuthContext
 * 3. AuthContext sends data to Laravel API (/api/auth/register)
 * 4. Laravel creates user account and returns a token
 * 5. Token is saved to localStorage and user is redirected to dashboard
 *
 * KEY CONCEPTS:
 * - useState: Stores form data and UI state (loading, error)
 * - useAuth: Gets the register function from AuthContext
 * - useNavigate: Redirects user after successful registration
 * - Link: React Router component for navigation to login page
 */

import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Register() {
    // =========================================================================
    // STATE VARIABLES
    // =========================================================================

    // Form inputs - controlled components (React manages the input values)
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });

    // UI state
    const [error, setError] = useState('');      // Error message to display
    const [loading, setLoading] = useState(false); // True while request is in progress

    // =========================================================================
    // HOOKS
    // =========================================================================

    // Get the register function from AuthContext
    const { register } = useAuth();

    // useNavigate lets us redirect the user programmatically
    const navigate = useNavigate();


    // =========================================================================
    // FORM HANDLERS
    // =========================================================================

    // Handle input changes
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // Handle form submission
    const handleSubmit = async (e) => {
        // Prevent default form submission (which would reload the page)
        e.preventDefault();

        // Clear any previous error
        setError('');

        // Validate passwords match
        if (formData.password !== formData.password_confirmation) {
            setError('Passwords do not match');
            return;
        }

        // Set loading to true (shows spinner or disables button)
        setLoading(true);

        // Call the register function from AuthContext
        const result = await register(
            formData.name,
            formData.email,
            formData.password,
            formData.password_confirmation
        );

        if (result.success) {
            // Registration successful! Redirect based on user role
            // Admin goes to dashboard, employee goes to my-assets (like Laravel)
            if (result.user?.role === 'admin') {
                navigate('/dashboard');
            } else {
                navigate('/my-assets');
            }
        } else {
            // Registration failed, show error message
            setError(result.message);
        }

        // Done loading
        setLoading(false);
    };


    // =========================================================================
    // RENDER THE REGISTER FORM
    // =========================================================================

    return (
        <div className="auth-page">
            <div className="auth-card">
                {/* Header */}
                <div className="auth-header">
                    <h1>Asset Track</h1>
                    <p>Create your account</p>
                </div>

                {/* Show error message if registration failed */}
                {error && (
                    <div className="auth-error">
                        {error}
                    </div>
                )}

                {/* Register Form */}
                <form onSubmit={handleSubmit} className="auth-form">
                    {/* Full Name Input */}
                    <div className="form-group">
                        <label htmlFor="name">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            placeholder="Enter your full name"
                            required
                            disabled={loading}
                        />
                    </div>

                    {/* Email Input */}
                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
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
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            placeholder="Enter your password"
                            required
                            minLength={8}
                            disabled={loading}
                        />
                        <p className="hint">Minimum 8 characters</p>
                    </div>

                    {/* Confirm Password Input */}
                    <div className="form-group">
                        <label htmlFor="password_confirmation">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            value={formData.password_confirmation}
                            onChange={handleChange}
                            placeholder="Confirm your password"
                            required
                            minLength={8}
                            disabled={loading}
                        />
                    </div>

                    {/* Submit Button */}
                    <button
                        type="submit"
                        className="btn btn-primary auth-btn"
                        disabled={loading}
                    >
                        {loading ? 'Creating Account...' : 'Create Account'}
                    </button>
                </form>

                {/* Login Link - Matches Laravel register.blade.php */}
                <div className="auth-footer">
                    <p>
                        Already have an account?{' '}
                        <Link to="/login" className="auth-link">
                            Sign in here
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Register;
