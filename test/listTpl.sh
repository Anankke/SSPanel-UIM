#!/bin/sh
find ./ -name "*.tpl" > ./test/tpl_files.txt

while read LINE
do
    echo '--------------------------------------------------'
    echo $LINE
    echo '--------------------------------------------------'
    ./test/smart-lint/smart-lint "$LINE" y
done < ./test/tpl_files.txt
