# LaunchCraft

Server-Driven UI backend for building and publishing websites. Built with Laravel 13 + MongoDB + Sanctum.

## Stack

- **PHP 8.4** (CLI)
- **Laravel 13**
- **MongoDB 7** via `mongodb/laravel-mongodb` ^5.8
- **Laravel Sanctum** ^4.3 (API token auth)
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

## API Endpoints

All routes prefixed with `/api`.

### Auth

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login, returns Bearer token |
| POST | `/api/logout` | Revoke current token |
| GET | `/api/user` | Get authenticated user |

### Websites (auth required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/websites` | List user's websites |
| POST | `/api/websites` | Create website |
| GET | `/api/websites/{id}` | Get website details |
| PUT | `/api/websites/{id}` | Update website |
| DELETE | `/api/websites/{id}` | Delete website |
| PUT | `/api/websites/{id}/publish` | Publish website with theme & sections |

### Mobile (auth required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/mobile/website` | Get authenticated user's published website (Flutter-ready) |

### Media (auth required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/websites/{id}/media` | List media files |
| POST | `/api/websites/{id}/media` | Upload file |
| DELETE | `/api/websites/{id}/media/{mediaId}` | Delete media |

## Mobile Endpoint Response

`GET /api/mobile/website` returns:

```json
{
  "website": {
    "id": "...",
    "name": "LaunchCraft Demo",
    "domain": "demo.launchcraft.app",
    "is_published": true
  },
  "theme": {
    "primary_color": "#6366f1",
    "secondary_color": "#ec4899",
    "font_family": "Inter, sans-serif"
  },
  "sections": [
    {
      "type": "hero",
      "sort_order": 0,
      "data": { ... },
      "style": { ... }
    }
  ]
}
```

Designed for **Server-Driven UI** on Flutter — loop `sections` and render each by `type`.

## Models

All models use `$connection = 'mongodb'` and extend MongoDB Eloquent classes:

- **User** — `hasMany(Website)`, `morphMany(PersonalAccessToken)`
- **Website** — `belongsTo(User)`, `hasMany(Media)`, casts `theme`, `sections`, `pages` as arrays, `is_published` as boolean
- **Media** — `belongsTo(Website)`
- **PersonalAccessToken** — extends Sanctum's PAT with `DocumentModel` trait, `getKeyType()` returns `'string'` for MongoDB hex IDs

## Testing

```bash
docker exec laravel_app php artisan test
```

Tests use SQLite in-memory for the default connection; MongoDB models bypass it via `$connection = 'mongodb'`. 35 tests cover auth, website CRUD, publish, mobile endpoint, and media.

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
