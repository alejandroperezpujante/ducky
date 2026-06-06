#!/bin/bash
# Ducky DEVELOPMENT deploy script (containerized, with Mailpit).
# Layers docker-compose.dev.yml over the base compose file and runs under a
# separate project name (ducky-dev) so it can coexist with prod on the same host.
# Run on the server after SSH login.
set -e

export COMPOSE_FILE=docker-compose.yml:docker-compose.dev.yml
export COMPOSE_PROJECT_NAME=ducky-dev

echo "==> Pulling latest changes..."
git pull --ff-only

echo "==> Building images..."
docker compose build

echo "==> Clearing caches..."
docker compose run --rm app php /var/www/html/tempest cache:clear --force --internal --all

echo "==> Regenerating discovery cache..."
docker compose run --rm app php /var/www/html/tempest discovery:generate --no-interaction

echo "==> Running migrations..."
docker compose run --rm app php /var/www/html/tempest migrate:up --force

# Uncomment when static pages are used:
# echo "==> Regenerating static pages..."
# docker compose run --rm app php /var/www/html/tempest static:clean --force
# docker compose run --rm app php /var/www/html/tempest static:generate --allow-dead-links --verbose=true

echo "==> Starting services..."
docker compose up -d

echo "==> Pruning old images..."
docker image prune -f

echo "==> Deploy complete."
echo "    App:     http://localhost:8080"
echo "    Mailpit: http://localhost:8025"
