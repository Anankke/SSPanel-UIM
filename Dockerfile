FROM indexyz/php
LABEL maintainer="Indexyz <indexyz@protonmail.com>"

COPY . /var/www
WORKDIR /var/www

RUN cp config/.config.example.php config/.config.php && \
    chmod -R 755 storage && \
    chmod -R 777 /app/storage/framework/smarty/compile/ && \
    curl -SL https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php && \
    php composer.phar install && \
    php xcat initQQWry && \
    php xcat initdownload && \
    crontab -l | { cat; echo "30 22 * * * php /app/xcat sendDiaryMail"; } | crontab - && \
    crontab -l | { cat; echo "0 0 * * * php /app/xcat dailyjob"; } | crontab - && \
    crontab -l | { cat; echo "*/1 * * * * php /app/xcat checkjob"; } | crontab - && \
    crontab -l | { cat; echo "*/1 * * * * php /app/xcat syncnode"; } | crontab - && \
    { \
        echo '[program:crond]'; \
        echo 'command=cron -f'; \
        echo 'autostart=true'; \
        echo 'autorestart=true'; \
        echo 'killasgroup=true'; \
        echo 'stopasgroup=true'; \
    } | tee /etc/supervisor/crond.conf