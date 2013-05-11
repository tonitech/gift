<?php
/**
 * @author tianying
 * @abstract This class is used to deal with user sign up action.
 * @create 2013-03-09 01:18:09
 */
class Business_User_Signup extends Business_User_Abstract
{
	/**
     * 判断用户名是否是存在
     * @param string $username
     * @return boolean
     */
    public function isNotExist($username)
    {
        $rows = $this->_db->select()
            ->from($this->_config->tablename, '*')
            ->where($this->_config->username . '=?', $username)
            ->query()
            ->fetchAll();
        if (count($rows) == 0) {
            return true;
        } else {
            return false;
        }
        $this->_db->closeConnection();
    }
    
	/**
     * 判断电子邮件地址是否存在
     * 
     * @param string $email
     * @return boolean
     */
    public function isEmailAddress($email)
    {
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($email)) {
            return true;
        } else {
            return false;
        }
    }
    
	/**
     * 将注册信息写入数据库，并发送电子邮件
     * @param array
     * keys: username|password|email
     */
    public function signupSuccess($info)
    {
        $this->_db->insert($this->_config->tablename, $info);
        $id = $this->_db->lastInsertId();
        $this->_db->closeConnection();
        return $id ? $id : false;
    }
}