{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">用户编辑 #{$edit_user->id}</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-3 col-sm-12">
            <section class="content-inner margin-top-no">
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">账户信息</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="email">注册邮箱</label>
                                <input class="form-control maxwidth-edit" id="email" type="email"
                                       value="{$edit_user->email}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="remark">用户备注</label>
                                <input class="form-control maxwidth-edit" id="remark" type="text"
                                       value="{$edit_user->remark}">
                            </div>
                            <p class="form-control-guide"><i class="material-icons">info</i>仅对管理员可见</p>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="user_name">用户昵称</label>
                                <input class="form-control maxwidth-edit" id="user_name" type="text"
                                       value="{$edit_user->user_name}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="pass">登录密码</label>
                                <input class="form-control maxwidth-edit" id="pass" type="password"
                                       autocomplete="new-password">
                            </div>
                            <p class="form-control-guide"><i class="material-icons">info</i>不修改请留空</p>
                            
                            <div class="form-group form-group-label">
                                <div class="checkbox switch">
                                    <label for="is_admin">
                                        <input {if $edit_user->is_admin==1}checked{/if} class="access-hide"
                                               id="is_admin" type="checkbox"><span class="switch-toggle"></span>管理员权限
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <div class="checkbox switch">
                                    <label for="enable">
                                        <input {if $edit_user->enable==1}checked{/if} class="access-hide" id="enable"
                                               type="checkbox"><span class="switch-toggle"></span>用户启用
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <div class="checkbox switch">
                                    <label for="ga_enable">
                                        <input {if $edit_user->ga_enable==1}checked{/if} class="access-hide"
                                               id="ga_enable" type="checkbox"><span class="switch-toggle"></span>两步认证
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="money">账户余额</label>
                                <input class="form-control maxwidth-edit" id="money" type="text"
                                       value="{$edit_user->money}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label for="is_multi_user">
                                    <label class="floating-label" for="sort">单端口多用户承载端口</label>
                                    <select id="is_multi_user" class="form-control maxwidth-edit" name="is_multi_user">
                                        <option value="0" {if $edit_user->is_multi_user==0}selected{/if}>非单端口多用户承载端口
                                        </option>
                                        <option value="1" {if $edit_user->is_multi_user==1}selected{/if}>混淆式单端口多用户承载端口
                                        </option>
                                        <option value="2" {if $edit_user->is_multi_user==2}selected{/if}>协议式单端口多用户承载端口
                                        </option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">邀请设置</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="invite_num">可用邀请数量</label>
                                <input class="form-control maxwidth-edit" id="invite_num" type="number"
                                       value="{$edit_user->invite_num}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="ref_by">邀请人ID</label>
                                <input class="form-control maxwidth-edit" id="ref_by" type="text"
                                       value="{$edit_user->ref_by}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-3 col-sm-12">
            <section class="content-inner margin-top-no">
				<div class="card">
					<div class="card-main">
						<div class="card-inner">
                            <h5 style="text-align: center;">封禁管理</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="ban_time">手动封禁时长。单位：分钟</label>
                                <input class="form-control maxwidth-edit" id="ban_time" type="text"
                                       value="0">
                            </div>
                            <p class="form-control-guide"><i class="material-icons">info</i>不封禁请留空</p>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="last_detect_ban_time">最近一次被封禁</label>
                                <input class="form-control maxwidth-edit" id="last_detect_ban_time" type="text"
                                       value="{$edit_user->last_detect_ban_time()}" readonly>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="relieve_time">当前解封时间</label>
                                <input class="form-control maxwidth-edit" id="relieve_time" type="text"
                                       value="{$edit_user->relieve_time()}" readonly>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="detect_ban_number">累计封禁次数</label>
                                <input class="form-control maxwidth-edit" id="detect_ban_number" type="text"
                                       value="{if $edit_user->detect_ban_number() == 0}无记录{else}存在记录 {$edit_user->detect_ban_number()} 条{/if}" readonly>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="all_detect_number">累计违规次数</label>
                                <input class="form-control maxwidth-edit" id="all_detect_number" type="text"
                                       value="{$edit_user->all_detect_number}" readonly>
                            </div>
						</div>
					</div>
				</div>

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">SS / SSR 设置</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="port">连接端口</label>
                                <input class="form-control maxwidth-edit" id="port" type="text"
                                       value="{$edit_user->port}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="passwd">连接密码</label>
                                <input class="form-control maxwidth-edit" id="passwd" type="text"
                                       value="{$edit_user->passwd}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="method">自定义加密</label>
                                <input class="form-control maxwidth-edit" id="method" type="text"
                                       value="{$edit_user->method}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="protocol">自定义协议</label>
                                <input class="form-control maxwidth-edit" id="protocol" type="text"
                                       value="{$edit_user->protocol}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="protocol_param">自定义协议参数</label>
                                <input class="form-control maxwidth-edit" id="protocol_param" type="text"
                                       value="{$edit_user->protocol_param}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="obfs">自定义混淆方式</label>
                                <input class="form-control maxwidth-edit" id="obfs" type="text"
                                       value="{$edit_user->obfs}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="obfs_param">自定义混淆参数</label>
                                <input class="form-control maxwidth-edit" id="obfs_param" type="text"
                                       value="{$edit_user->obfs_param}">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-3 col-sm-12">
            <section class="content-inner margin-top-no">
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">账户设置</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="group">用户群组</label>
                                <input class="form-control maxwidth-edit" id="group" type="number"
                                       value="{$edit_user->node_group}">
                                <p class="form-control-guide"><i class="material-icons">info</i>用户只能访问到组别等于这个数字或 0 的节点</p>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="class">用户级别</label>
                                <input class="form-control maxwidth-edit" id="class" type="number"
                                       value="{$edit_user->class}">
                                <p class="form-control-guide"><i class="material-icons">info</i>用户只能访问到等级小于等于这个数字的节点</p>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="class_expire">用户等级过期时间</label>
                                <input class="form-control maxwidth-edit" id="class_expire" type="text"
                                       value="{$edit_user->class_expire}">
                                <p class="form-control-guide"><i class="material-icons">info</i>不过期就请不要动</p>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="expire_in">用户账户过期时间</label>
                                <input class="form-control maxwidth-edit" id="expire_in" type="text"
                                       value="{$edit_user->expire_in}">
                                <p class="form-control-guide"><i class="material-icons">info</i>不过期就请不要动</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">流量设置</h5>
                            <hr/>

                            <div class="form-group form-group-label">
                                <label class="floating-label" for="transfer_enable">总流量。单位：GB</label>
                                <input class="form-control maxwidth-edit" id="transfer_enable" type="text"
                                       value="{$edit_user->enableTrafficInGB()}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="usedTraffic">已用流量</label>
                                <input class="form-control maxwidth-edit" id="usedTraffic" type="text"
                                       value="{$edit_user->usedTraffic()}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">重置设置</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="auto_reset_day">免费用户流量重置日</label>
                                <input class="form-control maxwidth-edit" id="auto_reset_day" type="number"
                                       value="{$edit_user->auto_reset_day}">
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="auto_reset_bandwidth">重置的免费流量。单位：GB</label>
                                <input class="form-control maxwidth-edit" id="auto_reset_bandwidth" type="number"
                                       value="{$edit_user->auto_reset_bandwidth}">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-3 col-sm-12">
            <section class="content-inner margin-top-no">
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">限制设置</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="node_speedlimit">速率限制。单位：Mbps</label>
                                <input class="form-control maxwidth-edit" id="node_speedlimit" type="text"
                                       value="{$edit_user->node_speedlimit}">
                                <p class="form-control-guide">
                                    <i class="material-icons">info</i>设置为 0 时不限制。此功能需后端支持
                                </p>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="node_connector">连接设备限制</label>
                                <input class="form-control maxwidth-edit" id="node_connector" type="text"
                                       value="{$edit_user->node_connector}">
                                <p class="form-control-guide">
                                    <i class="material-icons">info</i>设置为 0 时不限制。此功能需后端支持
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <h5 style="text-align: center;">访问限制</h5>
                            <hr/>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="node_speedlimit">禁止用户访问的IP，一行一个</label>
                                <textarea class="form-control maxwidth-edit" id="forbidden_ip"
                                          rows="8">{$edit_user->get_forbidden_ip()}</textarea>
                            </div>
                            
                            <div class="form-group form-group-label">
                                <label class="floating-label" for="node_speedlimit">禁止用户访问的端口，一行一个</label>
                                <textarea class="form-control maxwidth-edit" id="forbidden_port"
                                          rows="8">{$edit_user->get_forbidden_port()}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-10 col-md-push-1">
                                        <button id="submit" type="submit"
                                                class="btn btn-block btn-brand waves-attach waves-light">修改
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {include file='dialog.tpl'}
        </div>
    </div>
</main>

{include file='admin/footer.tpl'}

<script>
    //document.getElementById("class_expire").value="{$edit_user->class_expire}";
    window.addEventListener('load', () => {
        function submit() {
            if (document.getElementById('is_admin').checked) {
                var is_admin = 1;
            } else {
                var is_admin = 0;
            }
            if (document.getElementById('enable').checked) {
                var enable = 1;
            } else {
                var enable = 0;
            }
            if (document.getElementById('ga_enable').checked) {
                var ga_enable = 1;
            } else {
                var ga_enable = 0;
            }
            $.ajax({
                type: "PUT",
                url: "/admin/user/{$edit_user->id}",
                dataType: "json",
                data: {
                    email: $$getValue('email'),
                    pass: $$getValue('pass'),
                    auto_reset_day: $$getValue('auto_reset_day'),
                    auto_reset_bandwidth: $$getValue('auto_reset_bandwidth'),
                    is_multi_user: $$getValue('is_multi_user'),
                    port: $$getValue('port'),
                    group: $$getValue('group'),
                    passwd: $$getValue('passwd'),
                    transfer_enable: $$getValue('transfer_enable'),
                    invite_num: $$getValue('invite_num'),
                    node_speedlimit: $$getValue('node_speedlimit'),
                    method: $$getValue('method'),
                    remark: $$getValue('remark'),
                    user_name: $$getValue('user_name'),
                    money: $$getValue('money'),
                    enable,
                    is_admin,
                    ga_enable,
                    ban_time: $$getValue('ban_time'),
                    ref_by: $$getValue('ref_by'),
                    forbidden_ip: $$getValue('forbidden_ip'),
                    forbidden_port: $$getValue('forbidden_port'),
                    class: $$getValue('class'),
                    class_expire: $$getValue('class_expire'),
                    expire_in: $$getValue('expire_in'),
                    node_connector: $$getValue('node_connector'),
                    protocol: $$getValue('protocol'),
                    protocol_param: $$getValue('protocol_param'),
                    obfs: $$getValue('obfs'),
                    obfs_param: $$getValue('obfs_param'),
                },
                success: data => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: jqXHR => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = `发生错误：${
                            jqXHR.status
                            }`;
                }
            });
        }
        $("html").keydown(event => {
            if (event.keyCode == 13) {
                submit();
            }
        });
        $$.getElementById('submit').addEventListener('click', submit);
    })
</script>
