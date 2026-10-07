FROM php:8.4-cli-alpine

# Set memory limit and superuser flags for Composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Install required system tools, libraries, and NodeJS/NPM for Vite asset compilation
RUN apk add --no-cache \
    ca-certificates \
    curl \
    git \
    unzip \
    zip \
    bash \
    nodejs \
    npm \
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

# Compile frontend assets with Vite during Docker build
RUN npm install && npm run build && rm -rf node_modules

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

# Set directory permissions for Laravel storage, cache, and compiled build files
RUN chown -R www-data:www-data storage bootstrap/cache public/build && \
    chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# At runtime: discover packages, remove conflicting storage symlink, cache configs/routes/views, apply migrations persistently, and serve
CMD sh -c "rm -rf public/storage && php artisan package:discover --ansi && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
