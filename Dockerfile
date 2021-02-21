FROM php:7.4-apache

RUN a2enmod rewrite

RUN sed -i  "s/http:\/\/httpredir\.debian\.org\/debian/ftp:\/\/ftp\.debian\.org\/debian/g" /etc/apt/sources.list

RUN apt-get clean \
    && apt-get update \
    && apt-get install -y git \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# libraries
RUN apt-get update && apt-get install -y openssl libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

EXPOSE 80