#!/bin/bash


echo "Update Paths, images and related_songs for Songs"
php artisan song:path -f related_songs -i mage --dry-run

echo "remove vendor and composer.lock"
rm -rf vendor
rm -f composer.lock

echo "Patch MeiliSearch"
sh scripts/patch_meilisearch.sh

echo "composer install"
composer install