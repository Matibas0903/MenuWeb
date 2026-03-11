FROM php:8.2-apache

# Desactivar otros MPM y dejar solo prefork
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork

# Instalar driver MySQL para PDO
RUN docker-php-ext-install pdo pdo_mysql

# Copiar proyecto
COPY . /var/www/html/