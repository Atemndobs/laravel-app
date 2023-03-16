#!/bin/bash

docker exec -it mariadb_master_1 mysql -uroot -pmage mysql -e";
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"


docker exec -it mariadb_master_2 mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;

"


docker exec -it mariadb_slave_1 mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"

docker exec -it mariadb_slave_2 mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"

docker exec -it mariadb_slave_3 mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"

docker exec -it mariadb_slave_4 mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
"

docker exec -it mariadb_shard_A mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;

DROP SCHEMA IF EXISTS test;

stop slave;
change master to master_host='mariadb_shard_A',master_user='root',master_password='mage',master_use_gtid=slave_pos,master_connect_retry=5;
start slave;

"

docker exec -it mariadb_shard_B mysql -uroot -pmage mysql -e "
use mysql;
ALTER USER 'root'@'%' IDENTIFIED BY 'mage';FLUSH PRIVILEGES;
DROP user IF EXISTS 'mage'@'%';
FLUSH PRIVILEGES;
CREATE USER 'mage'@'%' IDENTIFIED BY 'mage';
GRANT ALL PRIVILEGES ON * . * TO 'mage'@'%';
FLUSH PRIVILEGES;
SET character_set_server = utf8;
SET collation_server = utf8mb3_general_ci;
DROP SCHEMA IF EXISTS test;
DROP SCHEMA IF EXISTS mage;

stop slave;
change master to master_host='mariadb_shard_B',master_user='root',master_password='mage',master_use_gtid=slave_pos,master_connect_retry=5;
start slave;

"

# /usr/bin/mysql --database=mage --user=mage -pmage --host=127.0.0.1 --port=4000 <  /home/atem/sites/curator/laravel/storage/app/backups/2022-08-22-21-02-03/db-dumps/mysql-mage.sql
