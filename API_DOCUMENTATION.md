# LaunchCraft API Documentation

> Base URL: `https://<YOUR_SERVER_URL>`  
> All responses follow the standard format `{ "status": "success"|"error", "message": "...", "data": ... }`

---

## Table of Contents

- [0. System & Health Check](#0-system--health-check)
- [1. Authentication (Shared)](#1-authentication-shared)
- [2. Web Builder Endpoints (Website Studio)](#2-web-builder-endpoints-website-studio)
- [3. Flutter Mobile App Endpoints](#3-flutter-mobile-app-endpoints)

---

## 0. System & Health Check

### `GET /api/ping`

Public endpoint to verify the server is alive.

| Property | Value |
|----------|-------|
| **Auth** | Public |
| **Headers** | — |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Pong! Server is active."
}
```

---

## 1. Authentication (Shared)

Both the Web Studio and the Flutter app use these endpoints to register, log in, and manage the session.

---

### `POST /api/register`

Create a new user account and receive a Sanctum token.

| Property | Value |
|----------|-------|
| **Auth** | Public |
| **Headers** | `Accept: application/json`, `Content-Type: application/json` |

**Request Body:**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securePassword123",
  "password_confirmation": "securePassword123"
}
```

**Response `201`:**

```json
{
  "status": "success",
  "message": "User registered successfully.",
  "data": {
    "user": {
      "id": "...",
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    },
    "token": "1|abc123..."
  }
}
```

**Error `422`** — Validation failure (email taken, password too short, etc.):

```json
{
  "status": "error",
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### `POST /api/login`

Authenticate with email + password and receive a Sanctum token.

| Property | Value |
|----------|-------|
| **Auth** | Public |
| **Headers** | `Accept: application/json`, `Content-Type: application/json` |

**Request Body:**

```json
{
  "email": "john@example.com",
  "password": "securePassword123"
}
```

**Response `200`:**

```json
{
  "status": "success",
  "message": "Logged in successfully.",
  "data": {
    "user": {
      "id": "...",
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|abc123..."
  }
}
```

**Error `422`** — Invalid credentials:

```json
{
  "status": "error",
  "message": "The provided credentials are incorrect.",
  "errors": {
    "email": ["The provided credentials are incorrect."]
  }
}
```

---

### `GET /api/user`

Fetch the authenticated user's profile.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Accept: application/json`, `Authorization: Bearer <token>` |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "id": "...",
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### `POST /api/logout`

Revoke the current access token.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Accept: application/json`, `Authorization: Bearer <token>`, `Content-Type: application/json` |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Logged out successfully."
}
```

---

## 2. Web Builder Endpoints (Website Studio)

All endpoints under this section require authentication. The Web Studio manages websites, their design, sections, theme, and media assets.

**Common Headers:**

| Header | Value |
|--------|-------|
| `Accept` | `application/json` |
| `Authorization` | `Bearer <token>` |
| `Content-Type` | `application/json` *(except for media upload)* |

---

### `GET /api/websites`

List all websites owned by the authenticated user.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": [
    {
      "_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "user_id": "...",
      "name": "My Portfolio",
      "business_type": "portfolio",
      "template": "modern",
      "theme": null,
      "sections": null,
      "status": "draft",
      "is_published": false,
      "slug": "my-portfolio-aBc123",
      "pages": null,
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    }
  ]
}
```

---

### `POST /api/websites`

Create a new website / project.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "name": "My Portfolio",
  "business_type": "portfolio",
  "template": "modern",
  "theme": {
    "primary_color": "#3B82F6",
    "secondary_color": "#1E293B",
    "font_family": "Inter"
  },
  "pages": [
    { "name": "Home", "slug": "home" },
    { "name": "About", "slug": "about" }
  ]
}
```

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `name` | Yes | string | Website display name |
| `business_type` | Yes | string | e.g. `portfolio`, `restaurant` |
| `template` | No | string | Template identifier |
| `theme` | No | object | Theme colors & fonts |
| `pages` | No | array | Custom pages array |

**Response `201`:**

```json
{
  "status": "success",
  "message": "Website created successfully.",
  "data": {
    "_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "user_id": "...",
    "name": "My Portfolio",
    "business_type": "portfolio",
    "template": "modern",
    "theme": {
      "primary_color": "#3B82F6",
      "secondary_color": "#1E293B",
      "font_family": "Inter"
    },
    "status": "draft",
    "slug": "my-portfolio-aBc123",
    "is_published": false,
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

---

### `GET /api/websites/{website}`

Fetch a single website by its MongoDB `_id`.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "user_id": "...",
    "name": "My Portfolio",
    "business_type": "portfolio",
    "template": "modern",
    "theme": null,
    "sections": null,
    "status": "draft",
    "is_published": false,
    "slug": "my-portfolio-aBc123",
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

**Error `403`** — Website belongs to another user:

```json
{
  "status": "error",
  "message": "Forbidden."
}
```

---

### `PUT /api/websites/{website}`

Update website metadata.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "name": "My Updated Portfolio",
  "business_type": "restaurant",
  "template": "classic",
  "theme": {
    "primary_color": "#10B981",
    "secondary_color": "#374151",
    "font_family": "Roboto"
  },
  "status": "draft",
  "pages": [
    { "name": "Home", "slug": "home" }
  ]
}
```

All fields are optional.

**Response `200`:**

```json
{
  "status": "success",
  "message": "Website updated successfully.",
  "data": {
    "_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "name": "My Updated Portfolio",
    "business_type": "restaurant",
    ...
  }
}
```

---

### `DELETE /api/websites/{website}`

Delete a website and all its associated data.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Website deleted successfully."
}
```

---

### `PUT /api/websites/{website}/publish`

Publish the website's current theme and sections. Makes the layout available to the Flutter mobile app.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "theme": {
    "primary_color": "#3B82F6",
    "secondary_color": "#1E293B",
    "font_family": "Inter"
  },
  "sections": [
    {
      "type": "hero",
      "sort_order": 0,
      "data": {
        "headline": "Welcome to My Site",
        "subheadline": "Built with LaunchCraft",
        "background_image": "https://example.com/hero.jpg"
      },
      "style": {
        "background_color": "#ffffff",
        "text_color": "#000000",
        "padding": "60px"
      }
    },
    {
      "type": "about",
      "sort_order": 1,
      "data": {
        "content": "This is the about section."
      },
      "style": {
        "background_color": "#f8fafc"
      }
    }
  ]
}
```

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `theme.primary_color` | No | string | Hex colour |
| `theme.secondary_color` | No | string | Hex colour |
| `theme.font_family` | No | string | Font name |
| `sections` | No | array | Ordered layout sections |
| `sections.*.type` | Yes* | string | Section type identifier |
| `sections.*.sort_order` | Yes* | integer | Rendering order |
| `sections.*.data` | No | object | Section content |
| `sections.*.style` | No | object | Section styling |

*\* Required when `sections` is provided.*

**Response `200`:**

```json
{
  "status": "success",
  "message": "Website published successfully.",
  "data": {
    "website": {
      "id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "name": "My Portfolio",
      "domain": null,
      "is_published": true
    },
    "theme": {
      "primary_color": "#3B82F6",
      "secondary_color": "#1E293B",
      "font_family": "Inter"
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
}
```

---

### `GET /api/websites/{website}/media`

List all media files uploaded to a website.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": [
    {
      "_id": "66f1a2b3c4d5e6f7a8b9c0d1",
      "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "filename": "abc123.jpg",
      "path": "uploads/abc123.jpg",
      "size": 204800,
      "url": "https://<YOUR_SERVER_URL>/storage/uploads/abc123.jpg",
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    }
  ]
}
```

---

### `POST /api/websites/{website}/media`

Upload an image file to the website's media library.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Accept: application/json`, `Authorization: Bearer <token>` |
| **Content-Type** | `multipart/form-data` |

