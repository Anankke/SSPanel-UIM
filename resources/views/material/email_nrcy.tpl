{if $config['enable_admin_contact'] == true}
<p>建议检查邮箱垃圾箱，或更换其他邮箱重试。您也可以通过下方展示的联系方式联系管理员</p>
{else}
<p>建议检查邮箱垃圾箱，或更换其他邮箱重试</p>
{/if}

<ul>
    {if $config['enable_admin_contact'] == true}
        {if $config['admin_contact1'] != ''}
            <li>{$config['admin_contact1']}</li>
        {/if}
        {if $config['admin_contact2'] != ''}
            <li>{$config['admin_contact2']}</li>
        {/if}
        {if $config['admin_contact3'] != ''}
            <li>{$config['admin_contact3']}</li>
        {/if}
    {/if}
</ul>