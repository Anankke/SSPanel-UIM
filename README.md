<img src="public/images/uim-logo-round_192x192.png" alt="logo" width="192" height="192" align="left" />

<h1>SSPanel UIM</h1>

> Across the Great Wall we can reach every corner in the world

<br/>

[![License](https://img.shields.io/github/license/Anankke/SSPanel-Uim?style=flat-square)](https://github.com/Anankke/SSPanel-Uim/blob/dev/LICENSE)
![GitHub repo size](https://img.shields.io/github/repo-size/anankke/sspanel-uim?style=flat-square&color=328657)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Anankke/SSPanel-Uim?style=flat-square)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/Anankke/SSPanel-Uim/lint.yml?branch=dev&label=lint&style=flat-square)
![Sonar Quality Gate](https://img.shields.io/sonar/quality_gate/sspanel-uim_SSPanel-Uim-Dev/dev?server=https%3A%2F%2Fsonarcloud.io&style=flat-square)
[![Sonar Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)
[![Sonar Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)
[![Sonar Security Rating](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=sspanel-uim_SSPanel-Uim-Dev&metric=coverage)](https://sonarcloud.io/summary/new_code?id=sspanel-uim_SSPanel-Uim-Dev)

[配套Trojan后端](https://github.com/sspanel-uim/TrojanX) | [Telegram 水群](https://t.me/ssunion) | [Telegram 通知频道](https://t.me/sspanel_uim) | [Telegram 开发频道](https://t.me/sspanel_uim_dev) | [Discord Dev Center](https://discord.gg/A7uFKCvf8V) | [Dev Blog](https://blog.sspanel.org)

## 简介

SSPanel UIM 是一款专为 Shadowsocks / V2Ray / Trojan 协议设计的多用途代理服务销售管理系统。

## 特性

- 集成 支付宝当面付，Stripe 银行卡，彩虹易支付 等多种支付系统
- 支持多种邮件服务，内置队列功能，无需第三方组件即可使用
- 内置基于 Bootstrap 5 的 tabler 主题，Smarty 模板引擎支持
- 支持 Shadowsocks 2022，Shadowsocks AEAD，Trojan-Go 等最新代理协议
- 通用订阅接口，一键 json/clash/sip008 格式订阅下发
- 自定义节点配置，模块化订阅系统，支持多种传统订阅模式

## 安装

SSPanel UIM 的需要以下程序才能正常的安装和运行：

- Git
- Nginx（必须使用 HTTPS/HTTPS is REQUIRED）
- PHP 8.1+ （推荐开启 OPcache/OPcache is recommended）
- MariaDB 10.6+（关闭严格模式，不兼容 MySQL/Disable strict mode, DO NOT USE MYSQL）
- Redis 7.0+

我们推荐用户在开始使用之前至少有一定程度的 PHP 和 Linux 使用知识，能够至少正确识别使用中所出现的问题并在 issue 中提供所需的信息。

对于拒绝阅读文档且拒绝提供任何反馈的，我们建议其使用其他非开源的方案。

## 文档

> 我们安装，我们更新，我们开发

[SSPanel UIM Wiki](https://wiki.sspanel.org)，在这里你可以找到大部分问题的解答。

## 项目

SSPanel-UIM 不单单是一个面板，它还包括了一系列周边项目来帮助你更好的使用它。

你可以在 [SSPanel UIM 项目组](https://github.com/sspanel-uim) 的页面查看由我们的开发者维护的其他项目。

## 贡献

[功能请求 & 问题回报](https://github.com/Anankke/SSPanel-Uim/issues/new) | [Fork & Pull Request](https://github.com/Anankke/SSPanel-Uim/fork) | [文档 Repo](https://github.com/sspanel-uim/Wiki) | [贡献者列表](https://wiki.sspanel.org/#/contributors)

SSPanel UIM 欢迎各种贡献，包括但不限于改进，新功能，文档和代码改进，问题和错误报告。

## 支持开发者

### M1Screw

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/O5O850UEH)

## Sponsor / 赞助商

[![](.github/jetbrains.png)](https://www.jetbrains.com/?from=SSPanel-UIM)

## 协议

[MIT License](blob/dev/LICENSE)
