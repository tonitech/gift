<?php
/**
 * BbsController
 * 
 * @author
 * @version 
 */
class BbsController extends View_Helper
{
	/**
	 * The default action - show the home page
	 */
	public function indexAction()
	{
		//$this->getLoginUserInfoView();
		$articleTable = new DbTable_Article();
		$articles = $articleTable->fetchAll(array('isvalid = ?' => 1));		
		$this->view->articles = $articles;
	}
	
	public function postArticleAction()
	{
		$title = $this->getRequest()->getParam('title');
		$content = $this->getRequest()->getParam('content');
		$articleTable = new DbTable_Article();
		$time = date('c');
		$usertable = Zend_Registry::get('dbtable')->user;
		$userinfo = Business_User_Auth::getInstance()->getUserInfoBySession();
		if ($userinfo['errorcode'] == -1) {
		    $rtn = $userinfo;
		} else {
    		$row = $articleTable->createRow(
    		    array(
        			'ctime' => $time,
        			'mtime' => $time,
        			'author' => $userinfo['result'][$usertable->id],
        			'liked_times' => 0,
        			'content' => $content,
        			'title' => $title,
    		        'last_reply_id' => $userinfo['result'][$usertable->id]
        		)
    		);
    		$row->save();
    		$rtn['errorcode'] = 0;
    		$rtn['errormsg'] = 'succes';
		}
		$this->_helper->getHelper('Json')->sendJson($rtn);
	}

	public function likeAction()
	{
		$aid = $this->getRequest()->getParam('aid');
		$articleTable = new DbTable_Article();
		$article = $articleTable->fetchRow(array('id = ?' => $aid));
		$article->liked_times = $article->liked_times + 1;
		$article->save();
		$this->_redirect('/bbs/index');
	}

	public function detailAction()
	{
		$articleId = $this->getRequest()->getParam('aid');
		$articleTable = new DbTable_Article();
		$article = $articleTable->fetchRow(array('id = ?' => $articleId));
		$reply = new Business_Forum_Reply($articleId);
		$this->view->article = $article;
		$this->view->replys = $reply->format();
	}
}
