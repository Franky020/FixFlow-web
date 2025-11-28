# Usa una imagen base de PHP con FPM
FROM php:8.2-fpm

# -------------------------------------------------------------
# 1. INSTALACIÓN DE DEPENDENCIAS DEL SISTEMA (TODO EN UN BLOQUE)
# -------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    # --- INSTALACIÓN DE NODE.JS ---
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    # --- INSTALAR CADDY ---
    && curl -sL "https://github.com/caddyserver/caddy/releases/download/v2.6.4/caddy_2.6.4_linux_amd64.tar.gz" | tar xz \
    && mv caddy /usr/bin/caddy \
    # --- LIMPIEZA FINAL DE APT ---
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP requeridas por Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo y copiar el código
WORKDIR /var/www/html
COPY . /var/www/html

# Copiar la configuración de Caddy
COPY Caddyfile /etc/caddy/Caddyfile

# -------------------------------------------------------------
# 2. INSTALACIÓN DE DEPENDENCIAS DEL PROYECTO
# -------------------------------------------------------------

# Instalar dependencias de Composer (Backend)
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias de Frontend (NPM)
RUN npm install

# Compilar assets para producción (Vite)
RUN npm run build

# Generar la clave y cachear la configuración de Laravel
RUN php artisan config:cache

# -------------------------------------------------------------
# 3. CONFIGURACIÓN FINAL Y ARRANQUE
# -------------------------------------------------------------

# Configuración de Permisos y Usuario
RUN useradd -ms /bin/bash laravel
RUN chown -R laravel:laravel /var/www/html
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Exponer el puerto del servidor web (Caddy)
EXPOSE 80

# Cambiar al usuario no-root por seguridad
USER laravel

# CMD para iniciar Caddy y PHP-FPM simultáneamente (usando -F para FPM)
CMD sh -c "/usr/bin/caddy run --config /etc/caddy/Caddyfile & php-fpm -F"
