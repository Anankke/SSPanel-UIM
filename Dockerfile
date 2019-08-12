FROM indexyz/php
LABEL maintainer="Indexyz <indexyz@protonmail.com>"

COPY . /app
WORKDIR /app
COPY docker/supervisor /etc/supervisor

RUN apt-get update -y && \
    apt-get install supervisor -y && \
    cp config/.config.example.php config/.config.php && \
    chmod -R 755 storage && \
    chmod -R 777 /app/storage/framework/smarty/compile/ && \
    curl -SL 'https://qqwry.mirror.noc.one/QQWry.Dat?from=sspanel_uim' -o storage/qqwry.dat && \
    curl -SL https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php && \
    php composer.phar install && \
    php xcat initdownload && \
    crontab -l | { cat; echo "30 22 * * * php /app/xcat sendDiaryMail"; } | crontab - && \
    crontab -l | { cat; echo "0 0 * * * php /app/xcat dailyjob"; } | crontab - && \
    crontab -l | { cat; echo "*/1 * * * * php /app/xcat checkjob"; } | crontab - && \
    crontab -l | { cat; echo "*/1 * * * * php /app/xcat syncnode"; } | crontab - && \
    mkdir -p /etc/supervisor/ && \
    echo_supervisord_conf > /etc/supervisord.conf && \
    echo "[include]" >> /etc/supervisord.conf && \
    echo "files = /etc/supervisor/*.conf" >> /etc/supervisord.conf && \
    apt clean autoclean -y && \
    apt autoremove -y && \
    apt-get remove --purge && \
    rm -rf /var/lib/{apt,dpkg,cache,log}/

ENTRYPOINT ["/usr/bin/supervisord -c /etc/supervisord.conf"]