#!/bin/bash
#set -e
#
## Variables
#DB_USER="mage"
#DB_NAME="mage"
#PG_DUMP_FILE="/var/www/html/storage/app/backups/latest/db-dumps/postgres-mage.sql"
#
## PostgreSQL commands to configure database settings
#psql -U $DB_USER -d $DB_NAME <<-EOSQL
#    -- Set the character set and collation if needed
#    -- These are generally set when creating the database, shown here just for example
#    -- ALTER DATABASE $DB_NAME SET client_encoding TO 'utf8';
#    -- ALTER DATABASE $DB_NAME SET lc_collate TO 'en_US.utf8';
#    -- ALTER DATABASE $DB_NAME SET lc_ctype TO 'en_US.utf8';
#EOSQL
#
## Importing the database dump
#psql -U $DB_USER -d $DB_NAME < $PG_DUMP_FILE
#
#echo "Database has been initialized and data imported successfully."

## ===================
#set -e
#
## Variables
#DB_USER="mage"
#DB_NAME="mage"
#PG_DUMP_FILE="/var/www/html/storage/app/backups/latest/db-dumps/postgres-mage.sql"
#CONTAINER_NAME="postgresml"
#
## Copying the dump file into the container
#docker cp $PG_DUMP_FILE $CONTAINER_NAME:/tmp/postgres-mage.sql
#
## Importing the database dump
#docker exec -i $CONTAINER_NAME psql -U $DB_USER -d $DB_NAME < /tmp/postgres-mage.sql
#
## Remove the dump file from the container after import (optional)
#docker exec $CONTAINER_NAME rm /tmp/postgres-mage.sql
#
#echo "Database has been initialized and data imported successfully."

## ===================

# Set variables for database connection
 #!/bin/bash




pgloader --debug --verbose mysql://mage:mage@127.0.0.1:4005/mage postgresql://postgresml:postgresml@localhost:5433/mage