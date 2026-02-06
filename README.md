# Dine Discover 🍽️ (PHP + MySQL + Yelp Fusion API)

A secure restaurant finder web app built with **PHP** and **MySQL** using the **Yelp Fusion API**.

**Features**
- Search restaurants by term + location
- Filters: price, open_now, min rating
- Restaurant details page
- Favorites stored in MySQL (session-based, no login required)
- Search logging stored in MySQL
- Simple file cache for Yelp responses (10 min)
- Security: PDO prepared statements, output escaping, CSRF protection for POST actions

## Prerequisites
- PHP 8+ (works on 7.4+)
- MySQL/MariaDB
- Yelp Fusion API key

## Setup

### 1) Database
Create database `dine_discover` and import schema `sql/schema.sql`.

### 2) Environment
Copy `.env.example` to `.env` and fill values:
```bash
cp .env.example .env
```

### 3) Run

#### Option A: XAMPP/WAMP (Windows)
Place folder in web root (e.g., `C:\xampp\htdocs\dine-discover`), start Apache + MySQL, open:
- `http://localhost/dine-discover/public/`

#### Option B: PHP built-in server
From project root:
```bash
php -S localhost:8000 -t public
```
Open:
-[ http://localhost:8000](http://localhost/dine-discover/public/index.php)

## Notes
- Cache is stored in `public/cache/` (safe to delete).
- Favorites are tied to your browser session id.
