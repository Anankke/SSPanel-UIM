#!/bin/bash

echo "key=$1"
echo "expire_in=$2"
cookie="uid=1; email=test@example.com; key=$1; expire_in=$2"

while read ROUTE
do
    http_code=$(curl -o /dev/null -H"Cookie: $cookie" -s -m 10 --connect-timeout 5 -w %{http_code} "http://sspanel.test:23480$ROUTE")
    echo '--------------------------------------------------'
    echo "$ROUTE - http code: $http_code"
done < ./test/route.list

echo '--------------------------------------------------'