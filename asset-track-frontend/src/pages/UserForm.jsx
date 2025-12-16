/**
 * User Form Page - Create/Edit User (Matches Laravel)
 * ====================================================
 *
 * This page handles both creating new users and editing existing ones.
 * Matches Laravel's users/create.blade.php and users/edit.blade.php
 */

import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { usersAPI } from '../services/api';
import { useToast } from '../context/ToastContext';

function UserForm() {
    const { id } = useParams(); // If id exists, we're editing
    const navigate = useNavigate();
    const { success, error } = useToast();
    const isEditing = Boolean(id);

    // Form state
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        role: 'employee',
        password: '',
        password_confirmation: '',
        is_active: true,
    });
    const [loading, setLoading] = useState(false);
    const [fetching, setFetching] = useState(isEditing);

    // Fetch user data if editing
    useEffect(() => {
        if (isEditing) {
            fetchUser();
        }
    }, [id]);

    const fetchUser = async () => {
        try {
            setFetching(true);
            const response = await usersAPI.getOne(id);
            const user = response.data.data || response.data;
            setFormData({
                name: user.name,
                email: user.email,
                role: user.role,
                password: '',
                password_confirmation: '',
                is_active: user.is_active,
            });
        } catch (err) {
            console.error('Error fetching user:', err);
            error('Failed to load user');
            navigate('/users');
        } finally {
            setFetching(false);
        }
    };

    // Handle form input changes
    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value
        }));
    };

    // Handle form submission
    const handleSubmit = async (e) => {
        e.preventDefault();

        // Validate passwords match (for create mode)
        if (!isEditing && formData.password !== formData.password_confirmation) {
            error('Passwords do not match');
            return;
        }

        setLoading(true);

        try {
            if (isEditing) {
                // Edit mode - don't send password fields
                await usersAPI.update(id, {
                    name: formData.name,
                    email: formData.email,
                    role: formData.role,
                    is_active: formData.is_active,
                });
                success('User updated successfully');
                navigate(`/users/${id}`);
            } else {
                // Create mode - include password
                await usersAPI.create({
                    name: formData.name,
                    email: formData.email,
                    role: formData.role,
                    password: formData.password,
                    password_confirmation: formData.password_confirmation,
                });
                success('User created successfully');
                navigate('/users');
            }
        } catch (err) {
            console.error('Error saving user:', err);
            error(err.response?.data?.message || 'Failed to save user');
        } finally {
            setLoading(false);
        }
    };

    if (fetching) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading user...</p>
            </div>
        );
    }

    return (
        <div className="form-page">
            {/* Breadcrumb */}
            <div className="breadcrumb">
                {isEditing ? (
                    <>
                        <Link to="/users">Users</Link>
                        <span>/</span>
                        <Link to={`/users/${id}`}>{formData.name}</Link>
                        <span>/</span>
                        <span>Edit</span>
                    </>
                ) : (
                    <Link to="/users">← Back to Users</Link>
                )}
            </div>

            {/* Form Card */}
            <div className="form-card form-card-sm">
                <h1>{isEditing ? `Edit User: ${formData.name}` : 'Create New User'}</h1>

                <form onSubmit={handleSubmit}>
                    {/* Full Name */}
                    <div className="form-group">
                        <label htmlFor="name">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    {/* Email */}
                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    {/* Role */}
                    <div className="form-group">
                        <label htmlFor="role">Role</label>
                        <select
                            id="role"
                            name="role"
                            value={formData.role}
                            onChange={handleChange}
                            required
                        >
                            <option value="employee">Employee</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    {/* Password fields - only for create */}
                    {!isEditing && (
                        <>
                            <div className="form-group">
                                <label htmlFor="password">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    value={formData.password}
                                    onChange={handleChange}
                                    required
                                    minLength={8}
                                />
                                <p className="hint">Minimum 8 characters</p>
                            </div>

                            <div className="form-group">
                                <label htmlFor="password_confirmation">Confirm Password</label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    value={formData.password_confirmation}
                                    onChange={handleChange}
                                    required
                                    minLength={8}
                                />
                            </div>
                        </>
                    )}

                    {/* Active Status - only for edit */}
                    {isEditing && (
                        <div className="form-group checkbox-group">
                            <label>
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    checked={formData.is_active}
                                    onChange={handleChange}
                                />
                                User is active (can login)
                            </label>
                        </div>
                    )}

                    {/* Buttons */}
                    <div className="form-actions">
                        <button
                            type="submit"
                            className={`btn ${isEditing ? 'btn-primary' : 'btn-success'}`}
                            disabled={loading}
                        >
                            {loading ? 'Saving...' : (isEditing ? 'Update User' : 'Create User')}
                        </button>
                        <Link
                            to={isEditing ? `/users/${id}` : '/users'}
                            className="btn-link"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>

                {/* Security Notice - edit mode only */}
                {isEditing && (
                    <div className="info-box warning">
                        <p>
                            <strong>Note:</strong> For security reasons, passwords cannot be changed here.
                            Users must use the password reset feature.
                        </p>
                    </div>
                )}
            </div>
        </div>
    );
}

export default UserForm;
