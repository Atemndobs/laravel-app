#!/bin/bash

rm vendor/meilisearch/meilisearch-php/src/Http/Client.php
cp patches/Client.php vendor/meilisearch/meilisearch-php/src/Http/Client.php
