<div class="card-inner">
                        <p><font color="#137e93" size="4"><i class="icon icon-lg">start</i>epay在线充值</font><p>
                        <p class="card-heading"></p>
                        <form class="epay" name="epay" action="/user/payment/purchase/epay" method="post">
                            <input class="form-control maxwidth-edit" id="price" name="price" placeholder="输入金额，选择以下要付款的渠道" autofocus="autofocus" type="number" min="0.01" max="1000" step="0.01" required="required">
                            <br>
                             <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="alipay" ><img src="/images/alipay.png" width="120px" height="50px" /></button>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="wxpay" ><img src="/images/wechatpay.png" width="120px" height="50px" /></button>
                           <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="qqpay" ><img src="/images/qqpay.png" width="120px" height="50px" /></button>
						   <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value=usdt" ><img src="/images/usdt.png" width="120px" height="50px" /></button>
                           
                        </form>
                        </div>