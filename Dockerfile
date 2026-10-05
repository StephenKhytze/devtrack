FROM php:8.4-cli-alpine

# Set memory limit and superuser flags for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Install required system tools and libraries
RUN apk add --no-cache \
    ca-certificates \
    curl \
    git \
    unzip \
    zip \
    bash \
    icu-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev

# Install complete PHP extensions suite for Laravel & TiDB MySQL
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    pcntl \
    dom \
    xml \
    simplexml \
    mbstring \
    fileinfo

# Update CA certificates for TiDB Cloud SSL connection
RUN update-ca-certificates

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy all project files
COPY . .

# Install production dependencies safely with no-scripts and ignore-platform-reqs
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

# Set directory permissions for Laravel storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# At runtime: discover packages with active secrets, cache configurations, run migrations, and serve
CMD sh -c "php artisan package:discover --ansi && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
