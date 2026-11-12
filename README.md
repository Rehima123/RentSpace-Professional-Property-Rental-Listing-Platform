# RentSpace — Professional Property Rental & Listing Platform

> A production-quality full-stack property rental platform built with **Laravel 9 REST API** + **Vue 3 SPA**, targeting the Ethiopian rental market. Built as a serious portfolio project demonstrating clean architecture, secure authentication, role-based access control, and modern UI/UX.

---

## 📸 Screenshots

| Home Page | Property Listings | Property Details |
|-----------|-----------------|-----------------|
| Hero section with search | Filters + pagination | Gallery, specs, inquiry form |

| Owner Dashboard | Tenant Dashboard | Admin Dashboard |
|----------------|-----------------|----------------|
| Stats + listings table | Favorites + inquiries | Pending approvals + user management |

---

## ✨ Features

### 🔐 Authentication & Authorization
- Secure registration and login via **Laravel Sanctum SPA** (cookie-based, no tokens in localStorage)
- Three roles: **Admin**, **Property Owner**, **Tenant**
- Role-based route guards on both frontend (Vue Router) and backend (Policies + Middleware)
- Passwords hashed with bcrypt

### 🏠 Property Management
- Owners create, edit, delete listings with up to **10 image uploads**
- Property types: Apartment, House, Studio, Condominium, Commercial, Office
- Listings start as **pending** and require admin approval before going live
- Content changes on approved listings automatically reset to pending
- 25 selectable amenities per property (WiFi, Generator, Parking, etc.)

### 🔍 Property Search & Discovery
- Full-text search by title, city, description, address
- 7 filter params: city, type, price range, bedrooms, furnishing status
- Sort: Newest, Price Low→High, Price High→Low
- Shareable filter URLs (synced to query string)
- Paginated results (default 12/page, max 50)

### ❤️ Favorites System
- Toggle favorite with one click (no duplicate favorites)
- Favorites list with remove functionality
- Login prompt for unauthenticated users

### 📩 Inquiry System
- Tenants send inquiries with message + preferred viewing date
- Duplicate prevention: one active inquiry per tenant per property
- After rejection, tenant can re-inquire
- Owners accept or reject inquiries
- Tenant contact info (phone, email) only shown to the property owner

### 📊 Dashboards
- **Owner**: stats, recent inquiries, manage listings, add/edit properties
- **Tenant**: favorites count, inquiry stats, quick actions
- **Admin**: platform-wide stats, approve/reject pending listings, manage users (suspend/reactivate)

---

## 🛠 Technology Stack

### Backend
| Technology | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.0.30 | Runtime |
| Laravel | 9.x | Framework |
| Laravel Sanctum | 3.x | SPA authentication |
| MySQL / MariaDB | 10.4 | Database |
| Eloquent ORM | — | Database interactions |
| PHPUnit | 9.x | Testing (60 tests) |

### Frontend
| Technology | Version | Purpose |
|-----------|---------|---------|
| Vue.js | 3.x | SPA framework |
| Vite | 8.x | Build tool |
| Pinia | 2.x | State management |
| Vue Router | 4.x | Client-side routing |
| Axios | 1.x | HTTP client |
| Tailwind CSS | 3.x | Utility-first styling |
| Lucide Vue | 0.469 | Icon library |

---

## 📁 Project Structure

```
RENTSPACE/
├── backend/          # Laravel 9 REST API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/    # Thin controllers
│   │   │   ├── Requests/           # Form Request validation
│   │   │   ├── Resources/          # API Resources (JSON transformation)
│   │   │   └── Middleware/         # RoleMiddleware
│   │   ├── Models/                 # Eloquent models
│   │   ├── Policies/               # Authorization policies
│   │   └── Traits/                 # HandlesImageUpload
│   ├── database/
│   │   ├── migrations/             # 11 migrations
│   │   ├── factories/              # UserFactory, PropertyFactory, AmenityFactory
│   │   └── seeders/                # UserSeeder, AmenitySeeder, PropertySeeder
│   ├── routes/api.php              # 30 API endpoints
│   └── tests/Feature/              # 60 feature tests
│
└── frontend/         # Vue 3 SPA
    └── src/
        ├── assets/styles/          # Tailwind + custom CSS design system
        ├── components/
        │   ├── layout/             # AppNavbar, AppFooter, PublicLayout, DashboardLayout
        │   ├── ui/                 # 10 base components (Button, Input, Modal, Toast...)
        │   ├── property/           # PropertyCard
        │   └── dashboard/          # StatCard
        ├── router/                 # Vue Router with role guards
        ├── services/               # Axios API layer (auth, property, inquiry, favorite)
        ├── stores/                 # Pinia (auth, property, ui)
        ├── utils/                  # constants.js, format.js
        └── views/
            ├── public/             # Home, Properties, PropertyDetails, Login, Register
            ├── tenant/             # Dashboard, Favorites, Inquiries, Profile
            ├── owner/              # Dashboard, AddProperty, EditProperty, ManageProperties, ManageInquiries
            └── admin/              # Dashboard, ManageUsers, ManageListings
```

