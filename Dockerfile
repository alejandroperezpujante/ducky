# ─────────────────────────────────────────────────────────────
# Stage 1: build — PHP+Composer AND Node+Vite assets
# vite-plugin-tempest shells out to `php tempest vite:config`
# during `vite build`, so PHP + vendor/ must exist in this stage.
# ─────────────────────────────────────────────────────────────
FROM composer:2 AS build

RUN apk add --no-cache icu-dev nodejs npm \
    && docker-php-ext-install intl \
    && npm install -g pnpm

WORKDIR /build

# Composer deps first (layer cache); --no-scripts: source not present yet
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --prefer-dist \
    --no-progress \
    --no-interaction

# Node deps (layer cache)
COPY package.json pnpm-lock.yaml vite.config.ts ./
RUN pnpm install --frozen-lockfile

# Full source — needed for `php tempest vite:config` to discover entrypoints
COPY . .

# PHP + vendor/ + source all present → Vite build succeeds
RUN pnpm run build

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
COPY --from=build /build .

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
COPY --from=build /build/public /var/www/html/public

# nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
