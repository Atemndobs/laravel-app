#!/bin/bash

# Run the command and extract the PID
pid=$(ps aux | grep "php /var/www/html/artisan schedule:work" | grep -v grep | awk '{print $2}')

# Check if the PID is empty
if [ -z "$pid" ]; then
  echo "No matching process found."
else
  echo "Killing process with PID: $pid"
  # Kill the process using the obtained PID
  kill -9 "$pid"
fi