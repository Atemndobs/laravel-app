#!/bin/bash

# Check if the required arguments were provided
if [ "$#" -ne 3 ]; then
  echo "Usage: $0 <ZIP_FILE_NAME> <DB_USER> <DB_PASSWORD>"
  exit 1
fi

# Define variables
ZIP_FILE=$1              # The ZIP file name passed as the first argument
DB_USER=$2               # The database user passed as the second argument
DB_PASSWORD=$3           # The database password passed as the third argument
BASE_URL="https://minio.goose-neon.ts.net/curator/backups"  # Base URL where the file is hosted
DOWNLOAD_URL="$BASE_URL/$ZIP_FILE"  # Full URL to download the ZIP file
UNZIP_DIR="db-dumps"
SQL_FILE="mysql-mage.sql"
TARGET_DIR="/var/www/html/storage/app/backups/latest/db-dumps"
DB_NAME="mage"
DB_HOST="mariadb-galera-headless.curator.svc.cluster.local"
DB_PORT="3306"

# Step 1: Download the ZIP file
echo "Downloading the backup file from $DOWNLOAD_URL..."
curl -O -L $DOWNLOAD_URL

# Step 2: Unzip the downloaded file
echo "Unzipping the backup file $ZIP_FILE..."
unzip $ZIP_FILE

# Step 3: Move the unzipped SQL file to the target location
echo "Moving the SQL file to the target directory..."
mv $UNZIP_DIR/$SQL_FILE $TARGET_DIR/$SQL_FILE

# Step 4: Import the SQL file into the database
echo "Importing the SQL file into the database..."
/usr/bin/mysql --database=$DB_NAME --user=$DB_USER --password=$DB_PASSWORD --host=$DB_HOST --port=$DB_PORT < $TARGET_DIR/$SQL_FILE

# Step 5: Clean up - optional, you can delete the ZIP file and/or the extracted files
echo "Cleaning up the downloaded files..."
rm -f $ZIP_FILE
rm -rf $UNZIP_DIR

echo "Backup restoration process completed."
