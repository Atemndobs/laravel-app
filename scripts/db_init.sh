#!/bin/bash
echo "Patch MeiliSearch"
sh scripts/patch_meilisearch.sh

echo "Update Paths, images and related_songs for Songs"
php artisan song:path -f related_songs -i mage --dry-run
rm -rf vendor
rm -f composer.lock
composer install