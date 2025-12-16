/**
 * Categories Page - Category Management (Matches Laravel Blade)
 * =============================================================
 *
 * Features (matching Laravel exactly):
 * - Table view only (no mobile cards - Laravel is table only)
 * - Pagination
 * - Edit, Delete actions
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { categoriesAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import Pagination from '../components/Pagination';
import ConfirmDialog from '../components/ConfirmDialog';

function Categories() {
    const { success, error } = useToast();

    // Data state
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);

    // Pagination
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);

    // Delete confirm
    const [deleteConfirm, setDeleteConfirm] = useState({ show: false, category: null });
    const [deleting, setDeleting] = useState(false);

    // Fetch categories
    useEffect(() => {
        fetchCategories();
    }, [currentPage]);

    const fetchCategories = async () => {
        try {
            setLoading(true);
            const response = await categoriesAPI.getAll({
                page: currentPage,
                per_page: 15,
            });

            if (response.data.data) {
                setCategories(response.data.data);
                if (response.data.meta) {
                    setTotalPages(response.data.meta.last_page || 1);
                }
            } else {
                setCategories(response.data);
            }
        } catch (err) {
            console.error('Error fetching categories:', err);
            error('Failed to load categories');
        } finally {
            setLoading(false);
        }
    };

    // Handle delete
    const handleDelete = async () => {
        if (!deleteConfirm.category) return;

        setDeleting(true);
        try {
            await categoriesAPI.delete(deleteConfirm.category.id);
            success('Category deleted successfully');
            setDeleteConfirm({ show: false, category: null });
            fetchCategories();
        } catch (err) {
            console.error('Error deleting category:', err);
            error(err.response?.data?.message || 'Failed to delete category');
        } finally {
            setDeleting(false);
        }
    };

    if (loading && categories.length === 0) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading categories...</p>
            </div>
        );
    }

    return (
        <div className="categories-page">
            {/* Page Header */}
            <div className="page-header">
                <div>
                    <h1>Categories</h1>
                    <p className="page-subtitle">Manage asset categories</p>
                </div>
                <Link to="/categories/create" className="btn btn-primary">
                    + Add New Category
                </Link>
            </div>

            {/* Table */}
            <div className="table-card">
                <table className="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Assets Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {categories.length > 0 ? (
                            categories.map((category) => (
                                <tr key={category.id}>
                                    <td>{category.id}</td>
                                    <td className="category-name">{category.name}</td>
                                    <td className="category-slug">{category.slug}</td>
                                    <td>{category.assets_count || 0}</td>
                                    <td className="actions-cell">
                                        <Link to={`/categories/${category.id}/edit`} className="action-link edit">
                                            Edit
                                        </Link>
                                        <button
                                            onClick={() => setDeleteConfirm({ show: true, category })}
                                            className="action-link delete"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="5" className="no-data">
                                    No categories found. Create one to get started.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
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
                title="Delete Category?"
                message={`Are you sure you want to delete "${deleteConfirm.category?.name}"?`}
                confirmText="Delete"
                confirmVariant="danger"
                onConfirm={handleDelete}
                onCancel={() => setDeleteConfirm({ show: false, category: null })}
                loading={deleting}
            />
        </div>
    );
}

export default Categories;
