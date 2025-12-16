# AssetTrack - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- ✅ PHP 8.2+ installed
- ✅ Composer installed
- ✅ MySQL running (XAMPP)
- ✅ Node.js & NPM installed

### Application Setup

The application is **already configured and ready to use**. All setup steps have been completed:

```bash
✅ Dependencies installed (composer install, npm install)
✅ Environment configured (.env file)
✅ Database connected (assetlara)
✅ Migrations run
✅ Storage symlink created
✅ Test data seeded
```

---

## 🎯 Access the Application

### 1. Start the Server

```bash
cd /opt/lampp/htdocs/projects/assetTrack/assetlara
php artisan serve
```

The application will be available at: **http://localhost:8000**

### 2. Login Credentials

**Admin Account** (Full Access):
- Email: `admin@example.com`
- Password: `password`

**Employee Accounts** (Limited Access):
- Email: `john@example.com` / Password: `password`
- Email: `jane@example.com` / Password: `password`

---

## 📋 Quick Feature Tour

### As Admin

1. **Login** → `http://localhost:8000/login`
   - Use admin credentials
   - You'll be redirected to `/dashboard`

2. **View All Assets** → `/assets`
   - See list of all assets with images
   - Edit, View, or Delete any asset

3. **Create New Asset** → `/assets/create`
   - Fill in asset details
   - Upload an image (optional)
   - Click "Save Asset"

4. **View Asset Details** → Click "View" on any asset
   - See full details and large image
   - Assign to employee (if available)
   - Mark as returned (if assigned)

5. **Edit Asset** → Click "Edit" on any asset
   - Update details
   - Replace image
   - Change status

6. **Delete Asset** → Click "Delete" on any asset
   - Only works if asset is not assigned
   - Soft delete (recoverable)

7. **Assign Asset** → From asset detail page
   - Select employee from dropdown
   - Click "Check Out"
   - Status changes to "assigned"

8. **Return Asset** → From asset detail page
   - Click "Check In (Mark as Available)"
   - Status changes back to "available"

### As Employee

1. **Login** → `http://localhost:8000/login`
   - Use employee credentials
   - You'll be redirected to `/my-assets`

2. **View My Assets** → `/my-assets`
   - See only assets assigned to you
   - View images, serial numbers, assigned dates
   - Cannot edit or delete

3. **Restricted Access**
   - ❌ Cannot access `/assets` (admin only)
   - ❌ Cannot access `/dashboard` (admin only)
   - ✅ Can only view own assigned assets

---

## 🗂️ Sample Data Available

### Users
| Name | Email | Password | Role |
|------|-------|----------|------|
| Admin User | admin@example.com | password | admin |
| John Doe | john@example.com | password | employee |
| Jane Smith | jane@example.com | password | employee |

### Categories
- Laptops
- Monitors
- Keyboards
- Mice
- Phones
- Tablets

### Sample Assets
| Name | Serial Number | Category | Status |
|------|---------------|----------|--------|
| Dell Latitude 5420 | DL-LAT-001 | Laptops | available |
| MacBook Pro 14" | AP-MBP-001 | Laptops | available |
| LG UltraWide 34" | LG-UW-001 | Monitors | available |

---

## 🖼️ Image Upload Feature

### How to Upload Images

1. **Create New Asset**:
   - Go to `/assets/create`
   - Fill in details
   - Click "Choose File" under "Asset Image"
   - Select image (JPEG, PNG, or WEBP, max 2MB)
   - Submit form

2. **Edit Existing Asset**:
   - Go to asset detail page
   - Click "Edit"
   - Current image shown (if exists)
   - Choose new file to replace
   - Submit form (old image auto-deleted)

### Where Images are Stored

- **Physical Location**: `storage/app/public/assets/`
- **Public URL**: `http://localhost:8000/storage/assets/{filename}`
- **Symlink**: `public/storage` → `storage/app/public`

### Image Display

- **Index Page**: 64x64px thumbnails
- **Show Page**: 264x264px full image
- **My Assets**: 64x64px thumbnails
- **No Image**: Gray placeholder with "No image" text

---

## 🔒 Permission Matrix

| Action | Admin | Employee | Guest |
|--------|-------|----------|-------|
| Login | ✅ | ✅ | ✅ |
| View All Assets | ✅ | ❌ | ❌ |
| View Own Assets | ✅ | ✅ | ❌ |
| Create Asset | ✅ | ❌ | ❌ |
| Edit Asset | ✅ | ❌ | ❌ |
| Delete Asset | ✅ | ❌ | ❌ |
| Assign Asset | ✅ | ❌ | ❌ |
| Return Asset | ✅ | ❌ | ❌ |
| Dashboard | ✅ | ❌ | ❌ |

---

## 🧪 Testing Workflow

### Complete CRUD Test

1. **Create**
   ```
   Login as admin
   → Go to /assets
   → Click "+ Add New Asset"
   → Enter: Name="Test Laptop", SN="TEST-001", Category=Laptops, Status=available
   → Upload image (optional)
   → Click "Save Asset"
   → Verify redirect to /assets with success message
   → Verify asset appears in list
   ```

