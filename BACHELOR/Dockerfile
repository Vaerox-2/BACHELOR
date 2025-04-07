FROM php:8.1-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN apt update && apt install -y imagemagick

RUN mkdir /prizes
RUN chown -R www-data:www-data /prizes
