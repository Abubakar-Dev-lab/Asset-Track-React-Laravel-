/**
 * Navbar Component - Top Navigation Bar (Matches Laravel Layout)
 * ==============================================================
 *
 * Features:
 * - Desktop: Full navigation with links
 * - Mobile: Hamburger menu that toggles mobile menu
 * - Role-based navigation (admin vs employee)
 * - User info and logout button
 */

import { useState } from 'react';
import { NavLink, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Navbar() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    // Handle logout
    const handleLogout = async () => {
        await logout();
        navigate('/login');
    };

    // Toggle mobile menu
    const toggleMobileMenu = () => {
        setMobileMenuOpen(!mobileMenuOpen);
    };

    // Close mobile menu when clicking a link
    const closeMobileMenu = () => {
        setMobileMenuOpen(false);
    };

    return (
        <nav className="navbar">
            <div className="navbar-container">
                {/* Left side - Logo and Desktop Navigation */}
                <div className="navbar-left">
                    {/* Logo/Brand */}
                    <Link
                        to={user?.role === 'admin' ? '/dashboard' : '/my-assets'}
                        className="navbar-brand"
                    >
                        AssetGuard
                    </Link>

                    {/* Desktop Navigation Links */}
                    <div className="navbar-links desktop-only">
                        {user?.role === 'admin' ? (
                            <>
                                <NavLink to="/dashboard" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>
                                    Dashboard
                                </NavLink>
                                <NavLink to="/assets" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>
                                    Assets
                                </NavLink>
                                <NavLink to="/users" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>
                                    Users
                                </NavLink>
                                <NavLink to="/categories" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>
                                    Categories
                                </NavLink>
                                <Link to="/assets/create" className="btn btn-primary btn-sm">
                                    + Add Asset
                                </Link>
                            </>
                        ) : (
                            <NavLink to="/my-assets" className={({ isActive }) => isActive ? 'nav-link active' : 'nav-link'}>
                                My Assets
                            </NavLink>
                        )}
                    </div>
                </div>

                {/* Right side - User info and actions */}
                <div className="navbar-right">
                    {/* User info - desktop only */}
                    <Link to="/profile" className="navbar-user desktop-only">
                        <span className="user-name">{user?.name}</span>
                        <span className={`badge badge-sm role-${user?.role}`}>
                            {user?.role === 'admin' ? 'Admin' : 'Employee'}
                        </span>
                    </Link>

                    {/* Logout button */}
                    <button onClick={handleLogout} className="btn-logout">
                        Logout
                    </button>

                    {/* Mobile menu button */}
                    <button
                        className="mobile-menu-btn"
                        onClick={toggleMobileMenu}
                        aria-label="Toggle menu"
                        aria-expanded={mobileMenuOpen}
                    >
                        <svg className="menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            {/* Mobile Navigation Menu */}
            <div className={`mobile-menu ${mobileMenuOpen ? 'open' : ''}`}>
                {user?.role === 'admin' ? (
                    <>
                        <NavLink
                            to="/dashboard"
                            className={({ isActive }) => isActive ? 'mobile-nav-link active' : 'mobile-nav-link'}
                            onClick={closeMobileMenu}
                        >
                            Dashboard
                        </NavLink>
                        <NavLink
                            to="/assets"
                            className={({ isActive }) => isActive ? 'mobile-nav-link active' : 'mobile-nav-link'}
                            onClick={closeMobileMenu}
                        >
                            Assets
                        </NavLink>
                        <NavLink
                            to="/users"
                            className={({ isActive }) => isActive ? 'mobile-nav-link active' : 'mobile-nav-link'}
                            onClick={closeMobileMenu}
                        >
                            Users
                        </NavLink>
                        <NavLink
                            to="/categories"
                            className={({ isActive }) => isActive ? 'mobile-nav-link active' : 'mobile-nav-link'}
                            onClick={closeMobileMenu}
                        >
                            Categories
                        </NavLink>
                        <Link
                            to="/assets/create"
                            className="mobile-nav-link btn-mobile-primary"
                            onClick={closeMobileMenu}
                        >
                            + Add Asset
                        </Link>
                    </>
                ) : (
                    <NavLink
                        to="/my-assets"
                        className={({ isActive }) => isActive ? 'mobile-nav-link active' : 'mobile-nav-link'}
                        onClick={closeMobileMenu}
                    >
                        My Assets
                    </NavLink>
                )}
                <NavLink
                    to="/profile"
                    className={({ isActive }) => isActive ? 'mobile-nav-link active' : 'mobile-nav-link'}
                    onClick={closeMobileMenu}
                >
                    Profile ({user?.name})
                </NavLink>
            </div>
        </nav>
    );
}

export default Navbar;
