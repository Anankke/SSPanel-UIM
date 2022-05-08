#!/bin/bash

check()
{
    dir=$(pwd)

    if [[ ! -e "${dir}/vendor/filp/whoops/src/Whoops/Handler/PrettyPageHandler.php" ]];then
        echo -e "\033[31m Please execute [composer install] first. \033[0m"
        exit
    fi

    file="${dir}/vendor/filp/whoops/src/Whoops/Handler/PrettyPageHandler.php"
}

getline()
{
    del_line=$(cat -n ${file} | grep "PHP_AUTH_PW" | awk '{print $1}')
    sed -i "${del_line}d" $file

    line=$(cat -n ${file} | grep "blacklist php provided auth based values" | awk '{print $1}')
    line=$(expr ${line} + 2)
}

backup()
{
    cp -f ${file} ${dir}/vendor/filp/whoops/src/Whoops/Handler/PrettyPageHandler.php.bak
}

run()
{
    list='key baseUrl db_host db_database db_username db_password muKey muKeyList adminApiToken telegram_token telegram_request_token cloudflare_email cloudflare_key cloudflare_name sentry_dsn github_access_token pwdMethod salt'
    for key in $list
    do
        sed -i "${line}i \ \ \ \ \ \ \ \ \$this->blacklist('_ENV', '${key}');" ${file}
    done

    masked_line=$(cat -n ${file} | grep "str_repeat" | awk '{print $1}')
    masked_line=$(expr ${masked_line} + 2)

    sed -i "${masked_line}i \ \ \ \ \ \ \ \ \ \ \ \ \}" ${file}
    sed -i "${masked_line}i \ \ \ \ \ \ \ \ \ \ \ \ \ \ \ \ \$values[\$key] = '*';" ${file}
    sed -i "${masked_line}i \ \ \ \ \ \ \ \ \ \ \ \ \if (is_array(\$superGlobal[\$key])) {" ${file}
}

tip()
{
    echo -e "\033[31m Important environment configuration has been blocked, but please note that even so, it is still very dangerous to open debug mode in the production environment, please close it in time. \033[0m"
}

recover()
{
    check
    cp -f ${dir}/vendor/filp/whoops/src/Whoops/Handler/PrettyPageHandler.php.bak ${file}
    echo -e "\033[32m Whoops has been restored to the original file. \033[0m"
}

main()
{
    check
    getline
    backup
    run
    tip
}

if [[ $1 != "recover" ]];then
    main
else
    recover
fi