2. **Read**
   ```
   Click "View" on the new asset
   → Verify all details display correctly
   → Verify image shows (if uploaded)
   → Verify status badge color
   ```

3. **Update**
   ```
   Click "Edit" button
   → Change name to "Updated Test Laptop"
   → Replace image (optional)
   → Click "Update Asset"
   → Verify changes saved
   → Verify old image deleted (if replaced)
   ```

4. **Delete**
   ```
   Go back to /assets
   → Click "Delete" on test asset
   → Confirm deletion
   → Verify asset removed from list
   → Verify soft delete (data still in database with deleted_at)
   ```

### Assignment Workflow Test

1. **Assign Asset**
   ```
   Login as admin
   → Go to /assets
   → Click "View" on any available asset
   → Select "John Doe" from dropdown
   → Click "Check Out"
   → Verify status changes to "assigned"
   → Verify success message
   ```

2. **Employee View**
   ```
   Logout
   → Login as john@example.com
   → Verify redirect to /my-assets
   → Verify assigned asset appears in list
   → Verify image displays
   ```

3. **Return Asset**
   ```
   Logout
   → Login as admin
   → Go to assigned asset detail page
   → Click "Check In (Mark as Available)"
   → Verify status changes to "available"
   → Logout
   → Login as john@example.com
   → Verify asset no longer in /my-assets
   ```

### Security Test

1. **Admin Bypass**
   ```
   Try to delete assigned asset
   → Should show error: "Cannot delete an asset that is currently assigned"
   ```

2. **Employee Restriction**
   ```
   Login as employee
   → Try to access /assets directly
   → Should get 403 Forbidden
   → Try to access /dashboard
   → Should get 403 Forbidden
   ```

3. **Throttle Test**
   ```
   Go to /login
   → Enter wrong password 5 times quickly
   → Should get throttled: "Too many login attempts"
   → Wait 1 minute to retry
   ```

---

## 📡 API Access

### Get Authenticated User
```bash
GET /api/user
Headers: Authorization: Bearer {token}
```

### Get All Assets
```bash
GET /api/assets
Headers: Authorization: Bearer {token}
```

**Note**: You need to authenticate with Laravel Sanctum to get a token first.

---

## 🔧 Useful Commands

### Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### View All Routes
```bash
php artisan route:list
```

### Run Migrations
```bash
php artisan migrate
```

### Seed Database (Fresh Data)
```bash
php artisan migrate:fresh --seed
```

### Check Application Status
```bash
php artisan about
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

---

## 🐛 Troubleshooting

### Issue: Images Not Displaying

**Solution**:
```bash
php artisan storage:link
```
Then refresh page.

### Issue: Login Redirects to Same Page

**Solution**: Clear sessions
```bash
php artisan session:clear
rm -rf storage/framework/sessions/*
```

### Issue: 500 Error

**Solution**: Check logs
```bash
tail -n 50 storage/logs/laravel.log
```

Enable debug mode in `.env`:
```
APP_DEBUG=true
```

### Issue: Database Connection Error

**Solution**: Check `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assetlara
DB_USERNAME=root
DB_PASSWORD=
```

Ensure MySQL is running in XAMPP.

### Issue: Permission Denied

**Solution**: Fix storage permissions
```bash
chmod -R 775 storage bootstrap/cache
```

---

## 📊 Database Schema

### users
- id, name, email, password, role, is_active, timestamps, soft_deletes

### categories
- id, name, slug (unique), timestamps

### assets
- id, category_id, name, serial_number (unique), status, image_path, timestamps, soft_deletes

### assignments
- id, user_id, asset_id, assigned_by, assigned_at, returned_at, notes

---

## 🎨 Status Color Codes

| Status | Badge Color | Meaning |
|--------|-------------|---------|
| Available | Green | Ready to assign |
| Assigned | Blue | Currently with employee |
| Broken | Red | Needs repair |
| Maintenance | Yellow | Under maintenance |

---

## 📚 Additional Resources

- **Routes Documentation**: See `ROUTES_CONNECTIVITY_REPORT.md`
- **Improvements Summary**: See `IMPROVEMENTS_SUMMARY.md`
- **Laravel Docs**: https://laravel.com/docs/12.x

---

## ✅ System Health Check

Before using the application, verify:

```bash
# Check application status
php artisan about

# Expected output should show:
# ✅ Storage: LINKED
# ✅ Database: mysql (connected)
# ✅ Debug Mode: ENABLED (for development)
# ✅ Maintenance Mode: OFF
```

---

## 🎉 You're Ready!

The application is fully configured with:
- ✅ All routes working
- ✅ Image upload functional
- ✅ Events dispatching
- ✅ Test data seeded
- ✅ Security measures in place
- ✅ Clean, maintainable code

**Start the server and begin testing!**

```bash
php artisan serve
```

Then visit: **http://localhost:8000**

---

*Happy Asset Tracking! 🚀*
