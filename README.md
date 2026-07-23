# LaunchCraft

Server-Driven UI backend for building and publishing multi-tenant websites. Built with **Laravel 13 + MongoDB + Sanctum**.

Business types supported: `portfolio`, `restaurant` (extensible).

## Stack

- **PHP 8.4** (CLI)
- **Laravel 13**
- **MongoDB 7** via `mongodb/laravel-mongodb`
- **Laravel Sanctum** (API token auth)
- **Docker** (PHP + MongoDB containers)

## Quick Start

```bash
# Clone and start containers
git clone https://github.com/IbramMamdouh0/LaunchCraft.git
cd LaunchCraft
docker compose up -d

# Install dependencies
docker exec laravel_app composer install
docker exec laravel_app php artisan key:generate

# Seed demo data (optional)
docker exec laravel_app php artisan db:seed --class=MobileTestSeeder
```

Server runs at `http://localhost:8080`.

## Architecture

### Tenant Isolation

Every resource is scoped to a `website_id`. All mobile mutations verify that the website belongs to the authenticated user before allowing any CRUD operation. The website's `business_type` determines which modules the Flutter app renders:

- **portfolio** → Projects
- **restaurant** → Categories, Menu Items, Orders

### Response Format

All API responses follow a unified structure:

```json
{
  "status": "success" | "error",
  "message": "Human-readable message.",
  "data": { ... }
}
```

Validation errors include an additional `errors` object.

## API Overview

Full reference with request/response examples at [`API_DOCUMENTATION.md`](API_DOCUMENTATION.md).

| Section | Base Path | Auth |
|---------|-----------|------|
| Health | `/api/ping` | Public |
| Auth | `/api/*` | Public / Bearer |
| Web Builder | `/api/websites*` | Bearer |
| Mobile | `/api/mobile/*` | Bearer |

### Health & Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/ping` | Server health check (public) |
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login, returns Bearer token |
| GET | `/api/user` | Get authenticated user |
| POST | `/api/logout` | Revoke current token |

### Web Builder (Website Studio)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/websites` | List user's websites |
| POST | `/api/websites` | Create website |
| GET | `/api/websites/{id}` | Get website details |
| PUT | `/api/websites/{id}` | Update website metadata |
| DELETE | `/api/websites/{id}` | Delete website |
| PUT | `/api/websites/{id}/publish` | Publish theme + sections |
| GET | `/api/websites/{id}/media` | List media files |
| POST | `/api/websites/{id}/media` | Upload image (multipart) |
| DELETE | `/api/websites/{id}/media/{mediaId}` | Delete media file |

### Mobile App (Flutter)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/mobile/website` | Fetch published website layout |

**Portfolio Module:**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/mobile/projects?website_id=` | List projects |
| POST | `/api/mobile/projects` | Create project |
| GET | `/api/mobile/projects/{id}` | Get project |
| PUT | `/api/mobile/projects/{id}` | Update project |
| DELETE | `/api/mobile/projects/{id}` | Delete project |

**Restaurant Module:**

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/mobile/categories?website_id=` | List categories (sorted) |
| POST | `/api/mobile/categories` | Create category |
| GET | `/api/mobile/categories/{id}` | Get category |
| PUT | `/api/mobile/categories/{id}` | Update category |
| DELETE | `/api/mobile/categories/{id}` | Delete category |
| GET | `/api/mobile/menu-items?website_id=` | List menu items |
| POST | `/api/mobile/menu-items` | Create menu item |
| GET | `/api/mobile/menu-items/{id}` | Get menu item |
| PUT | `/api/mobile/menu-items/{id}` | Update menu item |
| DELETE | `/api/mobile/menu-items/{id}` | Delete menu item |
| GET | `/api/mobile/orders?website_id=&status=` | List orders (filterable) |
| GET | `/api/mobile/orders/{id}` | Get order details |
| PATCH | `/api/mobile/orders/{id}/status` | Update order status |

## Models

All models use `$connection = 'mongodb'` and extend `MongoDB\Laravel\Eloquent\Model`.

| Model | Collection | Key Fields |
|-------|------------|------------|
| `User` | `users` | — |
| `Website` | `websites` | `user_id`, `name`, `business_type`, `template`, `theme`, `sections`, `is_published`, `slug` |
| `Media` | `media` | `website_id`, `filename`, `path`, `size` |
| `Project` | `projects` | `website_id`, `title`, `description`, `images[]`, `project_url`, `category` |
| `Category` | `categories` | `website_id`, `name`, `sort_order` |
| `MenuItem` | `menu_items` | `website_id`, `category_id`, `title`, `description`, `price`, `image`, `is_available` |
| `Order` | `orders` | `website_id`, `customer_name`, `customer_phone`, `items[]`, `total_price`, `status` |

## Testing

```bash
docker exec laravel_app php artisan test
```

Tests use SQLite in-memory for the default connection; MongoDB models bypass it via `$connection = 'mongodb'`.

## CI/CD

GitHub Actions runs tests on push/PR to `main`:
- PHP 8.4 + MongoDB 7 service container
- `mongodb` extension via pecl
- Full test suite

## Docker

```yaml
services:
  app:   PHP 8.4 CLI + MongoDB extension, exposes port 8000
  mongo: MongoDB 7, port 27017
```
