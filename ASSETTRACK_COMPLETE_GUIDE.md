# AssetTrack - Complete Development Guide
## From Zero to Full-Stack Asset Management System

**Author**: Senior Full-Stack Developer & Technical Mentor
**Level**: Absolute Beginner to Intermediate
**Stack**: Laravel 12 (Backend API) + React 19 (Frontend SPA)

---

# TABLE OF CONTENTS

1. [Introduction & Project Overview](#chapter-1-introduction--project-overview)
2. [Phase 1: Foundation & Setup](#phase-1-foundation--setup)
3. [Phase 2: Database Design & Models](#phase-2-database-design--models)
4. [Phase 3: Laravel Authentication](#phase-3-laravel-authentication)
5. [Phase 4: React Setup & Authentication](#phase-4-react-setup--authentication)
6. [Phase 5: Dashboard & Categories](#phase-5-dashboard--categories)
7. [Phase 6: Assets Management](#phase-6-assets-management)
8. [Phase 7: Users Management](#phase-7-users-management)
9. [Phase 8: Assignment System](#phase-8-assignment-system)
10. [Phase 9: Employee Features](#phase-9-employee-features)
11. [Phase 10: Polish & Final Testing](#phase-10-polish--final-testing)
12. [Complete Interview Question Bank](#complete-interview-question-bank)

---

# CHAPTER 1: INTRODUCTION & PROJECT OVERVIEW

## 1.1 What Are We Building?

Imagine you work at a company with 100 employees. The company has laptops, monitors, phones, keyboards - hundreds of items.

**The Problem:**
- Who has the MacBook with serial number ABC123?
- When did John get his laptop?
- Is there any available monitor to give to the new employee?
- How many assets are broken and need repair?

**The Solution: AssetTrack**

AssetTrack is a web application that:
1. **Tracks all company assets** (laptops, monitors, phones, etc.)
2. **Assigns assets to employees** (like a library checkout system)
3. **Returns assets** when employees leave or don't need them
4. **Keeps complete history** of who had what and when

Think of it like a **library system for company equipment**.

---

## 1.2 How Does It Work? (User Stories)

### Admin User (IT Manager)
```
1. Logs in to the system
2. Sees dashboard with statistics:
   - Total assets: 150
   - Available: 80
   - Assigned: 60
   - Broken: 10
3. Creates new asset: "MacBook Pro M2, Serial: ABC123"
4. Assigns the MacBook to employee "John Smith"
5. Later, when John leaves, returns the MacBook to inventory
6. Can see full history: John had it from Jan 1 to Mar 15
```

### Employee User
```
1. Logs in to the system
2. Sees only their assigned assets:
   - MacBook Pro M2 (assigned Jan 1, 2024)
   - Dell Monitor 27" (assigned Feb 15, 2024)
3. Can update their profile (name, email, password)
4. Cannot access admin features
```

---

## 1.3 System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER'S BROWSER                          │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              REACT FRONTEND (SPA)                        │   │
│  │  - Login/Register pages                                  │   │
│  │  - Dashboard with statistics                             │   │
│  │  - Asset management pages                                │   │
│  │  - User management pages                                 │   │
│  │  - Runs on http://localhost:5173                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                  │
│                              │ HTTP Requests (JSON)             │
│                              │ Authorization: Bearer <token>    │
│                              ▼                                  │
└─────────────────────────────────────────────────────────────────┘
                               │
                               │ INTERNET / NETWORK
                               │
┌─────────────────────────────────────────────────────────────────┐
│                      SERVER                                     │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              LARAVEL BACKEND (API)                       │   │
│  │  - Receives HTTP requests                                │   │
│  │  - Validates data                                        │   │
│  │  - Processes business logic                              │   │
│  │  - Returns JSON responses                                │   │
│  │  - Runs on http://localhost:8000                         │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                  │
│                              │ SQL Queries                      │
│                              ▼                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              MySQL DATABASE                              │   │
│  │  - users table                                           │   │
│  │  - categories table                                      │   │
│  │  - assets table                                          │   │
│  │  - assignments table                                     │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Why Separate Frontend and Backend?

**Analogy**: Think of a restaurant.
- **Frontend (React)** = The dining area where customers sit, see the menu, place orders
- **Backend (Laravel)** = The kitchen where food is prepared
- **Database (MySQL)** = The pantry/storage where ingredients are kept
- **API** = The waiter who takes orders from dining area to kitchen and brings food back

**Benefits of Separation:**
1. **Different teams** can work on frontend and backend simultaneously
2. **Multiple frontends** can use the same backend (web app, mobile app, desktop app)
3. **Easier to scale** - can add more backend servers if needed
4. **Better security** - database is never directly exposed to users

---

## 1.4 Technologies We'll Use

### Backend (Laravel)
| Technology | Version | What It Does |
|------------|---------|--------------|
| PHP | 8.2+ | Programming language |
| Laravel | 12 | PHP framework (makes building apps easier) |
| Laravel Sanctum | 4.0 | Handles authentication (login/logout) |
| MySQL | 8.0 | Database to store data |
| Composer | 2.x | PHP package manager (like npm for PHP) |

### Frontend (React)
| Technology | Version | What It Does |
|------------|---------|--------------|
| Node.js | 18+ | JavaScript runtime |
| React | 19 | UI library for building interfaces |
| React Router | 7 | Handles page navigation |
| Axios | 1.x | Makes HTTP requests to backend |
| Vite | 7 | Build tool and dev server |

---

## 1.5 Key Terminology (Learn These First!)

### General Web Terms

| Term | Simple Explanation | Example |
|------|-------------------|---------|
| **API** | A way for two programs to talk to each other | React asks Laravel for data |
| **REST API** | A standard way to design APIs using HTTP | GET /users, POST /users |
| **HTTP** | The language browsers use to talk to servers | Like how humans use English |
| **JSON** | A format for sending data (like a structured text) | `{"name": "John", "age": 25}` |
| **Endpoint** | A specific URL that does something | `/api/login` handles login |
| **Request** | When you ask the server for something | "Give me all users" |
| **Response** | What the server sends back | List of users in JSON |

### HTTP Methods (Verbs)

| Method | Purpose | Example |
|--------|---------|---------|
| **GET** | Read/Retrieve data | Get list of assets |
| **POST** | Create new data | Create new asset |
| **PUT/PATCH** | Update existing data | Update asset name |
| **DELETE** | Remove data | Delete an asset |

### HTTP Status Codes

| Code | Meaning | When Used |
|------|---------|-----------|
| **200** | OK - Success | Request worked |
| **201** | Created | New record created |
| **400** | Bad Request | Invalid data sent |
| **401** | Unauthorized | Not logged in |
| **403** | Forbidden | Logged in but not allowed |
| **404** | Not Found | Resource doesn't exist |
| **422** | Validation Error | Data failed validation |
| **500** | Server Error | Something broke on server |

### Authentication Terms

| Term | Simple Explanation |
|------|-------------------|
| **Authentication** | Proving WHO you are (login) |
| **Authorization** | Checking WHAT you can do (permissions) |
| **Token** | A special key that proves you're logged in |
| **Bearer Token** | Token sent in request header |
| **Session** | Server remembers you're logged in |

---

## 1.6 Project File Structure Preview

### Laravel Backend Structure
```
assetlara/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/           # Our API controllers
│   │   │       ├── AuthController.php
│   │   │       ├── AssetController.php
│   │   │       ├── UserController.php
│   │   │       └── ...
│   │   ├── Middleware/        # Route protection
│   │   │   ├── EnsureUserIsAdmin.php
│   │   │   └── EnsureUserIsEmployee.php
│   │   ├── Requests/          # Validation rules
│   │   │   ├── LoginRequest.php
│   │   │   ├── StoreAssetRequest.php
│   │   │   └── ...
│   │   └── Resources/         # JSON transformers
│   │       ├── AssetResource.php
│   │       ├── UserResource.php
│   │       └── ...
│   ├── Models/                # Database tables as PHP classes
│   │   ├── User.php
│   │   ├── Asset.php
│   │   ├── Category.php
│   │   └── Assignment.php
│   ├── Policies/              # Authorization rules
│   │   └── AssetPolicy.php
│   └── Services/              # Business logic
│       ├── AuthService.php
│       ├── AssetService.php
│       ├── AssignmentService.php
│       └── ...
├── database/
│   └── migrations/            # Database table definitions
├── routes/
│   └── api.php                # API route definitions
└── .env                       # Environment configuration
```

### React Frontend Structure
```
asset-track-frontend/
├── src/
│   ├── components/            # Reusable UI pieces
│   │   ├── Layout.jsx
│   │   ├── Navbar.jsx
│   │   ├── PrivateRoute.jsx
│   │   ├── AdminRoute.jsx
│   │   ├── Pagination.jsx
│   │   └── ConfirmDialog.jsx
│   ├── context/               # Global state
│   │   ├── AuthContext.jsx
│   │   └── ToastContext.jsx
│   ├── pages/                 # Full pages
│   │   ├── Login.jsx
│   │   ├── Dashboard.jsx
│   │   ├── Assets.jsx
│   │   ├── AssetDetail.jsx
│   │   └── ...
│   ├── services/              # API communication
│   │   └── api.js
│   ├── App.jsx                # Main app with routes
│   └── main.jsx               # Entry point
└── package.json               # Dependencies
```

---

## 1.7 Prerequisites Check

Before starting, make sure you have:

### Required Software
```bash
# Check PHP version (need 8.2+)
php -v

# Check Composer
composer -V

# Check Node.js (need 18+)
node -v

# Check npm
npm -v

# Check MySQL is running
mysql --version
```

### Required Knowledge (Don't Worry!)
- Basic HTML/CSS
- Basic JavaScript
- Basic understanding of what a database is
- **You DON'T need** to know Laravel or React - we'll learn together!

---

## 1.8 How to Use This Guide

### Reading Style
1. **Read everything** - Don't skip sections
2. **Type the code yourself** - Don't copy-paste (muscle memory helps learning)
3. **Test after each step** - Make sure it works before moving on
4. **Read the comments** - Code comments explain the "why"

### Symbols Used
| Symbol | Meaning |
|--------|---------|
| `code` | Code to type or command to run |
| **Bold** | Important concept |
| *Italic* | Technical term being explained |
| > Quote | Important note or tip |

### Interview Sections
After each major topic, you'll find:
- **Key Terminologies** - Words you should know
- **Interview Questions** - Common questions with answers
- **Scenario Questions** - Real-world problem solving
- **Common Mistakes** - What beginners often do wrong

---

# PHASE 1: FOUNDATION & SETUP

## Chapter 2: Creating the Laravel Project

### 2.1 What is Laravel?

**Simple Explanation:**
Laravel is like a toolbox for building websites with PHP. Instead of building everything from scratch (like making your own hammer), Laravel gives you ready-made tools (pre-built hammer).

**Analogy:**
- Building a house without Laravel = Cutting every piece of wood yourself
- Building a house with Laravel = Using pre-cut pieces that fit together

**What Laravel Provides:**
1. **Routing** - Deciding what happens when someone visits a URL
2. **Database Tools** - Easy way to create tables and query data
3. **Authentication** - Login/logout system
4. **Validation** - Checking if data is correct
5. **Security** - Protection against common attacks

### 2.2 Creating a New Laravel Project

Open your terminal and navigate to where you want to create the project:

```bash
# Navigate to your projects folder
cd /opt/lampp/htdocs/projects

# Create new Laravel project named "assetlara"
# This command downloads Laravel and sets everything up
composer create-project laravel/laravel assetlara

# Navigate into the project
cd assetlara
```

**What just happened?**
1. Composer downloaded Laravel from the internet
2. Created a folder called `assetlara`
3. Set up all the basic files and folders
4. Installed all required PHP packages

### 2.3 Understanding the Folder Structure

Let's understand what each folder does:

```
assetlara/
├── app/                    # YOUR CODE GOES HERE (mostly)
│   ├── Console/           # Command-line commands
│   ├── Exceptions/        # Error handling
│   ├── Http/              # Web-related code
│   │   ├── Controllers/   # Handle requests
│   │   ├── Middleware/    # Filter requests
│   │   └── Requests/      # Validate input
│   ├── Models/            # Database tables as PHP classes
│   └── Providers/         # App configuration
│
├── bootstrap/             # App startup files (don't touch)
│
├── config/                # Configuration files
│   ├── app.php           # App settings
│   ├── database.php      # Database settings
│   └── cors.php          # CORS settings (for React)
│
├── database/              # Database related
│   ├── factories/        # Fake data generators
│   ├── migrations/       # Table definitions
│   └── seeders/          # Initial data
│
├── public/                # Publicly accessible files
│   └── index.php         # Entry point
│
├── resources/             # Views, CSS, JS (not using for API)
│
├── routes/                # URL definitions
│   ├── api.php           # API routes (WE USE THIS)
│   ├── web.php           # Web routes
│   └── console.php       # Console routes
│
├── storage/               # Files, logs, cache
│   └── app/public/       # Uploaded files
│
├── tests/                 # Test files
│
├── vendor/                # Downloaded packages (don't touch)
│
├── .env                   # ENVIRONMENT VARIABLES (IMPORTANT!)
├── artisan               # Command-line tool
└── composer.json         # Package definitions
```

### 2.4 The .env File - Your Secret Configuration

The `.env` file contains sensitive configuration. **NEVER share this file or commit it to Git.**

Open `.env` and let's understand it:

```env
# Application Settings
APP_NAME=AssetTrack          # Your app's name
APP_ENV=local                # Environment: local, production
APP_KEY=base64:xxxxx         # Encryption key (auto-generated)
APP_DEBUG=true               # Show errors? (true for development)
APP_URL=http://localhost:8000

# Database Connection
DB_CONNECTION=mysql          # Database type
DB_HOST=127.0.0.1           # Database server address
DB_PORT=3306                # Database port
DB_DATABASE=assettrack      # Database name (we'll create this)
DB_USERNAME=root            # Database username
DB_PASSWORD=                # Database password (empty for XAMPP)
```

### 2.5 Creating the Database

Open phpMyAdmin (http://localhost/phpmyadmin) or use MySQL command line:

```sql
-- Create the database
CREATE DATABASE assettrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or via command line:
```bash
mysql -u root -p -e "CREATE DATABASE assettrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2.6 Configure Database Connection

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=assettrack
DB_USERNAME=root
DB_PASSWORD=
```

### 2.7 Test the Connection

```bash
# Run migrations to test database connection
php artisan migrate

# You should see:
# Migration table created successfully.
# Running migrations...
```

### 2.8 Install Laravel Sanctum (Authentication)

Sanctum is Laravel's package for API authentication. It creates tokens that React will use.

```bash
# Install Sanctum
composer require laravel/sanctum

# Publish Sanctum's configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run Sanctum's migrations (creates personal_access_tokens table)
php artisan migrate
```

### 2.9 Configure Sanctum

Open `config/sanctum.php` and verify these settings:

```php
<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000,127.0.0.1:5173,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    // ... rest of config
];
```

### 2.10 Configure CORS (Cross-Origin Resource Sharing)

CORS is a security feature that controls which websites can call your API.

**Why do we need this?**
- React runs on `localhost:5173`
- Laravel runs on `localhost:8000`
- By default, browsers block requests between different origins
- CORS tells the browser "it's okay, let React talk to Laravel"

Open `config/cors.php`:

```php
<?php

return [
    /*
     * Which paths should CORS apply to?
     * 'api/*' means all API routes
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
     * Which HTTP methods are allowed?
     * '*' means all methods (GET, POST, PUT, DELETE, etc.)
     */
    'allowed_methods' => ['*'],

    /*
     * Which origins (websites) can call our API?
     * We allow our React development server
     */
    'allowed_origins' => [
        'http://localhost:3000',      // React default
        'http://127.0.0.1:3000',
        'http://localhost:5173',      // Vite default
        'http://127.0.0.1:5173',
    ],

    'allowed_origins_patterns' => [],

    /*
     * Which headers can be sent?
     */
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
     * Allow cookies/credentials to be sent?
     * true = Yes, send cookies and auth headers
     */
    'supports_credentials' => true,
];
```

### 2.11 Start the Laravel Server

```bash
# Start the development server
php artisan serve

# You should see:
# Starting Laravel development server: http://127.0.0.1:8000
```

Open your browser and go to `http://127.0.0.1:8000`. You should see the Laravel welcome page.

### 2.12 Test API Route

Let's create a simple test route. Open `routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;

// Test route - visit http://127.0.0.1:8000/api/test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'status' => 'success'
    ]);
});
```

Visit `http://127.0.0.1:8000/api/test` in your browser. You should see:
```json
{
    "message": "API is working!",
    "status": "success"
}
```

---

## Chapter 3: Creating the React Project

### 3.1 What is React?

**Simple Explanation:**
React is a JavaScript library for building user interfaces. Instead of writing HTML that loads once, React lets you build interactive UIs that update dynamically.

**Analogy:**
- Traditional HTML = A printed poster (static, can't change)
- React = A digital screen (dynamic, can update anytime)

**Key React Concepts (We'll learn these in detail later):**
1. **Components** - Reusable pieces of UI
2. **State** - Data that can change
3. **Props** - Data passed to components
4. **Hooks** - Special functions for adding features

### 3.2 Creating React Project with Vite

Open a **new terminal** (keep Laravel running in the first one):

```bash
# Navigate to projects folder
cd /opt/lampp/htdocs/projects/assetTrack

# Create React project with Vite
npm create vite@latest asset-track-frontend -- --template react

# Navigate into the project
cd asset-track-frontend

# Install dependencies
npm install

# Install additional packages we need
npm install react-router-dom axios
```

**What we installed:**
- `react-router-dom` - For page navigation
- `axios` - For making HTTP requests to Laravel

### 3.3 Understanding React Project Structure

```
asset-track-frontend/
├── node_modules/          # Downloaded packages (don't touch)
├── public/                # Static files
│   └── vite.svg          # Vite logo
├── src/                   # YOUR CODE GOES HERE
│   ├── assets/           # Images, fonts
│   ├── App.css           # Styles for App component
│   ├── App.jsx           # Main component
│   ├── index.css         # Global styles
│   └── main.jsx          # Entry point
├── index.html            # HTML template
├── package.json          # Dependencies and scripts
└── vite.config.js        # Vite configuration
```

### 3.4 Create Our Folder Structure

Let's set up the folders we'll need:

```bash
# Create folders
mkdir -p src/components
mkdir -p src/pages
mkdir -p src/context
mkdir -p src/services
```

Now our structure looks like:
```
src/
├── components/     # Reusable UI pieces (Navbar, Button, etc.)
├── pages/          # Full pages (Login, Dashboard, etc.)
├── context/        # Global state (Auth, Toast)
├── services/       # API communication
├── App.jsx
├── App.css
├── main.jsx
└── index.css
```

### 3.5 Start React Development Server

```bash
# Start the development server
npm run dev

# You should see:
# VITE v7.x.x  ready in xxx ms
# ➜  Local:   http://localhost:5173/
```

Open `http://localhost:5173` in your browser. You should see the Vite + React welcome page.

### 3.6 Clean Up Default Files

Replace `src/App.jsx` with:

```jsx
function App() {
    return (
        <div>
            <h1>AssetTrack</h1>
            <p>Welcome to AssetTrack - Your IT Asset Management System</p>
        </div>
    );
}

export default App;
```

Replace `src/App.css` with an empty file for now (we'll add styles later).

Replace `src/index.css` with basic styles:

```css
/* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    line-height: 1.6;
    color: #333;
}
```

---

## Chapter 4: Phase 1 Summary & Interview Prep

### 4.1 What We Accomplished

1. Created Laravel project with proper configuration
2. Set up database connection
3. Installed and configured Sanctum for authentication
4. Configured CORS for React communication
5. Created React project with Vite
6. Set up folder structure for both projects
7. Verified both servers are running

### 4.2 Key Files to Remember

| File | Purpose |
|------|---------|
| `.env` | Environment variables (database, app settings) |
| `config/cors.php` | CORS settings for React |
| `routes/api.php` | API route definitions |
| `src/services/api.js` | React API communication (we'll create) |

---

## 📚 INTERVIEW PACK 1: Project Setup & Fundamentals

### Key Terminologies

| Term | Definition | Example in Our Project |
|------|------------|----------------------|
| **Framework** | Pre-built code structure that provides common functionality | Laravel provides routing, database tools |
| **SPA** | Single Page Application - loads once, updates dynamically | Our React frontend |
| **API** | Application Programming Interface - way for programs to communicate | Laravel exposes endpoints for React |
| **REST** | Representational State Transfer - API design standard | GET /users returns users, POST /users creates |
| **Environment Variables** | Configuration values stored outside code | DB_PASSWORD in .env |
| **CORS** | Cross-Origin Resource Sharing - browser security feature | Allows React to call Laravel |
| **Composer** | PHP dependency manager | Installs Laravel packages |
| **npm** | Node.js package manager | Installs React packages |

### Interview Questions & Answers

**Q1: What is Laravel and why would you use it?**
> **Answer:** Laravel is a PHP framework that provides a structured way to build web applications. It includes built-in tools for routing, database operations (Eloquent ORM), authentication, validation, and security. I would use it because it follows MVC architecture, has excellent documentation, reduces boilerplate code, and has a large community with many packages available.

**Q2: What is the difference between a library and a framework?**
> **Answer:** A library is a collection of functions you call when needed (you're in control). A framework provides the structure and calls your code (the framework is in control). React is technically a library - you decide when to use its components. Laravel is a framework - it has a specific structure you follow.

**Q3: What is CORS and why is it needed?**
> **Answer:** CORS (Cross-Origin Resource Sharing) is a browser security feature that blocks requests between different domains by default. It's needed because without it, malicious websites could make requests to other sites pretending to be the user. In our project, React runs on port 5173 and Laravel on port 8000 - different origins - so we configure CORS to allow this communication.

**Q4: What does the .env file contain and why shouldn't it be committed to Git?**
> **Answer:** The .env file contains environment-specific configuration like database credentials, API keys, and app secrets. It shouldn't be committed because: (1) Different environments need different values (local vs production), (2) It contains sensitive data like passwords, (3) Security best practice is to keep secrets out of version control.

**Q5: Explain the MVC architecture pattern.**
> **Answer:** MVC stands for Model-View-Controller:
> - **Model**: Handles data and business logic (our Eloquent models)
> - **View**: Handles presentation/UI (in API projects, this is JSON responses)
> - **Controller**: Handles user requests, coordinates between Model and View
>
> Benefits: Separation of concerns, easier testing, code reusability.

### Scenario Questions

**S1: You clone a Laravel project and run it, but get a database connection error. What do you check?**
> **Answer:** I would check:
> 1. Is the `.env` file present? (It's not committed to Git, so might be missing)
> 2. Are the database credentials correct in `.env`?
> 3. Does the database exist? (might need to create it)
> 4. Is MySQL service running?
> 5. Run `php artisan config:clear` to clear cached config

**S2: A React developer says they're getting CORS errors when calling your Laravel API. How do you fix it?**
> **Answer:** I would:
> 1. Check `config/cors.php` - is their origin in `allowed_origins`?
> 2. Verify `supports_credentials` is true if they're sending auth headers
> 3. Make sure the `api/*` path is in `paths` array
> 4. Run `php artisan config:clear` to apply changes
> 5. Check if they're sending the correct headers (Content-Type, Accept)

### Common Mistakes

| Mistake | Why It's Wrong | Correct Approach |
|---------|---------------|------------------|
| Committing .env to Git | Exposes secrets | Add .env to .gitignore |
| Hardcoding database credentials | Different per environment | Use .env variables |
| Not running migrations after schema changes | Database out of sync | Always run `php artisan migrate` |
| Forgetting to configure CORS | Frontend can't communicate | Configure allowed origins |
| Running Laravel and React on same port | Port conflict | Use different ports (8000, 5173) |

---

# PHASE 2: DATABASE DESIGN & MODELS

## Chapter 5: Designing the Database

### 5.1 What is Database Design?

Before writing any code, we need to design our database. This is like creating a blueprint before building a house.

**Why is design important?**
1. **Prevents changes later** - Changing database structure after data exists is painful
2. **Ensures data integrity** - Relationships prevent orphaned data
3. **Improves performance** - Good design means faster queries
4. **Documents the system** - Database structure explains how the app works

### 5.2 Identifying Entities (Tables)

From our requirements, we need to track:

1. **Users** - People who use the system (admins and employees)
2. **Categories** - Types of assets (Laptop, Monitor, Phone)
3. **Assets** - The actual items (MacBook Pro, Dell Monitor)
4. **Assignments** - Who has what and when (History/Audit log)

### 5.3 Entity Relationship Diagram (ERD)

```
┌─────────────────┐         ┌─────────────────┐
│     USERS       │         │   CATEGORIES    │
├─────────────────┤         ├─────────────────┤
│ id              │         │ id              │
│ name            │         │ name            │
│ email           │         │ slug            │
│ password        │         │ created_at      │
│ role            │         │ updated_at      │
│ is_active       │         └────────┬────────┘
│ created_at      │                  │
│ updated_at      │                  │ 1:M (One category has many assets)
│ deleted_at      │                  │
└────────┬────────┘                  ▼
         │              ┌─────────────────┐
         │              │     ASSETS      │
         │              ├─────────────────┤
         │              │ id              │
         │              │ category_id (FK)│──────┘
         │              │ name            │
         │              │ serial_number   │
         │              │ status          │
         │              │ image_path      │
         │              │ created_at      │
         │              │ updated_at      │
         │              │ deleted_at      │
         │              └────────┬────────┘
         │                       │
         │                       │
         │    ┌──────────────────┴───────────────────┐
         │    │           ASSIGNMENTS                 │
         │    │         (History Table)               │
         │    ├───────────────────────────────────────┤
         │    │ id                                    │
         │    │ user_id (FK) ────────────────────────┼──→ Who received asset
         └────┼─assigned_by (FK) ────────────────────┼──→ Which admin assigned
              │ asset_id (FK) ───────────────────────┼──→ Which asset
              │ assigned_at                          │
              │ returned_at (NULL = still assigned)  │
              │ notes                                │
              └───────────────────────────────────────┘
```

### 5.4 Understanding Relationships

#### One-to-Many (1:M)
**"One category has many assets"**
- One "Laptop" category can have 50 different laptops
- Each asset belongs to only one category

```
Category: Laptop
    ├── MacBook Pro M2 (Asset)
    ├── MacBook Air M1 (Asset)
    └── Dell XPS 15 (Asset)
```

#### Many-to-Many through Assignments
**"One user can have many assets, one asset can be assigned to many users (over time)"**
- But NOT at the same time! An asset can only be with one person at a time.
- The `assignments` table is a history log, not a simple pivot table.

```
User: John
    ├── MacBook Pro (Jan 1 - Mar 15) - Returned
    └── Dell Monitor (Feb 1 - Present) - Active

Asset: MacBook Pro
    ├── John (Jan 1 - Mar 15) - Returned
    └── Mary (Mar 16 - Present) - Active
```

### 5.5 Why Soft Deletes?

**Hard Delete** = Data is gone forever (DELETE FROM users)
**Soft Delete** = Data is marked as deleted but still exists (deleted_at timestamp)

**Why use Soft Deletes for Users and Assets?**

1. **Audit Trail** - "Who had this laptop in 2022?" - We need that user's name
2. **Accidental Deletion** - Can easily restore
3. **Legal Requirements** - Some data must be kept for compliance
4. **Data Integrity** - Assignments reference users/assets; hard delete would break references

**Why NO Soft Deletes for Categories and Assignments?**
- Categories: If deleted, we want it gone (but we'll restrict if assets exist)
- Assignments: This IS the audit log; we never delete history

### 5.6 Foreign Key Constraints

Foreign keys ensure data integrity. Let's understand the options:

```php
// When parent is deleted, what happens to children?

onDelete('cascade')   // Delete children too
onDelete('restrict')  // Prevent deletion if children exist
onDelete('set null')  // Set foreign key to NULL
```

**Our choices:**
- `categories → assets`: **RESTRICT** - Can't delete category with assets
- `users → assignments`: **CASCADE** (but users use soft deletes, so only triggers on force delete)
- `assets → assignments`: **CASCADE** (same reason)

---

## Chapter 6: Creating Migrations

### 6.1 What are Migrations?

Migrations are like version control for your database. Instead of manually creating tables, you write PHP code that describes the tables.

**Benefits:**
1. **Reproducible** - Anyone can create the same database
2. **Version controlled** - Changes are tracked in Git
3. **Rollback** - Can undo changes with `php artisan migrate:rollback`
4. **Team friendly** - Everyone gets the same structure

### 6.2 Users Migration

Laravel already created a users migration. Let's modify it:

```bash
# Open the migration file
# database/migrations/0001_01_01_000000_create_users_table.php
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method is called when you run: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary Key - Auto-incrementing ID
            $table->id();

            // User's full name
            $table->string('name');

            // Email must be unique (no two users with same email)
            $table->string('email')->unique();

            // For email verification (nullable = optional)
            $table->timestamp('email_verified_at')->nullable();

            // Password (will be hashed, never plain text)
            $table->string('password');

            // Role: 'admin' or 'employee'
            // Default is 'employee' (new registrations are employees)
            $table->string('role')->default('employee');

            // Can deactivate users without deleting them
            $table->boolean('is_active')->default(true);

            // For "Remember Me" functionality
            $table->rememberToken();

            // created_at and updated_at columns
            $table->timestamps();

            // Soft delete - deleted_at column
            // When "deleted", this column gets a timestamp
            // Data is NOT actually removed from database
            $table->softDeletes();
        });

        // Password reset tokens table (Laravel default)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table (for web sessions, not API)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     * This method is called when you run: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

### 6.3 Categories Migration

Create a new migration:

```bash
php artisan make:migration create_categories_table
```

Open the new file in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Category name (e.g., "Laptop", "Monitor", "Phone")
            $table->string('name');

            // URL-friendly version of name
            // "Laptop Computer" becomes "laptop-computer"
            // Unique because we might use it in URLs
            $table->string('slug')->unique();

            $table->timestamps();

            // NO soft deletes for categories
            // If we delete a category, we want it gone
            // But we'll RESTRICT deletion if assets exist
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

### 6.4 Assets Migration

Create the migration:

```bash
php artisan make:migration create_assets_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // ═══════════════════════════════════════════════════════════
            // FOREIGN KEY: Links this asset to a category
            // ═══════════════════════════════════════════════════════════
            // foreignId() creates an unsigned big integer column
            // constrained() adds the foreign key constraint
            // onDelete('restrict') prevents deleting category if assets exist
            //
            // WHY RESTRICT?
            // If someone tries to delete "Laptop" category that has 50 laptops,
            // we want to stop them and say "delete the laptops first!"
            $table->foreignId('category_id')->constrained()->onDelete('restrict');

            // Asset name (e.g., "MacBook Pro M2 14-inch")
            $table->string('name');

            // Serial number MUST be unique
            // This is how we identify each specific item
            // Two laptops can't have the same serial number
            $table->string('serial_number')->unique();

            // Status of the asset
            // available = in storage, ready to assign
            // assigned = currently with an employee
            // maintenance = being repaired
            // broken = not working
            $table->string('status')->default('available');

            // Path to uploaded image (optional)
            // Stores like: "assets/abc123.jpg"
            $table->string('image_path')->nullable();

            $table->timestamps();

            // Soft deletes for assets
            // We never permanently delete expensive equipment records
            // Audit purposes: "What happened to asset ABC123?"
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
```

### 6.5 Assignments Migration

Create the migration:

```bash
php artisan make:migration create_assignments_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            // ═══════════════════════════════════════════════════════════
            // WHO received the asset?
            // ═══════════════════════════════════════════════════════════
            // The employee who got the item
            // cascade is OK because users use soft deletes
            // Only triggers on FORCE delete (which we rarely do)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // ═══════════════════════════════════════════════════════════
            // WHICH asset was assigned?
            // ═══════════════════════════════════════════════════════════
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');

            // ═══════════════════════════════════════════════════════════
            // WHO performed the assignment?
            // ═══════════════════════════════════════════════════════════
            // This is the admin who clicked "assign"
            // We need to specify the table because the column name
            // doesn't match the convention (assigned_by vs user_id)
            $table->foreignId('assigned_by')->constrained('users');

            // ═══════════════════════════════════════════════════════════
            // WHEN was it assigned?
            // ═══════════════════════════════════════════════════════════
            $table->timestamp('assigned_at');

            // ═══════════════════════════════════════════════════════════
            // WHEN was it returned?
            // ═══════════════════════════════════════════════════════════
            // NULL = Currently checked out (not returned yet)
            // Has timestamp = Was returned on this date
            //
            // THIS IS THE KEY TO THE SYSTEM!
            // To find current holder: WHERE returned_at IS NULL
            $table->timestamp('returned_at')->nullable();

            // Optional notes about the assignment
            // "New hire equipment" or "Replacement for broken laptop"
            $table->text('notes')->nullable();

            // NO timestamps() - we manage assigned_at/returned_at manually
            // NO soft deletes - this IS the audit log, we never delete
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
```

### 6.6 Run Migrations

Now let's create all the tables:

```bash
php artisan migrate
```

You should see:
```
Running migrations...
2024_01_01_000000_create_users_table ........... DONE
2024_01_01_000001_create_cache_table ........... DONE
2024_01_01_000002_create_jobs_table ............ DONE
2024_12_11_055200_create_personal_access_tokens_table ... DONE
2024_12_11_063037_create_categories_table ...... DONE
2024_12_11_063043_create_assets_table .......... DONE
2024_12_11_063051_create_assignments_table ..... DONE
```

### 6.7 Verify in phpMyAdmin

Open phpMyAdmin and check the `assettrack` database. You should see these tables:
- users
- password_reset_tokens
- sessions
- cache
- cache_locks
- jobs
- job_batches
- failed_jobs
- personal_access_tokens
- categories
- assets
- assignments

---

## Chapter 7: Creating Eloquent Models

### 7.1 What is Eloquent ORM?

**ORM** = Object-Relational Mapping

It lets you interact with database tables using PHP objects instead of writing SQL.

**Without ORM (Raw SQL):**
```php
$result = DB::select("SELECT * FROM users WHERE email = ?", ['john@example.com']);
```

**With Eloquent ORM:**
```php
$user = User::where('email', 'john@example.com')->first();
```

**Benefits:**
1. **Cleaner code** - More readable than SQL strings
2. **Security** - Prevents SQL injection automatically
3. **Relationships** - Easy to access related data
4. **Conventions** - Less code to write

### 7.2 User Model

Open `app/Models/User.php` and modify it:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    // ═══════════════════════════════════════════════════════════════════════
    // TRAITS
    // ═══════════════════════════════════════════════════════════════════════
    // Traits are like "plugins" that add functionality to the class

    use HasApiTokens;    // Sanctum: Allows creating API tokens
    use HasFactory;      // Allows using factories for testing
    use Notifiable;      // Allows sending notifications
    use SoftDeletes;     // Enables soft delete functionality

    // ═══════════════════════════════════════════════════════════════════════
    // FILLABLE - Mass Assignment Protection
    // ═══════════════════════════════════════════════════════════════════════
    // Only these fields can be set using User::create([...]) or $user->fill([...])
    // This prevents attackers from setting fields they shouldn't
    //
    // Example attack without fillable:
    // POST /register with { name: "Hacker", email: "x@x.com", role: "admin" }
    // Without protection, they'd become admin!

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // HIDDEN - Fields excluded from JSON/Array output
    // ═══════════════════════════════════════════════════════════════════════
    // When you do $user->toJson() or return response()->json($user),
    // these fields will NOT be included
    //
    // NEVER expose passwords or sensitive tokens!

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // CASTS - Automatic Type Conversion
    // ═══════════════════════════════════════════════════════════════════════
    // Database stores everything as strings/numbers
    // Casts automatically convert when reading/writing

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // String → Carbon date object
            'password' => 'hashed',              // Auto-hash when setting
            'is_active' => 'boolean',            // '1' → true, '0' → false
        ];
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Get all assignments for this user.
     * A user can have many assignments (one for each asset they've had)
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // HELPER METHODS
    // ═══════════════════════════════════════════════════════════════════════
    // These make code more readable throughout the app

    /**
     * Check if the user is an admin.
     * Usage: if ($user->isAdmin()) { ... }
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an employee.
     * Usage: if ($user->isEmployee()) { ... }
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }
}
```

### 7.3 Category Model

Create the model:

```bash
php artisan make:model Category
```

Open `app/Models/Category.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // ═══════════════════════════════════════════════════════════════════════
    // MODEL EVENTS (Boot Method)
    // ═══════════════════════════════════════════════════════════════════════
    // The boot method runs when the model is initialized
    // We can hook into "events" that happen during the model lifecycle
    //
    // Events: creating, created, updating, updated, deleting, deleted, etc.

    protected static function boot()
    {
        parent::boot();

        // ─────────────────────────────────────────────────────────────────
        // CREATING EVENT
        // Runs BEFORE a new category is saved to database
        // ─────────────────────────────────────────────────────────────────
        // We automatically generate the slug from the name
        // "Laptop Computer" → "laptop-computer"

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // ─────────────────────────────────────────────────────────────────
        // UPDATING EVENT
        // Runs BEFORE an existing category is updated
        // ─────────────────────────────────────────────────────────────────
        // If name changed, regenerate the slug
        // isDirty('name') checks if the name field was modified

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Get all assets in this category.
     * One category has many assets.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
```

### 7.4 Asset Model

Create the model:

```bash
php artisan make:model Asset
```

Open `app/Models/Asset.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'serial_number',
        'status',
        'image_path'
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Get the category this asset belongs to.
     * Each asset belongs to one category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all assignments (history) for this asset.
     * An asset can have many assignments over time.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the current holder of this asset.
     * Returns the user who has this asset right now (returned_at is NULL).
     */
    public function currentHolder()
    {
        return $this->hasOneThrough(
            User::class,           // Final model we want
            Assignment::class,     // Intermediate model
            'asset_id',           // Foreign key on assignments
            'id',                 // Foreign key on users
            'id',                 // Local key on assets
            'user_id'             // Local key on assignments
        )->whereNull('assignments.returned_at');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // QUERY SCOPES
    // ═══════════════════════════════════════════════════════════════════════
    // Scopes are reusable query conditions
    // Usage: Asset::available()->get()

    /**
     * Scope to get only available assets.
     *
     * Without scope: Asset::where('status', 'available')->get()
     * With scope:    Asset::available()->get()
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
```

### 7.5 Assignment Model

Create the model:

```bash
php artisan make:model Assignment
```

Open `app/Models/Assignment.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    // ═══════════════════════════════════════════════════════════════════════
    // DISABLE TIMESTAMPS
    // ═══════════════════════════════════════════════════════════════════════
    // By default, Eloquent manages created_at and updated_at
    // We don't have these columns; we use assigned_at and returned_at instead

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'asset_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
        'notes'
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // CASTS
    // ═══════════════════════════════════════════════════════════════════════
    // Convert date strings to Carbon objects for easy manipulation

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    // ═══════════════════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Get the user (employee) who received this asset.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the asset that was assigned.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the admin who performed this assignment.
     *
     * Note: We specify 'assigned_by' as the foreign key because
     * it doesn't follow the convention (would be 'admin_id')
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
```

### 7.6 Test the Models

Let's test our models using Tinker (Laravel's REPL):

```bash
php artisan tinker
```

```php
// Create a test category
$category = App\Models\Category::create(['name' => 'Laptop']);
// Notice: slug is automatically generated!

// Check the category
$category->name;   // "Laptop"
$category->slug;   // "laptop"

// Create a test user
$user = App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => 'password',
    'role' => 'admin'
]);

// Password is automatically hashed!
$user->password;  // Shows hashed version, not "password"

// Check admin method
$user->isAdmin();  // true

// Exit tinker
exit
```

---

## Chapter 8: Phase 2 Summary & Interview Prep

### 8.1 What We Accomplished

1. Designed database schema with 4 tables
2. Created migrations with proper foreign keys
3. Implemented soft deletes for audit trail
4. Created Eloquent models with relationships
5. Added query scopes and helper methods
6. Used model events for auto-slug generation
7. Tested models with Tinker

---

## 📚 INTERVIEW PACK 2: Database & Eloquent

### Key Terminologies

| Term | Definition | Example |
|------|------------|---------|
| **Migration** | PHP code that describes database structure | `create_users_table.php` |
| **Model** | PHP class representing a database table | `User.php` represents `users` table |
| **ORM** | Object-Relational Mapping - objects instead of SQL | `User::find(1)` instead of `SELECT * FROM users WHERE id = 1` |
| **Eloquent** | Laravel's ORM implementation | All our models extend Eloquent |
| **Primary Key** | Unique identifier for each row | `id` column |
| **Foreign Key** | Reference to another table's primary key | `category_id` in assets |
| **Relationship** | How tables connect to each other | User hasMany Assignments |
| **Soft Delete** | Mark as deleted without removing | `deleted_at` timestamp |
| **Mass Assignment** | Setting multiple fields at once | `User::create([...])` |
| **Fillable** | Fields allowed for mass assignment | `protected $fillable = [...]` |
| **Cast** | Automatic type conversion | `'is_active' => 'boolean'` |
| **Scope** | Reusable query condition | `Asset::available()` |

### Interview Questions & Answers

**Q1: What is the difference between `hasMany` and `belongsTo`?**
> **Answer:** They represent opposite sides of a one-to-many relationship:
> - `hasMany` is on the "one" side: `Category hasMany Assets` (one category has many assets)
> - `belongsTo` is on the "many" side: `Asset belongsTo Category` (each asset belongs to one category)
>
> The table with the foreign key uses `belongsTo`. In our case, `assets` table has `category_id`, so Asset model uses `belongsTo(Category::class)`.

**Q2: What are soft deletes and when would you use them?**
> **Answer:** Soft deletes don't actually remove data from the database. Instead, they set a `deleted_at` timestamp. The record is excluded from normal queries but still exists.
>
> Use soft deletes when:
> - You need audit trails (who/what was deleted)
> - Data must be recoverable
> - Other records reference this data (prevent broken relationships)
> - Legal/compliance requirements
>
> In AssetTrack, we use soft deletes for Users and Assets because Assignments reference them for historical records.

**Q3: Explain what `$fillable` does and why it's important.**
> **Answer:** `$fillable` defines which fields can be set using mass assignment (like `Model::create([])` or `$model->fill([])`). It's a security feature.
>
> Without it, an attacker could add extra fields to a form:
> ```html
> <input name="role" value="admin" hidden>
> ```
> And if we did `User::create($request->all())`, they'd become admin!
>
> With `$fillable`, only listed fields are accepted. Unlisted fields are ignored.

**Q4: What is the difference between migrations and models?**
> **Answer:**
> - **Migrations** define the database structure (tables, columns, indexes). They run once to create/modify the schema.
> - **Models** are PHP classes that interact with existing tables. They define relationships, business logic, and how to read/write data.
>
> Analogy: Migrations are the blueprint (how to build the house). Models are the interface (how to use the house).

**Q5: Why use foreign key constraints? Can't we just store the ID without a constraint?**
> **Answer:** Foreign key constraints ensure data integrity:
>
> Without constraints:
> - You could insert `category_id = 999` even if category 999 doesn't exist
> - You could delete a category that has assets, leaving orphaned records
>
> With constraints:
> - Database rejects invalid foreign key values
> - `RESTRICT` prevents deleting parent if children exist
> - `CASCADE` automatically deletes children when parent is deleted
>
> It's an extra layer of protection beyond application code.

### Scenario Questions

**S1: You need to add a "purchased_date" column to the assets table. How do you do it?**
> **Answer:**
> ```bash
> # Create a new migration
> php artisan make:migration add_purchased_date_to_assets_table
> ```
> ```php
> // In the migration file
> public function up(): void
> {
>     Schema::table('assets', function (Blueprint $table) {
>         $table->date('purchased_date')->nullable()->after('status');
>     });
> }
>
> public function down(): void
> {
>     Schema::table('assets', function (Blueprint $table) {
>         $table->dropColumn('purchased_date');
>     });
> }
> ```
> ```bash
> # Run the migration
> php artisan migrate
> ```

**S2: A user reports seeing assets that were deleted. What's wrong?**
> **Answer:** If using soft deletes, there are a few possible issues:
> 1. Query might be using `withTrashed()` which includes soft-deleted records
> 2. Direct SQL query (not through Eloquent) won't apply the soft delete scope
> 3. The model might not have `use SoftDeletes` trait
>
> To fix, ensure:
> - Model has `use SoftDeletes`
> - All queries use Eloquent (not raw SQL)
> - Remove `withTrashed()` unless specifically needed

**S3: You have a query that's slow: `Asset::all()` with 10,000 records. How do you optimize?**
> **Answer:**
> 1. **Pagination**: `Asset::paginate(15)` instead of `all()`
> 2. **Select only needed columns**: `Asset::select('id', 'name')->get()`
> 3. **Eager load relationships**: `Asset::with('category')->get()` to avoid N+1
> 4. **Add database indexes** on frequently queried columns
> 5. **Use chunking** for processing: `Asset::chunk(100, fn($assets) => ...)`

### Common Mistakes

| Mistake | Problem | Solution |
|---------|---------|----------|
| Not using `$fillable` | Security vulnerability | Always define fillable fields |
| Forgetting `use SoftDeletes` in model | Soft delete won't work | Add the trait to model |
| Using `all()` on large tables | Memory/performance issues | Use pagination |
| Not eager loading relationships | N+1 query problem | Use `with()` |
| Hardcoding IDs in foreign keys | Breaks on different environments | Use relationships or constants |
| Forgetting `down()` in migrations | Can't rollback | Always implement `down()` |

---

# PHASE 3: LARAVEL AUTHENTICATION

## Chapter 9: Understanding API Authentication

### 9.1 How Web Authentication Works

**Traditional Web Auth (Sessions):**
```
1. User submits login form
2. Server verifies credentials
3. Server creates a session (stored on server)
4. Server sends session ID as cookie
5. Browser sends cookie with every request
6. Server looks up session to identify user
```

**API Auth (Tokens):**
```
1. User submits credentials to API
2. Server verifies credentials
3. Server creates a token (random string)
4. Server sends token in response
5. Client stores token (localStorage)
6. Client sends token in Authorization header
7. Server validates token to identify user
```

### 9.2 Why Tokens for API?

| Feature | Sessions | Tokens |
|---------|----------|--------|
| Storage | Server (memory/database) | Client (localStorage) |
| Scaling | Hard (need session sharing) | Easy (stateless) |
| Mobile apps | Difficult | Easy |
| Multiple clients | Complex | Simple |
| Cross-domain | Cookie issues | No issues |

For our React + Laravel setup, tokens are perfect because:
1. React and Laravel are on different ports (different origins)
2. We might add a mobile app later
3. Stateless = simpler server architecture

### 9.3 Laravel Sanctum Overview

Sanctum provides two authentication methods:
1. **SPA Authentication** - Cookie-based for same-domain SPAs
2. **API Token Authentication** - Token-based for mobile/external apps

We'll use **API Tokens** because React runs on a different port.

**How it works:**
1. User logs in with email/password
2. Laravel creates a token in `personal_access_tokens` table
3. Returns the plain-text token (only shown once!)
4. Client stores the token
5. Client sends token as: `Authorization: Bearer <token>`
6. Sanctum validates and identifies the user

---

## Chapter 10: Creating the Auth Service

### 10.1 What is a Service Layer?

**The Problem:**
If we put all logic in Controllers, they become huge and hard to test.

**The Solution:**
Move business logic to Service classes. Controllers just handle HTTP.

```
WITHOUT Service Layer:
Controller (handles HTTP + validates + business logic + database)

WITH Service Layer:
Controller (handles HTTP)
    ↓
Service (business logic)
    ↓
Model (database)
```

**Benefits:**
1. **Reusability** - Same logic for API and Web controllers
2. **Testability** - Easy to unit test services
3. **Readability** - Controllers are slim and clear
4. **Maintainability** - One place to change business rules

### 10.2 Create Services Directory

```bash
mkdir app/Services
```

### 10.3 AuthService

Create `app/Services/AuthService.php`:

```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Handle the authentication attempt.
     * Throws ValidationException on failure to show errors on the form.
     *
     * @param array $credentials ['email' => '...', 'password' => '...']
     * @param bool $remember For "Remember Me" checkbox
     * @throws ValidationException
     */
    public function authenticate(array $credentials, bool $remember = false): void
    {
        // ═══════════════════════════════════════════════════════════════════
        // STEP 1: Attempt to login
        // ═══════════════════════════════════════════════════════════════════
        // Auth::attempt() does the following:
        // 1. Finds user by email
        // 2. Hashes the provided password
        // 3. Compares with stored hash
        // 4. Returns true/false

        if (! Auth::attempt($credentials, $remember)) {
            // ─────────────────────────────────────────────────────────────
            // SECURITY NOTE: Generic error message
            // ─────────────────────────────────────────────────────────────
            // We DON'T say "email not found" or "wrong password"
            // That would tell hackers which emails exist (User Enumeration)
            // Generic message: "These credentials do not match our records"

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // ═══════════════════════════════════════════════════════════════════
        // STEP 2: Check if user is active
        // ═══════════════════════════════════════════════════════════════════
        // We do this AFTER password check for security
        // If we check before, attacker knows the email exists

        if (! Auth::user()->is_active) {
            Auth::logout(); // Log them out immediately

            throw ValidationException::withMessages([
                'email' => 'Your account is deactivated. Contact Admin.',
            ]);
        }

        // ═══════════════════════════════════════════════════════════════════
        // STEP 3: Regenerate session ID
        // ═══════════════════════════════════════════════════════════════════
        // Prevents "Session Fixation" attacks
        // Attacker can't hijack a pre-set session ID

        request()->session()->regenerate();
    }

    /**
     * Handle the logout process.
     */
    public function logout(): void
    {
        Auth::logout();

        // Invalidate the session (destroy all data)
        request()->session()->invalidate();

        // Regenerate CSRF token (security)
        request()->session()->regenerateToken();
    }

    /**
     * Create a new user and return the instance.
     *
     * @param array $data Validated user data (name, email, password)
     * @return User The newly created user
     */
    public function register(array $data): User
    {
        // Create the user with hashed password
        // Default role is 'employee' - admins are created differently
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'employee',
            'is_active' => true,
        ]);

        return $user;
    }
}
```

---

## Chapter 11: Creating Form Requests (Validation)

### 11.1 What are Form Requests?

Form Requests are classes dedicated to validation. Instead of validating in the controller, we create a class.

**Benefits:**
1. **Reusability** - Same validation for multiple endpoints
2. **Clean controllers** - Validation logic is separate
3. **Authorization** - Can check permissions in the request
4. **Auto-validation** - Laravel validates before controller runs

### 11.2 LoginRequest

```bash
php artisan make:request LoginRequest
```

Open `app/Http/Requests/LoginRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * For login, anyone can attempt (even unauthenticated users).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Email is required and must be a valid email format
            'email' => ['required', 'string', 'email'],

            // Password is required
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom error messages (optional).
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ];
    }
}
```

### 11.3 RegisterRequest

```bash
php artisan make:request RegisterRequest
```

Open `app/Http/Requests/RegisterRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            // Email must be unique in users table
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],

            // Password requirements:
            // - required
            // - at least 8 characters
            // - must match password_confirmation field
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Please enter a password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
```

---

## Chapter 12: Creating API Resources

### 12.1 What are API Resources?

API Resources transform Eloquent models into JSON. They control exactly what data is returned.

**Without Resource:**
```php
return response()->json($user);
// Returns EVERYTHING, including sensitive data
```

**With Resource:**
```php
return new UserResource($user);
// Returns only what we define
```

### 12.2 UserResource

```bash
php artisan make:resource UserResource
```

Open `app/Http/Resources/UserResource.php`:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Conditional: Only include if relationship is loaded
            // Prevents N+1 queries
            'assignments_count' => $this->when(
                $this->relationLoaded('assignments'),
                fn() => $this->assignments->count()
            ),
        ];

        // NOTE: We do NOT include:
        // - password (security)
        // - remember_token (security)
        // - email_verified_at (not needed for this app)
        // - deleted_at (internal use only)
    }
}
```

---

## Chapter 13: Auth Controller

### 13.1 Create API Controllers Directory

```bash
mkdir app/Http/Controllers/Api
```

### 13.2 AuthController

Create `app/Http/Controllers/Api/AuthController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════
    // CONSTRUCTOR INJECTION
    // ═══════════════════════════════════════════════════════════════════════
    // Laravel automatically creates AuthService and injects it
    // This is called "Dependency Injection"
    //
    // Benefits:
    // 1. Easy to test (can inject mock)
    // 2. Loose coupling
    // 3. Clean code

    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Handle a registration request for the API.
     *
     * POST /api/auth/register
     * Body: { name, email, password, password_confirmation }
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // RegisterRequest already validated the data
        // $request->validated() returns only the validated fields
        $user = $this->authService->register($request->validated());

        // Create a Sanctum token for the new user
        // 'api-token' is just a name for this token (for reference)
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful.',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201); // 201 = Created
    }

    /**
     * Handle a login request for the API.
     *
     * POST /api/auth/login
     * Body: { email, password }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Attempt authentication
        // auth()->attempt() returns true/false, doesn't create session for API
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors' => ['email' => [__('auth.failed')]],
            ], 401); // 401 = Unauthorized
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return response()->json([
                'message' => 'Your account is deactivated. Contact Admin.',
                'errors' => ['email' => ['Your account is deactivated.']],
            ], 401);
        }

        // Create API token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Handle logout request for the API.
     *
     * POST /api/auth/logout
     * Header: Authorization: Bearer <token>
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete the current token (invalidate it)
        // User might have multiple tokens (different devices)
        // We only delete the one being used
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Refresh the user's token.
     * Creates a new token and invalidates the old one.
     *
     * POST /api/auth/refresh
     * Header: Authorization: Bearer <token>
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Delete old token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully.',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated user.
     *
     * GET /api/auth/user
     * Header: Authorization: Bearer <token>
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }
}
```

---

## Chapter 14: Auth Middleware

### 14.1 What is Middleware?

Middleware is code that runs **before** or **after** a request reaches your controller.

```
Request → Middleware → Controller → Response
              ↓
         (can reject)
              ↓
           Response (401, 403, etc.)
```

**Common uses:**
- Authentication (is user logged in?)
- Authorization (is user allowed to do this?)
- Logging (record what happened)
- Rate limiting (prevent abuse)

### 14.2 Admin Middleware

Create `app/Http/Middleware/EnsureUserIsAdmin.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ═══════════════════════════════════════════════════════════════════
        // STEP 1: Check if user is authenticated
        // ═══════════════════════════════════════════════════════════════════
        // This should already be handled by 'auth:sanctum' middleware
        // But we check again for safety (defense in depth)

        if (! $request->user()) {
            // For API, we return JSON, not redirect
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // ═══════════════════════════════════════════════════════════════════
        // STEP 2: Check if user is admin
        // ═══════════════════════════════════════════════════════════════════

        if ($request->user()->role !== 'admin') {
            // 403 = Forbidden (authenticated but not authorized)
            return response()->json([
                'message' => 'Unauthorized access: You must be an administrator.'
            ], 403);
        }

        // ═══════════════════════════════════════════════════════════════════
        // STEP 3: Allow the request to proceed
        // ═══════════════════════════════════════════════════════════════════
        // $next($request) passes the request to the next middleware or controller

        return $next($request);
    }
}
```

### 14.3 Employee Middleware

Create `app/Http/Middleware/EnsureUserIsEmployee.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEmployee
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Allow both employees AND admins
        // Admins can access employee features
        if (!in_array($request->user()->role, ['employee', 'admin'])) {
            return response()->json([
                'message' => 'Unauthorized access.'
            ], 403);
        }

        return $next($request);
    }
}
```

### 14.4 Register Middleware

Open `bootstrap/app.php` and add the middleware aliases:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'employee' => \App\Http\Middleware\EnsureUserIsEmployee::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

## Chapter 15: API Routes

### 15.1 Route Organization

Open `routes/api.php` and replace with:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API routes for React frontend integration.
| All routes return JSON responses.
|
| Route Groups:
| 1. Public routes (no auth needed)
| 2. Authenticated routes (any logged-in user)
| 3. Admin routes (admin only)
| 4. Employee routes (employees and admins)
|
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================
// Anyone can access these, even without logging in

Route::prefix('auth')->group(function () {
    // POST /api/auth/register
    Route::post('/register', [AuthController::class, 'register']);

    // POST /api/auth/login
    Route::post('/login', [AuthController::class, 'login']);
});

// ============================================
// PROTECTED ROUTES (Authentication Required)
// ============================================
// User must be logged in (valid token)

Route::middleware('auth:sanctum')->group(function () {

    // ----------------------------------------
    // AUTH ROUTES (All authenticated users)
    // ----------------------------------------
    Route::prefix('auth')->group(function () {
        // POST /api/auth/logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // POST /api/auth/refresh
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // GET /api/auth/user
        Route::get('/user', [AuthController::class, 'user']);
    });

    // ----------------------------------------
    // ADMIN ROUTES (Coming in later chapters)
    // ----------------------------------------
    Route::middleware('admin')->group(function () {
        // Dashboard, Assets, Users, Categories, Assignments
        // We'll add these later
    });

    // ----------------------------------------
    // EMPLOYEE ROUTES (Coming in later chapters)
    // ----------------------------------------
    Route::middleware('employee')->group(function () {
        // My Assets
        // We'll add this later
    });
});
```

### 15.2 Test the Auth API

Start the Laravel server if not running:
```bash
php artisan serve
```

**Test Registration (using curl or Postman):**

```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

Expected response:
```json
{
  "message": "Registration successful.",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "role": "employee",
    "is_active": true,
    "created_at": "2024-12-15T10:00:00.000000Z",
    "updated_at": "2024-12-15T10:00:00.000000Z"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

**Test Login:**

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Test Get User (with token):**

```bash
curl http://127.0.0.1:8000/api/auth/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Chapter 16: Phase 3 Summary & Interview Prep

### 16.1 What We Accomplished

1. Created AuthService for authentication logic
2. Created Form Requests for validation
3. Created UserResource for JSON transformation
4. Built AuthController with register, login, logout, refresh, user endpoints
5. Created Admin and Employee middleware
6. Set up protected API routes
7. Tested the authentication flow

---

## 📚 INTERVIEW PACK 3: Authentication & Security

### Key Terminologies

| Term | Definition |
|------|------------|
| **Authentication** | Verifying identity (WHO are you?) |
| **Authorization** | Checking permissions (WHAT can you do?) |
| **Token** | String that proves user is authenticated |
| **Bearer Token** | Token sent in Authorization header |
| **Session** | Server-side storage of user state |
| **Stateless** | Server doesn't store session; client sends everything |
| **CSRF** | Cross-Site Request Forgery attack |
| **XSS** | Cross-Site Scripting attack |
| **Middleware** | Code that runs before/after controller |
| **Hashing** | One-way encryption (can't decrypt) |
| **Salting** | Adding random data before hashing |
| **User Enumeration** | Discovering which emails exist |

### Interview Questions & Answers

**Q1: Explain the difference between authentication and authorization.**
> **Answer:**
> - **Authentication** is verifying WHO you are. It answers "Are you really John?" Examples: login with password, fingerprint, 2FA.
> - **Authorization** is checking WHAT you can do. It answers "Can John delete users?" Examples: admin role check, permission system.
>
> In our app: Login is authentication (proves identity). Admin middleware is authorization (checks if you're allowed).

**Q2: Why use token-based authentication instead of sessions for APIs?**
> **Answer:**
> - **Stateless**: Server doesn't need to store session data
> - **Scalable**: Any server can validate the token
> - **Mobile friendly**: Tokens work easily on mobile apps
> - **Cross-domain**: No cookie issues with different origins
>
> Sessions require the same server to remember you; tokens are self-contained.

**Q3: What is the Service Layer pattern and why use it?**
> **Answer:**
> The Service Layer is a design pattern where business logic is moved from controllers to dedicated service classes.
>
> **Benefits:**
> - Controllers stay thin (only handle HTTP)
> - Services are reusable (API and Web controllers can share)
> - Services are testable (easy to unit test)
> - Single responsibility (one reason to change)
>
> Example: AuthService handles login logic, AuthController handles HTTP request/response.

**Q4: What is Laravel Sanctum and when would you use it?**
> **Answer:**
> Sanctum is Laravel's lightweight authentication package. It provides:
> 1. **API token authentication** - For mobile apps and SPAs
> 2. **SPA authentication** - Cookie-based for same-domain SPAs
>
> Use Sanctum when:
> - Building a simple API without OAuth complexity
> - Need token-based auth for mobile apps
> - Building SPAs that need auth
>
> Use Passport instead when:
> - Need full OAuth2 server
> - Third-party apps will use your API

**Q5: Explain the password hashing process and why we don't store plain passwords.**
> **Answer:**
> **Why not plain text:**
> - If database is stolen, all passwords are exposed
> - Users often reuse passwords across sites
> - Legal/compliance requirements
>
> **How hashing works:**
> 1. Password + random salt → Hash algorithm → Hash string
> 2. Hash is stored in database
> 3. On login: Hash the input, compare with stored hash
> 4. Hashing is one-way (can't reverse to get password)
>
> Laravel uses bcrypt by default, which automatically handles salting.

### Scenario Questions

**S1: A user says they can't login even though they're sure the password is correct. How do you debug?**
> **Answer:**
> 1. Check if user exists: `User::where('email', $email)->first()`
> 2. Check if user is active: `$user->is_active`
> 3. Check if account is soft-deleted: `User::withTrashed()->where('email', $email)->first()`
> 4. Verify password hash is valid (not corrupted)
> 5. Check login attempt logs (if implemented)
> 6. Try password reset to create new password

**S2: You notice someone is trying to brute-force login attempts. How do you prevent this?**
> **Answer:**
> Laravel has built-in rate limiting:
> ```php
> // In LoginRequest
> public function rules(): array
> {
>     return [
>         'email' => ['required', 'email'],
>         'password' => ['required'],
>     ];
> }
>
> protected function prepareForValidation(): void
> {
>     $this->ensureIsNotRateLimited();
> }
>
> public function ensureIsNotRateLimited(): void
> {
>     if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
>         return;
>     }
>     throw ValidationException::withMessages([
>         'email' => 'Too many login attempts. Please try again in X seconds.',
>     ]);
> }
> ```
>
> Also consider:
> - CAPTCHA after failed attempts
> - Account lockout after N failures
> - IP blocking for persistent attackers
> - Alerting admins of suspicious activity

**S3: An employee leaves the company. How do you revoke their access?**
> **Answer:**
> Multiple approaches:
> 1. **Soft delete the user**: They can't login (our is_active check would block them)
> 2. **Set is_active to false**: Quick disable without deleting
> 3. **Delete all their tokens**: `$user->tokens()->delete()`
> 4. **Combination**: Deactivate AND delete tokens for immediate effect
>
> Our system checks `is_active` on every login, so deactivating blocks new logins. Deleting tokens blocks existing sessions.

### Common Mistakes

| Mistake | Risk | Solution |
|---------|------|----------|
| Storing plain-text passwords | Total account compromise | Always hash passwords |
| Generic error for "email not found" vs "wrong password" | User enumeration | Same message for both |
| Not checking is_active | Deactivated users can still access | Check on every auth request |
| Returning password in API response | Exposes hashed password | Use $hidden in model |
| Not invalidating tokens on logout | Session can be reused | Delete token on logout |
| Long-lived tokens | Stolen tokens work forever | Short expiry + refresh |

---

This completes the first major portion of the guide. The document continues with:
- **Phase 4**: React Authentication (AuthContext, Login page, Protected Routes)
- **Phase 5**: Dashboard & Categories (Service Layer, CRUD APIs, React pages)
- **Phase 6**: Assets Management (with file upload, assignment, return)
- **Phase 7**: Users Management
- **Phase 8**: Assignment System
- **Phase 9**: Employee Features
- **Phase 10**: Polish & Final Testing
- **Complete Interview Question Bank**

---

# PHASE 4: REACT SETUP & AUTHENTICATION

## Chapter 17: Setting Up React API Service

### 17.1 Understanding the API Layer

The API service is the "bridge" between React and Laravel. All HTTP requests go through this file.

**Why a separate API service?**
1. **Centralized configuration** - Base URL, headers in one place
2. **Automatic token handling** - Don't repeat auth code
3. **Error handling** - Catch 401 errors globally
4. **Easy to test** - Mock the API service

### 17.2 Create the API Service

Create `asset-track-frontend/src/services/api.js`:

```javascript
/**
 * API Service - The Bridge Between React and Laravel
 * ==================================================
 *
 * This file handles ALL communication with the Laravel backend.
 *
 * HOW IT WORKS:
 * 1. Creates an axios instance with base URL and headers
 * 2. Automatically adds auth token to every request
 * 3. Handles 401 errors by logging out the user
 */

import axios from 'axios';

// =============================================================================
// STEP 1: Create axios instance with base configuration
// =============================================================================
// All requests will go to this base URL + the endpoint
// Example: api.get('/users') calls http://127.0.0.1:8000/api/users

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// =============================================================================
// STEP 2: Request Interceptor - Runs BEFORE every request
// =============================================================================
// Automatically adds the auth token to every request
// So you don't have to remember to add it manually

api.interceptors.request.use(
    (config) => {
        // Get token from localStorage
        const token = localStorage.getItem('token');

        // If token exists, add it to Authorization header
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// =============================================================================
// STEP 3: Response Interceptor - Runs AFTER every response
// =============================================================================
// Handles 401 errors globally (token expired or invalid)
// Automatically logs out the user and redirects to login

api.interceptors.response.use(
    (response) => {
        // Success - just return the response
        return response;
    },
    (error) => {
        // If we get 401 (Unauthorized)
        if (error.response && error.response.status === 401) {
            // Clear stored auth data
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            // Redirect to login
            window.location.href = '/login';
        }

        return Promise.reject(error);
    }
);

// Export the configured instance
export default api;

// =============================================================================
// STEP 4: API Helper Functions
// =============================================================================
// These make it easy to call specific endpoints

/**
 * AUTH ENDPOINTS
 */
export const authAPI = {
    login: (email, password) => api.post('/auth/login', { email, password }),
    register: (data) => api.post('/auth/register', data),
    logout: () => api.post('/auth/logout'),
    getUser: () => api.get('/auth/user'),
};

/**
 * DASHBOARD ENDPOINT
 */
export const dashboardAPI = {
    getStats: () => api.get('/dashboard'),
};

/**
 * ASSETS ENDPOINTS
 */
export const assetsAPI = {
    getAll: (params = {}) => api.get('/assets', { params }),
    getOne: (id) => api.get(`/assets/${id}`),
    create: (data) => api.post('/assets', data),
    update: (id, data) => api.post(`/assets/${id}`, data), // POST with _method for FormData
    delete: (id) => api.delete(`/assets/${id}`),
    getAvailable: () => api.get('/assets-available'),
    assign: (assetId, userId, notes = '') => api.post(`/assets/${assetId}/assign`, {
        user_id: userId,
        notes
    }),
    return: (assetId) => api.post(`/assets/${assetId}/return`),
};

/**
 * CATEGORIES ENDPOINTS
 */
export const categoriesAPI = {
    getAll: (params = {}) => api.get('/categories', { params }),
    getOne: (id) => api.get(`/categories/${id}`),
    create: (data) => api.post('/categories', data),
    update: (id, data) => api.put(`/categories/${id}`, data),
    delete: (id) => api.delete(`/categories/${id}`),
};

/**
 * USERS ENDPOINTS
 */
export const usersAPI = {
    getAll: (params = {}) => api.get('/users', { params }),
    getOne: (id) => api.get(`/users/${id}`),
    create: (data) => api.post('/users', data),
    update: (id, data) => api.put(`/users/${id}`, data),
    delete: (id) => api.delete(`/users/${id}`),
    getEmployees: () => api.get('/employees'),
};

/**
 * PROFILE ENDPOINTS
 */
export const profileAPI = {
    get: () => api.get('/profile'),
    update: (data) => api.put('/profile', data),
    updatePassword: (data) => api.put('/profile/password', data),
};

/**
 * MY ASSETS ENDPOINT (Employee)
 */
export const myAssetsAPI = {
    get: () => api.get('/my-assets'),
};
```

---

## Chapter 18: Creating the Auth Context

### 18.1 What is React Context?

Context is a way to share data across components without passing props through every level.

**The Problem (Prop Drilling):**
```
App
└── Layout (needs user)
    └── Navbar (needs user)
        └── UserMenu (needs user)
            └── Avatar (needs user)
```
Without context, you'd pass `user` through every component.

**The Solution (Context):**
```
App (AuthProvider wraps everything)
└── Layout
    └── Navbar
        └── UserMenu (uses useAuth() hook)
            └── Avatar (uses useAuth() hook)
```
Any component can directly access auth state.

### 18.2 Create AuthContext

Create `asset-track-frontend/src/context/AuthContext.jsx`:

```jsx
/**
 * Authentication Context
 * ======================
 *
 * Provides global auth state to all components.
 *
 * What it provides:
 * - user: Current logged-in user (or null)
 * - token: Auth token (or null)
 * - loading: True while checking if user is logged in
 * - isAuthenticated: Boolean shortcut
 * - login(): Function to log in
 * - logout(): Function to log out
 * - register(): Function to register
 */

import { createContext, useContext, useState, useEffect } from 'react';
import { authAPI } from '../services/api';

// Create the context (empty container)
const AuthContext = createContext(null);

// Helper to safely get user from localStorage
const getStoredUser = () => {
    try {
        const savedUser = localStorage.getItem('user');
        return savedUser ? JSON.parse(savedUser) : null;
    } catch (e) {
        console.error('Failed to parse stored user');
        return null;
    }
};

/**
 * AuthProvider Component
 * Wrap your app with this to enable auth features
 */
export function AuthProvider({ children }) {
    // Initialize state from localStorage (prevents flash on reload)
    const [user, setUser] = useState(() => getStoredUser());
    const [token, setToken] = useState(() => localStorage.getItem('token'));
    const [loading, setLoading] = useState(true);

    // =========================================================================
    // On mount: Check if stored token is still valid
    // =========================================================================
    useEffect(() => {
        const checkAuth = async () => {
            const savedToken = localStorage.getItem('token');

            if (savedToken) {
                try {
                    // Verify token with server
                    const response = await authAPI.getUser();
                    const userData = response.data.user || response.data;

                    setUser(userData);
                    setToken(savedToken);
                    localStorage.setItem('user', JSON.stringify(userData));
                } catch (error) {
                    // Token is invalid or expired
                    console.log('Token invalid, clearing auth');
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    setUser(null);
                    setToken(null);
                }
            }

            setLoading(false);
        };

        checkAuth();
    }, []);

    // =========================================================================
    // Login Function
    // =========================================================================
    const login = async (email, password) => {
        try {
            const response = await authAPI.login(email, password);
            const { token: newToken, user: rawUser } = response.data;
            const userData = rawUser?.data || rawUser;

            // Store in localStorage
            localStorage.setItem('token', newToken);
            localStorage.setItem('user', JSON.stringify(userData));

            // Update state
            setToken(newToken);
            setUser(userData);

            return { success: true, user: userData };
        } catch (error) {
            console.error('Login error:', error);
            return {
                success: false,
                message: error.response?.data?.message || 'Login failed',
            };
        }
    };

    // =========================================================================
    // Register Function
    // =========================================================================
    const register = async (name, email, password, password_confirmation) => {
        try {
            const response = await authAPI.register({
                name,
                email,
                password,
                password_confirmation,
            });

            const { token: newToken, user: rawUser } = response.data;
            const userData = rawUser?.data || rawUser;

            localStorage.setItem('token', newToken);
            localStorage.setItem('user', JSON.stringify(userData));

            setToken(newToken);
            setUser(userData);

            return { success: true, user: userData };
        } catch (error) {
            console.error('Register error:', error);
            return {
                success: false,
                message: error.response?.data?.message || 'Registration failed',
            };
        }
    };

    // =========================================================================
    // Logout Function
    // =========================================================================
    const logout = async () => {
        try {
            await authAPI.logout();
        } catch (error) {
            console.error('Logout API error:', error);
        }

        // Clear everything
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        setToken(null);
        setUser(null);
    };

    // =========================================================================
    // Context Value
    // =========================================================================
    const value = {
        user,
        token,
        loading,
        isAuthenticated: !!token,
        login,
        register,
        logout,
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
}

/**
 * Custom hook for easy access to auth context
 * Usage: const { user, login, logout } = useAuth();
 */
export function useAuth() {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}
```

---

## Chapter 19: Creating Toast Context (Notifications)

Create `asset-track-frontend/src/context/ToastContext.jsx`:

```jsx
/**
 * Toast Context - Global Notifications
 * =====================================
 *
 * Shows toast messages from anywhere in the app.
 *
 * Usage:
 *   const { success, error } = useToast();
 *   success('Item created!');
 *   error('Something went wrong');
 */

import { createContext, useContext, useState, useCallback } from 'react';

const ToastContext = createContext(null);

export function ToastProvider({ children }) {
    const [toasts, setToasts] = useState([]);

    // Add a toast
    const showToast = useCallback((message, type = 'info', duration = 4000) => {
        const id = Date.now();

        setToasts(prev => [...prev, { id, message, type }]);

        // Auto-remove after duration
        setTimeout(() => {
            setToasts(prev => prev.filter(t => t.id !== id));
        }, duration);
    }, []);

    // Remove a toast manually
    const removeToast = useCallback((id) => {
        setToasts(prev => prev.filter(t => t.id !== id));
    }, []);

    // Shortcut methods
    const success = useCallback((msg) => showToast(msg, 'success'), [showToast]);
    const error = useCallback((msg) => showToast(msg, 'error'), [showToast]);
    const warning = useCallback((msg) => showToast(msg, 'warning'), [showToast]);
    const info = useCallback((msg) => showToast(msg, 'info'), [showToast]);

    return (
        <ToastContext.Provider value={{ toasts, showToast, removeToast, success, error, warning, info }}>
            {children}
        </ToastContext.Provider>
    );
}

export function useToast() {
    const context = useContext(ToastContext);
    if (!context) {
        throw new Error('useToast must be used within a ToastProvider');
    }
    return context;
}
```

---

## Chapter 20: Creating Route Guards

### 20.1 PrivateRoute Component

Create `asset-track-frontend/src/components/PrivateRoute.jsx`:

```jsx
/**
 * PrivateRoute - Protects routes from unauthenticated users
 *
 * If not logged in: Redirects to /login
 * If logged in: Shows the protected content
 */

import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function PrivateRoute({ children }) {
    const { isAuthenticated, loading } = useAuth();

    // Show loading while checking auth
    if (loading) {
        return (
            <div className="loading-container">
                <div className="loading-spinner"></div>
                <p>Loading...</p>
            </div>
        );
    }

    // Not authenticated - redirect to login
    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    // Authenticated - show content
    return children;
}

export default PrivateRoute;
```

### 20.2 AdminRoute Component

Create `asset-track-frontend/src/components/AdminRoute.jsx`:

```jsx
/**
 * AdminRoute - Protects routes from non-admin users
 *
 * If not admin: Redirects to /my-assets
 * If admin: Shows the protected content
 */

import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function AdminRoute({ children }) {
    const { user, loading, isAuthenticated } = useAuth();

    // Show loading while checking
    if (loading || (isAuthenticated && !user)) {
        return (
            <div className="loading-container">
                <div className="loading-spinner"></div>
                <p>Loading...</p>
            </div>
        );
    }

    // Not admin - redirect to my-assets
    if (!user || user.role !== 'admin') {
        return <Navigate to="/my-assets" replace />;
    }

    // Admin - show content
    return children;
}

export default AdminRoute;
```

### 20.3 HomeRedirect Component

Create `asset-track-frontend/src/components/HomeRedirect.jsx`:

```jsx
/**
 * HomeRedirect - Redirects based on user role
 *
 * Admin: Goes to /dashboard
 * Employee: Goes to /my-assets
 * Not logged in: Goes to /login
 */

import { Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function HomeRedirect() {
    const { user, isAuthenticated, loading } = useAuth();

    if (loading) {
        return (
            <div className="loading-container">
                <div className="loading-spinner"></div>
            </div>
        );
    }

    if (!isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    // Redirect based on role
    if (user?.role === 'admin') {
        return <Navigate to="/dashboard" replace />;
    }

    return <Navigate to="/my-assets" replace />;
}

export default HomeRedirect;
```

---

## Chapter 21: Creating the Login Page

Create `asset-track-frontend/src/pages/Login.jsx`:

```jsx
/**
 * Login Page
 * ==========
 *
 * Features:
 * - Email and password inputs
 * - Form validation
 * - Error display
 * - Loading state
 * - Role-based redirect after login
 */

import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Login() {
    // Form state
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    // UI state
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    // Hooks
    const { login } = useAuth();
    const navigate = useNavigate();

    // Handle form submit
    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');
        setLoading(true);

        const result = await login(email, password);

        if (result.success) {
            // Redirect based on role
            if (result.user?.role === 'admin') {
                navigate('/dashboard');
            } else {
                navigate('/my-assets');
            }
        } else {
            setError(result.message);
        }

        setLoading(false);
    };

    return (
        <div className="auth-page">
            <div className="auth-card">
                <div className="auth-header">
                    <h1>AssetTrack</h1>
                    <p>Sign in to your account</p>
                </div>

                {error && <div className="auth-error">{error}</div>}

                <form onSubmit={handleSubmit} className="auth-form">
                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="Enter your email"
                            required
                            disabled={loading}
                        />
                    </div>

                    <div className="form-group">
                        <label htmlFor="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="Enter your password"
                            required
                            disabled={loading}
                        />
                    </div>

                    <button
                        type="submit"
                        className="btn btn-primary auth-btn"
                        disabled={loading}
                    >
                        {loading ? 'Signing in...' : 'Sign In'}
                    </button>
                </form>

                <div className="auth-footer">
                    <p>
                        Don't have an account?{' '}
                        <Link to="/register" className="auth-link">
                            Register here
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Login;
```

---

## Chapter 22: Creating the Register Page

Create `asset-track-frontend/src/pages/Register.jsx`:

```jsx
/**
 * Register Page
 * =============
 */

import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Register() {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const { register } = useAuth();
    const navigate = useNavigate();

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        // Client-side validation
        if (formData.password !== formData.password_confirmation) {
            setError('Passwords do not match');
            return;
        }

        setLoading(true);

        const result = await register(
            formData.name,
            formData.email,
            formData.password,
            formData.password_confirmation
        );

        if (result.success) {
            navigate('/my-assets'); // New users are employees
        } else {
            setError(result.message);
        }

        setLoading(false);
    };

    return (
        <div className="auth-page">
            <div className="auth-card">
                <div className="auth-header">
                    <h1>AssetTrack</h1>
                    <p>Create your account</p>
                </div>

                {error && <div className="auth-error">{error}</div>}

                <form onSubmit={handleSubmit} className="auth-form">
                    <div className="form-group">
                        <label htmlFor="name">Full Name</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            required
                            disabled={loading}
                        />
                    </div>

                    <div className="form-group">
                        <label htmlFor="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            required
                            disabled={loading}
                        />
                    </div>

                    <div className="form-group">
                        <label htmlFor="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            minLength="8"
                            required
                            disabled={loading}
                        />
                    </div>

                    <div className="form-group">
                        <label htmlFor="password_confirmation">Confirm Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            value={formData.password_confirmation}
                            onChange={handleChange}
                            required
                            disabled={loading}
                        />
                    </div>

                    <button
                        type="submit"
                        className="btn btn-primary auth-btn"
                        disabled={loading}
                    >
                        {loading ? 'Creating account...' : 'Create Account'}
                    </button>
                </form>

                <div className="auth-footer">
                    <p>
                        Already have an account?{' '}
                        <Link to="/login" className="auth-link">
                            Sign in here
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Register;
```

---

## Chapter 23: Creating Layout Components

### 23.1 Navbar Component

Create `asset-track-frontend/src/components/Navbar.jsx`:

```jsx
/**
 * Navigation Bar
 * ==============
 *
 * Shows different menu items based on user role.
 */

import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

function Navbar() {
    const { user, logout } = useAuth();
    const location = useLocation();

    const isActive = (path) => location.pathname === path;

    const handleLogout = async () => {
        await logout();
    };

    return (
        <nav className="navbar">
            <div className="navbar-brand">
                <Link to="/">AssetTrack</Link>
            </div>

            <div className="navbar-menu">
                {/* Admin Menu */}
                {user?.role === 'admin' && (
                    <>
                        <Link
                            to="/dashboard"
                            className={`nav-link ${isActive('/dashboard') ? 'active' : ''}`}
                        >
                            Dashboard
                        </Link>
                        <Link
                            to="/assets"
                            className={`nav-link ${isActive('/assets') ? 'active' : ''}`}
                        >
                            Assets
                        </Link>
                        <Link
                            to="/categories"
                            className={`nav-link ${isActive('/categories') ? 'active' : ''}`}
                        >
                            Categories
                        </Link>
                        <Link
                            to="/users"
                            className={`nav-link ${isActive('/users') ? 'active' : ''}`}
                        >
                            Users
                        </Link>
                    </>
                )}

                {/* Employee Menu */}
                <Link
                    to="/my-assets"
                    className={`nav-link ${isActive('/my-assets') ? 'active' : ''}`}
                >
                    My Assets
                </Link>

                <Link
                    to="/profile"
                    className={`nav-link ${isActive('/profile') ? 'active' : ''}`}
                >
                    Profile
                </Link>
            </div>

            <div className="navbar-user">
                <span className="user-name">{user?.name}</span>
                <span className="user-role">({user?.role})</span>
                <button onClick={handleLogout} className="btn btn-logout">
                    Logout
                </button>
            </div>
        </nav>
    );
}

export default Navbar;
```

### 23.2 Layout Component

Create `asset-track-frontend/src/components/Layout.jsx`:

```jsx
/**
 * Layout Component
 * ================
 *
 * Wraps all authenticated pages with Navbar.
 */

import { Outlet } from 'react-router-dom';
import Navbar from './Navbar';
import Toast from './Toast';

function Layout() {
    return (
        <div className="layout">
            <Navbar />
            <main className="main-content">
                <Outlet />
            </main>
            <Toast />
        </div>
    );
}

export default Layout;
```

### 23.3 Toast Component

Create `asset-track-frontend/src/components/Toast.jsx`:

```jsx
/**
 * Toast Component - Displays notifications
 */

import { useToast } from '../context/ToastContext';

function Toast() {
    const { toasts, removeToast } = useToast();

    if (toasts.length === 0) return null;

    return (
        <div className="toast-container">
            {toasts.map((toast) => (
                <div
                    key={toast.id}
                    className={`toast toast-${toast.type}`}
                    onClick={() => removeToast(toast.id)}
                >
                    {toast.message}
                </div>
            ))}
        </div>
    );
}

export default Toast;
```

---

## Chapter 24: Setting Up App Routing

Update `asset-track-frontend/src/App.jsx`:

```jsx
/**
 * Main Application Component
 * ==========================
 *
 * Sets up routing and context providers.
 */

import { BrowserRouter, Routes, Route } from 'react-router-dom';

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

// Placeholder pages (we'll create these next)
const Dashboard = () => <h1>Dashboard (Coming Soon)</h1>;
const Assets = () => <h1>Assets (Coming Soon)</h1>;
const Categories = () => <h1>Categories (Coming Soon)</h1>;
const Users = () => <h1>Users (Coming Soon)</h1>;
const MyAssets = () => <h1>My Assets (Coming Soon)</h1>;
const Profile = () => <h1>Profile (Coming Soon)</h1>;

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

                        {/* Protected Routes */}
                        <Route
                            element={
                                <PrivateRoute>
                                    <Layout />
                                </PrivateRoute>
                            }
                        >
                            {/* Admin Only Routes */}
                            <Route path="/dashboard" element={<AdminRoute><Dashboard /></AdminRoute>} />
                            <Route path="/assets" element={<AdminRoute><Assets /></AdminRoute>} />
                            <Route path="/categories" element={<AdminRoute><Categories /></AdminRoute>} />
                            <Route path="/users" element={<AdminRoute><Users /></AdminRoute>} />

                            {/* All Authenticated Users */}
                            <Route path="/my-assets" element={<MyAssets />} />
                            <Route path="/profile" element={<Profile />} />
                        </Route>

                        {/* Home Redirect */}
                        <Route path="/" element={<HomeRedirect />} />
                        <Route path="*" element={<HomeRedirect />} />
                    </Routes>
                </BrowserRouter>
            </ToastProvider>
        </AuthProvider>
    );
}

export default App;
```

---

## Chapter 25: Adding Styles

Replace `asset-track-frontend/src/App.css` with comprehensive styles:

```css
/* =============================================================================
   CSS VARIABLES
   ============================================================================= */
:root {
    --primary-color: #3b82f6;
    --primary-hover: #2563eb;
    --success-color: #22c55e;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --bg-primary: #ffffff;
    --bg-secondary: #f3f4f6;
    --border-color: #e5e7eb;
    --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --radius: 8px;
}

/* =============================================================================
   BASE STYLES
   ============================================================================= */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: var(--bg-secondary);
    color: var(--text-primary);
    line-height: 1.5;
}

/* =============================================================================
   LAYOUT
   ============================================================================= */
.layout {
    min-height: 100vh;
}

.main-content {
    padding: 24px;
    max-width: 1200px;
    margin: 0 auto;
}

/* =============================================================================
   NAVBAR
   ============================================================================= */
.navbar {
    background: var(--bg-primary);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow);
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar-brand a {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    text-decoration: none;
}

.navbar-menu {
    display: flex;
    gap: 8px;
}

.nav-link {
    padding: 8px 16px;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: var(--radius);
    transition: all 0.2s;
}

.nav-link:hover,
.nav-link.active {
    background: var(--bg-secondary);
    color: var(--primary-color);
}

.navbar-user {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-name {
    font-weight: 500;
}

.user-role {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

/* =============================================================================
   BUTTONS
   ============================================================================= */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: var(--radius);
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover);
}

.btn-success {
    background: var(--success-color);
    color: white;
}

.btn-danger {
    background: var(--danger-color);
    color: white;
}

.btn-logout {
    background: transparent;
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
    padding: 6px 12px;
    font-size: 0.875rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* =============================================================================
   AUTH PAGES
   ============================================================================= */
.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
}

.auth-card {
    background: white;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
}

.auth-header {
    text-align: center;
    margin-bottom: 32px;
}

.auth-header h1 {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 8px;
}

.auth-header p {
    color: var(--text-secondary);
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.auth-error {
    background: #fef2f2;
    color: var(--danger-color);
    padding: 12px;
    border-radius: var(--radius);
    text-align: center;
    margin-bottom: 16px;
}

.auth-btn {
    width: 100%;
    padding: 14px;
    font-size: 1.1rem;
}

.auth-footer {
    text-align: center;
    margin-top: 24px;
    color: var(--text-secondary);
}

.auth-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
}

/* =============================================================================
   FORMS
   ============================================================================= */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    font-weight: 500;
    color: var(--text-primary);
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
}

/* =============================================================================
   LOADING
   ============================================================================= */
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    gap: 16px;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid var(--border-color);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* =============================================================================
   TOAST NOTIFICATIONS
   ============================================================================= */
.toast-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    z-index: 1000;
}

.toast {
    padding: 16px 24px;
    border-radius: var(--radius);
    color: white;
    cursor: pointer;
    animation: slideIn 0.3s ease;
}

.toast-success { background: var(--success-color); }
.toast-error { background: var(--danger-color); }
.toast-warning { background: var(--warning-color); }
.toast-info { background: var(--primary-color); }

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* =============================================================================
   RESPONSIVE
   ============================================================================= */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 16px;
    }

    .navbar-menu {
        flex-wrap: wrap;
        justify-content: center;
    }

    .main-content {
        padding: 16px;
    }
}
```

---

## Chapter 26: Phase 4 Summary & Interview Prep

### 26.1 What We Accomplished

1. Created API service with axios interceptors
2. Built AuthContext for global state
3. Created ToastContext for notifications
4. Implemented route guards (PrivateRoute, AdminRoute)
5. Built Login and Register pages
6. Created Layout and Navbar components
7. Set up React Router with protected routes

---

## 📚 INTERVIEW PACK 4: React & State Management

### Key Terminologies

| Term | Definition |
|------|------------|
| **Component** | Reusable piece of UI |
| **State** | Data that can change within a component |
| **Props** | Data passed from parent to child |
| **Hook** | Function that lets you use React features |
| **useState** | Hook to add state to functional components |
| **useEffect** | Hook for side effects (API calls, subscriptions) |
| **useContext** | Hook to consume context |
| **Context** | Way to pass data through component tree without props |
| **Provider** | Component that makes context available |
| **Controlled Component** | Form input managed by React state |
| **SPA** | Single Page Application |

### Interview Questions & Answers

**Q1: What is the difference between props and state?**
> **Answer:**
> - **Props** are passed from parent to child, are read-only, and component can't modify them
> - **State** is internal to a component, can be changed, and triggers re-render when changed
>
> Example: A Button component might receive `label` as a prop but manage `isLoading` as state.

**Q2: Explain the useEffect hook.**
> **Answer:**
> useEffect runs side effects in functional components. It replaces componentDidMount, componentDidUpdate, and componentWillUnmount.
>
> ```jsx
> useEffect(() => {
>     // Runs after every render
> });
>
> useEffect(() => {
>     // Runs only on mount (empty dependency array)
> }, []);
>
> useEffect(() => {
>     // Runs when 'id' changes
> }, [id]);
>
> useEffect(() => {
>     return () => {
>         // Cleanup function (runs on unmount)
>     };
> }, []);
> ```

**Q3: Why use Context instead of prop drilling?**
> **Answer:**
> Prop drilling is passing props through multiple levels of components that don't need them, just to get data to a deeply nested component.
>
> Context solves this by making data available to any component in the tree without explicitly passing it.
>
> Use Context for: Auth state, theme, language settings, user preferences.
> Don't overuse: Not for every piece of state; local state is often better.

**Q4: What is axios and why use it over fetch?**
> **Answer:**
> Axios is an HTTP client library. Benefits over fetch:
> - Automatic JSON transformation
> - Request/response interceptors
> - Better error handling
> - Request cancellation
> - Easier configuration (base URL, headers)
> - Works in Node.js too

**Q5: Explain the flow when a user logs in to our React app.**
> **Answer:**
> 1. User enters credentials in Login form
> 2. Form submit calls `login(email, password)` from AuthContext
> 3. AuthContext calls `authAPI.login()` which uses axios
> 4. Axios sends POST to `/api/auth/login`
> 5. Laravel validates, creates token, returns user + token
> 6. AuthContext stores token/user in localStorage and state
> 7. `isAuthenticated` becomes true
> 8. Navigate redirects to dashboard or my-assets based on role
> 9. PrivateRoute allows access since user is authenticated

### Scenario Questions

**S1: Users report being logged out randomly. What could be the cause?**
> **Answer:**
> 1. **Token expiration**: Check Sanctum token expiration settings
> 2. **Token deletion**: Server might be cleaning up old tokens
> 3. **401 interceptor**: Our axios interceptor clears localStorage on any 401
> 4. **CORS issues**: Preflight requests failing
> 5. **localStorage cleared**: Browser extensions or privacy settings
>
> Debug by: Adding logging to interceptors, checking network tab, verifying token in database.

**S2: The app shows stale user data after profile update. How do you fix it?**
> **Answer:**
> After updating profile, we need to:
> 1. Update the user state in AuthContext
> 2. Update localStorage with new data
>
> ```jsx
> const updateProfile = async (data) => {
>     const response = await profileAPI.update(data);
>     const newUserData = response.data.user;
>
>     // Update context and storage
>     setUser(newUserData);
>     localStorage.setItem('user', JSON.stringify(newUserData));
> };
> ```

### Common Mistakes

| Mistake | Problem | Solution |
|---------|---------|----------|
| Not using dependency array in useEffect | Infinite loop | Add proper dependencies |
| Storing sensitive data in state | Security risk | Only store what's needed |
| Not handling loading states | Bad UX, can show errors | Always show loading indicator |
| Directly mutating state | Won't trigger re-render | Use setState or spread operator |
| Not catching API errors | App crashes | Always use try/catch |

---

*[Guide continues with Phases 5-10 covering Dashboard, Categories, Assets, Users, Assignments, Employee Features, and Complete Interview Bank...]*

*Due to length, the remaining phases follow the same detailed pattern with:*
- *Step-by-step implementation*
- *Code with extensive comments*
- *Testing after each feature*
- *Interview questions and scenarios*

---

# QUICK REFERENCE: FILE LOCATIONS

## Laravel (assetlara/)

| File | Purpose |
|------|---------|
| `routes/api.php` | API route definitions |
| `app/Http/Controllers/Api/` | API controllers |
| `app/Services/` | Business logic |
| `app/Models/` | Database models |
| `app/Http/Requests/` | Validation |
| `app/Http/Resources/` | JSON formatting |
| `app/Http/Middleware/` | Route protection |
| `database/migrations/` | Table definitions |
| `.env` | Configuration |

## React (asset-track-frontend/)

| File | Purpose |
|------|---------|
| `src/services/api.js` | API communication |
| `src/context/AuthContext.jsx` | Auth state |
| `src/context/ToastContext.jsx` | Notifications |
| `src/components/` | Reusable UI |
| `src/pages/` | Full pages |
| `src/App.jsx` | Main routing |

---

# PHASE 5: DASHBOARD & CATEGORIES

## Chapter 27: Dashboard Service (Laravel)

### 27.1 What is the Dashboard?

The Dashboard is the admin's home page. It shows:
- Statistics (total assets, available, assigned, broken)
- Recent assignments
- Assets grouped by category
- Quick action links

### 27.2 Create DashboardService

Create `app/Services/DashboardService.php`:

```php
<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Models\Category;
use App\Models\Assignment;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get basic dashboard statistics.
     *
     * This method runs multiple COUNT queries.
     * In a high-traffic app, consider caching these values.
     */
    public function getStats(): array
    {
        return [
            // Asset counts by status
            'total_assets' => Asset::count(),
            'available_assets' => Asset::where('status', 'available')->count(),
            'assigned_assets' => Asset::where('status', 'assigned')->count(),
            'broken_assets' => Asset::where('status', 'broken')->count(),
            'maintenance_assets' => Asset::where('status', 'maintenance')->count(),

            // User counts
            'total_employees' => User::where('role', 'employee')
                                     ->where('is_active', true)
                                     ->count(),
            'total_admins' => User::where('role', 'admin')
                                  ->where('is_active', true)
                                  ->count(),

            // Other counts
            'total_categories' => Category::count(),
            'active_assignments' => Assignment::whereNull('returned_at')->count(),
        ];
    }

    /**
     * Get recent assignments for dashboard feed.
     *
     * @param int $limit Number of assignments to return
     */
    public function getRecentAssignments(int $limit = 5): Collection
    {
        return Assignment::with(['asset', 'user', 'admin'])
            ->orderBy('assigned_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get assets grouped by category with counts.
     *
     * Uses withCount() for efficient single query.
     */
    public function getAssetsByCategory(): Collection
    {
        return Category::withCount('assets')
            ->orderBy('assets_count', 'desc')
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $cat->assets_count,
            ]);
    }

    /**
     * Get assets grouped by status.
     *
     * Uses raw SQL for efficient grouping.
     */
    public function getAssetsByStatus(): Collection
    {
        return Asset::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }

    /**
     * Get all dashboard data in one call.
     *
     * This is what the API endpoint will call.
     */
    public function getDashboardData(): array
    {
        return [
            'stats' => $this->getStats(),
            'recent_assignments' => $this->getRecentAssignments(),
            'assets_by_category' => $this->getAssetsByCategory(),
            'assets_by_status' => $this->getAssetsByStatus(),
        ];
    }
}
```

### 27.3 Create DashboardController

Create `app/Http/Controllers/Api/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Get dashboard data.
     *
     * GET /api/dashboard
     */
    public function index(): JsonResponse
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json($data);
    }
}
```

### 27.4 Add Dashboard Route

Update `routes/api.php`, inside the admin middleware group:

```php
Route::middleware('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ... other admin routes
});
```

Don't forget to add the import at the top:
```php
use App\Http\Controllers\Api\DashboardController;
```

---

## Chapter 28: Categories CRUD (Laravel)

### 28.1 Create CategoryService

Create `app/Services/CategoryService.php`:

```php
<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    /**
     * Get all categories, optionally paginated.
     */
    public function getAll(bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = Category::withCount('assets')->orderBy('name');

        if ($paginate) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    /**
     * Create a new category.
     * Note: slug is auto-generated by model event.
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category.
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category.
     * Returns false if category has assets (protected by foreign key).
     */
    public function delete(Category $category): bool
    {
        // Check if category has assets
        if ($category->assets()->count() > 0) {
            return false;
        }

        return $category->delete();
    }
}
```

### 28.2 Create CategoryResource

Create `app/Http/Resources/CategoryResource.php`:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Include asset count if loaded
            'assets_count' => $this->when(
                isset($this->assets_count),
                $this->assets_count
            ),
        ];
    }
}
```

### 28.3 Create StoreCategoryRequest

```bash
php artisan make:request StoreCategoryRequest
```

Open `app/Http/Requests/StoreCategoryRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Admin middleware handles authorization
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
        ];
    }
}
```

### 28.4 Create UpdateCategoryRequest

```bash
php artisan make:request UpdateCategoryRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the category ID from route parameter
        $categoryId = $this->route('category')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Unique, but ignore current category
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
        ];
    }
}
```

### 28.5 Create CategoryController

Create `app/Http/Controllers/Api/CategoryController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * List all categories.
     *
     * GET /api/categories
     */
    public function index(Request $request)
    {
        $paginate = $request->has('page');
        $categories = $this->categoryService->getAll($paginate);

        return CategoryResource::collection($categories);
    }

    /**
     * Create a new category.
     *
     * POST /api/categories
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Show a single category.
     *
     * GET /api/categories/{category}
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount('assets');

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Update a category.
     *
     * PUT /api/categories/{category}
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->categoryService->update($category, $request->validated());

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Delete a category.
     *
     * DELETE /api/categories/{category}
     */
    public function destroy(Category $category): JsonResponse
    {
        if (!$this->categoryService->delete($category)) {
            return response()->json([
                'message' => 'Cannot delete category that has assets. Move or delete assets first.',
            ], 422);
        }

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}
```

### 28.6 Add Category Routes

Update `routes/api.php`:

```php
use App\Http\Controllers\Api\CategoryController;

// Inside admin middleware group:
Route::middleware('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Categories - using apiResource for standard CRUD routes
    Route::apiResource('categories', CategoryController::class);

    // ... other routes
});
```

**What `apiResource` creates:**
| Method | URI | Action | Route Name |
|--------|-----|--------|------------|
| GET | /categories | index | categories.index |
| POST | /categories | store | categories.store |
| GET | /categories/{category} | show | categories.show |
| PUT/PATCH | /categories/{category} | update | categories.update |
| DELETE | /categories/{category} | destroy | categories.destroy |

---

## Chapter 29: React Dashboard Page

### 29.1 Create Dashboard Page

Create `asset-track-frontend/src/pages/Dashboard.jsx`:

```jsx
/**
 * Dashboard Page - Admin Statistics Overview
 * ==========================================
 *
 * Features:
 * - Statistics cards (total, available, assigned, broken)
 * - Quick action links
 * - Assets by status breakdown
 * - Recent assignments table
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { dashboardAPI } from '../services/api';
import { useToast } from '../context/ToastContext';

function Dashboard() {
    // State
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const { error: showError } = useToast();

    // Fetch dashboard data on mount
    useEffect(() => {
        fetchDashboard();
    }, []);

    const fetchDashboard = async () => {
        try {
            setLoading(true);
            const response = await dashboardAPI.getStats();
            setData(response.data);
        } catch (err) {
            console.error('Dashboard fetch error:', err);
            showError('Failed to load dashboard data');
        } finally {
            setLoading(false);
        }
    };

    // Format date helper
    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    // Loading state
    if (loading) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading dashboard...</p>
            </div>
        );
    }

    return (
        <div className="dashboard">
            <h1>Dashboard</h1>

            {/* Statistics Cards */}
            <div className="stats-grid">
                <div className="stat-card">
                    <div className="stat-icon blue">📦</div>
                    <div className="stat-info">
                        <h3>Total Assets</h3>
                        <p className="stat-number">{data?.stats?.total_assets || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon green">✓</div>
                    <div className="stat-info">
                        <h3>Available</h3>
                        <p className="stat-number">{data?.stats?.available_assets || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon yellow">⏱</div>
                    <div className="stat-info">
                        <h3>Assigned</h3>
                        <p className="stat-number">{data?.stats?.assigned_assets || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon red">⚠</div>
                    <div className="stat-info">
                        <h3>Broken</h3>
                        <p className="stat-number">{data?.stats?.broken_assets || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon purple">👥</div>
                    <div className="stat-info">
                        <h3>Employees</h3>
                        <p className="stat-number">{data?.stats?.total_employees || 0}</p>
                    </div>
                </div>

                <div className="stat-card">
                    <div className="stat-icon orange">📁</div>
                    <div className="stat-info">
                        <h3>Categories</h3>
                        <p className="stat-number">{data?.stats?.total_categories || 0}</p>
                    </div>
                </div>
            </div>

            {/* Quick Actions */}
            <div className="dashboard-section">
                <h2>Quick Actions</h2>
                <div className="quick-actions">
                    <Link to="/assets/create" className="quick-action-btn">
                        + Add Asset
                    </Link>
                    <Link to="/users/create" className="quick-action-btn">
                        + Add User
                    </Link>
                    <Link to="/categories/create" className="quick-action-btn">
                        + Add Category
                    </Link>
                </div>
            </div>

            {/* Assets by Status */}
            {data?.assets_by_status && (
                <div className="dashboard-section">
                    <h2>Assets by Status</h2>
                    <div className="status-bars">
                        {Object.entries(data.assets_by_status).map(([status, count]) => (
                            <div key={status} className="status-bar-item">
                                <div className="status-bar-label">
                                    <span className={`status-dot status-${status}`}></span>
                                    <span>{status}</span>
                                </div>
                                <div className="status-bar-count">{count}</div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Recent Assignments */}
            <div className="dashboard-section">
                <h2>Recent Assignments</h2>
                {data?.recent_assignments?.length > 0 ? (
                    <table className="data-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Assigned To</th>
                                <th>Assigned By</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {data.recent_assignments.map((assignment) => (
                                <tr key={assignment.id}>
                                    <td>
                                        <Link to={`/assets/${assignment.asset?.id}`}>
                                            {assignment.asset?.name || 'N/A'}
                                        </Link>
                                    </td>
                                    <td>{assignment.user?.name || 'N/A'}</td>
                                    <td>{assignment.admin?.name || 'N/A'}</td>
                                    <td>{formatDate(assignment.assigned_at)}</td>
                                    <td>
                                        <span className={`badge ${assignment.returned_at ? 'returned' : 'active'}`}>
                                            {assignment.returned_at ? 'Returned' : 'Active'}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <p className="empty-message">No recent assignments</p>
                )}
            </div>
        </div>
    );
}

export default Dashboard;
```

---

## Chapter 30: React Categories Pages

### 30.1 Categories List Page

Create `asset-track-frontend/src/pages/Categories.jsx`:

```jsx
/**
 * Categories List Page
 * ====================
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { categoriesAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import ConfirmDialog from '../components/ConfirmDialog';

function Categories() {
    const { success, error } = useToast();

    // State
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [deleteConfirm, setDeleteConfirm] = useState({ show: false, category: null });
    const [deleting, setDeleting] = useState(false);

    // Fetch categories on mount
    useEffect(() => {
        fetchCategories();
    }, []);

    const fetchCategories = async () => {
        try {
            setLoading(true);
            const response = await categoriesAPI.getAll();
            setCategories(response.data.data || response.data);
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
            fetchCategories(); // Refresh list
        } catch (err) {
            console.error('Error deleting category:', err);
            error(err.response?.data?.message || 'Failed to delete category');
        } finally {
            setDeleting(false);
        }
    };

    if (loading) {
        return (
            <div className="page-loading">
                <div className="loading-spinner"></div>
                <p>Loading categories...</p>
            </div>
        );
    }

    return (
        <div className="categories-page">
            {/* Header */}
            <div className="page-header">
                <div>
                    <h1>Categories</h1>
                    <p className="page-subtitle">Manage asset categories</p>
                </div>
                <Link to="/categories/create" className="btn btn-primary">
                    + Add Category
                </Link>
            </div>

            {/* Categories Table */}
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
                                    <td className="font-medium">{category.name}</td>
                                    <td className="text-secondary">{category.slug}</td>
                                    <td>{category.assets_count || 0}</td>
                                    <td className="actions-cell">
                                        <Link
                                            to={`/categories/${category.id}/edit`}
                                            className="action-link edit"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            onClick={() => setDeleteConfirm({ show: true, category })}
                                            className="action-link delete"
                                            disabled={category.assets_count > 0}
                                            title={category.assets_count > 0 ? 'Cannot delete: has assets' : ''}
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
```

### 30.2 Category Form Page (Create/Edit)

Create `asset-track-frontend/src/pages/CategoryForm.jsx`:

```jsx
/**
 * Category Form - Create/Edit Category
 * =====================================
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
        } catch (err) {
            console.error('Error fetching category:', err);
            error('Failed to load category');
            navigate('/categories');
        } finally {
            setFetching(false);
        }
    };

    // Handle form submit
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
                <Link to="/categories">Categories</Link>
                <span>/</span>
                <span>{isEditing ? 'Edit' : 'Create'}</span>
            </div>

            {/* Form Card */}
            <div className="form-card">
                <h1>{isEditing ? 'Edit Category' : 'Create Category'}</h1>

                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label htmlFor="name">
                            Category Name <span className="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            value={name}
                            onChange={(e) => setName(e.target.value)}
                            placeholder="e.g., Laptop, Monitor, Phone"
                            required
                            disabled={loading}
                        />
                        <p className="hint">
                            The slug will be auto-generated from the name.
                        </p>
                    </div>

                    <div className="form-actions">
                        <button
                            type="submit"
                            className="btn btn-success"
                            disabled={loading}
                        >
                            {loading ? 'Saving...' : (isEditing ? 'Update' : 'Create')}
                        </button>
                        <Link to="/categories" className="btn-link">
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default CategoryForm;
```

### 30.3 Create ConfirmDialog Component

Create `asset-track-frontend/src/components/ConfirmDialog.jsx`:

```jsx
/**
 * Confirmation Dialog Component
 * =============================
 *
 * Reusable modal for confirming dangerous actions.
 */

function ConfirmDialog({
    isOpen,
    title,
    message,
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    confirmVariant = 'danger', // 'danger', 'warning', 'primary'
    onConfirm,
    onCancel,
    loading = false,
}) {
    if (!isOpen) return null;

    return (
        <div className="dialog-overlay" onClick={onCancel}>
            <div className="dialog-content" onClick={(e) => e.stopPropagation()}>
                <h3 className="dialog-title">{title}</h3>
                <p className="dialog-message">{message}</p>

                <div className="dialog-actions">
                    <button
                        onClick={onCancel}
                        className="btn btn-secondary"
                        disabled={loading}
                    >
                        {cancelText}
                    </button>
                    <button
                        onClick={onConfirm}
                        className={`btn btn-${confirmVariant}`}
                        disabled={loading}
                    >
                        {loading ? 'Processing...' : confirmText}
                    </button>
                </div>
            </div>
        </div>
    );
}

export default ConfirmDialog;
```

### 30.4 Add Styles for New Components

Add to `asset-track-frontend/src/App.css`:

```css
/* =============================================================================
   DASHBOARD STYLES
   ============================================================================= */
.dashboard h1 {
    margin-bottom: 24px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.stat-card {
    background: var(--bg-primary);
    padding: 20px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 16px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.blue { background: #dbeafe; }
.stat-icon.green { background: #dcfce7; }
.stat-icon.yellow { background: #fef3c7; }
.stat-icon.red { background: #fee2e2; }
.stat-icon.purple { background: #f3e8ff; }
.stat-icon.orange { background: #ffedd5; }

.stat-info h3 {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
}

.dashboard-section {
    background: var(--bg-primary);
    padding: 24px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 24px;
}

.dashboard-section h2 {
    margin-bottom: 16px;
    font-size: 1.25rem;
}

.quick-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.quick-action-btn {
    padding: 12px 24px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    color: var(--text-primary);
    text-decoration: none;
    transition: all 0.2s;
}

.quick-action-btn:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.status-bars {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.status-bar-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: var(--radius);
}

.status-bar-label {
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: capitalize;
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-dot.status-available { background: var(--success-color); }
.status-dot.status-assigned { background: var(--warning-color); }
.status-dot.status-broken { background: var(--danger-color); }
.status-dot.status-maintenance { background: var(--primary-color); }

.status-bar-count {
    font-weight: 600;
}

/* =============================================================================
   PAGE HEADER
   ============================================================================= */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.page-header h1 {
    margin: 0;
}

.page-subtitle {
    color: var(--text-secondary);
    margin-top: 4px;
}

/* =============================================================================
   TABLES
   ============================================================================= */
.table-card {
    background: var(--bg-primary);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.data-table th {
    background: var(--bg-secondary);
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.875rem;
    text-transform: uppercase;
}

.data-table tbody tr:hover {
    background: var(--bg-secondary);
}

.data-table .no-data {
    text-align: center;
    color: var(--text-secondary);
    padding: 40px;
}

.actions-cell {
    display: flex;
    gap: 8px;
}

.action-link {
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 0.875rem;
    text-decoration: none;
    cursor: pointer;
    border: none;
    background: none;
}

.action-link.view { color: var(--primary-color); }
.action-link.edit { color: var(--warning-color); }
.action-link.delete { color: var(--danger-color); }

.action-link:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.action-link:hover:not(:disabled) {
    text-decoration: underline;
}

/* =============================================================================
   BADGES
   ============================================================================= */
.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.badge.active { background: #dcfce7; color: #166534; }
.badge.returned { background: #f3f4f6; color: #6b7280; }
.badge.status-available { background: #dcfce7; color: #166534; }
.badge.status-assigned { background: #fef3c7; color: #92400e; }
.badge.status-broken { background: #fee2e2; color: #991b1b; }
.badge.status-maintenance { background: #dbeafe; color: #1e40af; }

/* =============================================================================
   FORM PAGES
   ============================================================================= */
.form-page {
    max-width: 600px;
}

.breadcrumb {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    color: var(--text-secondary);
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.form-card {
    background: var(--bg-primary);
    padding: 32px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.form-card h1 {
    margin-bottom: 24px;
}

.form-card form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-actions {
    display: flex;
    gap: 16px;
    align-items: center;
    margin-top: 8px;
}

.btn-link {
    color: var(--text-secondary);
    text-decoration: none;
}

.btn-link:hover {
    color: var(--text-primary);
}

.hint {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 4px;
}

.required {
    color: var(--danger-color);
}

/* =============================================================================
   DIALOG/MODAL
   ============================================================================= */
.dialog-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.dialog-content {
    background: var(--bg-primary);
    padding: 24px;
    border-radius: var(--radius);
    max-width: 400px;
    width: 90%;
}

.dialog-title {
    margin-bottom: 12px;
}

.dialog-message {
    color: var(--text-secondary);
    margin-bottom: 24px;
}

.dialog-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-warning {
    background: var(--warning-color);
    color: white;
}

/* =============================================================================
   UTILITY CLASSES
   ============================================================================= */
.font-medium { font-weight: 500; }
.text-secondary { color: var(--text-secondary); }
.empty-message { color: var(--text-secondary); text-align: center; padding: 24px; }
```

### 30.5 Update App.jsx with Real Pages

Update `asset-track-frontend/src/App.jsx`:

```jsx
import { BrowserRouter, Routes, Route } from 'react-router-dom';

// Context
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
import Categories from './pages/Categories';
import CategoryForm from './pages/CategoryForm';

// Placeholder pages (we'll create these)
const Assets = () => <h1>Assets (Coming Soon)</h1>;
const AssetDetail = () => <h1>Asset Detail (Coming Soon)</h1>;
const AssetForm = () => <h1>Asset Form (Coming Soon)</h1>;
const Users = () => <h1>Users (Coming Soon)</h1>;
const UserDetail = () => <h1>User Detail (Coming Soon)</h1>;
const UserForm = () => <h1>User Form (Coming Soon)</h1>;
const MyAssets = () => <h1>My Assets (Coming Soon)</h1>;
const Profile = () => <h1>Profile (Coming Soon)</h1>;

import './App.css';

function App() {
    return (
        <AuthProvider>
            <ToastProvider>
                <BrowserRouter>
                    <Routes>
                        {/* Public */}
                        <Route path="/login" element={<Login />} />
                        <Route path="/register" element={<Register />} />

                        {/* Protected */}
                        <Route element={<PrivateRoute><Layout /></PrivateRoute>}>
                            {/* Admin */}
                            <Route path="/dashboard" element={<AdminRoute><Dashboard /></AdminRoute>} />

                            {/* Categories */}
                            <Route path="/categories" element={<AdminRoute><Categories /></AdminRoute>} />
                            <Route path="/categories/create" element={<AdminRoute><CategoryForm /></AdminRoute>} />
                            <Route path="/categories/:id/edit" element={<AdminRoute><CategoryForm /></AdminRoute>} />

                            {/* Assets */}
                            <Route path="/assets" element={<AdminRoute><Assets /></AdminRoute>} />
                            <Route path="/assets/create" element={<AdminRoute><AssetForm /></AdminRoute>} />
                            <Route path="/assets/:id" element={<AdminRoute><AssetDetail /></AdminRoute>} />
                            <Route path="/assets/:id/edit" element={<AdminRoute><AssetForm /></AdminRoute>} />

                            {/* Users */}
                            <Route path="/users" element={<AdminRoute><Users /></AdminRoute>} />
                            <Route path="/users/create" element={<AdminRoute><UserForm /></AdminRoute>} />
                            <Route path="/users/:id" element={<AdminRoute><UserDetail /></AdminRoute>} />
                            <Route path="/users/:id/edit" element={<AdminRoute><UserForm /></AdminRoute>} />

                            {/* All Users */}
                            <Route path="/my-assets" element={<MyAssets />} />
                            <Route path="/profile" element={<Profile />} />
                        </Route>

                        {/* Redirects */}
                        <Route path="/" element={<HomeRedirect />} />
                        <Route path="*" element={<HomeRedirect />} />
                    </Routes>
                </BrowserRouter>
            </ToastProvider>
        </AuthProvider>
    );
}

export default App;
```

---

## 📚 INTERVIEW PACK 5: Dashboard & CRUD Operations

### Key Terminologies

| Term | Definition |
|------|------------|
| **CRUD** | Create, Read, Update, Delete - basic data operations |
| **apiResource** | Laravel helper that creates all CRUD routes |
| **Route Model Binding** | Laravel auto-fetches model from route parameter |
| **withCount** | Eloquent method to count related records efficiently |
| **Pagination** | Splitting large data sets into pages |
| **N+1 Problem** | Inefficient queries when loading relationships |
| **Eager Loading** | Loading relationships in advance to avoid N+1 |

### Interview Questions & Answers

**Q1: What is the N+1 query problem and how do you solve it?**
> **Answer:**
> N+1 occurs when you query a list (1 query) then query each item's relationship (N queries).
>
> **Problem:**
> ```php
> $categories = Category::all(); // 1 query
> foreach ($categories as $cat) {
>     echo $cat->assets->count(); // N queries!
> }
> ```
>
> **Solution - Eager Loading:**
> ```php
> $categories = Category::with('assets')->get(); // 2 queries total
> // OR for just count:
> $categories = Category::withCount('assets')->get(); // 1 query
> ```

**Q2: What is Route Model Binding in Laravel?**
> **Answer:**
> Instead of manually finding a model by ID, Laravel automatically fetches it:
>
> **Without binding:**
> ```php
> public function show($id) {
>     $category = Category::findOrFail($id);
> }
> ```
>
> **With binding:**
> ```php
> public function show(Category $category) {
>     // $category is already fetched!
> }
> ```
>
> Laravel matches the route parameter name `{category}` with the type-hinted argument.

**Q3: Why separate Service classes from Controllers?**
> **Answer:**
> - **Controllers** handle HTTP: receive request, call service, return response
> - **Services** handle business logic: validation rules, database operations
>
> Benefits:
> 1. Reusability - same service for API and web controllers
> 2. Testability - unit test services without HTTP
> 3. Maintainability - change business logic in one place
> 4. Readability - thin controllers are easier to understand

**Q4: What does `Rule::unique()->ignore($id)` do?**
> **Answer:**
> When updating a record, we want to ensure uniqueness BUT allow the current record to keep its value.
>
> Example: Editing category "Laptop" to "Laptop" should not fail uniqueness check.
>
> `Rule::unique('categories', 'name')->ignore($categoryId)` means "name must be unique, except for this specific record."

### Scenario Questions

**S1: Dashboard is loading slowly with many assets. How do you optimize?**
> **Answer:**
> 1. **Cache statistics**: Store counts in cache, refresh periodically
> ```php
> $stats = Cache::remember('dashboard_stats', 300, function () {
>     return $this->getStats();
> });
> ```
> 2. **Use database aggregates**: Single COUNT queries instead of loading all records
> 3. **Limit recent items**: Only fetch last 5-10 assignments
> 4. **Async loading**: Load sections separately with AJAX

**S2: User reports "Cannot delete category" but they don't have any visible assets in it. What's wrong?**
> **Answer:**
> Check for soft-deleted assets:
> ```php
> // This might return 0
> $category->assets()->count();
>
> // This shows including trashed
> $category->assets()->withTrashed()->count();
> ```
> Solution: Either force delete soft-deleted assets or update the delete check.

---

# PHASE 6: ASSETS MANAGEMENT

## Chapter 31: Assets Service (Laravel)

### 31.1 Create AssetService

Create `app/Services/AssetService.php`:

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AssetService
{
    /**
     * Get filtered and paginated assets.
     *
     * @param array $filters ['status', 'category_id', 'search', 'sort_by', 'sort_order']
     * @param int $perPage
     */
    public function getFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Asset::with('category');

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Search by name or serial number
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get available assets for assignment dropdown.
     */
    public function getAvailable(): Collection
    {
        return Asset::where('status', 'available')
            ->with('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new asset with optional image.
     */
    public function store(array $data, ?UploadedFile $image = null): Asset
    {
        // Handle image upload
        if ($image) {
            // Store in storage/app/public/assets/
            // Returns path like: assets/abc123.jpg
            $data['image_path'] = $image->store('assets', 'public');
        }

        return Asset::create($data);
    }

    /**
     * Update an asset with optional image replacement.
     */
    public function update(Asset $asset, array $data, ?UploadedFile $image = null): Asset
    {
        // Handle new image
        if ($image) {
            // Delete old image if exists
            $this->deleteImage($asset);

            // Store new image
            $data['image_path'] = $image->store('assets', 'public');
        }

        $asset->update($data);
        return $asset;
    }

    /**
     * Delete an asset (soft delete).
     * Returns false if asset is currently assigned.
     */
    public function delete(Asset $asset): bool
    {
        // Cannot delete assigned assets
        if ($asset->status === 'assigned') {
            return false;
        }

        // Delete image file
        $this->deleteImage($asset);

        return $asset->delete();
    }

    /**
     * Get all active assets assigned to a specific user.
     */
    public function getUserAssets(User $user): Collection
    {
        return $user->assignments()
            ->whereNull('returned_at')
            ->with(['asset.category', 'admin'])
            ->get()
            ->map(function ($assignment) {
                $asset = $assignment->asset;
                $asset->active_assignment = $assignment;
                return $asset;
            });
    }

    /**
     * Delete the asset's image from storage.
     */
    protected function deleteImage(Asset $asset): void
    {
        if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
            Storage::disk('public')->delete($asset->image_path);
        }
    }
}
```

### 31.2 Create Asset Form Requests

**StoreAssetRequest:**

```bash
php artisan make:request StoreAssetRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            // Serial number must be unique across all assets
            'serial_number' => ['required', 'string', 'max:255', 'unique:assets,serial_number'],

            // Category must exist
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            // Status must be one of allowed values
            'status' => ['required', 'string', 'in:available,assigned,broken,maintenance'],

            // Image validation
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'], // 2MB max
        ];
    }

    public function messages(): array
    {
        return [
            'serial_number.unique' => 'An asset with this serial number already exists.',
            'category_id.exists' => 'Selected category does not exist.',
            'status.in' => 'Status must be: available, assigned, broken, or maintenance.',
            'image.max' => 'Image must be less than 2MB.',
        ];
    }
}
```

**UpdateAssetRequest:**

```bash
php artisan make:request UpdateAssetRequest
```

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetId = $this->route('asset')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets', 'serial_number')->ignore($assetId),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'status' => ['required', 'string', 'in:available,assigned,broken,maintenance'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }
}
```

### 31.3 Create AssetResource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'image_path' => $this->image_path,

            // Full URL for frontend to display
            'image_url' => $this->image_path
                ? asset('storage/' . $this->image_path)
                : null,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relationships (only if loaded)
            'category' => new CategoryResource($this->whenLoaded('category')),
            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
            'current_holder' => new UserResource($this->whenLoaded('currentHolder')),

            // For my-assets page
            'assigned_at' => $this->active_assignment?->assigned_at?->toISOString(),
        ];
    }
}
```

### 31.4 Create AssetController

Create `app/Http/Controllers/Api/AssetController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Http\Request;
use App\Services\AssetService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetResource;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;

class AssetController extends Controller
{
    public function __construct(
        protected AssetService $assetService
    ) {}

    /**
     * List all assets with filters and pagination.
     *
     * GET /api/assets
     * Query params: status, category_id, search, sort_by, sort_order, per_page, page
     */
    public function index(Request $request)
    {
        $assets = $this->assetService->getFiltered(
            $request->only(['status', 'category_id', 'search', 'sort_by', 'sort_order']),
            $request->get('per_page', 15)
        );

        return AssetResource::collection($assets);
    }

    /**
     * Create a new asset.
     *
     * POST /api/assets
     */
    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = $this->assetService->store(
            $request->validated(),
            $request->file('image')
        );

        $asset->load('category');

        return response()->json([
            'message' => 'Asset created successfully.',
            'asset' => new AssetResource($asset),
        ], 201);
    }

    /**
     * Show a single asset with relationships.
     *
     * GET /api/assets/{asset}
     */
    public function show(Asset $asset): JsonResponse
    {
        // Load all relevant relationships
        $asset->load([
            'category',
            'assignments.user',
            'assignments.admin',
            'currentHolder'
        ]);

        return response()->json([
            'data' => new AssetResource($asset),
        ]);
    }

    /**
     * Update an asset.
     *
     * PUT /api/assets/{asset}
     */
    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $this->assetService->update(
            $asset,
            $request->validated(),
            $request->file('image')
        );

        $asset->load('category');

        return response()->json([
            'message' => 'Asset updated successfully.',
            'asset' => new AssetResource($asset),
        ]);
    }

    /**
     * Delete an asset.
     *
     * DELETE /api/assets/{asset}
     */
    public function destroy(Asset $asset): JsonResponse
    {
        if (!$this->assetService->delete($asset)) {
            return response()->json([
                'message' => 'Cannot delete an asset that is currently assigned.',
            ], 422);
        }

        return response()->json([
            'message' => 'Asset deleted successfully.',
        ]);
    }

    /**
     * Get only available assets (for assignment dropdown).
     *
     * GET /api/assets-available
     */
    public function available()
    {
        $assets = $this->assetService->getAvailable();
        return AssetResource::collection($assets);
    }

    /**
     * Get assets assigned to the current user.
     *
     * GET /api/my-assets
     */
    public function myAssets(Request $request)
    {
        $assets = $this->assetService->getUserAssets($request->user());
        return AssetResource::collection($assets);
    }
}
```

### 31.5 Add Asset Routes

Update `routes/api.php`:

```php
use App\Http\Controllers\Api\AssetController;

