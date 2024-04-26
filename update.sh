#!/usr/bin/bash

cat << "EOF"
Usage:
./update.sh dev --> Upgrade to the latest development version
./update.sh release $release_version $db_version --> Upgrade to the release version with the specified database version
./update.sh release-nogit --> Upgrade to the current release version without git(You will need to manually download the latest release version)
EOF

do_update_sspanel_dev(){
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

do_update_sspanel_release(){
    tag=$1
    db_version=$2
    git pull --tags
    git reset --hard $tag
    rm -r storage/framework/smarty/compile/*
    php composer.phar install --no-dev
    php composer.phar selfupdate
    php xcat Update
    php xcat Tool importSetting
    php xcat Migration $db_version
}

do_update_sspanel_release_nogit(){
    rm -r storage/framework/smarty/compile/*
    php composer.phar install --no-dev
    php composer.phar selfupdate
    php xcat Update
    php xcat Tool importSetting
    php xcat Migration latest
}

if [[ $1 == "dev" ]]; then
    do_update_sspanel_dev
    exit 0
fi

if [[ $1 == "release" ]]; then
    if [[ $2 == "" ]]; then
        echo "Error: The release version cannot be empty!"
        exit 1
    fi

    if [[ $3 == "" ]]; then
        echo "Error: The database version cannot be empty!"
        exit 1
    fi

    do_update_sspanel_release $2 $3
    exit 0
fi

if [[ $1 == "release-nogit" ]]; then
    do_update_sspanel_release_nogit
    exit 0
fi
