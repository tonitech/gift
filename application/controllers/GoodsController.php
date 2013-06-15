<?php
/**
 * @abstract This is the controller used to deal with goods actions.
 * @create 2013-03-15 00:15:18
 */
class GoodsController extends View_Helper
{
    public function indexAction()
    {
        $this->view->title = 'Gift-选礼物';
        $this->getLoginUserInfoView();
        $keyword = $this->_request->getParam('keyword');
        $this->view->keyword = $keyword;
    }
    
    public function detailAction()
    {
    	$this->view->title = 'Gift-查看礼物';
    	$this->getLoginUserInfoView();
    	$id = $this->_request->getParam('id');
    	$goodsOperationObj = new Business_Goods_Operation();
    	$this->view->details = $goodsOperationObj->getGoodsDetailsById($id);
    	$this->view->comments = Business_Goods_Comment::getInstance()->getComment($id);
    }
    
    public function getProductIdAction()
    {
        $url = $this->_request->getParam('url');
        $taobaoObj = new Business_Goods_Taobao();
        $rtn = $taobaoObj->getFanliProductId($url);
        $this->_helper->getHelper('Json')->sendJson($rtn);
    }
    
    /**
     * AJAX获取商品
     */
    public function getProductListAction()
    {
    	$cate = $this->_request->getParam('cate');
    	$page = $this->_request->getParam('page');
    	$userid = $this->_request->getParam('userid');
    	$usertype = $this->_request->getParam('usertype');
    	$order = $this->_request->getParam('order');
    	$keyword = $this->_request->getParam('keyword');
    	
    	$table = Zend_Registry::get('dbtable')->goods;
    	$operationObj = new Business_Goods_Operation();
    	if (empty($page)) {
    		$page = 1;
    	}
    	$list = $operationObj->getGoodsList($page, 12, $order, $cate, $userid, $usertype, $keyword);
    	$blocks = array();
    	foreach ($list as $item) {
    	    $userinfo = Business_User_Auth::getInstance()->getUserInfoById($item[$table->userid]);
    		$blocks[] = '<div class="book_item hide1 masonry-brick" shareid="' . $item[$table->id] . '" id="' . $item[$table->keyid] . '" style="visibility: hidden; position: absolute;">
	        	<div class="bi_body">
	        		<ul class="pic">
	        			<li>
	        				<a style="width:200px;" href="/goods/detail/id/' . $item[$table->id] . '" target="_blank">
	        					<img class="book_img lazyload" width="' . $item[$table->picWidth] . '" height="' . $item[$table->picHeight] . '" src="' . $item[$table->picUrl] . '" alt="" style="display: block;" ">
	        				</a>
	        				<p>' . $item[$table->price] . '
	        				</p>
	        			</li>
	        		</ul>
	        		<div class="content">' . $item[$table->description] . '
	        		</div>
	        		<div class="favorite">
	        			<a href="###" class="favaImg">
	        			</a>
	        			<div class="favDiv">
	        				<a target="_blank" class="favCount SHARE_FAV_COUNT" href="/goods/detail/id/' . $item[$table->id] . '">' . $item[$table->like] . '
	        				</a>
	        			</div>
	        			<a target="_blank" href="/goods/detail/id/' . $item[$table->id] . '" class="creply">
	        				<b>' . $item[$table->comment] . '</b>评论
	        			</a>
	        		</div>
	        		<div class="user">
	        			<a title="amy" href="' . APPLICATION_ACTION_PATH . '/user/profile/uid/' . $userinfo['id'] . '" target="_blank">
	        				<img class="GUID lazyload" uid="9" src="' . APPLICATION_PUBLIC_PATH . $userinfo['avatar'] . '" width="30" alt="' . $userinfo['username'] . '" style="display: block;">
	        			</a>
	        			<p>
	        				<span class="u">
	        					<a class="GUID " uid="9" title="' . $userinfo['username'] . '" href="' . APPLICATION_ACTION_PATH . '/user/profile/uid/' . $userinfo['id'] . '" target="_blank">' . $userinfo['username'] . '
	        					</a>
	        				</span>
	        				<span class="t">' . date('Y年m月d日 H:i', strtotime($item[$table->ctime])) . '
	        				</span>
	        			</p>
	        		</div>
	        	</div>
	        	<div class="bi_foot">
	        	</div>
	        </div>';
    	}
    	$this->_helper->getHelper('Json')->sendJson($blocks);
    }
    
	public function shareAction()
	{
		$goods = $this->_request->getParam('goods');
		$description = $this->_request->getParam('desc');
		$tmp = array('description' => $description);
		$goodsDetails = array_merge($goods, $tmp);
		$goodsOperationObj = new Business_Goods_Operation();
		$rtn = $goodsOperationObj->addGoods($goodsDetails);
		$this->_helper->getHelper('Json')->sendJson($rtn);
	}
	
	public function commentGoodsAction()
	{
	    $goodsid = $this->getRequest()->getParam('goodsid');
	    $goodsauthor = $this->getRequest()->getParam('goodsauthor');
	    $content = $this->getRequest()->getParam('content');
	    $userinfo = Business_User_Auth::getInstance()->getUserInfoBySession();
	    if ($userinfo['errorcode'] == -1) {
	        $rtn = $userinfo;
	    } else {
    	    $bind = array(
    	        'ctime' => date('c'),
    	        'mtime' => date('c'),
    	        'goods_id' => $goodsid,
    	        'reply_to_author' => $goodsauthor,
    	        'content' => $content,
    	        'author' => $userinfo['result']['id']
    	    );
    	    $rtn = Business_Goods_Comment::getInstance()->setComment($bind);
	    }
	    $this->_helper->getHelper('Json')->sendJson($rtn);
	}
	
	public function likeAction()
	{
	    $goodsid = $this->getRequest()->getParam('goodsid');
	    $rtn = Business_Goods_Operation::getInstance()->setGoodsLike($goodsid);
	    $this->_helper->getHelper('Json')->sendJson($rtn);
	}
	
	public function searchAction()
	{
	    $keyword = $this->getRequest()->getParam('keyword');
	    $result = Utility_Db::getInstance()
	       ->conn()
	       ->select()
	       ->from('goods', '*')
	       ->where('`name` like \'%' . $keyword . '%\'')
	       ->query()
	       ->fetchAll();
	    var_dump($result);exit;
	}
}