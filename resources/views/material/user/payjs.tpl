<div class="card-inner">
    <p class="card-heading">输入充值金额后，点击下方的图标进行充值</p>
    <div class="form-group form-group-label">
        <label class="floating-label" for="amount">金额</label>
        <input class="form-control" id="amount" type="text">
    </div>
</div>
<div id="qrarea">
    <button class="btn btn-flat waves-attach" id="btnSubmit" name="type" onclick="pay('wechat')">
        <!-- iCon by SFont.Cn -->
        <img src="data:image/svg+xml;base64,CjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDEwMDAgMTAwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwMCAxMDAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPG1ldGFkYXRhPiDnn6Lph4/lm77moIfkuIvovb0gOiBodHRwOi8vd3d3LnNmb250LmNuLyA8L21ldGFkYXRhPjxnPjxwYXRoIGQ9Ik0zMTIuNiwzMTUuN2MtMTkuMSwwLTM4LjMsMTIuNi0zOC4zLDMxLjhjMCwxOSwxOS4yLDMxLjcsMzguMywzMS43YzE5LjEsMCwzMS43LTEyLjgsMzEuNy0zMS44QzM0NC4zLDMyOC4yLDMzMS43LDMxNS43LDMxMi42LDMxNS43TDMxMi42LDMxNS43TDMxMi42LDMxNS43eiBNNDkwLjMsMzc5LjFjMTkuMiwwLDMxLjgtMTIuOCwzMS44LTMxLjdjMC0xOS4xLTEyLjYtMzEuOC0zMS44LTMxLjhjLTE5LDAtMzguMSwxMi42LTM4LjEsMzEuOEM0NTIuMywzNjYuNCw0NzEuNCwzNzkuMSw0OTAuMywzNzkuMUw0OTAuMywzNzkuMUw0OTAuMywzNzkuMXogTTU3Mi45LDUwMGMtMTIuNiwwLTI1LjQsMTIuNi0yNS40LDI1LjNjMCwxMi44LDEyLjgsMjUuNCwyNS40LDI1LjRjMTkuMiwwLDMxLjgtMTIuNiwzMS44LTI1LjRDNjA0LjcsNTEyLjYsNTkyLjIsNTAwLDU3Mi45LDUwMEw1NzIuOSw1MDBMNTcyLjksNTAweiBNNzEyLjcsNTAwYy0xMi42LDAtMjUuMywxMi43LTI1LjMsMjUuNGMwLDEyLjgsMTIuOCwyNS40LDI1LjMsMjUuNGMxOS4xLDAsMzEuOC0xMi42LDMxLjgtMjUuNEM3NDQuNSw1MTIuNiw3MzEuOCw1MDAsNzEyLjcsNTAwTDcxMi43LDUwMEw3MTIuNyw1MDB6IE04MDEuNSwxMEgxOTguNEM5NC40LDEwLDEwLDk0LjQsMTAsMTk4LjR2NjAzLjJDMTAsOTA1LjYsOTQuMyw5OTAsMTk4LjQsOTkwaDYwMy4xYzkyLjcsMCwxNjkuOC02NywxODUuNS0xNTUuMmwyLjktMTUzLjlWMTk4LjRDOTkwLDk0LjQsOTA1LjYsMTAsODAxLjUsMTBMODAxLjUsMTBMODAxLjUsMTB6IE0zOTUuMiw2MzkuOGMtMzEuNywwLTU3LjItNi40LTg4LjktMTIuN2wtODguOCw0NC41bDI1LjQtNzYuNGMtNjMuNi00NC41LTEwMS43LTEwMS44LTEwMS43LTE3MS41YzAtMTIwLjksMTE0LjQtMjE2LDI1NC4xLTIxNmMxMjQuOSwwLDIzNC4zLDc2LjEsMjU2LjMsMTc4LjRjLTguMi0wLjktMTYuNC0xLjUtMjQuNS0xLjVjLTEyMC43LDAtMjE1LjksOTAtMjE1LjksMjAxYzAsMTguNiwyLjksMzYuNCw3LjgsNTMuM0M0MTEsNjM5LjQsNDAzLjEsNjM5LjgsMzk1LjIsNjM5LjhMMzk1LjIsNjM5LjhMMzk1LjIsNjM5Ljh6IE03NjkuNyw3MjguOGwxOS4yLDYzLjVsLTY5LjctMzguM2MtMjUuNCw2LjQtNTAuOSwxMi43LTc2LjIsMTIuN2MtMTIwLjksMC0yMTYtODIuNS0yMTYtMTg0LjNjMC0xMDEuNiw5NS4xLTE4NC4zLDIxNi0xODQuM2MxMTQuMSwwLDIxNS44LDgyLjgsMjE1LjgsMTg0LjNDODU4LjgsNjM5LjgsODIwLjgsNjkwLjUsNzY5LjcsNzI4LjhMNzY5LjcsNzI4LjhMNzY5LjcsNzI4Ljh6IiBzdHlsZT0iZmlsbDojMTFjZDZlIj48L3BhdGg+PC9nPjwvc3ZnPiAg"
             width="64" height="64">
    </button>
</div>
<script>
    var pid = 0;
    var flag = false;
    function pay(type) {
        if (type = 'wechat') {
        }
        var price = parseFloat($$getValue('amount'));
        //console.log("将要使用 " + type + " 充值" + price + "元");
        if (isNaN(price)) {
            $("#readytopay").modal('hide');
            $("#result").modal();
            $$.getElementById('msg').innerHTML = '非法的金额！'
            return;
        }
        $('#readytopay').modal();
        $.ajax({
            url: "/user/payment/purchase",
            data: {
                price,
                type,
            },
            dataType: 'json',
            type: "POST",
            success: (data) => {
                if (data.code == 0) {
                    //console.log(data);
                    $("#readytopay").modal('hide');
                    {
                        pid = data.pid;
                        $$.getElementById('qrarea').innerHTML = '<div class="text-center"><p>使用微信扫描二维码支付.</p><div align="center" id="qrcode" style="padding-top:10px;"></div><p>充值完毕后会自动跳转</p></div>';
                        var qrcode = new QRCode("qrcode", {
                            correctLevel: 3,  //解决超过200字符的二维码生成问题
                            render: "canvas",
                            width: 200,
                            height: 200,
                            text: data.url      //使用encodeURI()函数报会调低至错误
                        });
                        if(flag == false){
                            tid = setTimeout(f, 1000); //循环调用触发setTimeout
                            flag = true;
                        }else{
                            return 0;
                        }
                    }
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    //console.log(data);
                }
            }
        });
    }

    function f() {
        $.ajax({
            type: "POST",
            url: "/payment/status",
            dataType: "json",
            data: {
                pid
            },
            success: (data) => {
                if (data.result) {
                    //console.log(data);
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = '充值成功！';
                    window.setTimeout("location.href=window.location.href", {$config['jump_delay']});
                }
            },
            error: (jqXHR) => {
                //console.log(jqXHR);
            }
        });
        tid = setTimeout(f, 1000); //循环调用触发setTimeout
    }
</script>
