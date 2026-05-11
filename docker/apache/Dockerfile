FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# On change le DocumentRoot correctement
RUN sed -i 's!/var/www/html!/var/www/Project_CESI_Web/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/html!/var/www/Project_CESI_Web/public!g' /etc/apache2/apache2.conf

WORKDIR /var/www/Project_CESI_Web