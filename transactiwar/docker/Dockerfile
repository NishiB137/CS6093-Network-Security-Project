# Use the official PHP 8.2 with Apache
FROM php:8.2-apache

# Install the underlying C libraries required for PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev

# Install raw PHP extensions for PostgreSQL 
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Overwrite the default Apache configuration with our custom one
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf