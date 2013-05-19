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
    }
    
    public function detailAction()
    {
    	$this->view->title = 'Gift-查看礼物';
    	$this->getLoginUserInfoView();
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
    	$order = $this->_request->getParam('order');
    	
    	$table = Zend_Registry::get('dbtable')->goods;
    	$operationObj = new Business_Goods_Operation();
    	if (empty($page)) {
    		$page = 1;
    	}
    	$list = $operationObj->getGoodsList($page, 12, $order, $cate, $userid);
    	$blocks = array();
    	foreach ($list as $item) {
    		$blocks[] = '<div class="book_item hide1 masonry-brick" shareid="' . $item[$table->id] . '" id="' . $item[$table->keyid] . '" style="visibility: hidden; position: absolute;">
	        	<div class="bi_body">
	        		<ul class="pic">
	        			<li>
	        				<a style="width:200px;" href="/note/5258" target="_blank">
	        					<img class="book_img lazyload" width="' . $item[$table->picWidth] . '" height="' . $item[$table->picHeight] . '" src="' . $item[$table->picUrl] . '" alt="" style="display: block;" ">
	        				</a>
	        				<p>' . $item[$table->price] . '
	        				</p>
	        			</li>
	        		</ul>
	        		<div class="content">' . $item[$table->description] . '
	        		</div>
	        		<div class="favorite">
	        			<a href="javascript:;" class="favaImg" onclick="$.Fav_Share(5258,this,32,\'#share_item_5258\');">
	        			</a>
	        			<div class="favDiv">
	        				<a target="_blank" class="favCount SHARE_FAV_COUNT" href="/note/5258">' . $item[$table->like] . '
	        				</a>
	        			</div>
	        			<a target="_blank" href="/note/5258" class="creply">
	        				<b>' . $item[$table->comment] . '</b>评论
	        			</a>
	        		</div>
	        		<div class="user">
	        			<a title="amy" href="/u/9" target="_blank">
	        				<img class="GUID lazyload" uid="9" original="http://www.ibudian.com/public/upload/avatar/noavatar_small.jpg" src="http://www.ibudian.com/public/upload/avatar/noavatar_small.jpg" width="30" alt="amy" style="display: block;">
	        			</a>
	        			<p>
	        				<span class="u">
	        					<a class="GUID " uid="9" title="amy" href="/u/9" target="_blank">amy
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
	}
}