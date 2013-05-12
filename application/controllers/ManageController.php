<?php
class ManageController extends Zend_Controller_Action
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
     * 管理员登陆界面
     * 如果登陆直接进入homepage管理页
     */
    public function indexAction()
    {
        $this->view->title = 'Gift管理员登录';
        $authObj = new Business_Auth();
        if ($authObj->isLogin()) {
            $this->_redirect('/manage/homepage');
        }
    }
    
    /**
     * 用户登陆验证action
     * 如果用户名密码正确直接分配session
     * 如果有选择记住我，就分配cookie
     * 成功之后跳转到homepage管理页
     */
    public function loginAction()
    {
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
                $this->_redirect('/manage/homepage');
            } else {
                $this->view->errormsg = '<div id="login_error">登录失败！</div>';
                $this->render('index');
            }
        } else {
            $this->view->errormsg = '<div id="login_error">登录失败！</div>';
            $this->render('index');
        }
    }
    
    /**
     * homepage管理页面
     * 包括登陆验证
     */
    public function homepageAction()
    {
        $authObj = new Business_Auth();
        if ($authObj->isLogin()) {
            $this->view->title = 'Gift网站首页管理';
            $homepageObj = new Business_Manage_Homepage();
            list($slider, $sliderTable, $modules, $modulesTable) = $homepageObj->getSliderAndModules();
            $this->view->slider = $slider;
            $this->view->sliderTable = $sliderTable;
            $this->view->modules = $modules;
            $this->view->modulesTable = $modulesTable;
        } else {
            $this->_redirect('/manage');
        }
    }
    
    /**
     * 用户退出action
     * 退出之后跳转到首页
     */
    public function logoutAction()
    {
        $authObj = new Business_Auth();
        $authObj->logout();
        $authObj->deleteCookie();
        $this->_redirect('/manage');
    }
    
    /**
     * 修改首页模块信息的action
     */
    public function modHomepageModulesAction()
    {
        $form = $this->_request->getParams();
        unset($form['controller']);
        unset($form['action']);
        unset($form['module']);
        $content = array();
        $homepageObj = new Business_Manage_Homepage();
        $tableMaps = $homepageObj->getHomepageModulesTableInfo();
        foreach ($form as $key => $value) {
            $content[$tableMaps->$key] = $value;
        }
        $rtn = $homepageObj->updateHomepageModules($content);
        $this->view->errormsg = $rtn['errormsg'];
        list($slider, $sliderTable, $modules, $modulesTable) = $homepageObj->getSliderAndModules();
        $this->view->slider = $slider;
        $this->view->sliderTable = $sliderTable;
        $this->view->modules = $modules;
        $this->view->modulesTable = $modulesTable;
        $this->render('homepage');
    }
    
    /**
     * 修改首页slider的action
     */
    public function modHomepageSliderAction()
    {
        $form = $this->_request->getParams();
        unset($form['controller']);
        unset($form['action']);
        unset($form['module']);
        $homepageObj = new Business_Manage_Homepage();
        $tableMaps = $homepageObj->getHomepageSliderTableInfo();
        $amount = count($form);
        $content = array();
        for ($i = 1; $i <= $amount / 4; $i++) {
            if ($form['imageUrl' . $i] != null) {
                $content[] = array(
                    $tableMaps->title => $form['title' . $i],
                    $tableMaps->description => $form['description' . $i],
                    $tableMaps->detailUrl => $form['detailUrl' . $i],
                    $tableMaps->imageUrl => $form['imageUrl' . $i]
                );
            }
        }
        if (count($content) != 0) {
            $rtn = $homepageObj->updateHomepageSlider($content);
            $this->view->errormsg = $rtn['errormsg'];
        } else {
            $this->view->errormsg = 'Nothing submitted!';
        }
        list($slider, $sliderTable, $modules, $modulesTable) = $homepageObj->getSliderAndModules();
        $this->view->slider = $slider;
        $this->view->sliderTable = $sliderTable;
        $this->view->modules = $modules;
        $this->view->modulesTable = $modulesTable;
        $this->render('homepage');
    }
}