**Request Body (form-data):**

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `file` | Yes | file | Image (jpeg, png, jpg, gif, svg) — max 2 MB |

**Response `201`:**

```json
{
  "status": "success",
  "message": "Media uploaded successfully.",
  "data": {
    "_id": "66f1a2b3c4d5e6f7a8b9c0d1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "filename": "abc123.jpg",
    "path": "uploads/abc123.jpg",
    "size": 204800,
    "url": "https://<YOUR_SERVER_URL>/storage/uploads/abc123.jpg",
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

**Error `422`** — Invalid file type or size exceeded:

```json
{
  "status": "error",
  "message": "The file must be an image. (and 1 more error)",
  "errors": {
    "file": [
      "The file must be an image.",
      "The file must be a file of type: jpeg, png, jpg, gif, svg."
    ]
  }
}
```

---

### `DELETE /api/websites/{website}/media/{medium}`

Delete a media file from the library and storage.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Media deleted successfully."
}
```

---

## 3. Flutter Mobile App Endpoints

All endpoints in this section require authentication. The Flutter app uses these to fetch the published website layout and manage per-website data (projects for portfolios, categories/menu-items/orders for restaurants).

**Common Headers:**

| Header | Value |
|--------|-------|
| `Accept` | `application/json` |
| `Authorization` | `Bearer <token>` |
| `Content-Type` | `application/json` *(except where noted)* |

