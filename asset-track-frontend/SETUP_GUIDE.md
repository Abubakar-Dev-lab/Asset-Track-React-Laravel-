# Setup Guide: Running React + Laravel Together

This guide explains how to configure and run both projects together.

---

## Prerequisites

Make sure you have installed:
- Node.js (v18 or higher)
- PHP (v8.2 or higher)
- Composer
- MySQL

---

## Step 1: Setup Laravel Backend

### 1.1 Navigate to Laravel project

```bash
cd /opt/lampp/htdocs/projects/assetTrack/assetlara
```

### 1.2 Install dependencies (if not done)

```bash
composer install
```

### 1.3 Create/Update .env file

Make sure your `.env` file has these settings:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assetlara
DB_USERNAME=root
DB_PASSWORD=

# Sanctum settings (for API authentication)
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,127.0.0.1,127.0.0.1:5173
```

### 1.4 Run migrations (if database is empty)

```bash
php artisan migrate
```

### 1.5 Seed database with test data (optional)

```bash
php artisan db:seed
```

### 1.6 Start Laravel server

```bash
php artisan serve
```

Laravel will run at: **http://127.0.0.1:8000**

Keep this terminal open!

---

## Step 2: Setup React Frontend

### 2.1 Open a NEW terminal and navigate to React project

```bash
cd /opt/lampp/htdocs/projects/assetTrack/asset-track-frontend
```

### 2.2 Install dependencies (if not done)

```bash
npm install
```

### 2.3 Verify API URL in api.js

Open `src/services/api.js` and check the base URL:

```javascript
const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',  // Must match Laravel's URL
    // ...
});
```

### 2.4 Start React development server

```bash
npm run dev
```

React will run at: **http://localhost:5173**

---

## Step 3: Test the Connection

1. Open browser: **http://localhost:5173**
2. You should see the login page
3. Try logging in with test credentials:
   - Email: admin@example.com
   - Password: password

If login works, the connection is successful!

---

## CORS Configuration (Already Done)

CORS (Cross-Origin Resource Sharing) allows React (port 5173) to talk to Laravel (port 8000).

The configuration is in Laravel: `config/cors.php`

```php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',    // React dev server
        'http://127.0.0.1:5173',
    ],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
```

---

## Running Both Projects

You need **TWO terminal windows**:

### Terminal 1: Laravel Backend
```bash
cd /opt/lampp/htdocs/projects/assetTrack/assetlara
php artisan serve
```
Output: `Server running on [http://127.0.0.1:8000]`

### Terminal 2: React Frontend
```bash
cd /opt/lampp/htdocs/projects/assetTrack/asset-track-frontend
npm run dev
```
Output: `Local: http://localhost:5173/`

---

## Project Structure Overview

```
assetTrack/
│
├── assetlara/                      # LARAVEL BACKEND
│   ├── app/
│   │   ├── Http/Controllers/Api/   # API controllers
│   │   ├── Models/                 # Database models
│   │   └── Services/               # Business logic
│   ├── config/
│   │   └── cors.php                # CORS settings
│   ├── routes/
│   │   └── api.php                 # API routes
│   └── .env                        # Environment variables
│
└── asset-track-frontend/           # REACT FRONTEND
    ├── src/
    │   ├── services/
    │   │   └── api.js              # API connection
    │   ├── context/
    │   │   └── AuthContext.jsx     # Auth state
    │   ├── components/
    │   │   ├── Layout.jsx          # App layout
    │   │   ├── Navbar.jsx          # Top nav
    │   │   ├── Sidebar.jsx         # Side nav
    │   │   └── PrivateRoute.jsx    # Route guard
    │   ├── pages/
    │   │   ├── Login.jsx           # Login page
    │   │   ├── Dashboard.jsx       # Dashboard
    │   │   ├── Assets.jsx          # Assets CRUD
    │   │   ├── Categories.jsx      # Categories
    │   │   ├── Users.jsx           # Users
    │   │   └── Assignments.jsx     # Assignments
    │   ├── App.jsx                 # Main app + routes
    │   └── App.css                 # Styles
    └── package.json                # Dependencies
```

---

## Troubleshooting

### Problem: "CORS Error" in browser console

**Solution**: Make sure Laravel's `config/cors.php` includes React's URL:
```php
'allowed_origins' => [
    'http://localhost:5173',
],
```

Then clear Laravel config cache:
```bash
php artisan config:clear
```

### Problem: "401 Unauthorized" when logged in

**Solution**: Check that:
1. Token is saved in localStorage (check browser DevTools > Application > Local Storage)
2. Token is being sent with requests (check Network tab > request headers)

### Problem: "Connection refused"

**Solution**: Make sure Laravel is running:
```bash
php artisan serve
```

### Problem: "404 Not Found" on API calls

**Solution**: Check that:
1. Laravel is running on port 8000
2. The API endpoint exists (check `routes/api.php` in Laravel)
3. The URL in `api.js` is correct

### Problem: React page is blank

**Solution**: Check browser console (F12) for JavaScript errors.

---

## Creating Test Data

If your database is empty, create an admin user:

### Option 1: Use Laravel Tinker

```bash
cd /opt/lampp/htdocs/projects/assetTrack/assetlara
php artisan tinker
```

Then in tinker:
```php
\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'admin',
    'is_active' => true,
]);
```

### Option 2: Create a Seeder

This is already set up. Run:
```bash
php artisan db:seed
```

---

## Development Workflow

1. Make changes to React code
2. Vite automatically reloads the page (Hot Module Replacement)
3. Test in browser
4. Check Network tab for API calls
5. Check Laravel logs if something fails: `storage/logs/laravel.log`

---

## Building for Production

When ready to deploy:

### React Build
```bash
cd asset-track-frontend
npm run build
```
This creates a `dist/` folder with optimized files.

### Update API URL

For production, update `src/services/api.js`:
```javascript
baseURL: 'https://your-production-domain.com/api'
```

And update Laravel's `config/cors.php`:
```php
'allowed_origins' => [
    'https://your-frontend-domain.com',
],
```

---

## Quick Start Commands

```bash
# Terminal 1 - Start Laravel
cd /opt/lampp/htdocs/projects/assetTrack/assetlara
php artisan serve

# Terminal 2 - Start React
cd /opt/lampp/htdocs/projects/assetTrack/asset-track-frontend
npm run dev

# Open browser
# http://localhost:5173
```

That's it! Happy coding!
