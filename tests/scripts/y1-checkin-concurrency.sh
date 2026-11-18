#!/usr/bin/env bash
# Acceptance readings for the check-in / invite reward slice.
# Needs a migrated test database in config/.config.php, php, curl and a MariaDB client.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

HOST=127.0.0.1
PORT="${PORT:-18081}"
BASE="http://$HOST:$PORT"
UA="sspanel-y1-acceptance"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-root}"
DB_NAME="${DB_NAME:-sspanel_test}"
SQL_CLIENT="${SQL_CLIENT:-$(command -v mariadb || command -v mysql)}"
MB=1048576
GB=1073741824
TMP="$(mktemp -d)"

if [ -n "${DB_PASSWORD:-}" ]; then
    export MYSQL_PWD="$DB_PASSWORD"
fi

db() {
    "$SQL_CLIENT" -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -N -B "$DB_NAME" -e "$1"
}

setcfg() {
    db "DELETE FROM config WHERE item = '$1'"
    db "INSERT INTO config (item, value, class, is_public, type, \`default\`, mark) VALUES ('$1', '$2', '$3', 1, '$4', '$2', '')"
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

db "DELETE FROM user WHERE email LIKE 'y1-%@example.com'"

setcfg enable_checkin 1 feature bool
setcfg enable_checkin_captcha 0 feature bool
setcfg checkin_min 10 feature int
setcfg checkin_max 10 feature int
setcfg invite_reg_traffic_reward 2 ref int
setcfg invite_reg_money_reward 0 ref int
setcfg traffic_log 0 feature bool
setcfg login_log 0 feature bool
setcfg free_user_reset_day 0 feature int
setcfg free_user_reset_bandwidth 0 feature int
setcfg reg_mode reg_only feature string
setcfg enable_reg_captcha 0 feature bool
setcfg reg_email_verify 0 feature bool
setcfg min_port 10000 feature int
setcfg max_port 60000 feature int
setcfg reg_method aes-256-gcm reg string
setcfg reg_traffic 100 reg int
setcfg reg_daily_report 0 reg bool
setcfg reg_class 0 reg int
setcfg reg_class_time 365 reg int
setcfg reg_ip_limit 0 reg int
setcfg reg_speed_limit 0 reg int

PASSHASH="$(php -r 'echo password_hash("y1-password", PASSWORD_DEFAULT);')"
db "INSERT INTO user (email, user_name, pass, passwd, uuid, api_token, port, transfer_enable, last_check_in_time, is_shadow_banned, class_expire, reg_date) VALUES ('y1-checkin@example.com', 'y1-checkin', '$PASSHASH', '$(uuid)', '$(uuid)', '$(uuid)', 10010, 0, 0, 0, '2027-01-01 00:00:00', NOW())"
CHECKIN_ID="$(db "SELECT id FROM user WHERE email = 'y1-checkin@example.com'")"

PHP_CLI_SERVER_WORKERS=8 php -S "$HOST:$PORT" -t public public/index.php >"$TMP/server.log" 2>&1 &
SERVER_PID=$!

for _ in $(seq 1 50); do
    if curl -s -o /dev/null "$BASE/"; then
        break
    fi
    sleep 0.2
done

HEADERS="$TMP/login.headers"
if [ "$(curl -s -o "$TMP/login.body" -w '%{http_code}' -D "$HEADERS" -A "$UA" -X POST --data-urlencode "email=y1-checkin@example.com" --data-urlencode "password=y1-password" "$BASE/auth/login")" != "200" ]; then
    echo "FAIL: login request"
    cat "$TMP/login.body"
    exit 1
fi

COOKIES="$(grep -i '^set-cookie:' "$HEADERS" | sed 's/^[^:]*: //' | cut -d';' -f1 | sed 's/$/; /' | tr -d '\n' || true)"
check "login set uid cookie" "$(grep -ci '^set-cookie: uid=' "$HEADERS" || true)" "1"

BEFORE="$(db "SELECT transfer_enable FROM user WHERE id = $CHECKIN_ID")"
START="$(date +%s)"

checkin_pids=()

for i in $(seq 1 20); do
    curl -s -A "$UA" -H "Cookie: $COOKIES" -X POST "$BASE/user/checkin" -o "$TMP/checkin.$i" &
    checkin_pids+=("$!")
done

wait "${checkin_pids[@]}" || true

OK_COUNT="$(grep -l '"ret":1' "$TMP"/checkin.* | wc -l || true)"
ALREADY_COUNT="$(grep -l '"ret":0' "$TMP"/checkin.* | wc -l || true)"
AFTER="$(db "SELECT transfer_enable FROM user WHERE id = $CHECKIN_ID")"
LAST="$(db "SELECT last_check_in_time FROM user WHERE id = $CHECKIN_ID")"

SUCCESS_FILE="$(grep -l '"ret":1' "$TMP"/checkin.* | head -1 || true)"
ALREADY_FILE="$(grep -l '"ret":0' "$TMP"/checkin.* | head -1 || true)"

if [ -n "$SUCCESS_FILE" ]; then echo "sample success: $(cat "$SUCCESS_FILE")"; fi
if [ -n "$ALREADY_FILE" ]; then echo "sample already: $(cat "$ALREADY_FILE")"; fi

check "exactly one of 20 concurrent check-ins succeeds" "$OK_COUNT" "1"
check "the other 19 answer already checked in" "$ALREADY_COUNT" "19"
check "transfer_enable grows by exactly one reward" "$((AFTER - BEFORE))" "$((10 * MB))"
if [ "$LAST" -ge "$START" ]; then echo "PASS: last_check_in_time recorded"; else echo "FAIL: last_check_in_time not recorded"; fail=1; fi

db "UPDATE user SET last_check_in_time = UNIX_TIMESTAMP() - 86400 WHERE id = $CHECKIN_ID"
BEFORE_NEXT="$(db "SELECT transfer_enable FROM user WHERE id = $CHECKIN_ID")"
curl -s -A "$UA" -H "Cookie: $COOKIES" -X POST "$BASE/user/checkin" -o "$TMP/checkin.next"
AFTER_NEXT="$(db "SELECT transfer_enable FROM user WHERE id = $CHECKIN_ID")"
check "check-in succeeds again the next day" "$(grep -c '"ret":1' "$TMP/checkin.next" || true)" "1"
check "next day reward is added once" "$((AFTER_NEXT - BEFORE_NEXT))" "$((10 * MB))"

INVITER="$(uuid)"
db "INSERT INTO user (email, user_name, pass, passwd, uuid, api_token, port, transfer_enable, is_shadow_banned, class_expire, reg_date) VALUES ('y1-inviter@example.com', 'y1-inviter', '$PASSHASH', '$(uuid)', '$INVITER', '$(uuid)', 10011, 0, 0, '2027-01-01 00:00:00', NOW())"
INVITER_ID="$(db "SELECT id FROM user WHERE email = 'y1-inviter@example.com'")"
INVITE_CODE="Y1CODE$RANDOM$RANDOM"
db "INSERT INTO user_invite_code (code, user_id) VALUES ('$INVITE_CODE', $INVITER_ID)"

reg_pids=()

for i in 1 2; do
    curl -s -A "$UA" -X POST --data-urlencode "tos=true" --data-urlencode "email=y1-reg$i@example.com" --data-urlencode "name=y1reg$i" --data-urlencode "password=password123" --data-urlencode "confirm_password=password123" --data-urlencode "invite_code=$INVITE_CODE" "$BASE/auth/register" -o "$TMP/reg.$i" &
    reg_pids+=("$!")
done

wait "${reg_pids[@]}" || true

check "both concurrent invite registrations create a user" "$(db "SELECT COUNT(*) FROM user WHERE email IN ('y1-reg1@example.com', 'y1-reg2@example.com')")" "2"
check "the inviter receives both invite traffic rewards" "$(db "SELECT transfer_enable FROM user WHERE id = $INVITER_ID")" "$((2 * 2 * GB))"

for i in 1 2; do
    echo "reg.$i: $(head -c 200 "$TMP/reg.$i")"
done

if [ "$fail" -ne 0 ]; then
    echo "--- server log tail ---"
    tail -30 "$TMP/server.log"
    exit 1
fi

echo "ALL PASS"
