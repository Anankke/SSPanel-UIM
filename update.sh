#!/usr/bin/bash

[ $(id -u) != "0" ] && { echo "Error: You must be root to run this script!"; exit 1; }

do_upgrade_sspanel(){
    git pull
    git reset --hard origin/dev
    git fetch --prune --prune-tags
    rm -r storage/framework/smarty/compile/*
    php composer.phar update
    php composer.phar selfupdate
    php vendor/bin/phinx migrate
    php xcat Update
    php xcat Tool importAllSettings
}

do_upgrade_sspanel
