FROM php:8.3-cli-alpine

# Install system dependencies & tools
RUN apk add --no-cache ca-certificates curl git unzip zip bash

# Install PHP extensions using official installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql bcmath gd zip intl opcache pcntl

# Update CA certificates for secure TiDB Cloud SSL connection
RUN update-ca-certificates

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www

# Copy all project files
COPY . .

# Install production dependencies without running artisan scripts during build time
# (Artisan requires runtime environment variables which Render injects at startup)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Set permissions for storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# At runtime: discover packages, cache config/routes, run database migrations, and start server on Render's dynamic port
CMD sh -c "php artisan package:discover --ansi && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
