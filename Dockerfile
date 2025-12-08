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

# Copiar solo composer.json y composer.lock (para usar cache de Docker)
COPY composer.json composer.lock ./

# -----------------------------
# 4. Instalar dependencias PHP (sin ejecutar scripts)
# -----------------------------
RUN composer install --no-dev --no-scripts --optimize-autoloader

# -----------------------------
# 5. Copiar el resto del proyecto
# -----------------------------
COPY . .

# -----------------------------
# 6. Instalar Node.js y construir assets
# -----------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build \
    && npm cache clean --force

# -----------------------------
# 7. Exponer puerto
# -----------------------------
EXPOSE 8000

# -----------------------------
# 8. Comando final para Render
# -----------------------------
CMD php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan serve --host=0.0.0.0 --port=8000
