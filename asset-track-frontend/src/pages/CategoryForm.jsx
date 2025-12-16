/**
 * Category Form Page - Create/Edit Category (Matches Laravel)
 * ============================================================
 *
 * This page handles both creating new categories and editing existing ones.
 * Matches Laravel's categories/create.blade.php and categories/edit.blade.php
 */

import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { categoriesAPI } from '../services/api';
import { useToast } from '../context/ToastContext';

function CategoryForm() {
    const { id } = useParams(); // If id exists, we're editing
    const navigate = useNavigate();
    const { success, error } = useToast();
    const isEditing = Boolean(id);

    // Form state
    const [name, setName] = useState('');
    const [currentSlug, setCurrentSlug] = useState('');
    const [loading, setLoading] = useState(false);
    const [fetching, setFetching] = useState(isEditing);

    // Fetch category data if editing
    useEffect(() => {
        if (isEditing) {
            fetchCategory();
        }
    }, [id]);

    const fetchCategory = async () => {
        try {
            setFetching(true);
            const response = await categoriesAPI.getOne(id);
            const category = response.data.data || response.data;
            setName(category.name);
            setCurrentSlug(category.slug);
        } catch (err) {
            console.error('Error fetching category:', err);
            error('Failed to load category');
            navigate('/categories');
        } finally {
            setFetching(false);
        }
    };

    // Handle form submission
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            if (isEditing) {
                await categoriesAPI.update(id, { name });
                success('Category updated successfully');
            } else {
                await categoriesAPI.create({ name });
                success('Category created successfully');
            }
            navigate('/categories');
        } catch (err) {
            console.error('Error saving category:', err);
            error(err.response?.data?.message || 'Failed to save category');
        } finally {
            setLoading(false);
        }
    };

    if (fetching) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading category...</p>
            </div>
        );
    }

    return (
        <div className="form-page">
            {/* Breadcrumb */}
            <div className="breadcrumb">
                <Link to="/categories">← Back to Categories</Link>
            </div>

            {/* Form Card */}
            <div className="form-card form-card-sm">
                <h1>{isEditing ? 'Edit Category' : 'Add New Category'}</h1>

                <form onSubmit={handleSubmit}>
                    {/* Category Name */}
                    <div className="form-group">
                        <label htmlFor="name">Category Name</label>
                        <input
                            type="text"
                            id="name"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder="e.g., Laptops, Monitors, Furniture"
                            required
                        />
                        <p className="hint">
                            {isEditing && currentSlug
                                ? `Current slug: ${currentSlug}`
                                : 'The slug will be automatically generated from the name'}
                        </p>
                    </div>

                    {/* Submit Button */}
                    <div className="form-actions">
                        <button
                            type="submit"
                            className="btn btn-success"
                            disabled={loading}
                        >
                            {loading ? 'Saving...' : (isEditing ? 'Update Category' : 'Save Category')}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default CategoryForm;
