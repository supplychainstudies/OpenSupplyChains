#!/bin/bash

LOCAL_DEV_CONFIG="local-dev-config.source"

if [ ! -f "$LOCAL_DEV_CONFIG" ]; then
    echo ""
    echo -e "\tNo local dev configuration."
    echo -e "\t---------------------------"
    echo -e "\tDid you copy tools/local-dev-config.source.example to tools/local-dev-config.source?"
    echo ""
    exit 1
fi

# source configuration
# set defaults otherwise.

. "$LOCAL_DEV_CONFIG"

if [ ! -n "${LOCAL_DEV_APPS}" ]; then
    echo "FATAL: LOCAL_DEV_APPS variable not set. $LOCAL_DEV_APPS"
    exit 1
fi

# make cache and log directories
for app in ${LOCAL_DEV_APPS[@]}
do
echo -e "\n\tMaking cache directory for app \"$app\" @ /var/cache/$app."
mkdir -p "/var/cache/$app"

echo -e "\tMaking log directory for app \"$app\" @ /var/log/$app."
mkdir -p "/var/log/$app"

# change owner to www-data (apache)
echo -e "\tSetting permissions on cache and log directories..."
chown -R www-data:www-data "/var/cache/$app"
chown -R www-data:www-data "/var/log/$app"

done

echo -e "\n\t...done!"
