# ─────────────────────────────────────────────────────────────
# Stage 1: front-end assets (pnpm + Vite)
# ─────────────────────────────────────────────────────────────
FROM node:22-alpine AS frontend

RUN corepack enable && corepack prepare pnpm@latest --activate

WORKDIR /build

# Layer-cache dependency install separately from source copy
COPY package.json pnpm-lock.yaml vite.config.ts ./
COPY app/ ./app/

RUN pnpm install --frozen-lockfile

# Produce public/build/ and public/main.css
RUN pnpm run build

# ─────────────────────────────────────────────────────────────
# Stage 2: PHP vendor directory (Composer)
# ─────────────────────────────────────────────────────────────
FROM composer:2 AS vendor

WORKDIR /build

# Install deps first (no scripts) for better layer caching
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --prefer-dist \
    --no-progress \
    --no-interaction

# Copy full app source
COPY . .

# Copy compiled front-end assets
COPY --from=frontend /build/public/build/ ./public/build/
COPY --from=frontend /build/public/main.css ./public/main.css

# Re-dump autoload (triggers post-autoload-dump → discovery:generate)
RUN composer dump-autoload --no-dev

# ─────────────────────────────────────────────────────────────
# Target: app  (php-fpm)
# ─────────────────────────────────────────────────────────────
FROM php:8.5-fpm-alpine AS app

# Install runtime deps: icu for intl, other required exts are bundled
RUN apk add --no-cache icu-dev \
    && docker-php-ext-install intl

WORKDIR /var/www/html

# Copy application
COPY --from=vendor /build .

# Copy entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Ensure .tempest cache dir exists and is writable by www-data (php-fpm user)
RUN mkdir -p .tempest \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/.tempest

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

# ─────────────────────────────────────────────────────────────
# Target: web  (nginx static + reverse proxy)
# ─────────────────────────────────────────────────────────────
FROM nginx:alpine AS web

# Copy only the public web root (static assets + front controller)
COPY --from=vendor /build/public /var/www/html/public

# nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
