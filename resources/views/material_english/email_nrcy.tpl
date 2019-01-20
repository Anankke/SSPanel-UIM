<ul>
	<img src="/images/email_nrcy.jpg" height="458" width="468">
	<br />
	{if $config["enable_admin_contact"] == 'true'}
	<p>If you can't find the email, please contact the administrator:</p>
	{if $config["admin_contact1"]!=null}
	<li>{$config["admin_contact1"]}</li>
	{/if}
	{if $config["admin_contact2"]!=null}
	<li>{$config["admin_contact2"]}</li>
	{/if}
	{if $config["admin_contact3"]!=null}
	<li>{$config["admin_contact3"]}</li>
	{/if}
	{/if}
</ul>