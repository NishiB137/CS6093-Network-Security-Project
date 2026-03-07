# Use the official PHP 8.2 with Apache
FROM php:8.2-apache

# Install the underlying C libraries required for PostgreSQL and GD (Image processing)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Configure and install the GD extension for PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Install raw PHP extensions for PostgreSQL 
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Enable Apache mod_rewrite for custom MVC routing
RUN a2enmod rewrite

# 1. Enable SSL module
RUN a2enmod ssl

# 2. Copy the certs from your host to the container's internal SSL directory
COPY docker/certs/server.crt /etc/ssl/certs/server.crt
COPY docker/certs/server.key /etc/ssl/private/server.key


# Overwrite the default Apache configuration with our custom one
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

RUN mkdir -p /var/www/html/public/uploads/profile_images \
    && chown -R www-data:www-data /var/www/html/public/uploads