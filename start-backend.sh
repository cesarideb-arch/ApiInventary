#!/bin/bash
# Ejecutar migraciones
php artisan migrate --force
php artisan db:seed --class=UserSeed --force

# Iniciar servidor PHP built-in
exec php -S 0.0.0.0:$PORT public/index.php