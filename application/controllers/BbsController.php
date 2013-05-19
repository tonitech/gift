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
		$this->getLoginUserInfoView();
	}
	
	public function postAction()
	{
		
	}
}
