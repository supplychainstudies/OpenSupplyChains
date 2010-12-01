#!/bin/sh

# source configuration
# set defaults otherwise.

# make cache and log directories
mkdir -p /var/cache/sm-ivory/
mkdir -p /var/log/sm-ivory/

# change owner to www-data (apache)
sudo chown -R www-data:www-data /var/cache/sm-ivory/
sudo chown -R www-data:www-data /var/log/sm-ivory/
