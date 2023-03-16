#!/bin/bash

 /usr/bin/mysql --host=mariadb --port=3306 -uroot -pDHjCkvFGRf mysql -e";
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"
 /usr/bin/mysql --user=mage -pmage --host=mariadb --port=3306 -e "CREATE SCHEMA mage;"
 /usr/bin/mysql --database=mage --user=mage -pmage --host=mariadb --port=3306 <  /var/www/html/storage/app/backups/latest/db-dumps/mysql-mage.sql
