{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">用户 #{$edit_user->id}</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">用户编辑</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <a id="save_changes" href="#" class="btn btn-primary">
                            <i class="icon ti ti-device-floppy"></i>
                            保存
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">账户信息</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">邮箱</label>
                                <div class="col">
                                    <input id="email" type="email" class="form-control" value="{$edit_user->email}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">用户名</label>
                                <div class="col">
                                    <input id="user_name" type="text" class="form-control"
                                           value="{$edit_user->user_name}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">账户密码</label>
                                <div class="col">
                                    <input id="pass" type="text" class="form-control"
                                           placeholder="若需为此用户重置密码, 填写此栏">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">账户余额</label>
                                <div class="col">
                                    <input id="money" type="number" step="1" class="form-control"
                                           value="{$edit_user->money}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">邀请人</label>
                                <div class="col">
                                    <input id="ref_by" type="text" class="form-control" value="{$edit_user->ref_by}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">SS端口</label>
                                <div class="col">
                                    <input id="port" type="text" class="form-control" value="{$edit_user->port}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">SS加密方式</label>
                                <div class="col">
                                    <select id="method" class="col form-select" value="{$edit_user->method}">
                                        {foreach $ss_methods as $method}
                                            <option value="{$method}" {if $edit_user->method === $method}selected{/if}>
                                                {$method}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">注册IP</label>
                                <div class="col">
                                    <input type="text" class="form-control" value="{$edit_user->reg_ip}" disabled/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">注册日期</label>
                                <div class="col">
                                    <input type="text" class="form-control" value="{$edit_user->reg_date}" disabled/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最后使用时间</label>
                                <div class="col">
                                    <input type="text" class="form-control" value="{$edit_user->last_use_time}" disabled/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最后签到时间</label>
                                <div class="col">
                                    <input type="text" class="form-control" value="{$edit_user->last_check_in_time}" disabled/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">最后登录时间</label>
                                <div class="col">
                                    <input type="text" class="form-control" value="{$edit_user->last_login_time}" disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">使用限制</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">流量限制</label>
                                <div class="col">
                                    <input id="transfer_enable" type="text" class="form-control"
                                           value="{$edit_user->enableTraffic()}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">当期用量</label>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           value="{$edit_user->usedTraffic()}" disabled/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">累计用量</label>
                                <div class="col">
                                    <input type="text" class="form-control"
                                           value="{$edit_user->totalTraffic()}" disabled/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">节点群组</label>
                                <div class="col">
                                    <input id="node_group" type="text" class="form-control"
                                           value="{$edit_user->node_group}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">账户等级</label>
                                <div class="col">
                                    <input id="class" type="text" class="form-control"
                                           value="{$edit_user->class}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">等级过期时间</label>
                                <div class="col">
                                    <input id="class_expire" type="text" class="form-control"
                                           value="{$edit_user->class_expire}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">免费用户流量重置日</label>
                                <div class="col">
                                    <input id="auto_reset_day" type="text" class="form-control"
                                           value="{$edit_user->auto_reset_day}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">重置的免费流量(GB)</label>
                                <div class="col">
                                    <input id="auto_reset_bandwidth" type="text" class="form-control"
                                           value="{$edit_user->auto_reset_bandwidth}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">速度限制 (Mbps)</label>
                                <div class="col">
                                    <input id="node_speedlimit" type="text" class="form-control"
                                           value="{$edit_user->node_speedlimit}">
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <label class="form-label col-4 col-form-label">同時连接 IP 限制</label>
                                <div class="col">
                                    <input id="node_iplimit" type="text" class="form-control"
                                           value="{$edit_user->node_iplimit}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-light">
                            <h3 class="card-title">其他设置</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3 row">
                                <label class="form-label col-3 col-form-label">显示语言</label>
                                <div class="col">
                                    <select id="locale" class="col form-select" value="{$edit_user->locale}">
                                        {foreach $locales as $locale}
                                        <option value="{$locale}" {if $edit_user->locale === $locale}selected{/if}>
                                            {$locale}
                                        </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <span class="col">管理员</span>
                                <span class="col-auto">
                                    <label class="form-check form-check-single form-switch">
                                        <input id="is_admin" class="form-check-input" type="checkbox"
                                               {if $edit_user->is_admin}checked="" {/if}>
                                    </label>
                                </span>
                            </div>
                            <div class="form-group mb-3 row">
                                <span class="col">两步认证</span>
                                <span class="col-auto">
                                    <label class="form-check form-check-single form-switch">
                                        <input id="ga_enable" class="form-check-input" type="checkbox"
                                               {if $edit_user->ga_enable}checked="" {/if}>
                                    </label>
                                </span>
                            </div>
                            <div class="form-group mb-3 row">
                                <span class="col">账户异常状态（Shadow Banned）</span>
                                <span class="col-auto form-check-single form-switch">
                                    <input id="is_shadow_banned" class="form-check-input" type="checkbox"
                                           {if $edit_user->is_shadow_banned}checked=""{/if}>
                                </span>
                            </div>
                            <div class="form-group mb-3 row">
                                <span class="col">封禁用户</span>
                                <span class="col-auto">
                                    <label class="form-check form-check-single form-switch">
                                        <input id="is_banned" class="form-check-input" type="checkbox"
                                               {if $edit_user->is_banned}checked=""{/if}>
                                    </label>
                                </span>
                            </div>
                            <div class="form-group mb-3 col-12">
                                <span class="form-label col-12 col-form-label">手动封禁理由</span>
                                <span class="col-auto">
                                    <textarea id="banned_reason" class="form-control"
                                              value="{$edit_user->banned_reason}"></textarea>
                                </span>
                            </div>
                            <div class="form-group mb-3 col-12">
                                <label class="form-label col-12 col-form-label">账户备注</label>
                                <div class="col">
                                    <textarea id="remark" class="form-control" value="{$edit_user->remark}"
                                              placeholder="仅管理员可见"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#save_changes").click(function () {
        $.ajax({
            url: '/admin/user/{$edit_user->id}',
            type: 'PUT',
            dataType: "json",
            data: {
                {foreach $update_field as $key}
                {$key}: $('#{$key}').val(),
                {/foreach}
                is_admin: $("#is_admin").is(":checked"),
                ga_enable: $("#ga_enable").is(":checked"),
                is_shadow_banned: $("#is_shadow_banned").is(":checked"),
                is_banned: $("#is_banned").is(":checked"),
            },
            success: function (data) {
                if (data.ret === 1) {
                    $('#success-message').text(data.msg);
                    $('#success-dialog').modal('show');
                    window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    });
</script>

{include file='admin/footer.tpl'}
