#!/bin/bash
set -e

# 启动 MariaDB 服务
service mariadb start

# 等待 MariaDB 就绪
until mariadb-admin ping --silent; do
    echo "Waiting for MariaDB..."
    sleep 2
done

# 额外等待确保稳定
sleep 2

# 首次运行初始化（检查配置文件是否存在）
if [ ! -f /app/config/.config.php ]; then
    echo "Performing first-time setup..."

    # 生成随机数据库密码
    DB_PASS=$(openssl rand -base64 18 | tr -d "=+/" | cut -c1-24)
    ADMIN_EMAIL=${ADMIN_EMAIL:-admin@sspanel.com}
    ADMIN_PASS=${ADMIN_PASS:-admin123}

    # 创建数据库和用户（使用随机密码）
    mysql -e "CREATE DATABASE IF NOT EXISTS sspanel;"
    mysql -e "CREATE USER IF NOT EXISTS 'sspanel'@'localhost' IDENTIFIED BY '${DB_PASS}';"
    mysql -e "GRANT ALL PRIVILEGES ON sspanel.* TO 'sspanel'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"

    # 复制配置文件（如果存在示例文件）
    if [ -f /app/config/.config.example.php ]; then
        cp /app/config/.config.example.php /app/config/.config.php
    else
        echo "Error: /app/config/.config.example.php not found. Please ensure SSPanel config example exists."
        exit 1
    fi

    # 修改配置文件中的数据库密码（注意替换方式）
    # 假设 .config.example.php 中包含 'sspanelpwd' 作为占位符，将其替换为随机密码
    sed -i "s/sspanelpwd/${DB_PASS}/g" /app/config/.config.php

    # 执行数据库迁移（初始化全新数据库）
    php /app/xcat Migration new

    # 更新到最新数据库版本
    php /app/xcat Migration latest

    # 导入配置项
    php /app/xcat Tool importSetting

    # 创建管理员账户（使用非交互式命令）
    php /app/xcat Tool createAdmin "${ADMIN_EMAIL}" "${ADMIN_PASS}"

    # 设置基础权限（兼容挂载卷/缺文件场景）
    if [ -d /app ]; then
        chown -R www-data:www-data /app
        find /app -type d -exec chmod 755 {} \;
        find /app -type f -exec chmod 644 {} \;
    fi

    # 设置需要写权限的目录
    if [ -d /app/storage ]; then
        chmod -R 777 /app/storage
    fi
    if [ -d /app/public/clients ]; then
        chmod 775 /app/public/clients
    fi

    # 确保 storage 子目录存在且可写
    mkdir -p /app/storage/framework/smarty/{cache,compile}
    mkdir -p /app/storage/framework/twig/cache
    chmod -R 777 /app/storage/framework

    # 配置文件权限（初次安装）
    if [ -f /app/config/.config.php ]; then
        chmod 664 /app/config/.config.php
    fi
    if [ -f /app/config/appprofile.php ]; then
        chmod 664 /app/config/appprofile.php
    fi

    # 创建 PHP-FPM 的 socket 目录（确保存在）
    mkdir -p /run/php
    chown www-data:www-data /run/php

    echo "Setup completed. Admin: ${ADMIN_EMAIL} / ${ADMIN_PASS} ,dbpassword:${DB_PASS}"
fi

# 启动 Supervisor（管理 Nginx、PHP-FPM、Redis、Cron）
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf