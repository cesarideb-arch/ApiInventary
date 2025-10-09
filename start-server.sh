#!/bin/bash

# Debug info
echo "=== INICIANDO APLICACIÓN ==="
echo "PORT: $PORT"
echo "PWD: $(pwd)"
echo "LISTANDO ARCHIVOS:"
ls -la

# Esperar a que la base de datos esté lista
sleep 2

# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
echo "=== EJECUTANDO MIGRACIONES ==="
php artisan migrate --force

# Ejecutar seeders
echo "=== EJECUTANDO SEEDERS ==="
php artisan db:seed --class=UserSeed --force

# Optimizar
php artisan config:cache
php artisan route:cache

# Verificar que todo esté listo
echo "=== VERIFICANDO CONFIGURACIÓN ==="
php artisan route:list

# Iniciar servidor
echo "=== INICIANDO SERVIDOR EN PUERTO $PORT ==="
exec php -S 0.0.0.0:$PORT public/index.php
