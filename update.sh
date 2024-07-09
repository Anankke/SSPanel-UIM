#!/usr/bin/bash

cat << "EOF"
Usage:
./update.sh oss --> Update to the latest OSS version(You will need to manually download the latest release version)
EOF

do_update_dev(){
    git pull origin dev
    git reset --hard origin/dev
    git fetch --prune --prune-tags
    rm -r storage/framework/smarty/compile/*
    php composer.phar install --no-dev
    php composer.phar selfupdate
    php xcat Update
    php xcat Tool importSetting
    php xcat Migration latest
}

do_update_oss(){
    rm -r storage/framework/smarty/compile/*
    php composer.phar install --no-dev
    php composer.phar selfupdate
    php xcat Update
    php xcat Tool importSetting
    php xcat Migration latest
}

if [[ $1 == "dev" ]]; then
    do_update_dev
    exit 0
fi

if [[ $1 == "oss" ]]; then
    do_update_oss
    exit 0
fi
