#!/bin/bash

echo "Streaming Audio and Uploading to Warchers"

# php artisan watch:audio  and pipe the output to storage/logs/audio_$logFile
# php artisan watch:audio > storage/logs/audio_$(date +%Y-%m-%d_%H-%M-%S).log 2>&1 &
php artisan watch:upload > storage/logs/audio_$(date +%Y-%m-%d_%H-%M-%S).log 2>&1 &

echo "-----------------Watcher started ------------------------------------------"

# remove all log files
rm -rf /var/www/html/storage/logs/*