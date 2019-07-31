#!/bin/bash

echo ""
echo "key=$1"
echo "expire_in=$2"
echo '--------------------------------------------------'

cookie="uid=1; email=test@example.com; key=$1; expire_in=$2"

num=0
x=1
success=0

while read ROUTE
do
    http_code=$(curl -o /dev/null -H"Cookie: $cookie" -s -m 10 --connect-timeout 5 -w %{http_code} "http://sspanel.test:23480$ROUTE")
    num=$[num+x]
    [[ "$http_code" = "200" ]] && success=$[success+x]
    echo "$ROUTE - http code: $http_code"
done < ./test/route.list

echo '--------------------------------------------------'
echo "Success route: $success / $num"
