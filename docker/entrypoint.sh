#!/bin/sh
set -eu

# Caddy serves the host of the site address unless SERVER_NAME says otherwise
# (e.g. ":80" behind a reverse proxy that terminates TLS).
if [ -z "${SERVER_NAME:-}" ] && [ -n "${SSPANEL_BASE_URL:-}" ]; then
    SERVER_NAME=${SSPANEL_BASE_URL#*://}
    SERVER_NAME=${SERVER_NAME%%/*}
    export SERVER_NAME
fi

php /app/docker/bootstrap.php

exec "$@"
