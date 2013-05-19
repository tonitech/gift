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
	
	public function postAction()
	{
		$title = $this->getRequest()->getParam('title');
		$content = $this->getRequest()->getParam('content');
		$articleTable = new DbTable_Article();
		$row = $articleTable->createRow(
		    array(
    			'title' => $title,
    			'content' => $content,
    			'author' => 111,
    			'ctime' => date('c'),
    			'mtime' => date('c'),
    			'liked_times' => 0
    		)
		);
		$row->save();
		$this->_redirect('/bbs/index');
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
		$reply = new Reply($articleId);
		$this->view->article = $article;
		$this->view->replys = $reply->format();
	}
}
