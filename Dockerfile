FROM indexyz/php
LABEL maintainer="Indexyz <indexyz@protonmail.com>"

COPY . /var/www
WORKDIR /var/www

RUN cp config/.config.example.php "config/.config.php" && \
    cp config/appprofile.example.php config/appprofile.php && \
    chmod -R 755 storage && \
    chmod -R 777 /var/www/storage/framework/smarty/compile/ && \
    mv db/migrations/20000101000000_init_database.php.new db/migrations/20000101000000_init_database.php && \
    curl -SL https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php && \
    php composer.phar install && \
    php vendor/bin/phinx migrate && \
    php xcat Tool importAllSettings && \
    php xcat Tool detectConfigs && \
    php xcat initQQWry && \
    php xcat ClientDownload && \
    crontab -l | { cat; echo "30 23 * * * php /var/www/xcat SendDiaryMail"; } | crontab - && \
    crontab -l | { cat; echo "0 0 * * * php /var/www/xcat Job DailyJob"; } | crontab - && \
    crontab -l | { cat; echo "*/1 * * * * php /var/www/xcat Job CheckJob"; } | crontab - && \
    crontab -l | { cat; echo "0 */1 * * * php /var/www/xcat Job UserJob"; } | crontab - && \
    { \
        echo '[program:crond]'; \
        echo 'command=cron -f'; \
        echo 'autostart=true'; \
        echo 'autorestart=true'; \
        echo 'killasgroup=true'; \
        echo 'stopasgroup=true'; \
    } | tee /etc/supervisor/crond.conf
