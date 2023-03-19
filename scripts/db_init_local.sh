#!/bin/bash

# /usr/bin/mysql --user=mage -pmage --host=127.0.0.1 --port=4000 -e";
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#"
echo "DB initialized"
 /usr/bin/mysql --user=mage -pmage --host=127.0.0.1 --port=4000 -e "CREATE SCHEMA IF NOT EXISTS mage;"
 /usr/bin/mysql --database=mage --user=mage -pmage --host=127.0.0.1 --port=4000 <  ~/sites/curator/laravel/storage/app/backups/latest/db-dumps/mysql-mage.sql
echo "DB successfully initialized with backups/latest/db-dumps/mysql-mage.sql"

echo "Update Paths, images and related_songs for Songs"
php artisan song:path -f related_songs -i mage --dry-run

php artisan song:path -d music
php artisan song:path -d images
php artisan song:path -f related_songs -i mage

echo "Update Paths, images and related_songs for Songs"