# HY2 contract checks

Run with PHP 8.3 without Composer dependencies or a database:

```sh
php tests/Standalone/hysteria2.php
```

Optional first and second arguments write disabled-hopping and enabled-hopping
Xray JSON fixtures respectively, suitable for loading with Xray-core v26.3.27.
The script calls the real admin validator and subscription exporters, replacing
only the database node lookup. It checks integer/string port compatibility,
invalid configuration rejection, salamander validation, TLS pin behavior and
sing-box/Xray port-hopping conversion.

This is not a database integration test or a client connection test. It does not
verify migrations, concurrent report-ID deduplication, live quota accounting,
Linux firewall rules or every supported client's runtime behavior. Those require
the corresponding integration/deployment environments.

Xray JSON export requires verified TLS or `pinnedPeerCertSha256`; an insecure
request without a pin is rejected because the pinned Xray version no longer
accepts `allowInsecure` after its built-in cutoff. Non-Xray clients retain their
own TLS options. Xray-specific native masks cannot be made available in clients
which do not implement them.
