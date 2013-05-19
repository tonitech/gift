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
	 * 成功之后跳转到用户首页
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
				$this->_redirect('/user');
			} else {
				$this->view->errormsg = '<div id="login_error">登录失败！</div>';
				$this->render('login');
			}
		} else {
			$this->view->errormsg = '<div id="login_error">请输入用户名和密码！</div>';
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
		$authObj = new Business_User_Auth();
		if (!$authObj->isLogin()) {
		    $signupObj = new Business_User_Signup();
		    if ($this->_request->isPost()) {
		        //用户名判断
		        $username = $this->_request->getPost("username");
		        if (mb_strlen($username) < 2 || mb_strlen($username) > 20) {
		            $this->view->username = $username;
		            $this->view->usernameHint = "用户名长度必须为2-20个字符！";
		            $usernameJudge = false;
		        } elseif (!$signupObj->isNotExist($username)) {
		            $this->view->username = $username;
		            $this->view->usernameHint = "用户名已被注册！";
		            $usernameJudge = false;
		        } else {
		            $usernameJudge = true;
		        }
		        
		        //判断密码
		        $pwd1 = $this->_request->getPost("pwd1");
		        $pwd2 = $this->_request->getPost("pwd2");
		        if (mb_strlen($pwd1) < 6 || mb_strlen($pwd1) > 20) {
		            $this->view->pwd1Hint = "密码长度长度必须为6-20个字符！";
		            $pwdJudge = false;
		        } elseif ($pwd1 != $pwd2) {
		            $this->view->pwd2Hint = "两次输入的密码不同！";
		            $pwdJudge = false;
		        } else {
		            $pwdJudge = true;
		        }
		
		        //判断验证码
		        $code = $this->_request->getPost("code");
		        if ($signupObj->isCorrectCode($code)) {
		            $codeJudge = true;
		        } else {
		            $this->view->codeHint = "验证码输入不正确！";
		            $codeJudge = false;
		        }
		
		        //判断是否输入数据库
		        if ($usernameJudge == true && $pwdJudge == true && $codeJudge == true) {
		            //写入数据库
		            $info = array(
	                    'username' => $username,
	                    'password' => md5($pwd1),
		            );
		            $rtn = $signupObj->signupSuccess($info);
		            if ($rtn['errorcode'] == 0) {
    		            $this->_redirect('/');
		            } else {
		                $this->view->errormsg = $rtn['errormsg'];
		                $this->render("registration");
		            }
		        } else {
		            $captcha = $signupObj->getCaptcha();
		            $this->view->captchaDir = $captcha['dir'];
		            $this->view->captchaId = $captcha['name'];
		            $this->render("registration");
		        }
		    } else {
		        $captcha = $signupObj->getCaptcha();
		        $this->view->captchaDir = $captcha['dir'];
		        $this->view->captchaId = $captcha['name'];
		    }
		} else {
		    $this->_redirect('/');
		}
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
    	$this->view->title = '自家乐园';
    	$this->rediretToLogin();
    	$this->getLoginUserInfoView();
    }
    
    public function infoAction()
    {
        $this->view->title = '基本资料设置';
        $this->rediretToLogin();
        if (Business_User_Auth::getInstance()->isLogin()) {
			$rtn = Business_User_Auth::getInstance()->getUserInfoBySession();
			if ($rtn['errorcode'] != 0) {
				$info = array();
			} else {
			    $info = $rtn['result'];
			}
		} else {
			$info = array();
		}
		$this->view->userinfo = $info;
		
		$this->view->province = Business_User_Info::getInstance()->getProvince();
		if ($info['province'] != NULL) {
		    $this->view->provinceselected = $info['province'];
		    $cityArr = array(
		        'allCity' => Business_User_Info::getInstance()->getCity($info['province']),
		        'selected' => $info['city']
		    );
		
		    $cityView = '';
		    foreach ($cityArr['allCity'] as $city) {
		        if ($cityArr['selected'] == $city['code']) {
		            $cityView .= '<option value="'.$city['code'].'" selected>'.$city['name'].'</option>';
		        } else {
		            $cityView .= '<option value="'.$city['code'].'" >'.$city['name'].'</option>';
		        }
		    }
		    $this->view->city = $cityView;
		     
		     
		    $districtArr = array(
		        'allDistrict' => Business_User_Info::getInstance()->getDistrict($info['city']),
		        'selected' => $info['district']
		    );
		
		    $districtView = '';
		    foreach ($districtArr['allDistrict'] as $district) {
		        if ($districtArr['selected'] == $district['code']) {
		            $districtView .= '<option value="'.$district['code']
		            .'" selected>'.$district['name'].'</option>';
		        } else {
		            $districtView .= '<option value="'.$district['code'].'" >'.$district['name'].'</option>';
		        }
		    }
		    $this->view->district = $districtView;
		} else {
		    $this->view->city = '';
		    $this->view->district = '';
		}
		
		$this->view->occupation = Business_User_Info::getInstance()->getOccucpationList();
    }
    
    public function avatarAction()
    {
    
    }
    
    public function passwdAction()
    {
    
    }
    
    /**
     * 用户退出action
     * 退出之后跳转到首页
     */
    public function logoutAction()
    {
    	$authObj = new Business_User_Auth();
    	$authObj->logout();
    	$authObj->deleteCookie();
    	$this->_redirect('/');
    }
    
    public function cityAction()
    {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $province = $request->getQuery('province');
            $callbackCity = Business_User_Info::getInstance()->getCity($province);
            $cityFirst = $callbackCity[0]['code'];
            $callbackDistrict = Business_User_Info::getInstance()->getDistrict($cityFirst);
            $callBack = array(
                "city" => $callbackCity,
                "district" => $callbackDistrict,
            );
            $this->_helper->getHelper('Json')->sendJson($callBack);
        }
    }
    
    public function districtAction()
    {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $city = $request->getQuery('city');
            $callback = array(
                "district" => Business_User_Info::getInstance()->getDistrict($city)
            );
            $this->_helper->getHelper('Json')->sendJson($callBack);
        }
    }
}