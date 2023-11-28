{if $public_setting['live_chat'] === 'crisp'}
    <script>
        window.$crisp = [];
        window.CRISP_WEBSITE_ID = "{$public_setting["crisp_id"]}";
        (function () {
            d = document;
            s = d.createElement("script");
            s.src = "https://client.crisp.chat/l.js";
            s.async = 1;
            d.getElementsByTagName("head")[0].appendChild(s);
        })();
        $crisp.push(["safe", true])
        $crisp.push(["set", "user:nickname", "{$user->user_name}"],
            ["set", "user:email", "{$user->email}"],
            ["set", "session:data",
                [[
                    ["user_id", "{$user->id}"],
                    ["user_class", "{$user->class}"],
                    ["reg_email", "{$user->email}"],
                    ["class_expire_time", "{$user->class_expire}"],
                    ["available_traffic", "{$user->unusedTraffic()}"],
                    ["balance", "{$user->money}"]
                ]]
            ]);
    </script>
{/if}

{if $public_setting['live_chat'] === 'livechat'}
    <script>
        window.__lc = window.__lc ||
        {
        };
        window.__lc.license = "{$public_setting['livechat_license']}";
        window.__lc.params = [
            {
                name: '用户编号', value: '{$user->id}'
            },
            {
                name: '用户类别', value: '{$user->class}'
            },
            {
                name: '注册邮箱', value: '{$user->email}'
            },
            {
                name: '等级时间', value: '{$user->class_expire}'
            },
            {
                name: '剩余流量', value: '{$user->unusedTraffic()}'
            },
            {
                name: '账户余额', value: '{$user->money}'
            }
        ];

        (function (n, t, c) {
            function i(n) {
                return e._h ? e._h.apply(null, n) : e._q.push(n)
            }

            let e = {
                _q: [],
                _h: null,
                _v: "2.0",
                on: function () {
                    i(["on", c.call(arguments)])
                },
                once: function () {
                    i(["once", c.call(arguments)])
                },
                off: function () {
                    i(["off", c.call(arguments)])
                },
                get: function () {
                    if (!e._h) throw new Error("[LiveChatWidget] You can't use getters before load.");
                    return i(["get", c.call(arguments)])
                },
                call: function () {
                    i(["call", c.call(arguments)])
                },
                init: function () {
                    let n = t.createElement("script");
                    n.async = !0,
                        n.type = "text/javascript",
                        n.src = "https://cdn.livechatinc.com/tracking.js",
                        t.head.appendChild(n)
                }
            };
            !n.__lc.asyncInit && e.init(),
                n.LiveChatWidget = n.LiveChatWidget || e
        }(window, document, [].slice))
    </script>
{/if}
