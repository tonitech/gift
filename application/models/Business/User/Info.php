<?php
class Business_User_Info extends Business_User_Abstract
{
    private static $_instance;
    
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Business_User_Info();
        }
        return self::$_instance;
    }
    
    /**
     * 获得省份的内容
     *
     * @return array
     */
    public function getProvince()
    {
        $sql = "Select * from province";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
    
    /**
     * 获得城市的内容
     *
     * @param $paraProvince 所在省份
     * @return array
     */
    public function getCity($paraProvince)
    {
        $sql = "Select * from city where pcode='$paraProvince'";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
    
    /**
     * 获得区县的内容
     *
     * @param $paraCity 所在城市
     * @return array
     */
    public function getDistrict ($paraCity)
    {
        $sql = "Select * from district where ccode='$paraCity'";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
    
    public function getOccucpationList()
    {
        return $this->_db->select()->from('occupation')->query()->fetchAll();
    }
    
    /**
     * 判断电子邮件地址是否存在
     *
     * @param string $email
     * @return boolean
     */
    public function isEmailAddress($email)
    {
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($email)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function setBaseInfo($userInfo)
    {
        try {
            $user = Business_User_Auth::getInstance()->getUserInfoBySession();
            $where = array('id=?' => $user['result']['id']);
            $rtn = $this->_db->update($this->_config->tablename, $userInfo, $where);
            if ($rtn) {
                Business_User_Auth::getInstance()->refreshUserInfo();
                return array(
                    'errorcode' => 0,
                    'errormsg' => '修改成功'
                );
            } else {
                return array(
                    'errorcode' => -1,
                    'errormsg' => '失败'
                );
            }
        } catch (Exception $e) {
            return array(
                'errorcode' => -2,
                'errormsg' => $e->getMessage()
            );
        }
        return $rtn;
    }
    
    /**
     * 修改密码的时候判断是否为当前密码
     * @param string $oldpwd
     */
    public function checkOldPwd($oldpwd)
    {
        $usertable = Zend_Registry::get('dbtable')->user;
        $userinfo = Business_User_Auth::getInstance()->getUserInfoBySession();
        $username = $userinfo['result'][$usertable->username];
        $result = Utility_Db::getInstance()
            ->conn()
            ->select()
            ->from($usertable->tablename, '*')
            ->where($usertable->username . '=?', $username)
            ->query()
            ->fetch();
        
        if (md5($oldpwd) === $result[$usertable->password]) {
            return array(
                'errorcode' => 0,
                'errormsg' => 'success'
            );
        } else {
            return array(
                'errorcode' => -1,
                'errormsg' => 'fail'
            );
        }
    }
    
    /**
     * 修改密码
     * @param string $pwd1
     */
    public function changePwd($pwd)
    {
        $userinfo = Business_User_Auth::getInstance()->getUserInfoBySession();
        $usertable = Zend_Registry::get('dbtable')->user;
        $bind = array($usertable->password => md5($pwd));
        $where = $usertable->id . "='" . $userinfo['result'][$usertable->id] . "'";
        Utility_Db::getInstance()
            ->conn()
            ->update($usertable->tablename, $bind, $where);
        Business_User_Auth::getInstance()->refreshUserInfo();
    }
}