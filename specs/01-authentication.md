# Spec 01 — Authentication

**Feature:** User Registration, Login, Logout  
**User Story:** US1  
**Branch:** `feature/auth`  
**Agent:** OpenCode  
**Author:** BEN-ESSAHRAOUI Yassine  

---

## 1. Objective

Implement a complete session-based authentication system from scratch using Laravel 13's built-in Auth helpers. No scaffolding packages. Users must be able to register, log in, and log out. All non-auth routes must be protected.

---

## 2. Context & Constraints

- Laravel 13, Blade only, no JavaScript frameworks
- Use Breeze, no Jetstream, no Fortify, no any auth scaffold
- Manual controllers, manual routes, manual views
- Authentication uses Laravel's `Auth` facade and `auth` middleware
- Passwords hashed with `bcrypt` via `Hash::make()`
- Session-based — no tokens, no API auth

---

## 3. Database

Table involved: `users` (already provided by Laravel default migration)

| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned | PK |
| name | varchar(255) | |
| email | varchar(255) | unique |
| password | varchar(255) | bcrypt hashed |
| remember_token | varchar(100) | nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

No changes to the default Laravel users migration are needed.

---

## 4. Files to Create

| File | Purpose |
|---|---|
| `app/Http/Controllers/Auth/RegisterController.php` | Handles show register form + store new user |
| `app/Http/Controllers/Auth/LoginController.php` | Handles show login form + authenticate + logout |
| `app/Http/Requests/Auth/RegisterRequest.php` | Validation for registration |
| `app/Http/Requests/Auth/LoginRequest.php` | Validation for login |
| `resources/views/auth/register.blade.php` | Registration form |
| `resources/views/auth/login.blade.php` | Login form |
| `resources/views/layouts/app.blade.php` | Base layout with nav and logout button |

---

## 5. Files to Modify

| File | Change |
|---|---|
| `routes/web.php` | Add auth routes — GET/POST register, GET/POST login, POST logout |

---

## 6. Routes

```
GET  /register         → Auth\RegisterController@showForm
POST /register         → Auth\RegisterController@store
GET  /login            → Auth\LoginController@showForm
POST /login            → Auth\LoginController@authenticate
POST /logout           → Auth\LoginController@logout
```

After login → redirect to `/domains`  
After register → redirect to `/domains`  
After logout → redirect to `/login`  
Guest accessing protected route → redirect to `/login`

---

## 7. Controller Logic

### RegisterController
- `showForm()` — return `auth.register` view (redirect to `/domains` if already logged in)
- `store(RegisterRequest $request)` — create user, hash password, call `Auth::login()`, redirect to `/domains`

### LoginController
- `showForm()` — return `auth.login` view (redirect to `/domains` if already logged in)
- `authenticate(LoginRequest $request)` — attempt `Auth::attempt()`, on success redirect to `/domains`, on failure redirect back with error
- `logout()` — call `Auth::logout()`, invalidate session, regenerate token, redirect to `/login`

---

## 8. Form Request Validation

### RegisterRequest
```
name     → required | string | max:255
email    → required | email | max:255 | unique:users,email
password → required | string | min:8 | confirmed
```

### LoginRequest
```
email    → required | email
password → required | string
```

---

## 9. Layout File Requirements

`layouts/app.blade.php` must include:
- `@yield('content')` for page content
- A navigation bar showing the authenticated user's name
- A logout button (POST form with CSRF)
- Links to `/domains` in the nav
- `@yield('title')` in the `<title>` tag

---

## 10. What the Agent Must NOT Do

- ❌ Do not install Jetstream, Fortify, or any auth package
- ❌ Do not use `$request->validate()` directly in controllers — use FormRequest classes
- ❌ Do not generate token-based or API auth
- ❌ Do not create a `User` model — it already exists in Laravel's default scaffold
- ❌ Do not add any JavaScript to the forms
- ❌ Do not redirect to `/home` — always redirect to `/domains`
- ❌ Do not use `redirect()->intended()` without a proper fallback to `/domains`
- ❌ Do not forget CSRF `@csrf` on every form

---

## 11. Acceptance Criteria

- [ ] A guest can access `/register` and `/login` freely
- [ ] A guest accessing any other route is redirected to `/login`
- [ ] Registering with valid data creates a user and logs them in
- [ ] Registering with a duplicate email shows a validation error
- [ ] Logging in with wrong credentials shows an error message
- [ ] Logging out destroys the session and redirects to `/login`
- [ ] A logged-in user visiting `/login` or `/register` is redirected to `/domains`
