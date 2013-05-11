<?php
abstract class Business_Goods_Abstract
{
    protected $_db;
    protected $_config;
    public function __construct()
    {
        $this->_db = Utility_Db::getInstance()->conn();
        $this->_config = Zend_Registry::get('config')->producttable;
    }
}