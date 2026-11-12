# Docker 部署 | Docker Deployment

镜像 `ghcr.io/anankke/sspanel-uim` 包含面板代码、PHP 8.4 与 FrankenPHP（内置 Caddy，自动签发与续期 HTTPS 证书），以非 root 用户运行，支持 amd64 与 arm64。FrankenPHP 以经典模式运行，每个请求独立执行一次 PHP 脚本，与 php-fpm 相同，不使用常驻 worker 模式。

The image `ghcr.io/anankke/sspanel-uim` contains the panel, PHP 8.4 and FrankenPHP (Caddy with automatic HTTPS). It runs as a non-root user on amd64 and arm64. FrankenPHP runs in classic mode: every request executes the PHP script afresh, as with php-fpm; worker mode is not used.

## 快速开始 | Quick Start

```bash
mkdir sspanel && cd sspanel
curl -fsSLO https://raw.githubusercontent.com/Anankke/SSPanel-UIM/master/docker/compose.yaml
cat > .env <<EOF
SSPANEL_BASE_URL=https://panel.example.com
SSPANEL_DB_PASSWORD=$(openssl rand -hex 24)
EOF
docker compose up -d
docker compose exec web php xcat Tool createAdmin admin@example.com 'your-password'
```

域名需先解析到本机，80 与 443 端口可从公网访问，证书在首次启动时自动申请。首次启动由 `init` 服务建库、运行迁移并导入默认设置，完成后才启动 `web` 与 `cron`。

Point the domain at this host and open ports 80 and 443 first; the certificate is obtained on first start. On first start the `init` service creates the schema, runs migrations and imports default settings; `web` and `cron` start only after it succeeds.

## 服务 | Services

| 服务 Service | 作用 Role |
|---|---|
| `init` | 每次 `docker compose up` 运行一次：等待数据库、运行 `xcat Migration latest` 与 `xcat Tool importSetting` 后退出；任一失败则以非零退出，`web` 与 `cron` 不启动 / Runs once per `docker compose up`: waits for the database, runs `xcat Migration latest` and `xcat Tool importSetting`, then exits; if either fails it exits non-zero and `web` and `cron` are not started |
| `web` | 网站与节点 API，不运行迁移 / Website and node API; never runs migrations |
| `cron` | 每 5 分钟运行 `php xcat Cron`（supercronic） / Runs `php xcat Cron` every 5 minutes (supercronic) |
| `mariadb` | MariaDB 11.8 |
| `redis` | Redis 8，只保存限流计数与临时验证状态，不持久化 / Holds rate limits and short-lived verification state only, not persisted |

支持的部署形态是单机 `docker compose`；多个 `web` 副本同时首次启动不在支持范围内。

The supported deployment is a single-host `docker compose`; several `web` replicas starting for the first time at once is not supported.

## 配置 | Configuration

`.env` 中的变量 / Variables in `.env`:

| 变量 Variable | 默认 Default | 说明 Description |
|---|---|---|
| `SSPANEL_BASE_URL` | 必填 required | 站点地址，`https://` 开头、不以 `/` 结尾 / Site address, starting with `https://`, no trailing slash |
| `SSPANEL_DB_PASSWORD` | 必填 required | 数据库密码，同时用于创建 MariaDB 用户 / Database password, also used to create the MariaDB user |
| `SSPANEL_VERSION` | `latest` | 镜像版本 / Image tag |
| `SSPANEL_IMAGE` | `ghcr.io/anankke/sspanel-uim` | 镜像仓库，可换成镜像站 / Image repository, e.g. a mirror |
| `SSPANEL_HTTP_PORT`、`SSPANEL_HTTPS_PORT` | `80`、`443` | 宿主机端口，可写成 `127.0.0.1:8080` / Host ports, may be `127.0.0.1:8080` |
| `SERVER_NAME` | `SSPANEL_BASE_URL` 的域名 / host of `SSPANEL_BASE_URL` | Caddy 站点地址；`:80` 表示只提供 HTTP / Caddy site address; `:80` serves plain HTTP only |
| `SSPANEL_KEY`、`SSPANEL_MU_KEY` | 首次启动随机生成 / generated on first start | Cookie 密钥与节点通信密钥 / Cookie key and node API key |
| `SSPANEL_DB_HOST`、`SSPANEL_DB_PORT`、`SSPANEL_DB_DATABASE`、`SSPANEL_DB_USERNAME` | `mariadb`、`3306`、`sspanel`、`sspanel` | 外部数据库时修改 / Change for an external database |
| `SSPANEL_REDIS_HOST`、`SSPANEL_REDIS_PORT`、`SSPANEL_REDIS_PASSWORD` | `redis`、`6379`、空 empty | 外部 Redis 时修改 / Change for an external Redis |

每个 `SSPANEL_*` 变量都可以改用 `<变量名>_FILE` 指向一个文件（如 Docker secrets 的 `/run/secrets/db_password`），文件内容即变量值。

Every `SSPANEL_*` variable can instead be given as `<NAME>_FILE` pointing at a file whose content is the value, e.g. a Docker secret at `/run/secrets/db_password`.

其余设置：首次启动在数据卷中生成 `/data/sspanel/.config.php` 与 `/data/sspanel/appprofile.php`，之后不再覆盖。未在文件末尾改写的设置与订阅模板使用当前镜像的默认值，随镜像升级更新。修改方式：

