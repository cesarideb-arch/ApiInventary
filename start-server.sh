#!/bin/bash

# Limpiar cachés
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Migraciones y seeders
php artisan migrate --force
php artisan db:seed --class=UserSeed --force

# Regenerar cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar servidor
exec php artisan serve --host=0.0.0.0 --port=$PORT