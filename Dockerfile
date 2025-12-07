FROM php:8.2-fpm

-----------------------------
1️⃣ Instalar dependencias del sistema
-----------------------------

RUN apt-get update && apt-get install -y
git zip unzip curl libzip-dev libpq-dev libonig-dev
&& docker-php-ext-install pdo pdo_mysql bcmath mbstring tokenizer ctype xml
&& apt-get clean && rm -rf /var/lib/apt/lists/*

-----------------------------
2️⃣ Instalar Composer
-----------------------------

COPY --from=composer /usr/bin/composer /usr/bin/composer

-----------------------------
3️⃣ Copiar proyecto
-----------------------------

WORKDIR /var/www/html
COPY . .

-----------------------------
4️⃣ Instalar dependencias PHP
-----------------------------

RUN composer install --no-dev --optimize-autoloader

-----------------------------
5️⃣ Instalar Node.js y construir assets
-----------------------------

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
&& apt-get install -y nodejs
&& npm ci
&& npm run build
&& npm cache clean --force

-----------------------------
6️⃣ Configuración de Laravel
-----------------------------
Generar APP_KEY si no existe

RUN php artisan key --force

Cachear configuración, rutas y vistas para producción

RUN php artisan config
&& php artisan route
&& php artisan view

-----------------------------
7️⃣ Exponer puerto y comando por defecto
-----------------------------

EXPOSE 8000

Script de inicio: migraciones + serve

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000