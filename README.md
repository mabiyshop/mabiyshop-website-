# Mabiyshop Website - Laravel Project

## Project Overview
Mabiyshop is a Laravel 8 e-commerce application with Vue.js server-side rendering (SSR). It uses Vue 2 + Vuex + Vue Router for the frontend and Laravel Mix for asset compilation.

## Tech Stack
- **Backend**: Laravel 8.0.4, PHP 8.0.30
- **Frontend**: Vue 2.5, Vuex 3, Vue Router 3, Tailwind CSS
- **Database**: MySQL / MariaDB 11.4
- **Asset Build**: Laravel Mix, Webpack
- **Server-side Rendering**: spatie/laravel-server-side-rendering

## Prerequisites
- PHP >= 7.2.0 (tested with PHP 8.0.30)
- Composer 2.x
- Node.js & npm
- MySQL or MariaDB
- Git

## Local Development Setup

### 1. Clone the Repository
```bash
git clone <repository-url>
cd mabiyshop-website-
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Configuration
Copy `.env` to `.env.local` if needed, or edit the existing `.env`:

```env
APP_NAME=Mabiyshop
APP_ENV=local
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mabiyshop_local
DB_USERNAME=root
DB_PASSWORD=root123
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Database Setup
Create the database and run migrations:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS mabiyshop_local;"
php artisan migrate
```

Optional - seed the database:
```bash
php artisan db:seed
```

### 7. Compile Assets
```bash
# Development
npm run dev

# Or watch for changes
npm run watch

# Production build
npm run prod
```

### 8. Run the Application
```bash
php artisan serve --port=8000
```

Visit: **http://127.0.0.1:8000**

## Project Structure
```
mabiyshop-website-/
├── app/                    # Application core code
├── bootstrap/              # Framework bootstrapping
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── public/                 # Publicly accessible files
│   ├── assets/             # Compiled CSS/JS
│   └── images/             # Static images
├── resources/
│   ├── assets/             # Source CSS/JS/Vue files
│   └── views/              # Blade templates
├── routes/
│   ├── web.php             # Web routes
│   ├── api.php             # API routes
│   └── custom.php          # Custom routes
├── storage/                # Logs, cache, sessions
├── system_operator/        # Backup/deployment folder (not part of main app)
└── vendor/                 # Composer dependencies
```

## Important Notes
- `system_operator/` folder is a full project backup and should NOT be used for development
- The main application code is in the root directory
- `.env` contains production database credentials - use `.env.example` for local setup
- APP_KEY is already set in `.env` - run `php artisan key:generate` only if you reset it

## Common Commands
```bash
# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Check migration status
php artisan migrate:status

# Run tests
php artisan test

# List all routes
php artisan route:list
```

## Troubleshooting
1. **Class not found**: Run `composer dump-autoload`
2. **Permission denied on storage**: Ensure `storage/` and `bootstrap/cache/` are writable
3. **Asset not loading**: Run `npm run dev` to compile assets
4. **Database connection error**: Verify MySQL is running and credentials in `.env` are correct
