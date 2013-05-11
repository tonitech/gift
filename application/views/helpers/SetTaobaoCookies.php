<?php
class Zend_View_Helper_SetTaobaoCookies
{
    public function setTaobaoCookies()
    {
        $appkey = Zend_Registry::get('config')->taobao->appkey;
        /*填写appkey */
        $secret = Zend_Registry::get('config')->taobao->secretkey;
        /*填入Appsecret'*/
        $timestamp = time() . "000";
        $message = $secret . 'app_key' . $appkey 
            . 'timestamp' . $timestamp . $secret;
        $mysign = strtoupper(hash_hmac("md5", $message, $secret));
        setcookie("timestamp", $timestamp);
        setcookie("sign", $mysign);
    }
}