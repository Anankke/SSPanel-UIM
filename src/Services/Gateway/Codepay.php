<?php
/**
 * Created by PhpStorm.
 * User: tonyzou
 * Date: 2018/9/27
 * Time: 8:16 AM
 */

namespace App\Services\Gateway;

use App\Services\Auth;
use App\Services\Config;
use App\Models\Paylist;


class Codepay extends AbstractPayment
{

    public function isHTTPS()
    {
        define('HTTPS', false);
        if (defined('HTTPS') && HTTPS) {
            return true;
        }
        if (!isset($_SERVER)) {
            return false;
        }
        if (!isset($_SERVER['HTTPS'])) {
            return false;
        }
        if ($_SERVER['HTTPS'] === 1) {  //Apache
            return true;
        }

        if ($_SERVER['HTTPS'] === 'on') { //IIS
            return true;
        }

        if ($_SERVER['SERVER_PORT'] == 443) { //其他
            return true;
        }
        return false;
    }


    public function purchase($request, $response, $args)
    {
        $codepay_id = Config::get('codepay_id');//这里改成码支付ID
        $codepay_key = Config::get('codepay_key'); //这是您的通讯密钥
        $user = Auth::getUser();
        $price = $request->getParam('price');
        $type = $request->getParam('type');

        $pl = new Paylist();
        $pl->userid = $user->id;
        $pl->total = $price;
        $pl->tradeno = time() . 'UID' . $user->id;
        $pl->save();


        $url = ($this->isHTTPS() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $data = array(
            'id' => $codepay_id,//你的码支付ID
            'pay_id' => $pl->tradeno, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
            'type' => $type,//1支付宝支付 2QQ钱包 3微信支付
            'price' => $price,//金额100元
            'param' => '',//自定义参数
            'notify_url' => $url . '/payment/notify',//通知地址
            'return_url' => $url . '/user/code',//跳转地址
        ); //构造需要传递的参数

        ksort($data); //重新排序$data数组
        reset($data); //内部指针指向数组中的第一个元素

        $sign = ''; //初始化需要签名的字符为空
        $urls = ''; //初始化URL参数为空

        foreach ($data as $key => $val) { //遍历需要传递的参数
            if ($val == '' || $key == 'sign') {
                continue;
            } //跳过这些不参数签名
            if ($sign != '') { //后面追加&拼接URL
                $sign .= '&';
                $urls .= '&';
            }
            $sign .= "$key=$val"; //拼接为url参数形式
            $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
        }
        $query = $urls . '&sign=' . md5($sign . $codepay_key); //创建订单所需的参数
        $url = 'https://codepay.fateqq.com/creat_order/?' . $query; //支付页面


        header('Location:' . $url);
    }

    public function notify($request, $response, $args)
    {
        //以下五行无需更改
        ksort($_POST); //排序post参数
        reset($_POST); //内部指针指向数组中的第一个元素
        $codepay_key = Config::get('codepay_key'); //这是您的密钥
        $sign = '';//初始化
        foreach ($_POST as $key => $val) { //遍历POST参数
            if ($val == '' || $key == 'sign') {
                continue;
            } //跳过这些不签名
            if ($sign) {
                $sign .= '&';
            } //第一个字符串签名不加& 其他加&连接起来参数
            $sign .= "$key=$val"; //拼接为url参数形式
        }
        if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
            exit('fail'); //返回失败，等待下次回调
        }

//合法的数据
        //业务处理
        $pay_id = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
        $money = (float)$_POST['money']; //实际付款金额
        $price = (float)$_POST['price']; //订单的原价
        //$param = $_POST['param']; //自定义参数
        $pay_no = $_POST['pay_no']; //流水号
        $this->postPayment($pay_id, '码支付');

        exit('success'); //返回成功 不要删除哦
    }


    public function getPurchaseHTML()
    {
        return '
                        <div class="card-inner">
                        <p class="card-heading">请输入充值金额</p>
                        <form class="codepay" name="codepay" action="/user/code/codepay" method="get">
                            <input class="form-control maxwidth-edit" id="price" name="price" placeholder="输入充值金额后，点击你要付款的应用图标即可" autofocus="autofocus" type="number" min="0.01" max="1000" step="0.01" required="required">
                            <br>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="1" ><img src="/images/alipay.jpg" width="50px" height="50px" /></button>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="2" ><img src="/images/qqpay.jpg" width="50px" height="50px" /></button>
                            <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="3" ><img src="/images/weixin.jpg" width="50px" height="50px" /></button>

                        </form>
                        </div>
';
    }

    public function getReturnHTML($request, $response, $args)
    {
        // TODO: Implement getReturnHTML() method.
    }

    public function getStatus($request, $response, $args)
    {
        // TODO: Implement getStatus() method.
    }
}
