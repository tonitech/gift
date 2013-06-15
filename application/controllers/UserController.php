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
					$authObj->getCookieExpireTime(),
					'/'
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
		    $authResult = $this->checkLogin($username, $password);
		    if ($authResult['errorcode'] == 0) {
    			$rtn = Business_User_Auth::getInstance()->userLogin($authResult['result']['id'], $password);
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
        $this->rediretToLogin();
        $this->getLoginUserInfoView();
        
        // 上传图片后，把图片复制到upload文件夹下面
        if ($_POST) {
            $photo = $_FILES['img']['name'];
            $tmpAddr = $_FILES['img']['tmp_name'];
            $path = 'public/avatar/';
            $type = array(
                "jpg",
                "gif",
                "jpeg",
                "png"
            );
            $tool = substr(strrchr($photo, '.'), 1);
            if (!in_array(strtolower($tool), $type)) {
                echo "您只能上传以下类型文件: jpg, gif, jpeg, png！";
            } else {
                $filename = explode(".", $photo); // 把上传的文件名以"."好为准做一个数组。
                $filename[0] = time(); // 取文件名，用时间戳
                $name = implode(".", $filename); // 上传后的文件名
                $uploadfile = $path . $name;
                $uploadFileSession = new Zend_Session_Namespace('uploadfile');
                $uploadFileSession->upfile = '/' . $uploadfile; // 上传后的文件名地址
                $uploadFileSession->upfile2 = $uploadfile; // 上传后的文件名地址
                move_uploaded_file($tmpAddr, $uploadfile);
                list ($width, $height) = getimagesize($uploadfile);
            }
            $this->view->width = $width;
            $this->view->height = $height;
            $this->render('avatar');
        }
    }
    
    public function passwdAction()
    {
        $this->rediretToLogin();
        $this->getLoginUserInfoView();
    }
    
    /**
     * 用户退出action
     * 退出之后跳转到首页
     */
    public function logoutAction()
    {
    	Business_User_Auth::getInstance()->logout()->deleteCookie();
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
    
    public function updateInfoAction()
    {
        $request = $this->getRequest();
        if ($request->isGet()) {
            $userInfo = array();
            $username = NULL;
            $userInfo['username'] = $this->_request->getQuery("username");
            if (mb_strlen($userInfo['username']) < 6 || mb_strlen($userInfo['username']) > 20) {
                $errormsg = "用户名长度必须为6-20个字符！";
                $usernameJudge = false;
            } elseif (!Business_User_Signup::getInstance()->isCurrentUser($userInfo['username'])
			&&!Business_User_Signup::getInstance()->isNotExist($userInfo['username'])) {
                $errormsg = "用户名已被注册！";
                $usernameJudge = false;
            } else {
                $usernameJudge = true;
            }
            	
            $email = NULL;
            $userInfo['email'] = $this->_request->getQuery("email");
            if (empty($userInfo['email'])) {
                $emailJudge = true;
            } elseif (!Business_User_Info::getInstance()->isEmailAddress($userInfo['email'])) {
                $errormsg = "电子邮箱地址不正确！";
                $emailJudge = false;
            } else {
                $emailJudge = true;
            }
            	
            $userInfo['province'] = $this->_request->getQuery("province");
            $userInfo['city'] = $this->_request->getQuery("city");
            $userInfo['district'] = $this->_request->getQuery("district");
            
            $userInfo['gender'] = $this->_request->getQuery("gender");
            
            $birthdate = NULL;
            $year = $this->_request->getQuery("year");
            $month = $this->_request->getQuery("month");
            $day = $this->_request->getQuery("day");
            if ($year != 0 && $month != 0 && $day != 0) {
                $userInfo['birthday'] = $year."-".$month."-".$day;
                $birthdateJudge = true;
            } else if ($year == 0 && $month == 0 && $day == 0) {
                $userInfo['birthday'] = NULL;
                $birthdateJudge = true;
            } else {
                $errormsg = "请输入完整日期！";
                $birthdateJudge = false;
            }
            
            $userInfo['occupation'] = $this->_request->getQuery("occupation");
            $userInfo['blog'] = $this->_request->getQuery("blog");
            $userInfo['introduction'] = $this->_request->getQuery("introduction");
            
            $resultSent = NULL;
            if ($usernameJudge && $emailJudge && $birthdateJudge) {
                $time = date('Y-m-d H:i:s');
                $base = array(
                    'ctime' => $time,
                    'mtime' => $time
                );
                $userInfo = array_merge($base, $userInfo);
                $result = Business_User_Info::getInstance()->setBaseInfo($userInfo);
            } else {
                $result = array(
                    'errorcode' => -3,
                    'errormsg' => $errormsg
                );
            }
            $this->_helper->getHelper('Json')->sendJson($result);
        }
    }
    
    public function changePasswordAction()
    {
        $userinfo = Business_User_Auth::getInstance()->getUserInfoBySession();
        if ($userinfo['errorcode'] !== 0) {
            $this->_redirect('/user');
        } else {
            $rtn = array();
            if ($this->_request->isPost()) {
                $oldpwd = $this->_request->getPost("oldpwd");
                $pwd1 = $this->_request->getPost("pwd1");
                $pwd2 = $this->_request->getPost("pwd2");
                
                $isOldpwd = Business_User_Info::getInstance()->checkOldPwd($oldpwd);
                if ($isOldpwd['errorcode'] == -1) {
                    $rtn = array(
                        'errorcode' => -1,
                        'errormsg' => "当前密码输入错误！"
                    );
                    $isValid = false;
                } elseif (mb_strlen($pwd1) < 6 || mb_strlen($pwd1) > 16) {
                    $rtn = array(
                        'errorcode' => -2,
                        'errormsg' => "密码长度应为6~16个字符！"
                    );
                    $isValid = false;
                } elseif ($pwd1 != $pwd2) {
                    $rtn = array(
                        'errorcode' => -3,
                        'errormsg' => "两次输入的密码不同！"
                    );
                    $isValid = false;
                } else {
                    $isValid = true;
                }
    
                if ($isValid) {
                    Business_User_Info::getInstance()->changePwd($pwd1);
                    $rtn = array(
                        'errorcode' => 0,
                        'errormsg' => "success"
                    );
                }
            }
            $this->_helper->getHelper('Json')->sendJson($rtn);
        }
    }

    public function uploadAvatarAction()
    {
        error_reporting(E_ALL);
        
        $x = $this->_request->getParam('x');
        $y = $this->_request->getParam('y');
        $w = $this->_request->getParam('w');
        $h = $this->_request->getParam('h');
        
        $uploadFileSession = new Zend_Session_Namespace('uploadfile');
        list($width, $height, $type) = getimagesize($uploadFileSession->upfile2);
        $imageFileElements = explode('/', $uploadFileSession->upfile2);
        $imageFileName = end($imageFileElements);
        $supportType = array(
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_GIF
        );
        if (! in_array($type, $supportType, true)) {
            echo "this type of image does not support! only support jpg , gif or png";
            exit();
        }
        // Load image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImg = imagecreatefromjpeg($uploadFileSession->upfile2);
                break;
            case IMAGETYPE_PNG:
                $srcImg = imagecreatefrompng($uploadFileSession->upfile2);
                break;
            case IMAGETYPE_GIF:
                $srcImg = imagecreatefromgif($uploadFileSession->upfile2);
                break;
            default:
                echo "Load image error!";
                exit();
        }
        $thumb = imagecreatetruecolor($w, $h);
        imagecopyresampled($thumb, $srcImg, 0, 0, $x, $y, $w, $h, $w, $h);
        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($thumb, $uploadFileSession->upfile2);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($thumb, $uploadFileSession->upfile2);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($thumb, $uploadFileSession->upfile2);
                break;
            default:
                echo "Load image error!";
                $result = false;
                exit();
        }
        if ($result) {
            $usertable = Zend_Registry::get('dbtable')->user;
            $uploadFileElements = explode('/', $uploadFileSession->upfile2);
            unset($uploadFileElements[0]);
            $uploadFile = implode('/', $uploadFileElements);
            $rtn = Business_User_Info::getInstance()->setBaseInfo(
                array(
                    $usertable->mtime => date('c'),
                    $usertable->avatar => '/' . $uploadFile
                )
            );
            
            if ($result['errorcode'] == 0) {
                $tmp = array('result' => $uploadFileSession->upfile);
                $rtn = array_merge($rtn, $tmp);
                $uploadFileSession->__unset('upfile');
                $uploadFileSession->__unset('upfile2');
                Business_User_Auth::getInstance()->refreshUserInfo();
            }
        } else {
            $rtn = array(
                'errorcode' => -1,
                'errormsg' => 'failed'
            );
        }
        $this->_helper->getHelper('Json')->sendJson($rtn);
    }
}