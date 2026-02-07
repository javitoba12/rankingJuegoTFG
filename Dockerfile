FROM php:8.2-fpm

# -----------------------------
# 1. Instalar extensiones y dependencias del sistema
# -----------------------------
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql bcmath zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# -----------------------------
# 2. Instalar Composer
# -----------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -----------------------------
# 3. Preparar carpeta de proyecto
# -----------------------------
WORKDIR /var/www/html

# 4. Copiar el proyecto
COPY . .
RUN rm -f .env
RUN rm -f bootstrap/cache/*.php

# 5. Instalar dependencias 
# IMPORTANTE: Aseguramos que no se arrastren archivos de cache locales
RUN composer install --no-scripts --optimize-autoloader

# 6. Instalar Node y construir assets (Igual que lo tienes)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# 7. Permisos (FUNDAMENTAL para que Laravel escriba logs y cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Comando final corregido
# Limpiamos CUALQUIER cache que se haya colado en el COPY antes de migrar
CMD php artisan config:clear && \
    php artisan migrate --force  && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}