Other settings: the first start creates `/data/sspanel/.config.php` and `/data/sspanel/appprofile.php` in the data volume and never overwrites them. Settings and subscription templates not reassigned at the end of these files take the defaults of the running image and follow its upgrades. To edit:

```bash
docker compose exec web vi /data/sspanel/.config.php   # 例如 $_ENV['timeZone'] = 'Asia/Shanghai';
docker compose restart web cron
```

不要在容器中运行 `php xcat Update`，它会把 `.config.php` 改写成完整的示例文件。

Do not run `php xcat Update` in the container; it rewrites `.config.php` into a full copy of the example file.

使用外部数据库时，删除 `compose.yaml` 中的 `mariadb` 服务与 `init` 的 `depends_on.mariadb`，并设置 `SSPANEL_DB_HOST` 等变量。

For an external database, remove the `mariadb` service and `init`'s `depends_on.mariadb` from `compose.yaml` and set `SSPANEL_DB_HOST` and the related variables.

## 反向代理与 CDN | Behind a Reverse Proxy or CDN

前面已有一层负责证书（Nginx、Cloudflare 等）时，让容器只提供 HTTP，并只信任该代理传来的 `X-Forwarded-For`。默认不信任任何转发头，面板记录的地址是直接连接方的地址。

When another layer (Nginx, Cloudflare, …) terminates TLS, serve plain HTTP and trust `X-Forwarded-For` only from that proxy. By default no forwarded header is trusted and the panel records the address of the direct peer.

`.env`:

```bash
SERVER_NAME=:80
SSPANEL_HTTP_PORT=127.0.0.1:8080   # 代理在本机时 / when the proxy runs on this host
```

`compose.yaml` 中 `web` 增加 / add to `web` in `compose.yaml`:

```yaml
    environment:
      CADDY_GLOBAL_OPTIONS: |
        servers {
          trusted_proxies static 127.0.0.1/32 172.16.0.0/12
          trusted_proxies_strict
          client_ip_headers X-Forwarded-For
        }
```

`trusted_proxies` 填前置代理的地址段：本机 Nginx 写 Docker 网桥网段（如上，同时会信任本机所有 Docker 网桥上的容器），Cloudflare 写其公布的 IP 段（<https://www.cloudflare.com/ips/>），Cloudflare 的 `client_ip_headers` 用 `CF-Connecting-IP`。`trusted_proxies_strict` 让 Caddy 从 `X-Forwarded-For` 的最右端取第一个不受信任的地址，客户端自己伪造的值不会被采用。

List the proxy's addresses in `trusted_proxies`: the Docker bridge range for an Nginx on the same host (as above, which also trusts every container on the host's Docker bridges), or Cloudflare's published ranges (<https://www.cloudflare.com/ips/>) with `client_ip_headers CF-Connecting-IP`. `trusted_proxies_strict` makes Caddy take the right-most untrusted address in `X-Forwarded-For`, so a value forged by the client is ignored.

## 升级 | Upgrade

```bash
docker compose pull
docker compose up -d
```

固定版本时修改 `.env` 中的 `SSPANEL_VERSION` 后执行 `docker compose up -d`。`init` 用新镜像自动运行迁移；迁移失败时 `docker compose up -d` 报错，`web` 与 `cron` 不启动，原因见 `docker compose logs init`。

With a pinned version, change `SSPANEL_VERSION` in `.env` and run `docker compose up -d`. `init` runs the migrations of the new image; if one fails `docker compose up -d` reports it, `web` and `cron` are not started, and `docker compose logs init` shows why.

## 备份与回滚 | Backup and Rollback

迁移运行后不支持换回旧版本镜像：旧代码不保证能使用新的数据库结构。升级前备份：

Going back to an older image after its migrations have run is not supported: older code is not guaranteed to work with the newer schema. Back up before upgrading:

```bash
docker compose exec mariadb sh -c 'mariadb-dump -usspanel -p"$MARIADB_PASSWORD" sspanel' > sspanel-$(date +%F).sql
docker run --rm -v "$(basename "$PWD")_data:/data" -v "$PWD:/backup" alpine tar czf /backup/data-$(date +%F).tgz -C /data .
```

回滚：`docker compose down`，恢复数据库与 `data` 卷的备份，把 `SSPANEL_VERSION` 改回旧版本，`docker compose up -d`。

Rollback: `docker compose down`, restore the database and the `data` volume from the backup, set `SSPANEL_VERSION` back, `docker compose up -d`.

## 数据 | Data

| 位置 Location | 内容 Content |
|---|---|
| 卷 volume `data` → `/data` | `sspanel/.config.php`、`sspanel/appprofile.php`、`caddy/`（证书 certificates） |
| 卷 volume `db` → `/var/lib/mysql` | 数据库 Database |

GeoIP 数据库随镜像发布，镜像升级时更新；容器内运行 `xcat Tool updateGeoIP2` 的结果在容器重建后丢失。客户端下载（`xcat ClientDownload`）请使用 R2 模式。

The GeoIP databases ship with the image and update with it; running `xcat Tool updateGeoIP2` inside a container lasts until the container is recreated. Use R2 mode for client downloads (`xcat ClientDownload`).
