FROM php:8.2-fpm

# -----------------------------
# 1. Instalar extensiones del sistema
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

# Copiar solo composer.json y composer.lock
# esto acelera el build por cache
COPY composer.json composer.lock ./

# -----------------------------
# 4. Instalar dependencias PHP (sin scripts)
#    ❗ IMPORTANTE: evita artisan en el build
# -----------------------------
RUN composer install --no-dev --no-scripts --optimize-autoloader

# -----------------------------
# 5. Copiar el resto del proyecto
# -----------------------------
COPY . .

# -----------------------------
# 6. Instalar Node.js + Build de front (opcional)
# -----------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build \
    && npm cache clean --force

# -----------------------------
# 7. Exponer puerto
# -----------------------------
EXPOSE 8000

# -----------------------------
# 8. Comando FINAL
#    (ahora sí con variables de entorno cargadas por Render)
# -----------------------------
CMD php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan serve --host=0.0.0.0 --port=8000