#!/bin/bash

cp patches/AwsS3V3Adapter.php vendor/laravel/framework/src/Illuminate/Filesystem/AwsS3V3Adapter.php
cp patches/FilesystemAdapter.php  vendor/laravel/framework/src/Illuminate/Filesystem/FilesystemAdapter.php

cp patches/Filesystem.php vendor/league/flysystem/src/Filesystem.php

