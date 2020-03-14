<div class="card-inner">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <p class="card-heading">在线充值</p>
            <div class="form-group form-group-label">
                <label class="floating-label" for="amount">金额</label>
                <input class="form-control" id="amount" type="text">
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="h5 margin-top-sm text-black-hint" id="qrarea"></div>
        </div>
    </div>
</div>

<a class="btn btn-flat waves-attach" id="pay" onclick="pay();"><span class="icon">check</span>&nbsp;充值</a>

<script>
    var pid = 0;
    function pay() {
        $("#readytopay").modal();
        $("#readytopay").on('shown.bs.modal', function () {
            $.ajax({
                type: "POST",
                url: "/user/payment/purchase",
                dataType: "json",
                data: {
                    amount: $$getValue('amount')
                },
                success: (data) => {
                	console.log(data)
                	if (data.code === 0) {
                		window.location.href = data.url
                	} else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                	}
                },
                error: (jqXHR) => {
                    //console.log(jqXHR);
                    $("#readytopay").modal('hide');
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `${
                            jqXHR
                            } 发生错误了`;
                }
            })
        });
    }
    function f() {
        $.ajax({
            type: "POST",
            url: "/payment/status",
            dataType: "json",
            data: {
                pid: pid
            },
            success: (data) => {
                if (data.result) {
                    //console.log(data);
                    $("#alipay").modal('hide');
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = '充值成功';
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