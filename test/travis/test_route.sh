#!/bin/bash

echo "-----------------------------------------------------------"
echo "- start php-fpm & nginx"


sudo service php7.3-fpm restart
sudo nginx -c /etc/nginx/nginx.conf

echo "- set permission"

sudo chmod -R 777 $TRAVIS_BUILD_DIR
sudo chown -R www-data:www-data $TRAVIS_BUILD_DIR

echo "-----------------------------------------------------------"
curl -vvv 'http://sspanel.test:23480/index.php'
curl -o /dev/null -s -m 10 --connect-timeout 5 -w %{http_code} 'http://sspanel.test:23480/index.php'
curl -vvv 'http://sspanel.test:23480/paolu.html'
echo "-----------------------------------------------------------"
echo "* Nginx access.log:"
sudo cat /var/log/nginx/access.log
echo ""
echo "* Nginx error.log:"
sudo cat /var/log/nginx/error.log