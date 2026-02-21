# Compasia Inventory (Decoupled Architecture)

This project follows a **client-server (decoupled) architecture**:

- **Backend**: Laravel 12 API + MySQL
- **Frontend**: standalone Vue 3 + Pinia + Tailwind CSS app in [frontend](frontend)

## Required Stack

### Backend
# Compasia Inventory (Decoupled Backend + Frontend)

This project is split into:

- Backend (Laravel API): root folder
- Frontend (Vue 3 + Pinia + TailwindCSS): [frontend](frontend)

## Stack

### Backend

- Laravel 12 (latest in this project)
- PHP 8.2+
- MySQL
- Contract + Repository + Service pattern
- Standard API response envelope (`success`, `message`, `data`)

### Frontend

- Vue 3
- Pinia
- TailwindCSS
- Vite

## Product Data Design

Products now use this schema:

- `product_id` (unique)
- `type`
- `brand`
- `model`
- `capacity`
- `status`

Indexes are added for searching and sorting performance.

## Excel Import

- Upload endpoint accepts `.xlsx` only (`product_status_list.xlsx`)
- Import engine uses `openspout/openspout` (lightweight, streaming, low RAM)
- No `maatwebsite/excel` dependency is used

## API Features

### Products API

- Search: `search`
- Pagination: `per_page`, `page`
- Sorting: `sort_by`, `sort_direction`

Example:

```bash
GET /api/products?search=iphone&sort_by=brand&sort_direction=asc&per_page=25&page=1
```

### Response Envelope

All API endpoints return a structured payload with status and message.

## Setup

### 1) Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=compasia
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5173
```

Run migrations and server:

```bash
php artisan migrate
php artisan serve
```

### 2) Frontend

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

Frontend environment:

```env
VITE_API_BASE_URL=http://localhost:8000/api
```

## Testing

### Backend

```bash
php artisan test
```

### Frontend

```bash
cd frontend
npm run test:run
```

## Build

```bash
cd frontend
npm run build
```
