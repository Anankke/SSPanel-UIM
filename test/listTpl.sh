#!/bin/sh
find ./ -name "*.tpl" > ./test/tpl_files.txt

while read LINE
do
    echo '--------------------------------------------------'
    echo $LINE
    echo '--------------------------------------------------'
    ./test/smarty-lint/smarty-lint "$LINE" y
done < ./test/tpl_files.txt
