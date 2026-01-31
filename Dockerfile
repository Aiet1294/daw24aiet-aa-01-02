FROM php:8.2-apache

# 1. Instalar dependencias del sistema y extensiones necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql zip

# 2. Configurar el directorio raíz de Apache para que apunte a /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 3. Activar mod_rewrite para las rutas amigables de Laravel
RUN a2enmod rewrite

# 4. Copiar los archivos de la aplicación
COPY . /var/www/html

# 5. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Instalar dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 7. Asignar permisos a las carpetas de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache