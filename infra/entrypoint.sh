#!/bin/sh
set -e

LOCK_FILE="/var/www/sspanel/config/installed.lock"

if [ ! -f "$LOCK_FILE" ]; then
    echo "未检测到 installed.lock，正在初始化数据库..."
    cd /var/www/sspanel

    # 检查环境变量
    if [ -z "$SSPANEL_ADMIN_EMAIL" ] || [ -z "$SSPANEL_ADMIN_PASSWORD" ]; then
        echo "错误：必须设置 SSPANEL_ADMIN_EMAIL 和 SSPANEL_ADMIN_PASSWORD 环境变量"
        exit 1
    fi

    if [ ! -f "config/.config.php" ]; then
        cp config/.config.example.php config/.config.php
    fi

    if [ ! -f "config/appprofile.php" ]; then
        cp config/appprofile.example.php config/appprofile.php
    fi

    # 执行数据库迁移（初始化全新数据库）
    php xcat Migration new

    # 更新到最新数据库版本
    php xcat Migration latest

    # 导入配置项
    php xcat Tool importSetting

    # 创建管理员账户
    php xcat Tool createAdmin "$SSPANEL_ADMIN_EMAIL" "$SSPANEL_ADMIN_PASSWORD"

    touch "$LOCK_FILE"
    echo "数据库初始化完成，已创建 installed.lock"
else
    echo "已存在 installed.lock，跳过数据库初始化。"
fi

# 设置需要写权限的目录
chmod -R 777 /var/www/sspanel/storage
chmod 775 /var/www/sspanel/public/clients

# 确保 storage 子目录存在且可写
mkdir -p /var/www/sspanel/storage/framework/smarty/{cache,compile}
mkdir -p /var/www/sspanel/storage/framework/twig/cache
chmod -R 777 /var/www/sspanel/storage/framework

# 配置文件权限（初次安装）
chmod 664 /var/www/sspanel/config/.config.php
chmod 664 /var/www/sspanel/config/appprofile.php

# 启动主进程
exec "$@"