---

## 🌍 Cities & Currency

Supported cities: **Addis Ababa, Bahir Dar, Hawassa, Adama, Dessie, Kombolcha, Dire Dawa, Mekelle, Gondar, Jimma, Shashamane, Arba Minch, Hosaena**

Default currency: **ETB (Ethiopian Birr)**

---

## 🚀 Getting Started

### Prerequisites

- **PHP 8.0+** (tested on XAMPP with PHP 8.0.30)
- **Composer** (or `composer.phar`)
- **Node.js 18+** and npm
- **MySQL / MariaDB** (XAMPP recommended)
- **Git**

---

### Backend Setup

```bash
cd backend

# 1. Install PHP dependencies
composer install
# or if using composer.phar:
php ..\composer.phar install

# 2. Copy environment file
cp .env.example .env

# 3. Configure your .env
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Generate application key
php artisan key:generate

# 5. Create the database
# In MySQL/phpMyAdmin:
CREATE DATABASE rentspace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 6. Run migrations
php artisan migrate

# 7. Seed sample data
php artisan db:seed

# 8. Create storage symlink
php artisan storage:link

# 9. Start the development server
php artisan serve
# API available at: http://localhost:8000
```

---

### Frontend Setup

> ⚠️ If your project path contains `&` or spaces, use the Node.js direct invocation approach below instead of `npm run dev`.

```bash
cd frontend

# 1. Install dependencies
npm install

# 2. Copy environment file
cp .env.example .env

# 3. Start development server (standard)
npm run dev

# 3b. If path contains & or spaces (Windows workaround):
node node_modules/vite/bin/vite.js
# or from PowerShell:
Push-Location "C:\path\to\frontend"
& "C:\Program Files\nodejs\node.exe" "node_modules\vite\bin\vite.js"

# App available at: http://localhost:5173
```

The Vite dev server proxies `/api` and `/sanctum` calls to `http://localhost:8000`.

---

## 🔑 Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@rentspace.et | password |
| Owner | owner1@rentspace.et | password |
| Owner | owner2@rentspace.et | password |
| Tenant | tenant1@rentspace.et | password |
| Tenant | tenant2@rentspace.et | password |

---

## 🗺 API Documentation

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/register` | Public | Register (owner or tenant) |
| POST | `/api/auth/login` | Public | Login |
| POST | `/api/auth/logout` | Required | Logout |
| GET | `/api/user` | Required | Get authenticated user |
| GET | `/api/profile` | Required | Get profile |
| PUT | `/api/profile` | Required | Update profile |

### Properties (Public)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/properties` | Paginated approved listings (search + filters) |
| GET | `/api/properties/featured` | Featured listings for homepage |
| GET | `/api/properties/cities` | City list with property counts |
| GET | `/api/properties/{id}` | Single property detail |
| GET | `/api/amenities` | All available amenities |

**Query parameters for GET /api/properties:**
```
search, city, type, min_price, max_price, bedrooms, bathrooms,
furnishing_status, sort (newest/price_asc/price_desc), per_page, page
```

### Properties (Owner)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/owner/properties` | Owner's own listings |
| POST | `/api/owner/properties` | Create listing (multipart/form-data) |
| PUT | `/api/owner/properties/{id}` | Update listing |
| POST | `/api/owner/properties/{id}` | Update listing (multipart, same route) |
| DELETE | `/api/owner/properties/{id}` | Delete listing |
| DELETE | `/api/owner/properties/{id}/images/{imgId}` | Remove a single image |

