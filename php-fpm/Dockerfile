
FROM php:8.2.3-fpm-bullseye

MAINTAINER support@cyber-duck.co.uk

ARG WWWGROUP=1000
ARG WWWUSER="atem"
ENV WWWGROUP ${WWWGROUP:-1000}

ENV COMPOSER_MEMORY_LIMIT='-1'
ENV NODE_VERSION=18

RUN apt-get update && \
    apt-get install -y --force-yes --no-install-recommends \
        libmemcached-dev \
        libzip-dev \
        libz-dev \
        libzip-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        openssh-server \
        libmagickwand-dev \
        git \
        cron \
        nano \
        libxml2-dev \
        libreadline-dev \
        libgmp-dev \
        mariadb-client \
        unzip

# Install soap extention
RUN docker-php-ext-install soap

# Install for image manipulation
RUN docker-php-ext-install exif

# Install the PHP pcntl extention
RUN docker-php-ext-install pcntl

# Install the PHP zip extention
RUN docker-php-ext-install zip

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install the PHP pdo_pgsql extention
RUN docker-php-ext-install pdo_pgsql

# Install the PHP bcmath extension
RUN docker-php-ext-install bcmath

# Install the PHP intl extention
RUN docker-php-ext-install intl

# Install the PHP gmp extention
RUN docker-php-ext-install gmp
RUN docker-php-ext-install sockets

# Install the FFI gmp extention
RUN docker-php-ext-install ffi
RUN docker-php-ext-enable ffi

#####################################
# PHPRedis:
#####################################
RUN pecl install redis && docker-php-ext-enable redis

#####################################
# Imagick:
#####################################

RUN pecl install imagick && \
    docker-php-ext-enable imagick

#####################################
# GD:
#####################################

# Install the PHP gd library
RUN docker-php-ext-install gd && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

#####################################
# xDebug:
#####################################

# Install the xdebug extension
RUN pecl install xdebug

# Install the XPROF  extention
RUN pecl install xhprof
RUN apt-get install -y graphviz
#RUN pecl install php8.2-xhprof

# Install the XPROF  extention
#RUN apt-get update && apt-get install php8.2-xhprof
#RUN apt-get install -y graphviz
# RUN docker-php-ext-install graphviz && docker-php-ext-enable graphviz
#####################################
# PHP Memcached:
#####################################

# Install the php memcached extension
RUN pecl install memcached && docker-php-ext-enable memcached

#####################################
# Composer:
#####################################

# Install composer and add its bin to the PATH.
RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/local/bin/composer
# Source the bash
RUN . ~/.bashrc

#####################################
# Laravel Schedule Cron Job:
#####################################

RUN echo "* * * * * www-data /usr/local/bin/php /var/www/artisan schedule:run >> /dev/null 2>&1"  >> /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

# add curator app dependencies
# RUN apt-get install -f php-pear
RUN pecl channel-update pecl.php.net \
    && apt-get install libmpdec-dev -y
RUN pecl install decimal
# RUN echo "extension=decimal.so" > /usr/local/etc/php/conf.d/decimal.ini
RUN git clone https://github.com/edenhill/librdkafka.git \
    && cd librdkafka \
    && ./configure \
    && make \
    && make install \
    && pecl install rdkafka \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# RUN apt-get update -y && apt-get install -y libghc-decimal-dev
# add yarn dependencies
RUN curl -sLS https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /usr/share/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/usr/share/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt jammy-pgdg main" > /etc/apt/sources.list.d/pgdg.list \
    && apt-get update \
    && apt-get install -y yarn

RUN apt-get update
RUN apt-get install -y ffmpeg
RUN python3 -m pip --version || apt-get install -y python3-pip
RUN pip3 install bandcamp-downloader

# RUN apt-get update
# RUN apt-get install -y python2
RUN pip3 install --upgrade spotdl
RUN pip3 install scdl
RUN chmod -R 777 /var/www
RUN mkdir -p /var/www/html/storage
RUN chmod -R 777 /var/www/html/storage

RUN mkdir -p  ~/.config/scdl/

#storj install
RUN curl -L https://github.com/storj/storj/releases/latest/download/uplink_linux_amd64.zip -o uplink_linux_amd64.zip
RUN unzip -o uplink_linux_amd64.zip
RUN install uplink /usr/local/bin/uplink
COPY accessgrant.txt accessgrant.txt
RUN yes n | uplink access import curator accessgrant.txt
#COPY access.json /root/.config/storj/uplink/access.json

#i  Install rclone
RUN curl https://rclone.org/install.sh | bash
COPY rclone.conf /root/.config/rclone/rclone.conf

# install fuse
RUN apt-get update
RUN apt-get install -y fuse
#RUN  modprobe fuse

