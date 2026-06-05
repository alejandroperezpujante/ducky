#!/bin/bash
# Tempest production deploy script (containerized).
# Mirrors the guide's deploy.sh steps — adapted for Docker.
# Run on the production server after SSH login.
set -e

echo "==> Pulling latest changes..."
git pull --ff-only

echo "==> Building images..."
docker compose build

echo "==> Clearing caches..."
docker compose run --rm app php /var/www/html/tempest cache:clear --force --internal --all

echo "==> Regenerating discovery cache..."
docker compose run --rm app php /var/www/html/tempest discovery:generate --no-interaction

# Uncomment when a database is introduced:
# echo "==> Running migrations..."
# docker compose run --rm app php /var/www/html/tempest migrate:up --force

# Uncomment when static pages are used:
# echo "==> Regenerating static pages..."
# docker compose run --rm app php /var/www/html/tempest static:clean --force
# docker compose run --rm app php /var/www/html/tempest static:generate --allow-dead-links --verbose=true

echo "==> Starting services..."
docker compose up -d

echo "==> Pruning old images..."
docker image prune -f

echo "==> Deploy complete."
