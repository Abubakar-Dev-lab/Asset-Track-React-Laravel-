# Learning Guide: Connecting React to Laravel API

This guide explains how the React frontend connects to the Laravel backend through APIs.
Read this document to understand the complete flow of data between the two projects.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [How API Communication Works](#2-how-api-communication-works)
3. [Authentication Flow](#3-authentication-flow)
4. [Making API Calls](#4-making-api-calls)
5. [Understanding Each File](#5-understanding-each-file)
6. [Common Patterns](#6-common-patterns)
7. [Debugging Tips](#7-debugging-tips)

---

## 1. Project Overview

We have TWO separate projects:

```
assetTrack/
├── assetlara/                 # Laravel Backend (Port 8000)
│   └── routes/api.php         # API endpoints live here
│
└── asset-track-frontend/      # React Frontend (Port 5173)
    └── src/services/api.js    # API connection setup
```

**Laravel Backend** (Port 8000):
- Handles database operations
- Provides REST API endpoints
- Manages authentication with Sanctum tokens

**React Frontend** (Port 5173):
- User interface (what users see)
- Calls Laravel API to get/send data
- Stores auth token in browser localStorage

---

## 2. How API Communication Works

### The Basic Flow

```
┌─────────────────┐         HTTP Request          ┌─────────────────┐
│                 │  ─────────────────────────>   │                 │
│  React Frontend │                               │  Laravel Backend │
│  (Browser)      │  <─────────────────────────   │  (Server)        │
│                 │         JSON Response          │                 │
└─────────────────┘                               └─────────────────┘
```

### Example: Getting All Assets

1. **React** makes a request:
   ```javascript
   axios.get('http://127.0.0.1:8000/api/assets')
   ```

2. **Laravel** receives the request at `/api/assets`

3. **Laravel** queries the database and returns JSON:
   ```json
   {
     "data": [
       { "id": 1, "name": "MacBook Pro", "status": "available" },
       { "id": 2, "name": "Dell Monitor", "status": "assigned" }
     ]
   }
   ```

4. **React** receives the JSON and displays it

---

## 3. Authentication Flow

### Login Process (Step by Step)

```
Step 1: User enters email/password in React login form
        ↓
Step 2: React sends POST to /api/auth/login
        ↓
Step 3: Laravel verifies credentials in database
        ↓
Step 4: Laravel creates a token and returns it
        {
          "token": "1|abc123xyz...",
          "user": { "name": "Admin", "role": "admin" }
        }
        ↓
Step 5: React saves token to localStorage
        localStorage.setItem('token', '1|abc123xyz...')
        ↓
Step 6: Every future request includes this token
        Authorization: Bearer 1|abc123xyz...
```

### Why Tokens?

HTTP is "stateless" - each request is independent. Laravel doesn't remember who you are.
The token is like a VIP pass - show it with every request to prove your identity.

### Token Storage

```javascript
// After login - save token
localStorage.setItem('token', response.data.token);

// Before every request - attach token
config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;

// On logout - remove token
localStorage.removeItem('token');
```

---

## 4. Making API Calls

### Basic Pattern (using our api.js)

```javascript
import api from '../services/api';

// GET request - fetch data
const response = await api.get('/assets');
const assets = response.data.data;

// POST request - create data
await api.post('/assets', {
  name: 'New Laptop',
  serial_number: 'ABC123',
  category_id: 1
});

// PUT request - update data
await api.put('/assets/5', {
  name: 'Updated Name'
});

// DELETE request - remove data
await api.delete('/assets/5');
```

### With useEffect (fetching on page load)

```javascript
function MyComponent() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await api.get('/assets');
        setData(response.data.data);
      } catch (error) {
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);  // Empty array = run once on mount

  if (loading) return <p>Loading...</p>;
  return <div>{/* render data */}</div>;
}
```

---

## 5. Understanding Each File

### `/src/services/api.js` - API Connection Layer

**Purpose**: Creates a configured axios instance that all components use.

**Key Parts**:
```javascript
// 1. Base URL - where Laravel is running
baseURL: 'http://127.0.0.1:8000/api'

// 2. Request Interceptor - adds token to every request
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// 3. Response Interceptor - handles 401 errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response.status === 401) {
      // Token expired, redirect to login
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### `/src/context/AuthContext.jsx` - Authentication State

**Purpose**: Provides login/logout functions and user data to all components.

**How it works**:
```javascript
// Create context
const AuthContext = createContext();

// Provider wraps the app
<AuthProvider>
  <App />
</AuthProvider>

// Any component can access auth:
const { user, login, logout, isAuthenticated } = useAuth();
```

### `/src/components/PrivateRoute.jsx` - Route Protection

**Purpose**: Prevents unauthenticated users from accessing protected pages.

**How it works**:
```javascript
function PrivateRoute({ children }) {
  const { isAuthenticated, loading } = useAuth();

  if (loading) return <Loading />;
  if (!isAuthenticated) return <Navigate to="/login" />;
  return children;  // Show the protected page
}
```

### `/src/pages/Assets.jsx` - Complete CRUD Example

**Purpose**: Demonstrates all API operations (Create, Read, Update, Delete).

**Key Operations**:
```javascript
// READ - Get all assets
const response = await assetsAPI.getAll();

// CREATE - Add new asset
await assetsAPI.create({ name, serial_number, category_id });

// UPDATE - Edit existing asset
await assetsAPI.update(assetId, { name, status });

// DELETE - Remove asset
await assetsAPI.delete(assetId);
```

---

## 6. Common Patterns

### Pattern 1: List Page with Fetch

```javascript
function ListPage() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchItems();
  }, []);

  const fetchItems = async () => {
    setLoading(true);
    const response = await api.get('/items');
    setItems(response.data.data);
    setLoading(false);
  };

  if (loading) return <p>Loading...</p>;

  return (
    <ul>
      {items.map(item => <li key={item.id}>{item.name}</li>)}
    </ul>
  );
}
```

### Pattern 2: Form Submit

```javascript
const handleSubmit = async (e) => {
  e.preventDefault();
  setSubmitting(true);

  try {
    await api.post('/items', formData);
    // Success - refresh list or redirect
    fetchItems();
  } catch (error) {
    // Show error to user
    setError(error.response?.data?.message || 'Failed');
  } finally {
    setSubmitting(false);
  }
};
```

### Pattern 3: Delete with Confirmation

```javascript
const handleDelete = async (id) => {
  if (!window.confirm('Are you sure?')) return;

  try {
    await api.delete(`/items/${id}`);
    fetchItems();  // Refresh list
  } catch (error) {
    alert('Failed to delete');
  }
};
```

---

## 7. Debugging Tips

### Check Network Tab

1. Open browser DevTools (F12)
2. Go to "Network" tab
3. Make an action (login, fetch data, etc.)
4. Click on the request to see:
   - Request URL
   - Request Headers (check Authorization)
   - Response data

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| 401 Unauthorized | Token missing/expired | Login again |
| 403 Forbidden | User doesn't have permission | Check user role |
| 404 Not Found | Wrong URL | Check API endpoint |
| 422 Validation Error | Invalid form data | Check required fields |
| CORS Error | Cross-origin blocked | Check Laravel CORS config |

### Console Logging

Add logs to trace the flow:

```javascript
const fetchAssets = async () => {
  console.log('Fetching assets...');
  try {
    const response = await api.get('/assets');
    console.log('Response:', response.data);
    setAssets(response.data.data);
  } catch (error) {
    console.error('Error fetching:', error);
    console.log('Error response:', error.response?.data);
  }
};
```

---

## Quick Reference: API Endpoints

| Action | Method | Endpoint | Body |
|--------|--------|----------|------|
| Login | POST | /api/auth/login | { email, password } |
| Logout | POST | /api/auth/logout | - |
| Get Dashboard | GET | /api/dashboard | - |
| List Assets | GET | /api/assets | - |
| Create Asset | POST | /api/assets | { name, serial_number, category_id } |
| Update Asset | PUT | /api/assets/{id} | { name, status } |
| Delete Asset | DELETE | /api/assets/{id} | - |
| Assign Asset | POST | /api/assets/{id}/assign | { user_id, notes } |
| Return Asset | POST | /api/assets/{id}/return | { notes } |
| List Categories | GET | /api/categories | - |
| List Users | GET | /api/users | - |
| List Assignments | GET | /api/assignments | - |

---

## Next Steps for Learning

1. **Read the code** - Start with `api.js`, then `AuthContext.jsx`, then `Login.jsx`
2. **Follow a request** - Use browser DevTools to see a full request/response cycle
3. **Try modifications** - Add a new field to an API call and see what happens
4. **Build something new** - Try adding a new page that fetches different data

Good luck with your learning!
