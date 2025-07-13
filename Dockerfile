# Stage 1: Build assets and vendor
FROM composer:2.5 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader

FROM node:20 AS node_modules
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

FROM node:20 AS assets
WORKDIR /app
COPY --from=node_modules /app/node_modules ./node_modules
COPY . .
RUN npm run build

# Stage 2: Production image
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    libpng \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    libxml2-dev \
    oniguruma-dev \
    zip \
    unzip \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    mysql-client \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Set working directory
WORKDIR /var/www

# Copy built vendor and assets from previous stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public ./public
COPY --from=assets /app/resources ./resources
COPY --from=assets /app/bootstrap ./bootstrap
COPY --from=assets /app/database ./database
COPY --from=assets /app/routes ./routes
COPY --from=assets /app/app ./app
COPY --from=assets /app/config ./config
COPY --from=assets /app/artisan ./artisan
COPY --from=assets /app/composer.json ./composer.json
COPY --from=assets /app/composer.lock ./composer.lock
COPY --from=assets /app/package.json ./package.json
COPY --from=assets /app/package-lock.json ./package-lock.json

# Set permissions for Laravel
RUN addgroup -g 1000 www-data && adduser -D -G www-data -u 1000 www-data \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 8000

CMD php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:8000 -t public
