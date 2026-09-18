FROM php:8.2-fpm-alpine

RUN docker-php-ext-install pdo_mysql

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html
