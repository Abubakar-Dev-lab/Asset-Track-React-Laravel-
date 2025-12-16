/**
 * Users Page - User Management (Matches Laravel Blade)
 * =====================================================
 *
 * Features (matching Laravel exactly):
 * - Mobile: Card view
 * - Desktop: Table view
 * - Pagination
 * - View, Edit, Delete actions
 * - Current user highlighted
 * - NO search/filter (Laravel doesn't have it)
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { usersAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import { useAuth } from '../context/AuthContext';
import Pagination from '../components/Pagination';
import ConfirmDialog from '../components/ConfirmDialog';

function Users() {
    const { success, error } = useToast();
    const { user: currentUser } = useAuth();

    // Data state
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);

    // Pagination
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    // Delete confirm
    const [deleteConfirm, setDeleteConfirm] = useState({ show: false, user: null });
    const [deleting, setDeleting] = useState(false);

    // Fetch users
    useEffect(() => {
        fetchUsers();
    }, [currentPage]);

    const fetchUsers = async () => {
        try {
            setLoading(true);
            const response = await usersAPI.getAll({
                page: currentPage,
                per_page: 15,
            });

            if (response.data.data) {
                setUsers(response.data.data);
                if (response.data.meta) {
                    setTotalPages(response.data.meta.last_page || 1);
                }
            } else {
                setUsers(response.data);
            }
        } catch (err) {
            console.error('Error fetching users:', err);
            error('Failed to load users');
        } finally {
            setLoading(false);
        }
    };

    // Handle delete
    const handleDelete = async () => {
        if (!deleteConfirm.user) return;

        setDeleting(true);
        try {
            await usersAPI.delete(deleteConfirm.user.id);
            success('User deleted successfully');
            setDeleteConfirm({ show: false, user: null });
            fetchUsers();
        } catch (err) {
            console.error('Error deleting user:', err);
            error(err.response?.data?.message || 'Failed to delete user');
        } finally {
            setDeleting(false);
        }
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    if (loading && users.length === 0) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading users...</p>
            </div>
        );
    }

    return (
        <div className="users-page">
            {/* Page Header */}
            <div className="page-header">
                <div>
                    <h1>User Management</h1>
                    <p className="page-subtitle">Manage system users and their roles</p>
                </div>
                <Link to="/users/create" className="btn btn-primary">
                    + Add New User
                </Link>
            </div>

            {/* Mobile Card View */}
            <div className="mobile-cards">
                {users.length > 0 ? (
                    users.map((user) => (
                        <div
                            key={user.id}
                            className={`card ${currentUser?.id === user.id ? 'card-highlight' : ''}`}
                        >
                            <div className="card-content">
                                <div className="card-header-row">
                                    <div>
                                        <h3 className="card-title">
                                            {user.name}
                                            {currentUser?.id === user.id && (
                                                <span className="you-badge">(You)</span>
                                            )}
                                        </h3>
                                        <p className="card-email">{user.email}</p>
                                    </div>
                                    <div className="badge-stack">
                                        <span className={`badge role-${user.role}`}>
                                            {user.role === 'admin' ? 'Admin' : 'Employee'}
                                        </span>
                                        <span className={`badge ${user.is_active ? 'active' : 'inactive'}`}>
                                            {user.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                </div>
                                <div className="card-footer-row">
                                    <span className="card-date">Joined {formatDate(user.created_at)}</span>
                                    <div className="card-actions">
                                        <Link to={`/users/${user.id}`} className="action-link view">View</Link>
                                        <Link to={`/users/${user.id}/edit`} className="action-link edit">Edit</Link>
                                        {currentUser?.id !== user.id && (
                                            <button
                                                onClick={() => setDeleteConfirm({ show: true, user })}
                                                className="action-link delete"
                                            >
                                                Delete
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))
                ) : (
                    <div className="empty-state">
                        <p>No users found.</p>
                    </div>
                )}
            </div>

            {/* Desktop Table View */}
            <div className="desktop-table">
                <div className="table-card">
                    <table className="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.length > 0 ? (
                                users.map((user) => (
                                    <tr
                                        key={user.id}
                                        className={currentUser?.id === user.id ? 'row-highlight' : ''}
                                    >
                                        <td>{user.id}</td>
                                        <td className="user-name">
                                            {user.name}
                                            {currentUser?.id === user.id && (
                                                <span className="you-badge">(You)</span>
                                            )}
                                        </td>
                                        <td className="user-email">{user.email}</td>
                                        <td>
                                            <span className={`badge role-${user.role}`}>
                                                {user.role === 'admin' ? 'Admin' : 'Employee'}
                                            </span>
                                        </td>
                                        <td>
                                            <span className={`badge ${user.is_active ? 'active' : 'inactive'}`}>
                                                {user.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td>{formatDate(user.created_at)}</td>
                                        <td className="actions-cell">
                                            <Link to={`/users/${user.id}`} className="action-link view">View</Link>
                                            <Link to={`/users/${user.id}/edit`} className="action-link edit">Edit</Link>
                                            {currentUser?.id !== user.id && (
                                                <button
                                                    onClick={() => setDeleteConfirm({ show: true, user })}
                                                    className="action-link delete"
                                                >
                                                    Delete
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="no-data">No users found</td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination */}
            <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={setCurrentPage}
            />

            {/* Delete Confirmation */}
            <ConfirmDialog
                isOpen={deleteConfirm.show}
                title="Delete User?"
                message={`Are you sure you want to delete "${deleteConfirm.user?.name}"?`}
                confirmText="Delete"
                confirmVariant="danger"
                onConfirm={handleDelete}
                onCancel={() => setDeleteConfirm({ show: false, user: null })}
                loading={deleting}
            />
        </div>
    );
}

export default Users;
