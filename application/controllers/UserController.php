<?php
/**
 * @author tianying
 * @abstract This is the controller used to deal with user actions.
 * @create 2013-03-10 22:14:02
 */
class UserController extends Zend_Controller_Action
{
	/**
	 * 进入控制器的时候查看客户端是否存在cookie
	 * 如果存在验证通过直接分配session
	 * 否则走到客户端指定的控制器
	 * @see Zend_Controller_Action::init()
	 */
	public function init()
	{
		$cookieName = Zend_Registry::get('config')->admin->cookie;
		if (isset($_COOKIE[$cookieName])) {
			$authObj = new Business_Auth();
			$cookieResult = $authObj->validateAuthSignature(
				$_COOKIE[$cookieName]
			);
			if ($cookieResult['errorcode'] == 0) {
				$authObj->setUserLogin($cookieResult['result']);
			}
		}
	}
	
	/**
	 * 用户登陆验证action
	 * 如果用户名密码正确直接分配session
	 * 如果有选择记住我，就分配cookie
	 * 成功之后跳转到homepage管理页
	 */
	public function checkAction()
	{
		$this->view->title = 'Gift登录';
		$username = $this->_request->getParam('username');
		$password = $this->_request->getParam('password');
		$rememberme = $this->_request->getParam('rememberme');
		if (isset($username) && isset($password) && $username != "" &&
		$password != "") {
			$authObj = new Business_Auth();
			$authResult = $authObj->checkLogin($username, $password);
			if ($authResult['errorcode'] == 0) {
				if (isset($rememberme)) {
					$cookieName = Zend_Registry::get('config')->admin->cookie;
					$cookie = $authObj->generateAuthSignature(
						$authResult['result'],
						$authObj->getCookieExpireTime()
					);
					setcookie(
						$cookieName,
						$cookie,
						$authObj->getCookieExpireTime()
					);
				}
				$authObj->setUserLogin($authResult['result']);
				$this->_redirect('/user');
			} else {
				$this->view->errormsg = '<div id="login_error">登录失败！</div>';
				$this->render('login');
			}
		} else {
			$this->view->errormsg = '<div id="login_error">登录失败！</div>';
			$this->render('login');
		}
	}
	
    public function loginAction()
    {
    	$this->view->title = 'Gift登录';
    	$authObj = new Business_Auth();
    	if ($authObj->isLogin()) {
    		$this->_redirect('/user');
    	}
    }
    
    public function indexAction()
    {
    }
    
    public function infoAction()
    {
    	
    }
    
    public function avatarAction()
    {
    
    }
    
    public function passwdAction()
    {
    
    }
}