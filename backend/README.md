# RentSpace — Backend (Laravel 9 REST API)

This is the backend for RentSpace. See the [main README](../README.md) for full documentation, setup instructions, and API reference.

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB_* in .env
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

## Tests

```bash
php artisan test
```

60 feature tests covering auth, property management, inquiries, and favorites.
