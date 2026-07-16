# Backend Adhivasindo

Backend API untuk Take Home Test Fullstack Adhivasindo menggunakan Laravel 12, MySQL, dan JWT authentication.

## Requirements

- PHP 8.2 atau lebih baru
- Composer
- MySQL

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

Default database pada `.env.example`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=adhivasindo_fullstack
DB_USERNAME=root
DB_PASSWORD=
```

## Authentication API

Base URL lokal:

```text
http://127.0.0.1:8000
```

### Register

Endpoint:

```http
POST /api/register
```

Request:

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

Success response:

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2026-07-16T06:00:00.000000Z",
        "updated_at": "2026-07-16T06:00:00.000000Z"
    }
}
```

### Login

Endpoint:

```http
POST /api/login
```

Request:

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

Success response:

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "jwt-token",
        "token_type": "bearer",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2026-07-16T06:00:00.000000Z",
            "updated_at": "2026-07-16T06:00:00.000000Z"
        }
    }
}
```

Invalid credentials response:

```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

Validation error response:

```json
{
    "success": false,
    "message": "Validation Error",
    "errors": {}
}
```

## Using JWT Token

For protected endpoints in future sprints, send the token from the login response in the `Authorization` header:

```http
Authorization: Bearer jwt-token
```

## Database Documentation

- [ERD](docs/erd.md)
- [Table Specification](docs/table-specification.md)