---

### `GET /api/mobile/website`

Fetch the authenticated user's published website layout, theme, and type.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "website": {
      "id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "name": "My Portfolio",
      "domain": null,
      "is_published": true
    },
    "theme": {
      "primary_color": "#3B82F6",
      "secondary_color": "#1E293B",
      "font_family": "Inter"
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
}
```

**Error `404`** — No published website found for the user:

```json
{
  "status": "error",
  "message": "No published website found."
}
```

---

### Portfolio Module

---

#### `GET /api/mobile/projects?website_id={id}`

List all portfolio projects for a given website.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Query Param** | `website_id` (required) — the website's MongoDB `_id` |

**Response `200`:**

```json
{
  "status": "success",
  "data": [
    {
      "_id": "67a1b2c3d4e5f6a7b8c9d0e1",
      "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "title": "E-commerce App",
      "description": "A full-stack e-commerce mobile application.",
      "images": [
        "https://example.com/screenshot1.png",
        "https://example.com/screenshot2.png"
      ],
      "project_url": "https://github.com/user/repo",
      "category": "mobile",
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    }
  ]
}
```

**Error `400`** — Missing `website_id`:

```json
{
  "status": "error",
  "message": "website_id is required."
}
```

**Error `404`** — Website not found or not owned by user:

```json
{
  "status": "error",
  "message": "Website not found."
}
```

---

#### `POST /api/mobile/projects`

Create a new portfolio project.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
  "title": "E-commerce App",
  "description": "A full-stack e-commerce mobile application.",
  "images": [
    "https://example.com/screenshot1.png",
    "https://example.com/screenshot2.png"
  ],
  "project_url": "https://github.com/user/repo",
  "category": "mobile"
}
```

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `website_id` | Yes | string | Target website `_id` |
| `title` | Yes | string | Project title |
| `description` | No | string | Project description |
| `images` | No | array[string] | Image URLs |
| `project_url` | No | string | Link to project |
| `category` | No | string | e.g. `web`, `mobile`, `design` |

**Response `201`:**

```json
{
  "status": "success",
  "message": "Project created successfully.",
  "data": {
    "_id": "67a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "title": "E-commerce App",
    "description": "A full-stack e-commerce mobile application.",
    "images": [
      "https://example.com/screenshot1.png",
      "https://example.com/screenshot2.png"
    ],
    "project_url": "https://github.com/user/repo",
    "category": "mobile",
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

---

#### `GET /api/mobile/projects/{id}`

Fetch a single project by its MongoDB `_id`.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "_id": "67a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "title": "E-commerce App",
    "description": "A full-stack e-commerce mobile application.",
    "images": [...],
    "project_url": "https://github.com/user/repo",
    "category": "mobile",
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

**Error `403`** — Project's website is not owned by the authenticated user:

```json
{
  "status": "error",
  "message": "Forbidden."
}
```

**Error `404`** — Project not found:

```json
{
  "status": "error",
  "message": "Project not found."
}
```

---

#### `PUT /api/mobile/projects/{id}`

Update a portfolio project.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "title": "Updated Title",
  "description": "Updated description.",
  "images": ["https://example.com/new.png"],
  "project_url": "https://example.com",
  "category": "web"
}
```

