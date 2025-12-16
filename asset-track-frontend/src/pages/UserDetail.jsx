/**
 * UserDetail Page - View User Details and Assignment History
 * ===========================================================
 *
 * Features:
 * - User information display
 * - Role and status badges
 * - Assignment history table
 */

import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { usersAPI } from '../services/api';
import { useToast } from '../context/ToastContext';

function UserDetail() {
    const { id } = useParams();
    const { error } = useToast();

    // State
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    // Fetch user details
    useEffect(() => {
        fetchUser();
    }, [id]);

    const fetchUser = async () => {
        try {
            setLoading(true);
            const response = await usersAPI.getOne(id);
            setUser(response.data.data);
        } catch (err) {
            console.error('Error fetching user:', err);
            error('Failed to load user details');
        } finally {
            setLoading(false);
        }
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    if (loading) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading user...</p>
            </div>
        );
    }

    if (!user) {
        return (
            <div className="page-error">
                <p>User not found</p>
                <Link to="/users" className="btn btn-primary">Back to Users</Link>
            </div>
        );
    }

    return (
        <div className="detail-page">
            {/* Breadcrumb */}
            <div className="breadcrumb">
                <Link to="/users">Users</Link>
                <span>/</span>
                <span>{user.name}</span>
            </div>

            {/* User Info Card */}
            <div className="detail-card">
                <div className="detail-card-header">
                    <h2>{user.name}</h2>
                    <div className="page-header-actions">
                        <Link to="/users" className="btn btn-secondary btn-sm">
                            ← Back
                        </Link>
                    </div>
                </div>
                <div className="detail-card-body">
                    <div className="detail-grid">
                        <div className="detail-item">
                            <label>Email</label>
                            <span>{user.email}</span>
                        </div>
                        <div className="detail-item">
                            <label>Role</label>
                            <span className={`badge role-${user.role}`}>
                                {user.role}
                            </span>
                        </div>
                        <div className="detail-item">
                            <label>Status</label>
                            <span className={`badge ${user.is_active ? 'active' : 'inactive'}`}>
                                {user.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        <div className="detail-item">
                            <label>User ID</label>
                            <span>#{user.id}</span>
                        </div>
                        <div className="detail-item">
                            <label>Registered</label>
                            <span>{formatDate(user.created_at)}</span>
                        </div>
                        <div className="detail-item">
                            <label>Last Updated</label>
                            <span>{formatDate(user.updated_at)}</span>
                        </div>
                        <div className="detail-item">
                            <label>Total Assignments</label>
                            <span>{user.assignments_count || 0}</span>
                        </div>
                        <div className="detail-item">
                            <label>Active Assignments</label>
                            <span>{user.active_assignments_count || 0}</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Assignment History */}
            <div className="detail-card">
                <div className="detail-card-header">
                    <h2>Assignment History</h2>
                </div>
                <div className="detail-card-body" style={{ padding: 0 }}>
                    {user.assignments && user.assignments.length > 0 ? (
                        <table className="data-table">
                            <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Serial Number</th>
                                    <th>Assigned At</th>
                                    <th>Returned At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {user.assignments.map((assignment) => (
                                    <tr key={assignment.id}>
                                        <td>
                                            <Link to={`/assets/${assignment.asset?.id}`}>
                                                {assignment.asset?.name || 'N/A'}
                                            </Link>
                                        </td>
                                        <td>{assignment.asset?.serial_number || 'N/A'}</td>
                                        <td>{formatDate(assignment.assigned_at)}</td>
                                        <td>{formatDate(assignment.returned_at)}</td>
                                        <td>
                                            {assignment.returned_at ? (
                                                <span className="badge returned">Returned</span>
                                            ) : (
                                                <span className="badge active">Active</span>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    ) : (
                        <div className="empty-state">
                            <p>No assignment history</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

export default UserDetail;
