<?php

/**
 * @author tianying
 * @abstract This class is used to deal with user sign up action.
 * @create 2013-03-09 01:18:09
 */
class Business_User_Signup extends Business_User_Abstract
{

    /**
     * 将注册信息写入数据库
     *
     * @param
     *            array
     *            keys: username|password|email
     */
    public function signupSuccess($info)
    {
        $time = date('Y-m-d H:i:s');
        $userTable = Zend_Registry::get('dbtable')->user;
        $base = array(
            $userTable->ctime => $time,
            $userTable->mtime => $time,
            $userTable->avatar => 'avatar.png'
        );
        $bind = array_merge($base, $info);
        try {
            $rtn = $this->_db->insert($userTable->tablename, $bind);
            if ($rtn) {
                return array(
                    'errorcode' => 0,
                    'errormsg' => 'success'
                );
            } else {
                return array(
                    'errorcode' => -1,
                    'errormsg' => '注册失败'
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
     * 获得验证码
     *
     * @return array 返回一个验证码图片和文件名
     *         keys dir|name
     */
    public function getCaptcha()
    {
        $this->codeSession = new Zend_Session_Namespace('code');
        $captcha = new Zend_Captcha_Image(
            array(
                'font' => 'public/images/faktos.ttf', // 字体文件路径
                'fontsize' => 12, // 字号
                'imgdir' => 'public/images/code/', // 验证码图片存放位置
                'session' => $this->codeSession, // 验证码session值
                'width' => 78, // 图片宽
                'height' => 32, // 图片高
                'wordlen' => 5
            )
        ); // 字母数
        
        $captcha->setDotNoiseLevel(0);
        $captcha->setLineNoiseLevel(0);
        $captcha->setGcFreq(3); // 设置删除生成的旧的验证码图片的随机几率
        $captcha->generate(); // 生成图片
        $login = new Zend_Session_Namespace('code');
        $login->code = $captcha->getWord(); // 将code放入session
        return array(
            "dir" => $captcha->getImgDir(),
            "name" => $captcha->getId()
        );
    }

    /**
     * 判断用户名是否是存在
     *
     * @param string $username            
     * @return boolean
     */
    public function isNotExist($username)
    {
        $userTable = Zend_Registry::get('dbtable')->user;
        $result = $this->_db->select()
            ->from($userTable->tablename)
            ->where($userTable->username . '=?', $username)
            ->query()
            ->fetchAll();
        
        if (count($result) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断验证码是否正确
     *
     * @param string $code            
     * @return boolean
     */
    public function isCorrectCode($code)
    {
        $login = new Zend_Session_Namespace('code');
        if ($code == $login->code) {
            return true;
        } else {
            return false;
        }
    }
}