#!/bin/bash

#echo "remove vendor and composer.lock"
#rm -rf vendor
#rm -f composer.lock
#
#echo "composer install"
#composer install

#echo "Patch MeiliSearch"
#sh scripts/patch_meilisearch.sh

rm -rf /var/www/html/storage/logs/*

/usr/bin/mysql --host=maxscale-masteronly.curator.svc.cluster.local --port=3306 -uroot -pmage mysql -e";
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8mb4;
SET collation_server = utf8mb4_unicode_ci;
"


/usr/bin/mysql --database=mage --user=mage -pmage --host=maxscale-masteronly.curator.svc.cluster.local --port=3306 <  /var/www/html/storage/app/backups/latest/db-dumps/mysql-mage.sql


echo "Checking Update Paths, images and related_songs for Songs : DRY RUN"
php artisan song:path -f related_songs -i mage --dry-run

#php artisan song:path -d music
#php artisan song:path -d images
#php artisan song:path -f related_songs -i mage

echo "To update Paths, images and related_songs for Songs"
echo "php artisan song:path -d music"
echo "php artisan song:path -d images"
echo "php artisan song:path -f related_songs -i mage"

# remove all log files
echo "remove all log files"
rm -rf /var/www/html/storage/logs/*

echo "chmod for storage folder"
chmod -R 777 /var/www/html/storage