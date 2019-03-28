
<script type="text/javascript">
function add_chatinline(){
	var hccid="{$config["mylivechat_id"]}";
	var nt=document.createElement("script");
	nt.async=true;
	nt.src="https://mylivechat.com/chatinline.aspx?hccid="+hccid;
	var ct=document.getElementsByTagName("script")[0];
	ct.parentNode.insertBefore(nt,ct);
}
add_chatinline();
</script>
