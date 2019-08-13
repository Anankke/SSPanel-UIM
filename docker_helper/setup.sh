#!/usr/bin/env bash
function setupPhp() {
  mkdir /run/php
  touch /var/log/fpm-php.www.log && chmod 777 /var/log/fpm-php.www.log
  cat <<EOT >>/etc/php/7.3/fpm/pool.d/www.conf
catch_workers_output = yes
php_flag[display_errors] = on
php_admin_value[error_log] =  /dev/stderr
php_admin_flag[log_errors] = on
EOT
  cat <<EOT >>/etc/php/7.3/fpm/php.ini
    opcache.enable=1
    opcache.enable_cli=1
EOT
}
function setupNginx() {
  cp /app/config/nginx/default.conf  /etc/nginx/sites-enabled/default
}
function setupNginxDebug() {
  cp /app/config/nginx/dev.conf /etc/nginx/sites-enabled/default
}
function setupCron() {
  # Add crontab file in the cron directory
  cp /app/docker_helper/crontab /etc/cron.d/sspanel
  # Give execution rights on the cron job
  chmod 0644 /etc/cron.d/sspanel
  # Create the log file to be able to run tail
  touch /var/log/cron.log
}
function runProduction() {
  setupPhp
  setupNginx
  setupCron
  echo "launching  cron"
  cron
    echo "launching  php-fpm"
  php-fpm7.3
  echo "launching  nginx"
  nginx -g 'daemon off;'
}
function enablePhpDebug() {

      cat <<EOT >>  /etc/php/7.3/fpm/php.ini
[xdebug]
xdebug.remote_enable=1
xdebug.remote_host=172.17.0.1
EOT
}
function runDebug() {
  setupPhp
  enablePhpDebug
  setupNginxDebug
  setupCron
  echo "launching  cron"
  cron
    echo "launching  php-fpm"
  php-fpm7.3
  echo "launching  nginx"
  nginx -g 'daemon off;'
}

COMMAND=$1
shift
if [ "$COMMAND" == "prod" ]; then
  echo "Run in Production mode "
  runProduction
elif [ "$COMMAND" == "debug" ]; then
      echo "Run in Debug mode "
  runDebug
else
  exec "$COMMAND"
fi

