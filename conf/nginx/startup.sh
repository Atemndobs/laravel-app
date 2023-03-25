#!/bin/bash

#if [ ! -f /etc/nginx/ssl/curator.crt ]; then
#    openssl genrsa -out "/etc/nginx/ssl/curator.key" 2048
#    chmod 644 "/etc/nginx/ssl/curator.key"
#    openssl req -x509 -new -nodes -key "/etc/nginx/ssl/curator.key" -sha256 -days 3650 -out "/etc/nginx/ssl/curator.pem"
#    chmod 644 "/etc/nginx/ssl/curator.pem"
#    openssl req -new -sha256 -nodes -out "/etc/nginx/ssl/curator.csr" -newkey rsa:2048 -keyout "/etc/nginx/ssl/curator.key" -config <( cat "/etc/nginx/ssl/curator.csr.cnf" )
#    #chmod 644 "/etc/nginx/ssl/curator.csr"
#    openssl x509 -req -in  "/etc/nginx/ssl/curator.csr" -CA "/etc/nginx/ssl/curator.pem" -CAkey "/etc/nginx/ssl/curator.key" -CAcreateserial -out "/etc/nginx/ssl/curator.crt" -days 3650 -sha256 -extfile "/etc/nginx/ssl/v3.ext"
#    chmod 644 "/etc/nginx/ssl/curator.crt"
#fi

# rm /etc/nginx/ssl/*
rm /etc/ssl/certs/ca-certificates.crt
if [ ! -f /etc/nginx/ssl/curator.crt ]; then
    openssl genrsa -out "/etc/nginx/ssl/curator.key" 2048
    # openssl req -x509 -new -nodes -key "/etc/nginx/ssl/curator.key" -sha256 -days 3650 -out "/etc/nginx/ssl/curator.pem"
    openssl req -x509 -new -nodes -key "/etc/nginx/ssl/curator.key" -sha256 -days 3650 -out "/etc/nginx/ssl/curator.pem" -subj "/C=US/ST=California/L=San Francisco/O=Curator/OU=Curator/CN=nginx"
    openssl req -new -key "/etc/nginx/ssl/curator.key" -out "/etc/nginx/ssl/curator.csr" -subj "/CN=curator/O=curator/C=UK"
    openssl x509 -req -days 365 -in "/etc/nginx/ssl/curator.csr" -signkey "/etc/nginx/ssl/curator.key" -out "/etc/nginx/ssl/curator.crt"
    chmod 644 "/etc/nginx/ssl/curator.key"
fi

chmod 777 "/etc/nginx/ssl/curator.key"
chmod 777 "/etc/nginx/ssl/curator.crt"
chmod 777 "/etc/nginx/ssl/curator.csr"
chmod 777 "/etc/nginx/ssl/curator.pem"

# cron job to renew the certificate
#if [ ! -f /etc/cron.d/certificate-renewal ]; then
#    echo "30 2 * * * root /usr/local/bin/certificate-renewal.sh" > /etc/cron.d/certificate-renewal
#    chmod +x /usr/local/bin/certificate-renewal.sh
#fi

# cron to restart nginx server evers 6 hours
(crontab -l ; echo "0 0 */4 * * root nginx -s reload") | crontab -


# Start crond in background
crond -l 2 -b

#rclone mount atem_remote:music/raw/audio/ /var/www/html/storage/app/public/uploads/audio

# Start nginx in foreground
echo "Starting nginx... nginx will restart every 6 hours"
nginx
