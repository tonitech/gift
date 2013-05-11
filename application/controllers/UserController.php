<?php
/**
 * @author tianying
 * @abstract This is the controller used to deal with user actions.
 * @create 2013-03-10 22:14:02
 */
class UserController extends Zend_Controller_Action
{
    private $_config;
    
    public function init()
    {
        $this->_config = Zend_Registry::get('config')->usertable;
    }

    public function isloginAction()
    {
        $userLogObj = new Business_User_Loginout();
        if ($userLogObj->isLogin()) {
            $result = array(
                'islogin' => 1,
                'msg' => 'success',
                'result' => $userLogObj->getUsername($userLogObj->getAccountId())
            );
        } else {
            $result = array(
                'islogin' => 0,
                'msg' => 'not login'
            );
        }
        $this->_helper->getHelper('Json')->sendJson($result);
    }
    
    public function signupAction()
    {
        $username = $this->_request->getParam('username');
        $password = $this->_request->getParam('password');
        $email = $this->_request->getParam('email');
        
        if (!isset($username) || !isset($password) || !isset($email)) {
            $result = array(
            	'errorcode' => -1,
                'errormsg' => 'failed'
            );
        } else {
            $userSignupObj = new Business_User_Signup();
            if (!$userSignupObj->isNotExist($username)) {
                $result = array(
                	'errorcode' => -2,
                    'errormsg' => 'you have signed up.'
                );
            } elseif (!$userSignupObj->isEmailAddress($email)) {
                $result = array(
                	'errorcode' => -3,
                    'errormsg' => 'email address is incorrect.'
                );
            } else {
                $time = date('Y-m-d H:i:s');
                $info = array(
                    $this->_config->ctime => $time,
                    $this->_config->mtime => $time,
                    $this->_config->username => $username,
                    $this->_config->password => md5($password),
                    $this->_config->email => $email,
                );
                $rtn = $userSignupObj->signupSuccess($info);
                if ($rtn === false) {
                    $result = array(
                    	'errorcode' => -4,
                        'errormsg' => 'failed'
                    );
                } else {
                    $result = array(
                    	'errorcode' => 0,
                        'errormsg' => 'success',
                        'result' => $username
                    );
                }
            }
        }
        $this->_helper->getHelper('Json')->sendJson($result);
    }
    
    public function loginAction()
    {
        $username = $this->_request->getParam('username');
        $password = $this->_request->getParam('password');
        if (!isset($username) || !isset($password)) {
            $result = array(
            	'errorcode' => -1,
                'errormsg' => 'failed'
            );
        } else {
            if ($this->_judgeUser($username, $password)) {
                $result = array(
                	'errorcode' => 0,
                    'errormsg' => 'success',
                    'result' => $username
                );
            } else {
                $result = array(
                	'errorcode' => -2,
                    'errormsg' => 'failed'
                );
            }
        }
        $this->_helper->getHelper('Json')->sendJson($result);
    }
    
    private function _judgeUser($username, $password)
    {
        $userLogObj = new Business_User_Loginout();
        $arr = array(
            'username' => $username,
            'password' => md5($password),
        );
        return $userLogObj->isLegal($arr) ? true : false;
    }
    
    public function logoutAction()
    {
        $userLogObj = new Business_User_Loginout();
        if ($userLogObj->logout()) {
            $result = array(
            	'errorcode' => 0,
                'errormsg' => 'success'
            );
            $this->_redirect('/');
        } else {
            $result = array(
            	'errorcode' => -1,
                'errormsg' => 'failed'
            );
        }
        $this->_helper->getHelper('Json')->sendJson($result);
    }
}