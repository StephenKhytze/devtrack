FROM php:8.3-cli-alpine

# Install system packages & PHP extensions needed for Laravel & TiDB MySQL
RUN apk add --no-cache \
    ca-certificates \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath gd zip opcache

# Update CA certificates for secure TiDB Cloud SSL connections
RUN update-ca-certificates

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set directory permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Expose Koyeb's standard application port
EXPOSE 8000

# Run migrations and start the Laravel web server
CMD sh -c "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"
