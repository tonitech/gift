<?php
/**
 * @author tianying
 * @abstract Database handler with Singleton Pattern
 * @create 2013-03-09 01:23:04
 **/
class Utility_Db
{
    private static $_instance;
    
    private function __construct()
    {
    }
    
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Utility_Db();
        }
        return self::$_instance;
    }
    
    public function conn()
    {
        $config = Zend_Registry::get('db');
    	$connParams = array("host" => $config->db->host,
				"username" => $config->db->username,
    			"password" => $config->db->password,
    			"dbname" => $config->db->dbname,
    			"charset" => $config->db->charset
    	);
    	$db = Zend_Db::factory('MYSQLI', $connParams);
    	return $db;
    }
}