All fields are optional.

**Response `200`:**

```json
{
  "status": "success",
  "message": "Project updated successfully.",
  "data": {
    "_id": "67a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "title": "Updated Title",
    ...
  }
}
```

---

#### `DELETE /api/mobile/projects/{id}`

Delete a project.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Project deleted successfully."
}
```

---

### Restaurant Module — Categories

---

#### `GET /api/mobile/categories?website_id={id}`

List all menu categories sorted by `sort_order`.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Query Param** | `website_id` (required) |

**Response `200`:**

```json
{
  "status": "success",
  "data": [
    {
      "_id": "68a1b2c3d4e5f6a7b8c9d0e1",
      "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "name": "Appetizers",
      "sort_order": 0,
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    },
    {
      "_id": "68a1b2c3d4e5f6a7b8c9d0e2",
      "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "name": "Main Courses",
      "sort_order": 1,
      ...
    }
  ]
}
```

---

#### `POST /api/mobile/categories`

Create a new menu category.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
  "name": "Appetizers",
  "sort_order": 0
}
```

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `website_id` | Yes | string | Target website `_id` |
| `name` | Yes | string | Category name |
| `sort_order` | No | integer | Display order (defaults to `0`) |

**Response `201`:**

```json
{
  "status": "success",
  "message": "Category created successfully.",
  "data": {
    "_id": "68a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "name": "Appetizers",
    "sort_order": 0,
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

---

#### `GET /api/mobile/categories/{id}`

Fetch a single category.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "_id": "68a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "name": "Appetizers",
    "sort_order": 0,
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

---

#### `PUT /api/mobile/categories/{id}`

Update a category.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "name": "Starters",
  "sort_order": 1
}
```

Both fields are optional.

**Response `200`:**

```json
{
  "status": "success",
  "message": "Category updated successfully.",
  "data": {
    "_id": "68a1b2c3d4e5f6a7b8c9d0e1",
    "name": "Starters",
    "sort_order": 1,
    ...
  }
}
```

---

#### `DELETE /api/mobile/categories/{id}`

Delete a category.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Category deleted successfully."
}
```

---

### Restaurant Module — Menu Items

---

#### `GET /api/mobile/menu-items?website_id={id}`

List all menu items for a given website.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Query Param** | `website_id` (required) |

**Response `200`:**

```json
{
  "status": "success",
  "data": [
    {
      "_id": "69a1b2c3d4e5f6a7b8c9d0e1",
      "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "category_id": "68a1b2c3d4e5f6a7b8c9d0e1",
      "title": "Margherita Pizza",
      "description": "Classic cheese and tomato pizza.",
      "price": 12.99,
      "image": "https://example.com/pizza.jpg",
      "is_available": true,
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    }
  ]
}
```

---

#### `POST /api/mobile/menu-items`

Create a new menu item.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
  "category_id": "68a1b2c3d4e5f6a7b8c9d0e1",
  "title": "Margherita Pizza",
  "description": "Classic cheese and tomato pizza.",
  "price": 12.99,
  "image": "https://example.com/pizza.jpg",
  "is_available": true
}
```

| Field | Required | Type | Description |
|-------|----------|------|-------------|
| `website_id` | Yes | string | Target website `_id` |
| `category_id` | No | string | Category `_id` |
| `title` | Yes | string | Item name |
| `description` | No | string | Item description |
| `price` | Yes | numeric | Price (>= 0) |
| `image` | No | string | Image URL |
| `is_available` | No | boolean | Defaults to `true` |

**Response `201`:**

```json
{
  "status": "success",
  "message": "Menu item created successfully.",
  "data": {
    "_id": "69a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "category_id": "68a1b2c3d4e5f6a7b8c9d0e1",
    "title": "Margherita Pizza",
    "description": "Classic cheese and tomato pizza.",
    "price": 12.99,
    "image": "https://example.com/pizza.jpg",
    "is_available": true,
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

---

#### `GET /api/mobile/menu-items/{id}`

Fetch a single menu item.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "_id": "69a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "category_id": "68a1b2c3d4e5f6a7b8c9d0e1",
    "title": "Margherita Pizza",
    "price": 12.99,
    "is_available": true,
    ...
  }
}
```

