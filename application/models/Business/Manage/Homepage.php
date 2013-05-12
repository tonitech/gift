<?php
/**
 * 操作首页显示数据库的类
 * @create 2013-04-30 18:30:59
 */
class Business_Manage_Homepage
{
    /**
     * 用于获取首页模块的数据库字段映射关系
     * @return array 返回首页数据库的字段映射关系数组
     */
    public function getHomepageModulesTableInfo()
    {
        return Zend_Registry::get('config')->homepage;
    }
    
	/**
     * 用于获取首页slider的数据库字段映射关系
     * @return array 返回首页slider数据库的字段映射关系数组
     */
    public function getHomepageSliderTableInfo()
    {
        return Zend_Registry::get('config')->homepageslider;
    }
    
    /**
     * 将修改的信息直接插入数据库表中
     * 这样有利于页面的版本控制
     * @param array $content
     * @return 返回更新的操作信息
     * return array(
            'errorcode' => 0,
            'errormsg' => 'success'
        );
     */
    public function updateHomepageModules($content)
    {
        $homepageTable = $this->getHomepageModulesTableInfo();
        try {
            $time = time();
            $basic = array(
                $homepageTable->ctime => $time,
                $homepageTable->mtime => $time
            );
            $binds = array_merge($basic, $content);
            $rtn = Utility_Db::getInstance()
                ->conn()
                ->insert($homepageTable->tablename, $binds);
            if ($rtn) {
                return array(
                    'errorcode' => 0,
                    'errormsg' => 'success'
                );
            } else {
                return array(
                    'errorcode' => -1,
                    'errormsg' => 'database failed'
                );
            }
        } catch (Zend_Exception $e) {
            return array(
                'errorcode' => -2,
                'errormsg' => $e->getMessage()
            );
        }
    }
    
	/**
     * 将修改的slider信息直接插入数据库表中
     * 这样有利于页面的版本控制
     * @param array $content
     * @return 返回slider更新的操作信息
     * return array(
            'errorcode' => 0,
            'errormsg' => 'success'
        );
     */
    public function updateHomepageSlider($content)
    {
        $table = $this->getHomepageSliderTableInfo();
        try {
            $time = date('Y-m-d H:i:s');
            foreach ($content as $item) {
                $basic = array(
                    $table->ctime => $time,
                    $table->mtime => $time
                );
                $binds = array_merge($basic, $item);
                $rtn = Utility_Db::getInstance()
                    ->conn()
                    ->insert($table->tablename, $binds);
                if (!$rtn) {
                    return array(
                        'errorcode' => -1,
                        'errormsg' => 'database failed'
                    );
                }
            }
            return array(
                'errorcode' => 0,
                'errormsg' => 'success'
            );
        } catch (Zend_Exception $e) {
            return array(
                'errorcode' => -2,
                'errormsg' => $e->getMessage()
            );
        }
    }
    
    /**
     * 获取首页slider的信息
     * 让它们对程序友好
     * @param array $homepageInfo
     * @return 返回slider的数组
     */
    public function getHomepageSlider()
    {
        $sliderTable = $this->getHomepageSliderTableInfo();
        $tmp = Utility_Db::getInstance()
            ->conn()
            ->select()
            ->from($sliderTable->tablename, $sliderTable->mtime)
            ->order($sliderTable->mtime . ' desc')
            ->query()
            ->fetch();
        
        $slider = Utility_Db::getInstance()
            ->conn()
            ->select()
            ->from($sliderTable->tablename)
            ->where($sliderTable->mtime . "=?", $tmp[$sliderTable->mtime])
            ->query()
            ->fetchAll();
        return $slider;
    }
    
    /**
     * 获取除了slider之外的所有信息
     * @param array $homepageInfo
     * @return 返回slider的数组之外的数组
     */
    public function getHomepageModules()
    {
        $modulesTable = $this->getHomepageModulesTableInfo();
        $modules = Utility_Db::getInstance()
            ->conn()
            ->select()
            ->from($modulesTable->tablename)
            ->order($modulesTable->mtime . ' desc')
            ->query()
            ->fetch();
        return $modules;
    }
    
    /**
     * 获取slider和modules的各种信息
     * @return
     * return array(
            $slider, 
            $sliderTable, 
            $modules, 
            $modulesTable
        );
     */
    public function getSliderAndModules()
    {
        $slider = $this->getHomepageSlider();
        $modules = $this->getHomepageModules();
        $sliderTable = $this->getHomepageSliderTableInfo();
        $modulesTable = $this->getHomepageModulesTableInfo();
        return array(
            $slider, 
            $sliderTable, 
            $modules, 
            $modulesTable
        );
    }
}