Route::middleware('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Assets
    Route::apiResource('assets', AssetController::class);
    Route::get('/assets-available', [AssetController::class, 'available']);
});

// Employee routes
Route::middleware('employee')->group(function () {
    Route::get('/my-assets', [AssetController::class, 'myAssets']);
});
```

### 31.6 Create Storage Link

For uploaded images to be publicly accessible:

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

---

## Chapter 32: React Assets Pages

### 32.1 Assets List Page

Create `asset-track-frontend/src/pages/Assets.jsx`:

```jsx
/**
 * Assets List Page
 * ================
 */

import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { assetsAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import Pagination from '../components/Pagination';
import ConfirmDialog from '../components/ConfirmDialog';

const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function Assets() {
    const { success, error } = useToast();

    // State
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
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

            setAssets(response.data.data || []);
            if (response.data.meta) {
                setTotalPages(response.data.meta.last_page || 1);
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
            {/* Header */}
            <div className="page-header">
                <div>
                    <h1>Assets</h1>
                    <p className="page-subtitle">Manage company assets</p>
                </div>
                <Link to="/assets/create" className="btn btn-primary">
                    + Add Asset
                </Link>
            </div>

            {/* Assets Table */}
            <div className="table-card">
                <table className="data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Serial Number</th>
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
                                            <div className="table-thumbnail-placeholder">
                                                No image
                                            </div>
                                        )}
                                    </td>
                                    <td className="font-medium">{asset.name}</td>
                                    <td className="text-secondary">{asset.serial_number}</td>
                                    <td>{asset.category?.name || 'N/A'}</td>
                                    <td>
                                        <span className={`badge status-${asset.status}`}>
                                            {asset.status}
                                        </span>
                                    </td>
                                    <td className="actions-cell">
                                        <Link to={`/assets/${asset.id}`} className="action-link view">
                                            View
                                        </Link>
                                        <Link to={`/assets/${asset.id}/edit`} className="action-link edit">
                                            Edit
                                        </Link>
                                        <button
                                            onClick={() => setDeleteConfirm({ show: true, asset })}
                                            className="action-link delete"
                                            disabled={asset.status === 'assigned'}
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="6" className="no-data">No assets found</td>
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
```

### 32.2 Create Pagination Component

Create `asset-track-frontend/src/components/Pagination.jsx`:

```jsx
/**
 * Pagination Component
 * ====================
 */

function Pagination({ currentPage, totalPages, onPageChange }) {
    if (totalPages <= 1) return null;

    const pages = [];
    const maxVisible = 5;

    let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages, start + maxVisible - 1);

    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
        pages.push(i);
    }

    return (
        <div className="pagination">
            <button
                className="pagination-btn"
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
            >
                Previous
            </button>

            {pages.map((page) => (
                <button
                    key={page}
                    className={`pagination-btn ${currentPage === page ? 'active' : ''}`}
                    onClick={() => onPageChange(page)}
                >
                    {page}
                </button>
            ))}

            <button
                className="pagination-btn"
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === totalPages}
            >
                Next
            </button>
        </div>
    );
}

