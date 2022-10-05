{if $config['live_chat'] == 'tawk'}
<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var id = "{$config['tawk_id']}";
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/' + id + '/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>
{/if}

{if $config['live_chat'] == 'crisp'}
<script type="text/javascript"> window.$crisp = [];
    window.CRISP_WEBSITE_ID = "{$config["crisp_id"]}";
    (function () {
        d = document;
        s = d.createElement("script");
        s.src = "https://client.crisp.chat/l.js";
        s.async = 1;
        d.getElementsByTagName("head")[0].appendChild(s);
    })();
    $crisp.push(["safe", true])
    $crisp.push(["set","user:nickname", "{$user->user_name}"],
              ["set","user:email","{$user->email}"],
              ["set", "session:data",
                [[
                  ["user_id","{$user->id}"],
                  ["user_class","{$user->class}"],
                  ["reg_email","{$user->email}"],
                  ["last_use_time","{$user->lastSsTime()}"],
                  ["expire_in","{$user->expire_in}"],
                  ["class_expire_time","{$user->class_expire}"],
                  ["available_traffic","{$user->unusedTraffic()}"],
                  ["balance","{$user->money}"]
                ]]
              ]);
</script>
{/if}

{if $config['live_chat'] == 'livechat'}
<script>
{literal}
window.__lc = window.__lc || {};
{/literal}
window.__lc.license = "{$config['livechat_id']}";;
window.__lc.params = [
    { name: '用户编号', value: '{$user->id}' },
    { name: '用户类别', value: '{$user->class}' },
    { name: '注册邮箱', value: '{$user->email}' },
    { name: '上次使用', value: '{$user->lastSsTime()}' },
    { name: '到期时间', value: '{$user->expire_in}' },
    { name: '等级时间', value: '{$user->class_expire}' },
    { name: '剩余流量', value: '{$user->unusedTraffic()}' },
    { name: '账户余额', value: '{$user->money}' }
];
{literal}
(function(n, t, c) {
    function i(n) {
        return e._h ? e._h.apply(null, n) : e._q.push(n)
    }
    var e = {
        _q: [],
        _h: null,
        _v: "2.0",
        on: function() {
            i(["on", c.call(arguments)])
        },
        once: function() {
            i(["once", c.call(arguments)])
        },
        off: function() {
            i(["off", c.call(arguments)])
        },
        get: function() {
            if (!e._h) throw new Error("[LiveChatWidget] You can't use getters before load.");
            return i(["get", c.call(arguments)])
        },
        call: function() {
            i(["call", c.call(arguments)])
        },
        init: function() {
            var n = t.createElement("script");
            n.async = !0,
            n.type = "text/javascript",
            n.src = "https://cdn.livechatinc.com/tracking.js",
            t.head.appendChild(n)
        }
    }; ! n.__lc.asyncInit && e.init(),
    n.LiveChatWidget = n.LiveChatWidget || e
} (window, document, [].slice))
</script>
{/literal}
{/if}

{if $config['live_chat'] == 'mylivechat'}
<script type="text/javascript">
    (() => {
        var hccid = "{$config['mylivechat_id']}";
        var nt = document.createElement("script");
        nt.async = true;
        nt.src = "https://mylivechat.com/chatinline.aspx?hccid=" + hccid;
        var ct = document.getElementsByTagName("script")[0];
        ct.parentNode.insertBefore(nt, ct);
    })();
</script>
{/if}