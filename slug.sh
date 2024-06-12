#!/bin/bash

# Check if the file is provided as an argument
if [ -z "$1" ]; then
  echo "Usage: $0 <file_with_slugs>"
  exit 1
fi

# Base URL for the requests
BASE_URL="http://curator-mood-extractor.curator.svc.cluster.local:3000/song"

# Read the file line by line
while IFS= read -r slug; do
  # Make the HTTP request
  curl "${BASE_URL}/${slug}"
  
  # Wait for 30 seconds before making the next request
  sleep 30
done < "$1"
