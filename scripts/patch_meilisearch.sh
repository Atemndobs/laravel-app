#!/bin/bash

rm -rf vendor/meilisearch/meilisearch-php/src/Http/Client.php
cp -f patches/Client.php vendor/meilisearch/meilisearch-php/src/Http/Client.php
echo "Patched MeiliSearch Client.php"