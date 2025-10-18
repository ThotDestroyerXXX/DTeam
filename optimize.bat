@echo off
REM DTeam Production Deployment Script for Windows
REM This script optimizes the Laravel application for production

REM Clear all caches first to ensure a clean state
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

REM Run composer optimizations
composer install --optimize-autoloader --no-dev

REM Cache bootstrap files
php artisan optimize

REM Cache routes (only if they don't use closures)
php artisan route:cache

REM Cache config
php artisan config:cache

REM Cache views
php artisan view:cache

echo Application optimized for production!
