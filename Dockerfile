FROM php:8.2-apache

# Install the PDO MySQL driver - not included in the base image by default
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite - needed to route all requests through index.php
RUN a2enmod rewrite

# Allow .htaccess to override Apache configuration in the web root
RUN echo '<Directory /var/www/html>\n\
        AllowOverride All\n\
    </Directory>' >> /etc/apache2/apache2.conf