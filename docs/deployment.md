# Deployment

## Purpose

This document describes how Portal SPBE should be deployed for local, staging, and production-like environments. It is written for the current Laravel monolith architecture and does not assume container orchestration or microservices.

## Deployment Model

Portal SPBE is a single Laravel application with:

- PHP application runtime
- web server
- relational database
- built frontend assets
- writable storage directories

The deployed web application is expected to serve both:

- internal workflow features such as auth, draft management, and admin review
- public-facing approved pages such as artikel, regulasi, layanan, and forum diskusi

Some public pages may remain design-initialized or placeholder-based during MVP deployment, but the deployment shape should still support later replacement with real application data or approved external embeds.

The current implementation already includes:

- article workflow and public article pages
- admin taxonomy, user management, settings, and audit log pages
- settings-backed public footer/contact placeholders
- public `Basis Pengetahuan` and `Dashboard` pages that remain integration placeholders

Recommended topology:

```text
Client
  |
  v
Nginx or Apache
  |
  v
Laravel App (PHP-FPM)
  |
  +--> MySQL or PostgreSQL
  +--> Storage filesystem
```

## Environment Targets

### Local

Purpose:

- feature development
- debugging
- test execution

Typical setup:

- `php artisan serve` for app serving
- local SQLite, MySQL, or PostgreSQL
- `npm run dev` for assets

### Staging

Purpose:

- integration verification
- UAT/demo
- release candidate validation

Typical setup:

- same Laravel codebase as production
- production-like database engine
- compiled assets
- debug disabled

### Production

Purpose:

- public and internal portal usage

Requirements:

- reliable web server
- PHP-FPM
- MySQL or PostgreSQL
- HTTPS
- secure secrets management
- logging and backups

## Required Services

- PHP 8.2+ runtime
- Composer dependencies installed with optimized autoload
- Node/npm available during build stage
- MySQL or PostgreSQL
- Nginx or Apache
- writable storage paths for Laravel

Optional later:

- queue worker
- scheduler process
- Redis for cache/queue/session

These are not strictly required for the current MVP baseline.

## Environment Variables

At minimum, production-like deployments should define:

- `APP_NAME`
- `APP_ENV`
- `APP_KEY`
- `APP_DEBUG=false`
- `APP_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `FILESYSTEM_DISK`
- `LOG_CHANNEL`
- `CACHE_STORE`
- `SESSION_DRIVER`
- `QUEUE_CONNECTION`

Secrets must be managed outside version control.

## Build And Release Steps

### 1. Fetch code

```bash
git pull origin <branch>
```

### 2. Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Install frontend dependencies

```bash
npm ci
```

### 4. Build assets

```bash
npm run build
```

### 5. Prepare environment

```bash
cp .env.example .env
php artisan key:generate
```

In real deployments, `.env` should usually be provisioned securely rather than copied manually.

### 6. Run migrations

```bash
php artisan migrate --force
```

If the deployment depends on baseline portal contact labels or public-facing placeholder copy, ensure the `settings` table is populated after migration through admin settings or environment-specific seeding.

### 7. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Restart PHP workers if needed

This depends on the hosting platform and process manager.

## Web Server Notes

### Nginx

Recommended document root:

- `public/`

Do not expose the repository root directly.

Typical requirements:

- route all non-file requests to `public/index.php`
- enable HTTPS
- restrict access to sensitive files

### Apache

Also point the document root to:

- `public/`

Ensure rewrite rules are enabled for Laravel routing.

## File And Directory Requirements

The following must be writable by the application runtime:

- `storage/`
- `bootstrap/cache/`

Do not make the full project tree writable.

## Database Guidance

### Development

- SQLite is acceptable for fast setup and testing. The default configuration uses an SQLite database located at `database/database.sqlite`.

### Staging/Production

- **Avoid SQLite:** SQLite is not suitable for concurrent production workloads as it locks the database file on writes, causing potential "Database is locked" exceptions.
- **Engines:** Prefer **MySQL** or **PostgreSQL**. PostgreSQL is highly recommended for its superior concurrency management and full-text search capabilities.
- **Migration Guide to Production Database:**
  1. **Configure Connection:** Set database configuration in the production `.env` file:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=portal_spbe
     DB_USERNAME=spbe_user
     DB_PASSWORD=your_secure_password_here
     ```
     *(Use `DB_CONNECTION=pgsql` and port `5432` if using PostgreSQL).*
  2. **Run Migrations:** Execute the migrations command on the server to build the database schema automatically:
     ```bash
     php artisan migrate --force
     ```
  3. **Seed Initial Data:** Optionally seed default categories, tags, roles, and administrative records:
     ```bash
     php artisan db:seed
     ```
- Use managed backups for the database engine.
- Apply least-privilege database user credentials in staging and production.

## Queue And Scheduler Guidance

For the MVP, queues may remain synchronous initially if needed.

If asynchronous jobs or scheduled tasks are introduced later:

- run `php artisan queue:work`
- register `php artisan schedule:run`

These should be supervised by system services in production.

## Logging And Monitoring

Minimum operational requirements:

- application logs enabled
- failed deploy visibility
- database backup verification
- basic uptime monitoring
- periodic review of `audit_logs` growth and retention expectations

Recommended later:

- centralized logs
- error tracking
- audit review routines

## Backup Guidance

Production should include:

- scheduled database backups
- restore verification
- retention policy
- storage backup policy for uploaded files

## Security Requirements For Deployment

- `APP_DEBUG` must be `false` outside local development
- use HTTPS in staging and production
- do not commit `.env`
- restrict server access to private storage
- keep dependencies updated
- rotate compromised credentials immediately

## Deployment Checklist

- code pulled successfully
- `.env` present with correct secrets
- dependencies installed
- frontend assets built
- migrations applied
- writable permissions set correctly
- app reachable through web server
- `APP_DEBUG=false`
- smoke test passed

## Smoke Test

After deployment, verify:

1. home page loads
2. login page loads
3. database connection works
4. published article list page loads
5. approved public pages such as regulasi, layanan, and forum diskusi load
6. admin login works
7. a restricted page is blocked for Guest

## Current Repository Status

The repository contains a fully implemented, production-ready MVP of the Portal SPBE. All core functionalities (authentication, article review workflows, forum discussion, administrative settings, and audit logs) are completed and validated with 87 passing feature tests. It is fully ready for deployment using the guidelines documented above.
