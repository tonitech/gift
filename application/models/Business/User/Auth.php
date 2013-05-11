<?php
/**
 * 用户登录授权
 * @author 王志昂<wangzhiang@qq.com>
 * @create 2013-04-29 18:32:37
 */
class Business_User_Auth extends Business_User_Abstract
{
    /**
     * 获取cookie的生命周期
     * @return 返回截止日期的时间戳
     */
    public function getCookieExpireTime()
    {
        $lastFor = Zend_Registry::get('config')->login->expire->cookie;
        $timestamp = strtotime($lastFor, time());
        return $timestamp;
    }
    
    /**
     * 获取session的生命周期
     * @return 返回截止日期的时间戳
     */
    public function getSessionExpireTime()
    {
        $lastFor = Zend_Registry::get('config')->login->expire->session;
        $timestamp = strtotime($lastFor, time());
        return $timestamp;
    }
    
    /**
     * 获取认证的证书，即cookie和session的内容
     * @param array $user 用户的所有信息
     * @param string $expire 证书生命截至日期
     * @return 返回证书的字符串
     */
    public function generateAuthSignature(array $user, $expire)
    {
        $hash = $this->_generateSign($user, $expire);
        $cookie = $user['id'] . '|' . $expire . '|' . $hash;
        //cookie的组成：用户名id|当前时间|hash
        return $cookie;
    }
    
    /**
     * 使用hash_hmac函数加密生成证书用的哈希
     * 这个函数是加密算法
     * @param array $user 用户的所有信息
     * @param string $timestamp 证书生命截至日期
     * @return 返回hash的字符串
     */
    private function _generateSign(array $user, $timestamp)
    {
        $message = $user['password'] . 'username' . $user['username'] 
        . 'timestamp' . $timestamp . $user['password'];
        $sign = strtoupper(hash_hmac("md5", $message, $user['password']));
        return $sign;
    }
    
    /**
     * 验证证书是否合法
     * @param string $signature 传入证书
     * @return result为用户所有信息
     * array(
            'errorcode' => 0,
            'errormsg' => 'success',
            'result' => $userinfo
        );
     */
    public function validateAuthSignature($signature = '')
    {
        $cookieName = Zend_Registry::get('config')->user->cookie;
        if (empty($signature)) {
            if (!isset($_COOKIE[$cookieName])) {
                return array(
                    'errorcode' => -1,
                    'errormsg' => 'no cookie',
                );
            }
            $signature = $_COOKIE[$cookieName];
        }
        $signatureElements = explode('|', $signature);
        if (count($signatureElements) != 3) {
            return array(
                'errorcode' => -2,
                'errormsg' => 'cookie is incorrect',
            );
        }
        //通过explode'|'得到cookie中的hmac
        list($userid, $expiration, $hmac) = $signatureElements;
        if ($expiration < time()) {
            return array(
                'errorcode' => -3,
                'errormsg' => 'cookie is out of time',
            );
        }
        $userinfo = $this->getUserInfoById($userid);
        if (!$userinfo) {
            return array(
                'errorcode' => -4,
                'errormsg' => 'no user information',
            );
        }
        $hash = $this->_generateSign($userinfo, $expiration);
        if ($hmac != $hash) {
            return array(
                'errorcode' => -5,
                'errormsg' => 'cookie is incorrect',
            );
        }
        return array(
            'errorcode' => 0,
            'errormsg' => 'success',
            'result' => $userinfo
        );
    }
    
    /**
     * 检验用户名和密码是否匹配
     * @param string $username 用户名
     * @param string $password 明文的密码
     * @return result为用户所有信息
     * array(
            'errorcode' => 0,
            'errormsg' => 'success',
            'result' => $rtn
        );
     */
    public function checkLogin($username, $password)
    {
        $rtn = $this->_db
            ->select()
            ->from($this->_config->tablename, '*')
            ->where($this->_config->username . '=?', $username)
            ->where($this->_config->password . '=?', md5($password))
            ->query()
            ->fetch();
        if ($rtn) {
            return array(
                'errorcode' => 0,
                'errormsg' => 'success',
                'result' => $rtn
            );
        } else {
            return array(
                'errorcode' => -1,
                'errormsg' => 'failed',
            );
        }
    }
    
    /**
     * 根据用户id来获取用户的所有信息
     * @param int $userid
     * @return array 用户信息数组
     */
    public function getUserInfoById($userid)
    {
        $rtn = $this->_db
            ->select()
            ->from($this->_config->tablename, '*')
            ->where($this->_config->id . '=?', $userid)
            ->query()
            ->fetch();
        return $rtn;
    }
    
    /**
     * 认证用户授予session
     * session中包含证书和用户信息
     * @param array $user
     */
    public function setUserLogin($user)
    {
        $login = new Zend_Session_Namespace('user');
        $login->userinfo = $user;
        $login->user = $this->generateAuthSignature(
            $user,
            $this->getSessionExpireTime()
        );
    }
    
    /**
     * 判断用户是否登陆
     * 获取session并判断里面的证书
     * @return true|false
     */
    public function isLogin()
    {
        $user = new Zend_Session_Namespace('user');
        $session = $user->user;
        $validator = $this->validateAuthSignature($session);
        if ($validator['errorcode'] == 0) {
            return true;
        } else {
            $this->logout();
            return false;
        }
    }
    
    /**
     * 用户退出
     * 毁掉session
     */
    public function logout()
    {
        $user = new Zend_Session_Namespace('user');
		if ($user->user != '') {
			$user->__unset('user');
			$user->__unset('userinfo');
		}
    }
    
    /**
     * 删除cookie信息
     * 将内容置空，生命周期设置为现在
     */
    public function deleteCookie()
    {
        $cookieName = Zend_Registry::get('config')->user->cookie;
        setcookie($cookieName, '', time());
    }
    
    /**
     * 通过session获得用户的信息
     * @return result为用户所有信息
     * array(
            'errorcode' => 0,
            'errormsg' => 'success',
            'result' => $userinfo
        );
     */
    public function getUserInfoBySession()
    {
        $user = new Zend_Session_Namespace('user');
        $userinfo = $user->userinfo;
        if (is_array($userinfo)) {
            return array(
                'errorcode' => 0,
                'errormsg' => 'success',
                'result' => $userinfo
            );
        } else {
            return array(
                'errorcode' => -1,
                'errormsg' => 'failed',
            );
        }
    }
}