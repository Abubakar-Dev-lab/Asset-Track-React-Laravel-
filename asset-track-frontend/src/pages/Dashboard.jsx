/**
 * Dashboard Page - Statistics and Overview
 * =========================================
 *
 * Features:
 * - Statistics cards (total assets, available, assigned, employees)
 * - Quick action links
 * - Assets by status breakdown
 * - Recent assignments table
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { dashboardAPI } from '../services/api';
import { useAuth } from '../context/AuthContext';

function Dashboard() {
    const { user } = useAuth();
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            const response = await dashboardAPI.getStats();
            // API returns: { stats, recent_assignments, assets_by_category, assets_by_status }
            // directly (not wrapped in .data)
            setStats(response.data);
            setError(null);
        } catch (err) {
            console.error('Error fetching dashboard:', err);
            setError('Failed to load dashboard data');
        } finally {
            setLoading(false);
        }
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    // Get relative time
    const getRelativeTime = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / (1000 * 60));
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays === 1) return 'Yesterday';
        return `${diffDays} days ago`;
    };

    if (loading) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading dashboard...</p>
            </div>
        );
    }

    if (error) {
        return (
            <div className="page-error">
                <p>{error}</p>
                <button onClick={fetchDashboardData} className="btn btn-primary">
                    Try Again
                </button>
            </div>
        );
    }

    return (
        <div className="dashboard">
            <h1>Dashboard</h1>

            {/* Statistics Cards */}
            <div className="stats-grid">
                <div className="stat-card">
                    <div className="stat-icon blue">📦</div>
                    <div className="stat-info">
                        <h3>Total Assets</h3>
                        <p className="stat-number">{stats?.stats?.total_assets || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon green">✓</div>
                    <div className="stat-info">
                        <h3>Available</h3>
                        <p className="stat-number">{stats?.stats?.available_assets || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon yellow">⏱</div>
                    <div className="stat-info">
                        <h3>Assigned</h3>
                        <p className="stat-number">{stats?.stats?.assigned_assets || stats?.stats?.active_assignments || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon purple">👥</div>
                    <div className="stat-info">
                        <h3>Employees</h3>
                        <p className="stat-number">{stats?.stats?.total_employees || 0}</p>
                    </div>
                </div>
            </div>

            {/* Quick Actions - Admin Only */}
            {user?.role === 'admin' && (
                <div className="dashboard-section">
                    <h2>Quick Actions</h2>
                    <div className="quick-actions">
                        <Link to="/assets" className="quick-action-card">
                            <div className="quick-action-icon">📋</div>
                            <div className="quick-action-info">
                                <h4>View All Assets</h4>
                                <p>Manage your asset inventory</p>
                            </div>
                        </Link>

                        <Link to="/assets" className="quick-action-card" onClick={(e) => {
                            // This would ideally open the create modal
                        }}>
                            <div className="quick-action-icon">➕</div>
                            <div className="quick-action-info">
                                <h4>Add New Asset</h4>
                                <p>Register a new asset</p>
                            </div>
                        </Link>

                        <Link to="/users" className="quick-action-card">
                            <div className="quick-action-icon">👥</div>
                            <div className="quick-action-info">
                                <h4>Manage Users</h4>
                                <p>View and manage employees</p>
                            </div>
                        </Link>
                    </div>
                </div>
            )}

            {/* Assets by Status */}
            {stats?.assets_by_status && Object.keys(stats.assets_by_status).length > 0 && (
                <div className="dashboard-section">
                    <h2>Assets by Status</h2>
                    <div className="status-grid">
                        {/* Laravel returns assets_by_status as {status: count} object */}
                        {Object.entries(stats.assets_by_status).map(([status, count]) => (
                            <div key={status} className={`status-card status-${status}`}>
                                <h4>{status.charAt(0).toUpperCase() + status.slice(1)}</h4>
                                <p className="status-count">{count}</p>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Recent Assignments */}
            <div className="dashboard-section">
                <h2>Recent Assignments</h2>
                {stats?.recent_assignments && stats.recent_assignments.length > 0 ? (
                    <>
                        {/* Mobile View */}
                        <div className="mobile-cards" style={{ marginTop: '12px' }}>
                            {stats.recent_assignments.map((assignment) => (
                                <div key={assignment.id} className="mobile-card">
                                    <div className="mobile-card-header">
                                        <div>
                                            <div className="mobile-card-title">
                                                {assignment.asset?.name || 'Unknown Asset'}
                                            </div>
                                            <div className="mobile-card-subtitle">
                                                {assignment.asset?.serial_number || 'N/A'}
                                            </div>
                                        </div>
                                        <span className={`badge ${assignment.returned_at ? 'returned' : 'active'}`}>
                                            {assignment.returned_at ? 'Returned' : 'Active'}
                                        </span>
                                    </div>
                                    <div className="mobile-card-body">
                                        <div className="mobile-card-field">
                                            <label>Assigned To</label>
                                            <span>{assignment.user?.name || 'N/A'}</span>
                                        </div>
                                        <div className="mobile-card-field">
                                            <label>When</label>
                                            <span>{getRelativeTime(assignment.assigned_at)}</span>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Desktop View */}
                        <div className="desktop-table" style={{ marginTop: '12px' }}>
                            <table className="data-table">
                                <thead>
                                    <tr>
                                        <th>Asset</th>
                                        <th>Assigned To</th>
                                        <th>Assigned By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {stats.recent_assignments.map((assignment) => (
                                        <tr key={assignment.id}>
                                            <td>
                                                <Link to={`/assets/${assignment.asset?.id}`}>
                                                    {assignment.asset?.name || 'N/A'}
                                                </Link>
                                                <div style={{ fontSize: '12px', color: 'var(--text-secondary)' }}>
                                                    {assignment.asset?.serial_number}
                                                </div>
                                            </td>
                                            <td>{assignment.user?.name || 'N/A'}</td>
                                            <td>{assignment.admin?.name || 'N/A'}</td>
                                            <td>
                                                {formatDate(assignment.assigned_at)}
                                                <div style={{ fontSize: '12px', color: 'var(--text-secondary)' }}>
                                                    {getRelativeTime(assignment.assigned_at)}
                                                </div>
                                            </td>
                                            <td>
                                                <span className={`badge ${assignment.returned_at ? 'returned' : 'active'}`}>
                                                    {assignment.returned_at ? 'Returned' : 'Active'}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </>
                ) : (
                    <div className="empty-state" style={{ padding: '24px' }}>
                        <p>No recent assignments</p>
                    </div>
                )}
            </div>
        </div>
    );
}

export default Dashboard;
