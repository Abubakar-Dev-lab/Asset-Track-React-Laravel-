/**
 * Assets Page - Asset Inventory List (Matches Laravel Blade)
 * ==========================================================
 *
 * Features (matching Laravel exactly):
 * - Mobile: Card view
 * - Desktop: Table view
 * - Pagination
 * - View, Edit, Delete actions
 * - NO search/filter (Laravel doesn't have it)
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { assetsAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import Pagination from '../components/Pagination';
import ConfirmDialog from '../components/ConfirmDialog';

// Laravel backend URL for images
const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function Assets() {
    const { success, error } = useToast();

    // Data state
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(true);

    // Pagination
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    // Delete confirm
    const [deleteConfirm, setDeleteConfirm] = useState({ show: false, asset: null });
    const [deleting, setDeleting] = useState(false);

    // Fetch assets
    useEffect(() => {
        fetchAssets();
    }, [currentPage]);

    const fetchAssets = async () => {
        try {
            setLoading(true);
            const response = await assetsAPI.getAll({
                page: currentPage,
                per_page: 15,
            });

            if (response.data.data) {
                setAssets(response.data.data);
                if (response.data.meta) {
                    setTotalPages(response.data.meta.last_page || 1);
                }
            } else {
                setAssets(response.data);
            }
        } catch (err) {
            console.error('Error fetching assets:', err);
            error('Failed to load assets');
        } finally {
            setLoading(false);
        }
    };

    // Handle delete
    const handleDelete = async () => {
        if (!deleteConfirm.asset) return;

        setDeleting(true);
        try {
            await assetsAPI.delete(deleteConfirm.asset.id);
            success('Asset deleted successfully');
            setDeleteConfirm({ show: false, asset: null });
            fetchAssets();
        } catch (err) {
            console.error('Error deleting asset:', err);
            error(err.response?.data?.message || 'Failed to delete asset');
        } finally {
            setDeleting(false);
        }
    };

    if (loading && assets.length === 0) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading assets...</p>
            </div>
        );
    }

    return (
        <div className="assets-page">
            {/* Page Header */}
            <div className="page-header">
                <div>
                    <h1>Asset Inventory</h1>
                    <p className="page-subtitle">Manage all company assets from this dashboard</p>
                </div>
                <Link to="/assets/create" className="btn btn-primary">
                    + Add New Asset
                </Link>
            </div>

            {/* Mobile Card View */}
            <div className="mobile-cards">
                {assets.length > 0 ? (
                    assets.map((asset) => (
                        <div key={asset.id} className="card">
                            <div className="card-content">
                                <div className="card-image-row">
                                    {asset.image_path ? (
                                        <img
                                            src={`${STORAGE_URL}${asset.image_path}`}
                                            alt={asset.name}
                                            className="card-thumbnail"
                                        />
                                    ) : (
                                        <div className="card-thumbnail-placeholder">No image</div>
                                    )}
                                    <div className="card-info">
                                        <div className="card-header-row">
                                            <div>
                                                <h3 className="card-title">{asset.name}</h3>
                                                <p className="card-serial">{asset.serial_number}</p>
                                            </div>
                                            <span className={`badge status-${asset.status}`}>
                                                {asset.status.charAt(0).toUpperCase() + asset.status.slice(1)}
                                            </span>
                                        </div>
                                        <p className="card-category">{asset.category?.name || 'N/A'}</p>
                                    </div>
                                </div>
                                <div className="card-actions">
                                    <Link to={`/assets/${asset.id}`} className="action-link view">View</Link>
                                    <Link to={`/assets/${asset.id}/edit`} className="action-link edit">Edit</Link>
                                    <button
                                        onClick={() => setDeleteConfirm({ show: true, asset })}
                                        className="action-link delete"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))
                ) : (
                    <div className="empty-state">
                        <p>No assets found.</p>
                    </div>
                )}
            </div>

            {/* Desktop Table View */}
            <div className="desktop-table">
                <div className="table-card">
                    <table className="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>ID</th>
                                <th>Serial Number</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {assets.length > 0 ? (
                                assets.map((asset) => (
                                    <tr key={asset.id}>
                                        <td>
                                            {asset.image_path ? (
                                                <img
                                                    src={`${STORAGE_URL}${asset.image_path}`}
                                                    alt={asset.name}
                                                    className="table-thumbnail"
                                                />
                                            ) : (
                                                <div className="table-thumbnail-placeholder">No image</div>
                                            )}
                                        </td>
                                        <td>{asset.id}</td>
                                        <td className="serial-number">{asset.serial_number}</td>
                                        <td className="asset-name">{asset.name}</td>
                                        <td>{asset.category?.name || 'N/A'}</td>
                                        <td>
                                            <span className={`badge status-${asset.status}`}>
                                                {asset.status.charAt(0).toUpperCase() + asset.status.slice(1)}
                                            </span>
                                        </td>
                                        <td className="actions-cell">
                                            <Link to={`/assets/${asset.id}`} className="action-link view">View</Link>
                                            <Link to={`/assets/${asset.id}/edit`} className="action-link edit">Edit</Link>
                                            <button
                                                onClick={() => setDeleteConfirm({ show: true, asset })}
                                                className="action-link delete"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="7" className="no-data">No assets found</td>
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
                title="Delete Asset?"
                message={`Are you sure you want to delete "${deleteConfirm.asset?.name}"?`}
                confirmText="Delete"
                confirmVariant="danger"
                onConfirm={handleDelete}
                onCancel={() => setDeleteConfirm({ show: false, asset: null })}
                loading={deleting}
            />
        </div>
    );
}

export default Assets;
