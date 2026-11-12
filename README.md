# RentSpace — Professional Property Rental & Listing Platform

> A production-quality full-stack property rental platform built with **Laravel 9** (REST API) and **Vue 3** (SPA), targeting the Ethiopian rental market.

---

## 🏠 Overview

RentSpace connects property owners and tenants in a modern, secure, and user-friendly platform. Owners publish listings, tenants discover and inquire about properties, and admins moderate the platform.

---

## ✨ Features

| Module | Details |
|---|---|
| **Authentication** | Register / Login / Logout via Laravel Sanctum (SPA cookie-based) |
| **Roles** | Admin · Owner · Tenant with full RBAC |
| **Property Management** | Create, edit, delete listings with multi-image upload |
| **Property Search** | Full-text search + filters (city, type, price, bedrooms, furnishing) |
| **Favorites** | Save and manage favourite properties |
| **Inquiry System** | Tenants send inquiries; owners accept/reject |
| **Dashboards** | Dedicated dashboards for each role with statistics |
| **Admin Panel** | Approve/reject listings, manage users, review reports |
| **Reports** | Tenants can report inappropriate listings |

---

## 🛠 Technology Stack

**Backend**
- PHP 8.0 · Laravel 9
- Laravel Sanctum (SPA authentication)
- Eloquent ORM · API Resources · Form Request validation · Policies
- MySQL (MariaDB 10.4)

**Frontend** *(Phase 5–10)*
- Vue 3 · Composition API · JavaScript
- Pinia · Vue Router · Axios
- Tailwind CSS · Lucide Icons

---

## 🗂 Project Structure

```
├── backend/        # Laravel 9 REST API
└── frontend/       # Vue 3 SPA (coming in Phase 5)
```

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.0+ (XAMPP recommended)
- Composer
- Node.js 18+
- MySQL / MariaDB

### Backend Setup

```bash
cd backend

# Install PHP dependencies
php composer.phar install
# or: composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your .env — set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Create the database
mysql -u root -e "CREATE DATABASE rentspace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Start development server
php artisan serve
# API available at http://localhost:8000
```

### Frontend Setup *(Phase 5)*

```bash
cd frontend
npm install
npm run dev
# App available at http://localhost:5173
```

---

## 🔑 API Endpoints (Phase 2)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/register` | Public | Register (owner or tenant) |
| POST | `/api/auth/login` | Public | Login |
| POST | `/api/auth/logout` | Required | Logout |
| GET | `/api/user` | Required | Get current user |
| GET | `/api/profile` | Required | Get profile |
| PUT | `/api/profile` | Required | Update profile |

More endpoints added in Phases 3–8.

---

## 🌍 Supported Cities

Addis Ababa · Dessie · Kombolcha · Bahir Dar · Hawassa · Adama · Dire Dawa · Mekelle · Gondar · Jimma

Currency: **ETB (Ethiopian Birr)**

---

## 🔒 Security

- Passwords hashed with bcrypt
- Sanctum SPA cookie authentication (no tokens in localStorage)
- Role-based access control via middleware and Policies
- Server-side validation on all inputs
- CORS restricted to frontend origin
- `.env` excluded from version control

---

## 📸 Screenshots

*Coming after Phase 6 frontend implementation.*

---

## 🧪 Testing

```bash
cd backend
php artisan test
```

---

## 📄 License

MIT
