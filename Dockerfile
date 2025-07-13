# Stage 1: Build JS assets
FROM node:20 AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production PHP image
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

# Copy application code
COPY . .

# Copy built assets from previous stage
COPY --from=assets /app/public ./public
COPY --from=assets /app/node_modules ./node_modules

# Install Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Set permissions for Laravel
RUN addgroup -g 1000 www-data && adduser -D -G www-data -u 1000 www-data \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 8000

CMD php artisan migrate --force && php artisan storage:link && php -S 0.0.0.0:8000 -t public
