<form name="paysubmit" action="/user/code/yft/pay" method="post">
    <input name="subject" type="hidden" value="Balance recharge" size="35"/>
    <input name="total_fee" type="hidden" value="{$price}" size="35"/>
</form>
<script>
    document.forms['paysubmit'].submit();
</script>