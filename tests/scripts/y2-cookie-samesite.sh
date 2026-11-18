#!/usr/bin/env bash
# Acceptance readings for the session cookie attributes slice.
# Needs a migrated test database in config/.config.php, php, curl and a MariaDB client.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

HOST=127.0.0.1
PORT="${PORT:-18082}"
BASE="http://$HOST:$PORT"
OTHER="https://other-site.example"
UA="sspanel-y2-acceptance"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-root}"
DB_NAME="${DB_NAME:-sspanel_test}"
SQL_CLIENT="${SQL_CLIENT:-$(command -v mariadb || command -v mysql)}"
TMP="$(mktemp -d)"

if [ -n "${DB_PASSWORD:-}" ]; then
    export MYSQL_PWD="$DB_PASSWORD"
fi

db() {
    "$SQL_CLIENT" -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -N -B "$DB_NAME" -e "$1"
}

uuid() {
    php -r 'require "vendor/autoload.php"; echo Ramsey\Uuid\Uuid::uuid4()->toString();'
}

fail=0
check() {
    if [ "$2" = "$3" ]; then
        echo "PASS: $1 ($2)"
    else
        echo "FAIL: $1 (expected $3, got $2)"
        fail=1
    fi
}

cleanup() {
    if [ -n "${SERVER_PID:-}" ]; then
        kill "$SERVER_PID" 2>/dev/null || true
    fi
    rm -rf "$TMP"
}
trap cleanup EXIT

db "DELETE FROM user WHERE email LIKE 'y2-%@example.com'"
db "DELETE FROM config WHERE item IN ('login_log', 'enable_login_captcha')"
db "INSERT INTO config (item, value, class, is_public, type, \`default\`, mark) VALUES ('login_log', '0', 'feature', 1, 'bool', '0', '')"
db "INSERT INTO config (item, value, class, is_public, type, \`default\`, mark) VALUES ('enable_login_captcha', '0', 'feature', 1, 'bool', '0', '')"

PASSHASH="$(php -r 'echo password_hash("y2-password", PASSWORD_DEFAULT);')"
db "INSERT INTO user (email, user_name, pass, passwd, uuid, api_token, port, transfer_enable, is_shadow_banned, class_expire, reg_date) VALUES ('y2-cookie@example.com', 'y2-cookie', '$PASSHASH', '$(uuid)', '$(uuid)', '$(uuid)', 10020, 1099511627776, 0, '2027-01-01 00:00:00', NOW())"

PHP_CLI_SERVER_WORKERS=4 php -S "$HOST:$PORT" -t public public/index.php >"$TMP/server.log" 2>&1 &
SERVER_PID=$!

for _ in $(seq 1 50); do
    if curl -s -o /dev/null "$BASE/"; then
        break
    fi
    sleep 0.2
done

HEADERS="$TMP/login.headers"
curl -s -o "$TMP/login.body" -D "$HEADERS" -A "$UA" -H "Origin: $OTHER" -X POST --data-urlencode "email=y2-cookie@example.com" --data-urlencode "password=y2-password" "$BASE/auth/login" >/dev/null

COOKIE_NAMES="uid email key ip device expire_in"

for name in $COOKIE_NAMES; do
    line="$(grep -i "^set-cookie: $name=" "$HEADERS" | head -1 | sed 's/^[^:]*: //' || true)"

    if [ -z "$line" ]; then
        echo "FAIL: no Set-Cookie for $name"
        fail=1
        continue
    fi

    lower="$(printf '%s' "$line" | tr '[:upper:]' '[:lower:]')"
    for attr in 'samesite=lax' 'secure' 'httponly' 'path=/'; do
        case "$lower" in
            *"$attr"*) echo "PASS: $name carries $attr" ;;
            *) echo "FAIL: $name misses $attr ($line)"; fail=1 ;;
        esac
    done
done

COOKIES="$(grep -i '^set-cookie:' "$HEADERS" | sed 's/^[^:]*: //' | cut -d';' -f1 | sed 's/$/; /' | tr -d '\n' || true)"

check "cross-site full page GET stays logged in" "$(curl -s -o "$TMP/user.html" -w '%{http_code}' -A "$UA" -H "Cookie: $COOKIES" -H "Referer: $OTHER/return" "$BASE/user")" "200"
check "cross-site full page GET renders the user panel" "$(grep -c 'y2-cookie@example.com' "$TMP/user.html" || true)" "1"

check "form POST without the cookies is sent to the login page" "$(curl -s -o /dev/null -w '%{http_code}' -A "$UA" -H "Origin: $OTHER" -X POST "$BASE/user/checkin")" "302"
check "the same cookies still authenticate after the attribute change" "$(curl -s -o /dev/null -w '%{http_code}' -A "$UA" -H "Cookie: $COOKIES" "$BASE/user")" "200"

if [ "$fail" -ne 0 ]; then
    echo "--- login headers ---"
    cat "$HEADERS"
    echo "--- server log tail ---"
    tail -30 "$TMP/server.log"
    exit 1
fi

echo "ALL PASS"
