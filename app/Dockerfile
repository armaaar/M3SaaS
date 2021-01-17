FROM php:7.4-apache
# update system
RUN apt-get update && apt-get upgrade -y && apt-get install -y cron
# install apache mods
RUN a2enmod rewrite && a2enmod headers && a2enmod expires
# RUN service apache2 restart
# Install php extensions
RUN docker-php-ext-install pdo pdo_mysql
# copy app
COPY . /var/www/html/
# Add crob job command
# COPY ./m3saas_cron /etc/cron.hourly/
# COPY ./m3saas_cron /etc/cron.daily/
# RUN service cron restart
RUN crontab /var/www/html/m3saas_cronjob
EXPOSE 80
