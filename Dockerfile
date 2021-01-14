FROM php:7.4-apache
# update system
RUN apt-get update && apt-get upgrade -y
# install apache mods
RUN a2enmod rewrite && a2enmod headers && a2enmod expires
RUN service apache2 restart
# Install php extensions
RUN docker-php-ext-install pdo pdo_mysql
# copy app
COPY . /var/www/html/
EXPOSE 80
