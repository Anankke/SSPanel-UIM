FROM debian:stable
RUN apt update && apt -y install lsb-release apt-transport-https ca-certificates wget nano git zip unzip  \
    &&  wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
     &&   echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |  tee /etc/apt/sources.list.d/php7.3.list \
     && apt update && apt -y install php7.3 php7.3-pgsql php7.3-opcache php7.3-mysql php7.3-gd php7.3-fpm php7.3-curl php7.3-bcmath \
     php7.3-mbstring php7.3-xml php7.3-xmlrpc php7.3-zip php7.3 php7.3-json php7.3-bz2  nginx mycli cron

WORKDIR /app
RUN mkdir -p /app/public\
 && mkdir -p /app/storage \
 && chown -R www-data:www-data /app \
 &&  wget https://getcomposer.org/installer -O  composer.phar \
 &&  wget -O /app/storage/qqwry.dat https://qqwry.mirror.noc.one/QQWry.Dat

COPY ./composer.json ./xcat  /app/
# the following line is optional, only if you are build this image in China
RUN php composer.phar config repo.packagist composer https://packagist.phpcomposer.com
RUN  php composer.phar && php composer.phar  install

VOLUME /app
