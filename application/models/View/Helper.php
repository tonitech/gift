<?php
class View_Helper extends Zend_Controller_Action
{
	public function init()
	{
		parent::init();
		$cookieName = Zend_Registry::get('config')->user->cookie;
		if (isset($_COOKIE[$cookieName])) {
			$cookieResult = Business_User_Auth::getInstance()->validateAuthSignature(
				$_COOKIE[$cookieName]
			);
			if ($cookieResult['errorcode'] == 0) {
				Business_User_Auth::getInstance()->setUserLogin($cookieResult['result']);
			}
		}
	}
	
	public function getLoginUserInfoView()
	{
		if (Business_User_Auth::getInstance()->isLogin()) {
			$rtn = Business_User_Auth::getInstance()->getUserInfoBySession();
			if ($rtn['errorcode'] == 0) {
				$info = array(
						'id' => $rtn['result']['id'],
						'username' => $rtn['result']['username'],
						'avatar' => $rtn['result']['avatar']
				);
			} else {
				$info = array();
			}
		} else {
			$info = array();
		}
		$this->view->userinfo = $info;
	}
	
	public function rediretToLogin()
	{
		$authObj = new Business_User_Auth();
		if (!$authObj->isLogin()) {
			$this->_redirect('/user/login');
		}
	}
}