#!/bin/bash

/opt/apacheds-2.0.0-M19/bin/apacheds start default

sleep 30

ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f /root/data.ldif -w secret
ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f /root/data.ldif -w secret
ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f /root/data.ldif -w secret
ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f /root/data.ldif -w secret

cd /root/src;
composer install;
mv tests/OpenLdapObject/Tests/TestConfiguration.php tests/OpenLdapObject/Tests/TestConfiguration.php.origin;
mv ../TestConfiguration.php tests/OpenLdapObject/Tests/TestConfiguration.php

if [ "$run_bash" == "true" ];
then
    bash;
else
    phpunit 2>&1 > /root/log/out.log;
    OUT=$?
    cat /root/log/out.log;
    if [ $OUT -eq 0 ];
    then
        echo "success" > /root/log/test.result;
    else
        echo "fail" > /root/log/test.result;
    fi;
fi;
mv tests/OpenLdapObject/Tests/TestConfiguration.php.origin tests/OpenLdapObject/Tests/TestConfiguration.php;