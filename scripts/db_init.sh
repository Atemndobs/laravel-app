#!/bin/bash

#echo "remove vendor and composer.lock"
#rm -rf vendor
#rm -f composer.lock
#
#echo "composer install"
#composer install

#echo "Patch MeiliSearch"
#sh scripts/patch_meilisearch.sh

/usr/bin/mysql --host=mariadb-galera.curator.svc.cluster.local --port=3306 -uroot -pmage mysql -e";
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"


/usr/bin/mysql --database=mage --user=mage -pmage --host=mariadb-galera --port=3306 <  /var/www/html/storage/app/backups/latest/db-dumps/mysql-mage.sql


echo "Update Paths, images and related_songs for Songs"
php artisan song:path -f related_songs -i mage --dry-run

php artisan song:path -d music
php artisan song:path -d images
php artisan song:path -f related_songs -i mage

echo "Update Paths, images and related_songs for Songs"