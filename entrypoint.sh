#!/bin/bash
set -e

echo "Starting deployment script..."

# Ensure the database file exists
touch /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database/database.sqlite
chmod 777 /var/www/html/database

# Generate app key if not set
php artisan key:generate --force

# Run migrations and seed
echo "Running migrations..."
php artisan migrate:fresh --seed --force

echo "Starting Apache..."
# Start Apache in the foreground
exec apache2-foreground
