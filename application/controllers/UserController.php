<?php
/**
 * @author tianying
 * @abstract This is the controller used to deal with user actions.
 * @create 2013-03-10 22:14:02
 */
class UserController extends View_Helper
{
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
			$authObj = new Business_User_Auth();
			$authResult = $authObj->checkLogin($username, $password);
			if ($authResult['errorcode'] == 0) {
				if (isset($rememberme)) {
					$cookieName = Zend_Registry::get('config')->user->cookie;
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
	
	/**
	 * 用户AJAX登陆验证action
	 * 如果用户名密码正确直接分配session和cookie
	 */
	public function ajaxLoginAction()
	{
		$username = $this->_request->getParam('username');
		$password = $this->_request->getParam('password');
		if (!empty($username) && !empty($password)) {
			$authObj = new Business_User_Auth();
			$authResult = $authObj->checkLogin($username, $password);
			if ($authResult['errorcode'] == 0) {
				$cookieName = Zend_Registry::get('config')->user->cookie;
				$cookie = $authObj->generateAuthSignature(
					$authResult['result'],
					$authObj->getCookieExpireTime()
				);
				setcookie(
					$cookieName,
					$cookie,
					$authObj->getCookieExpireTime()
				);
				$authObj->setUserLogin($authResult['result']);
				$rtn['errorcode'] = 0;
				$rtn['errormsg'] = 'succes';
				$rtn['result'] = array(
					'id' => $authResult['result']['id'],
					'username' => $authResult['result']['username'],
					'avatar' => $authResult['result']['avatar']
				);
				$rtn['cookieName'] = $cookieName;
				$rtn['cookieValue'] = $cookie;
				$rtn['expire'] = $authObj->getCookieExpireTime();
			} else {
				$rtn['errorcode'] = -1;
				$rtn['errormsg'] = '用户名和密码错误！';
			}
		} else {
			$rtn['errorcode'] = -2;
			$rtn['errormsg'] = '请输入用户名和密码！';
		}
		$this->_helper->getHelper('Json')->sendJson($rtn);
	}
	
	public function registrationAction()
	{
		$this->view->title = 'Gift注册';
	}
	
    public function loginAction()
    {
    	$this->view->title = 'Gift登录';
    	$authObj = new Business_User_Auth();
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