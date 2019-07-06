#!/bin/bash

chmod +x ./test/listTpl.sh
cd uim-index-dev

echo "==========================================================="
echo "# Lint & Audit"
echo ""
echo "$ npm audit"
echo ""

npm audit || true
cd ..

echo ""
if [[ ! -n "$(git log -1 --pretty=%B | grep 'skip lint')" ]]; then
    echo "- lint php"
    "-----------------------------------------------------------"
    phplint '**/*.php' '!vendor/**';
    "-----------------------------------------------------------"
    echo "- lint uim-index-dev-vue"
    cd uim-index-dev
    npm run lint;
    cd ..
    echo "-----------------------------------------------------------"
    echo "- lint smarty template"
    ./test/listTpl.sh;
    echo "-----------------------------------------------------------"
fi

cd uim-index-dev

echo "- build uim-index-dev"

npm run build

echo "-----------------------------------------------------------"

cd ..