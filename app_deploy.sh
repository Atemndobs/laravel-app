#!/bin/bash

# ANSI color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check and kill any running php artisan processes
echo -e "${BLUE}Checking for existing php artisan processes...${NC}"
if pgrep -f "php artisan"; then
    echo -e "${RED}Killing existing php artisan processes...${NC}"
    pkill -f "php artisan"
    echo -e "${GREEN}Processes killed.${NC}"
else
    echo -e "${BLUE}No existing php artisan processes found.${NC}"
fi

# Rest of your script...

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
echo -e "${BLUE}Remove vendor and composer.lock${NC}"
rm -f composer.lock

echo -e "${BLUE}Composer install${NC}"
composer install --no-interaction # --ignore-platform-reqs --optimize-autoloader --no-dev
npm install
chown -R www-data:www-data /var/www/html

# sim link for storage
ln -s /var/www/html/storage/app/public /var/www/html/public/storage

# run database migrations
echo -e "${BLUE}Running DB Migrations with the DB Init Script${NC}"
# sh /var/www/html/scripts/db_init.sh

# run indexers
php artisan indexer:reindex
php artisan scout:import 'App\Models\Song' && php artisan scout:index songs
php artisan scout:import 'App\Models\Catalog' && php artisan scout:index catalogs

cat /var/www/html/scripts/db_init.sh

chmod -R 777 /var/www/html/

# run scheduler
echo -e "${BLUE}Run scheduler${NC}"
php /var/www/html/artisan schedule:work >> /dev/null 2>&1 &

# run database migrations
echo -e "${BLUE}For running database migrations, If Needed${NC}"
echo -e "${BLUE}sh /var/www/html/scripts/db_init.sh${NC}"
