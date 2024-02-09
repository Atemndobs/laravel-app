#!/bin/bash

# We don't install the packages at runtime. This is done when building the Docker image.
# The reason behind this is to be able to serve the container as fast as possible,
# without adding overhead to the scaling process.

# This file is only for small minor fixes on the project, like deploying the files to the CDN,
# caching the config, route, view, running migrations, etc.

# Check Running Processes



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
echo "remove vendor and composer.lock"
rm -f composer.lock

echo "composer install"
composer install --no-interaction # --ignore-platform-reqs --optimize-autoloader --no-dev
npm install
chown -R www-data:www-data /var/www/html

# sim link for storage
ln -s /var/www/html/storage/app/public /var/www/html/public/storage
# run database migrations
echo "Running DB Migrations with the DB Init Script"
sh /var/www/html/scripts/db_init.sh

# run indexers
php artisan indexer:reindex
php artisan scout:import 'App\Models\Song' && php artisan scout:index songs
php artisan scout:import 'App\Models\Catalog' && php artisan scout:index catalogs

cat /var/www/html/scripts/db_init.sh

chmod -R 777 /var/www/html/

# run scheduler
echo "run scheduler"
php /var/www/html/artisan schedule:work >> /dev/null 2>&1 &

# run database migrations
echo " For running database migrations, If Needed"
echo "sh /var/www/html/scripts/db_init.sh"
root@curator-core-cf65f54fb-r479q:/var/www/html#