/**
 * Layout Component - Main App Structure (Matches Laravel Layout)
 * ==============================================================
 *
 * This component provides the main layout structure matching Laravel's app.blade.php:
 * - Top navbar with navigation (no sidebar - Laravel uses top nav)
 * - Main content area
 * - Toast notifications
 */

import { Outlet } from 'react-router-dom';
import Navbar from './Navbar';
import Toast from './Toast';

function Layout() {
    return (
        <div className="app-layout">
            {/* Toast Notifications Container */}
            <Toast />

            {/* Top Navigation Bar */}
            <Navbar />

            {/* Main Content Area */}
            <main className="main-content">
                <div className="content-container">
                    <Outlet />
                </div>
            </main>
        </div>
    );
}

export default Layout;
