# Usa una imagen base de PHP con FPM (FastCGI Process Manager)
# FPM es el procesador de PHP al que se conectará tu servidor web (Nginx o Caddy).
FROM php:8.2-fpm

# Instalar dependencias del sistema operativo (para Git, zip, y extensiones de PHP)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Instalar Caddy (servidor web)
RUN curl -sL "https://github.com/caddyserver/caddy/releases/download/v2.6.4/caddy_2.6.4_linux_amd64.tar.gz" | tar xz \
    && mv caddy /usr/bin/caddy

# Copiar configuración de Caddy
COPY Caddyfile /etc/caddy/Caddyfile
# ... (Resto de la instalación, Composer, etc., es igual) ...

# Limpiar los paquetes descargados para reducir el tamaño final de la imagen
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP requeridas por Laravel y muchas aplicaciones
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer (el gestor de dependencias de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar el código fuente del proyecto a la imagen
COPY . /var/www/html

# Instalar las dependencias de Laravel
# Usamos --no-dev para instalar solo las dependencias de producción y reducir el tamaño.
RUN composer install --no-dev --optimize-autoloader

# Generar la clave de la aplicación (esto debe hacerse antes de que se ejecute la aplicación)
# Nota: En producción, la clave APP_KEY debe ser una variable de entorno de Render.
# Sin embargo, para fines de construcción, puedes generar el archivo de caché.
RUN php artisan config:cache

# Configuración de Permisos
# Crear un usuario no-root por seguridad. Laravel necesita permisos para la carpeta storage
RUN useradd -ms /bin/bash laravel
RUN chown -R laravel:laravel /var/www/html
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Exponer el puerto de PHP-FPM (el puerto por donde escuchará)
EXPOSE 80

# Cambiar al usuario no-root para ejecutar la aplicación (seguridad)
USER laravel

# Usa un script de shell para asegurar que ambos procesos corran y permanezcan vivos
CMD sh -c "/usr/bin/caddy run --config /etc/caddy/Caddyfile & php-fpm -F"
