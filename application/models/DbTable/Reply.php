<?php

class DbTable_Reply extends Zend_Db_Table_Abstract
{

    protected $_name = 'replys';
	protected $_id = 'id';
	
	public function findByArticleId($aid)
	{
		return $this->fetchAll(array('article_id = ?' => $aid));
	}
}
