# Backend Adhivasindo

## Project Overview

Backend API untuk Take Home Test Fullstack Adhivasindo. Project ini menyediakan JWT authentication dan Content Management API untuk kebutuhan aplikasi LMS frontend.

## Features

- Register user.
- Login menggunakan JWT.
- Protected content API.
- List content dengan search dan pagination.
- Create, detail, update, dan delete content.
- Ownership authorization untuk update dan delete.
- API documentation menggunakan Scribe.
- Dokumentasi database menggunakan Mermaid ERD dan table specification.

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL
- JWT Authentication (`php-open-source-saver/jwt-auth`)
- Scribe API Documentation (`knuckleswtf/scribe`)
- Composer

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

Server lokal default:

```text
http://127.0.0.1:8000
```

## Environment

Contoh konfigurasi `.env`:

```env
APP_NAME="Adhivasindo Fullstack"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adhivasindo_fullstack
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=
JWT_ALGO=HS256
```

## JWT Setup

Generate `JWT_SECRET` setelah `.env` dibuat:

```bash
php artisan jwt:secret
```

Gunakan token dari `/api/login` pada protected endpoint:

```http
Authorization: Bearer jwt-token
```

## Database Migration

Jalankan migration:

```bash
php artisan migrate
```

Fresh migration untuk QA:

```bash
php artisan migrate:fresh
```

## Generate API Docs

Dokumentasi API dibuat menggunakan Scribe.

Generate ulang dokumentasi:

```bash
php artisan scribe:generate
```

Akses dokumentasi setelah server berjalan:

```text
http://127.0.0.1:8000/docs
```

Dokumentasi yang dihasilkan mencakup:

- Register
- Login
- List Content
- Create Content
- Detail Content
- Update Content
- Delete Content

## Testing

Format code:

```bash
vendor/bin/pint
```

Run automated tests:

```bash
php artisan test
```

Security audit:

```bash
composer audit
```

Route check:

```bash
php artisan route:list
```

## Folder Structure

```text
app/
├── Http/
│   ├── Controllers/      API controllers
│   ├── Requests/         Form Request validation
│   └── Resources/        API response resources
├── Models/               Eloquent models
└── Traits/               Shared API response helper

database/
├── factories/            Model factories for tests
└── migrations/           Database schema

docs/
├── erd.md                Mermaid ERD
└── table-specification.md

routes/
└── api.php               API route definitions

tests/
└── Feature/              Feature tests
```

## API Endpoints

Base URL:

```text
http://127.0.0.1:8000
```

Authentication:

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| POST | `/api/register` | No | Register new user. |
| POST | `/api/login` | No | Login and receive JWT token. |

Content:

| Method | Endpoint | Auth | Description |
| --- | --- | --- | --- |
| GET | `/api/contents` | JWT | List contents with search and pagination. |
| POST | `/api/contents` | JWT | Create content. |
| GET | `/api/contents/{id}` | JWT | Get content detail. |
| PUT | `/api/contents/{id}` | JWT | Update owned content. |
| DELETE | `/api/contents/{id}` | JWT | Delete owned content. |

## Database Documentation

- [ERD](docs/erd.md)
- [Table Specification](docs/table-specification.md)

## Author

Fadlan Syah
