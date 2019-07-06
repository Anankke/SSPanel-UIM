#!/bin/bash
echo "┌──────────────────────────────────────────────────────────┐"
echo "│                 SSPanel UIM - Travis Test                │"
echo "└──────────────────────────────────────────────────────────┘"

if [[ ! -n "$(git log -1 --pretty=%B | grep 'skip lint')" ]]; then
    ship_lint="TRUE"
else
    ship_lint="FALSE"
fi

echo "==========================================================="
echo "# Build Enviroment"
echo ""
echo "* Shell: $SHELL"
echo "* User: $(whoami)"
echo "* Build DIR: $TRAVIS_BUILD_DIR"
echo "* Skip Lint: $ship_lint"
echo "==========================================================="

echo "- add www-data user to travis user group"

sudo adduser www-data travis

echo "- add ppa:ondrej/ph to sources.list & Change apt mirror"
echo "-----------------------------------------------------------"

sudo add-apt-repository ppa:ondrej/php -y
curl -sL https://noc.one/ubuntu | bash

echo "-----------------------------------------------------------"
echo "- add NodeJS 10 to sources.list"
echo "-----------------------------------------------------------"

curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -

echo "-----------------------------------------------------------"
echo "- install PHP dependencies & NodeJS"
echo "-----------------------------------------------------------"

sudo apt clean all && sudo apt -qq -y update
sudo apt install -y nodejs php7.3-fpm php7.3-mysql php7.3-curl php7.3-gd php7.3-mbstring php7.3-xml php7.3-xmlrpc php7.3-opcache php7.3-zip php7.3-json php7.3-bz2 php7.3-bcmath nginx dash bash

echo "==========================================================="
echo "# Configure MySQL"
echo ""
echo "* User: root"
echo "* Password: sspanel"
echo "* Import from glzjin_all.sql"
echo "==========================================================="

echo "- create & import sql"

sudo mysql -e "CREATE DATABASE sspanel; use sspanel; source sql/glzjin_all.sql;"

echo "- ser user & password"

sudo mysql -e "use mysql; update user set authentication_string=PASSWORD('sspanel') where User='root'; update user set plugin='mysql_native_password';FLUSH PRIVILEGES;"

echo "-----------------------------------------------------------"

sudo mysql_upgrade -u root -psspanel

echo "- start mysql service"

sudo service mysql restart

echo "==========================================================="
echo "# Configure Nginx"
echo ""

echo "- import sspanel.test.conf"

sudo cp test/travis-ci.conf /etc/nginx/sites-available/sspanel.test.conf
sudo sed -e "s?%TRAVIS_BUILD_DIR%?$TRAVIS_BUILD_DIR?g" --in-place /etc/nginx/sites-available/sspanel.test.conf

echo "- delete default.conf"

sudo rm -rf /etc/nginx/sites-available/default

echo "- add sspanel.test to sites-enabled"

sudo ln -s /etc/nginx/sites-available/sspanel.test.conf /etc/nginx/sites-enabled/

echo "-----------------------------------------------------------"
echo "* Current available site:"

sudo ls /etc/nginx/sites-available/

echo "* Current available site:"

sudo ls /etc/nginx/sites-enabled/

echo "* Nginx Server Conf:"

sudo cat /etc/nginx/nginx.conf

echo "* sspanel.test.conf:"

sudo cat /etc/nginx/sites-enabled/sspanel.test.conf