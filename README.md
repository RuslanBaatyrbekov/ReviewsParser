# Yandex Maps Reviews Aggregator

[![tests](https://github.com/RuslanBaatyrbekov/ReviewsParser/actions/workflows/tests.yml/badge.svg)](https://github.com/RuslanBaatyrbekov/ReviewsParser/actions/workflows/tests.yml)

A Single Page Application (SPA) built with Laravel and Vue 3 (Inertia.js) to aggregate and display reviews from Yandex Maps. It features platform connection, review pagination, statistics calculation, and dynamic sorting.

## 🛠 Tech Stack

* **Backend:** PHP 8.x, Laravel 11, MySQL/PostgreSQL
* **Frontend:** Vue 3 (Composition API), Inertia.js, Tailwind CSS
* **Authentication:** Laravel Breeze

## 🚀 Key Features

* **Authentication:** User login and registration.
* **Settings Page:** A form to connect and add a Yandex Maps organization link.
* **Reviews Page:** Displays reviews (name, avatar, date, rating, text), overall company rating, and total review count. Includes pagination and date sorting (newest/oldest).
* **Overwrite Logic:** Adding a new link securely deletes the user's old reviews and fetches new ones within a database transaction.

## 🧩 Review Source & Mock Data

Yandex Maps protects organisation pages with SmartCaptcha, which blocks headless browsers and plain HTTP clients. Instead of fighting the bot protection, the app talks to a `App\Contracts\ReviewSource` interface:

* `App\Services\YandexParser` is the current **mock implementation**: it simulates network latency and generates a realistic dataset (15–30 reviews).
* Plugging in an official API or a licensed data provider means writing one more `ReviewSource` implementation and changing a single binding in `AppServiceProvider` — controllers, DB transactions and the Vue UI stay untouched.
* Only Yandex Maps links are accepted; importing a new link replaces the user's previous reviews inside a single database transaction.

## ⚙️ Local Installation & Setup

1. Clone the repository and install dependencies:
```bash
git clone https://github.com/RuslanBaatyrbekov/ReviewsParser.git
cd ReviewsParser
composer install
npm install
```

2. Configure environment variables:
```bash
cp .env.example .env
php artisan key:generate
```

3. Run migrations and seed the database (this will create a test user):
```bash
php artisan migrate --seed
```

4. Start the local server and frontend build tool in two separate terminal windows:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

## 🔐 Test Access

Running the database seeder automatically creates a test account for quick access:
* **Email:** admin@admin.com
* **Password:** password

## 🌍 Production Deployment (VDS/VPS)

1. Clone the repository to your server.
2. Install PHP dependencies optimized for production: `composer install --optimize-autoloader --no-dev`
3. Build frontend assets: `npm install && npm run build`
4. Configure Nginx to point the root to the `/public` directory, routing all requests through `index.php`.
5. Ensure the web server (e.g., `www-data`) has write permissions for the storage and cache directories:
```bash
chown -R www-data:www-data storage bootstrap/cache
```

## ✅ Tests

```bash
php artisan test
```

Feature tests cover the reviews page (per-user isolation, statistics, pagination, sorting) and the import flow (replacement in a transaction, empty results, URL validation, auth) using a fake `ReviewSource`. Set `YANDEX_MOCK_DELAY=0` in `.env` to skip the simulated latency locally.

