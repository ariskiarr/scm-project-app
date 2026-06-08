#!/bin/bash
set -e

echo "==> Starting Laravel SCM App on Railway..."

# ------------------------------------------------------------------
# 1. Ensure storage & bootstrap/cache directories have correct perms
# ------------------------------------------------------------------
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

# ------------------------------------------------------------------
# 2. SQLite: create database file if it doesn't exist yet
#    (Railway filesystem is ephemeral – use a Volume for persistence)
# ------------------------------------------------------------------
DB_PATH="${DB_DATABASE:-$(pwd)/database/database.sqlite}"
if [ ! -f "$DB_PATH" ]; then
    echo "==> Creating SQLite database at $DB_PATH"
    touch "$DB_PATH"
fi

# ------------------------------------------------------------------
# 3. Run migrations
# ------------------------------------------------------------------
echo "==> Running migrations..."
php artisan migrate --force --no-interaction

# ------------------------------------------------------------------
# 4. Seed only if the users table is empty (safe first-run seed)
# ------------------------------------------------------------------
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> Database is empty – running seeders..."
    php artisan db:seed --force --no-interaction
else
    echo "==> Database already has data – skipping seeders."
fi

# ------------------------------------------------------------------
# 5. Create storage symlink (suppress error if already exists)
# ------------------------------------------------------------------
php artisan storage:link --quiet || true

# ------------------------------------------------------------------
# 6. Start Laravel server
# ------------------------------------------------------------------
echo "==> App is ready. Listening on port ${PORT:-8080}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
