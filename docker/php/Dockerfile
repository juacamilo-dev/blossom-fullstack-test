FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpdo-mysql \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data /var/www/html