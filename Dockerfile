FROM php:8.2-fpm

#para que Docker sepa que estas variables existirán en el sistema

ENV DB_CONNECTION=mysql
ENV DB_HOST=mysql.railway.internal
ENV DB_PORT=3306
ENV DB_DATABASE=railway
ENV DB_USERNAME=root
ENV DB_PASSWORD=vuIXqThQscTtSDujqJgMjYvSLcWBylQX

# -----------------------------
# 1. Instalar extensiones y dependencias del sistema
# -----------------------------
#docker-php-ext-configure gd --> cuando instales la librería de imágenes (GD), asegúrate de que sepa leer fuentes (FreeType) y archivos JPEG". Sin esto, 
#no podría procesar fotos.
#apt-get update: Actualiza la lista de paquetes disponibles en los repositorios para descargar las versiones más recientes.
#git, zip, unzip: Esenciales para que Composer pueda descargar y extraer sus dependencias para el funcionamiento del proyecto
#curl para descargar Node.js.
#libzip-dev, libpng-dev, etc.: Son librerías de desarrollo. Es el código necesario para que PHP pueda compilar sus propias 
#extensiones de manejo de archivos ZIP e imágenes.
#pdo pdo_mysql: Vital para Laravel. Son los drivers que permiten que PHP hable con la base de datos MySQL en Railway.
#bcmath: Requerido por Laravel para cálculos matemáticos de alta precisión
#zip: Permite a PHP manipular archivos comprimidos.
#gd: La librería para manipulación de imágenes (redimensionar, recortar, etc.).
#Al borrar los archivos temporales de la instalación y las listas de paquetes justo después de instalar, hago que la imagen final de docker sea mucho más pequeña y 
#rápida de subir a Railway.

RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql bcmath zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# -----------------------------
# 2. Instalar Composer v2 (version 2) de su directorio oficial en Docker Hub, la biblioteca oficial en la nube donde los desarrolladores
#guardan y comparten sus imágenes listas para usar.
#Alli cojo el ejecutable y lo copio a mi directorio, de esta manera consigo instalar composer
# el segundo argumento en el comando, es la ruta del ejecutable dentro de la imagen docker de composer en Docker Hub, el tercer argumento es mi ruta 
#local donde instalare composer, 
# usare el mismo mapeado que usa el desarrollador en mi contenedor
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

# 5. Instalar dependencias de composer
# IMPORTANTE: Me aseguro que no se arrastren archivos de cache locales, solo las dependencias limpias
RUN composer install --no-scripts --optimize-autoloader

# 6. Instalar Node y construir assets necesarios para que laravel funcone
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# 7. Permisos (FUNDAMENTAL para que Laravel escriba logs y cache)

#Significa Change Owner (Cambiar dueño), lo que quiere decir que necesito cambiar el dueño del proyecto (cambio al usuario root por el usuario www-data)
#En el servidor web que usa Docker (Apache o PHP-FPM), el "usuario" que ejecuta los procesos se llama por defecto www-data
#Por defecto, al copiar archivos con Docker, estos suelen pertenecer al usuario root. Pero Laravel necesita que 
#el usuario www-data sea el "dueño" para poder manipular los archivos de storage y cache.

# RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache Para darle permisos de escritura al servidor para que pueda generar logs, 
#guardar sesiones y compilar vistas.

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 

#Para aumentar el maximo de pesos de archivos en el servidor php de laravel
#esta es la ruta estándar en Linux para las imágenes oficiales de PHP, alli creo el archivo uploads ini, si no existe,
#y dentro del archivo, añado estas dos lineas para indicar al servidor php, que ignore los valores por defecto
#de tamaño de archivos y use estos

#en conf.d se guardan archivos de configuracion varios, como : Límites de memoria o espacio (como en este caso), zonas horarias, configuraciones de errores etc

#Esta ruta se usa dentro del habitat o el SO de este docker
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini \ 
    && echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini

#&& chmod -R 775 storage && chmod -R 775 bootstrap/cache
# 8. Comando final 
CMD php artisan config:clear && php artisan route:clear && php artisan cache:clear && php artisan storage:link && chmod -R 775 /var/www/html/storage && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
#CMD php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=$PORT
#Para ejecutar factories descomentar el comando anterior y comentar el primer CMD