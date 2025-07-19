#!/bin/bash

set -e

REPO_PATH="julyphant/SSPanel-UIM"

download_with_retry() {
    local url=$1
    local dest=$2
    local retry=5
    local count=0

    echo "Start downloading $url"

    until [ $count -ge $retry ]
    do
        if curl -fsSL "$url" -o "$dest"; then
            echo " - Successfully downloaded $dest"
            return 0
        else
            count=$((count+1))
            echo " - Failed download $dest, retrying ($count/$retry)..."
            sleep 2
        fi
    done
    echo " - Failed download $dest, exiting"
    exit 1
}

# 1. 下载 docker-compose.yml
download_with_retry \
    "https://raw.githubusercontent.com/$REPO_PATH/refs/heads/master/docker-compose.yml" \
    "docker-compose.yml"

# 2. 下载 docker-configs.zip 并解压
CONFIGS_URL=`curl -s https://api.github.com/repos/$REPO_PATH/releases/latest | grep browser_download_url | cut -d '"' -f 4`
download_with_retry \
    "$CONFIGS_URL" \
    "docker-configs.zip"

if unzip -o docker-configs.zip; then
    echo "解压 docker-configs.zip 成功"
    rm docker-configs.zip
else
    echo "解压 docker-configs.zip 失败，请重新运行脚本"
    exit 1
fi

# 3. 用户输入
while true; do
    read -rp "请输入站点域名（如 example.com）: " DOMAIN
    if [[ "$DOMAIN" =~ ^[A-Za-z0-9.-]+\.[A-Za-z]{2,}$ ]]; then
        break
    else
        echo "域名格式不合法，请重新输入！"
    fi
done

read -rp "请输入站点名称: " SSPANEL_APP_NAME
while true; do
    read -rp "请输入初始管理员邮箱: " SSPANEL_ADMIN_EMAIL
    if [[ "$SSPANEL_ADMIN_EMAIL" =~ ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$ ]]; then
        break
    else
        echo "邮箱格式不合法，请重新输入！"
    fi
done

while true; do
    echo "是否启用自动申请 Let's Encrypt 证书？（必须将域名解析到本机且80端口可访问）"
    read -rp "请输入 yes 或 no: " enable_ssl
    if [[ "$enable_ssl" == "yes" ]]; then
        DISABLE_LETSENCRYPT=false
        break
    elif [[ "$enable_ssl" == "no" ]]; then
        DISABLE_LETSENCRYPT=true
        break
    else
        echo "必须输入 yes 或 no，请重新输入！"
    fi
done

# 4. 生成密码和key
SSPANEL_ADMIN_PASSWORD=$(openssl rand -base64 6 | tr -dc 'a-zA-Z0-9' | head -c8)
SSPANEL_KEY=$(openssl rand -hex 16)
SSPANEL_MUKEY=$(openssl rand -hex 16)

# 5. 修改 .env
[ -f .env ] || cp docker-configs/.env.example .env

modify_env() {
    local key=$1
    local value=$2
    if grep -q "^$key=" .env; then
        sed -i "s|^$key=.*|$key=$value|" .env
    else
        echo "$key=$value" >> .env
    fi
}

modify_env "DISABLE_LETSENCRYPT" "$DISABLE_LETSENCRYPT"
modify_env "DOMAIN" "$DOMAIN"
modify_env "SSPANEL_APP_NAME" "$SSPANEL_APP_NAME"
modify_env "SSPANEL_ADMIN_EMAIL" "$SSPANEL_ADMIN_EMAIL"
modify_env "SSPANEL_ADMIN_PASSWORD" "$SSPANEL_ADMIN_PASSWORD"
modify_env "SSPANEL_KEY" "$SSPANEL_KEY"
modify_env "SSPANEL_MUKEY" "$SSPANEL_MUKEY"

# 6. 输出信息
IP=$(hostname -I | awk '{print $1}')

if [ "$DISABLE_LETSENCRYPT" = "false" ]; then
    PROTO="https"
else
    PROTO="http"
fi

echo
echo "======================================"
echo "SSPanel-UIM 配置下载完成"
echo "请通过如下命令启动服务:"
echo "   docker compose up -d"
echo 
echo "访问地址（域名）: ${PROTO}://${DOMAIN}/"
echo "访问地址（本机IP）: ${PROTO}://${IP}/"
echo "管理员邮箱: $SSPANEL_ADMIN_EMAIL"
echo "管理员初始密码: $SSPANEL_ADMIN_PASSWORD"
echo "======================================"