---

#### `PUT /api/mobile/menu-items/{id}`

Update a menu item.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "title": "Pepperoni Pizza",
  "price": 14.99,
  "is_available": false
}
```

All fields are optional.

**Response `200`:**

```json
{
  "status": "success",
  "message": "Menu item updated successfully.",
  "data": {
    "_id": "69a1b2c3d4e5f6a7b8c9d0e1",
    "title": "Pepperoni Pizza",
    "price": 14.99,
    "is_available": false,
    ...
  }
}
```

---

#### `DELETE /api/mobile/menu-items/{id}`

Delete a menu item.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Menu item deleted successfully."
}
```

---

### Restaurant Module — Orders

---

#### `GET /api/mobile/orders?website_id={id}&status={status}`

List orders for a website, optionally filtered by status.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Query Params** | `website_id` (required), `status` (optional) |

**Status values:** `pending`, `preparing`, `completed`, `cancelled`

**Response `200`:**

```json
{
  "status": "success",
  "data": [
    {
      "_id": "70a1b2c3d4e5f6a7b8c9d0e1",
      "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
      "customer_name": "Alice Smith",
      "customer_phone": "+201234567890",
      "items": [
        {
          "menu_item_id": "69a1b2c3d4e5f6a7b8c9d0e1",
          "title": "Margherita Pizza",
          "quantity": 2,
          "price": 12.99
        }
      ],
      "total_price": 25.98,
      "status": "pending",
      "created_at": "2026-07-22T12:00:00.000000Z",
      "updated_at": "2026-07-22T12:00:00.000000Z"
    }
  ]
}
```

---

#### `GET /api/mobile/orders/{id}`

Fetch a single order.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |

**Response `200`:**

```json
{
  "status": "success",
  "data": {
    "_id": "70a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "customer_name": "Alice Smith",
    "customer_phone": "+201234567890",
    "items": [
      {
        "menu_item_id": "69a1b2c3d4e5f6a7b8c9d0e1",
        "title": "Margherita Pizza",
        "quantity": 2,
        "price": 12.99
      }
    ],
    "total_price": 25.98,
    "status": "pending",
    "created_at": "2026-07-22T12:00:00.000000Z",
    "updated_at": "2026-07-22T12:00:00.000000Z"
  }
}
```

---

#### `PATCH /api/mobile/orders/{id}/status`

Update the status of an order.

| Property | Value |
|----------|-------|
| **Auth** | Bearer Token |
| **Headers** | `Content-Type: application/json` |

**Request Body:**

```json
{
  "status": "preparing"
}
```

| Field | Required | Type | Allowed Values |
|-------|----------|------|----------------|
| `status` | Yes | string | `pending`, `preparing`, `completed`, `cancelled` |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Order status updated successfully.",
  "data": {
    "_id": "70a1b2c3d4e5f6a7b8c9d0e1",
    "website_id": "66a1b2c3d4e5f6a7b8c9d0e1",
    "customer_name": "Alice Smith",
    "status": "preparing",
    ...
  }
}
```

**Error `422`** — Invalid status value:

```json
{
  "status": "error",
  "message": "The selected status is invalid.",
  "errors": {
    "status": ["The selected status is invalid."]
  }
}
```

---

## Error Reference

| HTTP Code | Meaning |
|-----------|---------|
| `200` | Success |
| `201` | Created successfully |
| `400` | Bad request (missing or invalid parameters) |
| `403` | Forbidden (resource belongs to another user) |
| `404` | Resource not found |
| `422` | Validation error (check `errors` object in response) |
| `500` | Internal server error |

All error responses follow the same structure:

```json
{
  "status": "error",
  "message": "Human-readable error description."
}
```

Validation errors also include an `errors` field:

```json
{
  "status": "error",
  "message": "The email field is required. (and 1 more error)",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```
