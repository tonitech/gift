<?php
/**
 * @author tianying
 * @abstract This is the controller used to deal with goods actions.
 * @create 2013-03-15 00:15:18
 */
class GoodsController extends Zend_Controller_Action
{
    public function init()
    {
    }
    
    public function indexAction()
    {
        $this->view->title = 'Goods List';
    }
    
    public function getProductIdAction()
    {
        $url = $this->_request->getParam('url');
        $taobaoObj = new Business_Goods_Taobao();
        $rtn = $taobaoObj->getFanliProductId($url);
        $this->_helper->getHelper('Json')->sendJson($rtn);
    }
}