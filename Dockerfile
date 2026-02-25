FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl \
    libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    libicu-dev libgd-dev libsqlite3-dev sqlite3 \
    nodejs npm \
    && docker-php-ext-install \
    pdo pdo_mysql pdo_sqlite mbstring zip exif pcntl intl gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application source
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install and build frontend assets
RUN npm install && npm run build

# Create storage symbolic link for Laravel
RUN php artisan storage:link

# Set permissions for storage and bootstrap
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache

# Expose port for Laravel dev server (GCP Cloud Run defaults to 8080)
EXPOSE 8080

# Start Laravel using artisan serve
CMD touch database/database.sqlite && \
    php artisan migrate:fresh --seed --force && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear && \
    php artisan serve --host=0.0.0.0 --port=8080