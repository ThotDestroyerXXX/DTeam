#!/bin/sh

# DTeam Production Deployment Script
# This script optimizes the Laravel application for production

# Clear all caches first to ensure a clean state
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run composer optimizations
composer install --optimize-autoloader --no-dev

# Cache bootstrap files
php artisan optimize

# Cache routes (only if they don't use closures)
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views
php artisan view:cache

echo "Application optimized for production!"