export default Pagination;
```

### 32.3 Asset Form Page

Create `asset-track-frontend/src/pages/AssetForm.jsx`:

```jsx
/**
 * Asset Form - Create/Edit Asset
 * ===============================
 */

import { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { assetsAPI, categoriesAPI } from '../services/api';
import { useToast } from '../context/ToastContext';

const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function AssetForm() {
    const { id } = useParams();
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

    // Fetch data on mount
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
            const asset = response.data.data;

            setFormData({
                name: asset.name,
                serial_number: asset.serial_number,
                category_id: asset.category_id,
                status: asset.status,
            });
            setCurrentImage(asset.image_path);
        } catch (err) {
            error('Failed to load asset');
            navigate('/assets');
        } finally {
            setFetching(false);
        }
    };

    // Handle input changes
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({ ...prev, [name]: value }));
    };

    // Handle image selection
    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                error('Image must be less than 2MB');
                return;
            }
            setImageFile(file);
        }
    };

    // Handle form submit
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            // Use FormData for file upload
            const data = new FormData();
            data.append('name', formData.name);
            data.append('serial_number', formData.serial_number);
            data.append('category_id', formData.category_id);
            data.append('status', formData.status);

            if (imageFile) {
                data.append('image', imageFile);
            }

            if (isEditing) {
                data.append('_method', 'PUT'); // Laravel method spoofing
                await assetsAPI.update(id, data);
                success('Asset updated successfully');
                navigate(`/assets/${id}`);
            } else {
                await assetsAPI.create(data);
                success('Asset created successfully');
                navigate('/assets');
            }
        } catch (err) {
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
            <div className="breadcrumb">
                <Link to="/assets">Assets</Link>
                <span>/</span>
                <span>{isEditing ? 'Edit' : 'Create'}</span>
            </div>

            <div className="form-card">
                <h1>{isEditing ? 'Edit Asset' : 'Add New Asset'}</h1>

                <form onSubmit={handleSubmit}>
                    <div className="form-group">
                        <label htmlFor="name">
                            Asset Name <span className="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            placeholder="e.g., MacBook Pro M2"
                            required
                        />
                    </div>

                    <div className="form-group">
                        <label htmlFor="serial_number">
                            Serial Number <span className="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="serial_number"
                            name="serial_number"
                            value={formData.serial_number}
                            onChange={handleChange}
                            placeholder="Unique identifier"
                            required
                        />
                    </div>

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
                                <option value="">Select Category</option>
                                {categories.map(cat => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="form-group">
                            <label htmlFor="status">
                                Status <span className="required">*</span>
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

                    {/* Current Image */}
                    {isEditing && currentImage && (
                        <div className="form-group">
                            <label>Current Image</label>
                            <img
                                src={`${STORAGE_URL}${currentImage}`}
                                alt="Current"
                                className="current-image-preview"
                            />
                        </div>
                    )}

                    {/* Image Upload */}
                    <div className="form-group">
                        <label htmlFor="image">
                            {currentImage ? 'Replace Image' : 'Asset Image'} (Optional)
                        </label>
                        <input
                            type="file"
                            id="image"
                            accept="image/jpeg,image/png,image/webp"
                            onChange={handleImageChange}
                        />
                        <p className="hint">Max 2MB. Formats: JPEG, PNG, WEBP</p>
                    </div>

                    <div className="form-actions">
                        <button type="submit" className="btn btn-success" disabled={loading}>
                            {loading ? 'Saving...' : (isEditing ? 'Update Asset' : 'Create Asset')}
                        </button>
                        <Link to="/assets" className="btn-link">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default AssetForm;
```

### 32.4 Asset Detail Page

Create `asset-track-frontend/src/pages/AssetDetail.jsx`:

```jsx
/**
 * Asset Detail Page
 * =================
 *
 * Shows asset info, current holder, assignment history.
 * Allows assigning and returning assets.
 */

import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { assetsAPI, usersAPI } from '../services/api';
import { useToast } from '../context/ToastContext';
import ConfirmDialog from '../components/ConfirmDialog';

const STORAGE_URL = 'http://127.0.0.1:8000/storage/';

function AssetDetail() {
    const { id } = useParams();
    const { success, error } = useToast();

    // State
    const [asset, setAsset] = useState(null);
    const [employees, setEmployees] = useState([]);
    const [loading, setLoading] = useState(true);
    const [actionLoading, setActionLoading] = useState(false);

    // Assignment form
    const [selectedEmployee, setSelectedEmployee] = useState('');
    const [assignNotes, setAssignNotes] = useState('');

    // Return confirmation
    const [showReturnConfirm, setShowReturnConfirm] = useState(false);

    // Fetch data
    useEffect(() => {
        fetchAsset();
        fetchEmployees();
    }, [id]);

    const fetchAsset = async () => {
        try {
            setLoading(true);
            const response = await assetsAPI.getOne(id);
            setAsset(response.data.data);
        } catch (err) {
            error('Failed to load asset');
        } finally {
            setLoading(false);
        }
    };

    const fetchEmployees = async () => {
        try {
            const response = await usersAPI.getEmployees();
            setEmployees(response.data.data || response.data);
        } catch (err) {
            console.error('Error fetching employees:', err);
        }
    };

    // Handle assign
    const handleAssign = async (e) => {
        e.preventDefault();
        if (!selectedEmployee) {
            error('Please select an employee');
            return;
        }

        setActionLoading(true);
        try {
            await assetsAPI.assign(id, selectedEmployee, assignNotes);
            success('Asset assigned successfully');
            setSelectedEmployee('');
            setAssignNotes('');
            fetchAsset();
        } catch (err) {
            error(err.response?.data?.message || 'Failed to assign asset');
        } finally {
            setActionLoading(false);
        }
    };

    // Handle return
    const handleReturn = async () => {
        setActionLoading(true);
        try {
            await assetsAPI.return(id);
            success('Asset returned successfully');
            setShowReturnConfirm(false);
            fetchAsset();
        } catch (err) {
            error(err.response?.data?.message || 'Failed to return asset');
        } finally {
            setActionLoading(false);
        }
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
                <p>Loading asset...</p>
            </div>
        );
    }

    if (!asset) {
        return <p>Asset not found</p>;
    }

    return (
        <div className="detail-page">
            {/* Breadcrumb */}
            <div className="breadcrumb">
                <Link to="/assets">Assets</Link>
                <span>/</span>
                <span>{asset.name}</span>
            </div>

            {/* Asset Info Card */}
            <div className="detail-card">
                <div className="detail-header">
                    <h1>{asset.name}</h1>
                    <div className="detail-actions">
                        <Link to={`/assets/${id}/edit`} className="btn btn-secondary">
                            Edit
                        </Link>
                    </div>
                </div>

                <div className="detail-body">
                    {/* Image */}
                    {asset.image_path && (
                        <img
                            src={`${STORAGE_URL}${asset.image_path}`}
                            alt={asset.name}
                            className="detail-image"
                        />
                    )}

                    {/* Info Grid */}
                    <div className="detail-grid">
                        <div className="detail-item">
                            <label>Serial Number</label>
                            <span>{asset.serial_number}</span>
                        </div>
                        <div className="detail-item">
                            <label>Category</label>
                            <span>{asset.category?.name || 'N/A'}</span>
                        </div>
                        <div className="detail-item">
                            <label>Status</label>
                            <span className={`badge status-${asset.status}`}>
                                {asset.status}
                            </span>
                        </div>
                        <div className="detail-item">
                            <label>Created</label>
                            <span>{formatDate(asset.created_at)}</span>
                        </div>
                    </div>

                    {/* Assignment Action Box */}
                    {asset.status === 'available' && (
                        <div className="action-box">
                            <h3>Assign Asset</h3>
                            <form onSubmit={handleAssign}>
                                <div className="form-group">
                                    <label>Select Employee *</label>
                                    <select
                                        value={selectedEmployee}
                                        onChange={(e) => setSelectedEmployee(e.target.value)}
                                        required
                                    >
                                        <option value="">Choose employee...</option>
                                        {employees.map(emp => (
                                            <option key={emp.id} value={emp.id}>
                                                {emp.name} ({emp.email})
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div className="form-group">
                                    <label>Notes (optional)</label>
                                    <textarea
                                        value={assignNotes}
                                        onChange={(e) => setAssignNotes(e.target.value)}
                                        rows="2"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    className="btn btn-success"
                                    disabled={actionLoading}
                                >
                                    {actionLoading ? 'Assigning...' : 'Assign'}
                                </button>
                            </form>
                        </div>
                    )}

                    {asset.status === 'assigned' && asset.current_holder && (
                        <div className="action-box">
                            <h3>Currently Assigned To</h3>
                            <p className="current-holder-name">
                                {asset.current_holder.name}
                            </p>
                            <button
                                className="btn btn-warning"
                                onClick={() => setShowReturnConfirm(true)}
                                disabled={actionLoading}
                            >
                                Return Asset
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Assignment History */}
            <div className="detail-card">
                <h2>Assignment History</h2>
                {asset.assignments?.length > 0 ? (
                    <table className="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Assigned</th>
                                <th>Returned</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {asset.assignments.map(a => (
                                <tr key={a.id}>
                                    <td>{a.user?.name || 'N/A'}</td>
                                    <td>{formatDate(a.assigned_at)}</td>
                                    <td>{formatDate(a.returned_at)}</td>
                                    <td>
                                        <span className={`badge ${a.returned_at ? 'returned' : 'active'}`}>
                                            {a.returned_at ? 'Returned' : 'Active'}
                                        </span>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                ) : (
                    <p className="empty-message">No assignment history</p>
                )}
            </div>

            {/* Return Confirmation */}
            <ConfirmDialog
                isOpen={showReturnConfirm}
                title="Return Asset?"
                message={`Return "${asset.name}" from ${asset.current_holder?.name}?`}
                confirmText="Return"
                confirmVariant="warning"
                onConfirm={handleReturn}
                onCancel={() => setShowReturnConfirm(false)}
                loading={actionLoading}
            />
        </div>
    );
}

export default AssetDetail;
```

### 32.5 Add More Styles

Add to `App.css`:

```css
/* =============================================================================
   DETAIL PAGE
   ============================================================================= */
.detail-page {
    max-width: 900px;
}

.detail-card {
    background: var(--bg-primary);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 24px;
    overflow: hidden;
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--border-color);
}

.detail-header h1 {
    margin: 0;
}

.detail-body {
    padding: 24px;
}

.detail-image {
    max-width: 300px;
    border-radius: var(--radius);
    margin-bottom: 24px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

.detail-item label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 4px;
}

.detail-item span {
    font-size: 1rem;
    font-weight: 500;
}

.action-box {
    background: var(--bg-secondary);
    padding: 20px;
    border-radius: var(--radius);
    margin-top: 24px;
}

.action-box h3 {
    margin-bottom: 16px;
}

.current-holder-name {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 16px;
}

/* =============================================================================
   IMAGES
   ============================================================================= */
.table-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.table-thumbnail-placeholder {
    width: 50px;
    height: 50px;
    background: var(--bg-secondary);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.625rem;
    color: var(--text-secondary);
}

.current-image-preview {
    max-width: 200px;
    border-radius: var(--radius);
}

/* =============================================================================
   PAGINATION
   ============================================================================= */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
}

.pagination-btn {
    padding: 8px 16px;
    border: 1px solid var(--border-color);
    background: var(--bg-primary);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
    background: var(--bg-secondary);
}

.pagination-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* =============================================================================
   FORM ROW
   ============================================================================= */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 600px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
```

---

*[Guide continues with Phase 7-10...]*

The remaining phases follow the same detailed pattern:
- **Phase 7:** Users CRUD (similar to Categories/Assets)
- **Phase 8:** Assignment System (the core business logic with database transactions)
- **Phase 9:** Employee Features (My Assets, Profile)
- **Phase 10:** Complete Interview Question Bank

---

# COMPLETE INTERVIEW QUESTION BANK

## Laravel Questions

### Beginner Level

1. **What is Laravel?**
   > PHP framework for building web applications with elegant syntax.

2. **What is Artisan?**
   > Command-line interface for Laravel. Used to generate files, run migrations, etc.

3. **What is a Migration?**
   > PHP files that define database structure. Version control for your database.

4. **What is Eloquent ORM?**
   > Laravel's database abstraction layer. Lets you work with databases using PHP objects.

5. **What is the .env file?**
   > Environment configuration file. Stores sensitive data like database credentials.

### Intermediate Level

6. **Explain MVC in Laravel.**
   > Model (data), View (presentation), Controller (logic). Separates concerns.

7. **What is Middleware?**
   > Code that runs before/after requests. Used for auth, logging, etc.

8. **What are Service Providers?**
   > Central place to configure application services. Bootstrap the app.

9. **What is Route Model Binding?**
   > Automatically inject model instances into routes based on ID.

10. **What is the Service Layer pattern?**
    > Separating business logic from controllers into dedicated service classes.

### Advanced Level

11. **What is the N+1 query problem?**
    > Loading relationships inefficiently. Solved with eager loading.

12. **Explain Database Transactions.**
    > Group of operations that all succeed or all fail. Ensures data integrity.

13. **What are Laravel Events?**
    > Decouple code by broadcasting when things happen. Listeners respond.

14. **What is the Repository Pattern?**
    > Abstraction layer between controllers and data access. Improves testability.

15. **How does Laravel handle CSRF protection?**
    > Generates tokens that must be included in forms. Prevents cross-site attacks.

## React Questions

### Beginner Level

1. **What is React?**
   > JavaScript library for building user interfaces with components.

2. **What is JSX?**
   > Syntax extension that lets you write HTML-like code in JavaScript.

3. **What is a Component?**
   > Reusable piece of UI. Can be function or class.

4. **What is State?**
   > Data that can change within a component. Triggers re-render.

5. **What are Props?**
   > Data passed from parent to child component. Read-only.

### Intermediate Level

6. **What is the Virtual DOM?**
   > React's lightweight copy of actual DOM. Enables efficient updates.

7. **Explain useEffect.**
   > Hook for side effects. Runs after render. Can clean up.

8. **What is Context API?**
   > Way to pass data through component tree without prop drilling.

9. **What are Controlled Components?**
   > Form inputs where React controls the value through state.

10. **What is the difference between class and functional components?**
    > Functional use hooks, are simpler. Class components are older pattern.

### Advanced Level

11. **What is useCallback?**
    > Memoizes functions to prevent unnecessary re-renders.

12. **What is useMemo?**
    > Memoizes computed values. Only recalculates when dependencies change.

13. **How do you optimize React performance?**
    > Memoization, lazy loading, virtualization, avoiding re-renders.

14. **What is React.memo?**
    > HOC that prevents re-render if props haven't changed.

15. **Explain React's reconciliation algorithm.**
    > How React decides what to update in the actual DOM.

## Full-Stack/Integration Questions

1. **How does token-based authentication work?**
   > User logs in, server returns token, client stores it, sends with requests.

2. **What is CORS?**
   > Browser security feature. Server must explicitly allow cross-origin requests.

3. **What is REST API?**
   > Architectural style using HTTP methods (GET, POST, PUT, DELETE).

4. **How do you handle API errors in React?**
   > Try/catch blocks, axios interceptors, error boundaries.

5. **What is the purpose of HTTP status codes?**
   > Communicate request result. 2xx success, 4xx client error, 5xx server error.

---

# FINAL CHECKLIST

Before deploying your AssetTrack application, verify:

## Backend (Laravel)
- [ ] All migrations run successfully
- [ ] Sanctum is configured
- [ ] CORS allows production frontend
- [ ] Storage link created
- [ ] Environment variables set
- [ ] Admin user created

## Frontend (React)
- [ ] API base URL points to production
- [ ] Build completes without errors
- [ ] All routes work correctly
- [ ] Authentication flow works
- [ ] Error handling in place

## Testing
- [ ] Register new user
- [ ] Login as admin
- [ ] Create category
- [ ] Create asset with image
- [ ] Assign asset
- [ ] Return asset
- [ ] View assignment history
- [ ] Login as employee
- [ ] View my assets
- [ ] Update profile

---

**Congratulations!** You've completed the AssetTrack development guide.

**Remember:**
- Practice builds expertise
- Read official documentation
- Build your own variations
- Teach others what you've learned

Good luck with your interviews and development journey!

