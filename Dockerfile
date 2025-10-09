FROM php:8.2-cli

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar aplicación
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Dar permisos al script de inicio
RUN chmod +x .railway/start-server.sh

# Puerto expuesto
EXPOSE $PORT

# Comando de inicio
CMD ["sh", ".railway/start-server.sh"]