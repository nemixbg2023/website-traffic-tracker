FROM php:8.2-apache

# Install the PDO MySQL driver - not included in the base image by default
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite - needed to route all requests through index.php
RUN a2enmod rewrite

# Point Apache to the public/ subfolder instead of the project root
# so application code (src/, composer.json) stays outside the web root
RUN sed -i 's#/var/www/html#/var/www/app/public#g' /etc/apache2/sites-available/000-default.conf
RUN echo '<Directory /var/www/app/public>\n\
        AllowOverride All\n\
    </Directory>' >> /etc/apache2/apache2.conf

# Copy Composer binary from the offical Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app