#!/bin/bash

# We don't install the packages at runtime. This is done when building the Docker image.
# The reason behind this is to be able to serve the container as fast as possible,
# without adding overhead to the scaling process.

# This file is only for small minor fixes on the project, like deploying the files to the CDN,
# caching the config, route, view, running migrations, etc.

git clone https://github.com/Atemndobs/laravel-app.git /var/www/html/larvel-app
cp -rf /var/www/html/larvel-app/* /var/www/html
composer install --ignore-platform-reqs --optimize-autoloader --no-interaction
sh /var/www/html/scripts/db_init.sh


#php artisan config:cache
#php artisan route:cache
#php artisan view:cache
