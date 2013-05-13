<?php
/**
 * @author tianying
 * @abstract This class is used to deal with database and configs.
 * @create 2013-03-10 22:12:38
 */
abstract class Business_User_Abstract
{
    protected $_db;
    protected $_config;
    
    public function __construct()
    {
        $this->_db = Utility_Db::getInstance()->conn();
        $this->_config = Zend_Registry::get('dbtable')->user;
    }
}