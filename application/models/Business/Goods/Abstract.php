<?php
abstract class Business_Goods_Abstract
{
    protected $_db;
    protected $_table;
    public function __construct()
    {
        $this->_db = Utility_Db::getInstance()->conn();
        $this->_table = Zend_Registry::get('dbtable')->goods;
    }
}