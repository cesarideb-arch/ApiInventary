#!/bin/bash
# Ejecutar migraciones
php artisan migrate --force
php artisan db:seed --class=UserSeed --force

# Iniciar servidor
exec php artisan serve --host=0.0.0.0 --port=$PORT