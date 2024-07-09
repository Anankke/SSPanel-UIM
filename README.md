<img src=".github/next_1000.png" alt="next" width="600"/>

[![X (formerly Twitter)](https://img.shields.io/twitter/url?url=https%3A%2F%2Ftwitter.com%2FSSPanel_NeXT)](https://twitter.com/SSPanel_NeXT)
[![Discord](https://img.shields.io/discord/1049692075085549600?color=5865F2&label=Discord&style=flat-square)](https://discord.gg/A7uFKCvf8V)

## PSA

1. Regarding commit history and source code, we recently noticed an unauthorized redistribution attempt by SSPanel-UIM project, which may draw unnecessary attention to the NeXT Panel project, for the longevity of NeXT Panel and its projects, we have decided we will no longer publish git commit history. The newer release of the NeXT panel will contain a zip file that includes the project's source code.

2. The only official repo of the NeXT panel is [The-NeXT-Project/NeXT-Panel](https://github.com/The-NeXT-Project/NeXT-Panel) on GitHub, we don't distribute our project's code anywhere else and you should NOT download source code archive from 3rd party website/repo because we can't guarantee it's integrity and security.

3. NeXT panel is NOT a continuation of the original SSPanel-UIM project, and we DO NOT approve of their behavior of routinely copying our code base and branding to the SSPanel-UIM repository despite we have repeatedly expressed our concern. We have stopped supporting the old UIM version OTA service, and if you are not using the NeXT panel, you should not open the issue here since the project will undergo heavy refactoring & redesign in the coming months and we will not be able to provide any help regards old version.

##### (^^ Editor's Comment: LOL Bro saying not continuation and gets same seamless commit history even after trying to break it and all other nice stuff and changed the licences on day 1 closed source, typical Asperger behavior, but hey, it's a free world, and I'm not the one who has to live with the consequences of his actions. ^^)

## Pro Edition

NeXT Panel (Pro Edition) is a multipurpose proxy service management system designed for Shadowsocks(2022) / Vmess / Trojan / TUIC protocol, it has redesigned system architecture and replaced many of its PHP-based backends with high-performance Golang-based ones, and significantly improved site response time under heavy load.

The Pro version will use a yearly subscription model, we plan to provide a dedicated license purchase site and existing patron members can access it as well. We will publish further pricing info on our [Discord server](https://discord.gg/A7uFKCvf8V) and [Twitter account](https://twitter.com/SSPanel_NeXT), please stay tuned.

## Feature Comparison(OSS vs Pro)

| Feature                                                                                                                   | OSS Edition | Pro Edition |
|---------------------------------------------------------------------------------------------------------------------------|-------------|-------------|
| Core PHP Backend & Htmx/jQuery Frontend                                                                                   | ✅           | ✅           |
| Golang-based high-performance Node/User/Admin API                                                                         | ❌           | ✅(WiP)           |
| Golang-based high-performance statistical API that can support real-time client-side updates & server events              | ❌           | ✅(WiP)           |
| Access to over the air(OTA) service that provides one-click software update                                               | ❌           | ✅(WiP)         |
| Access to our experimental risk management API that can filter out potential spam/malicious/abusing users                 | ❌           | ✅(WiP)           |
| Access to our prebuilt Docker Image repository that supports frictionless site setup/update/migration experience          | ❌           | ✅           |
| Support for PostgreSQL in addition to the currently supported MariaDB as the main database                                | ❌           | ✅           |
| Easy to use panel initialization wizard, no CLI operation is needed                                                       | ❌           | ✅           |
| Integration with other cluster management system(Ansible/SaltStack), automatically manage your proxy servers in one place | ❌           | ✅(WiP)           |

Note some of the features will not be available on the Pro Edition on day 1, we expect those features will be gradually rolled out in the coming months.

## Ecosystem

- [NeXT Server](https://github.com/The-NeXT-Project/NeXT-Server)
- NeXT Desktop(WiP)
- [NetStatus-API-Go](https://github.com/The-NeXT-Project/NetStatus-API-Go)

## Documentation

[NeXT Panel Docs](https://nextpanel.dev/docs/category/overview)

## Blog

[NeXT Panel Blog](https://nextpanel.dev/blog)

## Support

<a href="https://www.patreon.com/catdev">Patreon (One time or monthly)</a>

<a href="https://www.vultr.com/?ref=8941355-8H">Vultr Ref Link</a>

<a href="https://www.digitalocean.com/?refcode=50f1a3b6244c">DigitalOcean Ref Link</a>

## License

[GPL-3.0 License](blob/dev/LICENSE)

## Star History

[![Star History Chart](https://api.star-history.com/svg?repos=The-NeXT-Project/NeXT-Panel&type=Date)](https://star-history.com/#The-NeXT-Project/NeXT-Panel&Date)
