/**
 * App.jsx - Main Application Component with Routing
 * ==================================================
 *
 * Routes match Laravel Blade pages exactly:
 * - /login - Login page
 * - /dashboard - Admin dashboard
 * - /assets - Asset list
 * - /assets/create - Create asset form
 * - /assets/:id - Asset detail
 * - /assets/:id/edit - Edit asset form
 * - /categories - Category list
 * - /categories/create - Create category form
 * - /categories/:id/edit - Edit category form
 * - /users - User list
 * - /users/create - Create user form
 * - /users/:id - User detail
 * - /users/:id/edit - Edit user form
 * - /profile - Edit profile
 * - /my-assets - Employee's assigned assets
 */

import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';

// Context Providers
import { AuthProvider } from './context/AuthContext';
import { ToastProvider } from './context/ToastContext';

// Components
import Layout from './components/Layout';
import PrivateRoute from './components/PrivateRoute';
import AdminRoute from './components/AdminRoute';
import HomeRedirect from './components/HomeRedirect';

// Pages
import Login from './pages/Login';
import Register from './pages/Register';
import Dashboard from './pages/Dashboard';
import Assets from './pages/Assets';
import AssetDetail from './pages/AssetDetail';
import AssetForm from './pages/AssetForm';
import Categories from './pages/Categories';
import CategoryForm from './pages/CategoryForm';
import Users from './pages/Users';
import UserDetail from './pages/UserDetail';
import UserForm from './pages/UserForm';
import Profile from './pages/Profile';
import MyAssets from './pages/MyAssets';

// Styles
import './App.css';

function App() {
    return (
        <AuthProvider>
            <ToastProvider>
                <BrowserRouter>
                    <Routes>
                        {/* Public Routes */}
                        <Route path="/login" element={<Login />} />
                        <Route path="/register" element={<Register />} />

                        {/* Protected Routes - wrapped in Layout */}
                        <Route
                            element={
                                <PrivateRoute>
                                    <Layout />
                                </PrivateRoute>
                            }
                        >
                            {/* Dashboard - Admin Only */}
                            <Route path="/dashboard" element={<AdminRoute><Dashboard /></AdminRoute>} />

                            {/* Assets - Admin Only */}
                            <Route path="/assets" element={<AdminRoute><Assets /></AdminRoute>} />
                            <Route path="/assets/create" element={<AdminRoute><AssetForm /></AdminRoute>} />
                            <Route path="/assets/:id" element={<AdminRoute><AssetDetail /></AdminRoute>} />
                            <Route path="/assets/:id/edit" element={<AdminRoute><AssetForm /></AdminRoute>} />

                            {/* Categories - Admin Only */}
                            <Route path="/categories" element={<AdminRoute><Categories /></AdminRoute>} />
                            <Route path="/categories/create" element={<AdminRoute><CategoryForm /></AdminRoute>} />
                            <Route path="/categories/:id/edit" element={<AdminRoute><CategoryForm /></AdminRoute>} />

                            {/* Users - Admin Only */}
                            <Route path="/users" element={<AdminRoute><Users /></AdminRoute>} />
                            <Route path="/users/create" element={<AdminRoute><UserForm /></AdminRoute>} />
                            <Route path="/users/:id" element={<AdminRoute><UserDetail /></AdminRoute>} />
                            <Route path="/users/:id/edit" element={<AdminRoute><UserForm /></AdminRoute>} />

                            {/* Profile - All authenticated users */}
                            <Route path="/profile" element={<Profile />} />

                            {/* My Assets - All authenticated users (Employee view) */}
                            <Route path="/my-assets" element={<MyAssets />} />
                        </Route>

                        {/* Role-based Home Redirect */}
                        <Route path="/" element={<HomeRedirect />} />
                        <Route path="*" element={<HomeRedirect />} />
                    </Routes>
                </BrowserRouter>
            </ToastProvider>
        </AuthProvider>
    );
}

export default App;
