/**
 * Profile Page - View and Edit User Profile
 * ===========================================
 *
 * Features:
 * - View profile information
 * - Edit name and email
 * - Change password
 */

import { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import { useToast } from '../context/ToastContext';
import api from '../services/api';

function Profile() {
    const { user } = useAuth();
    const { success, error } = useToast();

    // Profile form state
    const [profileData, setProfileData] = useState({
        name: '',
        email: '',
    });

    // Password form state
    const [passwordData, setPasswordData] = useState({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    // Loading states
    const [profileLoading, setProfileLoading] = useState(false);
    const [passwordLoading, setPasswordLoading] = useState(false);

    // Initialize form with user data
    useEffect(() => {
        if (user) {
            setProfileData({
                name: user.name || '',
                email: user.email || '',
            });
        }
    }, [user]);

    // Handle profile form change
    const handleProfileChange = (e) => {
        const { name, value } = e.target;
        setProfileData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // Handle password form change
    const handlePasswordChange = (e) => {
        const { name, value } = e.target;
        setPasswordData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    // Submit profile update
    const handleProfileSubmit = async (e) => {
        e.preventDefault();
        setProfileLoading(true);

        try {
            await api.put('/profile', profileData);
            success('Profile updated successfully');
        } catch (err) {
            console.error('Error updating profile:', err);
            error(err.response?.data?.message || 'Failed to update profile');
        } finally {
            setProfileLoading(false);
        }
    };

    // Submit password change
    const handlePasswordSubmit = async (e) => {
        e.preventDefault();

        // Validate passwords match
        if (passwordData.password !== passwordData.password_confirmation) {
            error('New passwords do not match');
            return;
        }

        setPasswordLoading(true);

        try {
            await api.put('/profile/password', passwordData);
            success('Password changed successfully');
            // Clear password form
            setPasswordData({
                current_password: '',
                password: '',
                password_confirmation: '',
            });
        } catch (err) {
            console.error('Error changing password:', err);
            error(err.response?.data?.message || 'Failed to change password');
        } finally {
            setPasswordLoading(false);
        }
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    return (
        <div className="profile-page">
            <div className="page-header">
                <h1>My Profile</h1>
            </div>

            {/* Account Info */}
            <div className="profile-section">
                <h2>Account Information</h2>
                <div className="profile-info">
                    <div className="profile-info-item">
                        <label>Role</label>
                        <span className={`badge role-${user?.role}`}>
                            {user?.role}
                        </span>
                    </div>
                    <div className="profile-info-item">
                        <label>Status</label>
                        <span className={`badge ${user?.is_active ? 'active' : 'inactive'}`}>
                            {user?.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                    <div className="profile-info-item">
                        <label>Member Since</label>
                        <span>{formatDate(user?.created_at)}</span>
                    </div>
                </div>
                <p style={{ marginTop: '16px', fontSize: '13px', color: 'var(--text-secondary)' }}>
                    Contact an administrator to change your role.
                </p>
            </div>

            {/* Profile Form */}
            <div className="profile-section">
                <h2>Profile Information</h2>
                <form onSubmit={handleProfileSubmit}>
                    <div className="form-row">
                        <div className="form-group">
                            <label>
                                Full Name <span className="required">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value={profileData.name}
                                onChange={handleProfileChange}
                                required
                            />
                        </div>
                        <div className="form-group">
                            <label>
                                Email Address <span className="required">*</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                value={profileData.email}
                                onChange={handleProfileChange}
                                required
                            />
                        </div>
                    </div>
                    <button
                        type="submit"
                        className="btn btn-primary"
                        disabled={profileLoading}
                    >
                        {profileLoading ? 'Saving...' : 'Save Changes'}
                    </button>
                </form>
            </div>

            {/* Password Form */}
            <div className="profile-section">
                <h2>Change Password</h2>
                <form onSubmit={handlePasswordSubmit}>
                    <div className="form-group">
                        <label>
                            Current Password <span className="required">*</span>
                        </label>
                        <input
                            type="password"
                            name="current_password"
                            value={passwordData.current_password}
                            onChange={handlePasswordChange}
                            required
                        />
                    </div>
                    <div className="form-row">
                        <div className="form-group">
                            <label>
                                New Password <span className="required">*</span>
                            </label>
                            <input
                                type="password"
                                name="password"
                                value={passwordData.password}
                                onChange={handlePasswordChange}
                                minLength={8}
                                required
                            />
                            <p className="hint">Minimum 8 characters</p>
                        </div>
                        <div className="form-group">
                            <label>
                                Confirm New Password <span className="required">*</span>
                            </label>
                            <input
                                type="password"
                                name="password_confirmation"
                                value={passwordData.password_confirmation}
                                onChange={handlePasswordChange}
                                minLength={8}
                                required
                            />
                        </div>
                    </div>
                    <button
                        type="submit"
                        className="btn btn-primary"
                        disabled={passwordLoading}
                    >
                        {passwordLoading ? 'Changing...' : 'Change Password'}
                    </button>
                </form>
            </div>
        </div>
    );
}

export default Profile;