### Favorites

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/favorites` | List user's favorites |
| POST | `/api/properties/{id}/favorite` | Toggle favorite |

### Inquiries

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/properties/{id}/inquiries` | Tenant | Send inquiry |
| GET | `/api/tenant/inquiries` | Tenant | List own inquiries |
| GET | `/api/owner/inquiries` | Owner | List inquiries on own properties |
| PATCH | `/api/owner/inquiries/{id}/status` | Owner | Accept/reject inquiry |

### Dashboards

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/tenant/dashboard` | Tenant | Tenant stats |
| GET | `/api/owner/dashboard` | Owner | Owner stats |
| GET | `/api/admin/dashboard` | Admin | Platform-wide stats |

### Admin

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/properties` | All properties (any status) |
| PATCH | `/api/admin/properties/{id}/status` | Approve / reject listing |
| GET | `/api/admin/users` | Paginated user list |
| PATCH | `/api/admin/users/{id}/status` | Suspend / reactivate user |

---

### Example Request & Response

**POST /api/auth/login**
```json
// Request
{ "email": "tenant1@rentspace.et", "password": "password" }

// Response 200
{
  "data": {
    "id": 5,
    "name": "Meron Alemu",
    "email": "tenant1@rentspace.et",
    "role": "tenant",
    "phone": "+251922200001",
    "status": "active"
  }
}
```

**GET /api/properties?city=Addis+Ababa&type=apartment&sort=price_asc&per_page=6**
```json
{
  "data": [ { "id": 1, "title": "Modern 3-Bedroom Apartment in Bole", "price": 35000, ... } ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "total": 14, "per_page": 6 }
}
```

---

## 🧪 Testing

```bash
cd backend

# Create test database (once)
# In MySQL: CREATE DATABASE rentspace_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run all 60 tests
php artisan test

# Run a specific test suite
php artisan test --filter AuthTest
php artisan test --filter PropertyTest
php artisan test --filter InquiryTest
php artisan test --filter FavoriteTest
```

**Test coverage:**
- Registration and login (role restrictions, validation, duplicate email)
- Authentication guards (401 for unauthenticated, 403 for wrong role)
- Property CRUD with owner scoping
- Cross-owner authorization (owner cannot modify another owner's property)
- Image upload and validation
- Admin approval/rejection
- Inquiry lifecycle (create, duplicate prevention, status updates, cross-owner restriction)
- Favorite toggle and list isolation

---

## 🔒 Security Highlights

- **No tokens in localStorage** — Sanctum SPA uses HTTP-only cookies
- **CSRF protection** — XSRF-TOKEN cookie auto-sent on state-mutating requests
- **Role-based authorization** — `RoleMiddleware` + `PropertyPolicy` on every route
- **Server-side validation** — All inputs validated via Form Requests
- **Secure image uploads** — UUID filenames, MIME type validation, 5MB limit per image
- **Environment variables** — `.env` excluded from git, `.env.example` ships with placeholders
- **Admin bypass** — Admins can access any property without exposing a separate vulnerable path
- **Principle of least privilege** — Owners only see/modify their own listings

---

## 🗄 Database Schema

| Table | Description |
|-------|-------------|
| `users` | Admin, owner, tenant accounts with role + status |
| `properties` | Listings with 20+ fields, indexed on city/status/type/price |
| `property_images` | Images with primary flag and sort order |
| `amenities` | Shared catalog of 25 amenities |
| `property_amenity` | Many-to-many pivot |
| `favorites` | UNIQUE(user_id, property_id) prevents duplicates |
| `inquiries` | Inquiry lifecycle with status tracking |
| `reports` | Property reports for moderation |

---

## 🚢 Deployment Notes

### Backend (Laravel)
1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Set a strong `APP_KEY`
3. Configure `DB_*` for your production MySQL instance
4. Configure `FRONTEND_URL` and `SANCTUM_STATEFUL_DOMAINS` for your domain
5. Run `php artisan config:cache && php artisan route:cache`
6. Set up a web server (Nginx/Apache) pointing to `backend/public`
7. Run `php artisan storage:link` on the server

### Frontend (Vue 3)
1. Set `VITE_API_URL=https://your-api-domain.com` in `.env`
2. Run `npm run build` (or the Node.js direct invocation if path has special chars)
3. Serve the `dist/` directory from your CDN or static host
4. Configure your server to serve `index.html` for all routes (SPA fallback)

---

## 📄 License

MIT © 2024 RentSpace

---

*Built with ❤️ for the Ethiopian rental market. Designed to be production-quality, portfolio-worthy, and genuinely useful.*
