<?php
class Zend_View_Helper_GetTaobaoSecretkey
{
    public function getTaobaoSecretkey()
    {
        return Zend_Registry::get('config')->taobao->secretkey;
    }
}