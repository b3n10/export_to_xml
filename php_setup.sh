#!/bin/bash
apt update
apt-get install -y libjpeg-dev libpng-dev zlib1g-dev libxpm-dev libfreetype6-dev zip unzip

# docker-php-ext-install pdo_mysql mysqli calendar gd
docker-php-ext-install calendar gd
docker-php-ext-configure gd --with-freetype --with-gd --with-webp-dir --with-jpeg-dir --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir --enable-gd-native-ttf

# for htaccess
# a2enmod rewrite

# vi mode
# echo "set -o vi" >> ~/.bashrc

# run `su ben` to tag new files to ben
# addgroup --gid 1000 ben
# adduser --disabled-password --gecos "" --force-badname --ingroup ben ben

# configure /etc/apache2/apache2.conf and/or /etc/apache2/sites-enabled/000-default.conf
# to set up document root
