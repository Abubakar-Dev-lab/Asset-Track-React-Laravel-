/**
 * AssetDetail Page - View Asset Details and Assignment History
 * =============================================================
 *
 * Features:
 * - Asset information display
 * - Image display
 * - Assign asset to employee (if available)
 * - Return asset (if assigned)
 * - Assignment history table
 */

import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { assetsAPI, usersAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import ConfirmDialog from '../components/ConfirmDialog';

// Laravel backend URL for images
const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function AssetDetail() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { success, error } = useToast();

    // State
    const [asset, setAsset] = useState(null);
    const [employees, setEmployees] = useState([]);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(false);

    // Assign form
    const [selectedEmployee, setSelectedEmployee] = useState('');
    const [assignNotes, setAssignNotes] = useState('');

    // Confirm dialog
    const [showReturnConfirm, setShowReturnConfirm] = useState(false);

    // Fetch asset details
    useEffect(() => {
        fetchAsset();
        fetchEmployees();
    }, [id]);

    const fetchAsset = async () => {
        try {
            setLoading(true);
            const response = await assetsAPI.getOne(id);
            setAsset(response.data.data);
        } catch (err) {
            console.error('Error fetching asset:', err);
            error('Failed to load asset details');
        } finally {
            setLoading(false);
        }
    };

    const fetchEmployees = async () => {
        try {
            const response = await usersAPI.getEmployees();
            setEmployees(response.data.data);
        } catch (err) {
            console.error('Error fetching employees:', err);
        }
    };

    // Handle assign asset
    const handleAssign = async (e) => {
        e.preventDefault();
        if (!selectedEmployee) {
            error('Please select an employee');
            return;
        }

        setActionLoading(true);
        try {
            await assetsAPI.assign(id, selectedEmployee, assignNotes);
            success('Asset assigned successfully');
            setSelectedEmployee('');
            setAssignNotes('');
            fetchAsset();  // Refresh
        } catch (err) {
            console.error('Error assigning asset:', err);
            error(err.response?.data?.message || 'Failed to assign asset');
        } finally {
            setActionLoading(false);
        }
    };

    // Handle return asset
    const handleReturn = async () => {
        setActionLoading(true);
        try {
            await assetsAPI.return(id);
            success('Asset returned successfully');
            setShowReturnConfirm(false);
            fetchAsset();  // Refresh
        } catch (err) {
            console.error('Error returning asset:', err);
            error(err.response?.data?.message || 'Failed to return asset');
        } finally {
            setActionLoading(false);
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

    // Get relative time (e.g., "2 days ago")
    const getRelativeTime = (dateString) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
        return `${Math.floor(diffDays / 30)} months ago`;
    };

    if (loading) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading asset...</p>
            </div>
        );
    }

    if (!asset) {
        return (
            <div className="page-error">
                <p>Asset not found</p>
                <Link to="/assets" className="btn btn-primary">Back to Assets</Link>
            </div>
        );
    }

    return (
        <div className="detail-page">
            {/* Breadcrumb */}
            <div className="breadcrumb">
                <Link to="/assets">Assets</Link>
                <span>/</span>
                <span>{asset.name}</span>
            </div>

            {/* Asset Info Card */}
            <div className="detail-card">
                <div className="detail-card-header">
                    <h2>{asset.name}</h2>
                    <div className="page-header-actions">
                        <Link to={`/assets`} className="btn btn-secondary btn-sm">
                            ← Back
                        </Link>
                    </div>
                </div>
                <div className="detail-card-body">
                    {/* Asset Image */}
                    {asset.image_path && (
                        <img
                            src={`${STORAGE_URL}${asset.image_path}`}
                            alt={asset.name}
                            className="detail-image"
                        />
                    )}

                    {/* Asset Details Grid */}
                    <div className="detail-grid">
                        <div className="detail-item">
                            <label>Serial Number</label>
                            <span>{asset.serial_number}</span>
                        </div>
                        <div className="detail-item">
                            <label>Category</label>
                            <span>{asset.category?.name || 'N/A'}</span>
                        </div>
                        <div className="detail-item">
                            <label>Status</label>
                            <span className={`badge status-${asset.status}`}>
                                {asset.status}
                            </span>
                        </div>
                        <div className="detail-item">
                            <label>Created</label>
                            <span>{formatDate(asset.created_at)}</span>
                        </div>
                    </div>

                    {/* Action Box - Assign or Return */}
                    {asset.status === 'available' && (
                        <div className="action-box">
                            <h3>Assign Asset</h3>
                            <form onSubmit={handleAssign}>
                                <div className="form-group">
                                    <label>Select Employee <span className="required">*</span></label>
                                    <select
                                        value={selectedEmployee}
                                        onChange={(e) => setSelectedEmployee(e.target.value)}
                                        required
                                    >
                                        <option value="">Choose an employee...</option>
                                        {employees.map((emp) => (
                                            <option key={emp.id} value={emp.id}>
                                                {emp.name} ({emp.email})
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label>Notes (optional)</label>
                                    <textarea
                                        value={assignNotes}
                                        onChange={(e) => setAssignNotes(e.target.value)}
                                        placeholder="Add any notes about this assignment..."
                                        rows="2"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    className="btn btn-success"
                                    disabled={actionLoading}
                                >
                                    {actionLoading ? 'Assigning...' : 'Check Out'}
                                </button>
                            </form>
                        </div>
                    )}

                    {asset.status === 'assigned' && asset.current_holder && (
                        <div className="action-box">
                            <h3>Currently Assigned</h3>
                            <div className="current-holder">
                                <div className="current-holder-info">
                                    <div className="current-holder-name">
                                        {asset.current_holder.name}
                                    </div>
                                    <div className="current-holder-since">
                                        Assigned {getRelativeTime(asset.assignments?.[0]?.assigned_at)}
                                    </div>
                                </div>
                            </div>
                            <button
                                className="btn btn-warning"
                                onClick={() => setShowReturnConfirm(true)}
                                disabled={actionLoading}
                            >
                                Check In (Return)
                            </button>
                        </div>
                    )}

                    {(asset.status === 'maintenance' || asset.status === 'broken') && (
                        <div className="action-box">
                            <h3>Status: {asset.status}</h3>
                            <p style={{ color: 'var(--text-secondary)' }}>
                                This asset is currently {asset.status}. No actions available.
                            </p>
                        </div>
                    )}
                </div>
            </div>

            {/* Assignment History */}
            <div className="detail-card">
                <div className="detail-card-header">
                    <h2>Assignment History</h2>
                </div>
                <div className="detail-card-body" style={{ padding: 0 }}>
                    {asset.assignments && asset.assignments.length > 0 ? (
                        <table className="data-table">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Assigned At</th>
                                    <th>Returned At</th>
                                    <th>Assigned By</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {asset.assignments.map((assignment, index) => (
                                    <tr key={assignment.id}>
                                        <td>
                                            <Link to={`/users/${assignment.user?.id}`}>
                                                {assignment.user?.name || 'N/A'}
                                            </Link>
                                        </td>
                                        <td>{formatDate(assignment.assigned_at)}</td>
                                        <td>{formatDate(assignment.returned_at)}</td>
                                        <td>{assignment.admin?.name || 'N/A'}</td>
                                        <td>
                                            {assignment.returned_at ? (
                                                <span className="badge returned">Returned</span>
                                            ) : (
                                                <span className="badge current">Current</span>
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

            {/* Return Confirmation Dialog */}
            <ConfirmDialog
                isOpen={showReturnConfirm}
                title="Return Asset?"
                message={`Are you sure you want to check in "${asset.name}" from ${asset.current_holder?.name}?`}
                confirmText="Yes, Return"
                confirmVariant="warning"
                onConfirm={handleReturn}
                onCancel={() => setShowReturnConfirm(false)}
                loading={actionLoading}
            />
        </div>
    );
}

export default AssetDetail;
