#!/bin/bash

# Run the kill.sh script
sh kill.sh

# Run the app_deploy.sh script
sh app_deploy.sh

# Use 'tail -f' to continuously display the contents of log files in 'storage/logs/'
tail -f storage/logs/*
