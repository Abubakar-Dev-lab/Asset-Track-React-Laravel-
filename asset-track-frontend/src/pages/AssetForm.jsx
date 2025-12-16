/**
 * Asset Form Page - Create/Edit Asset (Matches Laravel)
 * ======================================================
 *
 * This page handles both creating new assets and editing existing ones.
 * Matches Laravel's assets/create.blade.php and assets/edit.blade.php
 */

import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { assetsAPI, categoriesAPI } from '../services/api';
import { useToast } from '../context/ToastContext';

// Laravel backend URL for images
const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function AssetForm() {
    const { id } = useParams(); // If id exists, we're editing
    const navigate = useNavigate();
    const { success, error } = useToast();
    const isEditing = Boolean(id);

    // Form state
    const [formData, setFormData] = useState({
        name: '',
        serial_number: '',
        category_id: '',
        status: 'available',
    });
    const [imageFile, setImageFile] = useState(null);
    const [currentImage, setCurrentImage] = useState(null);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(false);
    const [fetching, setFetching] = useState(isEditing);

    // Fetch categories and asset data (if editing)
    useEffect(() => {
        fetchCategories();
        if (isEditing) {
            fetchAsset();
        }
    }, [id]);

    const fetchCategories = async () => {
        try {
            const response = await categoriesAPI.getAll();
            setCategories(response.data.data || response.data);
        } catch (err) {
            console.error('Error fetching categories:', err);
        }
    };

    const fetchAsset = async () => {
        try {
            setFetching(true);
            const response = await assetsAPI.getOne(id);
            const asset = response.data.data || response.data;
            setFormData({
                name: asset.name,
                serial_number: asset.serial_number,
                category_id: asset.category_id,
                status: asset.status,
            });
            setCurrentImage(asset.image_path);
        } catch (err) {
            console.error('Error fetching asset:', err);
            error('Failed to load asset');
            navigate('/assets');
        } finally {
            setFetching(false);
        }
    };

    // Handle form input changes
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    // Handle image selection
    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validate file type and size
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                error('Please select a valid image (JPEG, PNG, or WEBP)');
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                error('Image must be less than 2MB');
                return;
            }
            setImageFile(file);
        }
    };

    // Handle form submission
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            // Use FormData for image upload
            const data = new FormData();
            data.append('name', formData.name);
            data.append('serial_number', formData.serial_number);
            data.append('category_id', formData.category_id);
            data.append('status', formData.status);
            if (imageFile) {
                data.append('image', imageFile);
            }

            if (isEditing) {
                data.append('_method', 'PUT');
                await assetsAPI.update(id, data);
                success('Asset updated successfully');
                navigate(`/assets/${id}`);
            } else {
                await assetsAPI.create(data);
                success('Asset created successfully');
                navigate('/assets');
            }
        } catch (err) {
            console.error('Error saving asset:', err);
            error(err.response?.data?.message || 'Failed to save asset');
        } finally {
            setLoading(false);
        }
    };

    if (fetching) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading asset...</p>
            </div>
        );
    }

    return (
        <div className="form-page">
            {/* Breadcrumb */}
            <div className="breadcrumb">
                {isEditing ? (
                    <>
                        <Link to="/assets">All Assets</Link>
                        <span>/</span>
                        <Link to={`/assets/${id}`}>{formData.name}</Link>
                        <span>/</span>
                        <span>Edit</span>
                    </>
                ) : (
                    <Link to="/assets">← Back to All Assets</Link>
                )}
            </div>

            {/* Form Card */}
            <div className="form-card">
                <h1>{isEditing ? `Edit Asset: ${formData.name}` : 'Add New Asset'}</h1>

                <form onSubmit={handleSubmit}>
                    {/* Asset Name */}
                    <div className="form-group">
                        <label htmlFor="name">
                            Asset Name (e.g., MacBook Pro M2) <span className="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    {/* Serial Number */}
                    <div className="form-group">
                        <label htmlFor="serial_number">
                            Serial Number (Unique) <span className="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="serial_number"
                            name="serial_number"
                            value={formData.serial_number}
                            onChange={handleChange}
                            required
                        />
                    </div>

                    {/* Category and Status - side by side */}
                    <div className="form-row">
                        <div className="form-group">
                            <label htmlFor="category_id">
                                Category <span className="required">*</span>
                            </label>
                            <select
                                id="category_id"
                                name="category_id"
                                value={formData.category_id}
                                onChange={handleChange}
                                required
                            >
                                <option value="">-- Select Category --</option>
                                {categories.map(cat => (
                                    <option key={cat.id} value={cat.id}>{cat.name}</option>
                                ))}
                            </select>
                        </div>

                        <div className="form-group">
                            <label htmlFor="status">
                                {isEditing ? 'Status' : 'Initial Status'} <span className="required">*</span>
                            </label>
                            <select
                                id="status"
                                name="status"
                                value={formData.status}
                                onChange={handleChange}
                                required
                            >
                                <option value="available">Available</option>
                                {isEditing && <option value="assigned">Assigned</option>}
                                <option value="maintenance">Maintenance</option>
                                <option value="broken">Broken</option>
                            </select>
                        </div>
                    </div>

                    {/* Current Image (edit mode) */}
                    {isEditing && currentImage && (
                        <div className="form-group">
                            <label>Current Image</label>
                            <img
                                src={`${STORAGE_URL}${currentImage}`}
                                alt={formData.name}
                                className="current-image-preview"
                            />
                        </div>
                    )}

                    {/* Image Upload */}
                    <div className="form-group">
                        <label htmlFor="image">
                            {isEditing && currentImage ? 'Replace Image (Optional)' : 'Asset Image (Optional)'}
                        </label>
                        <input
                            type="file"
                            id="image"
                            accept="image/jpeg,image/png,image/webp"
                            onChange={handleImageChange}
                        />
                        <p className="hint">Accepted formats: JPEG, PNG, WEBP. Max size: 2MB</p>
                    </div>

                    {/* Buttons */}
                    <div className="form-actions">
                        <button
                            type="submit"
                            className="btn btn-success"
                            disabled={loading}
                        >
                            {loading ? 'Saving...' : (isEditing ? 'Update Asset' : 'Save Asset')}
                        </button>
                        <Link
                            to={isEditing ? `/assets/${id}` : '/assets'}
                            className="btn-link"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default AssetForm;
