# Stage 1: Build frontend assets
FROM node:20 AS assets
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP application
FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Bring in the built frontend assets from stage 1
COPY --from=assets /app/public/build ./public/build

RUN composer install --optimize-autoloader --no-dev --no-interaction

RUN php artisan config:clear

CMD php artisan migrate --force && php artisan serve --host 0.0.0.0 --port ${PORT:-10000}