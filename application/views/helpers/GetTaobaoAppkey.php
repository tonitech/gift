<?php
class Zend_View_Helper_GetTaobaoAppkey
{
    public function getTaobaoAppkey()
    {
        return Zend_Registry::get('config')->taobao->appkey;
    }
}