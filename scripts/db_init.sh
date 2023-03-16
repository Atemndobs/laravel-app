#!/bin/bash

#docker exec -it mariadb_master_1 mysql -uroot -pmage mysql -e";
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#"
#
#
#docker exec -it mariadb_master_2 mysql -uroot -pmage mysql -e "
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#
#"
#
#
#docker exec -it mariadb_slave_1 mysql -uroot -pmage mysql -e "
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#"
#
#docker exec -it mariadb_slave_2 mysql -uroot -pmage mysql -e "
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#"
#
#docker exec -it mariadb_slave_3 mysql -uroot -pmage mysql -e "
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#"
#
#docker exec -it mariadb_slave_4 mysql -uroot -pmage mysql -e "
#ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
#DROP user IF EXISTS 'mage'@'%';
#FLUSH PRIVILEGES;
#CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
#GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
#FLUSH PRIVILEGES;
#SET character_set_server = utf8;
#SET collation_server = utf8mb3_general_ci;
#"
#
#

#docker exec -it mariadb_shard_A mysql -uroot -pmage  -e "CREATE SCHEMA mage;"
#docker exec -it mariadb_shard_B mysql -uroot -pmage  -e "CREATE SCHEMA mage;"

#docker exec -it mariadb_shard_B mysql -uroot -pmage  -e "DROP SCHEMA test; DROP SCHEMA mage; "

 /usr/bin/mysql --user=mage -pmage --host=127.0.0.1 --port=4000 -e "CREATE SCHEMA mage;"
 /usr/bin/mysql --database=mage --user=mage -pmage --host=127.0.0.1 --port=4000 <  /home/atem/sites/curator/laravel/storage/app/backups/latest/db-dumps/mysql-mage.sql


# /usr/bin/mysql --user=root -p"OHn}z>MT>{[mAFOCP)k_" --host=127.0.0.1 --port=3366 -e "CREATE database mage"
#  /usr/bin/mysql --database=mage --user=root -p"OHn}z>MT>{[mAFOCP)k_" --host=127.0.0.1 --port=3366 <  /home/atem/sites/curator/laravel/storage/app/backups/latest/db-dumps/mysql-mage.sql
