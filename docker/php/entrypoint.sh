#!/bin/sh
# Tempest container entrypoint — runs guide deploy steps, then starts php-fpm.
set -e

TEMPEST="/var/www/html/tempest"

echo "[entrypoint] Clearing Tempest caches..."
php "$TEMPEST" cache:clear --force --internal --all || true

echo "[entrypoint] Regenerating discovery cache..."
php "$TEMPEST" discovery:generate --no-interaction

# Uncomment when a database is added:
# echo "[entrypoint] Running migrations..."
# php "$TEMPEST" migrate:up --force

# Uncomment when static pages are used:
# echo "[entrypoint] Regenerating static pages..."
# php "$TEMPEST" static:clean --force
# php "$TEMPEST" static:generate --allow-dead-links

echo "[entrypoint] Starting PHP-FPM..."
exec "$@"
