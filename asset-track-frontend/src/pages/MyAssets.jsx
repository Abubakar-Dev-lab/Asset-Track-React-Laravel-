/**
 * MyAssets Page - Employee's Assigned Assets (Matches Laravel Blade)
 * ===================================================================
 *
 * Shows assets currently assigned to the logged-in employee.
 * This is the main page for employees (non-admin users).
 * Uses TABLE format like Laravel (not cards).
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../services/api';
import { useToast } from '../context/ToastContext';

// Laravel backend URL for images
const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function MyAssets() {
    const { error } = useToast();

    // State
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(true);

    // Fetch my assets
    useEffect(() => {
        fetchMyAssets();
    }, []);

    const fetchMyAssets = async () => {
        try {
            setLoading(true);
            const response = await api.get('/my-assets');
            console.log('My Assets API response:', response.data); // Debug log
            // Handle both response.data.data and response.data formats
            const assetsData = response.data.data || response.data || [];
            setAssets(Array.isArray(assetsData) ? assetsData : []);
        } catch (err) {
            console.error('Error fetching my assets:', err);
            error('Failed to load your assets');
        } finally {
            setLoading(false);
        }
    };

    // Get relative time
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

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    if (loading) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading your assets...</p>
            </div>
        );
    }

    return (
        <div className="my-assets-page">
            <div className="page-header">
                <div>
                    <h1>My Current Assets</h1>
                    <p className="page-subtitle">View all assets currently assigned to you</p>
                </div>
            </div>

            {/* Table */}
            <div className="table-card">
                <table className="data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Asset Name</th>
                            <th>Serial Number</th>
                            <th>Category</th>
                            <th>Assigned Since</th>
                        </tr>
                    </thead>
                    <tbody>
                        {assets.length > 0 ? (
                            assets.map((item) => {
                                const asset = item;
                                const assignedAt = item.assigned_at;

                                return (
                                    <tr key={asset.id || item.id}>
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
                                        <td className="asset-name">
                                            {asset.name || 'Unknown Asset'}
                                        </td>
                                        <td className="serial-number">{asset.serial_number || 'N/A'}</td>
                                        <td>{asset.category?.name || 'Uncategorized'}</td>
                                        <td>
                                            {formatDate(assignedAt)}
                                            {assignedAt && (
                                                <span className="date-relative">
                                                    ({getRelativeTime(assignedAt)})
                                                </span>
                                            )}
                                        </td>
                                    </tr>
                                );
                            })
                        ) : (
                            <tr>
                                <td colSpan="5" className="no-data">
                                    You do not have any active assets checked out.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

export default MyAssets;
