#!/bin/bash

# Mostrar variables de entorno para debugging
echo "PORT: $PORT"
echo "DATABASE_URL: $DATABASE_URL"

# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders
echo "Ejecutando seeders..."
php artisan db:seed --class=UserSeed --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar que el puerto esté disponible
echo "Iniciando servidor en puerto: $PORT"

# Iniciar servidor Laravel
exec php artisan serve --host=0.0.0.0 --port=$PORT