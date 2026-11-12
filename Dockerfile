# syntax=docker/dockerfile:1

# vendor/ is plain PHP and identical on every architecture, so Composer runs on the
# build host's native platform instead of under emulation.
FROM --platform=$BUILDPLATFORM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-autoloader --no-scripts --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize

FROM dunglas/frankenphp:1-php8.4-trixie

ARG TARGETARCH
ARG SUPERCRONIC_VERSION=v0.2.51

RUN install-php-extensions bcmath gmp opcache pdo_mysql redis yaml zip \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Checksums are the SHA1SUM values published with the supercronic release.
RUN set -eu; \
    case "$TARGETARCH" in \
        amd64) sha1=1692066e350a88ff4d7d6474fe3c7a6c41ec5e89 ;; \
        arm64) sha1=9d4c2000493215a1898502b2fc2dada3e8ea8c7a ;; \
        *) echo "unsupported architecture: $TARGETARCH" >&2; exit 1 ;; \
    esac; \
    curl -fsSLo /usr/local/bin/supercronic \
        "https://github.com/aptible/supercronic/releases/download/${SUPERCRONIC_VERSION}/supercronic-linux-${TARGETARCH}"; \
    echo "${sha1}  /usr/local/bin/supercronic" | sha1sum -c -; \
    chmod +x /usr/local/bin/supercronic

WORKDIR /app
COPY --from=vendor /app /app

RUN --mount=type=bind,from=composer:2,source=/usr/bin/composer,target=/usr/local/bin/composer \
    composer check-platform-reqs --no-dev

# Site configuration lives in the data volume; the code tree stays root-owned and read-only.
RUN ln -s /data/sspanel/.config.php config/.config.php \
    && ln -s /data/sspanel/appprofile.php config/appprofile.php \
    && mkdir -p /data/sspanel /data/caddy /config/caddy \
    && chown -R www-data:www-data /data /config storage/framework

USER www-data

ENTRYPOINT ["/app/docker/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/app/docker/Caddyfile", "--adapter", "caddyfile"]
