<script type="text/javascript"> window.$crisp = [];
    window.CRISP_WEBSITE_ID = "{$config["crisp_id"]}";
    (function () {
        d = document;
        s = d.createElement("script");
        s.src = "https://client.crisp.chat/l.js";
        s.async = 1;
        d.getElementsByTagName("head")[0].appendChild(s);
    })();
    $crisp.push(["set","user:nickname", "{$user->user_name}"],
              ["set","user:email","{$user->email}"],
              ["set", "session:data", 
                [[
                  ["ID","{$user->id}"],
                  ["Email","{$user->email}"],
                  ["Class","{$user->class}"],
                  ["vip-time","{$user->class_expire}"],
                  ["last-use","{$user->lastSsTime()}"],
                  ["traffic","{$user->unusedTraffic()}"],
                  ["online-IP","{$user->online_ip_count()}/{$user->node_connector}"],
                  ["money","{$user->money}元"]
                ]]
              ]);
</script>