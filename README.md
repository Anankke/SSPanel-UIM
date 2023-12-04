<img src="public/images/uim-logo-round_192x192.png" alt="logo" width="150" height="150" align="left" />

<h1>SSPanel UIM</h1>

> Across the Great Wall we can reach every corner in the world

<br/>

[![License](https://img.shields.io/github/license/Anankke/SSPanel-Uim?style=flat-square)](https://github.com/Anankke/SSPanel-Uim/blob/dev/LICENSE)
![GitHub repo size](https://img.shields.io/github/repo-size/anankke/sspanel-uim?style=flat-square&color=328657)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Anankke/SSPanel-Uim?style=flat-square)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/Anankke/SSPanel-Uim/lint.yml?branch=dev&label=lint&style=flat-square)
![Sonar Quality Gate](https://img.shields.io/sonar/quality_gate/sspanel-uim_SSPanel-Uim-Dev/dev?server=https%3A%2F%2Fsonarcloud.io&style=flat-square)
![Sonar Coverage](https://img.shields.io/sonar/coverage/sspanel-uim_SSPanel-Uim-Dev/dev?server=https%3A%2F%2Fsonarcloud.io&style=flat-square)
[![Sonar Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)
[![Sonar Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)
[![Sonar Security Rating](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)

[![Telegram 通知频道](https://img.shields.io/badge/Telegram-通知频道-blue?style=flat-square)](https://t.me/sspanel_uim)
[![Telegram 开发频道](https://img.shields.io/badge/Telegram-开发频道-blue?style=flat-square)](https://t.me/sspanel_uim_dev)
[![Discord](https://img.shields.io/discord/1049692075085549600?color=5865F2&label=Discord&style=flat-square)](https://discord.gg/A7uFKCvf8V)
[![Dev Blog](https://img.shields.io/badge/Dev-Blog-blue?style=flat-square)](https://blog.sspanel.org)

## 简介

SSPanel UIM 是一款专为 Shadowsocks / V2Ray / Trojan / TUIC 协议设计的多用途代理服务销售管理系统。

## 特性

- 集成 支付宝当面付，PayPal，Stripe 等多种支付系统
- 支持多种邮件服务，内置邮件队列功能，无需第三方组件即可使用
- 内置基于 Bootstrap 5 的 tabler 主题，模板引擎支持
- 支持 Shadowsocks 2022，TUIC 等最新代理协议
- 通用订阅接口，一键 json/clash/sip008/sing-box 格式订阅下发
- 自定义节点配置，模块化订阅系统，支持多种客户端专用订阅格式
- 重构的商店系统，支持包括但不限于包年包月，按量计费，接入类型计费等计费模式
- 重构的定时任务系统，一个命令即可自动完成所有定时任务

## 安装

SSPanel UIM 的需要以下程序才能正常的安装和运行：

- Git
- Nginx（必须使用 HTTPS/HTTPS is REQUIRED）
- PHP 8.2+ （强烈推荐开启 OPcache /OPcache is highly recommended）
- MariaDB 10.11+（关闭严格模式，不兼容 MySQL/Disable strict mode, DO NOT USE MySQL）
- Redis 7.0+

我们推荐用户在开始使用之前有一定程度的 PHP 和 Linux 使用知识，能够至少正确识别使用中所出现的问题并在 issue 中提供所需的信息。

对于拒绝阅读文档且拒绝提供任何反馈的，我们建议其使用其他非开源的方案。

## 文档

> 我们安装，我们更新，我们开发

[SSPanel UIM Wiki](https://wiki.sspanel.org)，在这里你可以找到大部分问题的解答。

## 项目

SSPanel-UIM 不单单是一个面板，它还包括了一系列周边项目来帮助你更好的使用它。

你可以在 [SSPanel UIM 项目组](https://github.com/sspanel-uim) 的页面查看由我们的开发者维护的其他项目。

## 支持开发者

### M1Screw

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/O5O850UEH)

<a href="https://www.vultr.com/?ref=8941355-8H">
<img src="https://www.vultr.com/media/logo_onwhite.png" alt="Vultr" width="200" align="left" />
</a>    

<br/>
<br/>

## Sponsor / 赞助商

[![](.github/jetbrains.png)](https://www.jetbrains.com/?from=SSPanel-UIM)

## 协议

[MIT License](blob/dev/LICENSE)