# Pstackify APM install
#RUN mkdir -p /stackify-php-apm && \
#    cd /stackify-php-apm && \
#    curl -L  https://download.stackify.com/stackify-php-apm/stackify-php-apm-latest.tar.gz -o stackify-php-apm-latest.tar.gz  &&\
#    tar -zxvf stackify-php-apm-latest.tar.gz; rm stackify-php-apm-latest.tar.gz && \
#    ./install.sh
#RUN ln -s /stackify-php-apm/linux_64/stackify-8.0.so /usr/local/lib/php/extensions/no-debug-non-zts-20210902/stackify.so
# RUN ln -s /stackify-php-apm/Stackify.php /usr/local/lib/php/extensions/no-debug-non-zts-20210902/Stackify.php

#RUN echo "[stackify]" >> [php_dir]/php.ini && \
#   echo "extension=stackify.so" >> [php_dir]/php.ini && \
#   echo "stackify.application_name=PHP Application" >>  [php_dir]/php.ini && \
#   echo "stackify.environment_name=Production" >>  [php_dir]/php.ini

# install htop
RUN apt-get update  && apt-get install -y htop
# install vim
RUN apt-get update  && apt-get install -y vim

RUN apt-get install -y aubio-tools libaubio-dev libaubio-doc


ADD ./laravel.ini /usr/local/etc/php/conf.d

#####################################
# Aliases:
#####################################
# docker-compose exec php-fpm dep --> locally installed Deployer binaries
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/vendor/bin/dep "$@"' > /usr/bin/dep
RUN chmod +x /usr/bin/dep
# docker-compose exec php-fpm art --> php artisan
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/artisan "$@"' > /usr/bin/art
RUN chmod +x /usr/bin/art
# docker-compose exec php-fpm migrate --> php artisan migrate
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/artisan migrate "$@"' > /usr/bin/migrate
RUN chmod +x /usr/bin/migrate
# docker-compose exec php-fpm fresh --> php artisan migrate:fresh --seed
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/artisan migrate:fresh --seed' > /usr/bin/fresh
RUN chmod +x /usr/bin/fresh
# docker-compose exec php-fpm t --> run the tests for the project and generate testdox
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/artisan config:clear\n/var/www/vendor/bin/phpunit -d memory_limit=2G --stop-on-error --stop-on-failure --testdox-text=tests/report.txt "$@"' > /usr/bin/t
RUN chmod +x /usr/bin/t
# docker-compose exec php-fpm d --> run the Laravel Dusk browser tests for the project
RUN echo '#!/bin/bash\n/usr/local/bin/php /var/www/artisan config:clear\n/bin/bash\n/usr/local/bin/php /var/www/artisan dusk -d memory_limit=2G --stop-on-error --stop-on-failure --testdox-text=tests/report-dusk.txt "$@"' > /usr/bin/d
RUN chmod +x /usr/bin/d

RUN rm -r /var/lib/apt/lists/*

RUN mkdir -p  "/var/www/html/storage/app/public/uploads/audio"
RUN mkdir -p  "/var/www/html/storage/app/public/uploads/images"

# RUN usermod -u 1000 www-data

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

RUN usermod -u 1000 sail

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html
COPY ./scdl.cfg /var/www/.config/scdl/scdl.cfg
RUN chmod +x /var/www/.config/scdl/scdl.cfg
COPY ./app_deploy.sh /var/www/html/
COPY ./scheduler_deploy.sh /var/www/html/
RUN chmod +x /var/www/html/app_deploy.sh
RUN chmod +x /var/www/html/scheduler_deploy.sh

RUN rm -f /var/www/html/uplink_linux_amd64.zip
RUN rm -f /var/www/html/accessgrant.txt
RUN rm -rf /var/www/html/uplink



# Install playwright
# Update the package lists
RUN apt-get update

# Install dependencies required by Playwright
RUN apt-get install -y curl wget gnupg ca-certificates libgbm-dev

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs

# Install Playwright
RUN npm i playwright

# Update the package lists
RUN apt-get update

# Install the links web browser
RUN apt-get install -y links
RUN apt-get install -y curl wget gnupg ca-certificates
RUN wget -q -O - https://dl.google.com/linux/linux_signing_key.pub | apt-key add -
RUN sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
RUN apt-get update
RUN apt-get install -y google-chrome-stable
RUN apt-get update
RUN apt-get install -y curl wget gnupg ca-certificates
RUN wget -O firefox.tar.bz2 "https://download.mozilla.org/?product=firefox-latest&os=linux64&lang=en-US"
RUN tar xjf firefox.tar.bz2 -C /opt/
RUN ln -s /opt/firefox/firefox /usr/bin/firefox
RUN apt-get install -y fonts-ipafont-gothic fonts-wqy-zenhei fonts-thai-tlwg fonts-kacst fonts-freefont-ttf libxss1

# Set up the entry point (replace script.js with your actual script)
# COPY script.js /app/script.js

RUN apt install tesseract-ocr

COPY ./docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN ln -s /usr/local/bin/docker-entrypoint.sh /
ENTRYPOINT ["docker-entrypoint.sh"]


#RUN "rclone mount atem_remote:curator/raw/audio/ /var/www/html/storage/app/public/uploads/audio --rc --allow-non-empty --allow-other"


EXPOSE 9000
CMD ["php-fpm"]
