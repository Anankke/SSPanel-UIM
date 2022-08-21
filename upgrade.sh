#!/usr/bin/bash

[ $(id -u) != "0" ] && { echo "Error: You must be root to run this script!"; exit 1; }

do_upgrade_sspanel(){
    git fetch --all
    git reset --hard origin/dev
    git pull
    php composer.phar u
    php vendor/bin/phinx migrate
    php xcat Update
    php xcat Tool importAllSettings
}

do_upgrade_sspanel
