<?php
/**
 * @author acer
 * @version 
 */
class Zend_View_Helper_GetUserInfo
{
    public function getUserInfo($id)
    {
        $table = Zend_Registry::get('dbtable')->user;
        return Business_User_Auth::getInstance()->getUserInfoById($id);
    }
}
