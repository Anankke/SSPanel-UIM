#!/bin/bash

echo "==========================================================="
echo "# Fuction Test"
echo ""
echo "- set permission"

sudo chmod -R 755 *

echo "- import .config.php"

cp test/config.php config/.config.php

echo "-----------------------------------------------------------"
echo "- create admin user"
echo ""
echo "* Email: test@example.com"
echo "* Password: test"
echo "-----------------------------------------------------------"

php xcat createAdmin

echo "-----------------------------------------------------------"
echo "$ php xcat syncusers"
echo "-----------------------------------------------------------"

php xcat syncusers

echo "-----------------------------------------------------------"
echo "$ php xcat initdownload"
echo "-----------------------------------------------------------"

php xcat initdownload

echo "-----------------------------------------------------------"
echo "$ php xcat initQQWry"
echo "-----------------------------------------------------------"

php xcat initQQWry
