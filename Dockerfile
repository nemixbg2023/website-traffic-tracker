FROM php:8.2-apache

# Install the PDO MySQL driver - not included in the base image by default
RUN docker-php-ext-install pdo pdo_mysql