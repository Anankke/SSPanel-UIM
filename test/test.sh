#!/bin/bash

while read ROUTE
do
    http_code=$(curl -o /dev/null -s -m 10 --connect-timeout 5 -w %{http_code} "http://sspanel.test:23480$ROUTE")
    echo '--------------------------------------------------'
    echo "$ROUTE - http code: $http_code"
done < ./test/route.list

echo '--------------------------------------------------'