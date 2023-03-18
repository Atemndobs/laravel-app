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

 /usr/bin/mysql --user=mage -pmage --host=127.0.0.1 --port=4000 -e "CREATE SCHEMA IF NOT EXISTS mage;"
 /usr/bin/mysql --database=mage --user=mage -pmage --host=127.0.0.1 --port=4000 <  ~/sites/curator/laravel/storage/app/backups/latest/db-dumps/mysql-mage.sql

