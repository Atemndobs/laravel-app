#!/bin/bash

# Variables
AWS_REGION="us-east-1"
BUCKET_NAME="curators3"
LOCAL_FILE_PATH="storage/app/public/uploads/audio/5MiD1d6C7EXxadJwekOudc/anik_khan_santa_maria.mp3"
S3_KEY="music/khan_santa_maria.mp3" # Change to your desired S3 object key

# Generate a pre-signed URL
PRE_SIGNED_URL=$(aws s3 presign s3://$BUCKET_NAME/$S3_KEY --region $AWS_REGION --expires-in 3600)

echo $PRE_SIGNED_URL

# Check if the pre-signed URL was generated successfully
if [ -z "$PRE_SIGNED_URL" ]
then
    echo "Failed to generate pre-signed URL. Please check your AWS settings."
    exit 1
else
    echo "Pre-signed URL generated successfully."
fi

# Upload the file using curl
curl -X PUT -T "$LOCAL_FILE_PATH" "$PRE_SIGNED_URL"

# End of the script
