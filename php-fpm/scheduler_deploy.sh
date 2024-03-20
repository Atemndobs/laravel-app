#!/bin/bash

# We don't install the packages at runtime. This is done when building the Docker image.
# The reason behind this is to be able to serve the container as fast as possible,
# without adding overhead to the scaling process.

# This file is only for small minor fixes on the project, like deploying the files to the CDN,
# caching the config, route, view, running migrations, etc.

git clone https://github.com/Atemndobs/laravel-app.git /var/www/html/larvel-app
cp -rf /var/www/html/larvel-app/* /var/www/html
rm -r /var/www/html/larvel-app
# create all laravel required storage directories
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
# set permissions
chmod -R 777 /var/www/html/storage
# install composer dependencies
composer install --ignore-platform-reqs --optimize-autoloader --no-interaction
chown -R www-data:www-data /var/www/html
# sim link for storage
ln -s /var/www/html/storage/app/public /var/www/html/public/storage
