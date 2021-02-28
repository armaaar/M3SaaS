FROM php:7.4-apache
# update system
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y \
        rsyslog \
        cron \
        libmosquitto-dev \
        sudo
# install apache mods
RUN a2enmod rewrite && a2enmod headers && a2enmod expires && a2enmod deflate
# Install php extensions
RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install Mosquitto-alpha \
    && docker-php-ext-enable mosquitto
# copy app
COPY . /var/www/html/
# Add crob job command
RUN crontab /var/www/html/m3saas_cronjob
# Expose apache server
EXPOSE 80
# Run init shell script on container start
CMD sh /var/www/html/bin/container_init.sh
