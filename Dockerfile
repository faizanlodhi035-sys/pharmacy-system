FROM php:8.2-cli-alpine

# Install system dependencies & SQLite & Node.js
RUN apk add --no-cache \
    sqlite \
    sqlite-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    oniguruma-dev \
    libxml2-dev \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_sqlite \
    mbstring \
    bcmath \
    gd \
    zip \
    xml \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application files
COPY . .

# Install production dependencies & build frontend assets
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm install \
    && npm run build \
    && rm -f public/hot

# Setup storage and database directories
RUN mkdir -p /app/database /app/storage/framework/views /app/storage/framework/sessions /app/storage/framework/cache /app/storage/logs /app/bootstrap/cache \
    && touch /app/database/database.sqlite \
    && chmod -R 777 /app/storage /app/bootstrap/cache /app/database

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]

