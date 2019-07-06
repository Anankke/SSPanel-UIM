#!/bin/bash

echo "==========================================================="
echo "$ composer install"
echo "-----------------------------------------------------------"

composer install

echo "-----------------------------------------------------------"
if [[ ! -n "$(git log -1 --pretty=%B | grep 'skip lint')" ]]; then
    echo "-----------------------------------------------------------"
    echo "- Install phplint"
    npm i -g phplint;
fi

echo "-----------------------------------------------------------"
echo "- Install dependencies for uim-index-dev"
echo "-----------------------------------------------------------"

cd uim-index-dev
npm i
cd ..

if [[ ! -n "$(git log -1 --pretty=%B | grep 'skip lint')" ]]; then
    echo "-----------------------------------------------------------"
    echo "- Install smarty-lint"
    cd test && git clone https://github.com/erunion/smarty-lint && cd .. ;
fi
