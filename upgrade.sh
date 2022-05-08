#!/bin/bash

check()
{
    dir=$(pwd)
    number_of_files_migrated_before_update=$(ls ${dir}/databases/migrations | wc -l | awk '{print $1}')
    composer_configuration_file_md5_before_update=$(md5sum ${dir}/composer.json | awk '{print $1}')
}

upgrade()
{
    git stash
    git pull origin new-feat:new-feat
    git stash pop
}

compare()
{
    number_of_files_migrated_after_update=$(ls ${dir}/databases/migrations | wc -l | awk '{print $1}')
    after_the_update_composer_config_file_md5=$(md5sum ${dir}/composer.json | awk '{print $1}')

    if [[ ${number_of_files_migrated_before_update} != ${number_of_files_migrated_after_update} ]];then
        vendor/bin/phinx migrate
    fi
    if [[ ${composer_configuration_file_md5_before_update} != ${after_the_update_composer_config_file_md5} ]];then
        if [[ -e "/usr/local/bin/composer" ]];then
            /usr/local/bin/composer update
            exit
        fi
        if [[ -e "${dir}/composer.phar" ]];then
            php composer.phar update
            exit
        fi
    fi
}

main()
{
    check
    upgrade
    compare
}

main
