FROM php:8.4-fpm-alpine3.22

# 国内源可加速
# RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories

# 必须的 alpine 包
RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        gmp-dev libzip-dev yaml-dev autoconf gcc g++ make \
    && apk add --no-cache mariadb-client

# 安装PHP扩展
RUN set -eux; \
    docker-php-ext-install -j$(nproc) bcmath gmp mysqli pdo_mysql zip

RUN export MAKEFLAGS="-j$(nproc)"; \
    pecl install redis yaml; \
    docker-php-ext-enable redis yaml

# 清理构建依赖，减小镜像体积
# RUN set -eux; \
#     apk del .build-deps; \
#     rm -rf /tmp/* /var/cache/apk/*

# Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux -o pipefail; curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/sspanel

# 复制代码
COPY . .

RUN composer install --no-dev --optimize-autoloader

COPY infra/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
CMD ["/entrypoint.sh", "php-fpm"]
