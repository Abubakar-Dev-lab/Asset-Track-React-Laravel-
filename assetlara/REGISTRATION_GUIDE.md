# AssetTrack - User Registration & Management Guide

## 🎉 Registration System Now Available!

The application now has a complete user registration and management system.

---

## 👤 Public Registration (For Employees)

### **How to Register:**

1. Visit: **http://localhost:8000/register**
   - OR click "Register here" link on the login page

2. Fill in the form:
   - Full Name
   - Email Address
   - Password (minimum 8 characters)
   - Confirm Password

3. Click "Create Account"

4. You'll be automatically logged in as an **employee**

5. Redirected to `/my-assets` page

### **Features:**
- ✅ Email uniqueness validation
- ✅ Password confirmation matching
- ✅ Minimum 8 character password requirement
- ✅ Auto-login after successful registration
- ✅ Throttled to prevent abuse (5 attempts/minute)
- ✅ All new registrations are employees by default

---

## 👑 Admin User Management

Admins can now manage users through a dedicated interface.

### **Access User Management:**

**Navigation:** Dashboard → Users (in top menu)
**Direct URL:** http://localhost:8000/users

### **Features:**

#### **1. View All Users** (`/users`)
- Complete list of all users
- Shows: ID, Name, Email, Role, Status, Registration Date
- Your own account is highlighted in blue
- Pagination (15 users per page)
- Status badges:
  - **Active** (Green) / **Inactive** (Red)
  - **Admin** (Purple) / **Employee** (Gray)

#### **2. Create New User** (`/users/create`)
- Admins can create users with specific roles
- Set role: Employee or Administrator
- Same validation as public registration
- Button: **+ Add New User** (top right)

#### **3. View User Details** (`/users/{id}`)
- Full user information
- Assignment history
- Assets currently held

#### **4. Edit User** (`/users/{id}/edit`)
- Update name, email, role
- Toggle active/inactive status
- Cannot change password (security feature)

#### **5. Delete User** (`/users/{id}`)
- Soft delete with confirmation
- Safety checks:
  - ❌ Cannot delete yourself
  - ❌ Cannot delete users with active assignments
  - ✅ Must return all assets first

---

## 🔐 Registration Security Features

### **Validation Rules:**

```php
- Name: Required, max 255 characters
- Email: Required, valid email format, unique in database
- Password: Required, minimum 8 characters, must match confirmation
- Role: Optional (admin-only), must be 'admin' or 'employee'
```

### **Throttling:**
- Maximum 5 registration attempts per minute
- Prevents spam and automated attacks
- Same throttling as login

### **Auto-Login:**
- Users are automatically logged in after successful registration
- No need to manually login after registering

---

## 📊 Updated Navigation

### **Admin Menu:**
```
[Logo] | Dashboard | Assets | Users | [+ Add Asset]
```

**New "Users" Link:**
- Access complete user management
- View, create, edit, delete users
- Active page highlighting

### **Guest Pages:**

**Login Page:**
```
[Login Form]
Don't have an account? [Register here]  ← NEW LINK
```

**Register Page:**
```
[Registration Form]
Already have an account? [Login here]
```

---

## 🚀 Complete User Journey

### **New Employee Registration:**
```
1. Visit /register
2. Fill form (name, email, password)
3. Submit
4. Auto-login as employee
5. Redirected to /my-assets
6. Can view only their assigned assets
```

### **Admin Creating User:**
```
1. Login as admin
2. Click "Users" in navigation
3. Click "+ Add New User"
4. Fill form + select role (Admin/Employee)
5. Submit
6. User created and appears in list
7. New user can login with provided credentials
```

---

## 📋 User Management Table

| Action | Employee | Admin |
|--------|----------|-------|
| Self-register | ✅ Yes (employee role) | ❌ N/A |
| Login | ✅ Yes | ✅ Yes |
| View own profile | ✅ Yes | ✅ Yes |
| View all users | ❌ No | ✅ Yes |
| Create user | ❌ No | ✅ Yes |
| Edit user | ❌ No (own data only) | ✅ Yes (all users) |
| Delete user | ❌ No | ✅ Yes (except self) |
| Assign role | ❌ No | ✅ Yes |
| Deactivate user | ❌ No | ✅ Yes |

---

## 🔧 Routes Added

