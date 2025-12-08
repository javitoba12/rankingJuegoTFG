FROM php:8.2-fpm

# 1. Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3. Copiar proyecto
WORKDIR /var/www/html
COPY . .

# 4. Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# 5. Instalar Node y build
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build \
    && npm cache clean --force

# 6. NO ejecutar artisan en build (Render no tiene env vars aún)
#    (Todo va en el CMD)

# 7. Exponer puerto
EXPOSE 8000

# 8. CMD final (se ejecuta con variables ya cargadas por Render)
CMD php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan serve --host=0.0.0.0 --port=8000
