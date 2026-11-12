#!/usr/bin/bash

set -euo pipefail

usage(){
    cat << "EOF"
Usage:
./update.sh master --> Upgrade to the latest master version
./update.sh release $release_version $db_version --> Upgrade to the release version with the specified database version
./update.sh release-nogit --> Upgrade to the current release version without git(You will need to manually download the latest release version)
EOF
}

step=''
started=0
old_rev=''
config_merged=0
migrated=0
tmp_dir=''
composer=()

# Runs on every exit. Before the checkout changes nothing needs undoing. After it
# and before the migration finishes, the code, vendor and .config.php go back to
# the previous revision; the database cannot be rolled back here. Once the
# migration has finished, the new code matches the database, so the remaining
# steps are finished forward instead.
on_exit(){
    local status=$?
    set +e
    [[ -n $tmp_dir ]] && rm -rf "$tmp_dir"
    [[ $status -eq 0 ]] && return

    echo
    echo "Error: update failed at step: $step (exit code $status)."

    if [[ $started -eq 0 ]]; then
        echo "Nothing has been changed. Fix the problem above and run update.sh again."
        exit "$status"
    fi

    if [[ $migrated -eq 1 ]]; then
        echo "Code, dependencies, .config.php and the database are already on the new version."
        echo "Fix the problem above, then finish the update with: php xcat Tool importSetting"
        exit "$status"
    fi

    if [[ -z $old_rev ]]; then
        echo "The files were replaced by hand, so update.sh cannot roll them back. Put the previous"
        echo "release back, then run: ${composer[*]} install --no-dev --optimize-autoloader"
        [[ $config_merged -eq 1 ]] && echo "and restore .config.php with: cp config/.config.php.bak config/.config.php"
        exit "$status"
    fi

    echo "Rolling the code back to $old_rev ..."
    local rolled_back=1
    git reset --hard "$old_rev" || rolled_back=0
    if [[ $config_merged -eq 1 ]]; then
        cp config/.config.php.bak config/.config.php || rolled_back=0
    fi
    rm -rf storage/framework/smarty/compile/*
    "${composer[@]}" install --no-dev --optimize-autoloader || rolled_back=0

    if [[ $rolled_back -eq 1 ]]; then
        echo "Code, dependencies and .config.php are back to $old_rev."
    else
        echo "Rollback did not complete. Restore the previous version by hand:"
        echo "  git reset --hard $old_rev"
        [[ $config_merged -eq 1 ]] && echo "  cp config/.config.php.bak config/.config.php"
        echo "  ${composer[*]} install --no-dev --optimize-autoloader"
    fi

    if [[ $step == migration ]]; then
        if [[ $status -eq 2 ]]; then
            echo "The database has not been changed."
        else
            echo "Migrations up to the db_version shown above have completed and stay in the database; the failing"
            echo "one may have left partial changes. Fix the cause and run update.sh again: it continues with the"
            echo "failed migration, which starts again from its first statement. If that fails because of statements"
            echo "it already ran, or the site does not work meanwhile, restore the database backup taken before the update."
        fi
    else
        echo "The database has not been changed."
    fi
    exit "$status"
}
trap on_exit EXIT

# Composer evaluates the target's own composer.json and composer.lock against the
# running PHP, so a PHP or extension the target needs stops the update before the
# checkout changes.
check_platform(){
    step='checking PHP and extensions'
    if [[ -n ${1:-} ]]; then
        tmp_dir=$(mktemp -d)
        git show "$1:composer.json" > "$tmp_dir/composer.json"
        git show "$1:composer.lock" > "$tmp_dir/composer.lock"
    fi
    if ! "${composer[@]}" check-platform-reqs --lock --no-dev --working-dir="${tmp_dir:-.}"; then
        echo "PHP $(php -r 'echo PHP_VERSION;') does not meet the requirements listed above, or this Composer"
        echo "is too old to check them. Upgrade PHP, install the listed extensions or update Composer first,"
        echo "then run update.sh again."
        return 1
    fi
}

preflight(){
    step='checking composer'
    if [[ -f composer.phar ]]; then
        composer=(php composer.phar)
    elif command -v composer > /dev/null; then
        composer=(composer)
    else
        echo "Neither composer.phar in this directory nor composer on PATH was found."
        echo "Install Composer as described in the installation guide."
        return 1
    fi
    check_platform "${1:-}"
    step='checking config directory'
    if [[ ! -w config || ! -w config/.config.php ]]; then
        echo "config/ and config/.config.php must be writable to back up and update .config.php."
        return 1
    fi
}

checkout(){
    old_rev=$(git rev-parse HEAD)
    started=1
    step='checkout'
    git reset --hard "$1"
}

upgrade(){
    started=1
    step='clearing template cache'
    rm -rf storage/framework/smarty/compile/*
    step='composer install'
    "${composer[@]}" install --no-dev --optimize-autoloader
    step='updating .config.php'
    php xcat Update
    config_merged=1
    step='migration'
    # Exit codes of Migration (0 done, 1 failed, 2 usage, nothing changed) are in its help (php xcat).
    php xcat Migration "$1"
    migrated=1
    step='importing settings'
    php xcat Tool importSetting
    echo "Update completed."
}

case ${1:-} in
    master)
        step='git fetch'
        git fetch --prune --prune-tags origin
        step='resolving the target revision'
        target=$(git rev-parse --verify 'origin/master^{commit}')
        preflight "$target"
        checkout "$target"
        upgrade latest
        ;;
    release)
        if [[ -z ${2:-} ]]; then
            echo "Error: The release version cannot be empty!"
            exit 1
        fi
        if [[ ! ${3:-} =~ ^[0-9]+$ ]]; then
            echo "Error: The database version must be a number!"
            exit 1
        fi
        step='git fetch'
        git fetch --tags origin
        step='resolving the target revision'
        target=$(git rev-parse --verify "refs/tags/$2^{commit}")
        preflight "$target"
        checkout "$target"
        upgrade "$3"
        ;;
    release-nogit)
        preflight
        upgrade latest
        ;;
    *)
        usage
        exit 1
        ;;
esac
