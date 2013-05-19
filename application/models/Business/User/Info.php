<?php
class Business_User_Info extends Business_User_Abstract
{
    private static $_instance;
    
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Business_User_Info();
        }
        return self::$_instance;
    }
    
    /**
     * 获得省份的内容
     *
     * @return array
     */
    public function getProvince()
    {
        $sql = "Select * from province";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
    
    /**
     * 获得城市的内容
     *
     * @param $paraProvince 所在省份
     * @return array
     */
    public function getCity($paraProvince)
    {
        $sql = "Select * from city where pcode='$paraProvince'";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
    
    /**
     * 获得区县的内容
     *
     * @param $paraCity 所在城市
     * @return array
     */
    public function getDistrict ($paraCity)
    {
        $sql = "Select * from district where ccode='$paraCity'";
        $result = $this->_db->fetchAll($sql);
        return $result;
    }
    
    public function getOccucpationList()
    {
        return $this->_db->select()->from('occupation')->query()->fetchAll();
    }
}