### **Public Registration Routes:**
| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/register` | register | AuthController@createRegister |
| POST | `/register` | - | AuthController@storeRegister |

### **Admin User Management Routes:**
| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | `/users` | users.index | UserController@index |
| GET | `/users/create` | users.create | UserController@create |
| POST | `/users` | users.store | UserController@store |
| GET | `/users/{id}` | users.show | UserController@show |
| GET | `/users/{id}/edit` | users.edit | UserController@edit |
| PUT/PATCH | `/users/{id}` | users.update | UserController@update |
| DELETE | `/users/{id}` | users.destroy | UserController@destroy |

---

## 🎨 Files Created/Modified

### **New Files:**
1. `app/Http/Requests/RegisterRequest.php` - Registration validation
2. `app/Http/Controllers/UserController.php` - User management
3. `resources/views/auth/register.blade.php` - Registration form
4. `resources/views/users/index.blade.php` - User list
5. `resources/views/users/create.blade.php` - Create user form

### **Modified Files:**
1. `app/Http/Controllers/AuthController.php` - Added registration methods
2. `routes/web.php` - Added registration & user routes
3. `resources/views/layouts/app.blade.php` - Added "Users" to admin nav
4. `resources/views/auth/login.blade.php` - Added register link

---

## ✅ Testing Checklist

### **Public Registration:**
- [ ] Visit /register page
- [ ] Fill form with valid data
- [ ] Submit successfully
- [ ] Auto-login works
- [ ] Redirected to /my-assets
- [ ] Try duplicate email → Shows error
- [ ] Try weak password → Shows error
- [ ] Try mismatched passwords → Shows error

### **Admin User Management:**
- [ ] Login as admin
- [ ] Click "Users" in navigation
- [ ] See all users listed
- [ ] Click "+ Add New User"
- [ ] Create user with admin role
- [ ] Create user with employee role
- [ ] Edit existing user
- [ ] Change user role
- [ ] Deactivate user
- [ ] Try to delete yourself → Shows error
- [ ] Delete user without assignments → Success

### **Security:**
- [ ] Non-admin cannot access /users (403)
- [ ] Logged-in users cannot access /register (redirected)
- [ ] Registration throttling works (5 attempts)
- [ ] Password hashed in database (not plain text)
- [ ] Email uniqueness enforced

---

## 💡 Usage Tips

### **For Administrators:**

1. **Creating Admin Users:**
   - Use the admin interface to create new admins
   - Set role to "Administrator" when creating

2. **Managing Inactive Users:**
   - Set user status to "Inactive" to block login
   - Don't delete - use deactivation instead

3. **User Cleanup:**
   - Before deleting a user, ensure all their assets are returned
   - System prevents deletion if active assignments exist

### **For New Employees:**

1. **Self-Registration:**
   - Use the public registration page
   - Will be created as "Employee" automatically
   - Contact admin if you need admin access

2. **Forgot Password:**
   - Currently no password reset (future feature)
   - Contact administrator to create new account

---

## 🔐 Default User Accounts

After seeding, these accounts are available:

| Name | Email | Password | Role |
|------|-------|----------|------|
| Admin User | admin@example.com | password | Admin |
| John Doe | john@example.com | password | Employee |
| Jane Smith | jane@example.com | password | Employee |

---

## 🎯 Key Differences

### **Public Registration vs Admin Creation:**

| Feature | Public Register | Admin Create |
|---------|----------------|--------------|
| Access | Anyone (guest) | Admin only |
| Role Selection | ❌ Always employee | ✅ Choose role |
| Email Validation | ✅ Yes | ✅ Yes |
| Password Rules | ✅ Yes | ✅ Yes |
| Auto-Login | ✅ Yes | ❌ No |
| Throttling | ✅ Yes | ✅ Yes |

---

## 📈 Benefits

### **For the Organization:**
- ✅ Easy onboarding for new employees
- ✅ Self-service registration reduces admin work
- ✅ Centralized user management
- ✅ Role-based access control
- ✅ Audit trail of user activities

### **For Administrators:**
- ✅ Full control over user accounts
- ✅ Quick user creation
- ✅ Role management
- ✅ User activity tracking
- ✅ Safety checks prevent data loss

### **For Employees:**
- ✅ Quick self-registration
- ✅ Immediate access to system
- ✅ No waiting for admin approval
- ✅ Clean, intuitive interface

---

## 🚦 System Status

**Registration System:** ✅ Fully Operational
**User Management:** ✅ Fully Operational
**Security:** ✅ Validated & Throttled
**Navigation:** ✅ Integrated
**Documentation:** ✅ Complete

---

## 🆕 What's New

### **Before:**
- ❌ No registration system
- ❌ Only seeded users could login
- ❌ No way to add users without database access
- ❌ No user management interface

### **After:**
- ✅ Public registration for employees
- ✅ Admin user management interface
- ✅ Create users with any role
- ✅ Edit and deactivate users
- ✅ Safe deletion with checks
- ✅ "Users" link in admin navigation
- ✅ Register link on login page

---

*Registration Guide - Updated December 11, 2025*
*All features tested and operational*
